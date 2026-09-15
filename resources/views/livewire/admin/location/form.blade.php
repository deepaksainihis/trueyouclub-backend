<h4 class="card-title">
    {{ $updateMode ? __('global.edit') : __('global.create') }}
    {{ __('cruds.location.title_singular')}}
</h4>

<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}" class="forms-sample">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-weight-bold justify-content-start">{{ __('cruds.location.fields.name')}}<i class="fas fa-asterisk"></i></label>
                <input type="text" class="form-control" wire:model.defer="name" placeholder="{{ __('cruds.location.fields.name')}}" autocomplete="off">
                @error('name') <span class="error text-danger">{{ $message }}</span>@enderror
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