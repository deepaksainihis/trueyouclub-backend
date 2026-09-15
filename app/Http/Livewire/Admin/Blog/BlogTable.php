<?php

namespace App\Http\Livewire\Admin\Blog;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;


class BlogTable extends Component
{
    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Title, Publish Date";

    public $type = 'blog';

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
        if (Str::contains('active', strtolower($searchValue))) {
            $statusSearch = 1;
        } else if (Str::contains('inactive', strtolower($searchValue))) {
            $statusSearch = 0;
        }

        $currentDate = now()->format('Y-m-d');

        $allBlogs = Post::query()->where('type', $this->type)->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                // ->orWhere('status', $statusSearch)
                ->orWhereRaw("DATE_FORMAT(publish_date,  '" . config('constants.search_full_datetime_format') . "') = ?", [date(config('constants.full_datetime_format'), strtotime($searchValue))]);
            // ->orWhereRaw("date_format(created_at, '" . config('constants.search_datetime_format') . "') like ?", ['%' . $searchValue . '%']);
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

        return view('livewire.admin.blog.blog-table', compact('allBlogs'));
    }
}
