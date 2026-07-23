<main class="relative min-h-screen overflow-hidden">


    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">
        <div id="catalogo" class="space-y-8 pt-6">
            <div @class([
                'flex flex-col lg:flex-row lg:items-end justify-between gap-4 border-b pb-4',
                'border-white/5' => $colorview == 'dark',
                'border-gray-100' => $colorview !== 'dark',
            ])>
                <div class="space-y-2">
                    <div @class([
                        'inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400 border',
                        'bg-brand-950/40 border-brand-900/50' => $colorview == 'dark',
                        'bg-brand-500/10 border-brand-500/20' => $colorview !== 'dark',
                    ])>
                        <i data-lucide="package-open" class="w-3.5 h-3.5"></i> Repuestos y Accesorios Certificados
                    </div>

                    <h2
                        class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-2xl sm:text-3xl font-black tracking-tight">
                        Catálogo Oficial <span class="text-brand-500">KAEL</span>
                    </h2>

                    <p class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-xs max-w-md">
                        Repuestos originales, lubricantes y accesorios seleccionados para mantener tu motocicleta con el
                        máximo rendimiento y seguridad.
                    </p>
                </div>

                <div @class([
                    'flex flex-wrap items-center gap-1.5 p-1.5 rounded-xl border backdrop-blur-sm self-start lg:self-end',
                    'bg-slate-950/60 border-white/5' => $colorview == 'dark',
                    'bg-gray-100 border-gray-200' => $colorview !== 'dark',
                ])>
                    <button
                        class="bg-brand-600 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                        Destacados
                    </button>
                    @php
                        $categorias = ['Motor', 'Transmisión', 'Frenos', 'Lubricantes', 'Eléctrico', 'Accesorios'];
                    @endphp
                    @foreach ($categorias as $cat)
                        <button
                            class="{{ $colorview == 'dark' ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900' }} px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div @class([
                    'rounded-2xl overflow-hidden group hover:border-brand-500/30 transition-all duration-300 flex flex-col justify-between border shadow-sm',
                    'bg-slate-950/40 border-white/5' => $colorview == 'dark',
                    'bg-white border-gray-100' => $colorview !== 'dark',
                ])>
                    <div class="relative">
                        <span
                            class="absolute top-3 left-3 z-20 bg-brand-600/90 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-brand-400/30">
                            Recomendado
                        </span>
                        <div @class([
                            'h-48 relative overflow-hidden flex items-center justify-center border-b',
                            'bg-slate-900/50 border-white/5' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                        ])>
                            <img src="{{ asset('images/kit-transmision.jpg') }}" alt="Kit de Arrastre"
                                class="w-full h-full object-cover opacity-85 group-hover:scale-105 group-hover:opacity-100 transition-all duration-500">
                        </div>
                    </div>
                    <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-brand-500 font-bold uppercase tracking-wider">Sistema de
                                    Transmisión</span>
                                <span class="text-[10px] text-gray-500 font-medium">Cod: 4152-XT</span>
                            </div>
                            <h3
                                class="{{ $colorview == 'dark' ? 'text-gray-200 group-hover:text-brand-400' : 'text-gray-900 group-hover:text-brand-500' }} text-sm font-black transition-colors">
                                Kit de Arrastre Racing DID
                            </h3>
                            <p
                                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-[11px] line-clamp-2">
                                Cadena reforzada con estoperas X-Ring y coronas de alta resistencia al torque para motos
                                de alta cilindrada.
                            </p>
                        </div>

                        <div @class([
                            'flex items-center justify-between text-[10px] p-2 rounded-xl border',
                            'bg-slate-900/30 border-white/5 text-gray-400' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100 text-gray-600' => $colorview !== 'dark',
                        ])>
                            <span class="flex items-center gap-1"><i data-lucide="check"
                                    class="w-3 h-3 text-emerald-500"></i> Stock Disponible</span>
                            <span class="text-gray-500 font-mono">Yamaha R3 / MT-03</span>
                        </div>

                        <div @class([
                            'pt-2 flex items-center justify-between gap-2 border-t',
                            'border-white/5' => $colorview == 'dark',
                            'border-gray-100' => $colorview !== 'dark',
                        ])>
                            <div>
                                <span class="block text-[9px] uppercase tracking-wider text-gray-400 font-bold">Precio
                                    Referencial</span>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-base font-black font-mono">
                                    $124.<span class="text-xs">50</span>
                                </span>
                            </div>
                            <a href="#" @class([
                                'px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-300 shadow-md hover:text-white hover:bg-brand-500 border shrink-0',
                                'bg-brand-950/30 border-brand-500/20 text-brand-400' =>
                                    $colorview == 'dark',
                                'bg-brand-50 border-brand-200 text-brand-600' => $colorview !== 'dark',
                            ])>
                                Cotizar <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div @class([
                    'rounded-2xl overflow-hidden group hover:border-brand-500/30 transition-all duration-300 flex flex-col justify-between border shadow-sm',
                    'bg-slate-950/40 border-white/5' => $colorview == 'dark',
                    'bg-white border-gray-100' => $colorview !== 'dark',
                ])>
                    <div class="relative">
                        <span
                            class="absolute top-3 left-3 z-20 bg-brand-500 text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-brand-400/30">
                            Más Solicitado
                        </span>
                        <div @class([
                            'h-48 relative overflow-hidden flex items-center justify-center border-b',
                            'bg-slate-900/50 border-white/5' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                        ])>
                            <img src="{{ asset('images/pastillas-brembo.jpg') }}" alt="Pastillas Brembo"
                                class="w-full h-full object-cover opacity-85 group-hover:scale-105 group-hover:opacity-100 transition-all duration-500">
                        </div>
                    </div>
                    <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-brand-500 font-bold uppercase tracking-wider">Sistema de
                                    Frenado</span>
                                <span class="text-[10px] text-gray-500 font-medium">Cod: BRM-882</span>
                            </div>
                            <h3
                                class="{{ $colorview == 'dark' ? 'text-gray-200 group-hover:text-brand-400' : 'text-gray-900 group-hover:text-brand-500' }} text-sm font-black transition-colors">
                                Pastillas Brembo Sinterizadas
                            </h3>
                            <p
                                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-[11px] line-clamp-2">
                                Máximo coeficiente de fricción térmica. Frenado preciso en seco y mojado sin desgaste
                                prematuro del disco.
                            </p>
                        </div>

                        <div @class([
                            'flex items-center justify-between text-[10px] p-2 rounded-xl border',
                            'bg-slate-900/30 border-white/5 text-gray-400' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100 text-gray-600' => $colorview !== 'dark',
                        ])>
                            <span class="flex items-center gap-1"><i data-lucide="check"
                                    class="w-3 h-3 text-emerald-500"></i> Stock Disponible</span>
                            <span class="text-gray-500 font-mono">Multimarca Universal</span>
                        </div>

                        <div @class([
                            'pt-2 flex items-center justify-between gap-2 border-t',
                            'border-white/5' => $colorview == 'dark',
                            'border-gray-100' => $colorview !== 'dark',
                        ])>
                            <div>
                                <span class="block text-[9px] uppercase tracking-wider text-gray-400 font-bold">Precio
                                    Taller</span>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-base font-black font-mono">
                                    $45.<span class="text-xs">90</span>
                                </span>
                            </div>
                            <a href="#" @class([
                                'px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-300 shadow-md hover:text-white hover:bg-brand-500 border shrink-0',
                                'bg-brand-950/30 border-brand-500/20 text-brand-400' =>
                                    $colorview == 'dark',
                                'bg-brand-50 border-brand-200 text-brand-600' => $colorview !== 'dark',
                            ])>
                                Consultar <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div @class([
                    'rounded-2xl overflow-hidden group hover:border-brand-500/30 transition-all duration-300 flex flex-col justify-between border shadow-sm',
                    'bg-slate-950/40 border-white/5' => $colorview == 'dark',
                    'bg-white border-gray-100' => $colorview !== 'dark',
                ])>
                    <div class="relative">
                        <span
                            class="absolute top-3 left-3 z-20 bg-brand-600/90 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-brand-400/30">
                            Premium
                        </span>
                        <div @class([
                            'h-48 relative overflow-hidden flex items-center justify-center border-b',
                            'bg-slate-900/50 border-white/5' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                        ])>
                            <img src="{{ asset('images/motul-7100.jpg') }}" alt="Motul 7100"
                                class="w-full h-full object-cover opacity-85 group-hover:scale-105 group-hover:opacity-100 transition-all duration-500">
                        </div>
                    </div>
                    <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-brand-500 font-bold uppercase tracking-wider">Lubricación
                                    Premium</span>
                                <span class="text-[10px] text-gray-500 font-medium">Cod: MTL-10W40</span>
                            </div>
                            <h3
                                class="{{ $colorview == 'dark' ? 'text-gray-200 group-hover:text-brand-400' : 'text-gray-900 group-hover:text-brand-500' }} text-sm font-black transition-colors">
                                Motul 7100 4T 10W40 Sintético
                            </h3>
                            <p
                                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-[11px] line-clamp-2">
                                Aceite 100% sintético basado en tecnología de Ésteres. Protección óptima de motor, caja
                                y embrague húmedo.
                            </p>
                        </div>

                        <div @class([
                            'flex items-center justify-between text-[10px] p-2 rounded-xl border',
                            'bg-slate-900/30 border-white/5 text-gray-400' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100 text-gray-600' => $colorview !== 'dark',
                        ])>
                            <span class="flex items-center gap-1"><i data-lucide="check"
                                    class="w-3 h-3 text-emerald-500"></i> Stock Disponible</span>
                            <span class="text-gray-500 font-mono">Cualquier Motor 4T</span>
                        </div>

                        <div @class([
                            'pt-2 flex items-center justify-between gap-2 border-t',
                            'border-white/5' => $colorview == 'dark',
                            'border-gray-100' => $colorview !== 'dark',
                        ])>
                            <div>
                                <span class="block text-[9px] uppercase tracking-wider text-gray-400 font-bold">Precio
                                    Taller</span>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-base font-black font-mono">
                                    $22.<span class="text-xs">00</span>
                                </span>
                            </div>
                            <a href="#" @class([
                                'px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-300 shadow-md hover:text-white hover:bg-brand-500 border shrink-0',
                                'bg-brand-950/30 border-brand-500/20 text-brand-400' =>
                                    $colorview == 'dark',
                                'bg-brand-50 border-brand-200 text-brand-600' => $colorview !== 'dark',
                            ])>
                                Consultar <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div @class([
                    'rounded-2xl overflow-hidden group hover:border-brand-500/30 transition-all duration-300 flex flex-col justify-between border shadow-sm',
                    'bg-slate-950/40 border-white/5' => $colorview == 'dark',
                    'bg-white border-gray-100' => $colorview !== 'dark',
                ])>
                    <div class="relative">
                        <span
                            class="absolute top-3 left-3 z-20 bg-amber-600/90 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-amber-400/30">
                            Tecnología Iridium
                        </span>
                        <div @class([
                            'h-48 relative overflow-hidden flex items-center justify-center border-b',
                            'bg-slate-900/50 border-white/5' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100' => $colorview !== 'dark',
                        ])>
                            <img src="{{ asset('images/bujia-ngk.jpg') }}" alt="Bujia NGK Iridium"
                                class="w-full h-full object-cover opacity-85 group-hover:scale-105 group-hover:opacity-100 transition-all duration-500">
                        </div>
                    </div>
                    <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-brand-500 font-bold uppercase tracking-wider">Encendido y
                                    Rendimiento</span>
                                <span class="text-[10px] text-gray-500 font-medium">Cod: NGK-IR7</span>
                            </div>
                            <h3
                                class="{{ $colorview == 'dark' ? 'text-gray-200 group-hover:text-brand-400' : 'text-gray-900 group-hover:text-brand-500' }} text-sm font-black transition-colors">
                                Bujía NGK Laser Iridium
                            </h3>
                            <p
                                class="{{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-[11px] line-clamp-2">
                                Electrodo central ultrafino que mejora la chispa, optimiza la combustión y reduce el
                                consumo de combustible.
                            </p>
                        </div>

                        <div @class([
                            'flex items-center justify-between text-[10px] p-2 rounded-xl border',
                            'bg-slate-900/30 border-white/5 text-gray-400' => $colorview == 'dark',
                            'bg-gray-50 border-gray-100 text-gray-600' => $colorview !== 'dark',
                        ])>
                            <span class="flex items-center gap-1"><i data-lucide="check"
                                    class="w-3 h-3 text-emerald-500"></i> Stock Disponible</span>
                            <span class="text-gray-500 font-mono">Inyección / Carburada</span>
                        </div>

                        <div @class([
                            'pt-2 flex items-center justify-between gap-2 border-t',
                            'border-white/5' => $colorview == 'dark',
                            'border-gray-100' => $colorview !== 'dark',
                        ])>
                            <div>
                                <span class="block text-[9px] uppercase tracking-wider text-gray-400 font-bold">Precio
                                    Taller</span>
                                <span
                                    class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-base font-black font-mono">
                                    $18.<span class="text-xs">20</span>
                                </span>
                            </div>
                            <a href="#" @class([
                                'px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-300 shadow-md hover:text-white hover:bg-brand-500 border shrink-0',
                                'bg-brand-950/30 border-brand-500/20 text-brand-400' =>
                                    $colorview == 'dark',
                                'bg-brand-50 border-brand-200 text-brand-600' => $colorview !== 'dark',
                            ])>
                                Consultar <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="text-center pt-2">
                <a href="#"
                    class="{{ $colorview == 'dark' ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 text-xs font-bold transition group">
                    Explorar catálogo completo
                    <i data-lucide="arrow-right"
                        class="w-4 h-4 text-brand-500 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

    </div>
</main>
