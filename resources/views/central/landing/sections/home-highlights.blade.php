    <section class="relative py-16 bg-white overflow-hidden">

        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 text-blue-700 px-5 py-2 font-bold text-sm mb-6">
                    <i class="fa-solid fa-compass"></i>
                    UN VISTAZO RÁPIDO
                </span>

                <h2 class="text-4xl lg:text-5xl font-black leading-tight text-slate-900">
                    Todo lo que necesitas
                    <span class="bg-gradient-to-r from-blue-600 via-cyan-500 to-sky-500 bg-clip-text text-transparent">
                        saber de Kael Tech
                    </span>
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6">

                <!-- Soluciones -->
                <a href="{{ route('central.soluciones') }}"
                    class="group relative overflow-hidden rounded-[28px] border border-slate-200 bg-white p-8 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 shadow-lg flex items-center justify-center mb-6">
                        <i class="fa-solid fa-layer-group text-white text-xl"></i>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-3">Soluciones</h3>

                    <p class="text-slate-600 leading-7 mb-6">
                        Kael POS para negocios y comercios, Kael Moto para talleres
                        de motos. Dos sistemas hechos a la medida de cada rubro.
                    </p>

                    <span class="inline-flex items-center gap-2 text-blue-600 font-bold group-hover:gap-3 transition-all">
                        Ver soluciones
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </span>
                </a>

                <!-- Planes Kael POS -->
                <a href="{{ route('central.planes') }}#planes-pos"
                    class="group relative overflow-hidden rounded-[28px] border border-slate-800 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-950 p-8 shadow-2xl hover:shadow-blue-500/20 hover:-translate-y-1 transition-all duration-300">

                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 shadow-lg flex items-center justify-center mb-6">
                        <i class="fa-solid fa-cash-register text-white text-xl"></i>
                    </div>

                    <h3 class="text-2xl font-black text-white mb-3">Kael POS desde S/ 29/mes</h3>

                    <p class="text-slate-300 leading-7 mb-6">
                        14 días de prueba gratis. Ventas, inventario y facturación
                        electrónica para tu negocio.
                    </p>

                    <span class="inline-flex items-center gap-2 text-cyan-300 font-bold group-hover:gap-3 transition-all">
                        Ver planes POS
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </span>
                </a>

                <!-- Planes Kael Moto -->
                <a href="{{ route('central.planes') }}#planes-moto"
                    class="group relative overflow-hidden rounded-[28px] border border-slate-800 bg-gradient-to-br from-slate-950 via-cyan-950 to-slate-950 p-8 shadow-2xl hover:shadow-cyan-500/20 hover:-translate-y-1 transition-all duration-300">

                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 shadow-lg flex items-center justify-center mb-6">
                        <i class="fa-solid fa-motorcycle text-white text-xl"></i>
                    </div>

                    <h3 class="text-2xl font-black text-white mb-3">Kael Moto desde S/ 49/mes</h3>

                    <p class="text-slate-300 leading-7 mb-6">
                        Mantenimientos, reservas online e historial por placa
                        para tu taller.
                    </p>

                    <span class="inline-flex items-center gap-2 text-cyan-300 font-bold group-hover:gap-3 transition-all">
                        Ver planes Moto
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </span>
                </a>

                <!-- Clientes -->
                <a href="{{ route('central.clientes') }}"
                    class="group relative overflow-hidden rounded-[28px] border border-slate-200 bg-white p-8 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 shadow-lg flex items-center justify-center mb-6">
                        <i class="fa-solid fa-users text-white text-xl"></i>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-3">¿Para quién es Kael?</h3>

                    <p class="text-slate-600 leading-7 mb-6">
                        Negocios y comercios que venden productos, y talleres de
                        motos que gestionan servicios. Conoce qué encaja contigo.
                    </p>

                    <span class="inline-flex items-center gap-2 text-blue-600 font-bold group-hover:gap-3 transition-all">
                        Ver más
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </span>
                </a>

            </div>

        </div>

    </section>
