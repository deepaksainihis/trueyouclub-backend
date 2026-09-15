<div>
    <div class="relative">

    @include('admin.partials.table-show-entries-search-box',['searchBoxPlaceholder'=>$searchBoxPlaceholder])
       
    <div class="quotes-listing">
        @if($allQuote->count() > 0)
        <ul>
            @foreach($allQuote as $serialNo => $quote)
            <li>
                <div class="webinar-item">
                    <p class="quotes-content">
                        {!! nl2br(e($quote->message)) !!}
                    </p>
                    
                    @php
                        $quotePercentage = 0;
                        $totalUsers = getTotalUsers();
                        $quotePercentage = ($quote->users()->count() / (int)$totalUsers) * 100;
                    @endphp
                    
                    <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{$quotePercentage}}%">{{ round($quotePercentage) }}%</div>
                    </div>
                    <span class="quotes-date">
                        <svg width="14" height="15" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.66683 2.33337H2.25016C1.7439 2.33337 1.3335 2.74378 1.3335 3.25004V9.66671C1.3335 10.173 1.7439 10.5834 2.25016 10.5834H8.66683C9.17309 10.5834 9.5835 10.173 9.5835 9.66671V3.25004C9.5835 2.74378 9.17309 2.33337 8.66683 2.33337Z" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.2915 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M3.62549 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M1.3335 5.08337H9.5835" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        {{ convertDateTimeFormat($quote->created_at,'fulldate') }}
                    </span>
                    <div class="update-webinar">
                        {{-- <ul class="d-flex">
                            @can('quote_delete')
                            <li>
                                <a href="javascript:void()" wire:click.prevent="$emitUp('delete', {{$quote->id}})" title="Delete">
                                    <svg width="18" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.3337 3.33333H13.167V1.66667C13.167 1.22464 12.9914 0.800716 12.6788 0.488155C12.3663 0.175595 11.9424 0 11.5003 0L6.50033 0C6.0583 0 5.63437 0.175595 5.32181 0.488155C5.00925 0.800716 4.83366 1.22464 4.83366 1.66667V3.33333H0.666992V5H2.33366V17.5C2.33366 18.163 2.59705 18.7989 3.06589 19.2678C3.53473 19.7366 4.17062 20 4.83366 20H13.167C13.83 20 14.4659 19.7366 14.9348 19.2678C15.4036 18.7989 15.667 18.163 15.667 17.5V5H17.3337V3.33333ZM6.50033 1.66667H11.5003V3.33333H6.50033V1.66667ZM14.0003 17.5C14.0003 17.721 13.9125 17.933 13.7562 18.0893C13.6 18.2455 13.388 18.3333 13.167 18.3333H4.83366C4.61265 18.3333 4.40068 18.2455 4.2444 18.0893C4.08812 17.933 4.00033 17.721 4.00033 17.5V5H14.0003V17.5Z" fill="#626263"/>
                                        <path d="M8.16667 8.3335H6.5V15.0002H8.16667V8.3335Z" fill="#626263"/>
                                        <path d="M11.5007 8.3335H9.83398V15.0002H11.5007V8.3335Z" fill="#626263"/>
                                    </svg>
                                </a>
                            </li>
                            @endcan
                        </ul> --}}
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
            @include('admin.partials.no-record-found')
        @endif
    </div>
    {{ $allQuote->links('vendor.pagination.custom-pagination') }}
</div>

</div>