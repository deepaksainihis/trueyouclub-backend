<div>

    <h4 class="card-title blog-title">@lang('global.view') @lang('cruds.volunteer.title_singular')</h4>
    <div class="contact-show-wrapper">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.volunteer.fields.full_name')</label>
        <div class="col-sm-9 col-form-label">
            {{ ucwords($detail->first_name) }} {{ ucwords($detail->last_name) }}
        </div>
    </div>


    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.volunteer.fields.phone')</label>
        <div class="col-sm-9 col-form-label">
            {{ $detail->phone }}
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.volunteer.fields.email')</label>
        <div class="col-sm-9 col-form-label">
            {{ $detail->email }}
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">@lang('cruds.volunteer.fields.location_id')</label>
        <div class="col-sm-9 col-form-label">
            {{ $detail->userLocation ? $detail->userLocation->name : '' }}
        </div>
    </div>


    {{-- <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">Want A Break ?</label>
        <div class="col-sm-9 col-form-label">

            @if($detail->is_active)
            <div class="badge badge-success">Break</div>
            @else
            <div class="badge badge-danger">Continue</div>
            @endif

        </div>
    </div> --}}

    {{-- <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">Rating</label>
        <div class="col-sm-9 col-form-label">

            <div class="rewardscrad card">
                <div class="star-rating">
                    <button type="button" class="{{ $detail->star_no >=1 ? 'on': 'off'}}"><span class="star">★</span></button>
                    <button type="button" class="{{ $detail->star_no >=2 ? 'on': 'off'}}"><span class="star">★</span></button>
                    <button type="button" class="{{ $detail->star_no >=3 ? 'on': 'off'}}"><span class="star">★</span></button>
                    <button type="button" class="{{ $detail->star_no >=4 ? 'on': 'off'}}"><span class="star">★</span></button>
                    <button type="button" class="{{ $detail->star_no ==5 ? 'on': 'off'}}"><span class="star">★</span></button>
                </div>
            </div>

        </div>
    </div> --}}

   

    <button wire:click.prevent="cancel" class="btn btn-secondary mt-4">
        {{ __('global.back')}}
        <span wire:loading wire:target="cancel">
            <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
        </span>
    </button>
    
    </div>

</div>