<?php

namespace App\Http\Livewire\Admin\Contact;

use Gate;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Contact;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;
use App\Mail\ContactReplyNotification;
use Illuminate\Support\Facades\Mail;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactDataTableExport; 

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    public $formMode = false, $updateMode = false, $viewMode = false;

    public $contact_id = null, $first_name,  $last_name, $phone_number, $email, $message, $status = 1;

    public $fullname, $reply, $reply_id;

    
    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10;

    public $filter_date_range, $filterStartDate, $filterEndDate;

    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm', 'reply'
    ];


    public function mount()
    {
        abort_if(Gate::denies('contact_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($columnName)
    {
        $this->resetPage();

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
        $searchValue = $this->search;
      
        $startDate = $this->filterStartDate ? $this->filterStartDate->startOfDay() : null;
        $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;

        $allContacts = Contact::query()->where(function ($query) use ($searchValue,$startDate,$endDate) {
            $query
                ->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $searchValue . '%'])
                ->orWhere('email', 'like', '%' . $searchValue . '%')
                ->orWhere('phone_number', 'like', '%' . $searchValue . '%')
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        });

        if(!is_null($startDate) && !is_null($endDate)){
            $allContacts = $allContacts->whereBetween('created_at', [$startDate, $endDate]);
        }

        if($this->sortColumnName == 'full_name'){
            $allContacts = $allContacts->orderByRaw("CONCAT(first_name, ' ', last_name) ".$this->sortDirection);
        }else{
            $allContacts = $allContacts->orderBy($this->sortColumnName, $this->sortDirection);
        }
        
        $allContacts = $allContacts->paginate($this->paginationLength);

        return view('livewire.admin.contact.index',compact('allContacts'));
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
        $this->resetPage();

        $this->reset(['filter_date_range','filterStartDate','filterEndDate']);
        $this->resetValidation();
        $this->initializePlugins();
    }

    public function exportToExcel()
    {
        return Excel::download(new ContactDataTableExport($this->filterStartDate,$this->filterEndDate,$this->search,$this->sortColumnName,$this->sortDirection), 'contact-list.xlsx');
    }

    public function show($id)
    {        
        $this->contact_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }

    public function cancel()
    {
        // dd($this->all());
        $this->reset(['formMode','updateMode','viewMode','contact_id','first_name','last_name','phone_number','email','message','status','fullname','reply','reply_id']);
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

    public function deleteConfirm($event)
    {
        $deleteId = $event['data']['inputAttributes']['deleteId'];
        $model    = Contact::find($deleteId);
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
            'inputAttributes' => ['contactId' => $id],
        ]);
    }

    public function confirmedToggleAction($event)
    {
        $contactId = $event['data']['inputAttributes']['contactId'];
        $model = Contact::find($contactId);
        $model->update(['status' => !$model->status]);

        // $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->status = (!$statusVal) ? 1 : 0;
    }

    public function reply($id)
    {
        $getContactDetail = Contact::where('id', $id)->first();
        $this->fullname     = $getContactDetail->first_name . ' ' . $getContactDetail->last_name;
        $this->email        = $getContactDetail->email;
        $this->reply_id     = $getContactDetail->id;
        $this->reply        = $getContactDetail->reply;

        $this->formMode     = true;
        $this->updateMode   = true;
    }

    public function draftReply($draft_id)
    {
        $draftDetail = Contact::find($draft_id);

        $draftDetail->update(['is_draft' => 1, 'reply' => $this->reply]);

        $this->alert('success', trans('messages.message_in_draft'));
    }

    public function sendReply($id)
    {
        $detail = Contact::find($id);

        $detail->update(['is_draft' => 0, 'reply' => $this->reply]);

        // Send email notification
        $name     = $detail->first_name . ' ' . $detail->last_name;
        $subject  = 'Assistance with Your Inquiry';
        $reply    = $detail->reply;

        Mail::to($detail->email)->send(new ContactReplyNotification($name, $subject, $reply));

        $this->alert('success', trans('messages.mail_sent'));

        return redirect()->to('/admin/contacts');
    }


   
}
