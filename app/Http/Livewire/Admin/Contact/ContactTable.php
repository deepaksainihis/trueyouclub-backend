<?php

namespace App\Http\Livewire\Admin\Contact;

use Livewire\Component;
use App\Models\Contact;
use Illuminate\Support\Str;
use Livewire\WithPagination;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactDataTableExport; 

class ContactTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10;

    public $filter_date_range, $filterStartDate, $filterEndDate;

    protected $listeners = [
        'refreshTable' => 'render',
    ];

    public function mount(){
        $this->search = session('datatable_search', null);
    }

    public function updatedPaginationLength()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
        session(['datatable_search' => $this->search]);
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

        return view('livewire.admin.contact.contact-table', compact('allContacts'));
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

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }


}
