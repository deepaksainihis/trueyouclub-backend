<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="{{ route('admin.dashboard') }}">
            @if(getSetting('site_logo'))
            <img src="{{ getSetting('site_logo') }}" class="mr-2" alt="logo"/>
            @else
            <img src="{{ asset(config('constants.default.logo')) }}" class="mr-2"  alt="logo"/>
            @endif
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}">
            @if(getSetting('short_logo'))
            <img src="{{ getSetting('short_logo') }}" alt="logo-mini"/>
            @else
            <img src="{{ asset(config('constants.default.short_logo')) }}" alt="logo-mini"/>
            @endif
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="icon-menu"></span>
    </button>
    <!-- <ul class="navbar-nav mr-lg-2">
        <li class="nav-item nav-search d-none d-lg-block">
        <div class="input-group">
            <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
            <span class="input-group-text" id="search">
                <i class="icon-search"></i>
            </span>
            </div>
            <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
        </div>
        </li>
    </ul> -->
    <ul class="navbar-nav navbar-nav-right">
        <!-- <li class="nav-item dropdown">
        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
            <i class="icon-bell mx-0"></i>
            <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
            <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
            <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
                <div class="preview-icon bg-success">
                <i class="ti-info-alt mx-0"></i>
                </div>
            </div>
            <div class="preview-item-content">
                <h6 class="preview-subject font-weight-normal">Application Error</h6>
                <p class="font-weight-light small-text mb-0 text-muted">
                Just now
                </p>
            </div>
            </a>
            <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
                <div class="preview-icon bg-warning">
                <i class="ti-settings mx-0"></i>
                </div>
            </div>
            <div class="preview-item-content">
                <h6 class="preview-subject font-weight-normal">Settings</h6>
                <p class="font-weight-light small-text mb-0 text-muted">
                Private message
                </p>
            </div>
            </a>
            <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
                <div class="preview-icon bg-info">
                <i class="ti-user mx-0"></i>
                </div>
            </div>
            <div class="preview-item-content">
                <h6 class="preview-subject font-weight-normal">New user registration</h6>
                <p class="font-weight-light small-text mb-0 text-muted">
                2 days ago
                </p>
            </div>
            </a>
        </div>
        </li> -->
        <li class="nav-item nav-profile dropdown">
            <div class="dropdown user-dropdown">
                <button class="btn dropdown-toggle ms-auto" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="dropdown-data">
                        <div class="img-user refresh-profile-image"><img src="{{ isset(auth()->user()->profile_image_url) && !empty(auth()->user()->profile_image_url) ? auth()->user()->profile_image_url : asset(config('constants.default.profile_image')) }}"  class="img-fluid" alt=""></div>
                        <div class="welcome-user">
                            <span class="welcome">welcome</span>
                            <span class="user-name-title auth-user-name">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                    <span class="arrow-icon">
                        <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.002 7L7.00195 0.999999L1.00195 7" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <ul class="list-unstyled mb-0">
                        <li><a class="dropdown-item" href="{{route('auth.admin-profile')}}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.16 10.87C12.06 10.86 11.94 10.86 11.83 10.87C9.45 10.79 7.56 8.84 7.56 6.44C7.56 3.99 9.54 2 12 2C14.45 2 16.44 3.99 16.44 6.44C16.43 8.84 14.54 10.79 12.16 10.87Z" stroke="#0A2540" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.16 14.56C4.74 16.18 4.74 18.82 7.16 20.43C9.91 22.27 14.42 22.27 17.17 20.43C19.59 18.81 19.59 16.17 17.17 14.56C14.43 12.73 9.92 12.73 7.16 14.56Z" stroke="#0A2540" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg> My Profile</a>
                        </li>
                        <!-- <li><a class="dropdown-item" href="#"><img src="{{ asset('admin/images/booksaved.svg') }}" class="img-fluid">My Buyers Data</a></li>
                        <li><a class="dropdown-item" href="#"><img src="{{ asset('admin/images/messages.svg') }}" class="img-fluid">Support</a></li> -->
                        @livewire('auth.admin.logout',['type'=>'navbar'])
                    </ul>
                </div>
            </div>
        </li>
        <!-- <li class="nav-item nav-settings d-none d-lg-flex">
        <a class="nav-link" href="#">
            <i class="icon-ellipsis"></i>
        </a>
        </li> -->
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
        <span class="icon-menu"></span>
    </button>
    </div>
</nav>
