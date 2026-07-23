<main class="relative  overflow-hidden">
    @if ($colorview === 'dark')
        <div class="absolute inset-0 z-20
        bg-gradient-to-r
        from-slate-700 v">
        </div>
    @endif
    <div class="relative z-30 max-w-7xl mx-auto px-6 py-24">

        <div id="servicios" class="space-y-8 pt-6">
            <div class="text-center space-y-2">
                <div
                    class="inline-flex items-center gap-2 bg-brand-950/40 border border-brand-900/50 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400">
                    <i data-lucide="wrench" class="w-3.5 h-3.5"></i> Soluciones Especializadas
                </div>
                <h2
                    class="text-2xl sm:text-3xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-tight">
                    Nuestros <span class="text-brand-500">Servicios Profesionales</span>
                </h2>
                <p class="text-xs text-gray-400 max-w-md mx-auto">
                    Ofrecemos soporte técnico de alta precisión adaptado a la tecnología específica de tu motocicleta.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div
                    class="glass-panel rounded-2xl p-6 space-y-4 hover:border-brand-500/30 transition duration-300 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-brand-950/50 border border-brand-900/40 flex items-center justify-center text-brand-400 shadow-md">
                            <i data-lucide="shield-alert"
                                class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                        </div>
                        <h3
                            class="text-base font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-wide">
                            Mantenimiento General</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Desarmado integral, limpieza profunda y puesta a punto completa para asegurar el rendimiento
                            óptimo
                            del motor.
                        </p>

                        <div class="pt-2 space-y-2">
                            <div
                                class="flex items-center justify-between bg-slate-900/40 border border-white/5 rounded-xl p-2.5">
                                <span class="text-xs text-gray-300 font-medium">Motos Carburadas</span>
                                <span
                                    class="text-[11px] bg-brand-950 text-brand-400 font-bold px-2.5 py-0.5 rounded-md border border-brand-900/50">Limpieza
                                    Carb.</span>
                            </div>
                            <div
                                class="flex items-center justify-between bg-slate-900/40 border border-white/5 rounded-xl p-2.5">
                                <span class="text-xs text-gray-300 font-medium">Motos Inyectadas (FI)</span>
                                <span
                                    class="text-[11px] bg-indigo-950 text-indigo-400 font-bold px-2.5 py-0.5 rounded-md border border-indigo-900/50">Diagnóstico
                                    OBD</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/5 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-gray-500 font-semibold tracking-wide">Servicio Completo</span>
                        <a href="#" @class([
                            'border text-brand-400 hover:text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-300 group shrink-0',
                            // 🌙 ESTILOS PARA MODO OSCURO
                            'bg-brand-950/30 border-brand-500/20 hover:bg-brand-500 hover:border-brand-400 shadow-[0_2px_10px_rgba(59,130,246,0.05)] hover:shadow-lg hover:shadow-brand-500/30' =>
                                $colorview == 'dark',
                            // ☀️ ESTILOS PARA MODO CLARO
                            'bg-white border-gray-200 hover:bg-brand-500 hover:border-brand-500 shadow-sm hover:shadow-lg hover:shadow-brand-500/20' =>
                                $colorview !== 'dark',
                        ])>
                            Cotizar
                            <i data-lucide="chevron-right"
                                class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-2xl p-6 space-y-4 hover:border-brand-500/30 transition duration-300 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-brand-950/50 border border-brand-900/40 flex items-center justify-center text-brand-400 shadow-md">
                            <i data-lucide="sliders" class="w-5 h-5 group-hover:scale-11 transition-transform"></i>
                        </div>
                        <h3
                            class="text-base font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-wide">
                            Mantenimiento Preventivo</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Revisiones periódicas e inspecciones técnicas esenciales diseñadas para mitigar fallas
                            prematuras en
                            ruta.
                        </p>

                        <div class="pt-2 space-y-2">
                            <div
                                class="flex items-center justify-between bg-slate-900/40 border border-white/5 rounded-xl p-2.5">
                                <span class="text-xs text-gray-300 font-medium">Motos Carburadas</span>
                                <span
                                    class="text-[11px] bg-brand-950 text-brand-400 font-bold px-2.5 py-0.5 rounded-md border border-brand-900/50">Calibración</span>
                            </div>
                            <div
                                class="flex items-center justify-between bg-slate-900/40 border border-white/5 rounded-xl p-2.5">
                                <span class="text-xs text-gray-300 font-medium">Motos Inyectadas (FI)</span>
                                <span
                                    class="text-[11px] bg-indigo-950 text-indigo-400 font-bold px-2.5 py-0.5 rounded-md border border-indigo-900/50">Escaneo
                                    de Sensores</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/5 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-gray-500 font-semibold tracking-wide">Inspección Periódica</span>
                        <a href="#" @class([
                            'border text-brand-400 hover:text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-300 group shrink-0',
                            // 🌙 ESTILOS PARA MODO OSCURO
                            'bg-brand-950/30 border-brand-500/20 hover:bg-brand-500 hover:border-brand-400 shadow-[0_2px_10px_rgba(59,130,246,0.05)] hover:shadow-lg hover:shadow-brand-500/30' =>
                                $colorview == 'dark',
                            // ☀️ ESTILOS PARA MODO CLARO
                            'bg-white border-gray-200 hover:bg-brand-500 hover:border-brand-500 shadow-sm hover:shadow-lg hover:shadow-brand-500/20' =>
                                $colorview !== 'dark',
                        ])>
                            Cotizar
                            <i data-lucide="chevron-right"
                                class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-2xl p-6 space-y-4 hover:border-brand-500/30 transition duration-300 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-brand-950/50 border border-brand-900/40 flex items-center justify-center text-brand-400 shadow-md">
                            <i data-lucide="layers" class="w-5 h-5 group-hover:-rotate-12 transition-transform"></i>
                        </div>
                        <h3
                            class="text-base font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-wide">
                            Otras Actividades</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Reparaciones puntuales, mejoras de rendimiento y servicios técnicos complementarios a
                            demanda.
                        </p>

                        <div class="pt-2 grid grid-cols-2 gap-2 text-[11px] text-gray-300 font-medium">
                            <div class="flex items-center gap-1.5 bg-slate-900/40 border border-white/5 p-2 rounded-xl">
                                <i data-lucide="zap" class="w-3 h-3 text-brand-400"></i> Sistema Eléctrico
                            </div>
                            <div class="flex items-center gap-1.5 bg-slate-900/40 border border-white/5 p-2 rounded-xl">
                                <i data-lucide="disc" class="w-3 h-3 text-brand-400"></i> Frenos / Pastillas
                            </div>
                            <div class="flex items-center gap-1.5 bg-slate-900/40 border border-white/5 p-2 rounded-xl">
                                <i data-lucide="activity" class="w-3 h-3 text-brand-400"></i> Motor / Embrague
                            </div>
                            <div class="flex items-center gap-1.5 bg-slate-900/40 border border-white/5 p-2 rounded-xl">
                                <i data-lucide="orbit" class="w-3 h-3 text-brand-400"></i> Transmisión / Cadena
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/5 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-gray-500 font-semibold tracking-wide">Taller Correctivo</span>
                        <a href="#" @class([
                            'border px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-300 shadow-sm group shrink-0',
                            // 🌙 ESTILOS PARA MODO OSCURO
                            'bg-slate-900/50 border-white/10 text-gray-300 hover:bg-brand-500 hover:border-brand-400 hover:text-white hover:shadow-lg hover:shadow-brand-500/30' =>
                                $colorview == 'dark',
                            // ☀️ ESTILOS PARA MODO CLARO
                            'bg-white border-gray-200 text-gray-600 hover:bg-brand-500 hover:border-brand-500 hover:text-white hover:shadow-lg hover:shadow-brand-500/20' =>
                                $colorview !== 'dark',
                        ])>
                            Consultar
                            <i data-lucide="chevron-right"
                                class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>
