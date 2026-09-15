<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($formMode)

                    @include('livewire.admin.category.form')

                    @elseif($viewMode)

                    @livewire('admin.category.show', ['category_id' => $category_id])

                    @else
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.category.title_singular') @lang('global.list') </h4>
                        <div class="card-top-box-item">
                            @can('category_create')
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
                                 @if($allCategory->count() > 0)
                                 @foreach($allCategory as $serialNo => $category)
                                 <div class="col-12 col-md-6">
                                     <div class="webinar-item">
                                         <div class="webinar-item-inner">
                                             <div class="webinar-img">
                                                 <img class="img-fluid" src="{{ $category->featured_image_url ? $category->featured_image_url : asset(config('constants.default.no_image')) }}" alt="">
                                             </div>
                                             <div class="webinar-content">
                                                <h3>
                                                    {{ ucwords($category->name) }}
                                                </h3>                                                      
                                                <div class="limit-description">
                                                    {!! $category->description !!}
                                                </div>

                                                <span class="quotes-date seminar-date">
                                                    <x-svg-icon icon="small-calender" />
                                                    {{ convertDateTimeFormat($category->created_at,'fulldate') }}
                                                </span> 
                                             </div>
                                         </div>
                                         <div class="update-webinar">
                                             <ul class="d-flex">
                                                 @can('category_edit')
                                                 <li>
                                                     <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$category->id}})" title="Edit">
                                                        <x-svg-icon icon="edit" />
                                                     </a>
                                                 </li>
                                                 @endcan
 
                                                 @can('category_delete')
                                                 <li>
                                                     <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$category->id}})" title="Delete">
                                                        <x-svg-icon icon="delete" />
                                                     </a>
                                                 </li>
                                                 @endcan
 
                                                 @can('category_show')
                                                 <li>
                                                     <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$category->id}})" title="View">
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
                                 {{ $allCategory->links('vendor.pagination.custom-pagination') }}
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