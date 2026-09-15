<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($formMode)

                    @include('livewire.admin.heroe.form')

                    @elseif($viewMode)

                    @livewire('admin.heroe.show', ['heroe_id' => $heroe_id])

                    @else
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.heroe.title') @lang('global.list') </h4>
                        <div class="card-top-box-item">
                            @can('heroes_create')
                            <button wire:click="create()" type="button" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                <x-svg-icon icon="add" />
                                {{__('global.add')}}
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="search-table-data">

                       {{-- @livewire('admin.heroe.heroe-table') --}}

                       <div class="relative">

                            @include('admin.partials.table-show-entries-search-box',['searchBoxPlaceholder'=>$searchBoxPlaceholder])
                            
                            <div class="webinar_listing">
                                <div class="row">
                                @if($allHeroe->count() > 0)
                                @foreach($allHeroe as $serialNo => $heroe)
                                <div class="col-12 col-md-6">
                                    <div class="webinar-item">
                                        <div class="webinar-item-inner">
                                            <div class="webinar-img">
                                                <img class="img-fluid" src="{{ $heroe->featured_image_url ? $heroe->featured_image_url : asset(config('constants.default.no_image')) }}" alt="">
                                            </div>
                                            <div class="webinar-content">
                                                <h3>
                                                    {{ ucwords($heroe->name) }}
                                                </h3>

                                                <div class="limit-description">
                                                    {!! $heroe->description !!}
                                                </div>

                                                <span class="quotes-date seminar-date">
                                                    <svg width="14" height="15" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8.66683 2.33337H2.25016C1.7439 2.33337 1.3335 2.74378 1.3335 3.25004V9.66671C1.3335 10.173 1.7439 10.5834 2.25016 10.5834H8.66683C9.17309 10.5834 9.5835 10.173 9.5835 9.66671V3.25004C9.5835 2.74378 9.17309 2.33337 8.66683 2.33337Z" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M7.2915 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M3.62549 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M1.3335 5.08337H9.5835" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    {{ convertDateTimeFormat($heroe->created_at,'fulldate') }}
                                                </span>


                                            </div>
                                        </div>
                                        <div class="update-webinar">
                                            <ul class="d-flex">
                                                @can('heroes_edit')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$heroe->id}})" title="Edit">
                                                        <x-svg-icon icon="edit" />
                                                    </a>
                                                </li>
                                                @endcan

                                                @can('heroes_delete')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$heroe->id}})" title="Delete">
                                                        <x-svg-icon icon="delete" />
                                                    </a>
                                                </li>
                                                @endcan

                                                @can('heroes_show')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$heroe->id}})" title="View">
                                                        <x-svg-icon icon="view" />
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
                                {{ $allHeroe->links('vendor.pagination.custom-pagination') }}
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

@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script type="text/javascript">
    document.addEventListener('loadPlugins', function(event) {       

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
</script>
@endpush