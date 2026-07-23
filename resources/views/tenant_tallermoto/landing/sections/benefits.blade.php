<!-- Oscurecer fondo
    <div class="absolute inset-0 z-10 "></div>-->

<!-- Gradiente lateral -->
<main class="relative  overflow-hidden">

    @if ($colorview == 'dark')
        <div class="absolute inset-0 z-20
        bg-gradient-to-l
        from-slate-700 v ">
        </div>
    @else
        <div class="absolute inset-0 z-20
        bg-gradient-to-t
        from-slate-100 h ">
        </div>
    @endif

    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">

        <div id="preview-historial" class="space-y-8 pt-6">
            <div class="text-center space-y-2">
                <div @class([
                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400 border',
                    'bg-brand-950/40 border-brand-900/50' => $colorview == 'dark',
                    'bg-brand-500/10 border-brand-500/20' => $colorview !== 'dark',
                ])>
                    <i data-lucide="folder-git-2" class="w-3.5 h-3.5"></i> Transparencia Total
                </div>
                <h2
                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-2xl sm:text-3xl font-black tracking-tight">
                    Tu Historial Clínico <span class="text-brand-500">Digital</span>
                </h2>
                <p class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-xs max-w-md mx-auto">
                    Con solo ingresar tu placa, accede al reporte técnico inmediato de las intervenciones y salud actual
                    de tu motocicleta.
                </p>
            </div>

            <div @class([
                'rounded-2xl p-6 max-w-4xl mx-auto relative overflow-hidden group shadow-2xl backdrop-blur-md border',
                'bg-slate-950/60 border-white/5 shadow-black/40' => $colorview == 'dark',
                'bg-white border-gray-200 shadow-gray-200/50' => $colorview !== 'dark',
            ])>
                <div class="absolute -left-16 -top-16 w-44 h-44 bg-brand-500/10 rounded-full blur-3xl"></div>

                <div @class([
                    'flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b pb-4 relative z-10',
                    'border-white/5' => $colorview == 'dark',
                    'border-gray-100' => $colorview !== 'dark',
                ])>
                    <div class="flex items-center gap-3.5">
                        <div
                            class="bg-gradient-to-r from-yellow-500 to-amber-400 text-slate-950 font-mono font-black text-sm px-3 py-1.5 rounded-lg border-2 border-amber-300 shadow-md tracking-wider">
                            MTX-987
                        </div>
                        <div>
                            <h3 class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-sm font-black">
                                Yamaha YZF-R3 (2024)
                            </h3>
                            <p class="text-[11px] text-gray-500">
                                Último ingreso registrado: <span
                                    class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-700' }}">Hace 12
                                    días</span>
                            </p>
                        </div>
                    </div>
                    <span
                        class="flex items-center gap-1.5 text-[10px] bg-emerald-500/10 text-emerald-500 px-3 py-1 rounded-full font-bold border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Estado: Óptimo en Ruta
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pt-5 relative z-10">

                    <div class="md:col-span-7 space-y-4">
                        <h4
                            class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-700' }} text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="clipboard-check" class="w-3.5 h-3.5 text-brand-500"></i> Último Diagnóstico
                            Aplicado
                        </h4>

                        <div @class([
                            'border rounded-xl p-4 space-y-3',
                            'bg-slate-900/40 border-white/5' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                        ])>
                            <div class="flex justify-between items-center text-xs">
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-700' }} font-medium">Mantenimiento
                                    General Realizado:</span>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} font-mono font-bold">15,400
                                    KM</span>
                            </div>

                            <div @class([
                                'grid grid-cols-2 gap-2 text-[11px]',
                                'text-gray-400' => $colorview == 'dark',
                                'text-gray-600' => $colorview !== 'dark',
                            ])>
                                @php
                                    $diagnosticos = [
                                        'Ajuste de Válvulas',
                                        'Limpieza de Inyectores',
                                        'Cambio de Aceite Sintético',
                                        'Sincronización Electrónica',
                                    ];
                                @endphp
                                @foreach ($diagnosticos as $diag)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="check"
                                            class="w-3.5 h-3.5 text-emerald-500 bg-emerald-500/10 rounded p-0.5 border border-emerald-500/20"></i>
                                        {{ $diag }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-5 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-[11px] font-semibold">
                                <span class="text-gray-400">Próximo Preventivo sugerido:</span>
                                <span class="text-brand-500 font-bold">En 2,600 KM</span>
                            </div>
                            <div @class([
                                'w-full h-2 rounded-full overflow-hidden border',
                                'bg-slate-900 border-white/5' => $colorview == 'dark',
                                'bg-gray-100 border-gray-200' => $colorview !== 'dark',
                            ])>
                                <div class="h-full bg-brand-500 rounded-full" style="width: 72%;"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center text-[10px] font-bold">
                            <div @class([
                                'border p-2 rounded-xl',
                                'bg-slate-900/40 border-white/5' => $colorview == 'dark',
                                'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                            ])>
                                <span class="block text-gray-400 uppercase tracking-wider mb-1">Frenos</span>
                                <span class="text-emerald-500">90% (OK)</span>
                            </div>
                            <div @class([
                                'border p-2 rounded-xl',
                                'bg-slate-900/40 border-white/5' => $colorview == 'dark',
                                'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                            ])>
                                <span class="block text-gray-400 uppercase tracking-wider mb-1">Fluidos</span>
                                <span class="text-emerald-500">100% (OK)</span>
                            </div>
                            <div @class([
                                'border p-2 rounded-xl',
                                'bg-amber-500/5 border-amber-500/20' => $colorview !== 'dark',
                                'bg-amber-950/10 border-amber-500/20' => $colorview == 'dark',
                            ])>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-500' }} block uppercase tracking-wider mb-1">Bujía</span>
                                <span class="text-amber-500">Cambio Próx.</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div @class([
                    'mt-6 pt-5 border-t flex flex-col md:flex-row items-center justify-between gap-4 relative z-10',
                    'border-white/5' => $colorview == 'dark',
                    'border-gray-100' => $colorview !== 'dark',
                ])>
                    <div class="space-y-1 text-center md:text-left">
                        <h5
                            class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} text-xs font-bold flex items-center justify-center md:justify-start gap-1.5">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brand-500"></i> ¿Quieres ver el estado
                            real de tu moto?
                        </h5>
                        <p class="text-[11px] text-gray-500">
                            Prueba nuestro sistema digital ingresando los dígitos de tu placa ahora mismo.
                        </p>
                    </div>

                    <a href="#buscar-placa"
                        class="w-full md:w-auto bg-brand-500 hover:bg-brand-400 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition-all duration-300 shadow-lg shadow-brand-500/20 hover:scale-[1.02] border border-brand-400/20 group shrink-0">
                        <i data-lucide="search"
                            class="w-4 h-4 text-white group-hover:scale-110 transition-transform"></i>
                        Consultar mi Placa Aquí
                        <i data-lucide="arrow-up"
                            class="w-4 h-4 text-white group-hover:-translate-y-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
