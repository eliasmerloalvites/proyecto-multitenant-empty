<!-- Gradiente lateral -->
<main class="relative  overflow-hidden">
    {{-- <div class="absolute inset-0 z-20
        bg-gradient-to-l
        from-slate-700 v ">
    </div> --}}


    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">

        <section id="servicios" @class([
            'py-24 relative overflow-hidden border-b transition-colors duration-300',
            'bg-slate-950 border-white/5' => $colorview == 'dark',
            'bg-gray-50 border-gray-200' => $colorview !== 'dark',
        ])>

            <div @class([
                'absolute top-1/4 -left-40 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-opacity duration-300',
                'bg-brand-600/10' => $colorview == 'dark',
                'bg-brand-500/5 opacity-70' => $colorview !== 'dark',
            ])></div>
            <div @class([
                'absolute bottom-1/4 -right-40 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-opacity duration-300',
                'bg-purple-600/10' => $colorview == 'dark',
                'bg-purple-500/5 opacity-70' => $colorview !== 'dark',
            ])></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <div @class([
                        'inline-flex items-center gap-2 px-3 py-1 border rounded-full text-[11px] font-black uppercase tracking-widest transition-colors duration-300',
                        'bg-brand-950/50 border-brand-500/30 text-brand-400' =>
                            $colorview == 'dark',
                        'bg-brand-50 border-brand-200 text-brand-600' => $colorview !== 'dark',
                    ])>
                        <span @class([
                            'w-1.5 h-1.5 rounded-full animate-pulse',
                            'bg-brand-400' => $colorview == 'dark',
                            'bg-brand-500' => $colorview !== 'dark',
                        ])></span>
                        Ingeniería Mecánica de Precisión
                    </div>

                    <h2 @class([
                        'text-3xl sm:text-4xl font-black tracking-tight uppercase transition-colors duration-300',
                        'text-white' => $colorview == 'dark',
                        'text-gray-900' => $colorview !== 'dark',
                    ])>
                        Servicios Especializados <br class="hidden sm:block">
                        <span class="bg-gradient-to-r from-brand-500 to-brand-400 bg-clip-text text-transparent">para tu
                            Motocicleta</span>
                    </h2>

                    <p @class([
                        'text-sm leading-relaxed transition-colors duration-300',
                        'text-gray-400' => $colorview == 'dark',
                        'text-gray-600' => $colorview !== 'dark',
                    ])>
                        Combinamos hardware de diagnóstico avanzado con mano de obra certificada para garantizar el
                        máximo rendimiento, seguridad y durabilidad en cada ruta.
                    </p>
                </div>

                <div id="filter-buttons-container" class="flex flex-wrap items-center justify-center gap-2 mb-12">
                    <button data-category="all" @class([
                        'filter-btn px-5 py-2.5 text-xs font-bold rounded-xl transition-all duration-300 border',
                        'bg-brand-600 border-brand-500 text-white shadow-[0_4px_20px_rgba(37,99,235,0.3)]' =>
                            $colorview == 'dark',
                        'bg-gray-900 border-gray-900 text-white shadow-md' => $colorview !== 'dark',
                    ])>
                        Todos los Mantenimientos
                    </button>

                    <button data-category="preventivo" @class([
                        'filter-btn px-5 py-2.5 text-xs font-bold rounded-xl border transition-all duration-300',
                        'bg-slate-900/60 hover:bg-slate-900 text-gray-400 hover:text-white border-white/5 hover:border-brand-500/30' =>
                            $colorview == 'dark',
                        'bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-900 border-gray-200' =>
                            $colorview !== 'dark',
                    ])>
                        Preventivos
                    </button>

                    <button data-category="correctivo" @class([
                        'filter-btn px-5 py-2.5 text-xs font-bold rounded-xl border transition-all duration-300',
                        'bg-slate-900/60 hover:bg-slate-900 text-gray-400 hover:text-white border-white/5 hover:border-brand-500/30' =>
                            $colorview == 'dark',
                        'bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-900 border-gray-200' =>
                            $colorview !== 'dark',
                    ])>
                        Correctivos & Motor
                    </button>

                    <button data-category="electronica" @class([
                        'filter-btn px-5 py-2.5 text-xs font-bold rounded-xl border transition-all duration-300',
                        'bg-slate-900/60 hover:bg-slate-900 text-gray-400 hover:text-white border-white/5 hover:border-brand-500/30' =>
                            $colorview == 'dark',
                        'bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-900 border-gray-200' =>
                            $colorview !== 'dark',
                    ])>
                        Electrónica e Inyección
                    </button>
                </div>

                <div id="services-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-24">

    <div data-service-category="preventivo" @class([
        'service-card backdrop-blur-md border rounded-2xl p-6 transition-all duration-300 group flex flex-col justify-between hover:-translate-y-1',
        'bg-slate-950/40 border-white/5 hover:border-brand-500/30 shadow-[0_10px_30px_rgba(0,0,0,0.5)]' => $colorview == 'dark',
        'bg-white border-gray-200/60 hover:border-brand-500/50 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)]' => $colorview !== 'dark',
    ])>
        <div class="space-y-4">
            <div @class([
                'w-12 h-12 border rounded-xl flex items-center justify-center transition-all duration-300',
                'bg-brand-950/40 border-brand-900/50 group-hover:bg-brand-600 group-hover:border-brand-600' => $colorview == 'dark',
                'bg-brand-50 border-brand-100 group-hover:bg-brand-100 group-hover:border-brand-200' => $colorview !== 'dark',
            ])>
                <i data-lucide="wrench" @class([
                    'w-5 h-5 transition-colors duration-300',
                    'text-brand-400 group-hover:text-white' => $colorview == 'dark',
                    'text-brand-600 group-hover:text-brand-700' => $colorview !== 'dark',
                ])></i>
            </div>
            <div>
                <h3 @class([
                    'text-base font-bold transition-colors duration-300',
                    'text-white group-hover:text-brand-400' => $colorview == 'dark',
                    'text-gray-900 group-hover:text-brand-600' => $colorview !== 'dark',
                ])>Mantenimiento Preventivo Full</h3>
                <p @class([
                    'text-xs mt-1.5 leading-relaxed',
                    'text-gray-400' => $colorview == 'dark',
                    'text-gray-600' => $colorview !== 'dark',
                ])>Calibración completa del sistema. Incluye limpieza de inyectores/carburador, ajuste de cadena, revisión de fluidos y 25 puntos clave de seguridad.</p>
            </div>
            <ul @class([
                'space-y-2 text-[11px] font-medium pt-2',
                'text-gray-400' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ])>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Lavado técnico premium incluido</li>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Inspección digital con reporte</li>
            </ul>
        </div>
        <div @class([
            'pt-6 border-t mt-6 flex items-center justify-between',
            'border-white/5' => $colorview == 'dark',
            'border-gray-100' => $colorview !== 'dark',
        ])>
            <div>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Desde</span>
                <span @class([
                    'text-lg font-black font-mono',
                    'text-brand-400' => $colorview == 'dark',
                    'text-gray-900' => $colorview !== 'dark',
                ])>S/. 120.00</span>
            </div>
            <a href="#info-reservas" @class([
                'inline-flex items-center gap-1.5 border px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-300',
                'bg-brand-600/10 hover:bg-brand-600 border-brand-500/20 text-brand-400 hover:text-white' => $colorview == 'dark',
                'bg-white hover:bg-gray-900 border-gray-200 text-gray-700 hover:text-white shadow-sm' => $colorview !== 'dark',
            ])>
                Agendar <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <div data-service-category="electronica" @class([
        'service-card backdrop-blur-md border rounded-2xl p-6 transition-all duration-300 group flex flex-col justify-between hover:-translate-y-1',
        'bg-slate-950/40 border-white/5 hover:border-purple-500/30 shadow-[0_10px_30px_rgba(0,0,0,0.5)]' => $colorview == 'dark',
        'bg-white border-gray-200/60 hover:border-purple-500/50 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)]' => $colorview !== 'dark',
    ]) platform-dependent>
        <div class="space-y-4">
            <div @class([
                'w-12 h-12 border rounded-xl flex items-center justify-center transition-all duration-300',
                'bg-purple-950/40 border-purple-900/50 group-hover:bg-purple-600 group-hover:border-purple-600' => $colorview == 'dark',
                'bg-purple-50 border-purple-100 group-hover:bg-purple-100 group-hover:border-purple-200' => $colorview !== 'dark',
            ])>
                <i data-lucide="cpu" @class([
                    'w-5 h-5 transition-colors duration-300',
                    'text-purple-400 group-hover:text-white' => $colorview == 'dark',
                    'text-purple-600 group-hover:text-purple-700' => $colorview !== 'dark',
                ])></i>
            </div>
            <div>
                <h3 @class([
                    'text-base font-bold transition-colors duration-300',
                    'text-white group-hover:text-purple-500' => $colorview == 'dark',
                    'text-gray-900 group-hover:text-purple-600' => $colorview !== 'dark',
                ])>Diagnóstico OBD Digital</h3>
                <p @class([
                    'text-xs mt-1.5 leading-relaxed',
                    'text-gray-400' => $colorview == 'dark',
                    'text-gray-600' => $colorview !== 'dark',
                ])>Lectura de códigos de falla en la ECU, borrado de alertas de tablero, mapeo de sensores en tiempo real y optimización de entrega de combustible.</p>
            </div>
            <ul @class([
                'space-y-2 text-[11px] font-medium pt-2',
                'text-gray-400' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ]) platform-dependent>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-purple-500 rounded-full"></span> Historial clínico digital inmediato</li>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-purple-500 rounded-full"></span> Compatible con marcas de alta gama</li>
            </ul>
        </div>
        <div @class([
            'pt-6 border-t mt-6 flex items-center justify-between',
            'border-white/5' => $colorview == 'dark',
            'border-gray-100' => $colorview !== 'dark',
        ]) platform-dependent>
            <div>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Desde</span>
                <span @class([
                    'text-lg font-black font-mono',
                    'text-purple-400' => $colorview == 'dark',
                    'text-gray-900' => $colorview !== 'dark',
                ]) platform-dependent>S/. 60.00</span>
            </div>
            <a href="#info-reservas" @class([
                'inline-flex items-center gap-1.5 border px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-300',
                'bg-purple-600/10 hover:bg-purple-600 border-purple-500/20 text-purple-400 hover:text-white' => $colorview == 'dark',
                'bg-white hover:bg-gray-900 border-gray-200 text-gray-700 hover:text-white shadow-sm' => $colorview !== 'dark',
            ])>
                Agendar <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <div data-service-category="correctivo" @class([
        'service-card backdrop-blur-md border rounded-2xl p-6 transition-all duration-300 group flex flex-col justify-between hover:-translate-y-1',
        'bg-slate-950/40 border-white/5 hover:border-brand-500/30 shadow-[0_10px_30px_rgba(0,0,0,0.5)]' => $colorview == 'dark',
        'bg-white border-gray-200/60 hover:border-brand-500/50 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)]' => $colorview !== 'dark',
    ])>
        <div class="space-y-4">
            <div @class([
                'w-12 h-12 border rounded-xl flex items-center justify-center transition-all duration-300',
                'bg-brand-950/40 border-brand-900/50 group-hover:bg-brand-600 group-hover:border-brand-600' => $colorview == 'dark',
                'bg-brand-50 border-brand-100 group-hover:bg-brand-100 group-hover:border-brand-200' => $colorview !== 'dark',
            ])>
                <i data-lucide="gauge" @class([
                    'w-5 h-5 transition-colors duration-300',
                    'text-brand-400 group-hover:text-white' => $colorview == 'dark',
                    'text-brand-600 group-hover:text-brand-700' => $colorview !== 'dark',
                ])></i>
            </div>
            <div>
                <h3 @class([
                    'text-base font-bold transition-colors duration-300',
                    'text-white group-hover:text-brand-400' => $colorview == 'dark',
                    'text-gray-900 group-hover:text-brand-600' => $colorview !== 'dark',
                ]) platform-dependent>Overhaul & Ajuste de Motor</h3>
                <p @class([
                    'text-xs mt-1.5 leading-relaxed',
                    'text-gray-400' => $colorview == 'dark',
                    'text-gray-600' => $colorview !== 'dark',
                ])>Desarmado mecánico avanzado, rectificación, cambio de pistones, anillos, empaquetaduras y sincronización fina de válvulas para recuperar la compresión original.</p>
            </div>
            <ul @class([
                'space-y-2 text-[11px] font-medium pt-2',
                'text-gray-400' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ])>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Repuestos 100% de fábrica</li>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Garantía extendida por escrito</li>
            </ul>
        </div>
        <div @class([
            'pt-6 border-t mt-6 flex items-center justify-between',
            'border-white/5' => $colorview == 'dark',
            'border-gray-100' => $colorview !== 'dark',
        ])>
            <div>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Evaluación</span>
                <span @class([
                    'text-lg font-black font-mono',
                    'text-white' => $colorview == 'dark',
                    'text-gray-900' => $colorview !== 'dark',
                ])>Cotización</span>
            </div>
            <a href="#contacto" @class([
                'inline-flex items-center gap-1.5 border px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-300',
                'bg-slate-900 hover:bg-slate-800 border-white/10 text-white' => $colorview == 'dark',
                'bg-gray-100 hover:bg-gray-200 border-gray-200 text-gray-800' => $colorview !== 'dark',
            ])>
                Consultar <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <div data-service-category="preventivo" @class([
        'service-card backdrop-blur-md border rounded-2xl p-6 transition-all duration-300 group flex flex-col justify-between hover:-translate-y-1',
        'bg-slate-950/40 border-white/5 hover:border-brand-500/30 shadow-[0_10px_30px_rgba(0,0,0,0.5)]' => $colorview == 'dark',
        'bg-white border-gray-200/60 hover:border-brand-500/50 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)]' => $colorview !== 'dark',
    ])>
        <div class="space-y-4">
            <div @class([
                'w-12 h-12 border rounded-xl flex items-center justify-center transition-all duration-300',
                'bg-brand-950/40 border-brand-900/50 group-hover:bg-brand-600 group-hover:border-brand-600' => $colorview == 'dark',
                'bg-brand-50 border-brand-100 group-hover:bg-brand-100 group-hover:border-brand-200' => $colorview !== 'dark',
            ])>
                <i data-lucide="shield-alert" @class([
                    'w-5 h-5 transition-colors duration-300',
                    'text-brand-400 group-hover:text-white' => $colorview == 'dark',
                    'text-brand-600 group-hover:text-brand-700' => $colorview !== 'dark',
                ])></i>
            </div>
            <div>
                <h3 @class([
                    'text-base font-bold transition-colors duration-300',
                    'text-white group-hover:text-brand-400' => $colorview == 'dark',
                    'text-gray-900 group-hover:text-brand-600' => $colorview !== 'dark',
                ]) platform-dependent>Optimización de Frenado</h3>
                <p @class([
                    'text-xs mt-1.5 leading-relaxed',
                    'text-gray-400' => $colorview == 'dark',
                    'text-gray-600' => $colorview !== 'dark',
                ]) platform-dependent>Sustitución de pastillas, purgado de líneas de líquido de freno hidráulico DOT 4/5.1 y purgado/calibración electrónica de módulos ABS.</p>
            </div>
            <ul @class([
                'space-y-2 text-[11px] font-medium pt-2',
                'text-gray-400' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ]) platform-dependent>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Pastillas cerámicas o sinterizadas</li>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Prueba de frenado en rodillo</li>
            </ul>
        </div>
        <div @class([
            'pt-6 border-t mt-6 flex items-center justify-between',
            'border-white/5' => $colorview == 'dark',
            'border-gray-100' => $colorview !== 'dark',
        ])>
            <div>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Desde</span>
                <span @class([
                    'text-lg font-black font-mono',
                    'text-brand-400' => $colorview == 'dark',
                    'text-gray-900' => $colorview !== 'dark',
                ])>S/. 45.00</span>
            </div>
            <a href="#info-reservas" @class([
                'inline-flex items-center gap-1.5 border px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-300',
                'bg-brand-600/10 hover:bg-brand-600 border-brand-500/20 text-brand-400 hover:text-white' => $colorview == 'dark',
                'bg-white hover:bg-gray-900 border-gray-200 text-gray-700 hover:text-white shadow-sm' => $colorview !== 'dark',
            ])>
                Agendar <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <div data-service-category="preventivo" @class([
        'service-card backdrop-blur-md border rounded-2xl p-6 transition-all duration-300 group flex flex-col justify-between hover:-translate-y-1',
        'bg-slate-950/40 border-white/5 hover:border-purple-500/30 shadow-[0_10px_30px_rgba(0,0,0,0.5)]' => $colorview == 'dark',
        'bg-white border-gray-200/60 hover:border-purple-500/50 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)]' => $colorview !== 'dark',
    ])>
        <div class="space-y-4">
            <div @class([
                'w-12 h-12 border rounded-xl flex items-center justify-center transition-all duration-300',
                'bg-purple-950/40 border-purple-900/50 group-hover:bg-purple-600 group-hover:border-purple-600' => $colorview == 'dark',
                'bg-purple-50 border-purple-100 group-hover:bg-purple-100 group-hover:border-purple-200' => $colorview !== 'dark',
            ])>
                <i data-lucide="sliders" @class([
                    'w-5 h-5 transition-colors duration-300',
                    'text-purple-400 group-hover:text-white' => $colorview == 'dark',
                    'text-purple-600 group-hover:text-purple-700' => $colorview !== 'dark',
                ])></i>
            </div>
            <div>
                <h3 @class([
                    'text-base font-bold transition-colors duration-300',
                    'text-white group-hover:text-purple-500' => $colorview == 'dark',
                    'text-gray-900 group-hover:text-purple-600' => $colorview !== 'dark',
                ])>Mantenimiento de Suspensión</h3>
                <p @class([
                    'text-xs mt-1.5 leading-relaxed',
                    'text-gray-400' => $colorview == 'dark',
                    'text-gray-600' => $colorview !== 'dark',
                ]) platform-dependent>Servicio a barras telescópicas convencionales e invertidas. Cambio de retenes de aceite, guardapolvos y calibración exacta de viscosidad por mililitro.</p>
            </div>
            <ul @class([
                'space-y-2 text-[11px] font-medium pt-2',
                'text-gray-400' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ]) platform-dependent>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-purple-500 rounded-full"></span> Aceites premium (Motul/Maxima)</li>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-purple-500 rounded-full"></span> Eliminación de fugas garantizada</li>
            </ul>
        </div>
        <div @class([
            'pt-6 border-t mt-6 flex items-center justify-between',
            'border-white/5' => $colorview == 'dark',
            'border-gray-100' => $colorview !== 'dark',
        ]) platform-dependent>
            <div>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Desde</span>
                <span @class([
                    'text-lg font-black font-mono',
                    'text-purple-400' => $colorview == 'dark',
                    'text-gray-900' => $colorview !== 'dark',
                ]) platform-dependent>S/. 85.00</span>
            </div>
            <a href="#info-reservas" @class([
                'inline-flex items-center gap-1.5 border px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-300',
                'bg-purple-600/10 hover:bg-purple-600 border-purple-500/20 text-purple-400 hover:text-white' => $colorview == 'dark',
                'bg-white hover:bg-gray-900 border-gray-200 text-gray-700 hover:text-white shadow-sm' => $colorview !== 'dark',
            ]) platform-dependent>
                Agendar <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <div data-service-category="electronica" @class([
        'service-card backdrop-blur-md border rounded-2xl p-6 transition-all duration-300 group flex flex-col justify-between hover:-translate-y-1',
        'bg-slate-950/40 border-white/5 hover:border-brand-500/30 shadow-[0_10px_30px_rgba(0,0,0,0.5)]' => $colorview == 'dark',
        'bg-white border-gray-200/60 hover:border-brand-500/50 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)]' => $colorview !== 'dark',
    ])>
        <div class="space-y-4">
            <div @class([
                'w-12 h-12 border rounded-xl flex items-center justify-center transition-all duration-300',
                'bg-brand-950/40 border-brand-900/50 group-hover:bg-brand-600 group-hover:border-brand-600' => $colorview == 'dark',
                'bg-brand-50 border-brand-100 group-hover:bg-brand-100 group-hover:border-brand-200' => $colorview !== 'dark',
            ])>
                <i data-lucide="zap" @class([
                    'w-5 h-5 transition-colors duration-300',
                    'text-brand-400 group-hover:text-white' => $colorview == 'dark',
                    'text-brand-600 group-hover:text-brand-700' => $colorview !== 'dark',
                ])></i>
            </div>
            <div>
                <h3 @class([
                    'text-base font-bold transition-colors duration-300',
                    'text-white group-hover:text-brand-400' => $colorview == 'dark',
                    'text-gray-900 group-hover:text-brand-600' => $colorview !== 'dark',
                ])>Sistema Eléctrico Avanzado</h3>
                <p @class([
                    'text-xs mt-1.5 leading-relaxed',
                    'text-gray-400' => $colorview == 'dark',
                    'text-gray-600' => $colorview !== 'dark',
                ])>Diagnóstico de alternadores, estatores, sistemas de carga, fugas de corriente, instalación y reparación de cableados principales junto a accesorios LED.</p>
            </div>
            <ul @class([
                'space-y-2 text-[11px] font-medium pt-2',
                'text-gray-400' => $colorview == 'dark',
                'text-gray-600' => $colorview !== 'dark',
            ])>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Aislamiento termo-contraíble marino</li>
                <li class="flex items-center gap-2"><span class="w-1 h-1 bg-brand-500 rounded-full"></span> Medición digital de amperaje</li>
            </ul>
        </div>
        <div @class([
            'pt-6 border-t mt-6 flex items-center justify-between',
            'border-white/5' => $colorview == 'dark',
            'border-gray-100' => $colorview !== 'dark',
        ])>
            <div>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Desde</span>
                <span @class([
                    'text-lg font-black font-mono',
                    'text-brand-400' => $colorview == 'dark',
                    'text-gray-900' => $colorview !== 'dark',
                ])>S/. 50.00</span>
            </div>
            <a href="#info-reservas" @class([
                'inline-flex items-center gap-1.5 border px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-300',
                'bg-brand-600/10 hover:bg-brand-600 border-brand-500/20 text-brand-400 hover:text-white' => $colorview == 'dark',
                'bg-white hover:bg-gray-900 border-gray-200 text-gray-700 hover:text-white shadow-sm' => $colorview !== 'dark',
            ])>
                Agendar <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

</div>
                
            </div>
        </section>


    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const buttons = document.querySelectorAll(".filter-btn");
        const cards = document.querySelectorAll(".service-card");

        buttons.forEach(button => {
            button.addEventListener("click", () => {
                const category = button.getAttribute("data-category");

                // 1. Cambiar los estilos visuales del botón activo de forma dinámica
                buttons.forEach(btn => {
                    btn.classList.remove("bg-brand-600", "text-white",
                        "border-brand-400/40",
                        "shadow-[0_0_15px_rgba(37,99,235,0.2)]");
                    btn.classList.add("bg-slate-900/60", "text-gray-400",
                        "border-white/5");
                });

                button.classList.remove("bg-slate-900/60", "text-gray-400", "border-white/5");
                button.classList.add("bg-brand-600", "text-white", "border-brand-400/40",
                    "shadow-[0_0_15px_rgba(37,99,235,0.2)]");

                // 2. Filtrar las tarjetas de servicios con transiciones limpias
                cards.forEach(card => {
                    const cardCategory = card.getAttribute("data-service-category");

                    if (category === "all" || cardCategory === category) {
                        card.classList.remove("hidden");
                        // Micro retraso para permitir un efecto fade-in sutil mediante opacidad
                        setTimeout(() => {
                            card.style.opacity = "1";
                            card.style.transform = "translateY(0)";
                        }, 10);
                    } else {
                        card.style.opacity = "0";
                        card.style.transform = "translateY(10px)";
                        card.classList.add("hidden");
                    }
                });
            });
        });
    });
</script>
