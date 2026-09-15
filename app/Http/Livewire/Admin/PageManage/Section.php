<?php

namespace App\Http\Livewire\Admin\PageManage;

use Livewire\Component;
use App\Models\Page;
use App\Models\Section as SectionModel;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;


class Section extends Component
{
    use  LivewireAlert, WithFileUploads;

    public $page = null, $selectedSection;

    public $image, $originalImage, $removeImage = false;

    public $content_text;

    protected $listeners = [
        'changeTab'
    ];

    public function mount($slug){
        $this->page = Page::where('slug',$slug)->where('status',1)->first();

        if(!$this->page){
            return abort(404);
        }
        else{
            $this->selectedSection = $this->page->sections()->where('status',1)->first();
            $this->changeTab($this->selectedSection->id);
        }
    }

    public function render()
    {
        $allSections = $this->page->sections()->where('status',1)->get();
        return view('livewire.admin.page-manage.section',compact('allSections'));
    }

    public function changeTab($sectionId){
        $this->selectedSection = SectionModel::where('id',$sectionId)->first();
        $this->content_text = $this->selectedSection->content_text ?? null;
        $this->originalImage = $this->selectedSection->image_url ?? null;
        
        $this->initializePlugins();
    }

    public function update(){

        $rules['content_text'] = 'required|strip_tags';

        if($this->selectedSection->is_image){
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg|max:' . config('constants.img_max_size');
        }
       
        $customMessages = [
            'required' => 'The field is required.',
            'strip_tags'=>'The field is required.',
        ];

        $validatedData = $this->validate($rules,$customMessages);

        DB::beginTransaction();
        try {

            $fetchRec = SectionModel::find($this->selectedSection->id);

            $fetchRec->content_text = $this->content_text;
            
            $uploadId = $fetchRec->sectionImage ? $fetchRec->sectionImage->id : null;

            if ($this->image && (!$this->removeImage)) {
                if($uploadId){
                    uploadImage($fetchRec, $this->image, 'section/images/',"page-section-image", 'original', 'update', $uploadId);
                }else{
                    uploadImage($fetchRec, $this->image, 'section/images/',"page-section-image", 'original', 'save', null);
                }
            }else{
                if($uploadId && $this->removeImage){
                    deleteFile($uploadId);
                }
            }

            $fetchRec->save();
            DB::commit();

            $this->resetFields();
          
            $this->alert('success',trans('messages.edit_success_message'));

        }catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            $this->alert('error',trans('messages.error_message'));
        }

    }

    public function initializePlugins()
    {
        $this->dispatchBrowserEvent('loadPlugins');
    }

    public function resetFields(){  
        $this->reset(['image']);
    }

    public function cancel(){
        return redirect()->route('admin.page-manage');
    }

   
}
