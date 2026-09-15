<div>
     {{-- Start Filter Form --}}
     <div class="card">
        <div class="card-body filter-section">
            <form wire:submit.prevent="submitFilterForm" class="forms-sample">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="filter_date_range">Select Date</label>
                            <input type="text" id="filter_date_range" class="form-control" wire:model.defer="filter_date_range" placeholder="Select Date" autocomplete="off" readonly="true">
                        </div>
                        @error('filter_date_range') <span class="error text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6 mt-2 mt-md-0 pl-3 pl-md-1">
                        <button type="submit" class="btn joinBtn" wire:loading.attr="disabled">
                            @lang('global.submit')
                            <span wire:loading wire:target="submitFilterForm">
                                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                        </button>
                        <button type="button" wire:click.prevent="restFilterForm" wire:loading.attr="disabled" class="btn btn-secondary ml-2 ml-md-3">
                            @lang('global.reset')
                            <span wire:loading wire:target="restFilterForm">
                                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                        </button>
                        
                        {{-- Start Custom Export Buttons --}}
                        <div wire:loading wire:target="exportToExcel" class="loader"></div>
                        <div class="export-buttons">
                            <button type="button" wire:click.prevent="exportToExcel" class="btn btn-success" title="Export Excel">
                                <x-svg-icon icon="excel" />
                            </button>
                        </div>
                        {{-- End Custom Export Buttons --}}

                    </div>
                </div>

            </form>
        </div>
    </div>
    {{-- End Filter Form --}}

    {{-- Start Datatables --}}
    <div class="table-responsive search-table-data">

        <div class="relative">

            @include('admin.partials.table-show-entries-search-box')


            <div class="table-responsive mt-3 my-team-details table-record">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-gray-500 text-xs font-medium">{{ trans('global.sno') }}</th>
                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.user.fields.name')}}
                                <span wire:click="sortBy('name')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortColumnName === 'name' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'name' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                </span>
                            </th>

                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.user.fields.rating')}}
                                <span wire:click="sortBy('star_no')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortColumnName === 'star_no' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'star_no' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                </span>
                            </th>

                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.user.fields.phone')}}
                            </th>

                            <th class="text-gray-500 text-xs">@lang('global.created')
                                <span wire:click="sortBy('created_at')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortColumnName === 'created_at' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'created_at' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                </span>
                            </th>

                            <th class="text-gray-500 text-xs">
                                Want A Break ?
                            </th>



                            <th class="text-gray-500 text-xs">@lang('global.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($allUsers->count() > 0)
                        @foreach($allUsers as $serialNo => $user)
                        <tr>
                            <td>{{ $serialNo+1 }}</td>
                            <td>{{ ucwords($user->name) }}</td>
                            <td>
                                <div class="rewardscrad card">
                                    <div class="star-rating">
                                        <button type="button" class="{{ $user->star_no >=1 ? 'on': 'off'}}"><span class="star">★</span></button>
                                        <button type="button" class="{{ $user->star_no >=2 ? 'on': 'off'}}"><span class="star">★</span></button>
                                        <button type="button" class="{{ $user->star_no >=3 ? 'on': 'off'}}"><span class="star">★</span></button>
                                        <button type="button" class="{{ $user->star_no >=4 ? 'on': 'off'}}"><span class="star">★</span></button>
                                        <button type="button" class="{{ $user->star_no ==5 ? 'on': 'off'}}"><span class="star">★</span></button>
                                    </div>
                                </div>
                            </td>
                            <td>{{ ucwords($user->phone) }}</td>

                            <td>{{ convertDateTimeFormat($user->created_at,'fulldate') }}</td>

                            <td>
                                <div class="toggleSwitch-wrap">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="customSwitch{{$user->id}}" wire:click.prevent="$emitUp('toggle',{{$user->id}})" {{ $user->is_active == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch{{$user->id}}">Break</label>
                                    </div>
                                </div>
                                {{-- <label class="toggle-switch">
                                    <input type="checkbox" class="toggleSwitch" wire:click.prevent="$emitUp('toggle',{{$user->id}})" {{ $user->is_active == 1 ? 'checked' : '' }}>
                                    <div class="switch-slider-other round"></div>
                                </label> --}}
                            </td>

                            <td>
                                <div class="update-webinar table-btns">
                                    <ul class="d-flex">
                                        @can('user_show')
                                        <li>
                                            <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$user->id}})" title="View">
                                                <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.8507 7.3175C19.1191 5.7175 16.2499 0.5 9.99989 0.5C3.74989 0.5 0.880726 5.7175 0.149059 7.3175C0.0508413 7.53192 0 7.76499 0 8.00083C0 8.23668 0.0508413 8.46975 0.149059 8.68417C0.880726 10.2825 3.74989 15.5 9.99989 15.5C16.2499 15.5 19.1191 10.2825 19.8507 8.6825C19.9487 8.46832 19.9995 8.23554 19.9995 8C19.9995 7.76446 19.9487 7.53168 19.8507 7.3175ZM9.99989 13.8333C4.74406 13.8333 2.29156 9.36167 1.66656 8.00917C2.29156 6.63833 4.74406 2.16667 9.99989 2.16667C15.2432 2.16667 17.6966 6.61917 18.3332 8C17.6966 9.38083 15.2432 13.8333 9.99989 13.8333Z" fill="#626263"/>
                                                    <path d="M9.99968 3.8335C9.17559 3.8335 8.37001 4.07787 7.6848 4.53571C6.9996 4.99355 6.46554 5.64429 6.15018 6.40565C5.83481 7.16701 5.7523 8.00479 5.91307 8.81304C6.07384 9.62129 6.47068 10.3637 7.0534 10.9464C7.63612 11.5292 8.37855 11.926 9.1868 12.0868C9.99505 12.2475 10.8328 12.165 11.5942 11.8497C12.3555 11.5343 13.0063 11.0002 13.4641 10.315C13.922 9.62983 14.1663 8.82425 14.1663 8.00016C14.165 6.8955 13.7256 5.83646 12.9445 5.05535C12.1634 4.27423 11.1043 3.83482 9.99968 3.8335ZM9.99968 10.5002C9.50522 10.5002 9.02187 10.3535 8.61075 10.0788C8.19963 9.80413 7.8792 9.41369 7.68998 8.95687C7.50076 8.50006 7.45125 7.99739 7.54771 7.51244C7.64418 7.02748 7.88228 6.58203 8.23191 6.2324C8.58154 5.88276 9.027 5.64466 9.51195 5.5482C9.9969 5.45174 10.4996 5.50124 10.9564 5.69046C11.4132 5.87968 11.8036 6.20011 12.0784 6.61124C12.3531 7.02236 12.4997 7.50571 12.4997 8.00016C12.4997 8.6632 12.2363 9.29909 11.7674 9.76793C11.2986 10.2368 10.6627 10.5002 9.99968 10.5002Z" fill="#626263"/>
                                                </svg>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('user_edit')
                                        {{-- <li>
                                            <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$user->id}})" title="Edit">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.5003 18.9583H7.50033C2.97533 18.9583 1.04199 17.025 1.04199 12.5V7.49996C1.04199 2.97496 2.97533 1.04163 7.50033 1.04163H9.16699C9.50866 1.04163 9.79199 1.32496 9.79199 1.66663C9.79199 2.00829 9.50866 2.29163 9.16699 2.29163H7.50033C3.65866 2.29163 2.29199 3.65829 2.29199 7.49996V12.5C2.29199 16.3416 3.65866 17.7083 7.50033 17.7083H12.5003C16.342 17.7083 17.7087 16.3416 17.7087 12.5V10.8333C17.7087 10.4916 17.992 10.2083 18.3337 10.2083C18.6753 10.2083 18.9587 10.4916 18.9587 10.8333V12.5C18.9587 17.025 17.0253 18.9583 12.5003 18.9583Z" fill="#40658B"></path>
                                                    <path d="M7.08311 14.7417C6.57478 14.7417 6.10811 14.5584 5.76645 14.225C5.35811 13.8167 5.18311 13.225 5.27478 12.6L5.63311 10.0917C5.69978 9.60838 6.01645 8.98338 6.35811 8.64172L12.9248 2.07505C14.5831 0.416716 16.2664 0.416716 17.9248 2.07505C18.8331 2.98338 19.2414 3.90838 19.1581 4.83338C19.0831 5.58338 18.6831 6.31672 17.9248 7.06672L11.3581 13.6334C11.0164 13.975 10.3914 14.2917 9.90811 14.3584L7.39978 14.7167C7.29145 14.7417 7.18311 14.7417 7.08311 14.7417ZM13.8081 2.95838L7.24145 9.52505C7.08311 9.68338 6.89978 10.05 6.86645 10.2667L6.50811 12.775C6.47478 13.0167 6.52478 13.2167 6.64978 13.3417C6.77478 13.4667 6.97478 13.5167 7.21645 13.4834L9.72478 13.125C9.94145 13.0917 10.3164 12.9084 10.4664 12.75L17.0331 6.18338C17.5748 5.64172 17.8581 5.15838 17.8998 4.70838C17.9498 4.16672 17.6664 3.59172 17.0331 2.95005C15.6998 1.61672 14.7831 1.99172 13.8081 2.95838Z" fill="#40658B"></path>
                                                    <path d="M16.5413 8.19173C16.483 8.19173 16.4246 8.1834 16.3746 8.16673C14.183 7.55006 12.4413 5.8084 11.8246 3.61673C11.733 3.2834 11.9246 2.94173 12.258 2.84173C12.5913 2.75006 12.933 2.94173 13.0246 3.27506C13.5246 5.05006 14.933 6.4584 16.708 6.9584C17.0413 7.05006 17.233 7.40006 17.1413 7.7334C17.0663 8.01673 16.8163 8.19173 16.5413 8.19173Z" fill="#40658B"></path>
                                                </svg>
                                            </a>
                                        </li> --}}
                                        @endcan


                                        @can('user_delete')
                                        <li>
                                            <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$user->id}})" title="Delete">
                                                <svg width="18" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.3337 3.33333H13.167V1.66667C13.167 1.22464 12.9914 0.800716 12.6788 0.488155C12.3663 0.175595 11.9424 0 11.5003 0L6.50033 0C6.0583 0 5.63437 0.175595 5.32181 0.488155C5.00925 0.800716 4.83366 1.22464 4.83366 1.66667V3.33333H0.666992V5H2.33366V17.5C2.33366 18.163 2.59705 18.7989 3.06589 19.2678C3.53473 19.7366 4.17062 20 4.83366 20H13.167C13.83 20 14.4659 19.7366 14.9348 19.2678C15.4036 18.7989 15.667 18.163 15.667 17.5V5H17.3337V3.33333ZM6.50033 1.66667H11.5003V3.33333H6.50033V1.66667ZM14.0003 17.5C14.0003 17.721 13.9125 17.933 13.7562 18.0893C13.6 18.2455 13.388 18.3333 13.167 18.3333H4.83366C4.61265 18.3333 4.40068 18.2455 4.2444 18.0893C4.08812 17.933 4.00033 17.721 4.00033 17.5V5H14.0003V17.5Z" fill="#626263"/>
                                                    <path d="M8.16667 8.3335H6.5V15.0002H8.16667V8.3335Z" fill="#626263"/>
                                                    <path d="M11.5007 8.3335H9.83398V15.0002H11.5007V8.3335Z" fill="#626263"/>
                                                </svg>
                                            </a>
                                        </li>
                                        @endcan
                                        
                                        <li>
                                            <a href="javascript:void()" wire:click.prevent="$emitUp('viewQuoteHistory', {{$user->id}})" title="Quote History">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_2203_42)">
                                                    <path d="M10 0C7.54038 0.00433296 5.16799 0.911771 3.33333 2.55V0H1.66667V4.28583C1.66711 4.69612 1.83029 5.08948 2.12041 5.37959C2.41052 5.66971 2.80388 5.83289 3.21417 5.83333H7.5V4.16667H4.05C5.40234 2.78756 7.19176 1.92106 9.11228 1.7153C11.0328 1.50954 12.9652 1.97731 14.579 3.03864C16.1928 4.09996 17.3878 5.68892 17.9597 7.53383C18.5316 9.37874 18.4448 11.365 17.7143 13.1531C16.9838 14.9411 15.6548 16.4198 13.9546 17.3364C12.2544 18.253 10.2887 18.5506 8.39338 18.1782C6.4981 17.8058 4.79103 16.7866 3.56405 15.2949C2.33707 13.8032 1.66639 11.9315 1.66667 10H0C0 11.9778 0.58649 13.9112 1.6853 15.5557C2.78412 17.2002 4.3459 18.4819 6.17317 19.2388C8.00043 19.9957 10.0111 20.1937 11.9509 19.8079C13.8907 19.422 15.6725 18.4696 17.0711 17.0711C18.4696 15.6725 19.422 13.8907 19.8079 11.9509C20.1937 10.0111 19.9957 8.00043 19.2388 6.17317C18.4819 4.3459 17.2002 2.78412 15.5557 1.6853C13.9112 0.58649 11.9778 0 10 0Z" fill="#626263"/>
                                                    <path d="M9.16663 5.83301V10.3447L11.9108 13.0888L13.0891 11.9105L10.8333 9.65467V5.83301H9.16663Z" fill="#626263"/>
                                                    </g>
                                                    <defs>
                                                    <clipPath id="clip0_2203_42">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                    </defs>
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                {{--
                                @can('user_show')
                                <button title="Show" type="button" wire:click.prevent="$emitUp('show', {{$user->id}})" class="btn btn-info view-btn btn-rounded btn-icon">
                                <i class="ti-eye"></i>
                                </button>
                                @endcan

                                @can('user_edit')
                                <button title="Edit" type="button" wire:click.prevent="$emitUp('edit', {{$user->id}})" class="btn btn-info btn-rounded btn-icon">
                                    <i class="ti-pencil"></i>
                                </button>
                                @endcan

                                @can('user_delete')
                                <button title="Delete" type="button" wire:click.prevent="$emitUp('delete', {{$user->id}})" class="btn btn-danger btn-rounded btn-icon">
                                    <i class="ti-trash"></i>
                                </button>
                                @endcan
                                --}}
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td class="text-center" colspan="7">{{ __('messages.no_record_found')}}</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
            {{ $allUsers->links('vendor.pagination.custom-pagination') }}

        </div>

    </div>
    {{-- End Datatables --}}

</div>
@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script type="text/javascript">
    
    filterDateRangePicker();

    document.addEventListener('loadPlugins', function(event) {
        filterDateRangePicker();
    });

    $(document).on('click','.ranges ul>li',function(event){

        if($(this).attr('data-range-key') == 'Today'){
            // console.log(moment().format('DD MMMM YYYY') + ' - ' + moment().format('DD MMMM YYYY'));

            $('#filter_date_range').val(moment().format('DD MMMM YYYY') + ' - ' + moment().format('DD MMMM YYYY'));

            @this.set('filter_date_range',moment().format('DD MMMM YYYY') + ' - ' + moment().format('DD MMMM YYYY'))
        }
        
    });

    function filterDateRangePicker(){

        // var start = moment().subtract(29, 'days');
        var start = moment();
        var end = moment();

        function cb(start, end) {
            // console.log(start.format('DD MMMM YYYY') + ' - ' + end.format('DD MMMM YYYY'));
            $('#filter_date_range').val(start.format('DD MMMM YYYY') + ' - ' + end.format('DD MMMM YYYY'));

            @this.set('filter_date_range',start.format('DD MMMM YYYY') + ' - ' + end.format('DD MMMM YYYY'))
        }

        $('#filter_date_range').daterangepicker({
            autoUpdateInput: false,
            startDate: start,
            endDate: end,
            locale: {
                format: 'DD MMMM YYYY',
            },
            ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

    }
</script>
@endpush
