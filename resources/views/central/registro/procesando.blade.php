@extends('central.landing.layouts.app')

@section('titulo', 'Creando tu cuenta — Kael Tech')

@section('content')

    <section class="relative min-h-screen bg-[#020817] flex items-center py-24 overflow-hidden">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div id="blobA" class="absolute -top-40 -left-32 w-[650px] h-[650px] bg-blue-500/20 blur-[180px] rounded-full transition-colors duration-700"></div>
            <div id="blobB" class="absolute -bottom-40 -right-32 w-[650px] h-[650px] bg-cyan-400/15 blur-[180px] rounded-full transition-colors duration-700"></div>
        </div>

        <div class="relative z-10 max-w-lg mx-auto px-6 w-full text-center">

            <!-- ESTADO: procesando -->
            <div id="panelProcesando">
                <div class="relative w-24 h-24 mx-auto mb-8">
                    <svg class="w-24 h-24 animate-spin" style="animation-duration: 1.6s;" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(148,163,184,.15)" stroke-width="8"/>
                        <circle cx="50" cy="50" r="42" fill="none" stroke="url(#gradAnillo)" stroke-width="8"
                                stroke-linecap="round" stroke-dasharray="264" stroke-dashoffset="180"/>
                        <defs>
                            <linearGradient id="gradAnillo" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#3b82f6"/>
                                <stop offset="100%" stop-color="#22d3ee"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center text-cyan-300 text-2xl">
                        <i class="fa-solid fa-gear animate-spin" style="animation-duration: 3s;"></i>
                    </div>
                </div>

                <h1 class="text-2xl md:text-3xl font-black text-white mb-3">Estamos creando tu cuenta</h1>

                <p id="pasoActual" class="text-slate-300 mb-8 leading-7 min-h-[28px] transition-opacity duration-300">
                    Preparando tu base de datos...
                </p>

                <div class="w-full h-2 rounded-full bg-slate-800/80 overflow-hidden mb-3">
                    <div id="barraProgreso" class="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 transition-all duration-700 ease-out" style="width: 8%;"></div>
                </div>

                <p class="text-slate-500 text-xs">
                    Esto puede tardar hasta un minuto. No cierres ni recargues esta página.
                </p>
            </div>

            <!-- ESTADO: completado (se rellena por JS, mismo diseño de central.registro.exito) -->
            <div id="panelExito" class="hidden">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-3xl mx-auto mb-6 shadow-xl">
                    <i class="fa-solid fa-check"></i>
                </div>

                <h1 class="text-3xl font-black text-white mb-3">¡Tu taller ya está listo! 🎉</h1>

                <p class="text-slate-300 mb-8 leading-7">
                    Creamos tu cuenta y tu panel de administración. Ya puedes ingresar con el
                    correo y la contraseña que registraste.
                </p>

                <a id="linkPanel" href="#"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold shadow-xl transition">
                    Ir a mi panel
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <p class="text-slate-500 text-sm mt-6">
                    Te llevamos automáticamente en <span id="segundosRedirect">5</span> segundos, o guarda este enlace:
                    <span id="urlPanelTexto" class="text-slate-300"></span>
                </p>
            </div>

            <!-- ESTADO: error (mismo diseño de central.registro.error) -->
            <div id="panelError" class="hidden">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white text-3xl mx-auto mb-6 shadow-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h1 class="text-2xl font-black text-white mb-3">No pudimos confirmar tu registro</h1>

                <p id="mensajeError" class="text-slate-300 mb-8 leading-7"></p>

                <a href="{{ route('central.registro.show') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold shadow-xl transition">
                    Volver a intentar
                </a>
            </div>

        </div>

    </section>

    <script>
        (function () {
            const TOKEN = @json($token);
            const URL_ESTADO = @json(route('central.registro.estado', ['token' => $token]));

            // Mensajes cosmeticos: no hay progreso real granular del Job (no
            // sabemos en que paso va), asi que se van rotando solos cada
            // pocos segundos mientras seguimos esperando el estado real.
            const pasos = [
                'Preparando tu base de datos...',
                'Configurando tu panel de administración...',
                'Creando tu usuario administrador...',
                'Dejando todo listo para tu primera venta...',
                'Ya casi terminamos...',
            ];
            let pasoIndex = 0;
            const $paso = document.getElementById('pasoActual');
            const $barra = document.getElementById('barraProgreso');

            function siguientePaso() {
                if (pasoIndex >= pasos.length - 1) return; // se queda en el ultimo mensaje hasta que el estado real llegue
                pasoIndex++;
                $paso.style.opacity = 0;
                setTimeout(function () {
                    $paso.textContent = pasos[pasoIndex];
                    $paso.style.opacity = 1;
                }, 250);
                $barra.style.width = Math.min(92, 8 + pasoIndex * 21) + '%';
            }
            const intervaloPasos = setInterval(siguientePaso, 4000);

            function mostrarExito(urlPanel) {
                clearInterval(intervaloPasos);
                $barra.style.width = '100%';
                document.getElementById('blobA').classList.replace('bg-blue-500/20', 'bg-cyan-400/20');

                setTimeout(function () {
                    document.getElementById('panelProcesando').classList.add('hidden');
                    document.getElementById('panelExito').classList.remove('hidden');
                    document.getElementById('linkPanel').href = urlPanel;
                    document.getElementById('urlPanelTexto').textContent = urlPanel;

                    let segundos = 5;
                    const $seg = document.getElementById('segundosRedirect');
                    const cuenta = setInterval(function () {
                        segundos--;
                        $seg.textContent = segundos;
                        if (segundos <= 0) {
                            clearInterval(cuenta);
                            window.location.href = urlPanel;
                        }
                    }, 1000);
                }, 400);
            }

            function mostrarError(mensaje) {
                clearInterval(intervaloPasos);
                document.getElementById('panelProcesando').classList.add('hidden');
                document.getElementById('panelError').classList.remove('hidden');
                document.getElementById('mensajeError').textContent = mensaje || 'No se pudo crear tu empresa. Intenta nuevamente o contáctanos.';
            }

            function consultarEstado() {
                fetch(URL_ESTADO, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.estado === 'completado') {
                            mostrarExito(data.url_panel);
                        } else if (data.estado === 'error') {
                            mostrarError(data.mensaje);
                        } else {
                            setTimeout(consultarEstado, 2500);
                        }
                    })
                    .catch(function () {
                        // Falla de red puntual: reintenta, no se cae la pantalla.
                        setTimeout(consultarEstado, 3000);
                    });
            }

            setTimeout(consultarEstado, 1500);
        })();
    </script>

@endsection
