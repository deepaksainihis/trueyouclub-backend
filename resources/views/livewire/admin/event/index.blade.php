<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($formMode)

                        @include('livewire.admin.event.form')

                    @elseif($viewMode)

                        @livewire('admin.event.show', ['event_id' => $event_id])

                    @elseif($attendanceViewMode)

                        @livewire('admin.event.show-attendance', ['event_id' => $event_id])

                    @else
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.event.title') @lang('global.list') </h4>
                        <div class="card-top-box-item">
                            @can('event_create')
                            <button wire:click="create()" type="button" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                <x-svg-icon icon="add" />
                                {{__('global.add')}}
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="search-table-data">                      

                       <div class="relative">

                            @include('admin.partials.table-show-entries-search-box',['searchBoxPlaceholder'=>$searchBoxPlaceholder])
                            
                            <div class="webinar_listing">
                                <div class="row">
                                @if($allEvent->count() > 0)
                                @foreach($allEvent as $serialNo => $event)
                                <div class="col-12 col-md-6">
                                    <div class="webinar-item">
                                        <div class="webinar-item-inner">
                                            <div class="webinar-img">
                                                <img class="img-fluid" src="{{ $event->featured_image_url ? $event->featured_image_url : asset(config('constants.default.no_image')) }}" alt="">
                                            </div>
                                            <div class="webinar-content">
                                                <h3>
                                                    {{ ucwords($event->title) }}
                                                </h3>
                                                <div class="date-time d-flex">
                                                    <x-svg-icon icon="calendar" />
                                                    <span>
                                                        {{ $event->event_date->format('d-F-Y').' '.\Carbon\Carbon::parse($event->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
                                                    </span>
                                                </div>
                                                
                                                <div class="date-time d-flex mt-1">
                                                    <i class='fas fa-map-marker-alt'></i>
                                                    <span>
                                                        {{ $event->eventLocation ? $event->eventLocation->name : '' }}
                                                    </span>
                                                </div>
                                                <div class="limit-description">
                                                    {!! $event->description !!}
                                                </div>

                                                <div class="quotes-date">
                                                    Total Invitations : {{ $event->total_invitation ?? 0 }}
                                                </div>

                                                <div class="quotes-date">
                                                    {{ __('cruds.event.fields.required_volunteers')}} : {{ $event->volunteer ?? 0 }}
                                                </div>
                                                {{-- <span class="quotes-date seminar-date">
                                                    <x-svg-icon icon="small-calender" />
                                                    {{ convertDateTimeFormat($event->created_at,'fulldate') }}
                                                </span> --}}


                                            </div>
                                        </div>
                                        <div class="update-webinar">
                                            <ul class="d-flex">
                                                @can('event_edit')
                                                
                                                    @php
                                                        $eventDateTime = \Carbon\Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->start_time);
                                                    @endphp

                                                    @if(!($eventDateTime < now())) 
                                                    <li>
                                                        <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$event->id}})" title="Edit">
                                                            <x-svg-icon icon="edit" />
                                                        </a>
                                                    </li>
                                                    @endif

                                                @endcan

                                                @can('event_delete')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$event->id}})" title="Delete">
                                                        <x-svg-icon icon="delete" />
                                                    </a>
                                                </li>
                                                @endcan

                                                @can('event_show')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$event->id}})" title="View">
                                                        <x-svg-icon icon="view" />
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('showAttendance', {{$event->id}})" title="Attendance">
                                                        <x-svg-icon icon="show-attendance-user" />
                                                    </a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                    @include('admin.partials.no-record-found')
                                @endif
                            </div>
                                {{ $allEvent->links('vendor.pagination.custom-pagination') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin/assets/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script type="text/javascript">
    document.addEventListener('loadPlugins', function(event) {       

        $('input[id="event_date"]').daterangepicker({
            autoApply: true,
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            minDate: new Date(),
            locale: {
                format: 'DD-MM-YYYY'
            },
        },
        function(start, end, label) {
            @this.set('event_date', start.format('YYYY-MM-DD'));

            @this.set('start_time', null);
            @this.set('end_time', null);

            var now = moment();
            if (start.isSame(now, 'day')) {
                UpdateStartTime(moment().startOf('hour').minute(moment().minute()))
            } else {
                UpdateStartTime(moment().startOf('day'))
            }
        });
        
        if(event.detail.updateMode){
            var fullStTime = new Date(event.detail.full_start_time);
            var fullEtTime = new Date(event.detail.full_end_time);
            
            UpdateStartTime(moment(fullStTime).startOf('day'), 'update_form');
            UpdateEndTime(moment(fullStTime).startOf('hour').minute(moment(fullStTime).minute()), 'update_form');

            $('#start_time').data('daterangepicker').setStartDate(fullStTime);
            $('#end_time').data('daterangepicker').setStartDate(fullEtTime);
        } else {
            UpdateStartTime(moment().startOf('hour').minute(moment().minute()), 'initial');
        }

        // Dropify 
        $('.dropify').dropify();
        $('.dropify-errors-container').remove();
        $('.dropify-clear').click(function(e) {
            e.preventDefault();
            var elementName = $(this).siblings('input[type=file]').attr('id');
            if (elementName == 'dropify-image') {
                @this.set('image', null);
                @this.set('originalImage', null);
                @this.set('removeImage', true);
            }
        });

        $('textarea#summernote').summernote({
            placeholder: 'Type something...',
            tabsize: 2,
            height: 200,
            fontNames: ['Arial', 'Helvetica', 'Times New Roman', 'Courier New', 'sans-serif'],
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                // ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', [ /*'link', 'picture', 'video'*/ ]],
                ['view', ['codeview', /*'help'*/ ]],
            ],
            callbacks: {
                onChange: function(description) {
                    // Update the Livewire property when the Summernote description changes
                    @this.set('description', description);
                }
            }
        });

        $('#updateButton').on('click', function() {
            // Get the current code view content
            var codeViewDescription = $('textarea#summernote').summernote('code');

            // Set the description in the code view
            $('textarea#summernote').summernote('code', codeViewDescription);

            // Trigger the Livewire update
            @this.set('description', codeViewDescription);
        });

    });

    // Start Time
    function UpdateStartTime(time, type='picker_update'){
        if(type == 'picker_update'){
            $('#start_time').data('daterangepicker').remove();
        }
        $('#start_time').daterangepicker({
            autoApply: true,
            timePicker: true,
            timePicker24Hour: false,
            singleDatePicker: true,
            autoUpdateInput: false,
            minDate: time,
            startDate: time,
            locale: {
                format: 'hh:mm A'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            @this.set('start_time', picker.startDate.format('HH:mm'));
            @this.set('end_time', null);
            console.log(picker.startDate)
            if (picker.startDate) {
                UpdateEndTime(moment(picker.startDate).startOf('hour').minute(moment(picker.startDate).minute()));
            } else {
                UpdateEndTime(moment().startOf('day'));
            }
        }).on('show.daterangepicker', function(ev, picker) {
            picker.container.find(".calendar-table").hide();
        });
        if(type != 'update_form'){
            UpdateEndTime(time, type);
        }
        
    }

    // End Time
    function UpdateEndTime(time, type='picker_update'){
        time.add(1, 'minutes');
        if(type == 'picker_update'){
            $('#end_time').data('daterangepicker').remove();
        }
        $('#end_time').daterangepicker({
            autoApply: true,
            timePicker: true,
            timePicker24Hour: false,
            singleDatePicker: true,
            autoUpdateInput: false,
            minDate: time,
            locale: {
                format: 'hh:mm A'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            @this.set('end_time', picker.startDate.format('HH:mm'));
        }).on('show.daterangepicker', function(ev, picker) {
            picker.container.find(".calendar-table").hide();
        });
    }
</script>
@endpush