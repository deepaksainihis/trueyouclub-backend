<?php

namespace App\Http\Livewire\Admin\Habit;

use Livewire\Component;
use App\Models\Habit;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public $updateMode = false, $modalType = '';
    public $habit_id, $title, $frequency, $status;

    protected $listeners = ['cancel', 'show', 'edit', 'delete', 'deleteConfirm'];

    public function render()
    {
        $habits = Habit::latest()->get();
        return view('livewire.admin.habit.index', compact('habits'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function create()
    {
        $this->reset(['title', 'frequency', 'status']);
        $this->status = true; // default
        $this->frequency = 'daily'; // default
        $this->modalType = 'Add';
        $this->dispatchBrowserEvent('openModal');
    }

    public function store()
    {
        $validatedData = $this->validate([
            'title' => 'required|string|max:255',
            'frequency' => 'nullable|string',
            'status' => 'boolean',
        ]);

        Habit::create($validatedData);

        $this->reset(['title', 'frequency', 'status']);
        $this->emit('refreshTable');
        $this->alert('success', 'Habit added successfully!');
    }

    public function edit($id)
    {
        $this->updateMode = true;
        $this->modalType = 'Edit';
        
        $habit = Habit::findOrFail($id);
        $this->habit_id = $habit->id;
        $this->title = $habit->title;
        $this->frequency = $habit->frequency;
        $this->status = $habit->status;

        $this->dispatchBrowserEvent('openModal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'title' => 'required|string|max:255',
            'frequency' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $habit = Habit::find($this->habit_id);
        $habit->update($validatedData);

        $this->reset(['updateMode', 'title', 'frequency', 'status']);
        $this->emit('refreshTable');
        $this->alert('success', 'Habit updated successfully!');
    }

    public function cancel()
    {
        $this->reset(['updateMode', 'title', 'frequency', 'status']);
        $this->resetValidation();
    }

    public function delete($id)
    {
        $this->confirm('Are you sure?', [
            'text' => 'You want to delete it.',
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
        $model = Habit::find($deleteId);
        if ($model) {
            $model->delete();
            $this->emit('refreshTable');
            $this->alert('success', 'Deleted successfully');
        }
    }
}
