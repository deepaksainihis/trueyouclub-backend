<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">Today Quote </h4>
                        @can('quote_create')
                        @if(!$todayQuote)
                        <div class="card-top-box-item">
                            <button wire:click="create()" type="button" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.73956 14.3355C8.55177 14.5232 8.2948 14.6418 7.99829 14.6418C7.42503 14.6418 6.95062 14.1674 6.95062 13.5942L6.95062 2.40586C6.95062 1.8326 7.42503 1.35819 7.99828 1.35819C8.57154 1.35819 9.04595 1.8326 9.04595 2.40586L9.04595 13.5942C9.05584 13.8808 8.92735 14.1477 8.73956 14.3355Z" fill="#0A2540" />
                                    <path d="M14.3337 8.74129C14.1459 8.92908 13.889 9.04769 13.5924 9.04769L2.40412 9.04769C1.83087 9.04769 1.35645 8.57327 1.35645 8.00002C1.35645 7.42676 1.83087 6.95235 2.40412 6.95235L13.5924 6.95235C14.1657 6.95235 14.6401 7.42676 14.6401 8.00002C14.6401 8.29653 14.5215 8.5535 14.3337 8.74129Z" fill="#0A2540" />
                                </svg>
                                {{__('global.add')}}
                            </button>
                        </div>
                        @endif
                        @endcan
                    </div>

                    <div class="search-table-data">
                        <div class="single-quotes">
                            @if($todayQuote)
                            <ul>
                                <li>
                                    <div class="webinar-item">
                                        <p class="quotes-content">
                                            {!! nl2br(e($todayQuote->message)) !!}
                                        </p>
                                        @php
                                            $quotePercentage = 0;
                                            $totalUsers = getTotalUsers();
                                            $quotePercentage = $totalUsers ? ($todayQuote->users()->count() / (int)$totalUsers) * 100 : 0;                                 
                                        @endphp
                                        <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar" style="width: {{$quotePercentage}}%">{{ round($quotePercentage) }}%</div>
                                        </div>
                                        <span class="quotes-date">
                                            <svg width="14" height="15" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.66683 2.33337H2.25016C1.7439 2.33337 1.3335 2.74378 1.3335 3.25004V9.66671C1.3335 10.173 1.7439 10.5834 2.25016 10.5834H8.66683C9.17309 10.5834 9.5835 10.173 9.5835 9.66671V3.25004C9.5835 2.74378 9.17309 2.33337 8.66683 2.33337Z" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M7.2915 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M3.62549 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M1.3335 5.08337H9.5835" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                            {{ convertDateTimeFormat($todayQuote->created_at,'fulldate') }}
                                        </span>
                                        <div class="update-webinar">
                                            <ul class="d-flex">
                                                @can('quote_edit')
                                                @if(now()->isSameDay($todayQuote->created_at))
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emit('edit', {{$todayQuote->id}})" title="Edit">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12.5003 18.9583H7.50033C2.97533 18.9583 1.04199 17.025 1.04199 12.5V7.49996C1.04199 2.97496 2.97533 1.04163 7.50033 1.04163H9.16699C9.50866 1.04163 9.79199 1.32496 9.79199 1.66663C9.79199 2.00829 9.50866 2.29163 9.16699 2.29163H7.50033C3.65866 2.29163 2.29199 3.65829 2.29199 7.49996V12.5C2.29199 16.3416 3.65866 17.7083 7.50033 17.7083H12.5003C16.342 17.7083 17.7087 16.3416 17.7087 12.5V10.8333C17.7087 10.4916 17.992 10.2083 18.3337 10.2083C18.6753 10.2083 18.9587 10.4916 18.9587 10.8333V12.5C18.9587 17.025 17.0253 18.9583 12.5003 18.9583Z" fill="#40658B"></path>
                                                            <path d="M7.08311 14.7417C6.57478 14.7417 6.10811 14.5584 5.76645 14.225C5.35811 13.8167 5.18311 13.225 5.27478 12.6L5.63311 10.0917C5.69978 9.60838 6.01645 8.98338 6.35811 8.64172L12.9248 2.07505C14.5831 0.416716 16.2664 0.416716 17.9248 2.07505C18.8331 2.98338 19.2414 3.90838 19.1581 4.83338C19.0831 5.58338 18.6831 6.31672 17.9248 7.06672L11.3581 13.6334C11.0164 13.975 10.3914 14.2917 9.90811 14.3584L7.39978 14.7167C7.29145 14.7417 7.18311 14.7417 7.08311 14.7417ZM13.8081 2.95838L7.24145 9.52505C7.08311 9.68338 6.89978 10.05 6.86645 10.2667L6.50811 12.775C6.47478 13.0167 6.52478 13.2167 6.64978 13.3417C6.77478 13.4667 6.97478 13.5167 7.21645 13.4834L9.72478 13.125C9.94145 13.0917 10.3164 12.9084 10.4664 12.75L17.0331 6.18338C17.5748 5.64172 17.8581 5.15838 17.8998 4.70838C17.9498 4.16672 17.6664 3.59172 17.0331 2.95005C15.6998 1.61672 14.7831 1.99172 13.8081 2.95838Z" fill="#40658B"></path>
                                                            <path d="M16.5413 8.19173C16.483 8.19173 16.4246 8.1834 16.3746 8.16673C14.183 7.55006 12.4413 5.8084 11.8246 3.61673C11.733 3.2834 11.9246 2.94173 12.258 2.84173C12.5913 2.75006 12.933 2.94173 13.0246 3.27506C13.5246 5.05006 14.933 6.4584 16.708 6.9584C17.0413 7.05006 17.233 7.40006 17.1413 7.7334C17.0663 8.01673 16.8163 8.19173 16.5413 8.19173Z" fill="#40658B"></path>
                                                        </svg>
                                                    </a>
                                                </li>
                                                @endif
                                                @endcan

                                                @can('quote_delete')
                                                {{-- <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emit('delete', {{$todayQuote->id}})" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" id="Outline" fill="none" viewBox="0 0 24 24" width="20" height="20">
                                                            <path d="M21,4H17.9A5.009,5.009,0,0,0,13,0H11A5.009,5.009,0,0,0,6.1,4H3A1,1,0,0,0,3,6H4V19a5.006,5.006,0,0,0,5,5h6a5.006,5.006,0,0,0,5-5V6h1a1,1,0,0,0,0-2ZM11,2h2a3.006,3.006,0,0,1,2.829,2H8.171A3.006,3.006,0,0,1,11,2Zm7,17a3,3,0,0,1-3,3H9a3,3,0,0,1-3-3V6H18Z" fill="#40658B"></path>
                                                            <path d="M10,18a1,1,0,0,0,1-1V11a1,1,0,0,0-2,0v6A1,1,0,0,0,10,18Z" fill="#40658B"></path>
                                                            <path d="M14,18a1,1,0,0,0,1-1V11a1,1,0,0,0-2,0v6A1,1,0,0,0,14,18Z" fill="#40658B"></path>
                                                        </svg>
                                                    </a>
                                                </li> --}}
                                                @endcan

                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            @else
                            @include('admin.partials.no-record-found')
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            {{-- Start quote list --}}
            <div class="card">
                <div class="card-body">
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.quote.title') @lang('global.list') </h4>
                    </div>
                    <div class="search-table-data">
                        @livewire('admin.quote.quote-table')
                    </div>
                </div>
            </div>
            {{-- End quote list --}}

        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="quote_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $modalType }} @lang('cruds.quote.fields.message')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}" class="forms-sample">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{-- <label class="font-weight-bold justify-content-start">{{ __('cruds.quote.fields.message')}}<i class="fas fa-asterisk"></i></label> --}}
                                    <textarea rows="10" class="form-control" wire:model.defer="message" placeholder="{{ __('cruds.quote.fields.message')}}" autocomplete="off"></textarea>
                                    @error('message') <span class="error text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
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
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/assets/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

@endpush

@push('scripts')
<script type="text/javascript">
    document.addEventListener('loadPlugins', function(event) {
        $('#quote_modal').modal('hide');
    });

    document.addEventListener('openModal', function(event) {
        $('#quote_modal').modal('show');
    });
</script>
@endpush