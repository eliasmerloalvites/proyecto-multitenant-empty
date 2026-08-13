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

                <a href="#inicio" class="text-blue-600 hover:text-blue-700 transition">
                    Inicio
                </a>

                <a href="#soluciones" class="text-slate-600 hover:text-blue-600 transition">
                    Soluciones
                </a>

                <a href="#planes-pos" class="text-slate-600 hover:text-blue-600 transition">
                    Planes
                </a>

                <a href="#clientes" class="text-slate-600 hover:text-blue-600 transition">
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

                <a href="#planes-pos"
                    class="px-6 py-3 rounded-xl
                bg-gradient-to-r from-blue-600 to-cyan-500
                hover:from-blue-700 hover:to-cyan-600
                text-white
                font-semibold
                shadow-xl shadow-blue-600/30
                transition-all">

                    Crear empresa

                </a>

            </div>

        </div>

    </header>
