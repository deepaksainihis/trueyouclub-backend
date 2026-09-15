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
                                {{ __('cruds.contacts.fields.full_name') }}
                                <span wire:click="sortBy('full_name')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortColumnName === 'full_name' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'full_name' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                </span>
                            </th>

                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.contacts.fields.phone_number')}}
                            </th>

                            <th class="text-gray-500 text-xs">@lang('global.created')
                                <span wire:click="sortBy('created_at')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortColumnName === 'created_at' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'created_at' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                </span>
                            </th>
                            <th class="text-gray-500 text-xs">@lang('global.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($allContacts->count() > 0)
                        @foreach($allContacts as $serialNo => $contact)
                        <tr>
                            <td>{{ $serialNo+1 }}</td>
                            <td>{{ ucwords($contact->full_name) }}</td>
                            <td>{{ ucwords($contact->phone_number) }}</td>

                            <td>{{ convertDateTimeFormat($contact->created_at,'fulldate') }}</td>
                            <td>

                                <div class="update-webinar table-btns">
                                    <ul class="d-flex">
                                        @can('contact_show')
                                        <li>
                                            <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$contact->id}})" title="View">
                                                <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.8507 7.3175C19.1191 5.7175 16.2499 0.5 9.99989 0.5C3.74989 0.5 0.880726 5.7175 0.149059 7.3175C0.0508413 7.53192 0 7.76499 0 8.00083C0 8.23668 0.0508413 8.46975 0.149059 8.68417C0.880726 10.2825 3.74989 15.5 9.99989 15.5C16.2499 15.5 19.1191 10.2825 19.8507 8.6825C19.9487 8.46832 19.9995 8.23554 19.9995 8C19.9995 7.76446 19.9487 7.53168 19.8507 7.3175ZM9.99989 13.8333C4.74406 13.8333 2.29156 9.36167 1.66656 8.00917C2.29156 6.63833 4.74406 2.16667 9.99989 2.16667C15.2432 2.16667 17.6966 6.61917 18.3332 8C17.6966 9.38083 15.2432 13.8333 9.99989 13.8333Z" fill="#626263"/>
                                                    <path d="M9.99968 3.8335C9.17559 3.8335 8.37001 4.07787 7.6848 4.53571C6.9996 4.99355 6.46554 5.64429 6.15018 6.40565C5.83481 7.16701 5.7523 8.00479 5.91307 8.81304C6.07384 9.62129 6.47068 10.3637 7.0534 10.9464C7.63612 11.5292 8.37855 11.926 9.1868 12.0868C9.99505 12.2475 10.8328 12.165 11.5942 11.8497C12.3555 11.5343 13.0063 11.0002 13.4641 10.315C13.922 9.62983 14.1663 8.82425 14.1663 8.00016C14.165 6.8955 13.7256 5.83646 12.9445 5.05535C12.1634 4.27423 11.1043 3.83482 9.99968 3.8335ZM9.99968 10.5002C9.50522 10.5002 9.02187 10.3535 8.61075 10.0788C8.19963 9.80413 7.8792 9.41369 7.68998 8.95687C7.50076 8.50006 7.45125 7.99739 7.54771 7.51244C7.64418 7.02748 7.88228 6.58203 8.23191 6.2324C8.58154 5.88276 9.027 5.64466 9.51195 5.5482C9.9969 5.45174 10.4996 5.50124 10.9564 5.69046C11.4132 5.87968 11.8036 6.20011 12.0784 6.61124C12.3531 7.02236 12.4997 7.50571 12.4997 8.00016C12.4997 8.6632 12.2363 9.29909 11.7674 9.76793C11.2986 10.2368 10.6627 10.5002 9.99968 10.5002Z" fill="#626263"/>
                                                </svg>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('contact_delete')
                                        <li>
                                            <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$contact->id}})" title="Delete">
                                                <svg width="18" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.3337 3.33333H13.167V1.66667C13.167 1.22464 12.9914 0.800716 12.6788 0.488155C12.3663 0.175595 11.9424 0 11.5003 0L6.50033 0C6.0583 0 5.63437 0.175595 5.32181 0.488155C5.00925 0.800716 4.83366 1.22464 4.83366 1.66667V3.33333H0.666992V5H2.33366V17.5C2.33366 18.163 2.59705 18.7989 3.06589 19.2678C3.53473 19.7366 4.17062 20 4.83366 20H13.167C13.83 20 14.4659 19.7366 14.9348 19.2678C15.4036 18.7989 15.667 18.163 15.667 17.5V5H17.3337V3.33333ZM6.50033 1.66667H11.5003V3.33333H6.50033V1.66667ZM14.0003 17.5C14.0003 17.721 13.9125 17.933 13.7562 18.0893C13.6 18.2455 13.388 18.3333 13.167 18.3333H4.83366C4.61265 18.3333 4.40068 18.2455 4.2444 18.0893C4.08812 17.933 4.00033 17.721 4.00033 17.5V5H14.0003V17.5Z" fill="#626263"/>
                                                    <path d="M8.16667 8.3335H6.5V15.0002H8.16667V8.3335Z" fill="#626263"/>
                                                    <path d="M11.5007 8.3335H9.83398V15.0002H11.5007V8.3335Z" fill="#626263"/>
                                                </svg>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>



                                {{-- <button type="button"  class="btn btn-info btn-rounded btn-icon">
                                    <i class="ti-pencil-alt"></i>
                                </button> --}}

                                {{-- @can('contact_show')
                                <button title="Show" type="button" wire:click.prevent="$emitUp('show', {{$contact->id}})" class="btn btn-info view-btn btn-rounded btn-icon">
                                <i class="ti-eye"></i>
                                </button>
                                @endcan

                                @can('contact_delete')
                                <button title="Delete" type="button" wire:click.prevent="$emitUp('delete', {{$contact->id}})" class="btn btn-danger btn-rounded btn-icon">
                                    <i class="ti-trash"></i>
                                </button>
                                @endcan --}}

                                {{-- @if($contact->is_draft == 1)
                                <button title="Reply" type="button" wire:click.prevent="$emitUp('reply', {{$contact->id}})" class="btn btn-success btn-rounded btn-icon">
                                <i class="fa fa-reply" aria-hidden="true"></i>
                                </button>
                                @endif --}}

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
            {{ $allContacts->links('vendor.pagination.custom-pagination') }}

        </div>
    </div>

    {{-- end Datatables --}}


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

    window.addEventListener('load', function () {
        Livewire.emit('pageReload');
        // console.log('destroy session');
    });

</script>
@endpush
