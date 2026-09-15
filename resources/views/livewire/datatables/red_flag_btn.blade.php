@if($flag_count > 0)
<button style="cursor:pointer;" wire:click="$emitUp('redFlagView', {{ $id }})" class="seller_flg_mark  {{ (isset($type) && !empty($type) && $type == 'icon-counter') ? 'btn btn-twitter' : ''}}  {{ isset($title) && !empty($title) ? 'btn btn-twitter px-4' : ''}}" >
    <div class="row">
        <img src="{{ asset('images/icons/red-flag.svg') }}" title="{{ $flag_count }} seller report on this buyer" /> {!! isset($title) && !empty($title) ? "<span class='ml-2'>".$title."</span>" : '' !!} 
    </div>    
</button>
    @if(isset($type) && !empty($type) && $type == 'icon-counter')
        <span class="badge badge-dark badge-counter">{{ $flag_count }}</span>
    @endif
@endif