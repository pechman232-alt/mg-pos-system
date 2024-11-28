<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <script src="{{ asset('backend/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/helpers.js') }}"></script>

    <script src="{{ url('/jquery/jquery.min.js') }}"></script>
    <link href="{{ asset('/datatable/datatables.min.css') }}" rel="stylesheet">
    <script src="{{ url('/datatable/datatables.min.js') }}"></script>

    <link rel="shortcut icon" href="{{ asset('/assets/images/logo.png') }}">

    {{-- select2 --}}
    <link href="{{ asset('/assets/select2/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ url('/assets/select2/js/select2.min.js') }}"></script>
    {{-- select2 --}}

    <title>Home | TF Motors</title>

</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.navbar')
            <div class="layout-page">
                @include('layouts.header')
                <div class="content-wrapper">
                    {{-- <select class="js-example-basic-single" name="state">
                        <option value="AL">Alabama</option>
                        <option value="WY">Wyoming</option>
                        <option value="WY">Wyoming</option>
                        <option value="WY">Hello</option>
                      </select> --}}

                    @yield('content')

                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>

    <script>
        $('.js-example-basic-single').select2();

        // on change
        $('.js-example-basic-single').on('select2:open', function() {
        let searchBox = document.querySelector('.select2-search__field');
        if (searchBox) {
            searchBox.addEventListener('input', function(event) {
                console.log('Search query:', event.target.value);
                // Add your logic here for each search input change
            });
        }
    });

    // on selected
    $('.js-example-basic-single').on('change', function() {
        let selectedOptions = $(this).val();
        console.log('Selected options:', selectedOptions);
    });
    </script>

    {{---------------------------- Style Scrip --------------------------------}}
    <script src="{{ asset('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    <script src="{{ asset('backend/assets/js/dashboards-analytics.js') }}"></script>

    <script async defer src="{{ url('/jquery/buttons.js') }}"></script>
    {{---------------------------- Style Scrip --------------------------------}}

</body>
</html>
