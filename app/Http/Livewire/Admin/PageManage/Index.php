<?php

namespace App\Http\Livewire\Admin\PageManage;

use App\Models\Page;
use Livewire\Component;
use Gate;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

use Symfony\Component\HttpFoundation\Response;

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    public $search = '', $formMode = false, $updateMode = false, $viewMode = false;

    public $page_id = null, $title, $page_name, $subtitle, $image, $originalImage, $status = 1;

    public $showAddMore = false, $button = [['title' => '', 'link' => '']], $link = [];

    public $removeImage = false;

    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm'
    ];

    public function mount()
    {
        abort_if(Gate::denies('page_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function render()
    {
        return view('livewire.admin.page-manage.index');
    }

    public function create()
    {
        $this->resetPage();

        $this->initializePlugins();
        $this->formMode = true;
    }

    // public function store()
    // {
    //     $validatedData = $this->validate([
    //         'title'        => 'required',
    //         'subtitle'  => 'required|strip_tags',
    //         'status'       => 'required',
    //         'page_name'     => 'required',
    //         'image'        => 'nullable|image|max:' . config('constants.img_max_size'),
    //         'button'       => 'nullable',
    //     ], [
    //         'content.strip_tags' => 'The content field is required',
    //     ]);

    //     DB::beginTransaction();
    //     try {

    //         $validatedData['title']         = $this->title;
    //         $validatedData['subtitle']   = $this->subtitle;
    //         $validatedData['status']        = $this->status;
    //         $validatedData['button']        = $this->button;
    //         $validatedData['page_name']      = $this->page_name;

    //         $page = Page::create($validatedData);

    //         if ($this->image) {
    //             //Upload Image
    //             uploadImage($page, $this->image, 'page/image/', "page", 'original', 'save', null);
    //         }

    //         $this->formMode = false;

    //         DB::commit();

    //         $this->reset(['title', 'page_name', 'subtitle', 'button', 'status', 'image']);

    //         $this->flash('success', trans('messages.add_success_message'));

    //         return redirect()->route('admin.page-manage');
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         $this->alert('error', trans('messages.error_message'));
    //     }
    // }

    public function edit($id)
    {
        $this->resetPage();

        $this->initializePlugins();
        $this->formMode = true;
        $this->updateMode = true;

        $page = Page::findOrFail($id);
        $this->page_id          =  $page->id;
        $this->page_name         =  $page->page_name;
        $this->title            =  $page->title;
        // $this->type             =  $page->type;
        $this->subtitle         =  $page->subtitle;
        $this->status           =  $page->status;
        $this->originalImage    =  $page->image_url;
        $this->button           =  json_decode($page->button);

        if ($page->button) {
            $this->showAddMore = true;
        }
    }


    public function update()
    {
        $validatedData = $this->validate([
            'title'             => 'required',
            'subtitle'          => 'required',
            'status'            => 'required',
            'page_name'          => 'required',
            'button.*.title'    => 'required|string',
            'button.*.link'        => 'required|url',
        ], [
            'button.*.title.required' => 'This field is required',
            'button.*.link.required'     => 'This field is required',
        ]);

        if ($this->image) {
            $rules['image'] = 'nullable|image|max:' . config('constants.img_max_size');
        }

        $validatedData['subtitle'] = $this->subtitle;
        $validatedData['status'] = $this->status;

        DB::beginTransaction();
        try {

            $page = Page::find($this->page_id);

            // Check if the image has been changed
            if ($this->image) {
                if ($page->pageImage) {
                    $uploadImageId = $page->pageImage->id;
                    uploadImage($page, $this->image, 'page/image/', "page", 'original', 'update', $uploadImageId);
                } else {
                    uploadImage($page, $this->image, 'page/image/', "page", 'original', 'save', null);
                }
            }

            if ($this->removeImage) {
                if ($page->pageImage) {
                    $uploadImageId = $page->pageImage->id;
                    deleteFile($uploadImageId);
                }
            }

            $validatedData['button'] = $this->button;


            $page->update($validatedData);

            $this->formMode = false;
            $this->updateMode = false;

            DB::commit();

            $this->flash('success', trans('messages.edit_success_message'));

            $this->reset(['title', 'page_name', 'subtitle', 'button', 'status', 'image']);

            return redirect()->route('admin.page-manage');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function show($id)
    {
        $this->resetPage();

        $this->page_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }

    public function cancel()
    {
        $this->resetPage();

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
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
            'inputAttributes' => ['deleteId' => $id],
        ]);
    }

    public function deleteConfirm($event)
    {
        $deleteId = $event['data']['inputAttributes']['deleteId'];
        $model    = Page::find($deleteId);
        if(!$model){
            $this->emit('refreshTable'); 
            $this->alert('error', trans('messages.error_message'));   
        }else{
            $model->delete();
            $this->emit('refreshTable');    
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
            'inputAttributes' => ['pageId' => $id],
        ]);
    }

    public function confirmedToggleAction($event)
    {
        $pageId = $event['data']['inputAttributes']['pageId'];
        $model = Page::find($pageId);
        $model->update(['status' => !$model->status]);

        $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->status = (!$statusVal) ? 1 : 0;
    }

    public function addMore()
    {
        $this->button[] = ['title' => '', 'link' => ''];
        $this->showAddMore = true;
    }

    public function remove($index)
    {
        unset($this->button[$index]);
        $this->button = array_values($this->button);
    }
}
