<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('titulo')</title>

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Font --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset_root('adminlte/plugins/fontawesome-free/css/all.min.css') }}">

    {{-- AdminLTE --}}
    <link rel="stylesheet" href="{{ asset_root('adminlte/dist/css/adminlte.min.css') }}">

    {{-- Plugins --}}
    <link rel="stylesheet" href="{{ asset_root('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset_root('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

    <link rel="stylesheet" href="{{ asset_root('adminlte/plugins/toastr/toastr.min.css') }}">

    {{-- DataTables --}}
    <link rel="stylesheet" href="{{ asset_root('css/dataTables.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset_root('css/jquery.dataTables.min.css') }}">

    {{-- Daterangepicker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{-- Crop Tool --}}
    <link rel="stylesheet" href="{{ asset_root('ijaboCropTool/ijaboCropTool.min.css') }}">
    <link href="{{ asset_root('plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset_root('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet">
    
    {{-- <link rel="stylesheet" href="{{ asset_root('csskael/bootstrap.min.css') }}"> --}}
    @php
        $temaPanel = ($empresa->tipo_tema ?? null) === 'dark' ? 'dark' : 'light';
    @endphp
    <link rel="stylesheet" href="{{ asset_root('csskael/kael-' . $temaPanel . '.css') }}">
    <style>
        .preloader {
            background: var(--bg-main);
            transition: opacity .25s ease;
        }

        .preloader-spinner {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 4px solid rgba(37, 99, 235, .15);
            border-top-color: var(--primary);
            animation: preloader-spin .8s linear infinite;
        }

        .preloader-text {
            margin-top: 16px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: .02em;
        }

        @keyframes preloader-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    @yield('head')
</head>

<body class="hold-transition sidebar-mini tema-{{ $temaPanel }}">

    <div class="wrapper">

        {{-- Preloader --}}
        <div class="preloader flex-column justify-content-center align-items-center">
            <div class="preloader-spinner"></div>
            <div class="preloader-text">Cargando...</div>
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

        function seleccionarCaja(cajaId) {
            $.ajax({
                url: "{{ tenant_url('tenant.caja-sesion.seleccionar') }}",
                type: "POST",
                data: { caja_id: cajaId },
                dataType: 'json',
                success: function() {
                    window.location.reload();
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.error
                        ? xhr.responseJSON.error
                        : 'No se pudo cambiar de caja.';
                    Swal.fire({ icon: 'error', title: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            });
        }

        $('body').on('click', '.seleccionarCaja', function(e) {
            e.preventDefault();
            seleccionarCaja($(this).data('caja-id'));
        });

        $('body').on('click', '.aperturarCajaNavbar', function(e) {
            e.preventDefault();
            var cajaId = $(this).data('id');
            var nombre = $(this).data('nombre');
            var montoDefault = $(this).data('monto');

            Swal.fire({
                title: 'Aperturar "' + nombre + '"',
                input: 'number',
                inputLabel: 'Monto de apertura (S/)',
                inputValue: montoDefault,
                inputAttributes: { step: '0.01', min: '0' },
                showCancelButton: true,
                confirmButtonText: 'Aperturar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "{{ tenant_url('tenant.caja-sesion.abrir') }}",
                    type: 'POST',
                    data: { caja_id: cajaId, monto_apertura: result.value },
                    dataType: 'json',
                    success: function() { window.location.reload(); },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo aperturar la caja.';
                        Swal.fire({ icon: 'error', title: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    }
                });
            });
        });

        $('body').on('click', '.cerrarCajaNavbar', function(e) {
            e.preventDefault();
            var cajaId = $(this).data('id');
            var nombre = $(this).data('nombre');

            Swal.fire({
                title: 'Cerrar "' + nombre + '"',
                input: 'number',
                inputLabel: 'Monto real contado en caja (S/)',
                inputAttributes: { step: '0.01', min: '0' },
                showCancelButton: true,
                confirmButtonText: 'Cerrar caja',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "{{ tenant_url('tenant.caja-sesion.cerrar') }}",
                    type: 'POST',
                    data: { caja_id: cajaId, monto_real: result.value },
                    dataType: 'json',
                    success: function(data) {
                        window.location.reload();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo cerrar la caja.';
                        Swal.fire({ icon: 'error', title: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    }
                });
            });
        });

        @if ($requiereSeleccionCaja ?? false)
            Swal.fire({
                icon: 'question',
                title: '¿Con qué caja vas a trabajar?',
                html: `
                    <div class="d-flex flex-column" style="gap:8px;">
                        @foreach ($cajasDisponibles->filter(fn($cj) => $cj->sesionAbierta) as $cj)
                            <button type="button" class="btn btn-outline-primary btn-block seleccionar-caja-modal" data-caja-id="{{ $cj->CAJ_Id }}">
                                <i class="fas fa-cash-register mr-1"></i> {{ $cj->CAJ_Nombre }}
                            </button>
                        @endforeach
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    document.querySelectorAll('.seleccionar-caja-modal').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            Swal.close();
                            seleccionarCaja(this.dataset.cajaId);
                        });
                    });
                }
            });
        @endif
    </script>

</body>

</html>