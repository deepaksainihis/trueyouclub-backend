<?php

namespace App\Http\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\Location;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Str;

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    public $search = null, $formMode = false, $updateMode = false, $viewMode = false, $attendanceViewMode = false;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Title, Location Created Date";

    public $event_id = null, $title, $description, $totalInvitations, $created_at ,$image, $location_id, $originalImage,$event_date = null, $start_time=null,  $end_time = null,$full_start_time=null,  $full_end_time = null, $status = 1, $volunteer = 0;
    public $locations = [];
    public $removeImage = false;    
    
    protected $listeners = [
        'cancel', 'show', 'showAttendance', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm'
    ];

    public function mount()
    {
        abort_if(Gate::denies('event_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->created_at = Carbon::now()->format('d-m-Y');
    }

    public function updatedPaginationLength()
    {
        $this->resetPage();
    }


    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($columnName)
    {
        if ($this->sortColumnName === $columnName) {
            $this->sortDirection = $this->swapSortDirection();
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortColumnName = $columnName;
    }

    public function swapSortDirection()
    {
        return $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function render()
    {
        $statusSearch = null;
        $searchValue = $this->search;
        if (Str::contains('active', strtolower($searchValue))) {
            $statusSearch = 1;
        } else if (Str::contains('inactive', strtolower($searchValue))) {
            $statusSearch = 0;
        }

        $currentDate = now()->format('Y-m-d');

        $allEvent = Event::query()->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhereHas('eventLocation', function($query) use($searchValue){
                    $query->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhere('volunteer', $searchValue)
                ->orWhere('status', $statusSearch)
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        })
            
            ->orderByRaw('CASE 
                    WHEN created_at >= "'.$currentDate.'" THEN 0
                    WHEN created_at <= "'.$currentDate.'" THEN 1
                    ELSE 3
                END,
                created_at'
            )

            // ->orderBy('created_at', $this->sortDirection)

            // ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);

        $this->locations = Location::whereStatus(1)->pluck('name', 'id')->toArray();
        return view('livewire.admin.event.index',compact('allEvent'));
    }

    public function updatedEventDate(){
        $this->event_date = Carbon::parse($this->event_date)->format('d-m-Y');
    }

    public function updatedStartTime(){
        if($this->start_time){
            $this->start_time = Carbon::parse($this->start_time)->format('h:i A');
        }
    }


    public function updatedEndTime(){
        if($this->end_time){
            $this->end_time = Carbon::parse($this->end_time)->format('h:i A');
        }
    }   

    public function create()
    {
        $this->initializePlugins();
        $this->locations = Location::whereStatus(1)->pluck('name', 'id')->toArray();
        $this->formMode = true;
    }

    public function store()
    {
        $rules = [
            'title'         => 'required',           
            'description'   => 'required|strip_tags',
            'event_date'    => 'required|date',
            'start_time'    => 'required|date_format:h:i A',
            'end_time'      => 'required|date_format:h:i A',
            'status'        => 'required',
            'volunteer'      => 'required|numeric|min:0',
            'image'         => 'nullable|image|max:' . config('constants.img_max_size'),
            'location_id'   => 'required|exists:locations,id',
        ];

        $starDateTime = Carbon::parse($this->event_date.' '.$this->start_time);
        $endDateTime = Carbon::parse($this->event_date.' '.$this->end_time);
        $currentDateTime = Carbon::now();

        // Check if start time is greater than current time
        // if ($starDateTime->lt($currentDateTime)) {
        //     $rules['start_time'] = '|after:now';
        // }

        // Check if end time is greater than start time
        if ($endDateTime->lt($starDateTime)) {
            $rules['end_time'] = '|after:start_time';
        }

        $validatedData = $this->validate($rules, [
            'description.strip_tags' => 'The description field is required',
            'end_time.after' => 'The end time must be a time after the start time.',

            'location_id.required' => 'The location is required.',
            'location_id.exists' => 'The selected location is invalid.',
        ]);

        DB::beginTransaction();
        try {
            $validatedData['event_date']   = Carbon::parse($this->event_date)->format('Y-m-d');
            $validatedData['start_time']   = Carbon::parse($this->start_time)->format('H:i');
            $validatedData['end_time']   = Carbon::parse($this->end_time)->format('H:i');
            $validatedData['volunteer'] = $this->volunteer;            
            $validatedData['status'] = $this->status;            
            $event = Event::create($validatedData);

            if ($this->image) {
                uploadImage($event, $this->image, 'event/image/', "event", 'original', 'save', null);
            }

            DB::commit();

            $this->resetAllFields();

            $this->flash('success', trans('messages.add_success_message'));

            return redirect()->route('admin.events');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        $this->initializePlugins();
        $this->formMode = true;
        $this->updateMode = true;

        $this->locations = Location::whereStatus(1)->pluck('name', 'id')->toArray();

        $event = Event::findOrFail($id);
        $this->event_id         =  $event->id;
        $this->title             =  $event->title;
        $this->description      =  $event->description;
        $this->event_date      =  $event->event_date->format('d-m-Y');
        $this->start_time      =  Carbon::parse($event->start_time)->format('h:i A');
        $this->end_time        =  Carbon::parse($event->end_time)->format('h:i A');
        $this->volunteer =  $event->volunteer;
        $this->status           =  $event->status;
        $this->originalImage    =  $event->featured_image_url;   
        $this->location_id     =  $event->location_id;

        $this->full_start_time =  $event->event_date->format('Y-m-d').' '. Carbon::parse($event->start_time)->format('H:i:s');
        $this->full_end_time =  $event->event_date->format('Y-m-d').' '. Carbon::parse($event->end_time)->format('H:i:s');
    }


    public function update()
    {  
        $rules = [
            'title'         => 'required',           
            'description'   => 'required|strip_tags',
            'event_date'   => 'required|date',
            'start_time'   => 'required|date_format:h:i A',
            'end_time'     => 'required|date_format:h:i A',
            'volunteer'      => 'required|numeric|min:0',
            'status'        => 'required',
            'image'         => 'nullable|image|max:' . config('constants.img_max_size'),
            'location_id'   => 'required|exists:locations,id',
        ];
        
        $starDateTime = Carbon::parse($this->event_date.' '.$this->start_time);
        $endDateTime = Carbon::parse($this->event_date.' '.$this->end_time);
        $currentDateTime = Carbon::now();

        // Check if start time is greater than current time
        if ($starDateTime->lt($currentDateTime)) {
            $rules['start_time'] = '|after:now';
        }

        // Check if end time is greater than start time
        if ($endDateTime->lt($starDateTime)) {
            $rules['end_time'] = '|after:start_time';
        }

        $validatedData = $this->validate($rules, [
            'description.strip_tags' => 'The description field is required',
            'end_time.after' => 'The end time must be a time after the start time.',

            'location_id.required' => 'The location is required.',
            'location_id.exists' => 'The selected location is invalid.',
        ]);

        $validatedData['status'] = $this->status;        

        DB::beginTransaction();
        try {

            $event = Event::find($this->event_id);

            // Check if the image has been changed
            if ($this->image) {
                if ($event->featuredImage) {
                    $uploadImageId = $event->featuredImage->id;
                    uploadImage($event, $this->image, 'event/image/', "event", 'original', 'update', $uploadImageId);
                } else {
                    uploadImage($event, $this->image, 'event/image/', "event", 'original', 'save', null);
                }
            }

            if ($this->removeImage) {
                if ($event->featuredImage) {
                    $uploadImageId = $event->featuredImage->id;
                    deleteFile($uploadImageId);
                }
            }

            $event->update($validatedData);

            DB::commit();           

            $this->alert('success', trans('messages.edit_success_message'));
            $this->cancel();            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function showAttendance($id)
    {
        $this->event_id = $id;
        $this->formMode = false;
        $this->attendanceViewMode = true;
    }

    public function show($id)
    {
        $this->event_id = $id;
        $this->formMode = false;
        $this->attendanceViewMode = false;
        $this->viewMode = true;
    }


    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }

    public function cancel()
    {
        $this->resetAllFields();
        $this->resetValidation();
    }

    public function resetAllFields(){
        $this->reset(['formMode','updateMode','viewMode','attendanceViewMode','event_id','title','description','image','originalImage','volunteer','status','removeImage','event_date','start_time','end_time','full_start_time','full_end_time', 'location_id']);
    }

    public function delete($id)
    {
        $this->confirm('Are you sure?', [
            'text' => 'You want to delete it.',
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Yes, delete it!',
            'cancelButtonText' => 'No, cancel!',
            'onConfirmed' => 'deleteConfirm',
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
            'inputAttributes' => ['deleteId' => $id],
        ]);
        
    }

    public function deleteConfirm($event)
    {
        $deleteId = $event['data']['inputAttributes']['deleteId'];
        $model    = Event::find($deleteId);
        
        if(!$model){
            // $this->emit('refreshTable'); 
            $this->alert('error', trans('messages.error_message'));   
        }else{
            $model->eventRequests()->delete();
            $model->delete();

            $this->resetPage();
            
            // $this->emit('refreshTable');    
            $this->alert('success', trans('messages.delete_success_message'));
        }          
             
    }

    public function toggle($id)
    {
        $this->confirm('Are you sure?', [
            'text' => 'You want to change the status.',
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Yes Confirm!',
            'cancelButtonText' => 'No Cancel!',
            'onConfirmed' => 'confirmedToggleAction',
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
            'inputAttributes' => ['eventId' => $id],
        ]);
    }

    public function confirmedToggleAction($event)
    {
        $eventId = $event['data']['inputAttributes']['eventId'];
        $model = Event::find($eventId);
        $model->update(['status' => !$model->status]);

        // $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->status = (!$statusVal) ? 1 : 0;
    }
}
