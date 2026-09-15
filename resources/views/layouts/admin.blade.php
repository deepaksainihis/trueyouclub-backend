<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | @yield('title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="HIPL" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset(config('constants.default.admin_favicon')) }}" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.css" integrity="sha512-EzrsULyNzUc4xnMaqTrB4EpGvudqpetxG/WNjCpG6ZyyAGxeB6OBF9o246+mwx3l/9Cn838iLIcrxpPHTiygAA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="'https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Nunito+Sans:wght@400;500;600;700;800&display=swap" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.js" integrity="sha512-bAjB1exAvX02w2izu+Oy4J96kEr1WOkG6nRRlCtOSQ0XujDtmAstq5ytbeIxZKuT9G+KzBmNq5d23D6bkGo8Kg==" crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.9.2/tailwind.min.css" integrity="sha512-l7qZAq1JcXdHei6h2z8h8sMe3NbMrmowhOl+QkP3UhifPpCW2MC4M0i26Y8wYpbz1xD9t61MLT9L1N773dzlOA==" crossorigin="anonymous" />
    
    @include('partials.admin.css')    
 
    @livewireStyles

    @stack('styles')
</head>
<body>
    <div class="container-scroller">
    
        @include('partials.admin.header')

        <div class="container-fluid page-body-wrapper">

            @include('partials.admin.admin_sidebar')      

            <div class="main-panel">
                <!-- content-wrapper start -->
                    @yield('content')
                <!-- content-wrapper ends -->

                <footer class="footer">            
                    <span class="copyright-text d-block text-center">© {{ date('Y') }} All Copyrights Reserved By {{ config('constants.app_name') }}</span>
                </footer>
            </div>            
            <!-- main-panel ends -->            
        </div>
       
        <!-- page-body-wrapper ends -->
        
    </div>
    <!-- container-scroller -->
  
    @include('partials.admin.js')
   
    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}"></script> 

    <x-livewire-alert::flash />
    
    @stack('scripts')


</body>
</html>
<!-- new -->

