<?php

namespace App\Http\Livewire\Admin\Event;

use Livewire\Component;
use App\Models\EventRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;

class ShowAttendance extends Component
{
    use  LivewireAlert, WithPagination;
    protected $layout = null;
    public $search = null;
    public $sortColumnName = 'updated_at', $sortDirection = 'desc', $paginationLength = 10,$searchBoxPlaceholder = "Search By Name, Phone Number";
    protected  $allEventRequest= null;
    public $event_id = null;

    public function mount($event_id)
    {        
        $this->event_id = $event_id;             
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
        $statusSearch = 1;
        $starNumber = null;
        $allEventRequest = EventRequest::query()
        ->where('event_id', $this->event_id) // Filter by event_id
        ->whereHas('volunteer', function ($query) use ($searchValue, $statusSearch, $starNumber) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('phone', 'like', '%' . $searchValue . '%');

            if (!is_null($statusSearch)) {
                $query->where('is_active', $statusSearch);
            }

            if (!is_null($starNumber)) {
                $query->where('star_no', $starNumber);
            }
        })
        ->join('users', 'event_requests.volunteer_id', '=', 'users.id')
        ->orderBy('users.' . $this->sortColumnName, $this->sortDirection) // Sort by volunteer name or other fields
        ->select('event_requests.*'); // Select only the columns from event_requests

    // Apply pagination
    $allEventRequest = $allEventRequest->paginate($this->paginationLength);

        return view('livewire.admin.event.show-attendance',compact('allEventRequest'));
    }

    public function cancel()
    {
        $this->emitUp('cancel');
    }

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }

}
