<?php

namespace App\Http\Livewire\Admin\ChatRoom;

use Livewire\Component;

use App\Models\ChatRoom;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public $updateMode = false, $modalType = '';
    public $room_id, $name, $description, $icon, $is_active = true, $share_history = true;

    protected $listeners = ['cancel', 'show', 'edit', 'delete', 'deleteConfirm'];

    public function render()
    {
        $rooms = ChatRoom::latest()->get();
        return view('livewire.admin.chat-room.index', compact('rooms'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function create()
    {
        $this->reset(['name', 'description', 'icon']);
        $this->is_active = true;
        $this->share_history = true;
        $this->modalType = 'Add';
        $this->dispatchBrowserEvent('openModal');
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'share_history' => 'boolean',
        ]);

        ChatRoom::create($validatedData);

        $this->reset(['name', 'description', 'icon']);
        $this->dispatchBrowserEvent('refreshTable');
        $this->alert('success', 'Room added successfully!');
    }

    public function edit($id)
    {
        $this->updateMode = true;
        $this->modalType = 'Edit';
        
        $room = ChatRoom::findOrFail($id);
        $this->room_id = $room->id;
        $this->name = $room->name;
        $this->description = $room->description;
        $this->icon = $room->icon;
        $this->is_active = $room->is_active;
        $this->share_history = $room->share_history;

        $this->dispatchBrowserEvent('openModal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'share_history' => 'boolean',
        ]);

        $room = ChatRoom::find($this->room_id);
        $room->update($validatedData);

        $this->reset(['updateMode']);
        $this->dispatchBrowserEvent('refreshTable');
        $this->alert('success', 'Room updated successfully!');
    }

    public function cancel()
    {
        $this->reset();
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
        $model = ChatRoom::find($deleteId);
        if ($model) {
            $model->delete();
            $this->emit('refreshTable');
            $this->alert('success', 'Deleted successfully');
        }
    }
}
