<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home | TF Motors</title>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/demo.css') }}" />
    
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('/datatable/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/select2/css/select2.min.css') }}" />

    <link rel="shortcut icon" href="{{ asset('/assets/images/logo.png') }}">

    <script src="{{ asset('backend/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('backend/assets/js/config.js') }}"></script> 

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    @include('Helps.toast')

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.navbar')
            <div class="layout-page">
                @include('layouts.header')
                <div class="content-wrapper">
                    @yield('content')
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>

    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>

    <script src="{{ url('/datatable/datatables.min.js') }}"></script>
    <script src="{{ url('/assets/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script src="{{ asset('backend/assets/js/main.js') }}"></script>

    {{-- <script src="{{ asset('backend/assets/js/dashboards-analytics.js') }}"></script> --}}

    @stack('scripts') </body>
</html>