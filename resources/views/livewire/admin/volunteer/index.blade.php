<div class="content-wrapper">
    @include('livewire.admin.volunteer.invite-modal')

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($formMode)

                        @include('livewire.admin.volunteer.form')

                    @elseif($viewMode)

                        @livewire('admin.volunteer.show', ['user_id' => $user_id])                   

                    @else                        
                        <div wire:loading wire:target="create" class="loader"></div>
                        <div class="card-title top-box-set flex-wrap">
                            <h4 class="card-title-heading">@lang('cruds.volunteer.list') </h4>
                            <div class="card-top-box-item flex-direction-none">
                                @can('volunteer_create')
                                <button wire:click="create()" type="button" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                    <x-svg-icon icon="add" />
                                    {{__('global.add')}}
                                </button>
                                @endcan

                                @can('event_invite_volunteer_access')
                                    @if($filter_location_id)
                                        <button type="button" class="InviteBtn btn joinBtn btn-header" wire:click="triggerMassInviteModal(volunteer_selectedIds)"><x-svg-icon icon="invite-icon" /> Invite</button>
                                    @endif
                                @endcan
                            </div>
                        </div>                 
                                             
                        {{-- Start Filter Form --}}
                        <div class="card">
                            <div class="card-body filter-section">
                                <form wire:submit.prevent="submitFilterForm" class="forms-sample">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-0">
                                                <label for="filter_date_range">Select Date</label>
                                                <input type="text" id="filter_date_range" class="form-control" wire:model.defer="filter_date_range" placeholder="Select Date" autocomplete="off" readonly="true">
                                            </div>
                                            @error('filter_date_range') <span class="error text-danger">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="filter_location_id">Select Location</label>
                                                <select class="form-control" id="filter_location_id" wire:model.defer="filter_location_id">
                                                    <option value="" selected >Select Location</option>
                                                    @foreach($locations as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                                @error('filter_location_id') <span class="error text-danger">{{ $message }}</span> @enderror
                                            </div>
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
                                                <th><label class="custom-checkbox"><input type="checkbox" id="dt_cb_all" ><span></span></label></th>
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

                                                <th class="text-gray-500 text-xs">
                                                    {{ __('cruds.volunteer.fields.location_id')}}
                                                </th>

                                                <th class="text-gray-500 text-xs">@lang('global.created')
                                                    <span wire:click.prevent="sortBy('created_at')" class="float-right text-sm" style="cursor: pointer;">
                                                        <i class="fa fa-arrow-up {{ $sortColumnName === 'created_at' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                                        <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'created_at' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                                    </span>
                                                </th>                                                

                                                <th class="text-gray-500 text-xs">@lang('global.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($allUsers->count() > 0)
                                            @foreach($allUsers as $serialNo => $user)
                                            <tr>
                                                <td><input type="checkbox" class="dt_checkbox" name="volunteer_ids[]" value="{{ $user->id }}"></td>
                                                <td>{{ $serialNo+1 }}</td>
                                                <td>{{ ucwords($user->name) }}</td>
                                               
                                                <td>{{ ucwords($user->phone) }}</td>

                                                <td>{{ ucwords($user->userLocation ? $user->userLocation->name : '') }}</td>

                                                <td>{{ convertDateTimeFormat($user->created_at,'fulldate') }}</td>                                                

                                                <td>
                                                    <div class="update-webinar table-btns volunteer_list_table_action">
                                                        <ul class="d-flex">
                                                            @can('volunteer_show')
                                                            <li>
                                                                <a href="javascript:void()" wire:click.prevent="$emit('show', {{$user->id}})" title="View">
                                                                    <x-svg-icon icon="view" />
                                                                </a>
                                                            </li>
                                                            @endcan

                                                            @can('volunteer_edit')
                                                            <li>                                                                
                                                                <a href="javascript:void()" wire:click.prevent="$emit('edit', {{$user->id}})" title="Edit">
                                                                    <x-svg-icon icon="edit" />
                                                                </a>
                                                            </li>
                                                            @endcan


                                                            @can('volunteer_delete')
                                                            <li>
                                                                <a href="javascript:void()" wire:click.prevent="$emit('delete', {{$user->id}})" title="Delete">
                                                                    <x-svg-icon icon="delete" />
                                                                </a>
                                                            </li>
                                                            @endcan   
                                                            
                                                            @can('event_invite_volunteer_access')
                                                            @if($user->location_id)
                                                                <li>
                                                                    <a role="button" class="" wire:click="triggerInviteModal({{ $user->id }})" data-toggle="modal" data-target="#InviteModal" title="Invite"><x-svg-icon icon="invite-icon" /></a>                                                                
                                                                </li>
                                                            @endif
                                                            @endcan
                                                            
                                                        </ul>
                                                    </div>                                                   
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

                    @endif

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
    var volunteer_selectedIds = [];
    filterDateRangePicker();

    document.addEventListener('loadPlugins', function(event) {
        filterDateRangePicker();          
    });    

    function filterDateRangePicker(){
       
        $('#filter_date_range').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoApply: true, 
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10),
            locale: {
            format: 'DD MMMM YYYY' // Format the date to match what you want to store
        }
        }, function(start, end, label) {
            @this.set('filter_date_range',start.format('DD MMMM YYYY'));
        });
    }

    document.addEventListener('resetDatePicker', function(event) {        
        @this.set('filter_date_range', null);
    });
 

    $(document).on('click', '.toggle-password', function() {
        // Toggle the "eye-open" class
        $(this).toggleClass("eye-open");
        // Find the associated input field
        var input = $(this).siblings('input');
        // Toggle the input type between 'password' and 'text'
        input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password');
    });

    // Invite Mass Volunteer Functionality

    $(document).on('change', '#dt_cb_all', function(e)
    {
        e.preventDefault();
        var isChecked = $(this).prop('checked');
        $('.dt_checkbox').prop('checked', isChecked).trigger('change');
    });

    document.addEventListener('openInviteModal', function(event) {        
        $('#InviteModal').modal('show');
    });

    document.addEventListener('closeInviteModal', function(event) {        
        $('#InviteModal').modal('hide');
    });

    $(document).on('change', '.dt_checkbox', function(e)
    {
        e.preventDefault();       
        $('.dt_checkbox:checked').each(function() {
            volunteer_selectedIds.push($(this).val());
        });

        // When uncheck customer remove id from the selected_ids array
        if (!$(this).is(':checked')) {
            var valueToRemove = $(this).val();
            var indexToRemove = volunteer_selectedIds.indexOf(valueToRemove);
            if (indexToRemove !== -1) {
                volunteer_selectedIds.splice(indexToRemove, 1);
            }
        }

        volunteer_selectedIds = Array.from(new Set(volunteer_selectedIds));  
    });
 
    
    
</script>
@endpush