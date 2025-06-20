<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('light-bootstrap/img/yg-logo.png') }}">
        <link rel="icon" type="image/png" href="{{ asset('light-bootstrap/img/yg-logo.png') }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>{{ $title }}</title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
        <!--     Fonts and icons     -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
        <!-- CSS Files -->
        <link href="{{ asset('light-bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('light-bootstrap/css/light-bootstrap-dashboard.css?v=2.0.0') }} " rel="stylesheet" />
        <link href="{{ asset('light-bootstrap/css/demo.css') }}" rel="stylesheet" />
        <style>
            body {
                background: linear-gradient(135deg, #f8fafc 0%, #e3e6ed 100%) !important;
                font-family: 'Montserrat', Arial, sans-serif;
                color: #2c3e50;
            }
            .main-panel {
                background: transparent !important;
            }
            .card {
                border-radius: 1.5rem !important;
                box-shadow: 0 4px 24px rgba(44,62,80,0.08) !important;
                border: none !important;
            }
            .card-header, .card-title {
                background: #fff !important;
                border-radius: 1.5rem 1.5rem 0 0 !important;
                border-bottom: none !important;
            }
            .navbar {
                background: transparent !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            .sidebar {
                background: #34495e !important;
                border-radius: 1.5rem 0 0 1.5rem !important;
                box-shadow: 2px 0 16px rgba(44,62,80,0.06) !important;
            }
            .sidebar .nav > li > a {
                color: #fff !important;
                font-weight: 500;
                border-radius: 1rem !important;
                margin: 0.25rem 0;
                transition: background 0.2s;
            }
            .sidebar .nav > li.active > a, .sidebar .nav > li > a:hover {
                background: #2c3e50 !important;
                color: #f1c40f !important;
            }
            .btn-primary, .btn-success {
                border-radius: 2rem !important;
                font-weight: 500;
                letter-spacing: 1px;
                box-shadow: 0 2px 8px rgba(44,62,80,0.08);
            }
            .btn-primary {
                background: #f1c40f !important;
                color: #2c3e50 !important;
                border: none !important;
            }
            .btn-success {
                background: #27ae60 !important;
                color: #fff !important;
                border: none !important;
            }
            .card-body {
                padding: 2rem 2rem 2rem 2rem !important;
            }
            .display-5, h1, h2, h3, h4, h5, h6 {
                font-family: 'Montserrat', Arial, sans-serif;
                font-weight: 700;
            }
            .lead {
                font-size: 1.15rem;
                color: #6c757d;
            }
            .table {
                border-radius: 1rem !important;
                overflow: hidden;
            }
        </style>
    </head>

    <body>
        <div class="wrapper @if (!auth()->check() || request()->route()->getName() == "") wrapper-full-page @endif">

            @if (auth()->check() && request()->route()->getName() != "")
                @include('layouts.navbars.sidebar')
                @include('pages/sidebarstyle')
            @endif

            <div class="@if (auth()->check() && request()->route()->getName() != "") main-panel @endif">
                @include('layouts.navbars.navbar')
                @yield('content')
                @include('layouts.footer.nav')
            </div>
        </div>
       


    </body>
        <!--   Core JS Files   -->
    <script src="{{ asset('light-bootstrap/js/core/jquery.3.2.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('light-bootstrap/js/core/popper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('light-bootstrap/js/core/bootstrap.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('light-bootstrap/js/plugins/jquery.sharrre.js') }}"></script>
    <!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
    <script src="{{ asset('light-bootstrap/js/plugins/bootstrap-switch.js') }}"></script>
    <!--  Google Maps Plugin    -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
    <!--  Chartist Plugin  -->
    <script src="{{ asset('light-bootstrap/js/plugins/chartist.min.js') }}"></script>
    <!--  Notifications Plugin    -->
    <script src="{{ asset('light-bootstrap/js/plugins/bootstrap-notify.js') }}"></script>
    <!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
    <script src="{{ asset('light-bootstrap/js/light-bootstrap-dashboard.js?v=2.0.0') }}" type="text/javascript"></script>
    <!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
    <script src="{{ asset('light-bootstrap/js/demo.js') }}"></script>
    @stack('js')
    <script>
      $(document).ready(function () {
        
        $('#facebook').sharrre({
          share: {
            facebook: true
          },
          enableHover: false,
          enableTracking: false,
          enableCounter: false,
          click: function(api, options) {
            api.simulateClick();
            api.openPopup('facebook');
          },
          template: '<i class="fab fa-facebook-f"></i> Facebook',
          url: 'https://light-bootstrap-dashboard-laravel.creative-tim.com/login'
        });

        $('#google').sharrre({
          share: {
            googlePlus: true
          },
          enableCounter: false,
          enableHover: false,
          enableTracking: true,
          click: function(api, options) {
            api.simulateClick();
            api.openPopup('googlePlus');
          },
          template: '<i class="fab fa-google-plus"></i> Google',
          url: 'https://light-bootstrap-dashboard-laravel.creative-tim.com/login'
        });

        $('#twitter').sharrre({
          share: {
            twitter: true
          },
          enableHover: false,
          enableTracking: false,
          enableCounter: false,
          buttons: {
            twitter: {
              via: 'CreativeTim'
            }
          },
          click: function(api, options) {
            api.simulateClick();
            api.openPopup('twitter');
          },
          template: '<i class="fab fa-twitter"></i> Twitter',
          url: 'https://light-bootstrap-dashboard-laravel.creative-tim.com/login'
        });
      });
    </script>
</html>