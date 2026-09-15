<?php

namespace App\Http\Livewire\Admin\Transactions;

use Livewire\Component;
use App\Models\Transaction;

class Show extends Component
{
    public $details;
    
    public function mount($transaction_id){
        $this->details = Transaction::find($transaction_id);
    }
    
    public function render()
    {
        return view('livewire.admin.transactions.show');
    }
    
    public function cancel(){
     $this->emit('cancel');
    }
}

