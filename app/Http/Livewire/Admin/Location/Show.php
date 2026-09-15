<?php

namespace App\Http\Livewire\Admin\Location;

use App\Models\Location;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($location_id)
    {
        $this->detail = Location::find($location_id);
    }
    public function render()
    {
        return view('livewire.admin.location.show');
    }
    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
