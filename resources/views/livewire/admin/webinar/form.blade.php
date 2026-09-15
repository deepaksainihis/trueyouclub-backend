
<h4 class="card-title">
    {{ $updateMode ? __('global.edit') : __('global.create') }}
    {{ __('cruds.webinar.title')}}</h4>

<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}" class="forms-sample">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.webinar.fields.title')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="title" placeholder="{{ __('cruds.webinar.fields.title')}}" autocomplete="off">
                @error('title') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.webinar.fields.start_date')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" id="start_date" class="form-control" wire:model.defer="start_date" placeholder="{{ __('cruds.webinar.fields.start_date')}}" autocomplete="off" readonly="true">
                @error('start_date') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.webinar.fields.start_time')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" id="start_time" class="form-control" wire:model.defer="start_time" placeholder="{{ __('cruds.webinar.fields.start_time')}}" autocomplete="off" readonly="true">
                @error('start_time') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.webinar.fields.end_time')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" id="end_time" class="form-control" wire:model.defer="end_time" placeholder="{{ __('cruds.webinar.fields.end_time')}}" autocomplete="off" readonly="true">
                @error('end_time') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

   {{-- <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.webinar.fields.end_date')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" id="end_date" class="form-control" wire:model.defer="end_date" placeholder="{{ __('cruds.webinar.fields.end_date')}}" autocomplete="off" readonly="true">
                @error('end_date') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.webinar.fields.end_time')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" id="end_time" class="form-control" wire:model.defer="end_time" placeholder="{{ __('cruds.webinar.fields.end_time')}}" autocomplete="off" readonly="true">
                @error('end_time') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>--}}


    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.webinar.fields.meeting_link')}}<i class="fas fa-asterisk"></i>
                </label>
                <input type="url" class="form-control" placeholder="{{ __('cruds.webinar.fields.meeting_link')}}"  wire:model.defer="meeting_link" />
            </div>
            @error('meeting_link') <span class="error text-danger">{{ $message }}</span>@enderror
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

    {{-- <div class="row">
        <div class="col-md-12">
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

    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary mr-2 joinBtn">
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

