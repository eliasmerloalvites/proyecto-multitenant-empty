<main class="relative  overflow-hidden">

    @if ($colorview == 'dark')
        <div class="absolute inset-0 z-20
        bg-gradient-to-r
        from-slate-700 v ">
        </div>
    @else
        <div class="absolute inset-0 z-20
        bg-gradient-to-b
        from-slate-100 h ">
        </div>
    @endif

    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">
        <div id="nosotros" class="space-y-8 pt-6">
            <div class="text-center space-y-2">
                <div
                    class="inline-flex items-center gap-2 bg-brand-950/40 border border-brand-900/50 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Respaldo de Marca
                </div>
                <h2
                    class="text-2xl sm:text-3xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-tight">
                    Ingeniería y Pasión por las <span class="text-brand-500">Dos Ruedas</span>
                </h2>
                <p class="text-xs text-gray-400 max-w-md mx-auto">
                    Conoce el estándar operativo y los valores que nos impulsan a dejar cada motocicleta en un estado
                    perfecto
                    de rendimiento.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                <div class="lg:col-span-5 space-y-5">
                    <h3
                        class="text-lg font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-wide">
                        No solo reparamos motos,<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-brand-400">
                            optimizamos tu experiencia en ruta.
                        </span>
                    </h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        En KAEL nacimos con el objetivo de romper el esquema del taller mecánico tradicional. Combinamos
                        mano de
                        obra altamente calificada con un ecosistema digital que te permite monitorear la salud de tu
                        vehículo de
                        forma transparente y sin sorpresas.
                    </p>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Creemos que tu seguridad no es negociable; por ello, cada calibración de inyección, cambio de
                        fluidos o
                        mantenimiento general se rige bajo estrictos manuales de fábrica y estándares internacionales.
                    </p>

                    <div class="flex items-center gap-3 bg-slate-950/40 border border-white/5 p-3 rounded-xl max-w-sm">
                        <i data-lucide="award" class="w-5 h-5 text-brand-400 shrink-0"></i>
                        <span class="text-[11px] text-gray-400 font-medium leading-tight">
                            Taller homologado y certificado en sistemas de inyección electrónica multimarca.
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div
                        class="glass-panel rounded-2xl p-5 space-y-3 hover:border-brand-500/20 transition-colors duration-300">
                        <div
                            class="w-8 h-8 rounded-lg bg-brand-950/50 border border-brand-900/40 flex items-center justify-center text-brand-400">
                            <i data-lucide="users-2" class="w-4 h-4"></i>
                        </div>
                        <h4
                            class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-wider">
                            Especialistas Certificados
                        </h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Mecánicos capacitados constantemente en las nuevas tecnologías FI (Fuel Injection) y
                            diagnósticos
                            avanzados OBD.
                        </p>
                    </div>

                    <div
                        class="glass-panel rounded-2xl p-5 space-y-3 hover:border-brand-500/20 transition-colors duration-300">
                        <div
                            class="w-8 h-8 rounded-lg bg-brand-950/50 border border-brand-900/40 flex items-center justify-center text-brand-400">
                            <i data-lucide="binary" class="w-4 h-4"></i>
                        </div>
                        <h4
                            class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-wider">
                            Tecnología de Diagnóstico
                        </h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Contamos con escáneres multimarca de última generación para leer mapas de motor, sensores de
                            oxígeno
                            y alertas del tablero.
                        </p>
                    </div>

                    <div
                        class="glass-panel rounded-2xl p-5 space-y-3 hover:border-brand-500/20 transition-colors duration-300">
                        <div
                            class="w-8 h-8 rounded-lg bg-brand-950/50 border border-brand-900/40 flex items-center justify-center text-brand-400">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </div>
                        <h4
                            class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-wider">
                            Historial 100% Transparente
                        </h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Cada repuesto retirado de tu motocicleta queda registrado en nuestra base de datos. Sin
                            cobros
                            fantasma ni cambios imprevistos.
                        </p>
                    </div>

                    <div
                        class="glass-panel rounded-2xl p-5 space-y-3 hover:border-brand-500/20 transition-colors duration-300">
                        <div
                            class="w-8 h-8 rounded-lg bg-brand-950/50 border border-brand-900/40 flex items-center justify-center text-brand-400">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <h4
                            class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-wider">
                            Tiempos Optimizados</h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Gracias a nuestro sistema de reservas por bahías de trabajo, tu moto entra directo a
                            revisión en el
                            bloque horario acordado.
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </div>
</main>
