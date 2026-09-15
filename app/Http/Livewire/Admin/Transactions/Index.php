<?php

namespace App\Http\Livewire\Admin\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class Index extends Component
{
    public $viewMode = false, $transaction_id;

    protected $listeners = [
        'show','cancel'
    ];

    public function mount(){
        abort_if(Gate::denies('transaction_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }
    
    public function render()
    {
        return view('livewire.admin.transactions.index');
    }
    
    public function show($id){
        $this->transaction_id = $id;
        $this->viewMode = true;
    }
    
     public function cancel(){
        $this->viewMode = false;
        $this->reset();
    }
}
