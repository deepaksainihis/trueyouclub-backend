<?php

namespace App\Http\Livewire\Admin\Quote;

use App\Models\Quote;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;
    
    public $detail;

    public function mount($quote_id){
        $this->detail = Quote::find($quote_id);
    }
   
    public function render()
    {
        return view('livewire.admin.quote.show');
    }

    public function cancel(){
        $this->emitUp('cancel');
    }
}
