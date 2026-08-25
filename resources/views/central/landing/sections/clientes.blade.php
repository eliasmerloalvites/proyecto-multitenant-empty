    <!-- ======================================= -->
    <!-- CLIENTES -->
    <!-- ======================================= -->

    <section id="clientes" class="relative py-16 bg-white overflow-hidden">

        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center max-w-3xl mx-auto mb-10">

                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-5 py-2 text-blue-700 text-sm font-bold mb-6">
                    <i class="fa-solid fa-users"></i>
                    ¿PARA QUIÉN ES KAEL TECH?
                </span>

                <h2 class="text-4xl lg:text-4xl font-black text-slate-900 leading-tight">
                    Negocios que ya confían
                    <span class="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                        en la nube de Kael
                    </span>
                </h2>

                <p class="mt-5 text-slate-500 text-lg leading-8">
                    Acompañamos a dos tipos de negocio con soluciones hechas a su medida:
                    comercios que venden productos y talleres de motos que gestionan servicios.
                </p>

            </div>

            <div class="grid md:grid-cols-2 gap-6">

                <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-7 shadow-lg">

                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-white text-2xl shadow-lg mb-6">
                        <i class="fa-solid fa-shop"></i>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-3">
                        Negocios y comercios
                    </h3>

                    <p class="text-slate-500 leading-7 mb-6">
                        Tiendas, distribuidoras y negocios que venden productos y necesitan
                        controlar ventas, inventario, compras y facturación desde un solo lugar.
                    </p>

                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check text-blue-600"></i>
                            Punto de venta rápido y sencillo
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check text-blue-600"></i>
                            Control de stock e inventario
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check text-blue-600"></i>
                            Facturación electrónica SUNAT
                        </li>
                    </ul>

                    <a href="{{ route('central.planes') }}#planes-pos" class="inline-flex items-center gap-2 mt-8 text-blue-600 font-bold hover:gap-3 transition-all">
                        Ver planes Kael POS
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-7 shadow-2xl text-white">

                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-2xl shadow-lg mb-6">
                        <i class="fa-solid fa-motorcycle"></i>
                    </div>

                    <h3 class="text-2xl font-black mb-3">
                        Talleres de motos
                    </h3>

                    <p class="text-slate-300 leading-7 mb-6">
                        Talleres que quieren organizar sus mantenimientos, digitalizar su agenda
                        de reservas y, si lo necesitan, vender repuestos desde su propia web.
                    </p>

                    <ul class="space-y-3 text-sm text-slate-300">
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check text-cyan-400"></i>
                            Mantenimientos con checklist digital
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check text-cyan-400"></i>
                            Reservas online por bahía y turno
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check text-cyan-400"></i>
                            Web propia con historial por placa
                        </li>
                    </ul>

                    <a href="{{ route('central.planes') }}#planes-moto" class="inline-flex items-center gap-2 mt-8 text-cyan-300 font-bold hover:gap-3 transition-all">
                        Ver planes Kael Moto
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </div>

        </div>

    </section>
