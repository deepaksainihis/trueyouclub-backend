<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quote;
use App\Models\User;

class QuoteHistory extends Component
{
    public $userId;

    use WithPagination;

    public $search = null;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder="Search By Quote, Status, Created Date & Time";

    protected $listeners = [
        'refreshTable' => 'render',
        'updatePaginationLength',
    ];

    public function mount($user_id){
        $this->userId = $user_id;
    }

    public function updatePaginationLength($length)
    {
        $this->resetPage();
        $this->paginationLength = $length;
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

        $user_id = $this->userId;

        $userName = User::where('id',$user_id)->value('name');

        $quotesHistory = Quote::query()->with('users')->where(function ($query) use ($searchValue) {
            $query->where('message', 'like', '%' . $searchValue . '%')
                ->orWhereHas('users', function ($subquery) use ($searchValue) {
                    $subquery->where('quote_user.status', 'like', '%' . $searchValue . '%')
                        ->orWhereRaw("date_format(quote_user.created_at, '" . config('constants.search_full_datetime_format') . "') like ?", ['%' . $searchValue . '%'])
                        ->orderBy('quote_user.created_at', 'desc');
                });
        })
        ->whereHas('users', function ($subquery) use ($user_id) {
            $subquery->where('user_id', $user_id);
        })
        // ->withTrashed()
        ->orderBy($this->sortColumnName, $this->sortDirection)
        ->paginate($this->paginationLength);

        $totalSkipped = Quote::whereHas('users', function ($subquery) use ($user_id) {
            $subquery->where('user_id', $user_id)->where('status','skipped');
        })
        // ->withTrashed()
        ->get()->count();

        $totalAttendence = Quote::whereHas('users', function ($subquery) use ($user_id) {
            $subquery->where('user_id', $user_id)->where('status','completed');
        })
        // ->withTrashed()
        ->get()->count(); 

        return view('livewire.admin.user.quote-history',compact('quotesHistory','userName','totalSkipped','totalAttendence'));
    }

    public function cancel(){
        $this->emitUp('cancel');
    }
}
