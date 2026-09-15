<div>

    <h4 class="card-title blog-title">@lang('global.view') {{ ucwords($detail->page_name) }} @lang('cruds.pages.title_singular')</h4>

    <div class="show-wrapper mt-4">
        <div class="card">
            <div class="card-body-">
                <div class="inner-show-content">
                    <div class="show-d-img">
                        <img class="img-fuild" src="{{ $detail->image_url ? $detail->image_url : asset(config('constants.default.no_image')) }}" />
                    </div>
                    <h3>
                        {{ ucwords($detail->title) }}
                    </h3>
                    <p class="mt-4">
                        {{ ucwords($detail->subtitle) }}
                    </p>

                    @if(json_decode($detail->button, true))
                    <div class="row pageHome-show">
                        @foreach(json_decode($detail->button, true) as $button)
                        <div class="show-btn-box text-center mr-1">
                            <a href="{{ $button ? $button['link'] : '' }}" target="_blank" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                {{ $button['title'] }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif


                    <div class="show-btn-box text-center">
                        <button wire:click.prevent="cancel" class="btn btn-secondary">
                            {{ __('global.back')}}
                            <span wire:loading wire:target="cancel">
                                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>