<div>

    <h4 class="card-title blog-title">@lang('global.view') @lang('cruds.contacts.title_singular')</h4>
    <div class="contact-show-wrapper">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.contacts.fields.full_name')</label>
            <div class="col-sm-9 col-form-label">
                {{ ucwords($detail->first_name) }} {{ ucwords($detail->last_name) }}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.contacts.fields.phone_number')</label>
            <div class="col-sm-9 col-form-label">
                {{ $detail->phone_number }}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.contacts.fields.email')</label>
            <div class="col-sm-9 col-form-label">
                {{ $detail->email }}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.contacts.fields.message')</label>
            <div class="col-sm-9 col-form-label">
                {!! nl2br(e($detail->message)) !!}
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