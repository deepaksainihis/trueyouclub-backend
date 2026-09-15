<div>
    <div class="content-wrapper">
        <!-- Start headsection -->
        <div class="row">
            <div class="col-lg-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex float-left">
                            <h4 class="font-weight-bold">Settings</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End headsection -->

        <div class="row">
            <div class="col-lg-12">
                @if($allSettingType)
                <!-- Step form tab menu -->
                <ul class="nav nav-pills border-0">
                    @foreach ($allSettingType as $key=>$groupType)

                    @php
                    $groupName = str_replace('_',' ',$groupType)
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == $groupType  ? 'active' : '' }}" wire:click.prevent="changeTab('{{$groupType}}')" data-toggle="pill" href="javascript:void(0);">{{ ucwords($groupName) }}</a>
                    </li>

                    @endforeach
                </ul>
                @endif

                <!-- Step form content -->
                <div class="tab-content p-0 border-0">
                    <div class="tab-pane fade show active" id="{{$tab}}">

                        <div class="card mb-4">
                            <div class="card-header background-purple-color mb-4">
                                <label class="font-weight-bold">{{ ucwords(str_replace('_',' ',$tab)) }}</label>
                            </div>
                            <div class="card-body">

                                <form wire:submit.prevent="update">
                                    @if($settings)

                                    <div class="row">
                                        @foreach($settings as $setting)

                                        @if($setting->type == 'text')
                                        <div class="{{ in_array($setting->group,array('site','payment','social media')) ? 'col-sm-12' : 'col-sm-6'}}">
                                            <div class="form-group">
                                                <label class="font-weight-bold justify-content-start">{{ $setting->display_name }}
                                                    <i class="fas fa-asterisk"></i>
                                                </label>
                                                <input type="text" class="form-control" wire:model.defer="state.{{$setting->key}}" placeholder="{{$setting->display_name}}" />
                                                @error('state.'.$setting->key) <span class="error text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                        @elseif($setting->type == 'number')
                                        <div class="{{ in_array($setting->group,array('site','introduction_video')) ? 'col-sm-12' : 'col-sm-6'}}">
                                            <div class="form-group">
                                                <label class="font-weight-bold justify-content-start">{{ $setting->display_name }}
                                                    <i class="fas fa-asterisk"></i>
                                                </label>
                                                <input type="number" class="form-control" wire:model.defer="state.{{$setting->key}}" placeholder="{{$setting->display_name}}" />
                                                @error('state.'.$setting->key) <span class="error text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                        @elseif($setting->type == 'text_area')
                                        <div class="col-sm-12 mb-4">
                                            <div class="form-group mb-0" wire:ignore>
                                                <label class="font-weight-bold justify-content-start">{{ $setting->display_name }}
                                                    @if($setting->group != 'mail')
                                                    <i class="fas fa-asterisk"></i>
                                                    @endif
                                                </label>

                                                @if($setting->details)
                                                @php
                                                $parameterArray = explode(', ',$setting->details);
                                                @endphp
                                                @if($parameterArray)
                                                @foreach($parameterArray as $parameter)
                                                <button class="btn btn-sm btn-info copy-btn mb-1" data-elementVal="{{$parameter}}">{{ $parameter }}</button>
                                                @endforeach
                                                @endif
                                                @endif
                                                <textarea class="form-control summernote" wire:model.defer="state.{{$setting->key}}" data-elementName="state.{{$setting->key}}" placeholder="{{$setting->display_name}}" rows="4">{{$setting->value}}</textarea>

                                            </div>

                                            @error('state.'.$setting->key) <span class="error text-danger">{{ $message }}</span>@enderror
                                        </div>
                                        @elseif($setting->type == 'image')
                                        <div class="col-md-12 mb-4">
                                            <div class="form-group mb-0" wire:ignore>
                                                <label class="font-weight-bold">
                                                    {{ $setting->display_name }}

                                                    @if($setting->details)
                                                    <span>Size : {{ $setting->details }} </span>
                                                    @endif

                                                </label>
                                                <input type="file" id="{{$setting->key}}-image" wire:model.defer="state.{{$setting->key}}" class="dropify" data-default-file="{{ $setting->image_url }}" data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="jpeg png jpg svg" data-min-file-size-preview="1M" data-max-file-size-preview="3M" accept="image/jpeg, image/png, image/jpg,image/svg">
                                                <span wire:loading wire:target="state.{{$setting->key}}">
                                                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Loading
                                                </span>
                                            </div>
                                            @if($errors->has('state.'.$setting->key))
                                            <span class="error text-danger">
                                                {{ $errors->first('state.'.$setting->key) }}
                                            </span>
                                            @endif
                                        </div>
                                        @elseif($setting->type == 'video')
                                        <div class="col-md-12 mb-4">
                                            <div class="form-group mb-0" wire:ignore>
                                                <label class="font-weight-bold">{{ $setting->display_name }}</label>

                                                <input type="file" id="{{$setting->key}}-video" wire:model.defer="state.{{$setting->key}}" class="dropify" data-default-file="{{  $setting->video_url }}" data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="webm mp4 avi wmv flv mov" data-min-file-size-preview="1M" data-max-file-size-preview="3M" accept="video/webm, video/mp4, video/avi,video/wmv,video/flv,video/mov">

                                                <span wire:loading wire:target="state.{{$setting->key}}">
                                                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Loading
                                                </span>
                                            </div>
                                            @if($errors->has('state.'.$setting->key))
                                            <span class="error text-danger">
                                                {{ $errors->first('state.'.$setting->key) }}
                                            </span>
                                            @endif
                                        </div>

                                        @elseif($setting->type == 'toggle')
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold justify-content-start">{{ $setting->display_name }}
                                                    <i class="fas fa-asterisk"></i>
                                                </label>
                                                <div class="form-group">
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" id="{{$setting->key}}" value="{{$state[$setting->key]}}" class="toggleSwitch setting-toggle" {{ $state[$setting->key] == 'active' ? 'checked': '' }}>
                                                        <span class="switch-slider"></span>
                                                    </label>
                                                    @if($errors->has('state.'.$setting->key))
                                                    <span class="error text-danger">
                                                        {{ $errors->first('state.'.$setting->key) }}
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                        @endif

                                        @endforeach
                                    </div>

                                    @endif

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
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
    @if($settings)
    $(document).ready(function() {
        $('.dropify').dropify();
        $('.dropify-errors-container').remove();
        $('.dropify-clear').click(function(e) {
            e.preventDefault();
            var elementName = $(this).siblings('input[type=file]').attr('id');
            elementName = elementName.split('-');
            if (elementName[1] == 'image') {
                @this.set('state.' + elementName[0], null);
                @this.set('removeFile.remove_' + elementName[0], true);
            } else if (elementName[1] == 'video') {
                @this.set('state.' + elementName[0], null);
                @this.set('removeFile.remove_' + elementName[0], true);
            }
        });
    });

    $('textarea.summernote').summernote({
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
    @endif

    document.addEventListener('loadPlugins', function(event) {

        $('.dropify').dropify();
        $('.dropify-errors-container').remove();
        $('.dropify-clear').click(function(e) {
            e.preventDefault();
            var elementName = $(this).siblings('input[type=file]').attr('id');
            elementName = elementName.split('-');
            if (elementName[1] == 'image') {
                @this.set('state.' + elementName[0], 'delete');
                @this.set('removeFile.remove_' + elementName[0], true);
            } else if (elementName[1] == 'video') {
                @this.set('state.' + elementName[0], 'delete');
                @this.set('removeFile.remove_' + elementName[0], true);
            }
        });

        $('textarea.summernote').summernote({
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

    });


    $(document).ready(function() {
        $(document).on('click', '.copy-btn', function(event) {
            event.preventDefault();
            var elementVal = $(this).attr('data-elementVal');
            // console.log('click on copy btn',elementVal);
            copyToClipboard(elementVal);
            Livewire.emit('copyTextAlert');
        });

        $(document).on('change', '.section-type', function() {
            var sectionType = $(this).val();
            Livewire.emit('changeType', sectionType);

        });

        $(document).on('change', '.setting-toggle', function() {

            var $this = $(this);
            var elementId = $this.attr('id');
            var status = $this.val();

            if (status == 'inactive') {
                @this.set('state.' + elementId, 'active');
            } else if (status == 'active') {
                @this.set('state.' + elementId, 'inactive');
            }

        });
    });

    function copyToClipboard(value) {
        // Create a temporary input element
        var tempInput = document.createElement('input');
        tempInput.setAttribute('value', value);
        document.body.appendChild(tempInput);

        // Select the input element's content
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected text
        document.execCommand('copy');

        // Remove the temporary input element
        document.body.removeChild(tempInput);

        // alert('Link copied to clipboard: ' + url);
    }
</script>
@endpush