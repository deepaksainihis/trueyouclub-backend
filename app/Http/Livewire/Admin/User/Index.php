<?php

namespace App\Http\Livewire\Admin\User;

use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserDatatableExport; 

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    
    public $search = null;

    public $sortColumnName = 'updated_at', $sortDirection = 'desc', $paginationLength = 10;

    public $filter_date_range, $filterStartDate, $filterEndDate;

    public $formMode = false, $updateMode = false, $viewMode = false, $viewQuoteHistoryMode = false;

    public $user_id = null, $first_name,  $last_name, $phone, $email, $is_active = 1;

    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm','viewQuoteHistory'
    ];

    public function mount()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;

        $allUsers = User::query()->where(function ($query) use ($searchValue, $statusSearch, $starNumber) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('phone', 'like', '%' . $searchValue . '%')
                ->orWhere('star_no', $starNumber)
                ->orWhere('is_active', $statusSearch)
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
            })
            ->whereHas('roles', function ($query) {
                $query->where('id', config('constants.role.user'));
            });
        
        if(!is_null($startDate) && !is_null($endDate)){
            $allUsers = $allUsers->whereBetween('created_at', [$startDate, $endDate]);
        }

        $allUsers =  $allUsers->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);

        return view('livewire.admin.user.index', compact('allUsers'));
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
        return Excel::download(new UserDatatableExport($this->filterStartDate,$this->filterEndDate,$this->search,$this->sortColumnName,$this->sortDirection), 'user-list.xlsx');
    }

    public function create()
    {
        $this->initializePlugins();
        $this->formMode = true;
    }

    public function edit($id)
    {
        $this->initializePlugins();
        $this->formMode = true;
        $this->updateMode = true;

        $user = User::findOrFail($id);

        $this->user_id         =  $user->id;
        $this->first_name      =  $user->first_name;
        $this->last_name       =  $user->last_name;
        $this->phone           =  $user->phone;
        $this->email           =  $user->email;
        $this->is_active       =  $user->is_active;
    }


    public function update()
    {
        $validatedData = $this->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|digits:10',
            'email'      => 'required|email',
            'is_active'  => 'required',
        ], [
            'first_name.required' => 'The first name is required',
            'last_name.required' => 'The last name is required',
            'phone.digits' => '10 digit phone number is required',
        ]);


        $validatedData['first_name'] = $this->first_name;
        $validatedData['last_name']  = $this->last_name;
        $validatedData['phone']      = $this->phone;
        $validatedData['is_active']  = $this->is_active;

        DB::beginTransaction();
        try {

            $user = User::find($this->user_id);
            $user->update($validatedData);

            $this->formMode = false;
            $this->updateMode = false;

            DB::commit();

            $this->flash('success', trans('messages.edit_success_message'));

            $this->reset(['first_name', 'last_name', 'phone', 'email', 'is_active']);

            return redirect()->route('admin.users');
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
        $this->reset(['formMode','updateMode','viewMode','viewQuoteHistoryMode','user_id','first_name','last_name','phone','email','is_active']);
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

    public function confirmedToggleAction($event)
    {
        $userId = $event['data']['inputAttributes']['userId'];
        $model = User::find($userId);
       
        $model->update(['is_active' => !$model->is_active]);

        // $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->is_active = (!$statusVal) ? 1 : 0;
    }
}
