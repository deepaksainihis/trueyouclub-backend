<div>
    <section class="account-block">
        <div class="container-fluid p-0">
            <div class="account-session">
                <a href="{{ route('auth.login') }}" class="back back-fix">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 6H1" stroke="#0A2540" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.9 11L1 6L5.9 1" stroke="#0A2540" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Back
                </a>
                <div class="row align-items-center g-0 h-100vh">
                    <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center">
                        <div class="account-in">
                            <div class="center-content">
                                @if(getSetting('site_logo'))
                                <img src="{{ getSetting('site_logo') }}" class="img-fluid" alt="logo"/>
                                @else
                                <img src="{{ asset(config('constants.default.logo')) }}" class="img-fluid" alt="logo"/>
                                @endif
                                <h2>{{ trans('global.reset_password') }}</h2>
                            </div>
                            <form wire:submit.prevent="submit"  class="form">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <div class="form-group">
                                            <label>password</label>
                                            <div class="form-group-inner">
                                                <span class="form-icon"><img src="{{ asset('images/icons/password.svg') }}" class="img-fluid" alt="" /></span>
                                                <input id="pass_log_id" type="password" wire:model.defer="password" class="form-control" placeholder="Enter Your Password" autocomplete="off"/>
                                                <span toggle="#password-field" class="form-icon-password toggle-password eye-close"><img src="{{ asset('images/icons/eye.svg') }}" class="img-fluid" alt="" /></span>
                                            </div>
                                            @error('password') <span class="error text-danger">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-12">
                                        <div class="form-group mb-0">
                                            <label>Confirm password</label>
                                            <div class="form-group-inner">
                                                <span class="form-icon"><img src="{{ asset('images/icons/password.svg') }}" class="img-fluid" alt="" /></span>
                                                <input id="conpass_log_id" type="password" wire:model.defer="password_confirmation" class="form-control" placeholder="Enter Your Confirm Password" />
                                                <span toggle="#password-field" class="form-icon-password toggle-password-1 eye-close"><img src="{{ asset('images/icons/eye.svg') }}" class="img-fluid" alt="" /></span>
                                            </div>
                                        </div>
                                        @error('password_confirmation') <span class="error text-danger">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-12 col-lg-12">
                                        <div class="form-group-btn mb-0">
                                            <button type="submit"  wire:loading.attr="disabled" class="btn btn-fill">
                                                Submit
                                                <span wire:loading wire:target="submit">
                                                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="session-img">
                            <img src="{{  asset('images/login-screen.svg') }}" class="img-fluid" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>