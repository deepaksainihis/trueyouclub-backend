<?php

namespace App\Http\Livewire\Admin\MisReport;

use Livewire\Component;

use App\Models\Quote;
use Livewire\WithPagination;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllQuoteExcelReportExport; 
use App\Exports\QuoteExcelReportExport; 

class MisReportTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Quote Date";

    public $filter_date_range, $filterStartDate, $filterEndDate;

    protected $listeners = [
        'refreshTable' => 'render',
        'updatePaginationLength',
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
        $searchValue = $this->search;
       
        $startDate = $this->filterStartDate ? $this->filterStartDate->startOfDay() : null;
        $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;

        $allReports = Quote::query()->withCount([
            'users as total_completed_users' => function ($query) {
                $query->whereHas('quotes', function ($subQuery) {
                    $subQuery->where('quote_user.status', 'completed');
                });

                $query->whereHas('roles', function ($subQuery) {
                    $subQuery->where('id', config('constants.role.user'));
                });
            },
            'users as total_skipped_users' => function ($query) {
                $query->whereHas('quotes', function ($subQuery) {
                    $subQuery->where('quote_user.status', 'skipped');
                });
                $query->whereHas('roles', function ($subQuery) {
                    $subQuery->where('id', config('constants.role.user'));
                });
            },
            // 'users as total_leave_users' => function ($query) {
            //     $query->whereHas('roles', function ($subQuery) {
            //         $subQuery->where('id', config('constants.role.user'));
            //     })->whereDoesntHave('quotes');
            // }
        ])->where(function ($query) use ($searchValue) {
            $query->whereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        });

        if(!is_null($startDate) && !is_null($endDate)){
            $allReports = $allReports->whereBetween('created_at', [$startDate, $endDate]);
        }

        $allReports =  $allReports->orderBy($this->sortColumnName, $this->sortDirection)
        ->paginate($this->paginationLength);
        
        return view('livewire.admin.mis-report.mis-report-table',compact('allReports'));
    }

    public function submitFilterForm(){
        $this->resetPage();
        
        $rules = [
            'filter_date_range' => 'required',
        ];
        $this->validate($rules,[
            'filter_date_range.required'=>'Please select date',
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

    public function exportToExcel($type,$exportFileName,$quoteId = null)
    {
        if($type == 'all'){
            return Excel::download(new AllQuoteExcelReportExport($this->filterStartDate,$this->filterEndDate,$this->search,$this->sortColumnName,$this->sortDirection), $exportFileName.'.xlsx');
        }elseif($type == 'single'){
            return Excel::download(new QuoteExcelReportExport($quoteId), $exportFileName.'.xlsx');
        }
    }

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }
}
