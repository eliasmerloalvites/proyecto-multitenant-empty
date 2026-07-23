<main class="relative min-h-screen overflow-hidden">
    @php
        $fondo =
            $colorview == 'dark'
                ? 'landing_tallermoto/images/reserva-moto.png'
                : 'landing_tallermoto/images/reserva-moto-light.png';
    @endphp
    <div class="absolute inset-0 z-0">
        <img src="{{ asset_root($fondo) }}" class="w-full h-full object-cover" alt="">
    </div>

    <!-- Oscurecer fondo
    <div class="absolute inset-0 z-10 "></div>-->

    <!-- Gradiente lateral -->

    @if ($colorview === 'dark')
        <div class="absolute inset-0 z-20
        bg-gradient-to-l
        from-slate-950 v ">
        </div>
    @else
        <div class="absolute inset-0 z-20
        bg-gradient-to-t
        from-brand-300 h">
        </div>
    @endif

    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">

        <div id="info-reservas" class="space-y-8 pt-24">

            <div @class([
                'flex flex-col md:flex-row md:items-end justify-between gap-4 border-b pb-4',
                'border-white/5' => $colorview == 'dark',
                'border-gray-200' => $colorview !== 'dark',
            ])>
                <div class="space-y-2">
                    <div @class([
                        'inline-flex items-center gap-2 border px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400',
                        'bg-brand-950/40 border-brand-500/10' => $colorview == 'dark',
                        'bg-brand-500/10 border-brand-500/20' => $colorview !== 'dark',
                    ])>
                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i> Agendamiento Optimizado
                    </div>
                    <h2
                        class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-2xl sm:text-3xl font-black tracking-tight">
                        Reserva tu espacio en <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-brand-400">Tiempo
                            Real</span>
                    </h2>
                    <p class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-xs max-w-md">
                        Gestionamos nuestro taller mediante bahías de trabajo automatizadas para garantizar cero filas y
                        tiempos de espera mínimos.
                    </p>
                </div>

                <a href="#"
                    class="bg-brand-500 hover:bg-brand-400 text-white px-6 py-3.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition shadow-lg shadow-brand-500/20 group self-start md:self-end shrink-0">
                    Ir a Reservar <i data-lucide="arrow-right"
                        class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <div
                    class="lg:col-span-4 glass-panel rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden group">
                    <div
                        class="absolute -right-10 -bottom-10 w-32 h-32 bg-brand-500/10 rounded-full blur-2xl group-hover:bg-brand-500/20 transition-all">
                    </div>

                    <div class="space-y-4">
                        <div @class([
                            'w-10 h-10 rounded-xl border flex items-center justify-center text-brand-400',
                            'bg-brand-950/50 border-brand-500/10' => $colorview == 'dark',
                            'bg-brand-500/10 border-brand-500/20' => $colorview !== 'dark',
                        ])>
                            <i data-lucide="hourglass" class="w-5 h-5"></i>
                        </div>
                        <div class="space-y-2">
                            <h3
                                class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-base font-black tracking-wide">
                                Planificación Anticipada
                            </h3>
                            <p
                                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-xs leading-relaxed">
                                Para garantizar la disponibilidad de nuestros especialistas y la calibración de
                                herramientas de diagnóstico, las citas se habilitan con un máximo de <span
                                    class="text-brand-400 font-bold">1 semana de anticipación</span>.
                            </p>
                        </div>
                    </div>

                    <div @class([
                        'pt-6 mt-6 border-t flex items-center gap-3',
                        'border-white/5' => $colorview == 'dark',
                        'border-gray-100' => $colorview !== 'dark',
                    ])>
                        <div class="flex -space-x-2">
                            @php $dias = ['L', 'M', 'X', 'J', 'V', 'S']; @endphp
                            @foreach ($dias as $dia)
                                @if ($dia == 'X')
                                    <span
                                        class="w-6 h-6 rounded-full bg-brand-500 border {{ $colorview == 'dark' ? 'border-gray-950' : 'border-white' }} flex items-center justify-center text-[9px] font-bold text-white shadow-sm">X</span>
                                @else
                                    <span @class([
                                        'w-6 h-6 rounded-full border flex items-center justify-center text-[9px] font-bold',
                                        'bg-slate-800 border-slate-950 text-gray-400' => $colorview == 'dark',
                                        'bg-gray-100 border-white text-gray-500' => $colorview !== 'dark',
                                    ])>{{ $dia }}</span>
                                @endif
                            @endforeach
                        </div>
                        <span class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Ventana de 7 días
                            activa</span>
                    </div>
                </div>

                <div class="lg:col-span-4 glass-panel rounded-2xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">1. Elige tu Bahía</h3>
                        </div>
                        <span class="text-[10px] text-gray-400 font-mono">Módulos de Taller</span>
                    </div>

                    <div class="space-y-2.5">
                        <div @class([
                            'border rounded-xl p-3 flex items-center justify-between',
                            'border-brand-500/30 bg-brand-950/20' => $colorview == 'dark',
                            'border-brand-500/40 bg-brand-500/5' => $colorview !== 'dark',
                        ])>
                            <div class="flex items-center gap-3">
                                <i data-lucide="cpu" class="w-4 h-4 text-brand-400"></i>
                                <div>
                                    <div
                                        class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-bold">
                                        Bahía Alfa (Especializada)</div>
                                    <div
                                        class="{{ $colorview == 'dark' ? 'text-brand-400' : 'text-brand-500' }} text-[10px] font-medium">
                                        Motos Inyectadas / Electrónica</div>
                                </div>
                            </div>
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-brand-500 fill-brand-500/20"></i>
                        </div>

                        <div @class([
                            'border rounded-xl p-3 flex items-center justify-between opacity-60',
                            'border-white/5 bg-slate-900/30 text-gray-300' => $colorview == 'dark',
                            'border-gray-200 bg-gray-50 text-gray-700' => $colorview !== 'dark',
                        ])>
                            <div class="flex items-center gap-3">
                                <i data-lucide="wrench" class="w-4 h-4 text-gray-400"></i>
                                <div>
                                    <div class="text-xs font-bold">Bahía Beta (Mecánica)</div>
                                    <div class="text-[10px] text-gray-500">Motos Carburadas / Motores</div>
                                </div>
                            </div>
                        </div>

                        <div @class([
                            'border rounded-xl p-3 flex items-center justify-between opacity-60',
                            'border-white/5 bg-slate-900/30 text-gray-300' => $colorview == 'dark',
                            'border-gray-200 bg-gray-50 text-gray-700' => $colorview !== 'dark',
                        ])>
                            <div class="flex items-center gap-3">
                                <i data-lucide="activity" class="w-4 h-4 text-gray-400"></i>
                                <div>
                                    <div class="text-xs font-bold">Bahía Express</div>
                                    <div class="text-[10px] text-gray-500">Mantenimientos Preventivos Rápidos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 glass-panel rounded-2xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">2. Define el Bloque
                                Horario</h3>
                        </div>
                        <span class="text-[10px] text-gray-400 font-mono">Turnos Libres</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div @class([
                            'border p-2.5 rounded-xl text-center opacity-40',
                            'border-white/5 bg-slate-900/40' => $colorview == 'dark',
                            'border-gray-200 bg-gray-100' => $colorview !== 'dark',
                        ])>
                            <span class="block text-xs font-bold text-gray-400 line-through">08:00 AM</span>
                            <span class="text-[9px] text-red-500 font-semibold uppercase tracking-wider">Ocupado</span>
                        </div>
                        <div @class([
                            'border p-2.5 rounded-xl text-center shadow-md cursor-pointer',
                            'border-brand-500/30 bg-brand-950/20 shadow-brand-500/5' =>
                                $colorview == 'dark',
                            'border-brand-500/40 bg-brand-500/5 shadow-brand-500/10' =>
                                $colorview !== 'dark',
                        ])>
                            <span
                                class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} block text-xs font-bold">10:30
                                AM</span>
                            <span
                                class="text-[9px] text-brand-400 font-semibold uppercase tracking-wider">Disponible</span>
                        </div>
                        <div @class([
                            'border p-2.5 rounded-xl text-center shadow-sm cursor-pointer transition',
                            'border-white/5 bg-slate-900/40 hover:border-white/10' =>
                                $colorview == 'dark',
                            'border-gray-200 bg-white hover:border-gray-300 shadow-sm' =>
                                $colorview !== 'dark',
                        ])>
                            <span
                                class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} block text-xs font-bold">02:00
                                PM</span>
                            <span
                                class="text-[9px] text-emerald-500 font-semibold uppercase tracking-wider">Disponible</span>
                        </div>
                        <div @class([
                            'border p-2.5 rounded-xl text-center shadow-sm cursor-pointer transition',
                            'border-white/5 bg-slate-900/40 hover:border-white/10' =>
                                $colorview == 'dark',
                            'border-gray-200 bg-white hover:border-gray-300 shadow-sm' =>
                                $colorview !== 'dark',
                        ])>
                            <span
                                class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} block text-xs font-bold">04:30
                                PM</span>
                            <span
                                class="text-[9px] text-emerald-500 font-semibold uppercase tracking-wider">Disponible</span>
                        </div>
                    </div>

                    <div @class([
                        'border rounded-xl p-2.5 flex items-center gap-2.5',
                        'bg-slate-900/20 border-white/5' => $colorview == 'dark',
                        'bg-gray-50 border-gray-200' => $colorview !== 'dark',
                    ])>
                        <i data-lucide="info" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                        <p class="text-[10px] text-gray-500 leading-normal">
                            Cada bloque comprende 2 horas de atención dedicada exclusiva para tu moto.
                        </p>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mt-10">
                <div class="glass-panel rounded-2xl p-6">
                    <div class="text-xs uppercase text-gray-500">Bahías libres</div>
                    <div class="text-4xl font-black text-brand-400 mt-3">5</div>
                </div>

                <div class="glass-panel rounded-2xl p-6">
                    <div class="text-xs uppercase text-gray-500">Motos hoy</div>
                    <div class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-4xl font-black mt-3">
                        24</div>
                </div>

                <div class="glass-panel rounded-2xl p-6">
                    <div class="text-xs uppercase text-gray-500">Tiempo promedio</div>
                    <div class="text-4xl font-black text-emerald-400 mt-3">2.3h</div>
                </div>

                <div class="glass-panel rounded-2xl p-6">
                    <div class="text-xs uppercase text-gray-500">Clientes activos</div>
                    <div class="text-4xl font-black text-brand-400 mt-3">1247</div>
                </div>
            </div>

            <div class="glass-panel rounded-3xl p-8 mt-10">
                <div class="flex flex-wrap items-center gap-5">

                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 rounded-full bg-brand-500 flex items-center justify-center text-white">
                            <i data-lucide="clipboard-list"></i>
                        </div>
                        <span
                            class="text-xs mt-3 {{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} font-medium">Servicio</span>
                    </div>

                    <div class="flex-1 h-px {{ $colorview == 'dark' ? 'bg-white/10' : 'bg-gray-200' }}"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 rounded-full bg-brand-500 flex items-center justify-center text-white">
                            <i data-lucide="calendar-days"></i>
                        </div>
                        <span
                            class="text-xs mt-3 {{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} font-medium">Reserva</span>
                    </div>

                    <div class="flex-1 h-px {{ $colorview == 'dark' ? 'bg-white/10' : 'bg-gray-200' }}"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 rounded-full bg-brand-500 flex items-center justify-center text-white">
                            <i data-lucide="badge-check"></i>
                        </div>
                        <span
                            class="text-xs mt-3 {{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} font-medium">Confirmación</span>
                    </div>

                    <div class="flex-1 h-px {{ $colorview == 'dark' ? 'bg-white/10' : 'bg-gray-200' }}"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                            <i data-lucide="wrench"></i>
                        </div>
                        <span
                            class="text-xs mt-3 {{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} font-medium">Atención</span>
                    </div>

                </div>
            </div>

        </div>

    </div>
</main>
