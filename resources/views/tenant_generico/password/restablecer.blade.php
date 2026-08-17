<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña :: SAAS KAEL</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

    <style>
        .glow-effect:focus-within {
            box-shadow: 0 0 15px rgba(229, 35, 32, 0.25);
        }
    </style>
</head>

<body class="bg-brand-darkBg text-gray-100 min-h-screen flex items-center justify-center font-sans relative overflow-hidden">

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-red opacity-10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600 opacity-10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md p-6 relative z-10">

        <div class="bg-brand-cardBg border border-brand-border/60 rounded-2xl shadow-2xl p-8 backdrop-blur-md">

            <div class="text-center mb-8">
                <img src="{{ !empty($empresa->logo_pdf) ? asset_root($empresa->logo_pdf) : asset_root('images/icono.jpg') }}" alt="KAEL Logo" class="h-14 mx-auto mb-3 object-contain" />
                <h1 class="text-lg font-bold text-white">Elige tu nueva contraseña</h1>
            </div>

            <form id="ResetForm" name="ResetForm" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                        Correo
                    </label>
                    <div class="relative flex items-center rounded-xl bg-brand-inputBg border border-brand-border focus-within:border-brand-red transition-all duration-200 glow-effect">
                        <span class="pl-4 text-gray-500"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" id="email" value="{{ $email }}"
                            class="w-full bg-transparent py-3 px-3 text-sm text-white placeholder-gray-500 focus:outline-none"
                            required />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                        Nueva contraseña
                    </label>
                    <div class="relative flex items-center rounded-xl bg-brand-inputBg border border-brand-border focus-within:border-brand-red transition-all duration-200 glow-effect">
                        <span class="pl-4 text-gray-500"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="password"
                            class="w-full bg-transparent py-3 px-3 text-sm text-white placeholder-gray-500 focus:outline-none"
                            placeholder="Mínimo 8 caracteres" required />
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                        Confirmar contraseña
                    </label>
                    <div class="relative flex items-center rounded-xl bg-brand-inputBg border border-brand-border focus-within:border-brand-red transition-all duration-200 glow-effect">
                        <span class="pl-4 text-gray-500"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full bg-transparent py-3 px-3 text-sm text-white placeholder-gray-500 focus:outline-none"
                            placeholder="Repite la contraseña" required />
                    </div>
                </div>

                <button type="button" id="saveBtn"
                    class="w-full py-3.5 px-4 bg-brand-red hover:bg-brand-redHover text-white font-semibold rounded-xl shadow-lg shadow-brand-red/30 hover:shadow-brand-red/50 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer">
                    <span>Guardar nueva contraseña</span>
                    <i class="fa-solid fa-check text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

        </div>

    </div>

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
                timer: 3500,
                background: '#131924',
                color: '#fff'
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                const btn = $(this);
                btn.prop('disabled', true).addClass('opacity-75');

                $.ajax({
                    data: $('#ResetForm').serialize(),
                    url: "{{ tenant_url('tenant.password.update') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({ icon: 'success', title: data.success });
                        setTimeout(function() {
                            window.location.href = "{{ tenant_url('tenant.login') }}";
                        }, 1200);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.error || 'No se pudo restablecer tu contraseña.';
                        Toast.fire({ icon: 'error', title: msg });
                        btn.prop('disabled', false).removeClass('opacity-75');
                    }
                });
            });
        });
    </script>
</body>

</html>
