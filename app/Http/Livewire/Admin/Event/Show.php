<?php

namespace App\Http\Livewire\Admin\Event;

use App\Models\Event;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($event_id)
    {
        $this->detail = Event::find($event_id);
    }
    public function render()
    {
        return view('livewire.admin.event.show');
    }
    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
