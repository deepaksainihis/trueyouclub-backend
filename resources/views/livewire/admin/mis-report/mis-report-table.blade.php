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
                            <button type="button" wire:click.prevent="exportToExcel('all','All Quote MIS Report')" class="btn btn-success" title="Export All">
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

                            {{-- <th class="text-gray-500 text-xs">
                                {{ __('cruds.mis_reports.fields.quote') }}
                                <span wire:click="sortBy('quote')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortColumnName === 'quote' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'quote' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                </span>
                            </th> --}}

                            <th class="text-gray-500 text-xs">@lang('cruds.mis_reports.fields.date')
                                <span wire:click="sortBy('created_at')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortColumnName === 'created_at' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'created_at' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                </span>
                            </th>

                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.mis_reports.fields.total_completed')}}
                            </th>

                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.mis_reports.fields.total_skipped')}}
                            </th>

                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.mis_reports.fields.total_leave')}}
                            </th>

                            <th class="text-gray-500 text-xs">
                                {{ __('cruds.mis_reports.fields.total_user')}}
                            </th>

                          
                            <th class="text-gray-500 text-xs">@lang('global.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @if($allReports->count() > 0)
                            @foreach($allReports as $serialNo => $report)

                            @php
                                $userIds = \DB::table('quote_user')->where('quote_id',$report->id)->pluck('user_id')->toArray();
                                $total_leave_users = \App\Models\User::whereNotIn('id',$userIds)->whereHas('roles', function ($query) {
                                        $query->where('id', config('constants.role.user'));
                                    })->count();
                                $total_users = \App\Models\User::whereHas('roles', function ($query) {
                                        $query->where('id', config('constants.role.user'));
                                    })->count();

                                $total_user_completed = \DB::table('quote_user')->where('quote_id',$report->id)->where('status','completed')->count();
                                $total_user_skipped = \DB::table('quote_user')->where('quote_id',$report->id)->where('status','skipped')->count();

                            @endphp

                            <tr>

                                <td>{{ $serialNo+1 }}</td>
                                {{-- <td>{!! nl2br($report->message) !!}</td> --}}
                                <td>{{ convertDateTimeFormat($report->created_at,'fulldate') }}</td>

                                <td>{{ $total_user_completed }}</td>
                                <td>{{ $total_user_skipped }}</td>
                                <td>{{ $total_leave_users }}</td>
                                <td>{{ $total_users }}</td>
                                
                                <td>
                                    <div class="update-webinar table-btns mis-report-btn">
                                        <ul class="d-flex">
                                        
                                            <li>
                                                <a href="javascript:void()" wire:click.prevent="exportToExcel('single','Quote {{ convertDateTimeFormat($report->created_at,'fulldate') }}',{{$report->id}})" title="Export">
                                                    <x-svg-icon icon="excel" />
                                                </a>
                                            </li>
                                        
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="8">{{ __('messages.no_record_found')}}</td>
                            </tr>
                        @endif
                        
                    </tbody>
                </table>
            </div>
            
            {{ $allReports->links('vendor.pagination.custom-pagination') }}
            
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
</script>
@endpush
