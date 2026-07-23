<main class="relative  overflow-hidden">

    @if ($colorview == 'dark')
        <div class="absolute inset-0 z-20
            bg-gradient-to-l
            from-slate-700 v ">
        </div>
    @else
        <div class="absolute inset-0 z-20
        bg-gradient-to-b
        from-slate-100 h ">
        </div>
    @endif

    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">

        <div id="contacto" class="space-y-8 pt-6">
            <div class="text-center space-y-2">
                <div @class([
                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400 border',
                    'bg-brand-950/40 border-brand-900/50' => $colorview == 'dark',
                    'bg-brand-500/10 border-brand-500/20' => $colorview !== 'dark',
                ])>
                    <i data-lucide="help-circle" class="w-3.5 h-3.5"></i> Canales de Atención
                </div>
                <h2
                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-2xl sm:text-3xl font-black tracking-tight">
                    ¿Tienes dudas? <span class="text-brand-500">Conéctate con el Taller</span>
                </h2>
                <p class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-xs max-w-md mx-auto">
                    Estamos listos para responder tus consultas sobre cotizaciones especiales, repuestos específicos o
                    soporte técnico.
                </p>
            </div>

            <div @class([
                'rounded-2xl p-6 md:p-8 max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 relative overflow-hidden border transition-all duration-300',
                'bg-slate-950/40 border-white/5 shadow-[0_30px_70px_rgba(0,0,0,0.4)]' =>
                    $colorview == 'dark',
                'bg-white border-gray-100 shadow-xl' => $colorview !== 'dark',
            ])>
                @if ($colorview == 'dark')
                    <div
                        class="absolute -right-20 -top-20 w-48 h-48 bg-brand-500/10 rounded-full blur-3xl pointer-events-none">
                    </div>
                @endif

                <div class="lg:col-span-5 space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <h3
                            class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-500' }} text-base font-black tracking-wide uppercase">
                            Datos Operativos
                        </h3>

                        <div class="space-y-3 text-xs">
                            <a href="https://wa.me/tu_numero" target="_blank" @class([
                                'flex items-center gap-3 border p-3 rounded-xl transition group',
                                'bg-slate-900/40 border-white/5 hover:border-brand-500/30' =>
                                    $colorview == 'dark',
                                'bg-gray-50 border-gray-100 hover:border-brand-500/30 hover:bg-brand-50/30' =>
                                    $colorview !== 'dark',
                            ])>
                                <div @class([
                                    'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border',
                                    'bg-emerald-950/50 border-emerald-900/40 text-emerald-400' =>
                                        $colorview == 'dark',
                                    'bg-emerald-50 border-emerald-200 text-emerald-600' =>
                                        $colorview !== 'dark',
                                ])>
                                    <i data-lucide="message-square" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 font-bold uppercase">Soporte
                                        Express</span>
                                    <span
                                        class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} font-semibold group-hover:text-emerald-500 transition-colors">
                                        +51 987 654 321
                                    </span>
                                </div>
                            </a>

                            <div @class([
                                'flex items-center gap-3 border p-3 rounded-xl',
                                'bg-slate-900/40 border-white/5' => $colorview == 'dark',
                                'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                            ])>
                                <div @class([
                                    'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border',
                                    'bg-brand-950/50 border-brand-900/40 text-brand-400' =>
                                        $colorview == 'dark',
                                    'bg-brand-50 border-brand-200 text-brand-600' => $colorview !== 'dark',
                                ])>
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 font-bold uppercase">Correo
                                        Electrónico</span>
                                    <span
                                        class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} font-semibold">soporte@kael.com</span>
                                </div>
                            </div>

                            <div @class([
                                'flex items-center gap-3 border p-3 rounded-xl',
                                'bg-slate-900/40 border-white/5' => $colorview == 'dark',
                                'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                            ])>
                                <div @class([
                                    'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border',
                                    'bg-brand-950/50 border-brand-900/40 text-brand-400' =>
                                        $colorview == 'dark',
                                    'bg-brand-50 border-brand-200 text-brand-600' => $colorview !== 'dark',
                                ])>
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 font-bold uppercase">Ubicación
                                        Central</span>
                                    <span
                                        class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} font-semibold">Av.
                                        Principal Tecno 452, Ciudad</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div @class([
                        'border rounded-xl p-4 space-y-2.5',
                        'bg-slate-950/50 border-white/5' => $colorview == 'dark',
                        'bg-gray-50/80 border-gray-100' => $colorview !== 'dark',
                    ])>
                        <h4
                            class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-700' }} text-[10px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-brand-500"></i> Horario de Bahías Abiertas
                        </h4>
                        <div class="space-y-1 text-[11px]">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Lunes a Viernes:</span>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} font-semibold">08:00
                                    AM - 06:00 PM</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Sábados:</span>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-gray-300' : 'text-gray-800' }} font-semibold">08:00
                                    AM - 01:00 PM</span>
                            </div>
                            <div class="flex justify-between text-red-500 font-medium">
                                <span>Domingos / Feriados:</span>
                                <span class="font-bold">Cerrado</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="#" method="POST" class="lg:col-span-7 space-y-4">
                    <h3
                        class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-500' }} text-base font-black tracking-wide uppercase">
                        Envía un Mensaje Directo
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Nombre
                                Completo</label>
                            <input type="text" name="nombre" required placeholder="Tu nombre"
                                @class([
                                    'w-full rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-brand-500 border transition',
                                    'bg-slate-900/50 border-white/10 text-gray-200 placeholder-gray-600' =>
                                        $colorview == 'dark',
                                    'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400' =>
                                        $colorview !== 'dark',
                                ])>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Número de
                                Celular</label>
                            <input type="tel" name="celular" placeholder="Ej: 987654321"
                                @class([
                                    'w-full rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-brand-500 border transition',
                                    'bg-slate-900/50 border-white/10 text-gray-200 placeholder-gray-600' =>
                                        $colorview == 'dark',
                                    'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400' =>
                                        $colorview !== 'dark',
                                ])>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Correo
                            Electrónico</label>
                        <input type="email" name="email" required placeholder="ejemplo@correo.com"
                            @class([
                                'w-full rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-brand-500 border transition',
                                'bg-slate-900/50 border-white/10 text-gray-200 placeholder-gray-600' =>
                                    $colorview == 'dark',
                                'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400' =>
                                    $colorview !== 'dark',
                            ])>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Detalle de tu
                            Consulta</label>
                        <textarea name="mensaje" rows="3" required placeholder="Describe brevemente qué necesitas saber o cotizar..."
                            @class([
                                'w-full rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-brand-500 border transition resize-none',
                                'bg-slate-900/50 border-white/10 text-gray-200 placeholder-gray-600' =>
                                    $colorview == 'dark',
                                'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400' =>
                                    $colorview !== 'dark',
                            ])></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" @class([
                            'w-full py-3 rounded-xl text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition-all duration-300 group border',
                            'bg-white hover:bg-gray-50 border-gray-200 text-gray-900 shadow-sm' =>
                                $colorview !== 'dark',
                            'bg-slate-900 hover:bg-slate-800 border-white/10 text-white shadow-[0_4px_20px_rgba(0,0,0,0.3)]' =>
                                $colorview == 'dark',
                        ])>
                            Enviar Mensaje
                            <i data-lucide="send" @class([
                                'w-3.5 h-3.5 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform',
                                'text-gray-500 group-hover:text-brand-500' => $colorview !== 'dark',
                                'text-brand-400 group-hover:text-brand-300' => $colorview == 'dark',
                            ])></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>
