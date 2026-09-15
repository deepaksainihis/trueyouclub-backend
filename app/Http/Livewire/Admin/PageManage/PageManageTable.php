<?php

namespace App\Http\Livewire\Admin\PageManage;

use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class PageManageTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10;

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
        // if (Str::contains('active', strtolower($searchValue))) {
        //     $statusSearch = 1;
        // } else if (Str::contains('inactive', strtolower($searchValue))) {
        //     $statusSearch = 0;
        // }

        $allPages = Page::query()->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('page_name', 'like', '%' . $searchValue . '%')
                // ->orWhere('status', $statusSearch)
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        })
        ->where('status',1)
            ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);
        return view('livewire.admin.page-manage.page-manage-table', compact('allPages'));
    }
}
