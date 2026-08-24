    <footer id="contacto"
        class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white">

        <!-- ========================================== -->
        <!-- EFECTOS -->
        <!-- ========================================== -->

        <div class="absolute inset-0 overflow-hidden pointer-events-none">

            <div
                class="absolute
            -top-52
            -left-44
            w-[650px]
            h-[650px]
            rounded-full
            bg-blue-500/10
            blur-[180px]">
            </div>

            <div
                class="absolute
            -bottom-52
            -right-44
            w-[650px]
            h-[650px]
            rounded-full
            bg-cyan-400/10
            blur-[180px]">
            </div>

        </div>

        <div class="relative">

            <!-- ========================================== -->
            <!-- CTA -->
            <!-- ========================================== -->

            <section class="max-w-7xl mx-auto px-6 pt-20">

                <div
                    class="relative overflow-hidden rounded-[36px]
                bg-gradient-to-r
                from-blue-600
                via-cyan-500
                to-blue-700
                px-10
                lg:px-16
                py-14
                shadow-[0_40px_100px_rgba(37,99,235,.35)]">

                    <!-- Glow -->

                    <div
                        class="absolute
                    -top-20
                    -right-10
                    w-72
                    h-72
                    rounded-full
                    bg-white/20
                    blur-[120px]">
                    </div>

                    <div
                        class="relative
                    grid
                    lg:grid-cols-[1fr_auto]
                    gap-6
                    items-center">

                        <!-- Texto -->

                        <div>

                            <span
                                class="inline-flex
                            items-center
                            gap-2
                            rounded-full
                            bg-white/15
                            px-5
                            py-2
                            text-sm
                            font-semibold
                            backdrop-blur-lg
                            mb-6">

                                🚀 Comienza hoy mismo

                            </span>

                            <h2 class="text-4xl lg:text-4xl font-black leading-tight">

                                Digitaliza tu empresa

                                <br>

                                en menos de

                                <span class="text-yellow-300">

                                    5 minutos

                                </span>

                            </h2>

                            <p
                                class="mt-6
                            max-w-2xl
                            text-blue-100
                            text-lg
                            leading-8">

                                Únete a cientos de empresas que ya administran
                                sus ventas, inventario y facturación desde
                                Kael Tech.

                            </p>

                        </div>

                        <!-- Botones -->

                        <div
                            class="flex
                        flex-col
                        sm:flex-row
                        gap-4">

                            <a href="{{ route('central.registro.show') }}"
                                class="px-8
                            py-4
                            rounded-xl
                            bg-white
                            text-blue-700
                            font-bold
                            text-center
                            hover:bg-slate-100
                            transition">

                                Crear mi empresa

                            </a>

                            <a href="#demos" data-open-demo
                                class="px-8
                            py-4
                            rounded-xl
                            border
                            border-white/20
                            bg-white/10
                            backdrop-blur-lg
                            font-bold
                            text-center
                            hover:bg-white/20
                            transition">

                                Ver demostración

                            </a>

                        </div>

                    </div>

                </div>

            </section>

            <!-- ========================================== -->
            <!-- FOOTER -->
            <!-- ========================================== -->

            <section class="max-w-7xl mx-auto px-6 py-14">

                <div class="grid lg:grid-cols-[1.5fr_1fr_1fr_1fr] gap-12">

                    <!-- ================================= -->
                    <!-- LOGO -->
                    <!-- ================================= -->

                    <div>

                        <div class="flex items-center gap-4 mb-6">

                            <div
                                class="w-16
                            h-16
                            rounded-3xl
                            bg-gradient-to-br
                            from-blue-600
                            to-cyan-500
                            p-[2px]
                            shadow-xl">

                                <div
                                    class="w-full
                                h-full
                                rounded-3xl
                                bg-white
                                flex
                                items-center
                                justify-center">

                                    <img src="{{ asset('images/icono.jpg') }}"
                                        class="w-12 h-12 object-contain rounded-xl">

                                </div>

                            </div>

                            <div>

                                <h3 class="text-3xl font-black">

                                    Kael Tech

                                </h3>

                                <p class="text-slate-400">

                                    Business Cloud Platform

                                </p>

                            </div>

                        </div>

                        <p
                            class="text-slate-400
                        leading-8
                        max-w-md
                        mb-4">

                            Plataforma SaaS diseñada para automatizar la gestión
                            de negocios modernos, con tecnología en la nube,
                            seguridad y crecimiento sin límites.

                        </p>

                        <p class="text-slate-500 text-sm mb-8">
                            KAEL DEL VALLE S.A.C. — RUC 20616106865
                        </p>

                        <!-- Redes -->

                        <div class="flex gap-4">

                            <a href="https://www.facebook.com/KaelTech/" target="_blank" rel="noopener"
                                class="w-12 h-12 rounded-xl
                            bg-white/5
                            border
                            border-white/10
                            flex
                            items-center
                            justify-center
                            hover:bg-blue-600
                            hover:border-blue-500
                            transition">

                                <i class="fab fa-facebook-f"></i>

                            </a>

                            <a href="https://www.tiktok.com/@datavalle" target="_blank" rel="noopener"
                                class="w-12 h-12 rounded-xl
                            bg-white/5
                            border
                            border-white/10
                            flex
                            items-center
                            justify-center
                            hover:bg-sky-500
                            transition">

                                <i class="fab fa-tiktok"></i>

                            </a>

                        </div>

                    </div>

                    <!-- ================================= -->
                    <!-- SOLUCIONES -->
                    <!-- ================================= -->

                    <div>

                        <h3 class="text-xl font-bold mb-6">

                            Soluciones

                        </h3>

                        <ul class="space-y-4">

                            <li>
                                <a href="#planes-pos" class="text-slate-400 hover:text-cyan-400 transition">
                                    Sistema POS
                                </a>
                            </li>

                            <li>
                                <a href="#planes-moto" class="text-slate-400 hover:text-cyan-400 transition">
                                    Talleres de Motos
                                </a>
                            </li>

                            <li class="flex items-center gap-2">
                                <span class="text-slate-500 cursor-default">Restaurantes</span>
                                <span class="text-[10px] font-bold tracking-wide px-2 py-0.5 rounded-full bg-white/5 text-slate-500 border border-white/10">PRÓXIMAMENTE</span>
                            </li>

                            <li class="flex items-center gap-2">
                                <span class="text-slate-500 cursor-default">Ferreterías</span>
                                <span class="text-[10px] font-bold tracking-wide px-2 py-0.5 rounded-full bg-white/5 text-slate-500 border border-white/10">PRÓXIMAMENTE</span>
                            </li>

                            <li class="flex items-center gap-2">
                                <span class="text-slate-500 cursor-default">Ópticas</span>
                                <span class="text-[10px] font-bold tracking-wide px-2 py-0.5 rounded-full bg-white/5 text-slate-500 border border-white/10">PRÓXIMAMENTE</span>
                            </li>

                        </ul>

                    </div>

                    <!-- ================================= -->
                    <!-- EMPRESA -->
                    <!-- ================================= -->

                    <div>

                        <h3 class="text-xl font-bold mb-6">

                            Empresa

                        </h3>

                        <ul class="space-y-4">

                            <li>
                                <a href="#inicio" class="text-slate-400 hover:text-cyan-400 transition">
                                    Inicio
                                </a>
                            </li>

                            <li>
                                <a href="#soluciones" class="text-slate-400 hover:text-cyan-400 transition">
                                    Soluciones
                                </a>
                            </li>

                            <li>
                                <a href="#planes-pos" class="text-slate-400 hover:text-cyan-400 transition">
                                    Planes
                                </a>
                            </li>

                            <li>
                                <a href="#clientes" class="text-slate-400 hover:text-cyan-400 transition">
                                    Clientes
                                </a>
                            </li>

                            <li>
                                <a href="#contacto" class="text-slate-400 hover:text-cyan-400 transition">
                                    Contacto
                                </a>
                            </li>

                        </ul>

                    </div>

                    <!-- ================================= -->
                    <!-- CONTACTO -->
                    <!-- ================================= -->

                    <div>

                        <h3 class="text-xl font-bold mb-6">

                            Contáctanos

                        </h3>

                        <div class="space-y-5">

                            <div class="flex items-start gap-4">

                                <div
                                    class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-phone text-cyan-400"></i>

                                </div>

                                <div>

                                    <p class="font-semibold">

                                        Teléfono

                                    </p>

                                    <p class="text-slate-400">

                                        +51 953 765 418

                                    </p>

                                </div>

                            </div>

                            <div class="flex items-start gap-4">

                                <div
                                    class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-envelope text-cyan-400"></i>

                                </div>

                                <div>

                                    <p class="font-semibold">

                                        Correo

                                    </p>

                                    <p class="text-slate-400">

                                        contacto@kael.pe

                                    </p>

                                </div>

                            </div>

                            <div class="flex items-start gap-4">

                                <div
                                    class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-location-dot text-cyan-400"></i>

                                </div>

                                <div>

                                    <p class="font-semibold">

                                        Ubicación

                                    </p>

                                    <p class="text-slate-400">

                                        Pacasmayo, La Libertad - Perú

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- ========================================== -->
                <!-- NEWSLETTER -->
                <!-- ========================================== -->

                <div
                    class="mt-12 rounded-[30px]
                border border-white/10
                bg-white/5
                backdrop-blur-xl
                p-7">

                    <div class="grid lg:grid-cols-[1fr_auto] gap-6 items-center">

                        <div>

                            <h3 class="text-3xl font-black mb-3">

                                Mantente informado

                            </h3>

                            <p class="text-slate-400 leading-8 max-w-2xl">

                                Recibe novedades, nuevas funcionalidades,
                                promociones y contenido exclusivo sobre Kael Tech.

                            </p>

                        </div>

                        <form class="flex flex-col sm:flex-row gap-4">

                            <input type="email" placeholder="Tu correo electrónico"
                                class="w-full sm:w-[320px]
                            px-5
                            py-4
                            rounded-xl
                            bg-slate-900/70
                            border
                            border-white/10
                            focus:border-cyan-400
                            outline-none
                            text-white
                            placeholder:text-slate-500">

                            <button
                                class="px-8
                            py-4
                            rounded-xl
                            bg-gradient-to-r
                            from-blue-600
                            to-cyan-500
                            hover:from-blue-700
                            hover:to-cyan-600
                            font-bold
                            shadow-xl
                            transition">

                                Suscribirme

                            </button>

                        </form>

                    </div>

                </div>

                <!-- ========================================== -->
                <!-- COPYRIGHT -->
                <!-- ========================================== -->

                <div class="border-t border-white/10 mt-10 pt-8">

                    <div class="flex flex-col lg:flex-row justify-between items-center gap-6">

                        <p class="text-slate-500 text-sm">

                            © 2026 <span class="text-white font-semibold">Kael Tech</span>.
                            Todos los derechos reservados.

                        </p>

                        <div class="flex flex-wrap justify-center gap-6 text-sm">

                            <a href="{{ route('central.privacidad') }}" class="text-slate-500 hover:text-cyan-400 transition">
                                Política de privacidad
                            </a>

                            <a href="{{ route('central.terminos') }}" class="text-slate-500 hover:text-cyan-400 transition">
                                Términos y condiciones
                            </a>

                            <a href="mailto:contacto@kael.pe" class="text-slate-500 hover:text-cyan-400 transition">
                                Soporte
                            </a>

                        </div>

                        <p class="text-slate-500 text-sm">

                            Desarrollado con

                            <span class="text-red-500">❤</span>

                            por <span class="font-semibold text-white">Kael Tech</span>

                        </p>

                    </div>

                </div>

            </section>

        </div>

    </footer>
