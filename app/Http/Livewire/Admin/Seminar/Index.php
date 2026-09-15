<?php

namespace App\Http\Livewire\Admin\Seminar;

use App\Models\Seminar;
use App\Models\User;
use Gate;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\SeminarCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchasedSeminarTicketMail;
use Illuminate\Support\Str;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeminarDatatableExport; 

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    // protected $layout = null;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Title, Venue, Date";

    public $filter_date_range, $filterStartDate, $filterEndDate;


    public $formMode = false, $updateMode = false, $viewMode = false, $bookingMode=false;

    public $seminar_id = null, $title, $total_ticket, $ticket_price=null, $start_date = null, $start_time = null,  $end_time = null,  $venue, $image, $originalImage, $status = 1, $full_start_time=null,  $full_end_time = null;

    public $removeImage = false;

    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm', 'viewBookings',
    ];

    public function mount()
    {
        abort_if(Gate::denies('seminar_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        // if (Str::contains('active', strtolower($searchValue))) {
        //     $statusSearch = 1;
        // } else if (Str::contains('inactive', strtolower($searchValue))) {
        //     $statusSearch = 0;
        // }

        $startDate = $this->filterStartDate ? $this->filterStartDate->startOfDay() : null;
        $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;
        
        $allSeminar = Seminar::query()->select('*')
        ->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')

        ->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('venue', 'like', '%' . $searchValue . '%')
                ->orWhereRaw("DATE_FORMAT(start_date,  '" . config('constants.search_full_date_format') . "') = ?", [date(config('constants.full_date_format'), strtotime($searchValue))]);

            // Check for month name (e.g., January)
            $query->orWhereRaw('LOWER(DATE_FORMAT(start_date, "%M")) LIKE ?', ['%' . strtolower($searchValue) . '%']);

            // Check for day and month (e.g., 13 January)
            $query->orWhereRaw('LOWER(DATE_FORMAT(start_date, "%e %M")) LIKE ?', ['%' . strtolower($searchValue) . '%']);
        });

        if(!is_null($startDate) && !is_null($endDate)){
            $allSeminar = $allSeminar->whereBetween('start_date', [$startDate, $endDate]);
        }

        $allSeminar = $allSeminar->orderByRaw('
            CASE 
                WHEN CONCAT(start_date, " ", end_time) <= NOW() AND CONCAT(start_date, " ", end_time) >= NOW() THEN 0
                WHEN CONCAT(start_date, " ", end_time) > NOW() THEN 1
                ELSE 2
            END ASC,
            time_diff_seconds > 0 ASC, ABS(time_diff_seconds) ASC
        ');

        // ->orderBy($this->sortColumnName, $this->sortDirection)
        $allSeminar = $allSeminar->paginate($this->paginationLength);

        return view('livewire.admin.seminar.index',compact('allSeminar'));
    }

    public function submitFilterForm(){
        $this->resetPage();

        $rules = [
            'filter_date_range' => 'required',
        ];
        $this->validate($rules,[
            'filter_date_range'=>'Please select date'
        ]);

        $date_range = explode(' - ', $this->filter_date_range);
        $this->filterStartDate = Carbon::parse(date('Y-m-d',strtotime(str_replace(' ','-',$date_range[0]))));
        $this->filterEndDate   = Carbon::parse(date('Y-m-d',strtotime(str_replace(' ','-',$date_range[1]))));
    }

    public function restFilterForm(){
        $this->reset(['filter_date_range','filterStartDate','filterEndDate']);
        $this->resetValidation();

        $this->resetPage();
        $this->initializePlugins();
    }

    public function exportToExcel()
    {
        return Excel::download(new SeminarDatatableExport($this->filterStartDate,$this->filterEndDate,$this->search,$this->sortColumnName,$this->sortDirection), 'seminar-list.xlsx');
    }

    public function updatedStartDate()
    {
        $this->start_date = Carbon::parse($this->start_date)->format('d-m-Y');
    }

    public function updatedStartTime()
    {
        if($this->start_time){
            $this->start_time = Carbon::parse($this->start_time)->format('h:i A');
        }
    }

    public function updatedEndTime()
    {
        if($this->end_time){
            $this->end_time = Carbon::parse($this->end_time)->format('h:i A');
        }
    }


    public function create()
    {
        $this->initializePlugins();
        $this->formMode = true;
    }

    public function store()
    {
        $rules = [
            'title'        => 'required',
            'total_ticket' => 'required|numeric|min:1',
            'ticket_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|not_in:0',
            'start_date'   => 'required|date',
            'start_time'   => 'required|date_format:h:i A',
            'end_time'     => 'required|date_format:h:i A',
            'venue'        => 'required',
            // 'status'       => 'required',
            'image'        => 'nullable|image|max:' . config('constants.img_max_size'),
        ];
        
        $starDateTime = Carbon::parse($this->start_date.' '.$this->start_time);
        $endDateTime = Carbon::parse($this->start_date.' '.$this->end_time);
        $currentDateTime = Carbon::now();

        // Check if start time is greater than current time
        if ($starDateTime->lt($currentDateTime)) {
            $rules['start_time'] = '|after:now';
        }

        // Check if end time is greater than start time
        if ($endDateTime->lt($starDateTime)) {
            $rules['end_time'] = '|after:start_time';
        }

        $validatedData = $this->validate($rules,
            [
                'end_time.after' => 'The end time must be a time after the start time',
                'ticket_price.not_in'=> 'The ticket price is required'
            ]
        );

        $validatedData['start_date']   = Carbon::parse($this->start_date)->format('Y-m-d');
        $validatedData['start_time']   = Carbon::parse($this->start_time)->format('H:i');

        $validatedData['end_time']   = Carbon::parse($this->end_time)->format('H:i');

        $validatedData['status'] = $this->status;

        $seminar = Seminar::create($validatedData);

        //Upload Image
        if ($this->image) {
            uploadImage($seminar, $this->image, 'seminar/image/', "seminar", 'original', 'save', null);
        }

        $users = User::whereHas('roles', function($q){ $q->where('title', 'User');})->get();
        Notification::send($users, new SeminarCreated($seminar));

        $this->resetAllFields();

        $this->flash('success', trans('messages.add_success_message'));

        return redirect()->route('admin.seminars');
    }

    public function edit($id)
    {
        $this->formMode = true;
        $this->updateMode = true;

        $seminar = Seminar::findOrFail($id);
        $this->seminar_id      =  $seminar->id;
        $this->title           =  $seminar->title;
        $this->total_ticket    =  $seminar->total_ticket;
        $this->ticket_price     =  $seminar->ticket_price;
        $this->start_date      =  Carbon::parse($seminar->start_date)->format('d-m-Y');
        $this->start_time      =  Carbon::parse($seminar->start_time)->format('h:i A');
        $this->end_time        =  Carbon::parse($seminar->end_time)->format('h:i A');
        $this->venue           =  $seminar->venue;
        // $this->status          =  $seminar->status;
        $this->originalImage   =  $seminar->image_url;

        $this->full_start_time =  Carbon::parse($seminar->start_date.' '.$seminar->start_time)->format('Y-m-d H:i:s');
        $this->full_end_time   =  Carbon::parse($seminar->start_date.' '.$seminar->end_time)->format('Y-m-d H:i:s');

        $this->initializePlugins();
    }


    public function update()
    {
        $validatedArray['title']        = 'required';
        $validatedArray['total_ticket'] = 'required|numeric|min:1';
        $validatedArray['ticket_price']  = 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|not_in:0';

        $validatedArray['start_date']   = 'required|date';
        $validatedArray['start_time']   = 'required|date_format:h:i A';
        $validatedArray['end_time']     = 'required|date_format:h:i A';
        $validatedArray['venue']        = 'required';
        // $validatedArray['status']       = 'required';

        if ($this->image || $this->removeImage) {
            $validatedArray['image'] = 'nullable|image|max:' . config('constants.img_max_size');
        }

        $starDateTime = Carbon::parse($this->start_date.' '.$this->start_time);
        $endDateTime = Carbon::parse($this->start_date.' '.$this->end_time);
        $currentDateTime = Carbon::now();

        // Check if start time is greater than current time
        if ($starDateTime->lt($currentDateTime)) {
            $validatedArray['start_time'] = '|after:now';
        }

        // Check if end time is greater than start time
        if ($endDateTime->lt($starDateTime)) {
            $validatedArray['end_time'] = '|after:start_time';
        }

        $validatedData = $this->validate($validatedArray, [
            'end_time.after' => 'The end time must be a time after the start time',
            'ticket_price.not_in'=> 'The ticket price is required'
        ]);

        $validatedData['title']  = $this->title;
        $validatedData['venue']  = $this->venue;

        $validatedData['start_date']   = Carbon::parse($this->start_date)->format('Y-m-d');
        $validatedData['start_time']   = Carbon::parse($this->start_time)->format('H:i');

        $validatedData['end_time']   = Carbon::parse($this->end_time)->format('H:i');

        // $validatedData['status'] = $this->status;

        $seminar = Seminar::find($this->seminar_id);
        // Check if the image has been changed
       
        if ($this->image) {
            if($seminar->seminarImage){
                $uploadImageId = $seminar->seminarImage->id;
                uploadImage($seminar, $this->image, 'seminar/image/', "seminar", 'original', 'update', $uploadImageId);
            }else{
                uploadImage($seminar, $this->image, 'seminar/image/', "seminar", 'original', 'save', null);
            }
           
        }

        if($this->removeImage){
            if($seminar->seminarImage){
                $uploadImageId = $seminar->seminarImage->id;
                deleteFile($uploadImageId);
            }
        }

        $seminar->update($validatedData);

        $this->alert('success', trans('messages.edit_success_message'));

        $this->cancel();

        // return redirect()->route('admin.seminars');
    }

    public function show($id)
    {
        $this->seminar_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    public function viewBookings($id)
    {
        $this->seminar_id = $id;
        $this->formMode = false;
        $this->viewMode = false;
        $this->bookingMode = true;
    }


    public function initializePlugins()
    {
        $data = [
            'updateMode' => $this->updateMode, 
            'full_start_time' => $this->full_start_time,
            'full_end_time' => $this->full_end_time,
        ];
        $this->dispatchBrowserEvent('loadPlugins', $data);
    }

    public function cancel()
    {
        $this->resetAllFields();
        $this->resetValidation();
        $this->initializePlugins();
        $this->dispatchBrowserEvent('seminarCounterEvent');
    }

    public function resetAllFields(){
        
        $this->reset(['formMode','updateMode','viewMode','bookingMode','title','total_ticket','ticket_price','start_date','start_time','end_time','venue','image','originalImage','removeImage','full_start_time','full_end_time','seminar_id']);
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
            'inputAttributes' => ['deleteId' => $id],
        ]);
    }

    public function deleteConfirm($event)
    {
        $deleteId = $event['data']['inputAttributes']['deleteId'];
        $model    = Seminar::find($deleteId);
        if(!$model){
            $this->alert('error', trans('messages.error_message'));   
        }else{
            
            $this->resetPage();
            $model->delete();  
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
            'inputAttributes' => ['seminarId' => $id],
        ]);

    }

    public function confirmedToggleAction($event)
    {
        $seminarId = $event['data']['inputAttributes']['seminarId'];
        $model = Seminar::find($seminarId);

        $model->update(['status' => !$model->status]);

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->status = (!$statusVal) ? 1 : 0;
    }
}
