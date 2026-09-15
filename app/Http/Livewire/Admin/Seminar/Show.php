<?php

namespace App\Http\Livewire\Admin\Seminar;

use App\Models\Seminar;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;
    
    public $detail;

    public function mount($seminar_id){
        $this->detail = Seminar::find($seminar_id);
    }

    public function render()
    {
        return view('livewire.admin.seminar.show');
    }

    public function cancel(){
        $this->emitUp('cancel');
    }
}
