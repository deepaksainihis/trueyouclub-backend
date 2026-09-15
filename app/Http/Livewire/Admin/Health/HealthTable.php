<?php

namespace App\Http\Livewire\Admin\Health;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;


class HealthTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Title, Publish Date";

    public $type = 'health';

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
        if (Str::contains('active', strtolower($searchValue))) {
            $statusSearch = 1;
        } else if (Str::contains('inactive', strtolower($searchValue))) {
            $statusSearch = 0;
        }

        $currentDate = now()->format('Y-m-d');

        $allHealth = Post::query()->where('type', $this->type)->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                // ->orWhere('status', $statusSearch)
                ->orWhereRaw("date_format(publish_date, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        })

            ->orderByRaw('CASE 
                    WHEN publish_date >= "'.$currentDate.'" THEN 0
                    WHEN publish_date <= "'.$currentDate.'" THEN 1
                    ELSE 3
                END,
                publish_date'
            )
            // ->orderBy('publish_date', $this->sortDirection)

            // ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);

        return view('livewire.admin.health.health-table', compact('allHealth'));
    }
}
