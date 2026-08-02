<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema :: SAAS KAEL</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#E52320',
                            redHover: '#C81B18',
                            darkBg: '#0B0F17',
                            cardBg: '#131924',
                            inputBg: '#1A2232',
                            border: '#2A3548'
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

    <style>
        .glow-effect:focus-within {
            box-shadow: 0 0 15px rgba(229, 35, 32, 0.25);
        }
    </style>
</head>

<body class="bg-brand-darkBg text-gray-100 min-h-screen flex items-center justify-center font-sans relative overflow-hidden">

    <!-- Fondo dinámico con brillo de neón -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-red opacity-10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600 opacity-10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Contenedor Principal -->
    <div class="w-full max-w-md p-6 relative z-10">
        
        <!-- Card de Login -->
        <div class="bg-brand-cardBg border border-brand-border/60 rounded-2xl shadow-2xl p-8 backdrop-blur-md">
            
            <!-- Logo y Encabezado -->
            <div class="text-center mb-8">
                <img src="/images/login/logo_kael.png" alt="KAEL Logo" class="h-14 mx-auto mb-3 object-contain" />
                <span class="inline-block px-3 py-1 bg-brand-red/10 text-brand-red text-xs font-semibold rounded-full border border-brand-red/20 uppercase tracking-wider">
                    Panel Administrativo
                </span>
            </div>

            <!-- Formulario -->
            <form id="LoginForm" name="LoginForm" class="space-y-5">
                @csrf

                <!-- Campo Usuario / Email -->
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                        Usuario o Correo
                    </label>
                    <div class="relative flex items-center rounded-xl bg-brand-inputBg border border-brand-border focus-within:border-brand-red transition-all duration-200 glow-effect">
                        <span class="pl-4 text-gray-500">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="email" id="email" 
                            class="w-full bg-transparent py-3 px-3 text-sm text-white placeholder-gray-500 focus:outline-none @error('email') border-red-500 @enderror" 
                            placeholder="Ej. admin@kael.com" 
                            value="{{ old('email') }}" required tabindex="1" />
                    </div>
                    <input type="hidden" name="user" value="1" />
                    @error('email') 
                        <span class="text-red-500 text-xs mt-1 block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                        Contraseña
                    </label>
                    <div class="relative flex items-center rounded-xl bg-brand-inputBg border border-brand-border focus-within:border-brand-red transition-all duration-200 glow-effect">
                        <span class="pl-4 text-gray-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" 
                            class="w-full bg-transparent py-3 px-3 text-sm text-white placeholder-gray-500 focus:outline-none @error('password') border-red-500 @enderror" 
                            placeholder="••••••••" required tabindex="2" />
                        <button type="button" id="togglePassword" class="pr-4 text-gray-500 hover:text-gray-300 focus:outline-none">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    @error('password') 
                        <span class="text-red-500 text-xs mt-1 block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Mensaje Alerta de Sesión (Laravel) -->
                @if(session()->has('danger'))
                    <div class="p-3 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-xl">
                        {{ session()->get('danger') }}
                    </div>
                @endif

                <!-- Checkbox y Recordar -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center cursor-pointer text-gray-400 hover:text-gray-300">
                        <input type="checkbox" id="remember-me" tabindex="3"
                            class="w-4 h-4 rounded bg-brand-inputBg border-brand-border text-brand-red focus:ring-brand-red focus:ring-offset-brand-darkBg">
                        <span class="ml-2 text-xs">No cerrar sesión</span>
                    </label>
                </div>

                <!-- Botón de Ingreso -->
                <button type="button" id="saveBtn" name="saveBtn" 
                    class="w-full py-3.5 px-4 bg-brand-red hover:bg-brand-redHover text-white font-semibold rounded-xl shadow-lg shadow-brand-red/30 hover:shadow-brand-red/50 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer">
                    <span>Ingresar al Sistema</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- Footer con link de soporte -->
            <div class="mt-8 pt-6 border-t border-brand-border/40 text-center text-xs text-gray-500">
                <p class="flex items-center justify-center gap-1.5 mb-1">
                    <i class="fa-solid fa-desktop text-gray-400"></i>
                    <span>Acceso Intranet ERP</span>
                </p>
                <p>
                    ¿Necesitas ayuda? 
                    <a href="https://wa.me/51929386665/?text=Hola%20Necesito%20ayuda" target="_blank" 
                       class="text-brand-red hover:underline font-semibold inline-flex items-center gap-1">
                        Soporte Anexo A1E9M9A8 <i class="fa-brands fa-whatsapp text-xs"></i>
                    </a>
                </p>
            </div>

        </div>

        <p class="text-center text-xs text-gray-600 mt-6">
            &copy; {{ date('Y') }} SAAS KAEL. Todos los derechos reservados.
        </p>
    </div>

    <!-- Script para Manejo de Mostrar/Ocultar Password y Petición AJAX -->
    <script>
        $(document).ready(function() {
            // Configuración del Token CSRF para AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toggle para ver contraseña
            $('#togglePassword').click(function() {
                const passwordInput = $('#password');
                const icon = $(this).find('i');
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // SweetAlert2 Toast Config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                background: '#131924',
                color: '#fff'
            });
            
            // Evento Click Submit AJAX
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                
                const btn = $(this);
                btn.prop('disabled', true).addClass('opacity-75');

                $.ajax({
                    data: $('#LoginForm').serialize(),
                    url: "{{ tenant_url('tenant.login.post') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            icon: 'success',
                            title: data.success || '¡Acceso concedido!'
                        });
                        setTimeout(function() {
                            window.location.href = "/tenant/home";
                        }, 800);
                    },
                    error: function(data) {
                        btn.prop('disabled', false).removeClass('opacity-75');
                        Toast.fire({
                            icon: 'error',
                            title: 'Credenciales incorrectas'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>