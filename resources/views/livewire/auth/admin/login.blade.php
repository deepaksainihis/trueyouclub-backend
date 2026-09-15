<div>
    <!-- LOGIN START -->
    <section class="account-block">
        <div class="container-fluid p-0">
            <div class="account-session">
                <div class="session-img">
                    <img src="{{  asset('images/login-screen.svg') }}" class="img-fluid" alt="" />
                </div>
                <div class="row align-items-center g-0 h-100vh">
                    <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center">
                        <div class="account-in">
                            <div class="center-content">
                                
                                @if(getSetting('site_logo'))
                                <img src="{{ getSetting('site_logo') }}" class="img-fluid" alt="logo"/>
                                @else
                                <img src="{{ asset(config('constants.default.logo')) }}" class="img-fluid" alt="logo"/>
                                @endif

                                <h2> {{ __('auth.login.welcome_back') }}</h2>
                            </div>
                            <form wire:submit.prevent="submitLogin" class="form">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <div class="form-group">
                                            <label>{{ __('auth.login.email') }}</label>
                                            <div class="form-group-inner">
                                                <span class="form-icon"><img src="{{ asset('images/icons/email.svg') }}" class="img-fluid" alt="" /></span>
                                                <input type="email" class="form-control"  wire:model.defer="email" placeholder="{{ __('auth.login.email_placeholder') }}" />
                                            </div>
                                            @error('email') <span class="error text-danger ">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-12">
                                        <div class="form-group">
                                            <label>{{ __('auth.login.password') }}</label>
                                            <div class="form-group-inner">
                                                <span class="form-icon"><img src="{{ asset('images/icons/password.svg') }}" class="img-fluid" alt="" /></span>
                                                <input id="pass_log_id" type="password" class="form-control" placeholder="{{ __('auth.login.password_placeholder') }}" wire:model.defer="password" autocomplete="off"/>
                                                <span toggle="#password-field" class="form-icon-password toggle-password eye-close"><img src="{{ asset('images/icons/eye.svg') }}" class="img-fluid" alt="" /></span>
                                            </div>
                                            @error('password') <span class="error text-danger">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group-switch">
                                            <label class="switch">
                                                <input type="checkbox" id="checkbox" wire:model.defer="remember_me" />
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="toggle-heading">{{ __('auth.login.remember_me') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                        <a href="{{ route('auth.forget-password') }}" class="link-pass"> {{ __('auth.login.forgot_password') }} </a>
                                    </div>
                                    <div class="col-12 col-lg-12">
                                        <div class="form-group-btn">
                                            <button type="submit"  wire:loading.attr="disabled" class="btn btn-fill">
                                                {{ __('auth.login.login_btn') }}
                                                <span wire:loading wire:target="submitLogin">
                                                    <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- LOGIN END -->
</div>