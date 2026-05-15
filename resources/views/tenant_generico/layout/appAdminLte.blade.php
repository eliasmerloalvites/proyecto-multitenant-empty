<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('titulo')</title>

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Font --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
        href="{{ asset_root('adminlte/plugins/fontawesome-free/css/all.min.css') }}">

    {{-- AdminLTE --}}
    <link rel="stylesheet"
        href="{{ asset_root('adminlte/dist/css/adminlte.min.css') }}">

    {{-- Plugins --}}
    <link rel="stylesheet"
        href="{{ asset_root('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <link rel="stylesheet"
        href="{{ asset_root('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

    <link rel="stylesheet"
        href="{{ asset_root('adminlte/plugins/toastr/toastr.min.css') }}">

    {{-- DataTables --}}
    <link rel="stylesheet"
        href="{{ asset_root('css/dataTables.bootstrap4.min.css') }}">

    <link rel="stylesheet"
        href="{{ asset_root('css/jquery.dataTables.min.css') }}">

    {{-- Daterangepicker --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{-- Crop Tool --}}
    <link rel="stylesheet"
        href="{{ asset_root('ijaboCropTool/ijaboCropTool.min.css') }}">
    <link href="{{ asset_root('plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset_root('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet">
    
    {{-- <link rel="stylesheet" href="{{ asset_root('csskael/bootstrap.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset_root('csskael/kael-light.css') }}">

    
    @yield('head')
</head>

<body class="hold-transition sidebar-mini   ">

    <div class="wrapper">

        {{-- Preloader --}}
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake"
                src="{{ asset_root('adminlte/dist/img/AdminLTELogo.png') }}"
                alt="AdminLTELogo"
                height="60"
                width="60">
        </div>

        {{-- Navbar --}}
        @include('tenant_generico.partials.navbar')

        {{-- Sidebar --}}
        @include('tenant_generico.partials.sidebar')

        {{-- Content --}}
        @include('tenant_generico.partials.container')

        {{-- Control Sidebar --}}
        @include('tenant_generico.partials.controlSidebar')

    </div>

    {{-- jQuery --}}
    <script src="{{ asset_root('adminlte/plugins/jquery/jquery.min.js') }}"></script>

    {{-- jQuery UI --}}
    <script src="{{ asset_root('adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <script src="{{ asset_root('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>

    {{-- Bootstrap --}}
    <script src="{{ asset_root('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- AdminLTE --}}
    <script src="{{ asset_root('adminlte/dist/js/adminlte.min.js') }}"></script>

    {{-- OverlayScrollbars --}}
    <script src="{{ asset_root('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

    {{-- SweetAlert2 --}}
    <script src="{{ asset_root('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    {{-- Toastr --}}
    <script src="{{ asset_root('adminlte/plugins/toastr/toastr.min.js') }}"></script>

    {{-- Moment --}}
    <script src="{{ asset_root('adminlte/plugins/moment/moment.min.js') }}"></script>

    {{-- Daterangepicker --}}
    <script src="{{ asset_root('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

    {{-- ChartJS --}}
    <script src="{{ asset_root('adminlte/plugins/chart.js/Chart.min.js') }}"></script>

    {{-- DataTables --}}
    <script src="{{ asset_root('js/jquery.dataTables.min.js') }}"></script>

    <script src="{{ asset_root('js/dataTables.bootstrap4.min.js') }}"></script>

    {{-- Highcharts --}}
    <script src="https://code.highcharts.com/highcharts.js"></script>

    {{-- Crop Tool --}}
    <script src="{{ asset_root('ijaboCropTool/ijaboCropTool.min.js') }}"></script>

    {{-- Custom --}}
    <script src="{{ asset_root('js/image-elias.js') }}"></script>

    @stack('scripts')

    @yield('script')

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            cargarImagenPerfil();

        });

        function cargarImagenPerfil() {

            $.ajax({
                url: "{{ tenant_url('tenant.personal.getimagen') }}",
                type: "GET",
                success: function(data) {

                    $('#avatarImageHeader').attr('src', data.ruta);
                    $('#avatarImageMenu').attr('src', data.ruta);

                }
            });

        }
    </script>

</body>

</html>