<?php

namespace App\Http\Livewire\User;

use Livewire\Component;

class Index extends Component
{
    protected $layout = null;
    public function render()
    {
        return view('livewire.user.index');
    }
}
