<main class="relative min-h-screen overflow-hidden">
    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">
        <section id="nosotros-main" class="py-24 relative overflow-hidden border-b {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-200' }}">
            <div class="absolute top-1/2 -right-60 w-[500px] h-[500px] bg-brand-600/5 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-0 -left-40 w-96 h-96 bg-purple-600/5 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-24">
                    <div class="lg:col-span-6 space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 border rounded-full text-[10px] font-black uppercase tracking-widest
                            {{ $colorview == 'dark' ? 'bg-brand-950/50 border-brand-500/30 text-brand-400' : 'bg-red-50 border-red-200 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $colorview == 'dark' ? 'bg-brand-400' : 'bg-red-500' }}"></span>
                            ADN KAEL Technologies
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black tracking-tight {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }} uppercase leading-none">
                            No solo reparamos, <br>
                            <span class="bg-gradient-to-r {{ $colorview == 'dark' ? 'from-brand-500 to-brand-600' : 'from-brand-500 to-brand-400' }} bg-clip-text text-transparent">
                                Calibramos tu Pasión
                            </span>
                        </h2>
                        <div class="space-y-4 text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-600' }} leading-relaxed">
                            <p>
                                Nacimos bajo la premisa de revolucionar el servicio técnico de motocicletas. En 
                                <span class="{{ $colorview == 'dark' ? 'text-white' : 'text-slate-900' }} font-bold">KAEL</span>, 
                                entendemos que tu moto no es solo un medio de transporte, sino una pieza de ingeniería de precisión que merece un trato científico.
                            </p>
                            <p>
                                Fusionamos la experiencia de mecánicos certificados con herramientas de diagnóstico digital de última generación (OBD Multi-marca), garantizando que cada ajuste responda exactamente a las especificaciones de los manuales de taller de fábrica.
                            </p>
                        </div>

                        <div class="pt-2">
                            <blockquote class="border-l-2 {{ $colorview == 'dark' ? 'border-brand-500 text-gray-500' : 'border-red-500 text-slate-500' }} pl-4 text-[11px] italic">
                                "La diferencia entre un trabajo común y la excelencia mecánica radica en los decimales de calibración."
                            </blockquote>
                        </div>
                    </div>

                    <div class="lg:col-span-6 relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r {{ $colorview == 'dark' ? 'from-brand-500/10 to-brand-500/10' : 'from-red-500/5 to-red-500/5' }} rounded-3xl blur-md opacity-70 group-hover:opacity-100 transition duration-500"></div>
                        <div class="relative border rounded-3xl p-8 h-80 flex flex-col justify-between overflow-hidden backdrop-blur-md transition-all
                            {{ $colorview == 'dark' ? 'bg-slate-950/60 border-white/10' : 'bg-white border-slate-200 shadow-sm' }}">
                            <div class="flex items-start justify-between">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center border
                                    {{ $colorview == 'dark' ? 'bg-brand-950/40 border-brand-900/50 text-brand-400' : 'bg-red-50 border-red-200 text-red-500' }}">
                                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                                </div>
                                <span class="font-mono text-[10px] {{ $colorview == 'dark' ? 'text-gray-600' : 'text-slate-400' }} font-black">EST. 2026</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-mono font-bold tracking-widest uppercase {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}">
                                    Certificación Internacional
                                </span>
                                <h3 class="text-lg font-black uppercase mt-1 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">
                                    Estándares de Grado de Competición
                                </h3>
                                <p class="text-[11px] {{ $colorview == 'dark' ? 'text-gray-500' : 'text-slate-500' }} mt-2 leading-snug">
                                    Nuestro equipo pasa por auditorías técnicas constantes para dominar los nuevos sistemas de inyección electrónica, control de tracción y frenado ABS.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-24">
                    <div class="border p-6 rounded-2xl text-center backdrop-blur-sm shadow-xs transition-all
                        {{ $colorview == 'dark' ? 'bg-slate-950/30 border-white/5' : 'bg-white border-slate-100' }}">
                        <span class="block text-3xl font-black font-mono tracking-tighter italic {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">+1,200</span>
                        <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Motos Diagnosticadas</span>
                    </div>
                    <div class="border p-6 rounded-2xl text-center backdrop-blur-sm shadow-xs transition-all
                        {{ $colorview == 'dark' ? 'bg-slate-950/30 border-white/5 text-brand-400' : 'bg-white border-slate-100 text-red-500' }}">
                        <span class="block text-3xl font-black font-mono tracking-tighter italic">99.4%</span>
                        <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Índice de Precisión</span>
                    </div>
                    <div class="border p-6 rounded-2xl text-center backdrop-blur-sm shadow-xs transition-all
                        {{ $colorview == 'dark' ? 'bg-slate-950/30 border-white/5' : 'bg-white border-slate-100' }}">
                        <span class="block text-3xl font-black font-mono tracking-tighter italic {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">25 Puntos</span>
                        <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">De Control por Inspección</span>
                    </div>
                    <div class="border p-6 rounded-2xl text-center backdrop-blur-sm shadow-xs transition-all
                        {{ $colorview == 'dark' ? 'bg-slate-950/30 border-white/5 text-purple-400' : 'bg-white border-slate-100 text-purple-600' }}">
                        <span class="block text-3xl font-black font-mono tracking-tighter italic">100%</span>
                        <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Garantía Escrita</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="text-center md:text-left">
                        <h4 class="text-xs font-black uppercase tracking-widest {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-400' }}">
                            Por qué confiar en nuestro laboratorio
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div class="border p-6 rounded-2xl space-y-3 transition-all group
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5 hover:border-brand-500/20' : 'bg-white border-slate-200 hover:border-red-500/20 shadow-xs' }}">
                            <div class="w-10 h-10 border rounded-xl flex items-center justify-center transition-all 
                                {{ $colorview == 'dark' ? 'bg-slate-900 border-white/5 text-brand-400 group-hover:bg-brand-600 group-hover:text-white' : 'bg-slate-50 border-slate-200 text-red-500 group-hover:bg-red-500 group-hover:text-white group-hover:border-transparent' }}">
                                <i data-lucide="cpu" class="w-5 h-5"></i>
                            </div>
                            <h5 class="text-xs font-black uppercase {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">Tecnología de Diagnóstico</h5>
                            <p class="text-[11px] {{ $colorview == 'dark' ? 'text-gray-500' : 'text-slate-500' }} leading-relaxed">
                                Olvídate de las reparaciones basadas en adivinanzas. Utilizamos escáneres OBD de lectura profunda para mapear los sensores y la computadora de tu motocicleta al instante.
                            </p>
                        </div>

                        <div class="border p-6 rounded-2xl space-y-3 transition-all group
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5 hover:border-brand-500/20' : 'bg-white border-slate-200 hover:border-red-500/20 shadow-xs' }}">
                            <div class="w-10 h-10 border rounded-xl flex items-center justify-center transition-all 
                                {{ $colorview == 'dark' ? 'bg-slate-900 border-white/5 text-brand-400 group-hover:bg-brand-600 group-hover:text-white' : 'bg-slate-50 border-slate-200 text-red-500 group-hover:bg-red-500 group-hover:text-white group-hover:border-transparent' }}">
                                <i data-lucide="history" class="w-5 h-5"></i>
                            </div>
                            <h5 class="text-xs font-black uppercase {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">Trazabilidad Total</h5>
                            <p class="text-[11px] {{ $colorview == 'dark' ? 'text-gray-500' : 'text-slate-500' }} leading-relaxed">
                                Guardamos un registro clínico milimétrico de cada perno ajustado y fluido reemplazado. Puedes auditar la salud de tu unidad desde nuestra plataforma web las 24 horas.
                            </p>
                        </div>

                        <div class="border p-6 rounded-2xl space-y-3 transition-all group
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5 hover:border-brand-500/20' : 'bg-white border-slate-200 hover:border-red-500/20 shadow-xs' }}">
                            <div class="w-10 h-10 border rounded-xl flex items-center justify-center transition-all 
                                {{ $colorview == 'dark' ? 'bg-slate-900 border-white/5 text-brand-400 group-hover:bg-brand-600 group-hover:text-white' : 'bg-slate-50 border-slate-200 text-red-500 group-hover:bg-red-500 group-hover:text-white group-hover:border-transparent' }}">
                                <i data-lucide="award" class="w-5 h-5"></i>
                            </div>
                            <h5 class="text-xs font-black uppercase {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">Insumos de Primera Línea</h5>
                            <p class="text-[11px] {{ $colorview == 'dark' ? 'text-gray-500' : 'text-slate-500' }} leading-relaxed">
                                No comprometemos el rendimiento. Trabajamos exclusivamente con marcas globales líderes en lubricación y componentes de fricción para mantener intacto tu motor.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </section>
    </div>
</main>