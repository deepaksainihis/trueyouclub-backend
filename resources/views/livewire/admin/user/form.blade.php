<h4 class="card-title">
    {{ $updateMode ? __('global.edit') : __('global.create') }}
    {{ __('cruds.user.title')}}
</h4>

<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}" class="forms-sample">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.user.fields.first_name')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="first_name" placeholder="{{ __('cruds.user.fields.first_name')}}" autocomplete="off">
                @error('first_name') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.user.fields.last_name')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="last_name" placeholder="{{ __('cruds.user.fields.last_name')}}" autocomplete="off">
                @error('last_name') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.user.fields.email')}}<i class="fas fa-asterisk"></i></label>
                <input disabled type="email" class="form-control" wire:model.defer="email" placeholder="{{ __('cruds.user.fields.email')}}" autocomplete="off">
                @error('email') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.user.fields.phone')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="phone" placeholder="{{ __('cruds.user.fields.phone')}}" autocomplete="off">
                @error('phone') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    {{-- <div class="row logo-section">
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
    </div> --}}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold">{{__('global.status')}}</label>
                <div class="form-group">
                    <label class="toggle-switch">
                        <input type="checkbox" class="toggleSwitch" wire:change.prevent="changeStatus({{$is_active}})" value="{{ $is_active }}" {{ $is_active ==1 ? 'checked' : '' }}>
                        <span class="switch-slider"></span>
                    </label>
                </div>
                @error('state.is_active') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

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