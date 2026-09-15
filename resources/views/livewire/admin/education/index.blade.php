<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($formMode)

                    @include('livewire.admin.education.form')

                    @elseif($viewMode)

                    @livewire('admin.education.show', ['education_id' => $education_id])

                    @else
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.education.title') @lang('global.list') </h4>
                        <div class="card-top-box-item">
                            @can('education_create')
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
                                @if($allEducation->count() > 0)
                                @foreach($allEducation as $serialNo => $education)
                                <div class="col-12 col-md-6">
                                    <div class="webinar-item">
                                        <div class="webinar-item-inner">
                                            <div class="webinar-img">
                                                <img class="img-fluid" src="{{ $education->featured_image_url ? $education->featured_image_url : asset(config('constants.default.no_image')) }}" alt="">
                                            </div>
                                            <div class="webinar-content">
                                                <h3>
                                                    {{ ucwords($education->title) }}
                                                </h3>
                                                <div class="limit-description">
                                                    {!! $education->category->name !!}
                                                </div>
                                                <div class="limit-description">
                                                    {!! $education->description !!}
                                                </div>

                                                <span class="quotes-date seminar-date">
                                                    <x-svg-icon icon="small-calender" />
                                                    {{ convertDateTimeFormat($education->created_at,'fulldate') }}
                                                </span>


                                            </div>
                                        </div>
                                        <div class="update-webinar">
                                            <ul class="d-flex">
                                                @can('education_edit')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$education->id}})" title="Edit">
                                                        <x-svg-icon icon="edit" />
                                                    </a>
                                                </li>
                                                @endcan

                                                @can('education_delete')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$education->id}})" title="Delete">
                                                        <x-svg-icon icon="delete" />
                                                    </a>
                                                </li>
                                                @endcan

                                                @can('education_show')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$education->id}})" title="View">
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
                                {{ $allEducation->links('vendor.pagination.custom-pagination') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
<!-- Video Preview Modal -->
<div id="video-preview-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-50 modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Video Preview</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <video id="video-preview" width="100%" controls>
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
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

    $(document).ready(function () {
        // Initialize Dropify when the page is loaded
        initializeDropify();

        // Handle the change event for video_type dropdown
        $('#video_type_select').on('change', function () {
            toggleVideoInputs($(this).val());
        });

        // Reinitialize Dropify when Livewire processes an update
        Livewire.hook('message.processed', function () {
            initializeDropify();
            toggleVideoInputs($('#video_type_select').val());
        });
    });

    function toggleVideoInputs(videoType) {
        const videoLinkInput = $('#video_link_input');
        const videoUploadInput = $('#video_upload_input');

        if (videoType === 'video_link') {
            videoLinkInput.show();
            videoUploadInput.hide();
        } else if (videoType === 'upload_video') {
            videoLinkInput.hide();
            videoUploadInput.show();
        } else {
            videoLinkInput.hide();
            videoUploadInput.hide();
        }
    }  

    function initializeDropify(){
        $('.dropify').dropify();
        // Show preview button if video is uploaded
        $('.dropify').on('change', function () {
            if (this.files && this.files[0]) {
                $('#preview-video-btn').show();
            }
        });

        // Preview Button Click Event      
        $('#preview-video-btn').on('click', function (e) {
            e.preventDefault();
            const fileInput = document.getElementById('dropify-video');
            const file = fileInput.files[0];
            const defaultFile = $(fileInput).data('default-file');

            if (file) {
                const fileURL = URL.createObjectURL(file);
                $('#video-preview').attr('src', fileURL);
                $('#video-preview-modal').modal('show');
            } else if (defaultFile) {
                // Use the default file URL for preview if in update mode
                $('#video-preview').attr('src', defaultFile);
                $('#video-preview-modal').modal('show');
            }
        });        
        // Handle modal close event
        $('#video-preview-modal').on('hidden.bs.modal', function () {
            $('#video-preview').attr('src', ''); // Clear video source when modal is closed
        });
        
        $('.dropify-errors-container').remove();
        $('.dropify-clear').click(function(e) {
            e.preventDefault();
            var elementName = $(this).siblings('input[type=file]').attr('id');
            if (elementName == 'dropify-image') {
                @this.set('image', null);
                @this.set('originalImage', null);
                @this.set('removeImage', true);
            }

            if (elementName == 'dropify-video') {
                @this.set('video', null);
                @this.set('originalVideo', null);
                @this.set('removeVideo', true); 
                Livewire.emit('clearVideo');  
                $('#preview-video-btn').hide();             
            }           
        });
    }

    document.addEventListener('loadPlugins', function(event)
    {
        initializeDropify();
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