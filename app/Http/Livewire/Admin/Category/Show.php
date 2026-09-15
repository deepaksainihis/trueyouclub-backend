<?php

namespace App\Http\Livewire\Admin\Category;

use App\Models\Category;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($category_id)
    {
        $this->detail = Category::find($category_id);
    }

    public function render()
    {
        return view('livewire.admin.category.show');
    }

    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
