<?php

namespace App\Http\Livewire\Admin\MisReport;

use Gate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class Index extends Component
{
    protected $listeners = [
        'cancel',
    ];

    public function mount()
    {
        abort_if(Gate::denies('mis_report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function render()
    {
        return view('livewire.admin.mis-report.index');
    }

    public function cancel()
    {
        $this->reset();
        $this->resetValidation();
        $this->initializePlugins();
    }
}
