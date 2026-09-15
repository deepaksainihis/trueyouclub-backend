<?php

namespace App\Http\Livewire\Admin\Badge;

use Livewire\Component;
use App\Models\Badge;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert, WithFileUploads;

    public $updateMode = false, $modalType = '';
    public $badge_id, $name, $points_required, $type = 'badge', $image, $new_image;

    protected $listeners = ['cancel', 'show', 'edit', 'delete', 'deleteConfirm'];

    public function render()
    {
        $badges = Badge::orderBy('points_required', 'asc')->get();
        return view('livewire.admin.badge.index', compact('badges'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function create()
    {
        $this->reset(['name', 'points_required', 'type', 'new_image']);
        $this->modalType = 'Add';
        $this->dispatchBrowserEvent('openModal');
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'points_required' => 'required|integer|min:1',
            'type' => 'required|in:badge,trophy',
            'new_image' => 'nullable|image|max:2048',
        ]);

        if ($this->new_image) {
            $validatedData['image'] = $this->new_image->store('badges', 'public');
        }

        Badge::create($validatedData);

        $this->reset(['name', 'points_required', 'type', 'new_image']);
        $this->emit('refreshTable');
        $this->alert('success', 'Badge added successfully!');
    }

    public function edit($id)
    {
        $this->updateMode = true;
        $this->modalType = 'Edit';
        
        $badge = Badge::findOrFail($id);
        $this->badge_id = $badge->id;
        $this->name = $badge->name;
        $this->points_required = $badge->points_required;
        $this->type = $badge->type;
        $this->image = $badge->image;

        $this->dispatchBrowserEvent('openModal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'points_required' => 'required|integer|min:1',
            'type' => 'required|in:badge,trophy',
            'new_image' => 'nullable|image|max:2048',
        ]);

        $badge = Badge::find($this->badge_id);

        if ($this->new_image) {
            $validatedData['image'] = $this->new_image->store('badges', 'public');
        }

        $badge->update($validatedData);

        $this->reset(['updateMode', 'new_image']);
        $this->emit('refreshTable');
        $this->alert('success', 'Badge updated successfully!');
    }

    public function cancel()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function delete($id)
    {
        $this->confirm('Are you sure?', [
            'text' => 'You want to delete this badge.',
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
        $model = Badge::find($deleteId);
        if ($model) {
            $model->delete();
            $this->emit('refreshTable');
            $this->alert('success', 'Deleted successfully');
        }
    }
}
