<html style="" class=" js no-touch csstransforms3d csstransitions">

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8">
    <meta charset="utf-8">
    <title>Acceso al Sistema :: MTC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description">
    <meta content="" name="author">
    <link href="/pluginlogin/pace-theme-flash.css" rel="stylesheet"
        type="text/css"> {{-- DESCARGADO --}}
    <link type="text/css" rel="stylesheet" href="{{ asset_root('assets/libs/bootstrap-3.3.5/css/bootstrap.css') }}"
        rel="stylesheet" type="text/css">
    <link type="text/css" rel="stylesheet"
            href="{{ asset_root('assets/font/font-icon/font-awesome-4.4.0/css/font-awesome.css') }}">
    <link class="main-stylesheet" href="/pluginlogin/pages.css" rel="stylesheet"
        type="text/css"> {{-- DESCARGADO --}}
    <link rel="stylesheet" type="text/css" href="/pluginlogin/style.css"> {{-- DESCARGADO --}}
    <script src="/pluginlogin/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="/pluginlogin/modernizr.custom.js" type="text/javascript">
    </script>{{-- DESCARGADO --}}

    {{-- <script type="text/javascript">
        window.onload = function() {
            // fix for windows 8
            if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
                document.head.innerHTML +=
                '<link rel="stylesheet" type="text/css" href="~/Content/login/pages/css/windows.chrome.fix.css" />';
        }
    </script> --}}
    {{--libreria Toastr--}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{--libreria sweetalert2--}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{--JQUERY--}}
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
</head>

<body class="fixed-header  pace-done">
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>

    <div class="login-wrapper">
        <div class="bg-pic">
            <img src="/images/login/fondologinweb.jpg" data-src="/images/login/fondologinweb.png"
                data-src-retina="/images/login/fondologinweb.png" alt="" height="20%" class="lazy" />
            {{-- s --}}
        </div>
        <div class="login-container bg-white">
            <div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
                <img style="margin-left: 0" src="/images/login/logo.png" alt="logo" width="" height="70" />

                <style>
                    .zocial-link {
                        margin: 1px !important;
                        width: 87px;
                    }

                    #notify-message .alert {
                        margin-bottom: 0;
                        margin-top: 10px;
                    }

                </style>

                <div class="login-box">

                    <form  id="LoginForm" name="LoginForm" >
                        @csrf
                        <div class="p-t-25">

                            <div class="form-group form-group-default">
                                <label>Login</label>
                                <div class="controls">
                                    <input type="text" name="email" placeholder="Usuario" id="email"
                                        class="form-control @error('email') is-invalid @enderror login-form-field" value="{{old('email')}}" required tabindex="1" />
                                    <input type="hidden" name="user"  value="1" />
                                    @error('email') 
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                    		@enderror
                                </div>
                            </div>
                            <div class="form-group form-group-default">
                                <label>Contraseña</label>
                                <div class="controls">
                                    <input type="password"  class="form-control @error('password') is-invalid @enderror login-form-field" value="{{old('password')}}" id="password"
                                        name="password" placeholder="Credenciales" required tabindex="2" />
                                        @error('password') 
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @if(session()->has('danger'))
                                <div class="alert alert-danger col-md-12">{{ session()->get('danger') }}</div>
                            @endif
                            <div class="row" >
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <input type="checkbox" id="remember-me" class="login-form-field" tabindex="3" />
                                        <label for="remember-me">No cerrar sesión.</label>
                                    </div>
                                </div>
                                <div class="col-md-6" align="right"  >
                                <button class="btn btn-success btn-cons m-t-10  float-right" id="saveBtn" name="saveBtn" >
                                    Ingresar
                                </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="pull-bottom sm-pull-bottom">
                    <div class="m-b-30 p-r-80 sm-m-t-20 sm-p-r-15 sm-p-b-20 clearfix">
                        <div class="col-sm-12 no-padding m-t-10">
                            <p>
                                <small>
                                    Ingresa a la Intranet con tus credenciales del
                                    <i class="fa fa-desktop fa-fw fa-lg"></i>
                                    <b>ERP </b>.<br />Soporte al anexo
                                    <i class="fas fa-info-circle"></i> <a href="https://wa.me/51929386665/?text=Hola%20Necesito%20ayuda" target="_blank"><b>A1E9M9A8</b>.</a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="/pluginlogin/pace.min.js" type="text/javascript"></script> {{-- DESCARGADO --}}
    <script src="/pluginlogin/jquery.scrollTo.js" type="text/javascript"></script> {{-- DESCARGADO --}}
    <script src="{{ asset_root('assets/libs/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="/pluginlogin/jquery.blockUI.js" type="text/javascript"></script> {{-- DESCARGADO --}}
    <script src="/pluginlogin/bootstrap-dialog.min.js"
        type="text/javascript"></script> {{-- DESCARGADO --}}
    <script src="/pluginlogin/simple.tools.core.js" type="text/javascript"></script> {{-- DESCARGADO --}}
    <script type="text/javascript">
        App.BaseURL("");
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            
            $('#saveBtn').click(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $('#LoginForm').serialize(),
                    url: "{{ tenant_url('tenant.login.post') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        window.location.href = "/tenant/home";
                        $('#LoginForm').trigger("reset");
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Credenciales incorrectas'
                            
                        })
                    }
                });
            });
        });
    </script>

</body>

</html>
