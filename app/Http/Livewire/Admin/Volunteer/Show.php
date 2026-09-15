<?php

namespace App\Http\Livewire\Admin\Volunteer;

use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($user_id)
    {
        $this->detail = User::find($user_id);
    }
    public function render()
    {
        return view('livewire.admin.volunteer.show');
    }
    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
