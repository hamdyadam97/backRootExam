@php
    $version = 20220817;
    $nameUrl = Request::segment(1);
    $baseUrl = asset('backend') . '/';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>{{ config('app.name', 'Web') }}</title>
    <link href="" rel="shortcut icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="SemiColonWeb" />
    <meta name="robots" content="noindex" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href="{{ $baseUrl }}bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ $baseUrl }}bootstrap-extension/css/bootstrap-extension.css" rel="stylesheet">
    <link href="{{ $baseUrl }}css/animate.css" rel="stylesheet">
    <link href="{{ $baseUrl }}css/style.css" rel="stylesheet">
    <link href="{{ $baseUrl }}css/colors/blue.css" rel="stylesheet">
    <!-- DateTime Picker CSS -->
    <link href="{{ $baseUrl }}css/bootstrap-datetimepicker.css" id="theme" rel="stylesheet">
    <!-- other CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

    <link href="{{ $baseUrl }}css/toastr.min.css?{{ $version }}" rel="stylesheet">
    <link href="{{ $baseUrl }}css/jquery.dataTables.min.css">
    <link href="{{ $baseUrl }}css/custom.css?{{ $version }}" rel="stylesheet">
</head>

<body data-sidebar="dark" data-layout-mode="light">
    <div id="app" class="login-register">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ $baseUrl }}js/jquery_v3_6_0_full.js?{{ $version }}"></script>     
    <script src="{{ asset('/') }}assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/node-waves/waves.min.js"></script>

    <!-- App js -->
     <script src="{{ asset('/') }}assets/js/app.js"></script>

    <script src="{{ $baseUrl }}js/bootstrap-datetimepicker.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script src="{{ $baseUrl }}js/toastr.min.js?{{ $version }}"></script>
    <script src="{{ $baseUrl }}js/input-mask.js?{{ $version }}"></script>
    <script src="{{ $baseUrl }}js/custom_js.js?{{ $version }}"></script>


    <script type="text/javascript">
        var siteUrl = "{{ asset('/') }}";
        var baseUrl = "{{ asset('/') }}{{ Request::segment(1) }}/";
        var nameUrl = "{{ Request::segment(2) }}";
        var _token = "{{ csrf_token() }}";
    </script>

    @if (Session::has('status'))
        <script type="text/javascript">
            showMessage("success", "{{ Session::get('status') }}");
        </script>
        @php Session::forget('status') @endphp
    @endif
    @if (Session::has('success'))
        <script type="text/javascript">
            showMessage("success", "{{ Session::get('success') }}");
        </script>
        @php Session::forget('success') @endphp
    @endif
    @if (Session::has('error'))
        <script type="text/javascript">
            showMessage("error", "{{ Session::get('error') }}");
        </script>
        @php Session::forget('error') @endphp
    @endif
    @if (Session::has('warning'))
        <script type="text/javascript">
            showMessage("warning", "{{ Session::get('warning') }}");
        </script>
        @php Session::forget('warning') @endphp
    @endif

    @yield('js')
    @yield('pagejs')

</body>

</html>
