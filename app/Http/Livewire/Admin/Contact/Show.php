<?php

namespace App\Http\Livewire\Admin\Contact;

use App\Models\Contact;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($contact_id)
    {
        $this->detail = Contact::find($contact_id);
    }
    public function render()
    {
        return view('livewire.admin.contact.show');
    }
    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
