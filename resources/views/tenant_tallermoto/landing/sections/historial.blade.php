<!-- Oscurecer fondo
    <div class="absolute inset-0 z-10 "></div>-->

<!-- Gradiente lateral -->
<main class="relative  overflow-hidden">
    {{-- <div class="absolute inset-0 z-20
        bg-gradient-to-l
        from-slate-700 v ">
    </div> --}}

    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">

        <section id="historial-consulta" class="py-20 relative overflow-hidden">
            <div @class([
                'absolute top-1/3 left-1/2 -translate-x-1/2 w-[600px] h-[600px] rounded-full blur-[150px] pointer-events-none',
                'bg-brand-500/5' => $colorview == 'dark',
                'bg-brand-500/10' => $colorview !== 'dark',
            ])></div>

            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="text-center max-w-2xl mx-auto mb-12 space-y-3">
                    <div @class([
                        'inline-flex items-center gap-2 px-3 py-1 border rounded-full text-[10px] font-bold uppercase tracking-widest text-brand-400',
                        'bg-brand-950/40 border-brand-500/20' => $colorview == 'dark',
                        'bg-brand-500/10 border-brand-500/20' => $colorview !== 'dark',
                    ])>
                        <i data-lucide="database" class="w-3 h-3"></i> Central de Datos KAEL Cloud
                    </div>
                    <h2
                        class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-3xl font-black uppercase tracking-tight">
                        Historial Clínico <span class="text-brand-500">Digital</span>
                    </h2>
                    <p class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-xs">
                        Ingresa el número de placa de tu motocicleta para verificar el historial de mantenimiento
                        oficial, estado de componentes y descargar tus reportes.
                    </p>
                </div>

                <div class="max-w-md mx-auto mb-16">
                    <div @class([
                        'backdrop-blur-md p-4 rounded-3xl border shadow-2xl relative group',
                        'bg-slate-950/60 border-white/10' => $colorview == 'dark',
                        'bg-white border-gray-200' => $colorview !== 'dark',
                    ])>
                        @if ($colorview == 'dark')
                            <div
                                class="absolute -inset-px bg-gradient-to-r from-brand-500/20 to-brand-500/20 rounded-3xl blur opacity-30 group-hover:opacity-60 transition duration-500">
                            </div>
                        @endif

                        <div class="relative flex gap-2">
                            <div class="relative flex-1">
                                <div
                                    class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </div>
                                <input type="text" placeholder="EJEM: 1234-5X" @class([
                                    'w-full border focus:border-brand-500/50 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-mono font-bold uppercase placeholder-gray-500 focus:outline-none focus:ring-0 transition-all',
                                    'bg-slate-900 border-white/5 text-white' => $colorview == 'dark',
                                    'bg-gray-50 border-gray-200 text-gray-900' => $colorview !== 'dark',
                                ])>
                            </div>
                            <button
                                class="bg-brand-500 hover:bg-brand-400 text-white px-5 rounded-2xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-brand-500/20 flex items-center gap-1.5 shrink-0">
                                Consultar <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <div class="lg:col-span-7 space-y-6">

                        <div @class([
                            'backdrop-blur-md border rounded-3xl p-6 shadow-xl relative overflow-hidden',
                            'bg-slate-950/40 border-white/5' => $colorview == 'dark',
                            'bg-white border-gray-200' => $colorview !== 'dark',
                        ])>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 rounded-full blur-2xl"></div>

                            <div @class([
                                'flex flex-wrap items-center justify-between gap-4 border-b pb-4 mb-6',
                                'border-white/5' => $colorview == 'dark',
                                'border-gray-100' => $colorview !== 'dark',
                            ])>
                                <div class="flex items-center gap-3">
                                    <div @class([
                                        'w-12 h-12 border text-brand-400 rounded-2xl flex items-center justify-center',
                                        'bg-brand-500/10 border-brand-500/30' => $colorview == 'dark',
                                        'bg-brand-500/5 border-brand-500/20' => $colorview !== 'dark',
                                    ])>
                                        <i data-lucide="check-square" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span
                                            class="text-[9px] text-emerald-500 font-bold uppercase bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded">
                                            Servicio Completado
                                        </span>
                                        <h3
                                            class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-sm font-black uppercase tracking-wide mt-1">
                                            Mantenimiento Preventivo Full
                                        </h3>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[9px] text-gray-400 font-bold uppercase">Código de
                                        Orden</span>
                                    <span
                                        class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-900' }} text-xs font-mono font-bold">#OR-2026-894</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                                <div @class([
                                    'p-3 rounded-2xl border',
                                    'bg-slate-900/50 border-white/5' => $colorview == 'dark',
                                    'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                                ])>
                                    <span class="block text-[9px] text-gray-400 font-bold uppercase">Fecha de
                                        Salida</span>
                                    <span
                                        class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} text-xs font-mono font-medium">14/05/2026</span>
                                </div>
                                <div @class([
                                    'p-3 rounded-2xl border',
                                    'bg-slate-900/50 border-white/5' => $colorview == 'dark',
                                    'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                                ])>
                                    <span class="block text-[9px] text-gray-400 font-bold uppercase">Kilometraje</span>
                                    <span class="text-xs text-brand-500 font-mono font-black">12,450 Km</span>
                                </div>
                                <div @class([
                                    'p-3 rounded-2xl border col-span-2 sm:col-span-1',
                                    'bg-slate-900/50 border-white/5' => $colorview == 'dark',
                                    'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                                ])>
                                    <span class="block text-[9px] text-gray-400 font-bold uppercase">Técnico
                                        Asignado</span>
                                    <span
                                        class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} text-xs font-medium">Mec.
                                        Carlos R.</span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <h4
                                    class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} text-xs font-bold uppercase tracking-wider">
                                    Operaciones Ejecutadas:
                                </h4>
                                <div @class([
                                    'grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]',
                                    'text-gray-400' => $colorview == 'dark',
                                    'text-gray-600' => $colorview !== 'dark',
                                ])>
                                    @php
                                        $operaciones = [
                                            'Cambio de Aceite 10W40 Sintético',
                                            'Limpieza de Inyectores con Ultrasonido',
                                            'Calibración y Tensión de Cadena',
                                            'Diagnóstico OBD (Cero Errores)',
                                        ];
                                    @endphp
                                    @foreach ($operaciones as $op)
                                        <div @class([
                                            'flex items-center gap-2 px-3 py-2 rounded-xl',
                                            'bg-slate-900/30' => $colorview == 'dark',
                                            'bg-gray-50' => $colorview !== 'dark',
                                        ])>
                                            <i data-lucide="check" class="w-3.5 h-3.5 text-brand-500"></i>
                                            {{ $op }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div @class([
                                'mt-6 pt-5 border-t flex flex-col sm:flex-row items-center justify-between gap-4',
                                'border-white/5' => $colorview == 'dark',
                                'border-gray-100' => $colorview !== 'dark',
                            ])>
                                <div class="text-center sm:text-left">
                                    <span class="text-[10px] text-gray-400 font-medium block">¿Necesitas el informe
                                        físico de la inspección?</span>
                                </div>
                                <a href="#" @class([
                                    'w-full sm:w-auto inline-flex items-center justify-center gap-2 border px-5 py-3 rounded-xl text-xs font-bold transition-all group',
                                    'bg-slate-900 hover:bg-slate-800 border-white/10 text-gray-300 hover:text-white' =>
                                        $colorview == 'dark',
                                    'bg-gray-100 hover:bg-gray-200 border-gray-200 text-gray-700 hover:text-gray-900' =>
                                        $colorview !== 'dark',
                                ])>
                                    <i data-lucide="file-text"
                                        class="w-4 h-4 text-red-500 group-hover:scale-110 transition-transform"></i>
                                    Descargar Reporte Técnico .PDF
                                </a>
                            </div>
                        </div>

                        <div @class([
                            'border rounded-3xl p-5 flex items-center gap-4',
                            'bg-brand-950/20 border-brand-500/20' => $colorview == 'dark',
                            'bg-brand-500/5 border-brand-500/20' => $colorview !== 'dark',
                        ])>
                            <div
                                class="w-10 h-10 bg-brand-500/10 text-brand-500 rounded-xl flex items-center justify-center shrink-0">
                                <i data-lucide="bell" class="w-5 h-5 animate-bounce"></i>
                            </div>
                            <div>
                                <h5
                                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-bold uppercase">
                                    Próximo Escaneo Sugerido
                                </h5>
                                <p
                                    class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-[11px] mt-0.5">
                                    Se proyecta tu siguiente revisión preventiva a los <span
                                        class="text-brand-500 font-mono font-bold">15,000 Km</span> o el <span
                                        class="text-brand-500 font-mono font-bold">14/08/2026</span>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 space-y-6">

                        <div @class([
                            'backdrop-blur-md border rounded-3xl p-6 shadow-xl space-y-6',
                            'bg-slate-950/40 border-white/5' => $colorview == 'dark',
                            'bg-white border-gray-200' => $colorview !== 'dark',
                        ])>
                            <div @class([
                                'border-b pb-3',
                                'border-white/5' => $colorview == 'dark',
                                'border-gray-100' => $colorview !== 'dark',
                            ])>
                                <h3
                                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="activity" class="w-4 h-4 text-brand-500"></i> Estado General del
                                    Vehículo
                                </h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Yamaha R3 • Placa:
                                    1234-5X</p>
                            </div>

                            <div class="space-y-4">
                                @php
                                    $componentes = [
                                        [
                                            'name' => 'Sistema de Frenado (Pastillas/Líquido)',
                                            'pct' => 90,
                                            'status' => 'Óptimo',
                                            'color' => 'bg-emerald-500',
                                            'text' => 'text-emerald-500',
                                        ],
                                        [
                                            'name' => 'Compresión y Lubricación de Motor',
                                            'pct' => 95,
                                            'status' => 'Excelente',
                                            'color' => 'bg-emerald-500',
                                            'text' => 'text-emerald-500',
                                        ],
                                        [
                                            'name' => 'Kit de Arrastre (Cadena/Catalina)',
                                            'pct' => 45,
                                            'status' => 'Desgaste Medio',
                                            'color' => 'bg-amber-500',
                                            'text' => 'text-amber-500',
                                        ],
                                        [
                                            'name' => 'Batería y Sistema Eléctrico Alterno',
                                            'pct' => 85,
                                            'status' => 'Estable',
                                            'color' => 'bg-emerald-500',
                                            'text' => 'text-emerald-500',
                                        ],
                                    ];
                                @endphp

                                @foreach ($componentes as $comp)
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between text-[11px] font-bold">
                                            <span
                                                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }}">{{ $comp['name'] }}</span>
                                            <span class="{{ $comp['text'] }}">{{ $comp['pct'] }}%
                                                ({{ $comp['status'] }})</span>
                                        </div>
                                        <div @class([
                                            'w-full h-2 rounded-full overflow-hidden p-0.5 border',
                                            'bg-slate-900 border-white/5' => $colorview == 'dark',
                                            'bg-gray-100 border-gray-200/60' => $colorview !== 'dark',
                                        ])>
                                            <div class="h-full {{ $comp['color'] }} rounded-full"
                                                style="width: {{ $comp['pct'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div @class([
                                'border p-4 rounded-2xl text-[11px] leading-relaxed',
                                'bg-slate-900/60 border-white/5 text-gray-400' => $colorview == 'dark',
                                'bg-amber-500/5 border-amber-500/20 text-gray-600' => $colorview !== 'dark',
                            ])>
                                <span class="block font-black uppercase text-[9px] text-amber-500 mb-1 tracking-wider">
                                    Observación del Taller:
                                </span>
                                Se detectó que la cadena del kit de arrastre se encuentra cerca del límite de su vida
                                útil operativa. Se recomienda programar un cambio de kit en la próxima visita para
                                evitar pérdidas de potencia en alta velocidad.
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>


    </div>
</main>

<script></script>
