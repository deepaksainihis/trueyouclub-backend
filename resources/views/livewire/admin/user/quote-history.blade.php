<div>
    <div class="card-title top-box-set">
        <div>
            <h4 class="card-title-heading">{{ ucwords($userName) }} Quote History </h4>
            <div class="count-history">
                <div class="green-box">
                    Total Number of Attendance Days: <span>{{ $totalAttendence }}</span>
                </div> 
                <div class="red-box">
                    Total Number of Skipped Days:  <span>{{ $totalSkipped }}</span>
                </div>
            </div>
        </div>
        <button wire:click.prevent="cancel" class="btn btn-secondary mt-4">
            {{ __('global.back')}}
            <span wire:loading wire:target="cancel">
                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
            </span>
        </button>
    </div>
    <div class="table-responsive search-table-data">
    <div class="relative">

    @include('admin.partials.table-show-entries-search-box',['searchBoxPlaceholder'=>$searchBoxPlaceholder])
       
    <div class="quotes-listing quotes-history">
        @if($quotesHistory->count() > 0)
        <ul>
            @foreach($quotesHistory as $serialNo => $quote)
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
                    <span>
                        {{ucwords($quote->users()->where('id',$userId)->first()->pivot->status)}}
                    </span>
                    <span class="quotes-date">
                        <svg width="14" height="15" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.66683 2.33337H2.25016C1.7439 2.33337 1.3335 2.74378 1.3335 3.25004V9.66671C1.3335 10.173 1.7439 10.5834 2.25016 10.5834H8.66683C9.17309 10.5834 9.5835 10.173 9.5835 9.66671V3.25004C9.5835 2.74378 9.17309 2.33337 8.66683 2.33337Z" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.2915 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M3.62549 1.41675V3.25008" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M1.3335 5.08337H9.5835" stroke="#878787" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        {{ convertDateTimeFormat($quote->users[0]->pivot->created_at,'fulldatetime') }}
                    </span>
                    <!-- <div class="update-webinar">
                        <ul class="d-flex">
                            
                        </ul>
                    </div> -->
                </div>
            </li>
            @endforeach
        </ul>
        @else
            @include('admin.partials.no-record-found')
        @endif
    </div>
    {{ $quotesHistory->links('vendor.pagination.custom-pagination') }}
</div>
</div>

</div>