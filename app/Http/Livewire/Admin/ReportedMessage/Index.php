<?php

namespace App\Http\Livewire\Admin\ReportedMessage;

use Livewire\Component;
use App\Models\ReportedMessage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    protected $listeners = ['deleteConfirm'];

    public function render()
    {
        $reports = ReportedMessage::with(['reporter', 'room'])->latest()->get();
        return view('livewire.admin.reported-message.index', compact('reports'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function delete($id)
    {
        $this->confirm('Are you sure?', [
            'text' => 'You want to delete this report record.',
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Yes, delete it!',
            'cancelButtonText' => 'No, cancel!',
            'onConfirmed' => 'deleteConfirm',
            'inputAttributes' => ['deleteId' => $id],
        ]);
    }

    public function deleteConfirm($event)
    {
        $deleteId = $event['data']['inputAttributes']['deleteId'];
        $model = ReportedMessage::find($deleteId);
        if ($model) {
            $model->delete();
            $this->alert('success', 'Deleted successfully');
        }
    }
}
