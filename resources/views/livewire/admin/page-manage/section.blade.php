<div class="content-wrapper">

    <div class="card">
        <div class="card-title top-box-set">
            <h4 class="card-title-heading"> {{ $page->page_name }} @lang('cruds.sections.title') </h4>
            <button wire:click.prevent="cancel" class="btn btn-secondary">
                {{ __('global.back')}}
                <span wire:loading wire:target="cancel">
                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                </span>
            </button>
        </div>
    </div>
    {{-- End Header Section  --}}

        <div wire:loading wire:target="changeTab,cancel" class="loader"></div>

        <div class="row">
            <div class="col-lg-12">
                @if($allSections)
                <!-- Step form tab menu -->
                <ul class="nav nav-pills border-0">
                    @foreach ($allSections as $key=>$section)
                    <li class="nav-item">
                        <a class="nav-link {{ $selectedSection->id == $section->id  ? 'active' : '' }}" wire:click.prevent="changeTab('{{$section->id}}')" data-toggle="pill" href="javascript:void(0);">{{ ucwords($section->title) }}</a>
                    </li>

                    @endforeach
                </ul>
                @endif

                <!-- Step form content -->
                <div class="tab-content p-0 border-0">
                    <div class="tab-pane fade show active" id="{{$selectedSection->section_key}}">

                        <div class="card mb-4">
                            <div class="card-body">
                                <form wire:submit.prevent="update">

                                    <div class="row">
                                        @if($selectedSection->content_text)
                                            <div class="col-sm-12 mb-4">
                                                <div class="form-group mb-0" wire:ignore>
                                                    <label class="font-weight-bold justify-content-start">@lang('cruds.sections.fields.content_text')
                                                        <i class="fas fa-asterisk"></i>
                                                    </label>

                                                    <textarea class="form-control summernote" wire:model.defer="content_text" placeholder="{{ __('cruds.sections.fields.content_text') }}" rows="10" data-elementName="content_text">{{$content_text}}</textarea>

                                                </div>

                                                @error('content_text') <span class="error text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        @endif

                                        @if($selectedSection->is_image)
                                         <div class="col-md-12 mb-4">
                                            <div class="form-group mb-0" wire:ignore>
                                                <label class="font-weight-bold">
                                                    @lang('global.image')
                                                </label>
                                                <input type="file" id="dropify-image-{{$selectedSection->id}}" wire:model.defer="image" class="dropify" data-default-file="{{ $originalImage }}" data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="jpeg png jpg svg" data-min-file-size-preview="1M" data-max-file-size-preview="3M" accept="image/jpeg, image/png, image/jpg">
                                                <span wire:loading wire:target="image">
                                                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Loading
                                                </span>
                                            </div>
                                            @if($errors->has('image'))
                                            <span class="error text-danger">
                                                {{ $errors->first('image') }}
                                            </span>
                                            @endif
                                        </div>
                                        @endif

                                    </div>


                                    <div class="text-right mt-3">
                                        <button class="btn btn-primary" type="submit" wire:loading.attr="disabled">
                                            {{ __('global.update') }}
                                            <span wire:loading wire:target="update">
                                                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                                            </span>
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />
<link rel="stylesheet" href="{{ asset('admin/assets/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script type="text/javascript">

    loadAllPlugins();

    document.addEventListener('loadPlugins', function(event) {
        loadAllPlugins();
    });

    function loadAllPlugins(){
         $('textarea.summernote').summernote({
            placeholder: 'Type something...',
            tabsize: 2,
            height: 400,
            fontNames: ['Arial', 'Helvetica', 'Times New Roman', 'Courier New', 'sans-serif'],
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                // ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', [ /*'link', 'picture', 'video'*/ ]],
                ['view', [ /*'fullscreen',*/ 'codeview', /*'help'*/ ]],
            ],
            callbacks: {
                onChange: function(content) {
                    // Update the Livewire property when the Summernote content changes
                    var variableName = $(this).attr('data-elementName');
                    @this.set(variableName, content);
                }
            }
        });

        $('.dropify').dropify();
        $('.dropify-errors-container').remove();
        $('.dropify-clear').click(function(e) {
            e.preventDefault();
            var elementName = $(this).siblings('input[type=file]').attr('id');
            if (elementName != '') {
                @this.set('image', null);
                @this.set('originalImage', null);
                @this.set('removeImage', true);
            }
        });
    }

</script>
@endpush