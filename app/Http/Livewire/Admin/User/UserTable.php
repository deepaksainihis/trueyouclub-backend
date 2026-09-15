<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserDatatableExport; 

class UserTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'updated_at', $sortDirection = 'desc', $paginationLength = 10;

    public $filter_date_range, $filterStartDate, $filterEndDate;

    protected $listeners = [
        'refreshTable' => 'render',
    ];

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
            
        return view('livewire.admin.user.user-table', compact('allUsers'));
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

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }
}
