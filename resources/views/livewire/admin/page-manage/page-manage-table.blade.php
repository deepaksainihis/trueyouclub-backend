<div class="relative">

    <!-- Show entries & Search box -->
    @include('admin.partials.table-show-entries-search-box')
    <!-- End Show entries & Search box -->

    <div class="table-responsive mt-3 my-team-details table-record">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-gray-500 text-xs font-medium">{{ trans('global.sno') }}</th>

                    <th class="text-gray-500 text-xs">
                        {{ __('cruds.pages.fields.page_name')}}

                        <span wire:click="sortBy('page_name')" class="float-right text-sm" style="cursor: pointer;">
                            <i class="fa fa-arrow-up {{ $sortColumnName === 'created_at' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                            <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'created_at' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                        </span>
                    </th>

                    <th class="text-gray-500 text-xs">
                        {{ __('cruds.pages.fields.title')}}
                    </th>

                    <th class="text-gray-500 text-xs">@lang('global.created')
                        <span wire:click="sortBy('created_at')" class="float-right text-sm" style="cursor: pointer;">
                            <i class="fa fa-arrow-up {{ $sortColumnName === 'created_at' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                            <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'created_at' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                        </span>
                    </th>

                    {{-- <th class="text-gray-500 text-xs">
                        @lang('global.status')
                        <span wire:click="sortBy('status')" class="float-right text-sm" style="cursor: pointer;">
                            <i class="fa fa-arrow-up {{ $sortColumnName === 'status' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                    <i class="fa fa-arrow-down m-0 {{ $sortColumnName === 'status' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                    </span>
                    </th> --}}



                    <th class="text-gray-500 text-xs">@lang('global.action')</th>
                </tr>
            </thead>
            <tbody>
                @if($allPages->count() > 0)
                @foreach($allPages as $serialNo => $page)
                <tr>
                    <td>{{ $serialNo+1 }}</td>
                    <td>{{ ucwords($page->page_name) }}</td>
                    <td>{{ ucwords($page->title) }}</td>

                    <td>{{ convertDateTimeFormat($page->created_at,'fulldate') }}</td>
                    {{-- <td>
                        <label class="toggle-switch">
                            <input type="checkbox" class="toggleSwitch" wire:click.prevent="$emitUp('toggle',{{$page->id}})" {{ $page->status == 1 ? 'checked' : '' }}>
                    <div class="switch-slider round"></div>
                    </label>
                    </td> --}}

                    <td>
                        <div class="update-webinar table-btns">
                            <ul class="d-flex">
                                @can('page_show')
                                <li>
                                    <a href="javascript:void()" wire:click.prevent="$emitUp('show', {{$page->id}})" title="View">
                                        <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.8507 7.3175C19.1191 5.7175 16.2499 0.5 9.99989 0.5C3.74989 0.5 0.880726 5.7175 0.149059 7.3175C0.0508413 7.53192 0 7.76499 0 8.00083C0 8.23668 0.0508413 8.46975 0.149059 8.68417C0.880726 10.2825 3.74989 15.5 9.99989 15.5C16.2499 15.5 19.1191 10.2825 19.8507 8.6825C19.9487 8.46832 19.9995 8.23554 19.9995 8C19.9995 7.76446 19.9487 7.53168 19.8507 7.3175ZM9.99989 13.8333C4.74406 13.8333 2.29156 9.36167 1.66656 8.00917C2.29156 6.63833 4.74406 2.16667 9.99989 2.16667C15.2432 2.16667 17.6966 6.61917 18.3332 8C17.6966 9.38083 15.2432 13.8333 9.99989 13.8333Z" fill="#626263"/>
                                            <path d="M9.99968 3.8335C9.17559 3.8335 8.37001 4.07787 7.6848 4.53571C6.9996 4.99355 6.46554 5.64429 6.15018 6.40565C5.83481 7.16701 5.7523 8.00479 5.91307 8.81304C6.07384 9.62129 6.47068 10.3637 7.0534 10.9464C7.63612 11.5292 8.37855 11.926 9.1868 12.0868C9.99505 12.2475 10.8328 12.165 11.5942 11.8497C12.3555 11.5343 13.0063 11.0002 13.4641 10.315C13.922 9.62983 14.1663 8.82425 14.1663 8.00016C14.165 6.8955 13.7256 5.83646 12.9445 5.05535C12.1634 4.27423 11.1043 3.83482 9.99968 3.8335ZM9.99968 10.5002C9.50522 10.5002 9.02187 10.3535 8.61075 10.0788C8.19963 9.80413 7.8792 9.41369 7.68998 8.95687C7.50076 8.50006 7.45125 7.99739 7.54771 7.51244C7.64418 7.02748 7.88228 6.58203 8.23191 6.2324C8.58154 5.88276 9.027 5.64466 9.51195 5.5482C9.9969 5.45174 10.4996 5.50124 10.9564 5.69046C11.4132 5.87968 11.8036 6.20011 12.0784 6.61124C12.3531 7.02236 12.4997 7.50571 12.4997 8.00016C12.4997 8.6632 12.2363 9.29909 11.7674 9.76793C11.2986 10.2368 10.6627 10.5002 9.99968 10.5002Z" fill="#626263"/>
                                        </svg>
                                    </a>
                                </li>
                                @endcan

                                @can('page_edit')
                                <li>
                                    <a href="javascript:void()" wire:click.prevent="$emitUp('edit', {{$page->id}})" title="Edit">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.5003 18.9583H7.50033C2.97533 18.9583 1.04199 17.025 1.04199 12.5V7.49996C1.04199 2.97496 2.97533 1.04163 7.50033 1.04163H9.16699C9.50866 1.04163 9.79199 1.32496 9.79199 1.66663C9.79199 2.00829 9.50866 2.29163 9.16699 2.29163H7.50033C3.65866 2.29163 2.29199 3.65829 2.29199 7.49996V12.5C2.29199 16.3416 3.65866 17.7083 7.50033 17.7083H12.5003C16.342 17.7083 17.7087 16.3416 17.7087 12.5V10.8333C17.7087 10.4916 17.992 10.2083 18.3337 10.2083C18.6753 10.2083 18.9587 10.4916 18.9587 10.8333V12.5C18.9587 17.025 17.0253 18.9583 12.5003 18.9583Z" fill="#40658B"></path>
                                            <path d="M7.08311 14.7417C6.57478 14.7417 6.10811 14.5584 5.76645 14.225C5.35811 13.8167 5.18311 13.225 5.27478 12.6L5.63311 10.0917C5.69978 9.60838 6.01645 8.98338 6.35811 8.64172L12.9248 2.07505C14.5831 0.416716 16.2664 0.416716 17.9248 2.07505C18.8331 2.98338 19.2414 3.90838 19.1581 4.83338C19.0831 5.58338 18.6831 6.31672 17.9248 7.06672L11.3581 13.6334C11.0164 13.975 10.3914 14.2917 9.90811 14.3584L7.39978 14.7167C7.29145 14.7417 7.18311 14.7417 7.08311 14.7417ZM13.8081 2.95838L7.24145 9.52505C7.08311 9.68338 6.89978 10.05 6.86645 10.2667L6.50811 12.775C6.47478 13.0167 6.52478 13.2167 6.64978 13.3417C6.77478 13.4667 6.97478 13.5167 7.21645 13.4834L9.72478 13.125C9.94145 13.0917 10.3164 12.9084 10.4664 12.75L17.0331 6.18338C17.5748 5.64172 17.8581 5.15838 17.8998 4.70838C17.9498 4.16672 17.6664 3.59172 17.0331 2.95005C15.6998 1.61672 14.7831 1.99172 13.8081 2.95838Z" fill="#40658B"></path>
                                            <path d="M16.5413 8.19173C16.483 8.19173 16.4246 8.1834 16.3746 8.16673C14.183 7.55006 12.4413 5.8084 11.8246 3.61673C11.733 3.2834 11.9246 2.94173 12.258 2.84173C12.5913 2.75006 12.933 2.94173 13.0246 3.27506C13.5246 5.05006 14.933 6.4584 16.708 6.9584C17.0413 7.05006 17.233 7.40006 17.1413 7.7334C17.0663 8.01673 16.8163 8.19173 16.5413 8.19173Z" fill="#40658B"></path>
                                        </svg>
                                    </a>
                                </li>

                                @if($page->sections()->where('status',1)->count() > 0)
                                <li>
                                    <a href="{{ route('admin.page-sections',$page->slug) }}"  title="Sections">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 0H0.714286C0.524845 0 0.343164 0.0752549 0.209209 0.209209C0.0752549 0.343164 0 0.524845 0 0.714286V19.2857C0 19.4752 0.0752549 19.6568 0.209209 19.7908C0.343164 19.9247 0.524845 20 0.714286 20H10C10.1894 20 10.3711 19.9247 10.5051 19.7908C10.639 19.6568 10.7143 19.4752 10.7143 19.2857V0.714286C10.7143 0.524845 10.639 0.343164 10.5051 0.209209C10.3711 0.0752549 10.1894 0 10 0ZM9.28571 18.5714H1.42857V1.42857H9.28571V18.5714ZM19.2857 0H12.8571C12.6677 0 12.486 0.0752549 12.3521 0.209209C12.2181 0.343164 12.1429 0.524845 12.1429 0.714286V8.57143C12.1429 8.76087 12.2181 8.94255 12.3521 9.0765C12.486 9.21046 12.6677 9.28571 12.8571 9.28571H19.2857C19.4752 9.28571 19.6568 9.21046 19.7908 9.0765C19.9247 8.94255 20 8.76087 20 8.57143V0.714286C20 0.524845 19.9247 0.343164 19.7908 0.209209C19.6568 0.0752549 19.4752 0 19.2857 0ZM18.5714 7.85714H13.5714V1.42857H18.5714V7.85714ZM19.2857 11.4286H12.8571C12.6677 11.4286 12.486 11.5038 12.3521 11.6378C12.2181 11.7717 12.1429 11.9534 12.1429 12.1429V19.2857C12.1429 19.4752 12.2181 19.6568 12.3521 19.7908C12.486 19.9247 12.6677 20 12.8571 20H19.2857C19.4752 20 19.6568 19.9247 19.7908 19.7908C19.9247 19.6568 20 19.4752 20 19.2857V12.1429C20 11.9534 19.9247 11.7717 19.7908 11.6378C19.6568 11.5038 19.4752 11.4286 19.2857 11.4286ZM18.5714 18.5714H13.5714V12.8571H18.5714V18.5714Z" fill="#626263"/>
                                        </svg>
                                    </a>
                                </li>
                                @endif
                                
                                @endcan
                            </ul>
                        </div>

                        <!-- <button title="Show" type="button" wire:click.prevent="$emitUp('show', {{$page->id}})" class="btn btn-info view-btn btn-rounded btn-icon">
                            <i class="ti-eye"></i>
                        </button> -->


                        <!-- <button title="Edit" type="button" wire:click.prevent="$emitUp('edit', {{$page->id}})" class="btn btn-info btn-rounded btn-icon">
                            <i class="ti-pencil"></i>
                        </button> -->


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
    {{ $allPages->links('vendor.pagination.custom-pagination') }}

</div>