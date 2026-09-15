<?php

namespace App\Http\Livewire\Admin\DailyContent;

use Gate;
use Livewire\Component;
use App\Models\DailyContent;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class Index extends Component
{
    use LivewireAlert, WithFileUploads;

    public $updateMode = false, $modalType = '';
    public $content_id, $scheduled_date, $type = 'micro_learning', $title, $description, $media_path, $new_media;
    public $removeImage = false;

    protected $listeners = [
        'cancel', 'show', 'edit', 'delete', 'deleteConfirm'
    ];

    public function render()
    {
        $contents = DailyContent::orderBy('scheduled_date', 'desc')->get();
        return view('livewire.admin.daily-content.index', compact('contents'));
    }

    public function create()
    {
        $this->reset(['scheduled_date', 'type', 'title', 'description', 'new_media']);
        $this->modalType = 'Add';
        $this->dispatchBrowserEvent('openModal');
    }

    public function store()
    {
        $validatedData = $this->validate([
            'scheduled_date' => 'required|date',
            'type' => 'required|in:micro_learning,audio,video',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'new_media' => 'nullable|file|mimes:jpeg,png,mp3,wav,mp4,mov|max:20480',
        ]);

        if ($this->new_media) {
            $validatedData['media_path'] = $this->new_media->store('daily_contents', 'public');
        }

        DailyContent::create($validatedData);

        if (Carbon::parse($validatedData['scheduled_date'])->isToday()) {
            \Illuminate\Support\Facades\Artisan::call('push:daily-content');
        }

        $this->reset(['scheduled_date', 'type', 'title', 'description', 'new_media']);
        $this->emit('refreshTable');
        $this->alert('success', 'Daily content added successfully!');
    }

    public function edit($id)
    {
        $this->updateMode = true;
        $this->modalType = 'Edit';
        
        $content = DailyContent::findOrFail($id);
        $this->content_id = $content->id;
        $this->scheduled_date = Carbon::parse($content->scheduled_date)->format('Y-m-d');
        $this->type = $content->type;
        $this->title = $content->title;
        $this->description = $content->description;
        $this->media_path = $content->media_path;

        $this->dispatchBrowserEvent('openModal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'scheduled_date' => 'required|date',
            'type' => 'required|in:micro_learning,audio,video',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'new_media' => 'nullable|file|mimes:jpeg,png,mp3,wav,mp4,mov|max:20480',
        ]);

        $content = DailyContent::find($this->content_id);

        if ($this->new_media) {
            $validatedData['media_path'] = $this->new_media->store('daily_contents', 'public');
        }

        $content->update($validatedData);

        if (Carbon::parse($validatedData['scheduled_date'])->isToday()) {
            \Illuminate\Support\Facades\Artisan::call('push:daily-content');
        }

        $this->reset(['updateMode']);
        $this->emit('refreshTable');
        $this->alert('success', 'Daily content updated successfully!');
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
        $model = DailyContent::find($deleteId);
        if ($model) {
            $model->delete();
            $this->emit('refreshTable');
            $this->alert('success', 'Deleted successfully');
        }
    }
}
