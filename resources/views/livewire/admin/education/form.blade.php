
<h4 class="card-title">
    {{ $updateMode ? __('global.edit') : __('global.create') }}
    {{ __('cruds.education.title_singular')}}</h4>

<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}" class="forms-sample">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.education.fields.title')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="title" placeholder="{{ __('cruds.education.fields.title')}}" autocomplete="off">
                @error('title') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.education.fields.select_video_type') }}<i class="fas fa-asterisk"></i></label>
                <select class="form-control" wire:model="video_type" id="video_type_select">
                    <option value="" disabled {{ is_null($category_id) ? 'selected' : '' }}>{{ __('cruds.education.fields.select_video_type') }}</option>
                    @foreach(config('constants.education_video_type') as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @error('video_type') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
    
    @if($video_type === 'video_link')
    <div class="row" id="video_link_input" style="display: none;">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.education.fields.video_link')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="video_link" placeholder="{{ __('cruds.education.fields.video_link')}}" autocomplete="off">
                <span class="text-muted" style="font-size: 12px;display: block;margin-top: 3px;">Note:Please enter a valid video embed link (e.g., from YouTube or Vimeo). Direct video file links are not supported.</span>
                @error('video_link') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
    @endif

    @if($video_type === 'upload_video')
    <div class="row logo-section" id="video_upload_input" style="display: none;">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0" wire:ignore>
                <div class="d-flex align-items-end justify-content-between mb-2">
                    <label class="font-weight-bold justify-content-start position-relative">{{ __('global.video')}}<i class="fas fa-asterisk"></i></label>   
                    <!-- Preview Button -->                
                    <button type="button" id="preview-video-btn" class="btn btn-secondary mt-2" style="{{ $updateMode ? '' : 'display: none;' }}" title="Preview Video">
                        <x-svg-icon icon="preview" />                            
                    </button>  
                </div>            
                <input type="file" id="dropify-video" wire:model.defer="video" class="dropify" data-default-file="{{ $originalVideo }}"  data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="mp4"  accept="video/mp4,video/x-m4v,video/*">                              
                
                <span wire:loading wire:target="video">
                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i> {{__('global.loading')}}
                </span>
            </div>
            @if($errors->has('video'))
            <span class="error text-danger">
                {{ $errors->first('video') }}
            </span>
            @endif
        </div>
    </div>  
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.education.fields.select_category') }}<i class="fas fa-asterisk"></i></label>
                <select class="form-control" wire:model.defer="category_id">
                    <option value="" disabled {{ is_null($category_id) ? 'selected' : '' }}>{{ __('cruds.education.fields.select_category') }}</option>
                    @foreach($allcategory as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0" wire:ignore>
                <label class="font-weight-bold justify-content-start">{{ __('cruds.education.fields.description')}}<i class="fas fa-asterisk"></i></label>
                <textarea class="form-control" id="summernote" wire:model.defer="description" rows="4"></textarea>
            </div>
            @error('description') <span class="error text-danger">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="row logo-section">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0" wire:ignore>
                <label class="font-weight-bold justify-content-start">{{ __('global.image')}}</label>
                <input type="file" id="dropify-image" wire:model.defer="image" class="dropify" data-default-file="{{ $originalImage }}"  data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="jpeg png jpg svg" data-min-file-size-preview="1M" data-max-file-size-preview="3M"  accept="image/jpeg, image/png, image/jpg,image/svg">
                <span wire:loading wire:target="image">
                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i> {{__('global.loading')}}
                </span>
            </div>
            @if($errors->has('image'))
            <span class="error text-danger">
                {{ $errors->first('image') }}
            </span>
            @endif
        </div>
    </div>    

    

    <button type="submit" id="updateButton" wire:loading.attr="disabled" class="btn btn-primary mr-2">
        {{ $updateMode ? __('global.update') : __('global.submit') }}
        <span wire:loading wire:target="{{ $updateMode ? 'update' : 'store' }}">
            <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
        </span>
    </button>
    <button wire:click.prevent="cancel" class="btn btn-secondary">
        {{ __('global.cancel')}}
        <span wire:loading wire:target="cancel">
            <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
        </span>
    </button>
</form>

