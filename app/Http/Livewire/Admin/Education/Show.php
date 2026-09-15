<?php

namespace App\Http\Livewire\Admin\Education;

use App\Models\Education;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($education_id)
    {
        $this->detail = Education::find($education_id);
    }

    public function render()
    {
        return view('livewire.admin.education.show');
    }

    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
