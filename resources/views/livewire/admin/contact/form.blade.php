<h4 class="card-title">
    Reply To {{$fullname}}
</h4>


<form class="forms-sample">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0" wire:ignore>
                <label class="font-weight-bold justify-content-start">{{ __('cruds.contacts.fields.email')}} : {{$email}}</label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="form-group mb-0" wire:ignore>
                <label class="font-weight-bold justify-content-start">{{ __('cruds.contacts.fields.message')}}</label>
                <textarea class="form-control" id="summernote" wire:model.defer="reply" rows="4">{{$reply}}</textarea>
            </div>
            @error('reply') <span class="error text-danger">{{ $message }}</span>@enderror
        </div>
    </div>


    <button wire:click.prevent="cancel" class="btn btn-secondary">
        {{ __('global.back')}}
        <span wire:loading wire:target="cancel">
            <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
        </span>
    </button>

    <button wire:click="draftReply({{$reply_id}})" type="submit" wire:loading.attr="disabled" class="btn btn-primary mr-2">
        {{-- $updateMode ? __('global.update') : __('global.submit') --}}
        Draft
    </button>

    <button wire:click="sendReply({{$reply_id}})" type="submit" wire:loading.attr="disabled" class="btn btn-primary mr-2">
        {{-- $updateMode ? __('global.update') : __('global.submit') --}}
        Send
    </button>
</form>