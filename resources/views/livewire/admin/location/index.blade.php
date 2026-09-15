<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($formMode)

                        @include('livewire.admin.location.form')

                    @elseif($viewMode)

                        @livewire('admin.location.show', ['location_id' => $location_id])                   

                    @else                        
                        <div wire:loading wire:target="create" class="loader"></div>
                        <div class="card-title top-box-set flex-wrap">
                            <h4 class="card-title-heading">@lang('cruds.location.list') </h4>
                            <div class="card-top-box-item flex-direction-none">
                                @can('location_create')
                                <button wire:click="create()" type="button" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                    <x-svg-icon icon="add" />
                                    {{__('global.add')}}
                                </button>
                                @endcan
                            </div>
                        </div>                 
                                             
                       

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
                                                    {{ __('cruds.location.fields.name')}}
                                                    <span wire:click.prevent="sortBy('name')" class="float-right text-sm" style="cursor: pointer;">
                                                        <i class="fa fa-arrow-up {{ $sortColumnName === 'name' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                                        <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'name' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                                    </span>
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
                                            @if($allLocations->count() > 0)
                                            @foreach($allLocations as $serialNo => $location)
                                            <tr>
                                                <td>{{ $serialNo+1 }}</td>
                                                <td>{{ ucwords($location->name) }}</td>

                                                <td>{{ convertDateTimeFormat($location->created_at,'fulldate') }}</td>                                                

                                                <td>
                                                    <div class="update-webinar table-btns">
                                                        <ul class="d-flex">
                                                            @can('location_show')
                                                            <li>
                                                                <a href="javascript:void()" wire:click.prevent="$emit('show', {{$location->id}})" title="View">
                                                                    <x-svg-icon icon="view" />
                                                                </a>
                                                            </li>
                                                            @endcan

                                                            @can('location_edit')
                                                            <li>                                                                
                                                                <a href="javascript:void()" wire:click.prevent="$emit('edit', {{$location->id}})" title="Edit">
                                                                    <x-svg-icon icon="edit" />
                                                                </a>
                                                            </li>
                                                            @endcan


                                                            @can('location_delete')
                                                            <li>
                                                                <a href="javascript:void()" wire:click.prevent="$emit('delete', {{$location->id}})" title="Delete">
                                                                    <x-svg-icon icon="delete" />
                                                                </a>
                                                            </li>
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
                                {{ $allLocations->links('vendor.pagination.custom-pagination') }}

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

</script>
@endpush