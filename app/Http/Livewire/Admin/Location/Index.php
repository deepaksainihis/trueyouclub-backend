<?php

namespace App\Http\Livewire\Admin\Location;

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
use App\Models\Location;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    
    public $search = null;

    public $sortColumnName = 'updated_at', $sortDirection = 'desc', $paginationLength = 10;

    public $filter_date_range, $filterStartDate, $filterEndDate;

    public $formMode = false, $updateMode = false, $viewMode = false, $viewQuoteHistoryMode = false;

    public $location_id = null, $name;
    public $location_ids = [];
    public $custom_message = '';


    protected $listeners = [
        'cancel', 'show', 'edit', 'delete', 'deleteConfirm','viewQuoteHistory'
    ];

    public function mount()
    {
        abort_if(Gate::denies('location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        $searchValue = $this->search;
        $allLocations = Location::query()->where(function ($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
            });

        $allLocations =  $allLocations->orderBy($this->sortColumnName, $this->sortDirection)->paginate($this->paginationLength);

        return view('livewire.admin.location.index', compact('allLocations'));
    }

    public function restFilterForm(){
        $this->resetPage();
        $this->reset(['filter_date_range','filterStartDate','filterEndDate']);
        $this->resetValidation();
        $this->initializePlugins();
        $this->dispatchBrowserEvent('resetDatePicker'); 
    }

    public function create()
    {
        $this->initializePlugins();
        $this->formMode = true;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name' => ['required', 'unique:locations,name'],
        ],[
            'name.required' => 'The name is required'
        ]);

        try
        {
            DB::beginTransaction();
            $location = Location::create($validatedData);
            
            DB::commit();
            $this->resetAllFields();
            $this->flash('success',trans('messages.add_success_message'));
            return redirect()->route('admin.locations');

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

        $location = Location::findOrFail($id);

        $this->location_id  =  $location->id;
        $this->name         =  $location->name;
    }


    public function update()
    {
        $validatedData = $this->validate([
            'name' => ['required', 'unique:locations,name,' . $this->location_id . ',id,deleted_at,NULL'],
        ], [
            'name.required' => 'The name is required',
        ]);

        try
        {
            DB::beginTransaction();
            
            $user = Location::find($this->location_id);
            $user->update($validatedData);

            $this->formMode = false;
            $this->updateMode = false;

            DB::commit();

            $this->flash('success', trans('messages.edit_success_message'));
            $this->resetAllFields();
            return redirect()->route('admin.locations');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function show($id)
    {
        $this->location_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    public function viewQuoteHistory($id){
        $this->location_id = $id;
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
        $this->reset(['formMode','updateMode','viewMode','viewQuoteHistoryMode','location_id','name']);
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

    public function deleteConfirm($location)
    {
        $deleteId = $location['data']['inputAttributes']['deleteId'];
        $model    = Location::find($deleteId);
        if(!$model){
            // $this->emit('refreshTable'); 
            $this->alert('error', trans('messages.error_message'));   
        }else{
            $model->delete();
            // $this->emit('refreshTable');    
            $this->alert('success', trans('messages.delete_success_message'));
        }     
    }    

    public function resetAllFields(){
        $this->reset(['formMode','updateMode','viewMode','viewQuoteHistoryMode','location_id','name']);
    }   
}
