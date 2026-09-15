<?php

namespace App\Http\Livewire\Admin\Quote;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Quote;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class QuoteTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder="Search By Quote, Created Date";

    protected $listeners = [
        'refreshTable' => 'render',
    ];

    public function updatedPaginationLength()
    {
        $this->resetPage();
    }


    public function updatePaginationLength($length)
    {
        $this->resetPage();
        $this->paginationLength = $length;
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

        $allQuote = Quote::query()->where(function ($query) use ($searchValue) {
            $query->where('message', 'like', '%' . $searchValue . '%')
            ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);

        })->whereDate('created_at','<',Carbon::now())
            ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);

        return view('livewire.admin.quote.quote-table', compact('allQuote'));
    }
}
