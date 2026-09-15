<?php

namespace App\Http\Livewire\Admin\PageManage;

use App\Models\Page;
use Livewire\Component;

class Show extends Component
{
    protected $layout = null;

    public $detail;

    public function mount($page_id)
    {
        $this->detail = Page::find($page_id);
    }
    public function render()
    {
        return view('livewire.admin.page-manage.show');
    }
    public function cancel()
    {
        $this->emitUp('cancel');
    }
}
