    <!-- NAVBAR -->
    <header
        class="fixed top-0 left-0 w-full z-50
bg-white/80 backdrop-blur-xl
border-b border-slate-200/70
shadow-lg shadow-slate-900/5">

        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <!-- Logo -->
            <div class="flex items-center gap-4">

                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 p-[2px] shadow-lg">

                    <div class="w-full h-full rounded-2xl bg-white flex items-center justify-center">

                        <img src="{{ asset('images/icono.jpg') }}" class="w-9 h-9 object-contain rounded-lg">

                    </div>

                </div>

                <div>

                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Kael Tech
                    </h1>

                    <p class="text-xs text-slate-500 tracking-wide">
                        Business Cloud Platform
                    </p>

                </div>

            </div>

            <!-- Menú -->
            <nav class="hidden lg:flex items-center gap-8 text-[15px] font-semibold">

                <a href="{{ route('central.inicio') }}" class="{{ request()->routeIs('central.inicio') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition">
                    Inicio
                </a>

                <a href="{{ route('central.soluciones') }}" class="{{ request()->routeIs('central.soluciones') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition">
                    Soluciones
                </a>

                <a href="{{ route('central.planes') }}" class="{{ request()->routeIs('central.planes') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition">
                    Planes
                </a>

                <a href="{{ route('central.clientes') }}" class="{{ request()->routeIs('central.clientes') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition">
                    Clientes
                </a>

                <a href="#contacto" class="text-slate-600 hover:text-blue-600 transition">
                    Contacto
                </a>

            </nav>

            <!-- Botones -->
            <div class="flex items-center gap-3">

                <a href="/login"
                    class="hidden md:flex px-5 py-3 rounded-xl
                border border-slate-300
                hover:border-blue-600
                hover:text-blue-600
                transition
                font-semibold">

                    Iniciar sesión

                </a>

                <a href="{{ route('central.registro.show') }}"
                    class="hidden sm:flex px-6 py-3 rounded-xl
                bg-gradient-to-r from-blue-600 to-cyan-500
                hover:from-blue-700 hover:to-cyan-600
                text-white
                font-semibold
                shadow-xl shadow-blue-600/30
                transition-all">

                    Crear empresa

                </a>

                <!-- Botón hamburguesa (solo mobile/tablet) -->
                <button type="button" id="menuMobileBtn"
                    class="lg:hidden w-11 h-11 rounded-xl border border-slate-300 flex items-center justify-center text-slate-700 hover:border-blue-600 hover:text-blue-600 transition"
                    aria-label="Abrir menú" aria-expanded="false" aria-controls="menuMobile">
                    <i class="fa-solid fa-bars text-lg" id="menuMobileIcon"></i>
                </button>

            </div>

        </div>

        <!-- Menú mobile (drawer) -->
        <nav id="menuMobile"
            class="lg:hidden hidden bg-white border-t border-slate-200/70 shadow-lg">

            <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col gap-1 text-[15px] font-semibold">

                <a href="{{ route('central.inicio') }}" class="px-3 py-3 rounded-lg {{ request()->routeIs('central.inicio') ? 'text-blue-600' : 'text-slate-700 hover:text-blue-600' }} hover:bg-blue-50 transition">
                    Inicio
                </a>
                <a href="{{ route('central.soluciones') }}" class="px-3 py-3 rounded-lg {{ request()->routeIs('central.soluciones') ? 'text-blue-600' : 'text-slate-700 hover:text-blue-600' }} hover:bg-blue-50 transition">
                    Soluciones
                </a>
                <a href="{{ route('central.planes') }}" class="px-3 py-3 rounded-lg {{ request()->routeIs('central.planes') ? 'text-blue-600' : 'text-slate-700 hover:text-blue-600' }} hover:bg-blue-50 transition">
                    Planes
                </a>
                <a href="{{ route('central.clientes') }}" class="px-3 py-3 rounded-lg {{ request()->routeIs('central.clientes') ? 'text-blue-600' : 'text-slate-700 hover:text-blue-600' }} hover:bg-blue-50 transition">
                    Clientes
                </a>
                <a href="#contacto" class="px-3 py-3 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    Contacto
                </a>

                <div class="h-px bg-slate-200 my-2"></div>

                <a href="/login" class="px-3 py-3 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    Iniciar sesión
                </a>
                <a href="{{ route('central.registro.show') }}"
                    class="mt-1 px-3 py-3.5 rounded-xl text-center bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold shadow-lg shadow-blue-600/30">
                    Crear empresa
                </a>

            </div>

        </nav>

    </header>

    <script>
        (function () {
            const btn = document.getElementById('menuMobileBtn');
            const menu = document.getElementById('menuMobile');
            const icon = document.getElementById('menuMobileIcon');

            function cerrarMenu() {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }

            btn.addEventListener('click', function () {
                const abierto = !menu.classList.contains('hidden');
                if (abierto) {
                    cerrarMenu();
                } else {
                    menu.classList.remove('hidden');
                    btn.setAttribute('aria-expanded', 'true');
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                }
            });

            // Cierra el menú al tocar cualquier link (ancla o navegación real).
            menu.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', cerrarMenu);
            });
        })();
    </script>
