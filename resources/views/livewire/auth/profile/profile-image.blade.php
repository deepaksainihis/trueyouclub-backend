<div class="profile-image-change">
    <div wire:loading wire:target="profile_image" class="loader"></div>
    
    <div class="left-imag-col">
        @if ($profile_image)  
            <img src="{{ $profile_image->temporaryUrl() }}" alt="avatar" class="rounded-circle img-fluid">
        @else
            <img src="{{ ($authUser->profileImage()->first()) ? $authUser->profileImage()->first()->file_url : asset(config('constants.default.profile_image')) }}" alt="avatar" class="rounded-circle img-fluid">
        @endif   
    </div>

    <div class="right-content-profile">
        <!-- <h5 class="my-3">{{ ucfirst($authUser->name) }}</h5> -->
        <p class="text-muted mb-1">{{ $authUser->my_referral_code ?? ''  }}</p>
        <p class="text-muted mb-1">{{ $authUser->profile->profession ?? ''  }}</p>
        <div class="d-flex mb-2 profile-image-update">

            @if($showConfirmCancel)
                <button class="btn btn-outline-success btn-primary ms-1 mr-1" wire:key="action-{{generateRandomString(5)}}" wire:click.prevent="$emitSelf('confirmUpdateProfileImage')"><i class="fa fa-check"></i></button>

                <button class="btn btn-outline-danger btn-primary ms-1" wire:key="action-{{generateRandomString(5)}}"  wire:click.prevent="$emitSelf('cancelUpdateProfileImage')"><i class="fa fa-close"></i></button>
            @else
                <input id="profile-image-upload" wire:model.defer="profile_image" class="d-none" type="file" wire:change="validateProfileImage" accept="image/*">
                <button type="button" class="btn btn-primary ms-1" onclick="document.getElementById('profile-image-upload').click();" >Edit Profile Picture</button>
            @endif
        </div>
    </div>
</div>