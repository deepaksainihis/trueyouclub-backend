<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="HIPL" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}} | @yield('title')</title>

    <link rel="icon" type="image/png" href="{{asset(config('constants.default.admin_favicon'))}}">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/style.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Nunito+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">



    @livewireStyles
  </head>
  <body>

    <!-- @include('partials.auth-header') -->

    @yield('content')
    
    <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js')}} "></script>
    <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}"></script> 


    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <x-livewire-alert::flash />

   <script type="text/javascript">
		$(document).on('click', '.toggle-password', function() {

		    $(this).toggleClass("eye-close");
		    
		    var input = $("#pass_log_id");
		    input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
		});
    $(document).on('click', '.toggle-password-1', function() {
      $(this).toggleClass("eye-close");

      var input = $("#conpass_log_id");
      input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
    });
   </script>

  </body>
</html>

