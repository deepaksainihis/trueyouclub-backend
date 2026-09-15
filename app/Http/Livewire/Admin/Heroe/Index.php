<?php

namespace App\Http\Livewire\Admin\Heroe;

use App\Models\Heroe;
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

    public $sortColumnName = 'created_at', $sortDirection = 'desc', $paginationLength = 10, $searchBoxPlaceholder = "Search By Name, Created Date";

    public $heroe_id = null, $name, $description, $created_at ,$image, $originalImage, $status = 1;

    public $removeImage = false;    
    
    protected $listeners = [
        'cancel', 'show', 'edit', 'toggle', 'confirmedToggleAction', 'delete', 'deleteConfirm'
    ];

    public function mount()
    {
        abort_if(Gate::denies('heroes_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->created_at = Carbon::now()->format('d-m-Y');
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

        $allHeroe = Heroe::query()->where(function ($query) use ($searchValue, $statusSearch) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                // ->orWhere('status', $statusSearch)
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


        return view('livewire.admin.heroe.index',compact('allHeroe'));
    }

    public function updatedPublishDate()
    {
        $this->created_at = Carbon::parse($this->created_at)->format('d-m-Y');
    }

    public function create()
    {
        $this->initializePlugins();
        $this->formMode = true;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name'        => 'required',           
            'description'      => 'required|strip_tags',
            'status'       => 'required',
            'image'        => 'nullable|image|max:' . config('constants.img_max_size'),
        ], [
            'description.strip_tags' => 'The description field is required',
        ]);

        DB::beginTransaction();
        try {
            $validatedData['status'] = $this->status;            
            $heroe = Heroe::create($validatedData);

            if ($this->image) {
                uploadImage($heroe, $this->image, 'heroe/image/', "heroe", 'original', 'save', null);
            }

            DB::commit();

            $this->resetAllFields();

            $this->flash('success', trans('messages.add_success_message'));

            return redirect()->route('admin.heroes');
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

        $heroe = Heroe::findOrFail($id);
        $this->heroe_id         =  $heroe->id;
        $this->name             =  $heroe->name;
        $this->description      =  $heroe->description;
        $this->status           =  $heroe->status;
        $this->originalImage    =  $heroe->featured_image_url;        
    }


    public function update()
    {  
        $validatedData = $this->validate([
            'name'        => 'required',           
            'description'      => 'required|strip_tags',
            'status'       => 'required',
            'image'        => 'nullable|image|max:' . config('constants.img_max_size'),
        ], [
            'description.strip_tags' => 'The description field is required',
        ]);

        $validatedData['status'] = $this->status;        

        DB::beginTransaction();
        try {

            $heroe = Heroe::find($this->heroe_id);

            // Check if the image has been changed
            if ($this->image) {
                if ($heroe->featuredImage) {
                    $uploadImageId = $heroe->featuredImage->id;
                    uploadImage($heroe, $this->image, 'heroe/image/', "heroe", 'original', 'update', $uploadImageId);
                } else {
                    uploadImage($heroe, $this->image, 'heroe/image/', "heroe", 'original', 'save', null);
                }
            }

            if ($this->removeImage) {
                if ($heroe->featuredImage) {
                    $uploadImageId = $heroe->featuredImage->id;
                    deleteFile($uploadImageId);
                }
            }

            $heroe->update($validatedData);

            DB::commit();

            // $this->flash('success', trans('messages.edit_success_message'));

            $this->alert('success', trans('messages.edit_success_message'));

            $this->cancel();

            // return redirect()->route('admin.heroes');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->alert('error', trans('messages.error_message'));
        }
    }

    public function show($id)
    {
        $this->heroe_id = $id;
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
        $this->reset(['formMode','updateMode','viewMode','heroe_id','name','description','image','originalImage','status','removeImage']);
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
        $model    = Heroe::find($deleteId);
        
        if(!$model){
            // $this->emit('refreshTable'); 
            $this->alert('error', trans('messages.error_message'));   
        }else{
            if($model->featuredImage){
                $uploadImageId = $model->featuredImage->id;
                deleteFile($uploadImageId);
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
            'inputAttributes' => ['heroeId' => $id],
        ]);
    }

    public function confirmedToggleAction($event)
    {
        $heroeId = $event['data']['inputAttributes']['heroeId'];
        $model = Heroe::find($heroeId);
        $model->update(['status' => !$model->status]);

        // $this->emit('refreshTable');

        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal)
    {
        $this->status = (!$statusVal) ? 1 : 0;
    }
}
