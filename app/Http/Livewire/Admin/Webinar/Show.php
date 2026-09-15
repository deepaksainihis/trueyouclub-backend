<?php

namespace App\Http\Livewire\Admin\Webinar;

use App\Models\Webinar;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;
    
    public $detail;

    public function mount($webinar_id){
        $this->detail = Webinar::find($webinar_id);
    }
   
    public function render()
    {
        return view('livewire.admin.webinar.show');
    }

    public function cancel(){
        $this->emitUp('cancel');
    }
}
