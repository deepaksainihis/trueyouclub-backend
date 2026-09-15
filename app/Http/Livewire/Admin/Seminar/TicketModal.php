<?php

namespace App\Http\Livewire\Admin\Seminar;

use Livewire\Component;
use App\Models\Booking;

class TicketModal extends Component
{
    // protected $layout = null;

    public $booking_id, $bookingDetail, $showModal = false;

    public function mount($booking_id){
        $this->booking_id = $booking_id;
    }

    public function render()
    {
        return view('livewire.admin.seminar.ticket-modal');
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->bookingDetail = Booking::find($this->booking_id);
        $this->dispatchBrowserEvent('openTicketModal');
    }

    public function closeModal()
    { 
        $this->reset(['showModal','bookingDetail']);
        $this->dispatchBrowserEvent('closeTicketModal');
    }
}
