<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso al Sistema :: MTC</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    {{-- Tailwind CSS CDN (o tu compilado de Vite/Mix) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- FontAwesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
</head>

<body class="h-full text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    <div class="min-h-screen flex">
        
        {{-- ================================= --}}
        {{-- SECCIÓN IZQUIERDA: HERO / BANNER  --}}
        {{-- ================================= --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-[#020817] items-center justify-center p-12 overflow-hidden">
            {{-- Glowing Background Gradients --}}
            <div class="absolute -top-24 -left-24 w-[500px] h-[500px] bg-blue-600/20 blur-[130px] rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-[500px] h-[500px] bg-cyan-500/20 blur-[130px] rounded-full pointer-events-none"></div>

            {{-- Imagen de fondo con overlay sutil --}}
            <div class="absolute inset-0 z-0 opacity-40 mix-blend-overlay">
                <img src="/images/login/fondologinweb.png" alt="Fondo Login" class="w-full h-full object-cover">
            </div>

            {{-- Contenido sobre la imagen --}}
            <div class="relative z-10 max-w-lg text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider mb-6">
                    <i class="fa-solid fa-shield-halved"></i> Acceso Seguro a Intranet
                </div>
                <h1 class="text-4xl font-black text-white tracking-tight leading-tight mb-4">
                    Plataforma Integral de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">Gestión ERP</span>
                </h1>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Accede a todos los módulos centralizados del sistema para administrar operaciones, clientes y mantenimiento con total seguridad.
                </p>
            </div>
        </div>

        {{-- ================================= --}}
        {{-- SECCIÓN DERECHA: FORMULARIO LOGIN --}}
        {{-- ================================= --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-between bg-white p-8 sm:p-12 lg:p-16">
            
            {{-- Header / Logo --}}
            <div class="w-full max-w-md mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <img src="/images/login/logo_kael.png" alt="KAEL Logo" class="h-12 w-auto object-contain">
                    <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">v2.5</span>
                </div>

                {{-- Título Formulario --}}
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Iniciar Sesión</h2>
                    <p class="text-sm text-slate-500 mt-1">Ingresa tus credenciales autorizadas del ERP.</p>
                </div>

                {{-- Alert de Danger de Laravel (si existiera) --}}
                @if(session()->has('danger'))
                    <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-red-500 text-base"></i>
                        <span>{{ session()->get('danger') }}</span>
                    </div>
                @endif

                {{-- Formulario --}}
                <form id="LoginForm" name="LoginForm" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="user" value="1">

                    {{-- Campo Usuario / Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Usuario o Correo
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-user text-sm"></i>
                            </div>
                            <input type="text" name="email" id="email" 
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('email') border-red-500 bg-red-50/30 @enderror" 
                                placeholder="Ej. usuario@empresa.com" 
                                value="{{ old('email') }}" 
                                required 
                                tabindex="1">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Campo Contraseña --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input type="password" name="password" id="password" 
                                class="w-full pl-11 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('password') border-red-500 bg-red-50/30 @enderror" 
                                placeholder="••••••••••••" 
                                required 
                                tabindex="2">
                            {{-- Toggle Password Visibility --}}
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <i class="fa-regular fa-eye text-sm" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Recordar sesión & Botón Submit --}}
                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" id="remember-me" class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer transition" tabindex="3">
                            <span class="ml-2.5 text-xs font-medium text-slate-600 group-hover:text-slate-900 transition-colors">No cerrar sesión</span>
                        </label>
                    </div>

                    {{-- Botón Ingresar --}}
                    <button type="submit" id="saveBtn" name="saveBtn" 
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold py-3.5 px-6 rounded-2xl text-sm shadow-lg shadow-blue-500/25 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                        <span>Ingresar al Sistema</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </div>

            {{-- Footer / Soporte --}}
            <div class="w-full max-w-md mx-auto mt-8 pt-6 border-t border-slate-100">
                <div class="flex items-start gap-3 text-xs text-slate-500 leading-relaxed">
                    <div class="w-7 h-7 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0 mt-0.5">
                        <i class="fa-solid fa-headset text-xs"></i>
                    </div>
                    <div>
                        <p class="font-medium text-slate-600">
                            Ingresa a la Intranet con tus credenciales del <b>ERP</b>.
                        </p>
                        <p class="mt-0.5">
                            ¿Problemas de acceso? Contacta a soporte: 
                            <a href="https://wa.me/51929386665/?text=Hola%20Necesito%20ayuda" target="_blank" class="font-bold text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center gap-1 ml-1">
                                <span>A1E9M9A8</span>
                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Lógica JavaScript Adaptada --}}
    <script>
        $(document).ready(function() {
            // Setup CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // SweetAlert Toast Configuration
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            // Toggle Password Visibility
            $('#togglePassword').click(function() {
                const passwordInput = $('#password');
                const eyeIcon = $('#eyeIcon');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Form AJAX Submit
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                
                const $btn = $(this);
                const originalText = $btn.html();
                
                // Feedback visual de carga
                $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Validando...');

                $.ajax({
                    data: $('#LoginForm').serialize(),
                    url: "{{ route('central.login.post') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            icon: 'success',
                            title: data.success || '¡Bienvenido al sistema!'
                        });
                        
                        setTimeout(function() {
                            window.location.href = "/home";
                        }, 800);
                    },
                    error: function(data) {
                        $btn.prop('disabled', false).html(originalText);
                        
                        Toast.fire({
                            icon: 'error',
                            title: 'Credenciales incorrectas o no autorizadas'
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>