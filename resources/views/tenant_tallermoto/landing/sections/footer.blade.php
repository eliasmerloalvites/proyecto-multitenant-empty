<footer @class([
    'w-full border-t mt-16 relative z-10 backdrop-blur-md transition-colors duration-300',
    'bg-slate-950/60 border-white/5' => $colorview == 'dark',
    'bg-gray-50 border-gray-200' => $colorview !== 'dark',
])>

    <div @class([
        'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-6 border-b',
        'border-white/5' => $colorview == 'dark',
        'border-gray-200' => $colorview !== 'dark',
    ])>
        <div class="flex items-start gap-3 group">
            <div @class([
                'p-2 rounded-lg border transition-all duration-300 shrink-0 group-hover:bg-brand-600 group-hover:text-white group-hover:border-brand-600',
                'bg-brand-950/40 text-brand-400 border-brand-900/30' =>
                    $colorview == 'dark',
                'bg-brand-50 text-brand-600 border-brand-100' => $colorview !== 'dark',
            ])>
                <i data-lucide="calendar-days" class="w-4 h-4"></i>
            </div>
            <div>
                <h4
                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-bold transition-colors">
                    Reserva Online</h4>
                <p
                    class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} text-[11px] mt-0.5 leading-normal">
                    Agenda tu cita en menos de 1 minuto en la app.</p>
            </div>
        </div>

        <div class="flex items-start gap-3 group">
            <div @class([
                'p-2 rounded-lg border transition-all duration-300 shrink-0 group-hover:bg-brand-600 group-hover:text-white group-hover:border-brand-600',
                'bg-brand-950/40 text-brand-400 border-brand-900/30' =>
                    $colorview == 'dark',
                'bg-brand-50 text-brand-600 border-brand-100' => $colorview !== 'dark',
            ])>
                <i data-lucide="file-text" class="w-4 h-4"></i>
            </div>
            <div>
                <h4
                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-bold transition-colors">
                    Historial Digital</h4>
                <p
                    class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} text-[11px] mt-0.5 leading-normal">
                    Consulta el avance clínico total de tu moto por placa.</p>
            </div>
        </div>

        <div class="flex items-start gap-3 group">
            <div @class([
                'p-2 rounded-lg border transition-all duration-300 shrink-0 group-hover:bg-brand-600 group-hover:text-white group-hover:border-brand-600',
                'bg-brand-950/40 text-brand-400 border-brand-900/30' =>
                    $colorview == 'dark',
                'bg-brand-50 text-brand-600 border-brand-100' => $colorview !== 'dark',
            ])>
                <i data-lucide="map-pin" class="w-4 h-4"></i>
            </div>
            <div>
                <h4
                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-bold transition-colors">
                    Seguimiento en Vivo</h4>
                <p
                    class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} text-[11px] mt-0.5 leading-normal">
                    Monitorea los procesos y fases del taller paso a paso.</p>
            </div>
        </div>

        <div class="flex items-start gap-3 group">
            <div @class([
                'p-2 rounded-lg border transition-all duration-300 shrink-0 group-hover:bg-brand-600 group-hover:text-white group-hover:border-brand-600',
                'bg-brand-950/40 text-brand-400 border-brand-900/30' =>
                    $colorview == 'dark',
                'bg-brand-50 text-brand-600 border-brand-100' => $colorview !== 'dark',
            ])>
                <i data-lucide="package" class="w-4 h-4"></i>
            </div>
            <div>
                <h4
                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-bold transition-colors">
                    Componentes Orgánicos</h4>
                <p
                    class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} text-[11px] mt-0.5 leading-normal">
                    Garantía de stock y repuestos 100% de fábrica.</p>
            </div>
        </div>

        <div class="flex items-start gap-3 group">
            <div @class([
                'p-2 rounded-lg border transition-all duration-300 shrink-0 group-hover:bg-brand-600 group-hover:text-white group-hover:border-brand-600',
                'bg-brand-950/40 text-brand-400 border-brand-900/30' =>
                    $colorview == 'dark',
                'bg-brand-50 text-brand-600 border-brand-100' => $colorview !== 'dark',
            ])>
                <i data-lucide="shield-check" class="w-4 h-4"></i>
            </div>
            <div>
                <h4
                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-xs font-bold transition-colors">
                    Garantía Certificada</h4>
                <p
                    class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} text-[11px] mt-0.5 leading-normal">
                    Toda calibración cuenta con respaldo técnico oficial.</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 md:grid-cols-12 gap-8">

        <div class="md:col-span-5 space-y-5">
            <div class="flex items-center gap-2">
                <span
                    class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-800' }} text-xl font-black tracking-wider flex items-center">
                    <span class="text-brand-500 font-extrabold mr-0.5">K</span>KAEL
                </span>
            </div>

            <p class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} text-xs max-w-sm leading-relaxed">
                Elevando el estándar técnico en mantenimiento automotriz de motocicletas. Infraestructura digital
                integrada para un control total de tu ruta.
            </p>

            <div class="pt-2">
                <a href="#libro-reclamaciones-modal" onclick="openReclamacionesModal()" @class([
                    'inline-flex items-center gap-3 border p-2.5 rounded-xl transition-all duration-300 group max-w-xs shadow-sm',
                    'bg-slate-900/60 border-white/5 hover:border-amber-500/40 hover:bg-slate-900' =>
                        $colorview == 'dark',
                    'bg-white border-gray-200 hover:border-amber-500/40 hover:bg-amber-50/10' =>
                        $colorview !== 'dark',
                ])>
                    <div @class([
                        'w-9 h-9 rounded-lg flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform border',
                        'bg-amber-950/40 border-amber-900/50 text-amber-400' =>
                            $colorview == 'dark',
                        'bg-amber-50 border-amber-200 text-amber-600' => $colorview !== 'dark',
                    ])>
                        <i data-lucide="book-open-check" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left">
                        <span class="block text-[10px] text-amber-500 font-black uppercase tracking-wider">Libro de
                            Reclamaciones</span>
                        <span
                            class="{{ $colorview == 'dark' ? 'text-gray-500' : 'text-gray-600' }} text-[9px] block leading-tight">Virtual
                            conforme a la normativa de protección al consumidor.</span>
                    </div>
                </a>
            </div>

            <div class="space-y-2 pt-2">
                <h5
                    class="{{ $colorview == 'dark' ? 'text-gray-600' : 'text-gray-400' }} text-[9px] font-bold uppercase tracking-wider">
                    Comunidad Kael</h5>
                <div class="flex items-center gap-2.5">
                    <a href="#" target="_blank" @class([
                        'w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 group border',
                        'bg-slate-900 border-white/5 text-gray-400 hover:bg-brand-600 hover:text-white hover:border-brand-400' =>
                            $colorview == 'dark',
                        'bg-white border-gray-200 text-gray-500 hover:bg-brand-600 hover:text-white hover:border-brand-600' =>
                            $colorview !== 'dark',
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="group-hover:scale-110 transition-transform">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                        </svg>
                    </a>

                    <a href="#" target="_blank" @class([
                        'w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 group border',
                        'bg-slate-900 border-white/5 text-gray-400 hover:bg-pink-600 hover:text-white hover:border-pink-400' =>
                            $colorview == 'dark',
                        'bg-white border-gray-200 text-gray-500 hover:bg-pink-600 hover:text-white hover:border-pink-600' =>
                            $colorview !== 'dark',
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="group-hover:scale-110 transition-transform">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                        </svg>
                    </a>

                    <a href="#" target="_blank" @class([
                        'w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 group border',
                        'bg-slate-900 border-white/5 text-gray-400 hover:bg-zinc-800 hover:text-white hover:border-cyan-400' =>
                            $colorview == 'dark',
                        'bg-white border-gray-200 text-gray-500 hover:bg-zinc-900 hover:text-white hover:border-zinc-900' =>
                            $colorview !== 'dark',
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="group-hover:scale-110 transition-transform">
                            <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
                        </svg>
                    </a>

                    <a href="#" target="_blank" @class([
                        'w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 group border',
                        'bg-slate-900 border-white/5 text-gray-400 hover:bg-red-600 hover:text-white hover:border-red-400' =>
                            $colorview == 'dark',
                        'bg-white border-gray-200 text-gray-500 hover:bg-red-600 hover:text-white hover:border-red-600' =>
                            $colorview !== 'dark',
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="group-hover:scale-110 transition-transform">
                            <path
                                d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17" />
                            <polygon points="10 15 15 12 10 9" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="md:col-span-3 space-y-3">
            <h4
                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-500' }} text-[11px] font-bold uppercase tracking-widest">
                Mapa del Sitio</h4>
            <ul @class([
                'text-xs space-y-2 font-medium transition-colors',
                'text-gray-500 hover:text-white' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ])>
                <li><a href="#"
                        class="{{ $colorview == 'dark' ? 'hover:text-white' : 'hover:text-gray-900' }} transition">Inicio
                        del Sistema</a></li>
                <li><a href="#servicios"
                        class="{{ $colorview == 'dark' ? 'hover:text-white' : 'hover:text-gray-900' }} transition">Servicios
                        de Taller</a></li>
                <li><a href="#info-reservas"
                        class="{{ $colorview == 'dark' ? 'hover:text-white' : 'hover:text-gray-900' }} transition">Estación
                        de Reservas</a></li>
                <li><a href="#catalogo"
                        class="{{ $colorview == 'dark' ? 'hover:text-white' : 'hover:text-gray-900' }} transition">Catálogo
                        de Repuestos</a></li>
            </ul>
        </div>

        <div class="md:col-span-4 space-y-3">
            <h4
                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-500' }} text-[11px] font-bold uppercase tracking-widest">
                Security & Support</h4>
            <ul @class([
                'text-xs space-y-2 font-medium transition-colors',
                'text-gray-500' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ])>
                <li><a href="#buscar-placa"
                        class="{{ $colorview == 'dark' ? 'hover:text-white' : 'hover:text-gray-900' }} transition flex items-center gap-1.5"><span
                            class="w-1 h-1 bg-brand-500 rounded-full"></span> Consultar Historial Clínico</a></li>
                <li><a href="#"
                        class="{{ $colorview == 'dark' ? 'hover:text-white' : 'hover:text-gray-900' }} transition">Términos
                        del Servicio Digital</a></li>
                <li><a href="#"
                        class="{{ $colorview == 'dark' ? 'hover:text-white' : 'hover:text-gray-900' }} transition">Políticas
                        de Privacidad de Datos</a></li>
                <li><a href="#libro-reclamaciones-modal" onclick="openReclamacionesModal()"
                        class="hover:text-amber-500 transition flex items-center gap-1"><i data-lucide="book-marked"
                            class="w-3.5 h-3.5"></i> Libro de Reclamaciones</a></li>
            </ul>
        </div>
    </div>

    <div @class([
        'border-t py-4 transition-colors duration-300',
        'bg-slate-950/80 border-white/5 text-gray-600' => $colorview == 'dark',
        'bg-gray-100 border-gray-200 text-gray-500' => $colorview !== 'dark',
    ])>
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-[10px] font-medium">
            <p>&copy; 2026 KAEL Inc. Todos los derechos reservados.</p>
            <p class="flex items-center gap-1">
                Powered by <span class="text-brand-500/80 font-bold">KAEL Engine</span>
            </p>
        </div>
    </div>

</footer>
