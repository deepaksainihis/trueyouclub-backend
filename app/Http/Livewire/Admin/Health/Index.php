<?php

namespace App\Http\Livewire\Admin\Health;

use App\Models\Post;
use Gate;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    public $search = null, $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Title, Publish Date";

    public $formMode = false, $updateMode = false, $viewMode = false;

    public $health_id = null, $title,  $publish_date = null,  $content, $image, $originalImage, $removeImage = false, $status = 1;

    public $type = 'health';


    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm'
    ];

    public function mount()
    {
        abort_if(Gate::denies('health_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->publish_date = Carbon::now()->format('d-m-Y');
    }

    public function updatedPaginationLength()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($columnName)
    {
        if ($this->sortColumnName === $columnName) {
            $this->sortDirection = $this->swapSortDirection();
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortColumnName = $columnName;
    }

    public function render()
    {
        $statusSearch = null;
        $searchValue = $this->search;
        if (Str::contains('active', strtolower($searchValue))) {
            $statusSearch = 1;
        } else if (Str::contains('inactive', strtolower($searchValue))) {
            $statusSearch = 0;
        }

        $currentDate = now()->format('Y-m-d');

        $allHealth = Post::query()->where('type', $this->type)->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                // ->orWhere('status', $statusSearch)
                ->orWhereRaw("date_format(publish_date, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        })

            ->orderByRaw('CASE 
                    WHEN publish_date >= "'.$currentDate.'" THEN 0
                    WHEN publish_date <= "'.$currentDate.'" THEN 1
                    ELSE 3
                END,
                publish_date'
            )
            // ->orderBy('publish_date', $this->sortDirection)

            // ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);

        return view('livewire.admin.health.index',compact('allHealth'));
    }

    public function updatedPublishDate()
    {
        $this->publish_date = Carbon::parse($this->publish_date)->format('d-m-Y');
    }

    public function create()
    {
        $this->initializePlugins();
        $this->formMode = true;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'title'        => 'required',
            'publish_date' => 'required',
            'content'      => 'required|strip_tags',
            'status'       => 'required',
            'image'        => 'nullable|image|max:' . config('constants.img_max_size'),
        ], [
            'content.strip_tags' => 'The content field is required',
        ]);

        DB::beginTransaction();
        try {

            $validatedData['publish_date']   = Carbon::parse($this->publish_date)->format('Y-m-d');

            $validatedData['status'] = $this->status;
            $validatedData['type'] = $this->type;

            $health = Post::create($validatedData);

            if ($this->image) {
                uploadImage($health, $this->image, 'health/image/', "health", 'original', 'save', null);
            }

            DB::commit();

            $this->resetAllFields();

            $this->flash('success', trans('messages.add_success_message'));

            return redirect()->route('admin.health');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        $this->formMode = true;
        $this->updateMode = true;

        $health = Post::findOrFail($id);
        $this->health_id       =  $health->id;
        $this->title           =  $health->title;
        $this->publish_date    =  Carbon::parse($health->publish_date)->format('d-m-Y');
        $this->content         =  $health->content;
        $this->status          =  $health->status;
        $this->originalImage   =  $health->health_image_url;
        $this->type            =  $health->type;
        $this->initializePlugins();

    }


    public function update()
    {
        $rules['title']        = 'required';
        $rules['publish_date'] = 'required';
        $rules['content']      = 'required|strip_tags';
        $rules['status']       = 'required';

        if ($this->image) {
            $rules['image'] = 'nullable|image|max:' . config('constants.img_max_size');
        }

        $validatedData = $this->validate($rules, [
            'content.strip_tags' => 'The content field is required',
        ]);

        $validatedData['publish_date']   = Carbon::parse($this->publish_date)->format('Y-m-d');
        $validatedData['status'] = $this->status;
        $validatedData['type']   = $this->type;

        DB::beginTransaction();
        try {

            $health = Post::find($this->health_id);

            // Check if the image has been changed
            if ($this->image) {
                if ($health->healthImage) {
                    $uploadImageId = $health->healthImage->id;
                    uploadImage($health, $this->image, 'health/image/', "health", 'original', 'update', $uploadImageId);
                } else {
                    uploadImage($health, $this->image, 'health/image/', "health", 'original', 'save', null);
                }
            }

            if ($this->removeImage) {
                if ($health->healthImage) {
                    $uploadImageId = $health->healthImage->id;
                    deleteFile($uploadImageId);
                }
            }

            $health->update($validatedData);

            DB::commit();

            $this->alert('success', trans('messages.edit_success_message'));

            $this->cancel();

            // return redirect()->route('admin.health');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function show($id)
    {
        $this->health_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }

    public function cancel()
    {
        $this->resetAllFields();
        $this->resetValidation();
    }

    public function resetAllFields(){
        $this->reset(['formMode','updateMode','viewMode','health_id','publish_date','title','content','image','originalImage','removeImage','status']);
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
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
            'inputAttributes' => ['deleteId' => $id],
        ]);
    }

    public function deleteConfirm($event)
    {
        $deleteId = $event['data']['inputAttributes']['deleteId'];
        $model    = Post::find($deleteId);
        if(!$model){
            // $this->emit('refreshTable'); 
            $this->alert('error', trans('messages.error_message'));   
        }else{
            $model->delete();
            $this->resetPage();
            // $this->emit('refreshTable');    
            $this->alert('success', trans('messages.delete_success_message'));
        }     
    }

    public function toggle($id)
    {
        $this->confirm('Are you sure?', [
            'text' => 'You want to change the status.',
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Yes Confirm!',
            'cancelButtonText' => 'No Cancel!',
            'onConfirmed' => 'confirmedToggleAction',
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
            'inputAttributes' => ['healthId' => $id],
        ]);
    }

    public function confirmedToggleAction($event)
    {
        $healthId = $event['data']['inputAttributes']['healthId'];
        $model = Post::find($healthId);
        $model->update(['status' => !$model->status]);

        // $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->status = (!$statusVal) ? 1 : 0;
    }
}
