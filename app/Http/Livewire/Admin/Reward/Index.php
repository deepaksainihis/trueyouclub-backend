<?php

namespace App\Http\Livewire\Admin\Reward;

use Livewire\Component;

use App\Models\Reward;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use LivewireAlert, WithFileUploads;

    public $updateMode = false, $modalType = '';
    public $reward_id, $title, $description, $points_cost, $stock, $image_path, $new_image;

    protected $listeners = ['cancel', 'show', 'edit', 'delete', 'deleteConfirm'];

    public function render()
    {
        $rewards = Reward::latest()->get();
        return view('livewire.admin.reward.index', compact('rewards'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function create()
    {
        $this->reset(['title', 'description', 'points_cost', 'stock', 'new_image']);
        $this->modalType = 'Add';
        $this->dispatchBrowserEvent('openModal');
    }

    public function store()
    {
        $validatedData = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_cost' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'new_image' => 'nullable|image|max:2048',
        ]);

        if ($this->new_image) {
            $validatedData['image_path'] = $this->new_image->store('rewards', 'public');
        }

        Reward::create($validatedData);

        $this->reset(['title', 'description', 'points_cost', 'stock', 'new_image']);
        $this->emit('refreshTable');
        $this->alert('success', 'Reward added successfully!');
    }

    public function edit($id)
    {
        $this->updateMode = true;
        $this->modalType = 'Edit';
        
        $reward = Reward::findOrFail($id);
        $this->reward_id = $reward->id;
        $this->title = $reward->title;
        $this->description = $reward->description;
        $this->points_cost = $reward->points_cost;
        $this->stock = $reward->stock;
        $this->image_path = $reward->image_path;

        $this->dispatchBrowserEvent('openModal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_cost' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'new_image' => 'nullable|image|max:2048',
        ]);

        $reward = Reward::find($this->reward_id);

        if ($this->new_image) {
            $validatedData['image_path'] = $this->new_image->store('rewards', 'public');
        }

        $reward->update($validatedData);

        $this->reset(['updateMode', 'new_image']);
        $this->emit('refreshTable');
        $this->alert('success', 'Reward updated successfully!');
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
        $model = Reward::find($deleteId);
        if ($model) {
            $model->delete();
            $this->emit('refreshTable');
            $this->alert('success', 'Deleted successfully');
        }
    }
}
