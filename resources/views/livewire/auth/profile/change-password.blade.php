<form wire:submit.prevent="updatePassword">
    <div class="card-block">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="form-group">
                    <label>{{__('global.current_password') }}</label>
                    <div class="input-set">
                        <span class="icon-left"><img src="{{ asset('admin/images/password.svg') }}" alt="Img"></span>
                        <input type="password" class="form-control" wire:model.defer="current_password" id="currentpass_log_id" autocomplete="off" placeholder="{{__('global.current_password') }}">
                        <span toggle="#password-field" class="form-icon-password toggle-password"><img src="{{ asset('admin/images/eye.svg') }}" class="img-fluid" alt=""></span>
                        @error('current_password') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12">
                <div class="form-group">
                    <label>{{__('global.new_password')}}</label>
                    <div class="input-set">
                        <span class="icon-left"><img src="{{ asset('admin/images/password.svg') }}" alt="Img"></span>
                        <input  type="password" wire:model.defer="password" class="form-control" id="newpass_log_id" autocomplete="off" placeholder="{{__('global.new_password')}}">
                        <span toggle="#password-field" class="form-icon-password toggle-password1"><img src="{{ asset('admin/images/eye.svg') }}" class="img-fluid" alt=""></span>
                        @error('password') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12">
                <div class="form-group">
                    <label>{{__('global.confirm_new_password')}}</label>
                    <div class="input-set">
                        <span class="icon-left"><img src="{{ asset('admin/images/password.svg') }}" alt="Img"></span>
                        <input  type="password" wire:model.defer="password_confirmation" class="form-control" id="connewpass_log_id" autocomplete="off" placeholder="{{__('global.confirm_new_password')}}">
                        <span toggle="#password-field" class="form-icon-password toggle-password2"><img src="{{ asset('admin/images/eye.svg') }}" class="img-fluid" alt=""></span>
                    @error('password_confirmation') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="submit-btn">
    <button type="submit" class="btn btn btn-fill btn-blue uppassword btn-secondary">{{__('global.update_password')}}
    <span wire:loading wire:target="updatePassword">
                        <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                    </span>
    </button>
</div>
    
</form>