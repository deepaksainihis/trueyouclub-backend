<h4 class="card-title">
    {{ $updateMode ? __('global.edit') : __('global.create') }}
    {{ __('cruds.pages.title')}}
</h4>

<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}" class="forms-sample edit-page-wrap">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.pages.fields.page_name')}}<i class="fas fa-asterisk"></i></label>
                <input disabled type="text" class="form-control" wire:model.defer="page_name" placeholder="{{ __('cruds.pages.fields.page_name')}}" autocomplete="off">
                @error('page_name') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.pages.fields.title')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="title" placeholder="{{ __('cruds.pages.fields.title')}}" autocomplete="off">
                @error('title') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0" wire:ignore>
                <label class="font-weight-bold justify-content-start">{{ __('cruds.pages.fields.subtitle')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="subtitle" placeholder="{{ __('cruds.pages.fields.subtitle')}}" autocomplete="off">
            </div>
            @error('subtitle') <span class="error text-danger">{{ $message }}</span>@enderror
        </div>
    </div>
           

    <div class="row logo-section">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0" wire:ignore>
                <label class="font-weight-bold justify-content-start">{{ __('global.image')}}</label>
                <input type="file" id="dropify-image" wire:model.defer="image" class="dropify" data-default-file="{{ $originalImage }}" data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="jpeg png jpg svg" data-min-file-size-preview="1M" data-max-file-size-preview="3M" accept="image/jpeg, image/png, image/jpg,image/svg">
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

    <!-- add button for button title and link -->
    <div class="row mb-2">
        <div class="col-md-8"></div>
        <div class="col-md-4 text-right">
            <button type="button" wire:click="addMore" wire:loading.attr="disabled" class="btn btn-primary mr-2">
                {{ __('global.add_more') }} 
                <span wire:loading wire:target="addMore">
                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                </span>
            </button>
        </div>

    </div>

    @if($showAddMore)
    @foreach($button as $index=> $btn)
    <div class="row" wire:key="{{ $index }}">
        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.pages.fields.button')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="button.{{$index}}.title" placeholder="{{ __('cruds.pages.fields.button')}}" autocomplete="off">
                @error("button.$index.title") <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.pages.fields.link')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="button.{{ $index }}.link" placeholder="{{ __('cruds.pages.fields.link')}}" autocomplete="off">
                @error("button.$index.link") <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="col-md-2 text-right edit-draft">
            <button type="button" wire:click="remove({{ $index }})" wire:loading.attr="disabled" class="btn btn-danger">
                <svg width="18" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.3337 3.33333H13.167V1.66667C13.167 1.22464 12.9914 0.800716 12.6788 0.488155C12.3663 0.175595 11.9424 0 11.5003 0L6.50033 0C6.0583 0 5.63437 0.175595 5.32181 0.488155C5.00925 0.800716 4.83366 1.22464 4.83366 1.66667V3.33333H0.666992V5H2.33366V17.5C2.33366 18.163 2.59705 18.7989 3.06589 19.2678C3.53473 19.7366 4.17062 20 4.83366 20H13.167C13.83 20 14.4659 19.7366 14.9348 19.2678C15.4036 18.7989 15.667 18.163 15.667 17.5V5H17.3337V3.33333ZM6.50033 1.66667H11.5003V3.33333H6.50033V1.66667ZM14.0003 17.5C14.0003 17.721 13.9125 17.933 13.7562 18.0893C13.6 18.2455 13.388 18.3333 13.167 18.3333H4.83366C4.61265 18.3333 4.40068 18.2455 4.2444 18.0893C4.08812 17.933 4.00033 17.721 4.00033 17.5V5H14.0003V17.5Z" fill="#ffffff"></path>
                    <path d="M8.16667 8.3335H6.5V15.0002H8.16667V8.3335Z" fill="#ffffff"></path>
                    <path d="M11.5007 8.3335H9.83398V15.0002H11.5007V8.3335Z" fill="#ffffff"></path>
                </svg>
            </button>
        </div>


    </div>
    @endforeach
    @endif
    <!-- end add button for button title and link -->



    {{-- <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">{{__('global.status')}}</label>
    <div class="form-group">
        <label class="toggle-switch">
            <input type="checkbox" class="toggleSwitch" wire:change.prevent="changeStatus({{$status}})" value="{{ $status }}" {{ $status ==1 ? 'checked' : '' }}>
            <span class="switch-slider"></span>
        </label>
    </div>
    @error('state.status') <span class="error text-danger">{{ $message }}</span>@enderror
    </div>
    </div>
    </div> --}}

    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary mr-2">
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