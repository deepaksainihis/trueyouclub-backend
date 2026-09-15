<?php

namespace App\Http\Livewire\Admin\Volunteer;

use Gate;
use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserDatatableExport;
use App\Mail\SendInviteEventMail;
use App\Models\Event;
use App\Models\EventRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\Location;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Notification;

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    
    public $search = null;

    public $sortColumnName = 'updated_at', $sortDirection = 'desc', $paginationLength = 10;

    public $filter_date_range, $filterStartDate, $filterLocation, $filterEndDate, $filter_location_id;

    public $formMode = false, $updateMode = false, $viewMode = false, $viewQuoteHistoryMode = false;

    public $user_id = null, $first_name,  $last_name, $phone, $email,$password,$password_confirmation,$location_id, $is_active = 1;
    public $locations = [];
    public $events = [];
    public $volunteer_ids = [];
    public $event_id = null;
    public $volunteer_id = null , $user = null;
    public $custom_message = '';


    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm','viewQuoteHistory'
    ];

    public function mount()
    {
        abort_if(Gate::denies('volunteer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        if (Str::contains('break', strtolower($searchValue))) {
            $statusSearch = 1;
        } else if (Str::contains('continue', strtolower($searchValue))) {
            $statusSearch = 0;
        }

        $starNumber = null;
        if(in_array($searchValue,config('constants.ratings'))){
            $starNumber = $searchValue;
        }

        $startDate = $this->filterStartDate ? $this->filterStartDate->startOfDay() : null;
        // $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;

        $allUsers = User::query()->where(function ($query) use ($searchValue, $statusSearch, $starNumber) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('phone', 'like', '%' . $searchValue . '%')
                ->orWhere('star_no', $starNumber)
                ->orWhere('is_active', $statusSearch)
                ->orWhereHas('userLocation', function($query) use($searchValue){
                    $query->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
            })
            ->whereHas('roles', function ($query) {
                $query->where('id', config('constants.role.volunteer'));
            });
        
        if(!empty($startDate)){            
            $allUsers = $allUsers->whereHas('availabilities', function($query) use ($startDate) {
                $query->where('date', '=', $startDate);
            });
        }

        
        if(!empty($this->filter_location_id)){
            $allUsers = $allUsers->where('location_id', $this->filter_location_id);
        }

        // if(!is_null($startDate) && !is_null($endDate)){
        //     $allUsers = $allUsers->whereBetween('created_at', [$startDate, $endDate]);
        // }
        $this->locations = Location::whereStatus(1)->pluck('name', 'id')->toArray();
        $allUsers =  $allUsers->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);

        return view('livewire.admin.volunteer.index', compact('allUsers'));
    }

    public function submitFilterForm(){
        $this->resetPage();        
        $rules = [
            'filter_date_range' => 'required_without:filter_location_id',
            'filter_location_id' => 'required_without:filter_date_range',
        ];
        $this->validate($rules,[
            'filter_date_range'=>'Please select date',
            'filter_location_id'=>'Please select location',
        ]);

        $this->filterLocation = $this->filter_location_id;
        $this->filterStartDate = $this->filter_date_range ? Carbon::parse(date('Y-m-d',strtotime(str_replace(' ','-',$this->filter_date_range)))) : null;        

       /* $date_range = explode(' - ', $this->filter_date_range);
        $this->filterStartDate = Carbon::parse(date('Y-m-d',strtotime(str_replace(' ','-',$date_range[0]))));
        $this->filterEndDate   = Carbon::parse(date('Y-m-d',strtotime(str_replace(' ','-',$date_range[1]))));
       */
    }

    public function restFilterForm(){
        $this->resetPage();
        $this->reset(['filter_date_range', 'filter_location_id', 'filterLocation', 'filterStartDate','filterEndDate']);
        $this->resetValidation();
        $this->initializePlugins();
        $this->dispatchBrowserEvent('resetDatePicker'); 
    }

    public function exportToExcel()
    {
        return Excel::download(new UserDatatableExport($this->filterStartDate,$this->filterEndDate,$this->search,$this->sortColumnName,$this->sortDirection), 'user-list.xlsx');
    }

    public function create()
    {
        $this->initializePlugins();
        $this->locations = Location::whereStatus(1)->pluck('name', 'id')->toArray();
        $this->formMode = true;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|digits:10',
            'email'      => 'required|email',
            'location_id'=> 'required|exists:locations,id',
            // 'is_active'  => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8|same:password',
        ],[
            'first_name.required' => 'The first name is required',
            'last_name.required' => 'The last name is required',
            'phone.digits' => '10 digit phone number is required',
            'password.required' => 'Password is required!',
            'password.min' => 'The Password should be at least 8 letter.',
            'password.confirmed' => 'The Password should be confirm.',
            'password_confirmation.required' => 'Confirm password is required!',
            'password_confirmation.min' => 'The confirm password should be at least 8 letter.',
            'password_confirmation.same' => 'The password confirmation and password must match.',

            'location_id.required' => 'The location is required.',
            'location_id.exists' => 'The selected location is invalid.',
        ]);

        try
        {
            DB::beginTransaction();
            $validatedData['name'] = $this->first_name.' '.$this->last_name ; 
            $validatedData['password'] =  Hash::make($this->password);
            unset($validatedData['password_confirmation']);
            $user = User::create($validatedData);
            $user->roles()->sync([config('constants.role.volunteer')]);                 // Assign user Role      
            
            //Send welcome mail for user
            $subject = 'Welcome to ' . config('app.name');
            Mail::to($user->email)->queue(new WelcomeMail($subject, $user->name, $user->email,$this->password));

            $notification_message = config('constants.user_register_notification_message');
            Notification::send($user, new UserNotification($user, $notification_message));
            
            // Verification mail sent
            $user->NotificationSendToVerifyEmail();

            DB::commit();
            $this->resetAllFields();
            $this->flash('success',trans('messages.add_success_message'));
            return redirect()->route('admin.volunteers');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        $this->initializePlugins();
        $this->formMode = true;
        $this->updateMode = true;

        $user = User::findOrFail($id);

        $this->locations = Location::whereStatus(1)->pluck('name', 'id')->toArray();

        $this->user_id         =  $user->id;
        $this->first_name      =  $user->first_name;
        $this->last_name       =  $user->last_name;
        $this->phone           =  $user->phone;
        $this->email           =  $user->email;
        $this->location_id     =  $user->location_id;
        $this->is_active       =  $user->is_active;
    }


    public function update()
    {
        $validatedData = $this->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|digits:10',
            'email'      => 'required|email',
            'location_id'=> 'required|exists:locations,id',
            // 'is_active'  => 'required',
        ], [
            'first_name.required' => 'The first name is required',
            'last_name.required' => 'The last name is required',
            'phone.digits' => '10 digit phone number is required',

            'location_id.required' => 'The location is required.',
            'location_id.exists' => 'The selected location is invalid.',
        ]);

        try
        {
            DB::beginTransaction();
            $validatedData['name'] = $this->first_name.' '.$this->last_name ; 
            $user = User::find($this->user_id);
            $user->update($validatedData);

            $this->formMode = false;
            $this->updateMode = false;

            DB::commit();

            $this->flash('success', trans('messages.edit_success_message'));
            $this->resetAllFields();
            return redirect()->route('admin.volunteers');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function show($id)
    {
        $this->user_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    public function viewQuoteHistory($id){
        $this->user_id = $id;
        $this->formMode = false;
        $this->viewMode = false;
        $this->viewQuoteHistoryMode = true;
    }

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }

    public function cancel()
    {
        $this->reset(['formMode','updateMode','viewMode','viewQuoteHistoryMode','user_id','first_name','last_name','phone','email','is_active', 'location_id']);
        $this->resetValidation();
        $this->initializePlugins();
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

    public function deleteConfirm($volunteer)
    {
        $deleteId = $volunteer['data']['inputAttributes']['deleteId'];
        $model    = User::find($deleteId);
        if(!$model){
            // $this->emit('refreshTable'); 
            $this->alert('error', trans('messages.error_message'));   
        }else{
            $model->delete();
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
            'inputAttributes' => ['userId' => $id],
        ]);
    }

    public function confirmedToggleAction($volunteer)
    {
        $userId = $volunteer['data']['inputAttributes']['userId'];
        $model = User::find($userId);
       
        $model->update(['is_active' => !$model->is_active]);

        // $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->is_active = (!$statusVal) ? 1 : 0;
    }

    public function resetAllFields(){
        $this->reset(['formMode','updateMode','viewMode','viewQuoteHistoryMode','user_id','first_name','last_name','phone','email','is_active', 'location_id']);
    }   

    public function triggerMassInviteModal($volunteer_ids = [])
    {    
        if (count($volunteer_ids) == 0) {
            $this->alert('warning','Please select a volunteer');
        }
        else{
            $this->volunteer_ids =  $volunteer_ids;         
            $this->events = Event::where('status',1)->where('location_id', $this->filter_location_id)->where('event_date', '>=', now()->toDateString())->get();
            $this->dispatchBrowserEvent('openInviteModal');  
        }           
    }

    public function triggerInviteModal(User $user)
    {
        array_push($this->volunteer_ids, $user->id);        
        $this->events = Event::where('status',1)->where('location_id', $user->location_id)->where('event_date', '>=', now()->toDateString())->get();      
        $this->dispatchBrowserEvent('openInviteModal');                 
    }

    public function closeModal()
    {
        $this->reset(['event_id', 'volunteer_ids', 'custom_message']);
        $this->dispatchBrowserEvent('closeInviteModal'); 
    }

    public function submitSendInvite()
    {
        $validatedData = $this->validate([
            'event_id' => 'required|exists:events,id',
            'custom_message' => 'required|string',
        ],[
            'event_id.required' => 'The Event field is required',            
            'custom_message.required' => 'The Message field is required',
        ]);
        
        try{
            DB::beginTransaction();

           
            foreach($this->volunteer_ids as $volunteer_id){

                $sendNotificationFlag = false;

                $validatedData['volunteer_id'] = $volunteer_id;     

                $checkAlreadyInvited = EventRequest::where('volunteer_id', $volunteer_id)->where('event_id', $this->event_id)->first();

                if(is_null($checkAlreadyInvited)){
                    $validatedData['attempts'] = 1;  
                    $eventrequest =  EventRequest::create($validatedData);

                    $sendNotificationFlag = true;
                }else{

                    if($checkAlreadyInvited->status != 1){

                        $checkAlreadyInvited->increment('attempts');
                        $checkAlreadyInvited->status  = 0;
                        $checkAlreadyInvited->save();
                        $eventrequest =  $checkAlreadyInvited;

                        $sendNotificationFlag = true;

                    }
                }

                if($sendNotificationFlag){

                    $event= Event::where('id',$eventrequest->event_id)->first();
                    $volunteer= User::where('id',$volunteer_id)->first();
                    $eventDetail= $event->toArray();
                    
                    $eventDate = $event->event_date->format('d-M-Y');
                    $eventStartTime = \Carbon\Carbon::parse($event->start_time)->format('h:i A');
                    $eventEndTime   = \Carbon\Carbon::parse($event->end_time)->format('h:i A');

                    $eventDetail['featured_image_url'] = $event->featured_image_url ? $event->featured_image_url : asset(config('constants.default.no_image')); 
                    $eventDetail['formatted_date_time'] = $eventDate . ' ' .$eventStartTime. ' - ' .$eventEndTime; 

                    //Send Notification
                    $notification_message = trans('messages.invitation_notification',['name'=>strtolower($volunteer->first_name), 'event_name'=>ucwords($event->title), 'event_date'=>$eventDate, 'event_start_time' => $eventStartTime]);
                    $notification_message .= '<br>'.$this->custom_message;
                    Notification::send($volunteer, new UserNotification($volunteer, $notification_message));
                
                    //Send Mail
                    $subject = 'Invitation : '.$event->title;   
                    Mail::to($volunteer->email)->queue(new SendInviteEventMail($volunteer->name, $subject, $volunteer->email, $this->custom_message,$eventDetail));
                    
                }
            }

            DB::commit();
            $this->closeModal();
            $this->flash('success',trans('messages.invitation_sent_successfully'));
            return redirect()->route('admin.volunteers');  

        }catch(\Exception $e){
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine())
            $this->flash('error',trans('messages.error_message'));
            return redirect()->route('admin.volunteers');  
        }        

              
    }
}
