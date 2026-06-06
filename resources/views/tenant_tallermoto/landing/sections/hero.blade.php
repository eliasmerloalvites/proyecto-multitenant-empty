<main class="relative min-h-screen overflow-hidden">

    <!-- Imagen de fondo -->
    <div class="absolute inset-0 z-0">
        <img
            src="{{ asset_root('landing_tallermoto/images/hero-moto.png') }}"
            class="w-full h-full object-cover"
            alt="">
    </div>

    <!-- Oscurecer fondo -->
    <div class="absolute inset-0 z-10 "></div>

    <!-- Gradiente lateral -->
    <div class="absolute inset-0 z-20
        bg-gradient-to-r
        from-slate-950 v">
    </div>

    <!-- Contenido -->
    <div class="relative z-30 max-w-7xl mx-auto px-6 py-24">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center w-full">
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 bg-blue-950/50 border border-blue-900/50 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-blue-400">
                    <i data-lucide="bike" class="w-3.5 h-3.5"></i> Taller especializado en motocicletas
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[52px] font-black tracking-tight text-white leading-[1.15]">
                    Mantenimiento profesional<br>
                    para tu moto <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-400 to-purple-500">
                        con tecnología inteligente
                    </span>
                </h1>

                <p class="text-gray-400 text-sm md:text-base max-w-xl leading-relaxed">
                    Reserva mantenimientos online, consulta tu historial por placa y realiza seguimiento a tus reparaciones en tiempo real.
                </p>

                <div class="grid grid-cols-3 gap-4 text-xs font-semibold text-gray-300 max-w-lg pt-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-950/40 border border-blue-900/30 flex items-center justify-center text-blue-400 shadow-sm"><i data-lucide="shield-check" class="w-4 h-4"></i></div>
                        <span class="text-[11px] leading-tight">Técnicos <br>certificados</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-950/40 border border-blue-900/30 flex items-center justify-center text-blue-400 shadow-sm"><i data-lucide="package" class="w-4 h-4"></i></div>
                        <span class="text-[11px] leading-tight">Repuestos <br>originales</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-950/40 border border-blue-900/30 flex items-center justify-center text-blue-400 shadow-sm"><i data-lucide="award" class="w-4 h-4"></i></div>
                        <span class="text-[11px] leading-tight">Garantía de <br>servicio</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-lg shadow-blue-600/30">
                        <i data-lucide="calendar" class="w-4 h-4"></i> Reservar Ahora
                    </a>
                    <a href="#buscar-placa" class="neon-border bg-slate-950/40 text-gray-300 hover:text-white px-8 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2 transition backdrop-blur-sm">
                        <i data-lucide="search" class="w-4 h-4"></i> Consultar Historial
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-6 max-w-sm border-t border-white/5">
                    <div>
                        <div class="text-2xl font-black text-white">+5000</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Motos Atendidas</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">+2000</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Clientes</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white flex items-center gap-1">4.9 <span class="text-blue-500 text-lg">★</span></div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Valoración</div>
                    </div>
                </div>

                <div class="pt-4 space-y-2">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-gray-600">Trabajamos con las mejores marcas</p>
                    <div class="flex flex-wrap items-center gap-5 opacity-25 grayscale text-xs font-black tracking-tighter text-white">
                        <span>YAMAHA</span> <span>HONDA</span> <span>BAJAJ</span> <span>KTM</span> <span>PULSAR</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="glass-panel p-5 rounded-2xl w-full max-w-sm shadow-[0_25px_60px_rgba(0,0,0,0.5)] space-y-4">
                    <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-white tracking-wide">
                            <i data-lucide="activity" class="w-4 h-4 text-blue-500"></i> Sistema Kael
                        </div>
                        <span class="flex items-center gap-1 text-[9px] bg-green-950/60 text-green-400 px-2.5 py-0.5 rounded-full font-bold border border-green-900/50">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> En tiempo real
                        </span>
                    </div>
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i> Reservas Hoy</span>
                            <span class="font-bold text-white text-sm">24</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="wrench" class="w-4 h-4 text-blue-500"></i> Mantenimientos</span>
                            <span class="font-bold text-white text-sm">182</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 text-blue-500"></i> Clientes Activos</span>
                            <span class="font-bold text-white text-sm">1,247</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-white/5">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="smile" class="w-4 h-4 text-green-400"></i> Satisfacción</span>
                            <span class="font-black text-green-400 text-sm">98%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>