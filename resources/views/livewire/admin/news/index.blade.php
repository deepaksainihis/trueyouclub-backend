<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($formMode)

                    @include('livewire.admin.news.form')

                    @elseif($viewMode)

                    @livewire('admin.news.show', ['news_id' => $news_id])

                    @else
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.news.title') @lang('global.list') </h4>
                        <div class="card-top-box-item">
                            @can('news_create')
                            <button wire:click="create()" type="button" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.73956 14.3355C8.55177 14.5232 8.2948 14.6418 7.99829 14.6418C7.42503 14.6418 6.95062 14.1674 6.95062 13.5942L6.95062 2.40586C6.95062 1.8326 7.42503 1.35819 7.99828 1.35819C8.57154 1.35819 9.04595 1.8326 9.04595 2.40586L9.04595 13.5942C9.05584 13.8808 8.92735 14.1477 8.73956 14.3355Z" fill="#0A2540" />
                                    <path d="M14.3337 8.74129C14.1459 8.92908 13.889 9.04769 13.5924 9.04769L2.40412 9.04769C1.83087 9.04769 1.35645 8.57327 1.35645 8.00002C1.35645 7.42676 1.83087 6.95235 2.40412 6.95235L13.5924 6.95235C14.1657 6.95235 14.6401 7.42676 14.6401 8.00002C14.6401 8.29653 14.5215 8.5535 14.3337 8.74129Z" fill="#0A2540" />
                                </svg>
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
                                @if($allNews->count() > 0)
                                @foreach($allNews as $serialNo => $news)
                                <div class="col-12 col-md-6">
                                    <div class="webinar-item">
                                        <div class="webinar-item-inner">
                                            <div class="webinar-img">
                                                <img class="img-fluid" src="{{ $news->news_image_url ? $news->news_image_url : asset(config('constants.default.no_image')) }}" alt="">
                                            </div>
                                            <div class="webinar-content">
                                                <h3>
                                                    {{ ucwords($news->title) }}
                                                </h3>

                                                <div class="limit-description">
                                                    {!! $news->content !!}
                                                </div>

                                                <span class="quotes-date seminar-date">
                                                    <svg width="14" height="15" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8.66683 2.33337H2.25016C1.7439 2.33337 1.3335 2.74378 1.3335 3.25004V9.66671C1.3335 10.173 1.7439 10.5834 2.25016 10.5834H8.66683C9.17309 10.5834 9.5835 10.173 9.5835 9.66671V3.25004C9.5835 2.74378 9.17309 2.33337 8.66683 2.33337Z" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M7.2915 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M3.62549 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M1.3335 5.08337H9.5835" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    {{ convertDateTimeFormat($news->publish_date,'fulldate') }}
                                                </span>


                                            </div>
                                        </div>
                                        <div class="update-webinar">
                                            <ul class="d-flex">
                                                @can('news_edit')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$news->id}})" title="Edit">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12.5003 18.9583H7.50033C2.97533 18.9583 1.04199 17.025 1.04199 12.5V7.49996C1.04199 2.97496 2.97533 1.04163 7.50033 1.04163H9.16699C9.50866 1.04163 9.79199 1.32496 9.79199 1.66663C9.79199 2.00829 9.50866 2.29163 9.16699 2.29163H7.50033C3.65866 2.29163 2.29199 3.65829 2.29199 7.49996V12.5C2.29199 16.3416 3.65866 17.7083 7.50033 17.7083H12.5003C16.342 17.7083 17.7087 16.3416 17.7087 12.5V10.8333C17.7087 10.4916 17.992 10.2083 18.3337 10.2083C18.6753 10.2083 18.9587 10.4916 18.9587 10.8333V12.5C18.9587 17.025 17.0253 18.9583 12.5003 18.9583Z" fill="#40658B" />
                                                            <path d="M7.08311 14.7417C6.57478 14.7417 6.10811 14.5584 5.76645 14.225C5.35811 13.8167 5.18311 13.225 5.27478 12.6L5.63311 10.0917C5.69978 9.60838 6.01645 8.98338 6.35811 8.64172L12.9248 2.07505C14.5831 0.416716 16.2664 0.416716 17.9248 2.07505C18.8331 2.98338 19.2414 3.90838 19.1581 4.83338C19.0831 5.58338 18.6831 6.31672 17.9248 7.06672L11.3581 13.6334C11.0164 13.975 10.3914 14.2917 9.90811 14.3584L7.39978 14.7167C7.29145 14.7417 7.18311 14.7417 7.08311 14.7417ZM13.8081 2.95838L7.24145 9.52505C7.08311 9.68338 6.89978 10.05 6.86645 10.2667L6.50811 12.775C6.47478 13.0167 6.52478 13.2167 6.64978 13.3417C6.77478 13.4667 6.97478 13.5167 7.21645 13.4834L9.72478 13.125C9.94145 13.0917 10.3164 12.9084 10.4664 12.75L17.0331 6.18338C17.5748 5.64172 17.8581 5.15838 17.8998 4.70838C17.9498 4.16672 17.6664 3.59172 17.0331 2.95005C15.6998 1.61672 14.7831 1.99172 13.8081 2.95838Z" fill="#40658B" />
                                                            <path d="M16.5413 8.19173C16.483 8.19173 16.4246 8.1834 16.3746 8.16673C14.183 7.55006 12.4413 5.8084 11.8246 3.61673C11.733 3.2834 11.9246 2.94173 12.258 2.84173C12.5913 2.75006 12.933 2.94173 13.0246 3.27506C13.5246 5.05006 14.933 6.4584 16.708 6.9584C17.0413 7.05006 17.233 7.40006 17.1413 7.7334C17.0663 8.01673 16.8163 8.19173 16.5413 8.19173Z" fill="#40658B" />
                                                        </svg>
                                                    </a>
                                                </li>
                                                @endcan

                                                @can('news_delete')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$news->id}})" title="Delete">
                                                        <svg width="18" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.3337 3.33333H13.167V1.66667C13.167 1.22464 12.9914 0.800716 12.6788 0.488155C12.3663 0.175595 11.9424 0 11.5003 0L6.50033 0C6.0583 0 5.63437 0.175595 5.32181 0.488155C5.00925 0.800716 4.83366 1.22464 4.83366 1.66667V3.33333H0.666992V5H2.33366V17.5C2.33366 18.163 2.59705 18.7989 3.06589 19.2678C3.53473 19.7366 4.17062 20 4.83366 20H13.167C13.83 20 14.4659 19.7366 14.9348 19.2678C15.4036 18.7989 15.667 18.163 15.667 17.5V5H17.3337V3.33333ZM6.50033 1.66667H11.5003V3.33333H6.50033V1.66667ZM14.0003 17.5C14.0003 17.721 13.9125 17.933 13.7562 18.0893C13.6 18.2455 13.388 18.3333 13.167 18.3333H4.83366C4.61265 18.3333 4.40068 18.2455 4.2444 18.0893C4.08812 17.933 4.00033 17.721 4.00033 17.5V5H14.0003V17.5Z" fill="#626263"/>
                                                            <path d="M8.16667 8.3335H6.5V15.0002H8.16667V8.3335Z" fill="#626263"/>
                                                            <path d="M11.5007 8.3335H9.83398V15.0002H11.5007V8.3335Z" fill="#626263"/>
                                                        </svg>
                                                    </a>
                                                </li>
                                                @endcan

                                                @can('news_show')
                                                <li>
                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$news->id}})" title="View">
                                                        <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M19.8507 7.3175C19.1191 5.7175 16.2499 0.5 9.99989 0.5C3.74989 0.5 0.880726 5.7175 0.149059 7.3175C0.0508413 7.53192 0 7.76499 0 8.00083C0 8.23668 0.0508413 8.46975 0.149059 8.68417C0.880726 10.2825 3.74989 15.5 9.99989 15.5C16.2499 15.5 19.1191 10.2825 19.8507 8.6825C19.9487 8.46832 19.9995 8.23554 19.9995 8C19.9995 7.76446 19.9487 7.53168 19.8507 7.3175ZM9.99989 13.8333C4.74406 13.8333 2.29156 9.36167 1.66656 8.00917C2.29156 6.63833 4.74406 2.16667 9.99989 2.16667C15.2432 2.16667 17.6966 6.61917 18.3332 8C17.6966 9.38083 15.2432 13.8333 9.99989 13.8333Z" fill="#626263"/>
                                                            <path d="M9.99968 3.8335C9.17559 3.8335 8.37001 4.07787 7.6848 4.53571C6.9996 4.99355 6.46554 5.64429 6.15018 6.40565C5.83481 7.16701 5.7523 8.00479 5.91307 8.81304C6.07384 9.62129 6.47068 10.3637 7.0534 10.9464C7.63612 11.5292 8.37855 11.926 9.1868 12.0868C9.99505 12.2475 10.8328 12.165 11.5942 11.8497C12.3555 11.5343 13.0063 11.0002 13.4641 10.315C13.922 9.62983 14.1663 8.82425 14.1663 8.00016C14.165 6.8955 13.7256 5.83646 12.9445 5.05535C12.1634 4.27423 11.1043 3.83482 9.99968 3.8335ZM9.99968 10.5002C9.50522 10.5002 9.02187 10.3535 8.61075 10.0788C8.19963 9.80413 7.8792 9.41369 7.68998 8.95687C7.50076 8.50006 7.45125 7.99739 7.54771 7.51244C7.64418 7.02748 7.88228 6.58203 8.23191 6.2324C8.58154 5.88276 9.027 5.64466 9.51195 5.5482C9.9969 5.45174 10.4996 5.50124 10.9564 5.69046C11.4132 5.87968 11.8036 6.20011 12.0784 6.61124C12.3531 7.02236 12.4997 7.50571 12.4997 8.00016C12.4997 8.6632 12.2363 9.29909 11.7674 9.76793C11.2986 10.2368 10.6627 10.5002 9.99968 10.5002Z" fill="#626263"/>
                                                        </svg>
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
                                {{ $allNews->links('vendor.pagination.custom-pagination') }}
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script type="text/javascript">
    document.addEventListener('loadPlugins', function(event) {

        $('input[id="publish_date"]').daterangepicker({
                autoApply: true,
                singleDatePicker: true,
                showDropdowns: true,
                minDate: new Date(),
                locale: {
                    format: 'DD-MM-YYYY'
                },
            },
            function(start, end, label) {
                @this.set('publish_date', start.format('YYYY-MM-DD'));
            });

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
                onChange: function(content) {
                    // Update the Livewire property when the Summernote content changes
                    @this.set('content', content);
                }
            }
        });

        $('#updateButton').on('click', function() {
            // Get the current code view content
            var codeViewContent = $('textarea#summernote').summernote('code');

            // Set the content in the code view
            $('textarea#summernote').summernote('code', codeViewContent);

            // Trigger the Livewire update
            @this.set('content', codeViewContent);
        });

    });
</script>
@endpush