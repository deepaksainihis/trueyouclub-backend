<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              
                <div class="card-body">

                    @if($formMode)

                    @include('livewire.admin.webinar.form')

                    @elseif($viewMode)

                    @livewire('admin.webinar.show', ['webinar_id' => $webinar_id])

                    @else
                    <div wire:loading wire:target="create" class="loader"></div>
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">@lang('cruds.webinar.title') @lang('global.list') </h4>
                        <div class="card-top-box-item">
                            @can('webinar_create')
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

                       {{-- @livewire('admin.webinar.webinar-table') --}}

                       @include('admin.partials.table-show-entries-search-box',['searchBoxPlaceholder'=>$searchBoxPlaceholder])

                        <div class="webinar_listing">
                            <div class="row">
                                @if($allWebinar->count() > 0)
                                    @foreach($allWebinar as $serialNo => $webinar)
                                        @php 
                                            $date = $webinar->start_date;
                                            $time = $webinar->start_time; 
                                            $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
                                        
                                            $endDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date .' '.$webinar->end_time);

                                            $now = now();
                                            $startWebinarTime = \Carbon\Carbon::parse($dateTime);

                                            $startDays    = 0;
                                            $startHours   = 0;
                                            $startMinutes = 0;
                                            $startSeconds = 0;
                                            $diffInSeconds = 0;
                                            if($now < $startWebinarTime){
                                                $timeDiff = $now->diff($startWebinarTime);
                                                $diffInSeconds = $now->diffInSeconds($startWebinarTime);

                                                $startDays    = $timeDiff->days;
                                                $startHours   = $timeDiff->h;
                                                $startMinutes = $timeDiff->i;
                                                $startSeconds = $timeDiff->s;
                                            }
                                        @endphp
                                        <div class="col-12 col-md-6">
                                            <div class="webinar-item {{ $endDateTime < $now ? 'webinar-disabled' : '' }} {{ $diffInSeconds > 0 ? 'webinar-item-active' : '' }}" data-diff_in_seconds="{{ $diffInSeconds }}">
                                                <div class="webinar-item-inner">                                
                                                    @if($endDateTime < $now)
                                                        <div class="buyer-active-verfiy"><span>Expired Webinar </span></div>                            
                                                    @endif
                                                    <div class="buyer-active-verfiy ongoingtag {{$now >= $dateTime && $now <= $endDateTime ? '': 'd-none'}} "><span>Ongoing </span></div>
                                                    <div class="webinar-img">
                                                        <img class="img-fluid" src="{{ $webinar->image_url ? $webinar->image_url : asset(config('constants.default.no_image')) }}" alt="">
                                                    </div>
                                                    <div class="webinar-content">                                
                                                        <h3>
                                                            {{ ucwords($webinar->title) }}
                                                        </h3>
                                                        <div class="date-time d-flex">
                                                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6 4.8125C5.6925 4.8125 5.4375 4.5575 5.4375 4.25V2C5.4375 1.6925 5.6925 1.4375 6 1.4375C6.3075 1.4375 6.5625 1.6925 6.5625 2V4.25C6.5625 4.5575 6.3075 4.8125 6 4.8125Z" fill="#DA7821"/>
                                                                <path d="M12 4.8125C11.6925 4.8125 11.4375 4.5575 11.4375 4.25V2C11.4375 1.6925 11.6925 1.4375 12 1.4375C12.3075 1.4375 12.5625 1.6925 12.5625 2V4.25C12.5625 4.5575 12.3075 4.8125 12 4.8125Z" fill="#DA7821"/>
                                                                <path d="M6.375 11.375C6.2775 11.375 6.18 11.3525 6.09 11.315C5.9925 11.2775 5.9175 11.225 5.8425 11.1575C5.7075 11.015 5.625 10.8275 5.625 10.625C5.625 10.5275 5.6475 10.43 5.685 10.34C5.7225 10.25 5.775 10.1675 5.8425 10.0925C5.9175 10.025 5.9925 9.97249 6.09 9.93499C6.36 9.82249 6.6975 9.88251 6.9075 10.0925C7.0425 10.235 7.125 10.43 7.125 10.625C7.125 10.67 7.1175 10.7225 7.11 10.775C7.1025 10.82 7.0875 10.865 7.065 10.91C7.05 10.955 7.0275 11 6.9975 11.045C6.975 11.0825 6.9375 11.12 6.9075 11.1575C6.765 11.2925 6.57 11.375 6.375 11.375Z" fill="#DA7821"/>
                                                                <path d="M9 11.375C8.9025 11.375 8.805 11.3525 8.715 11.315C8.6175 11.2775 8.5425 11.225 8.4675 11.1575C8.3325 11.015 8.25 10.8275 8.25 10.625C8.25 10.5275 8.2725 10.43 8.31 10.34C8.3475 10.25 8.4 10.1675 8.4675 10.0925C8.5425 10.025 8.6175 9.97249 8.715 9.93499C8.985 9.81499 9.3225 9.8825 9.5325 10.0925C9.6675 10.235 9.75 10.43 9.75 10.625C9.75 10.67 9.7425 10.7225 9.735 10.775C9.7275 10.82 9.7125 10.865 9.69 10.91C9.675 10.955 9.6525 11 9.6225 11.045C9.6 11.0825 9.5625 11.12 9.5325 11.1575C9.39 11.2925 9.195 11.375 9 11.375Z" fill="#DA7821"/>
                                                                <path d="M11.625 11.375C11.5275 11.375 11.43 11.3525 11.34 11.315C11.2425 11.2775 11.1675 11.225 11.0925 11.1575C11.0625 11.12 11.0325 11.0825 11.0025 11.045C10.9725 11 10.95 10.955 10.935 10.91C10.9125 10.865 10.8975 10.82 10.89 10.775C10.8825 10.7225 10.875 10.67 10.875 10.625C10.875 10.43 10.9575 10.235 11.0925 10.0925C11.1675 10.025 11.2425 9.97249 11.34 9.93499C11.6175 9.81499 11.9475 9.8825 12.1575 10.0925C12.2925 10.235 12.375 10.43 12.375 10.625C12.375 10.67 12.3675 10.7225 12.36 10.775C12.3525 10.82 12.3375 10.865 12.315 10.91C12.3 10.955 12.2775 11 12.2475 11.045C12.225 11.0825 12.1875 11.12 12.1575 11.1575C12.015 11.2925 11.82 11.375 11.625 11.375Z" fill="#DA7821"/>
                                                                <path d="M6.375 14C6.2775 14 6.18 13.9775 6.09 13.94C6 13.9025 5.9175 13.85 5.8425 13.7825C5.7075 13.64 5.625 13.445 5.625 13.25C5.625 13.1525 5.6475 13.055 5.685 12.965C5.7225 12.8675 5.775 12.785 5.8425 12.7175C6.12 12.44 6.63 12.44 6.9075 12.7175C7.0425 12.86 7.125 13.055 7.125 13.25C7.125 13.445 7.0425 13.64 6.9075 13.7825C6.765 13.9175 6.57 14 6.375 14Z" fill="#DA7821"/>
                                                                <path d="M9 14C8.805 14 8.61 13.9175 8.4675 13.7825C8.3325 13.64 8.25 13.445 8.25 13.25C8.25 13.1525 8.2725 13.055 8.31 12.965C8.3475 12.8675 8.4 12.785 8.4675 12.7175C8.745 12.44 9.255 12.44 9.5325 12.7175C9.6 12.785 9.6525 12.8675 9.69 12.965C9.7275 13.055 9.75 13.1525 9.75 13.25C9.75 13.445 9.6675 13.64 9.5325 13.7825C9.39 13.9175 9.195 14 9 14Z" fill="#DA7821"/>
                                                                <path d="M11.625 14C11.43 14 11.235 13.9175 11.0925 13.7825C11.025 13.715 10.9725 13.6325 10.935 13.535C10.8975 13.445 10.875 13.3475 10.875 13.25C10.875 13.1525 10.8975 13.055 10.935 12.965C10.9725 12.8675 11.025 12.785 11.0925 12.7175C11.265 12.545 11.5275 12.4625 11.7675 12.515C11.82 12.5225 11.865 12.5375 11.91 12.56C11.955 12.575 12 12.5975 12.045 12.6275C12.0825 12.65 12.12 12.6875 12.1575 12.7175C12.2925 12.86 12.375 13.055 12.375 13.25C12.375 13.445 12.2925 13.64 12.1575 13.7825C12.015 13.9175 11.82 14 11.625 14Z" fill="#DA7821"/>
                                                                <path d="M15.375 7.87997H2.625C2.3175 7.87997 2.0625 7.62497 2.0625 7.31747C2.0625 7.00997 2.3175 6.75497 2.625 6.75497H15.375C15.6825 6.75497 15.9375 7.00997 15.9375 7.31747C15.9375 7.62497 15.6825 7.87997 15.375 7.87997Z" fill="#DA7821"/>
                                                                <path d="M12 17.5625H6C3.2625 17.5625 1.6875 15.9875 1.6875 13.25V6.875C1.6875 4.1375 3.2625 2.5625 6 2.5625H12C14.7375 2.5625 16.3125 4.1375 16.3125 6.875V13.25C16.3125 15.9875 14.7375 17.5625 12 17.5625ZM6 3.6875C3.855 3.6875 2.8125 4.73 2.8125 6.875V13.25C2.8125 15.395 3.855 16.4375 6 16.4375H12C14.145 16.4375 15.1875 15.395 15.1875 13.25V6.875C15.1875 4.73 14.145 3.6875 12 3.6875H6Z" fill="#DA7821"/>
                                                            </svg>
                                                            <span>
                                                                {{ convertDateTimeFormat($webinar->start_date.' '.$webinar->start_time,'fulldatetime') }} - {{ \Carbon\Carbon::parse($webinar->end_time)->format('h:i A') }}
                                                            </span>
                                                        </div>
                                                        <a href="{{ $endDateTime < $now ? 'javascript:void(0)' : $webinar->meeting_link }}" class="btn btn-primary joinBtn {{ $diffInSeconds != 0 ? 'd-none' : '' }}" target="_blank">
                                                            Join The Webinar
                                                        </a>
                                                    
                                                        @if($diffInSeconds != 0)
                                                        <div class="webinar-time-system webinar-time-{{ $webinar->id }} counter-main  {{ $diffInSeconds == 0 ? 'd-none' : '' }}">
                                                            
                                                            <div class="time-item counter-outer" data-label="days" data-value="{{ $startDays }}">
                                                                <b class="counter" >{{ $startDays }}</b><span>Days</span>
                                                            </div>:
                                                            <div class="time-item counter-outer" data-label="hours" data-value="{{ $startHours }}">
                                                                <b class="counter" >{{ $startHours }}</b><span>Hours</span>
                                                            </div>:
                                                            <div class="time-item counter-outer" data-label="minutes" data-value="{{ $startMinutes }}">
                                                                <b class="counter" >{{ $startMinutes }}</b><span>Minute</span>
                                                            </div>:
                                                            <div class="time-item counter-outer" data-label="seconds" data-value="{{ $startSeconds }}">
                                                                <b class="counter" >{{ $startSeconds }}</b><span>Second</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="update-webinar">
                                                    <ul class="d-flex">
                                                        @can('webinar_edit')
                                                            @if($endDateTime > $now)
                                                            <li>
                                                                <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$webinar->id}})" title="Edit">
                                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M12.5003 18.9583H7.50033C2.97533 18.9583 1.04199 17.025 1.04199 12.5V7.49996C1.04199 2.97496 2.97533 1.04163 7.50033 1.04163H9.16699C9.50866 1.04163 9.79199 1.32496 9.79199 1.66663C9.79199 2.00829 9.50866 2.29163 9.16699 2.29163H7.50033C3.65866 2.29163 2.29199 3.65829 2.29199 7.49996V12.5C2.29199 16.3416 3.65866 17.7083 7.50033 17.7083H12.5003C16.342 17.7083 17.7087 16.3416 17.7087 12.5V10.8333C17.7087 10.4916 17.992 10.2083 18.3337 10.2083C18.6753 10.2083 18.9587 10.4916 18.9587 10.8333V12.5C18.9587 17.025 17.0253 18.9583 12.5003 18.9583Z" fill="#40658B"/>
                                                                        <path d="M7.08311 14.7417C6.57478 14.7417 6.10811 14.5584 5.76645 14.225C5.35811 13.8167 5.18311 13.225 5.27478 12.6L5.63311 10.0917C5.69978 9.60838 6.01645 8.98338 6.35811 8.64172L12.9248 2.07505C14.5831 0.416716 16.2664 0.416716 17.9248 2.07505C18.8331 2.98338 19.2414 3.90838 19.1581 4.83338C19.0831 5.58338 18.6831 6.31672 17.9248 7.06672L11.3581 13.6334C11.0164 13.975 10.3914 14.2917 9.90811 14.3584L7.39978 14.7167C7.29145 14.7417 7.18311 14.7417 7.08311 14.7417ZM13.8081 2.95838L7.24145 9.52505C7.08311 9.68338 6.89978 10.05 6.86645 10.2667L6.50811 12.775C6.47478 13.0167 6.52478 13.2167 6.64978 13.3417C6.77478 13.4667 6.97478 13.5167 7.21645 13.4834L9.72478 13.125C9.94145 13.0917 10.3164 12.9084 10.4664 12.75L17.0331 6.18338C17.5748 5.64172 17.8581 5.15838 17.8998 4.70838C17.9498 4.16672 17.6664 3.59172 17.0331 2.95005C15.6998 1.61672 14.7831 1.99172 13.8081 2.95838Z" fill="#40658B"/>
                                                                        <path d="M16.5413 8.19173C16.483 8.19173 16.4246 8.1834 16.3746 8.16673C14.183 7.55006 12.4413 5.8084 11.8246 3.61673C11.733 3.2834 11.9246 2.94173 12.258 2.84173C12.5913 2.75006 12.933 2.94173 13.0246 3.27506C13.5246 5.05006 14.933 6.4584 16.708 6.9584C17.0413 7.05006 17.233 7.40006 17.1413 7.7334C17.0663 8.01673 16.8163 8.19173 16.5413 8.19173Z" fill="#40658B"/>
                                                                    </svg>
                                                                </a>
                                                            </li>
                                                            @endif
                                                        @endcan

                                                        @can('webinar_delete')
                                                            
                                                            <li>
                                                                <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$webinar->id}})" title="Delete">
                                                                    <svg width="18" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M17.3337 3.33333H13.167V1.66667C13.167 1.22464 12.9914 0.800716 12.6788 0.488155C12.3663 0.175595 11.9424 0 11.5003 0L6.50033 0C6.0583 0 5.63437 0.175595 5.32181 0.488155C5.00925 0.800716 4.83366 1.22464 4.83366 1.66667V3.33333H0.666992V5H2.33366V17.5C2.33366 18.163 2.59705 18.7989 3.06589 19.2678C3.53473 19.7366 4.17062 20 4.83366 20H13.167C13.83 20 14.4659 19.7366 14.9348 19.2678C15.4036 18.7989 15.667 18.163 15.667 17.5V5H17.3337V3.33333ZM6.50033 1.66667H11.5003V3.33333H6.50033V1.66667ZM14.0003 17.5C14.0003 17.721 13.9125 17.933 13.7562 18.0893C13.6 18.2455 13.388 18.3333 13.167 18.3333H4.83366C4.61265 18.3333 4.40068 18.2455 4.2444 18.0893C4.08812 17.933 4.00033 17.721 4.00033 17.5V5H14.0003V17.5Z" fill="#626263"/>
                                                                        <path d="M8.16667 8.3335H6.5V15.0002H8.16667V8.3335Z" fill="#626263"/>
                                                                        <path d="M11.5007 8.3335H9.83398V15.0002H11.5007V8.3335Z" fill="#626263"/>
                                                                    </svg>
                                                                </a>
                                                            </li>
                                                        
                                                        @endcan

                                                        @can('webinar_show')
                                                        
                                                            @if($endDateTime > $now)
                                                                <li>
                                                                    <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$webinar->id}})" title="View">
                                                                        <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M19.8507 7.3175C19.1191 5.7175 16.2499 0.5 9.99989 0.5C3.74989 0.5 0.880726 5.7175 0.149059 7.3175C0.0508413 7.53192 0 7.76499 0 8.00083C0 8.23668 0.0508413 8.46975 0.149059 8.68417C0.880726 10.2825 3.74989 15.5 9.99989 15.5C16.2499 15.5 19.1191 10.2825 19.8507 8.6825C19.9487 8.46832 19.9995 8.23554 19.9995 8C19.9995 7.76446 19.9487 7.53168 19.8507 7.3175ZM9.99989 13.8333C4.74406 13.8333 2.29156 9.36167 1.66656 8.00917C2.29156 6.63833 4.74406 2.16667 9.99989 2.16667C15.2432 2.16667 17.6966 6.61917 18.3332 8C17.6966 9.38083 15.2432 13.8333 9.99989 13.8333Z" fill="#626263"/>
                                                                            <path d="M9.99968 3.8335C9.17559 3.8335 8.37001 4.07787 7.6848 4.53571C6.9996 4.99355 6.46554 5.64429 6.15018 6.40565C5.83481 7.16701 5.7523 8.00479 5.91307 8.81304C6.07384 9.62129 6.47068 10.3637 7.0534 10.9464C7.63612 11.5292 8.37855 11.926 9.1868 12.0868C9.99505 12.2475 10.8328 12.165 11.5942 11.8497C12.3555 11.5343 13.0063 11.0002 13.4641 10.315C13.922 9.62983 14.1663 8.82425 14.1663 8.00016C14.165 6.8955 13.7256 5.83646 12.9445 5.05535C12.1634 4.27423 11.1043 3.83482 9.99968 3.8335ZM9.99968 10.5002C9.50522 10.5002 9.02187 10.3535 8.61075 10.0788C8.19963 9.80413 7.8792 9.41369 7.68998 8.95687C7.50076 8.50006 7.45125 7.99739 7.54771 7.51244C7.64418 7.02748 7.88228 6.58203 8.23191 6.2324C8.58154 5.88276 9.027 5.64466 9.51195 5.5482C9.9969 5.45174 10.4996 5.50124 10.9564 5.69046C11.4132 5.87968 11.8036 6.20011 12.0784 6.61124C12.3531 7.02236 12.4997 7.50571 12.4997 8.00016C12.4997 8.6632 12.2363 9.29909 11.7674 9.76793C11.2986 10.2368 10.6627 10.5002 9.99968 10.5002Z" fill="#626263"/>
                                                                        </svg>
                                                                    </a>
                                                                </li>
                                                            @endif

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

                            {{ $allWebinar->links('vendor.pagination.custom-pagination') }}
                                
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
<link rel="stylesheet" href="{{ asset('admin/assets/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript">
    document.addEventListener('loadPlugins', function(event) {
        $('input[id="start_date"]').daterangepicker({
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
            @this.set('start_date', start.format('YYYY-MM-DD'));

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
    });


    document.addEventListener('webinarCounterEvent', function(event) {
        webinarCounter();
    });

    webinarCounter();

    function webinarCounter() {
        $(".webinar-item-active").each(function(index, element) {
            var totalSeconds = $(this).data('diff_in_seconds');

            const countdownInterval = setInterval(() => {
                if (totalSeconds <= 0) {
                    // Countdown has reached zero
                    clearInterval(countdownInterval);

                    $(this).find('.joinBtn').removeClass('d-none');

                    $(this).find('.webinar-time-system').remove();
                    $(this).find('.ongoingtag').removeClass('d-none');
                } else {

                    // Calculate days, hours, minutes, and seconds
                    var days = Math.floor(totalSeconds / (24 * 60 * 60));
                    var hours = Math.floor((totalSeconds % (24 * 60 * 60)) / (60 * 60));
                    var minutes = Math.floor((totalSeconds % (60 * 60)) / 60);
                    var seconds = totalSeconds % 60;

                    // Display the countdown
                    $(this).find('.counter-outer[data-label = "days"] b.counter').text(days);
                    $(this).find('.counter-outer[data-label = "hours"] b.counter').text(hours)
                    $(this).find('.counter-outer[data-label = "minutes"] b.counter').text(minutes)
                    $(this).find('.counter-outer[data-label = "seconds"] b.counter').text(seconds)

                    // Decrement totalSeconds by 1
                    totalSeconds--;
                }
            }, 1000);
        });
    }

    function copyToClipboard(link) {
        var textarea = document.createElement('textarea');
        textarea.value = link;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        // Change the button text to "Copied"
        var copyButton = document.getElementById('copyButton'); // Replace 'copyButton' with the actual button ID
        if (copyButton) {
            copyButton.textContent = 'Copied';
            copyButton.setAttribute('disabled', true); // Optionally disable the button after copying
        }

        // Reset the button text after a delay (e.g., 2 seconds)
        setTimeout(function() {
            copyButton.textContent = 'Copy';
            copyButton.removeAttribute('disabled');
        }, 2000);
    }

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