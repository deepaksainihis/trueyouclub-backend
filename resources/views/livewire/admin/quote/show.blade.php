<div>
   
    <h4 class="card-title">@lang('global.view') @lang('cruds.quote.title_singular')</h4>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label font-weight-bold">@lang('cruds.quote.fields.message')</label>
            <div class="col-sm-9 col-form-label">
                 {{ ucwords($detail->message) }}
            </div>
        </div>
    
        <button wire:click.prevent="cancel" class="btn btn-secondary">
            {{ __('global.cancel')}}
            <span wire:loading wire:target="cancel">
                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
            </span>
        </button>
    
    </div>
    