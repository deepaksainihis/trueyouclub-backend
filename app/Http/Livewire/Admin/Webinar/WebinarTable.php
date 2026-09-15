<?php

namespace App\Http\Livewire\Admin\Webinar;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Webinar;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class WebinarTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'asc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Title, Date";

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

        $allWebinar = Webinar::query()
        ->select('*')
        ->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
        ->where(function ($query) use ($searchValue, $statusSearch) {

            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhereRaw("DATE_FORMAT(start_date,  '" . config('constants.search_full_date_format') . "') = ?", [date(config('constants.full_date_format'), strtotime($searchValue))]);

            // Check for month name (e.g., January)
            $query->orWhereRaw('LOWER(DATE_FORMAT(start_date, "%M")) LIKE ?', ['%' . strtolower($searchValue) . '%']);

            // Check for day and month (e.g., 13 January)
            $query->orWhereRaw('LOWER(DATE_FORMAT(start_date, "%e %M")) LIKE ?', ['%' . strtolower($searchValue) . '%']);
        })
        ->orderByRaw('
            CASE 
                WHEN CONCAT(start_date, " ", end_time) <= NOW() AND CONCAT(start_date, " ", end_time) >= NOW() THEN 0
                WHEN CONCAT(start_date, " ", end_time) > NOW() THEN 1
                ELSE 2
            END ASC,
            time_diff_seconds > 0 ASC, ABS(time_diff_seconds) ASC
        ')
        //->orderBy($this->sortColumnName, $this->sortDirection)
        ->paginate($this->paginationLength);

        return view('livewire.admin.webinar.webinar-table', compact('allWebinar'));
    }

}
