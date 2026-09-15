<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.event.event_attendance') </h4>    
                        <button wire:click.prevent="cancel" class="btn btn-secondary mt-4">
                            {{ __('global.back')}}
                            <span wire:loading wire:target="cancel">
                                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                        </button>                        
                    </div>                 
                      
                        {{-- Start Datatables --}}
                        <div class="table-responsive search-table-data">
                            <div class="relative">
                                @include('admin.partials.table-show-entries-search-box',['searchBoxPlaceholder'=>$searchBoxPlaceholder])

                                <div class="table-responsive mt-3 my-team-details table-record">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-gray-500 text-xs font-medium">{{ trans('global.sno') }}</th>
                                                <th class="text-gray-500 text-xs">
                                                    {{ __('cruds.volunteer.fields.name')}}
                                                    <span wire:click.prevent="sortBy('name')" class="float-right text-sm" style="cursor: pointer;">
                                                        <i class="fa fa-arrow-up {{ $sortColumnName === 'name' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                                        <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'name' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                                    </span>
                                                </th>                                               

                                                <th class="text-gray-500 text-xs">
                                                    {{ __('cruds.volunteer.fields.phone')}}
                                                </th>
                                               
                                                <th class="text-gray-500 text-xs">@lang('cruds.event.fields.status')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($allEventRequest->count() > 0)
                                            @foreach($allEventRequest as $serialNo => $eventrequest)
                                            <tr>
                                                <td>{{ $serialNo+1 }}</td>
                                                <td>{{ ucwords($eventrequest->volunteer->name) }}</td>                                                
                                                <td>{{ ucwords($eventrequest->volunteer->phone) }}</td>
                                                
                                                <td>
                                                    @can('event_request_edit')
                                                    <div class="eventAttendanceStatus">                                                       
                                                        <button class="btn 
                                                            @if($eventrequest->status == config('constants.event_invite_status.accepted'))
                                                                btn-success
                                                            @elseif($eventrequest->status == config('constants.event_invite_status.pending'))
                                                                btn-warning
                                                            @else
                                                                btn-danger
                                                            @endif">
                                                            @if($eventrequest->status == config('constants.event_invite_status.accepted'))
                                                                {{ __('Accepted') }}
                                                            @elseif($eventrequest->status == config('constants.event_invite_status.pending'))
                                                                {{ __('Pending') }}
                                                            @else
                                                                {{ __('Declined') }}
                                                            @endif
                                                        </button>
                                                    </div>                                                    
                                                    @endcan                                                   
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
                                {{ $allEventRequest->links('vendor.pagination.custom-pagination') }}

                            </div>

                        </div>
                        {{-- End Datatables --}}
                                       
                </div>
            </div>
        </div>
    </div>

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