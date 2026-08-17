    <!-- HERO -->
    <!-- ======================================= -->
    <!-- HERO SECTION - KAEL SAAS -->
    <!-- ======================================= -->

    <section id="inicio" class="relative overflow-hidden bg-[#020817] min-h-screen flex items-center">

        <!-- EFECTOS -->
        <div class="absolute inset-0 overflow-hidden">

            <div class="absolute -top-40 -left-32 w-[650px] h-[650px] bg-blue-500/20 blur-[180px] rounded-full"></div>

            <div class="absolute -bottom-40 -right-32 w-[650px] h-[650px] bg-cyan-400/15 blur-[180px] rounded-full">
            </div>

            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[900px] bg-sky-400/5 blur-[220px] rounded-full">
            </div>
            <div class="absolute inset-0 opacity-[0.05]"
                style="
                background-image: radial-gradient(#ffffff 1px, transparent 1px);
                background-size: 20px 20px;
            ">
            </div>

        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-0 py-14 w-full">

            <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-6 items-center pt-8">

                <!-- LEFT -->
                <div class="max-w-xl">

                    <!-- BADGE -->
                    <div
                        class="inline-flex items-center gap-3
                            bg-white/10
                            border border-white/15
                            backdrop-blur-xl
                            px-5 py-2.5
                            rounded-full
                            text-white
                            shadow-lg">

                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>

                        <span class="font-semibold">
                            Plataforma SaaS para empresas
                        </span>

                    </div>

                    <!-- TITULO -->
                    <h1 class="text-3xl md:text-4xl xl:text-5xl font-black leading-[1.05] text-white mb-5 tracking-tight">

                        Crea y administra
                        <br>

                        tu negocio

                        <span
                            class="bg-gradient-to-r
                        from-blue-400
                        via-cyan-300
                        to-sky-500
                        bg-clip-text
                        text-transparent">

                            en minutos

                        </span>

                    </h1>

                    <!-- DESCRIPCION -->
                    <p class="text-slate-300 text-lg leading-8 max-w-xl">

                        Sistemas completos en la nube para tu negocio y
                        talleres de motos. Próximamente: restaurantes,
                        ferreterías, ópticas y más.

                    </p>

                    <!-- BOTONES -->
                    <div class="flex items-center gap-4 mb-8">

                        <!-- BTN -->
                        <a href="{{ route('central.registro.show') }}"
                            class="group px-8 py-4 rounded-xl font-bold text-white bg-gradient-to-r
                                from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 shadow-xl shadow-blue-600/30 transition-all duration-300
                                hover:-translate-y-1">

                            <span class="flex items-center gap-3">

                                Crear mi empresa ahora

                                <span class="group-hover:translate-x-1 transition">
                                    →
                                </span>

                            </span>

                        </a>

                        <!-- BTN -->
                        <a href="#demos"
                            class="group px-8 py-4 rounded-xl border border-white/15 
                                bg-white/5 hover:bg-white/10 backdrop-blur-xl text-white transition-all duration-300 hover:border-cyan-400/40">

                            <span class="flex items-center gap-3">

                                <div
                                    class="w-7 h-7 rounded-full border border-cyan-400/40
                                        bg-cyan-400/10 flex items-center justify-center">

                                    <i class="fa-solid fa-play text-cyan-400 text-xs"></i>

                                </div>

                                <span class="font-semibold">
                                    Ver demos en vivo
                                </span>

                            </span>

                        </a>

                    </div>

                    <!-- BENEFICIOS -->
                    <div class="flex flex-wrap gap-6">

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                <i class="fa-solid fa-circle-check text-cyan-400"></i>

                            </div>

                            Sin instalación

                        </div>

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                <i class="fa-solid fa-cloud text-cyan-400"></i>

                            </div>

                            En la nube

                        </div>

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                <i class="fa-solid fa-shield-halved text-cyan-400"></i>

                            </div>

                            Seguro y confiable

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="relative flex justify-center lg:justify-end">

                    <!-- WEB -->
                    <img src="{{ asset('/images/web/dashboard-web.png') }}"
                        class="
                        relative
                        z-10
                        w-full
                        max-w-[980px]
                        object-contain

                        rounded-[20px]

                        border
                        border-white/10

                        shadow-[0_40px_100px_rgba(0,0,0,.55)]

                        overflow-hidden
                    ">

                    <!-- MOBILE -->
                    <img src="{{ asset('/images/web/dashboard-mobile.png') }}"
                        class="
                        hidden
                        md:block
                        absolute
                        bottom-[-40px]
                        right-[-40px]
                        w-[120px]
                        xl:w-[130px]
                        z-20
                        rotate-[-2deg]

                        rounded-[20px]

                        shadow-[0_30px_80px_rgba(0,0,0,.65)]

                        animate-float
                    ">

                </div>

            </div>

        </div>

    </section>
