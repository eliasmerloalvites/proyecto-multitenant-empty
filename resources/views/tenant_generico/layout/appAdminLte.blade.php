<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('titulo')</title><!--parte cambiante con yield-->

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/summernote/summernote-bs4.min.css')}}">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">

  <link rel="stylesheet" href="{{asset_root('adminlte/plugins/toastr/toastr.min.css')}}">

<!-- jQuery -->
<script src="{{asset_root('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset_root('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- Bootstrap 4 -->
<script src="{{asset_root('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset_root('adminlte/plugins/chart.js/Chart.min.js')}}"></script>
<!-- JQVMap -->
<!--<script src="{{asset_root('adminlte/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset_root('adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>-->
<!-- jQuery Knob Chart -->
<script src="{{asset_root('adminlte/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset_root('adminlte/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset_root('adminlte/plugins/daterangepicker/daterangepicker.js')}}"></script>

<!-- overlayScrollbars -->
<script src="{{asset_root('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>

<script src="{{asset_root('adminlte/plugins/sweetalert2/sweetalert2.min.js')}}"></script>

<script src="{{asset_root('adminlte/plugins/toastr/toastr.min.js')}}"></script>

{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  --}}
       

<!-- ./wrapper -->


<script src="{{asset_root('js/image-elias.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset_root('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset_root('adminlte/plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset_root('adminlte/plugins/sparklines/sparkline.js')}}"></script>
<!-- Theme style -->
<link rel="stylesheet" href="{{asset_root('adminlte/dist/css/adminlte.min.css')}}">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- JQVMap -->
<script src="{{asset_root('adminlte/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset_root('adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset_root('adminlte/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset_root('adminlte/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset_root('adminlte/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset_root('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset_root('adminlte/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset_root('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset_root('adminlte/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset_root('adminlte/dist/js/demo.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset_root('adminlte/dist/js/pages/dashboard.js')}}"></script>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

 <!-- Datatables -->
 <link href="{{ asset_root('css/bootstrap.min.css') }}" rel="stylesheet">
 <link href="{{ asset_root('css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

 <script src="{{ asset_root('js/jquery.js') }}"></script>
 <script src="{{ asset_root('js/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset_root('js/dataTables.bootstrap4.min.js') }}"></script>

 <script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.js"></script>
 <link rel="stylesheet" href="{{ asset_root('css/jquery.dataTables.min.css') }}">
 <script type="text/javascript" language="javascript"
     src="https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js"></script>
 <script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js">
 </script>
 <script type="text/javascript" language="javascript"
     src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
 <script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js">
 </script>
 <script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.1/js/buttons.html5.min.js">
 </script>
 <link rel="stylesheet" type="text/css"
     href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.dataTables.min.css">


  <!-- ijaboCropTool -->
  <link rel="stylesheet" href="{{ asset_root('ijaboCropTool/ijaboCropTool.min.css') }}">
  <script src="{{ asset_root('ijaboCropTool/ijaboCropTool.min.js') }}"></script>

 <!-- Fechas -->
     <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
     <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


 <!-- Graficos -->

     <script src="https://code.highcharts.com/highcharts.js"></script>
     <script src="https://code.highcharts.com/modules/data.js"></script>
     <script src="https://code.highcharts.com/modules/drilldown.js"></script>
     <script src="https://code.highcharts.com/modules/series-label.js"></script>
     <script src="https://code.highcharts.com/modules/exporting.js"></script>
     <script src="https://code.highcharts.com/modules/export-data.js"></script>
     <script src="https://code.highcharts.com/modules/accessibility.js"></script>

     <script src="{{ asset_root('Highcharts/code/highcharts.js') }}"></script>
     <script src="{{ asset_root('Highcharts/code/modules/pareto.js') }}"></script>
     <script src="{{ asset_root('Highcharts/code/modules/data.js') }}"></script>
     <script src="{{ asset_root('Highcharts/code/modules/drilldown.js') }}"></script>

@yield('script')


    @yield('head')

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100%;
                margin: 0;
            }

            .full-height {
                height: 100%;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="adminlte/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>
  
  <!-- Navbar -->
  @include('tenant_generico.partials.navbar')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  @include('tenant_generico.partials.sidebar')  
  @include('tenant_generico.partials.container')
  
  @include('tenant_generico.partials.controlSidebar')
</div>


@stack('scripts')
        <script type="text/javascript">
            if (window.location.hash && window.location.hash == '#_=_') {
                window.location.hash = '';
            }


            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                Datos();
            });


            function Datos() {
                $.ajax({
                    url: "{{ tenant_url('tenant.personal.getimagen') }}",
                    type: "GET",
                }).done(function(data) {
                    $('#avatarImageHeader').attr('src', data.ruta);
                    $('#avatarImageMenu').attr('src', data.ruta);
                })
            }
        </script>

</body>
</html>
