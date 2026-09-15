<div class="login-header">
     <div class="container">
      <div class="row justify-content-between align-items-center">
        <div class="col-md-6 col-6">
        <div class="login-logo">
            <a href="{{ url('/') }}">
                <img src="{{asset(config('constants.default.logo'))}}" alt="login logo">
            </a>
        </div>
        </div>
        <div class="col-md-6 col-6">
        <div class="go-back d-flex justify-content-end">
            <a href="{{ request()->is('/login') ? url('/') : route('auth.login') }}">
            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.9993 6.5H0.999268" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5.89927 11.5L0.999268 6.5L5.89927 1.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back
            </a>
        </div>
        </div>
      </div>
      </div>
   </div>