<?php

namespace App\Http\Livewire\Admin\Heroe;

use App\Models\Heroe;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($heroe_id)
    {
        $this->detail = Heroe::find($heroe_id);
    }

    public function render()
    {
        return view('livewire.admin.heroe.show');
    }

    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
