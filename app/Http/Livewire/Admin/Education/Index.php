<?php

namespace App\Http\Livewire\Admin\Education;

use App\Models\Category;
use App\Models\Education;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Str;

class Index extends Component
{
    use  LivewireAlert, WithFileUploads, WithPagination;

    public $search = null, $formMode = false, $updateMode = false, $viewMode = false;

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Title, Created Date";

    public $education_id = null, $title, $description, $video_link, $created_at ,$image, $originalImage, $video, $originalVideo,$videoExtenstion, $status = 1 ,$allcategory;
    public $category_id = ''; 
    public $video_type = ''; 

    public $removeImage = false,$removeVideo = false;    
    
    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm' ,'clearVideo'
    ];

    public function mount()
    {
        abort_if(Gate::denies('education_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->created_at = Carbon::now()->format('d-m-Y');
        $this->allcategory = Category::where('status',1)->get(); 
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

    public function swapSortDirection()
    {
        return $this->sortDirection === 'asc' ? 'desc' : 'asc';
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

        $allEducation = Education::query()->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('status', $statusSearch)
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        })
            
            ->orderByRaw('CASE 
                    WHEN created_at >= "'.$currentDate.'" THEN 0
                    WHEN created_at <= "'.$currentDate.'" THEN 1
                    ELSE 3
                END,
                created_at'
            )

            // ->orderBy('created_at', $this->sortDirection)

            // ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->paginationLength);


        return view('livewire.admin.education.index',compact('allEducation'));
    }

    public function updatedPublishDate()
    {
        $this->created_at = Carbon::parse($this->created_at)->format('d-m-Y');
    }

    public function create()
    {
        $this->initializePlugins();
        $this->formMode = true;
        $this->category_id      =  "";
        $this->allcategory = Category::where('status',1)->get(); 
    }

    public function store()
    {
        // dd($this->all());
        $this->initializePlugins();
        $validatedData = $this->validate([
            'title'         => 'required|string|max:150|unique:educations,title,NULL,id,deleted_at,NULL',                           
            'description'   => 'required|strip_tags',           
            'category_id'   => 'required|numeric|exists:categories,id',
            'status'        => 'required',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,svg',
            'video_type'    => 'required|in:'.implode(',',array_keys(config('constants.education_video_type'))),
            'video_link'    => 'nullable|string|required_if:video_type,video_link', 
            'video'         => 'nullable|file|mimes:mp4,avi,mov,wmv,webm,flv|required_if:video_type,upload_video', 
        ], [
            'description.strip_tags' => 'The description field is required',
            'category_id.required' => 'The Category is required.',
            'video_link.required_if' => 'The Video Link is required.',
            'video.required_if' => 'Please upload Video!',

        ],[
            'video_link' => 'Video Link'
        ]);

        DB::beginTransaction();
        try {
            $validatedData['status'] = $this->status;

            if($this->video_link){
                $videoLink = normalizeYouTubeEmbedUrl($this->video_link);
                $validatedData['video_link'] = $videoLink;
            }

            $education = Education::create($validatedData);

            if ($this->image) {
                uploadImage($education, $this->image, 'education/image/', "education", 'original', 'save', null);
            }

            if ($this->video) {
                $dateFolder = date("Y-m-W");                
                uploadImage($education, $this->video, 'education/video/', "education-video", 'original', 'save', null);
            }            

            DB::commit();

            $this->resetAllFields();

            $this->flash('success', trans('messages.add_success_message'));

            return redirect()->route('admin.education');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        $this->initializePlugins();
        $this->formMode = true;
        $this->updateMode = true;

        $education = Education::findOrFail($id);
        $this->education_id         =  $education->id;
        $this->title             =  $education->title;
        $this->description      =  $education->description;
        $this->category_id      =  $education->category_id;
        $this->video_type      =  $education->video_type;
        $this->video_link      =  $education->video_link;
        $this->status           =  $education->status;
        $this->originalImage    =  $education->featured_image_url;  
        $this->originalVideo  =  $education->educationVideo ? $education->education_video_url : '';
        $this->videoExtenstion = $education->educationVideo ? $education->educationVideo->extension : ""; 
        $this->allcategory = Category::where('status',1)->get();      
    }


    public function update()
    {   
        $rules = [
            'title'        => 'required|string|max:150|unique:educations,title,'.$this->education_id,           
            'description'   => 'required|strip_tags',  
            'category_id'   => 'required|numeric|exists:categories,id',
            'status'        => 'required',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,svg',
            'video_type'   => 'required|in:'.implode(',',array_keys(config('constants.education_video_type'))),
            'video_link'    => 'nullable|string|required_if:video_type,video_link', 
            'video'         => 'nullable|file|mimes:mp4,avi,mov,wmv,webm,flv',
        ];

        $education = Education::find($this->education_id);

        if ($this->removeVideo && ($this->video_type == 'upload_video')) {
            $rules['video']  = 'nullable|file|mimes:mp4,avi,mov,wmv,webm,flv|required_if:video_type,upload_video';
        }

        if( ($education->video_type  != 'upload_video') && ($this->video_type == 'upload_video') ){
            $rules['video']  = 'nullable|file|mimes:mp4,avi,mov,wmv,webm,flv|required_if:video_type,upload_video';
        }

        $validatedData = $this->validate($rules, [
            'description.strip_tags' => 'The description field is required',
            'category_id.required'   => 'The Category is required.',
            'video_link.required_if' => 'The Video Link is required.',
            'video.required_if'      => 'Please upload Video!',

        ],[
            'video_link' => 'Video Link'
        ]);


        $validatedData['status'] = $this->status;        

        DB::beginTransaction();
        try {

            $education = Education::find($this->education_id);

            // Check if the image has been changed
            if ($this->image) {
                if ($education->featuredImage) {
                    $uploadImageId = $education->featuredImage->id;
                    uploadImage($education, $this->image, 'education/image/', "education", 'original', 'update', $uploadImageId);
                } else {
                    uploadImage($education, $this->image, 'education/image/', "education", 'original', 'save', null);
                }
            }

            if ($this->removeImage) {
                if ($education->featuredImage) {
                    $uploadImageId = $education->featuredImage->id;
                    deleteFile($uploadImageId);
                }
            }

            // Check if video has been change

            if ($this->video) {
                if ($education->educationVideo) {
                    $uploadVideoId = $education->educationVideo->id;
                    uploadImage($education, $this->video, 'education/video/', "education-video", 'original', 'update', $uploadVideoId);
                } else {
                    uploadImage($education, $this->video, 'education/video/', "education-video", 'original', 'save', null);
                }
            }

            if ($this->removeVideo) {
                if ($education->educationVideo) {
                    $uploadVideoId = $education->educationVideo->id;
                    deleteFile($uploadVideoId);
                }
            }

            if($validatedData['video_type'] == 'video_link'){
                if($this->video_link){
                    $videoLink = normalizeYouTubeEmbedUrl($this->video_link);
                    $validatedData['video_link'] = $videoLink;
                }
            }else{
                $validatedData['video_link'] = null;
            }
            
            $education->update($validatedData);

            DB::commit();           

            $this->alert('success', trans('messages.edit_success_message'));
            $this->cancel();            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function show($id)
    {
        $this->education_id = $id;
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
        $this->reset(['formMode','updateMode','viewMode','education_id','title','video_link','description','image','originalImage','video','originalVideo','status','removeImage','removeVideo']);
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
        $model    = Education::find($deleteId);
        
        if(!$model){
            // $this->emit('refreshTable'); 
            $this->alert('error', trans('messages.error_message'));   
        }else{

            if($model->featuredImage){
                $uploadImageId = $model->featuredImage->id;
                deleteFile($uploadImageId);
            }

            if($model->educationVideo){
                $uploadVideoId = $model->educationVideo->id;
                deleteFile($uploadVideoId);
            }               
            
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
            'inputAttributes' => ['educationId' => $id],
        ]);
    }

    public function confirmedToggleAction($event)
    {
        $educationId = $event['data']['inputAttributes']['educationId'];
        $model = Education::find($educationId);
        $model->update(['status' => !$model->status]);

        // $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->status = (!$statusVal) ? 1 : 0;
    }


   
    public function clearVideo()
    {
        $this->video = null;
        $this->originalVideo = null;
        $this->removeVideo = true;
    }
}
