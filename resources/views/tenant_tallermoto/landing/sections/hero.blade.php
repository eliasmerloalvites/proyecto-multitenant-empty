<main class="relative min-h-screen overflow-hidden">
    @php
        $fondo =
            $colorview == 'dark'
                ? 'landing_tallermoto/images/hero-moto.png'
                : 'landing_tallermoto/images/hero-moto-light.png';
    @endphp
    <!-- Imagen de fondo -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset_root($fondo) }}" class="w-full h-full object-cover" alt="">
    </div>

    <!-- Oscurecer fondo
    <div class="absolute inset-0 z-10 "></div>
-->
    <!-- Gradiente lateral -->
    @if ($colorview === 'dark')
        <div class="absolute inset-0 z-20
        bg-gradient-to-r
        from-slate-950 v">
        </div>
    @else
        <div class="absolute inset-0 z-20
        bg-gradient-to-t
        from-brand-300 h">
        </div>
    @endif


    <!-- Contenido -->
    <div class="relative z-30 max-w-7xl mx-auto px-6 py-24">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center w-full">
            <div class="lg:col-span-7 space-y-6">
                <div
                    class="inline-flex items-center gap-2 bg-brand-950/50 border border-brand-900/50 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400">
                    <i data-lucide="bike" class="w-3.5 h-3.5"></i> Taller especializado en motocicletas
                </div>

                <h1
                    class="text-4xl sm:text-5xl lg:text-[52px] font-black tracking-tight {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} leading-[1.15]">
                    Mantenimiento profesional<br>
                    para tu moto <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500  to-brand-400">
                        con tecnología inteligente
                    </span>
                </h1>

                <p class="text-base {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }}">
                    Reserva mantenimientos online, consulta tu historial por placa y realiza seguimiento a tus
                    reparaciones en tiempo real.
                </p>

                <div class="grid grid-cols-3 gap-4 text-xs font-semibold text-gray-300 max-w-lg pt-2">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-7 h-7 rounded-lg bg-brand-950/40 border border-brand-900/30 flex items-center justify-center text-brand-400 shadow-sm">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <span
                            class="text-[11px] leading-tight font-medium {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }}">
                            Técnicos <br>certificados
                        </span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-7 h-7 rounded-lg bg-brand-950/40 border border-brand-900/30 flex items-center justify-center text-brand-400 shadow-sm">
                            <i data-lucide="package" class="w-4 h-4"></i>
                        </div>
                        <span
                            class="text-[11px] leading-tight font-medium {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }}">
                            Repuestos <br>originales
                        </span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-7 h-7 rounded-lg bg-brand-950/40 border border-brand-900/30 flex items-center justify-center text-brand-400 shadow-sm">
                            <i data-lucide="award" class="w-4 h-4"></i>
                        </div>
                        <span
                            class="text-[11px] leading-tight font-medium {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }}">
                            Garantía de <br>servicio
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="#"
                        class="bg-brand-500 hover:bg-brand-400 text-white px-8 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-lg shadow-brand-500/20">
                        <i data-lucide="calendar" class="w-4 h-4"></i> Reservar Ahora
                    </a>

                    <a href="#buscar-placa" @class([
                        'neon-border px-8 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2 transition backdrop-blur-sm',
                        // 🌙 Modo Oscuro: Fondo de cristal tecnológico oscuro y texto claro
                        'bg-slate-950/40 text-gray-300 hover:text-white' => $colorview == 'dark',
                        // ☀️ Modo Claro: Fondo blanco limpio y texto gris oscuro para legibilidad
                        'bg-white text-gray-700 hover:text-brand-500 border border-gray-200' =>
                            $colorview !== 'dark',
                    ])>
                        <i data-lucide="search" class="w-4 h-4"></i> Consultar Historial
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-6 max-w-sm border-t border-white/5">
                    <div>
                        <div class="text-2xl font-black text-white">+5000</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Motos Atendidas
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">+2000</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Clientes</div>
                    </div>
                    <div>
                        <div
                            class="text-2xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} flex items-center gap-1">
                            4.9 <span class="text-brand-500 text-lg">★</span></div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Valoración
                        </div>
                    </div>
                </div>

                <div class="pt-4 space-y-2">
                    <p
                        class="text-[9px] font-bold uppercase tracking-widest {{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-500' }}">
                        Trabajamos con las mejores
                        marcas</p>
                    <div
                        class="flex flex-wrap items-center gap-5 opacity-25 grayscale text-xs font-black tracking-tighter {{ $colorview == 'dark' ? 'text-gray-600' : 'text-gray-400' }}">
                        <span class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600/70' }}">
                            YAMAHA
                        </span>
                        <span class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600/70' }}">
                            HONDA
                        </span>
                        <span class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600/70' }}">
                            BAJAJ
                        </span>
                        <span class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600/70' }}">
                            KTM
                        </span>
                        <span>PULSAR</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="glass-panel p-5 rounded-2xl w-full max-w-sm shadow-[0_25px_60px_rgba(0,0,0,0.5)] space-y-4">
                    <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
                        <div
                            class="flex items-center gap-1.5 text-xs font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-wide">
                            <i data-lucide="activity" class="w-4 h-4 text-brand-500"></i> Sistema Kael
                        </div>
                        <span
                            class="flex items-center gap-1 text-[9px] bg-green-950/60 text-green-400 px-2.5 py-0.5 rounded-full font-bold border border-green-900/50">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> En tiempo real
                        </span>
                    </div>
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="calendar"
                                    class="w-4 h-4 text-brand-500"></i> Reservas Hoy</span>
                            <span
                                class="font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-sm">24</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="wrench"
                                    class="w-4 h-4 text-brand-500"></i> Mantenimientos</span>
                            <span
                                class="font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-sm">182</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="users"
                                    class="w-4 h-4 text-brand-500"></i> Clientes Activos</span>
                            <span
                                class="font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-sm">1,247</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-white/5">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="smile"
                                    class="w-4 h-4 text-green-400"></i> Satisfacción</span>
                            <span class="font-black text-green-400 text-sm">98%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="buscar-placa"
            class="glass-panel rounded-2xl p-6 md:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center shadow-xl">

            <div class="lg:col-span-4 flex items-center gap-4">
                <div @class([
                    'p-3 text-brand-400 rounded-xl border shrink-0',
                    'bg-brand-950/40 border-brand-500/10' => $colorview == 'dark',
                    'bg-brand-500/10 border-brand-500/20' => $colorview !== 'dark',
                ])>
                    <i data-lucide="file-search" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold {{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }}">
                        Consulta tu historial por placa
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Revisa todos los mantenimientos anteriores, próximos servicios y más.
                    </p>
                </div>
            </div>

            <form action="#" method="GET" class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-12 gap-4 w-full">
                <div class="sm:col-span-9 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i data-lucide="bike" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="placa" placeholder="Ej: ABC123, BCD456"
                        @class([
                            'w-full pl-11 pr-4 py-3.5 border rounded-xl focus:outline-none focus:border-brand-500 transition font-mono uppercase tracking-wider text-xs',
                            // 🌙 Estilos Input Modo Oscuro
                            'bg-slate-950/50 border-white/10 text-gray-300 placeholder-gray-600' =>
                                $colorview == 'dark',
                            // ☀️ Estilos Input Modo Claro
                            'bg-white border-gray-300 text-gray-800 placeholder-gray-400 shadow-sm' =>
                                $colorview !== 'dark',
                        ])>
                </div>

                <button type="submit"
                    class="sm:col-span-3 bg-brand-500 hover:bg-brand-400 text-white font-bold py-3.5 px-6 rounded-xl transition flex items-center justify-center gap-2 text-xs shadow-lg shadow-brand-500/20">
                    <i data-lucide="search" class="w-4 h-4"></i> Buscar
                </button>
            </form>
        </div>

        <div @class([
            'grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-6 pt-6 border-t',
            'border-white/5' => $colorview == 'dark',
            'border-gray-200' => $colorview !== 'dark',
        ])>

            <div class="flex items-start gap-3">
                <i data-lucide="calendar-days" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold {{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }}">Reserva
                        Online</h4>
                    <p
                        class="text-[11px] mt-0.5 leading-normal {{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }}">
                        Agenda tu cita en menos de 1 minuto.</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <i data-lucide="file-text" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold {{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }}">
                        Historial Digital</h4>
                    <p
                        class="text-[11px] mt-0.5 leading-normal {{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }}">
                        Consulta todo el historial de tu moto.</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <i data-lucide="map-pin" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold {{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }}">
                        Seguimiento en tiempo real</h4>
                    <p
                        class="text-[11px] mt-0.5 leading-normal {{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }}">
                        Sigue el estado de tu reparación paso a paso.</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <i data-lucide="package" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold {{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }}">Catálogo
                        de Repuestos</h4>
                    <p
                        class="text-[11px] mt-0.5 leading-normal {{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }}">
                        Repuestos originales con la mejor calidad.</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <i data-lucide="shield-check" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold {{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }}">Garantía
                        Total</h4>
                    <p
                        class="text-[11px] mt-0.5 leading-normal {{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }}">
                        Todos nuestros servicios incluyen garantía.</p>
                </div>
            </div>

        </div>

    </div>

</main>
