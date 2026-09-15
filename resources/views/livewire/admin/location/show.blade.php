<div>

    <h4 class="card-title blog-title">@lang('global.view') @lang('cruds.location.title_singular')</h4>
    <div class="contact-show-wrapper">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.location.fields.name')</label>
        <div class="col-sm-9 col-form-label">
            {{ ucwords($detail->name) }}
        </div>
    </div>
   

    <button wire:click.prevent="cancel" class="btn btn-secondary mt-4">
        {{ __('global.back')}}
        <span wire:loading wire:target="cancel">
            <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
        </span>
    </button>
    
    </div>

</div>