<!-- Gradiente lateral -->
<main class="relative overflow-hidden">

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
                    <form action="{{ url()->current() }}" method="GET" @class([
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
                                <!-- 1. Atributo name="Placa" asignado -->
                                <!-- 2. Mantener el valor ingresado mediante value="{{ request('Placa') }}" -->
                                <input type="text" name="Placa" value="{{ request('Placa') }}"
                                    placeholder="EJEM: 1234-5X" required @class([
                                        'w-full border focus:border-brand-500/50 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-mono font-bold uppercase placeholder-gray-500 focus:outline-none focus:ring-0 transition-all',
                                        'bg-slate-900 border-white/5 text-white' => $colorview == 'dark',
                                        'bg-gray-50 border-gray-200 text-gray-900' => $colorview !== 'dark',
                                    ])>
                            </div>
                            <button type="submit"
                                class="bg-brand-500 hover:bg-brand-400 text-white px-5 rounded-2xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-brand-500/20 flex items-center gap-1.5 shrink-0">
                                Consultar <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </form>
                </div>

                @if (isset($data['resumen']))
                    @php $res = $data['resumen']; @endphp

                    <div class="max-w-3xl mx-auto space-y-6">

                        <!-- CARD PRINCIPAL: DATOS GENERALES -->
                        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
                            <!-- Header con Tipo de Mantenimiento y Botón PDF -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-white/10">
                                <div>
                                    <span
                                        class="inline-block px-3 py-1 text-xs font-black tracking-wider uppercase bg-brand-500/20 text-brand-400 border border-brand-500/30 rounded-full mb-2">
                                        {{ $res['cabecera']['tipo'] }}
                                    </span>
                                    <h3 class="text-xl font-bold text-white">Último Mantenimiento</h3>
                                    <p class="text-xs text-gray-400">Atendido por: <span
                                            class="text-gray-200 font-semibold">{{ $res['cabecera']['mecanico'] }}</span>
                                        el {{ $res['cabecera']['fecha'] }}</p>
                                </div>

                                <!-- Botón para descargar/ver PDF completo -->
                                <a href="{{ $res['cabecera']['url_pdf'] }}/{{ $res['cabecera']['id'] }}/descargarpdf"
                                    download
                                    class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                    Descargar Ficha PDF
                                </a>
                            </div>

                            <!-- Grid de datos rápidos del vehículo -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                                <div class="bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">PLACA</span>
                                    <span
                                        class="text-base font-black text-white font-mono">{{ $res['cabecera']['placa'] }}</span>
                                </div>
                                <div class="bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">VEHÍCULO</span>
                                    <span
                                        class="text-sm font-bold text-white truncate block">{{ $res['cabecera']['unidad'] }}</span>
                                </div>
                                <div class="bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">KILOMETRAJE</span>
                                    <span class="text-sm font-bold text-emerald-400">{{ $res['cabecera']['km'] }}
                                        KM</span>
                                </div>
                                <div class="bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">CLIENTE</span>
                                    <span
                                        class="text-sm font-bold text-white truncate block">{{ $res['cabecera']['propietario'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CARD DE MÉTRICAS CLAVE (PUNTOS IMPORTANTES) -->
                        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
                            <h4
                                class="text-xs font-black uppercase tracking-wider text-gray-400 border-b border-white/5 pb-3">
                                Resumen Diagnóstico & Mediciones
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                <!-- 1. Motor & Aceite -->
                                <div class="p-4 rounded-2xl bg-slate-950/40 border border-white/5 space-y-2">
                                    <div class="flex items-center gap-2 text-brand-400 text-xs font-bold uppercase">
                                        <i data-lucide="gauge" class="w-4 h-4"></i> Motor
                                    </div>
                                    <div class="text-xs space-y-1 text-gray-300">
                                        <p class="flex justify-between"><span>Aceite:</span> <strong
                                                class="text-white">{{ $res['metricas']['aceite'] }}</strong></p>
                                        @if ($res['metricas']['valvula_adm'])
                                            <p class="flex justify-between"><span>Válv. Adm / Esc:</span> <strong
                                                    class="text-white">{{ $res['metricas']['valvula_adm'] }} /
                                                    {{ $res['metricas']['valvula_esc'] }}</strong></p>
                                        @endif
                                        @if ($res['metricas']['bujia_medida'])
                                            <p class="flex justify-between"><span>Bujía Calibre:</span> <strong
                                                    class="text-white">{{ $res['metricas']['bujia_medida'] }}</strong>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- 2. Neumáticos & Presión -->
                                <div class="p-4 rounded-2xl bg-slate-950/40 border border-white/5 space-y-2">
                                    <div class="flex items-center gap-2 text-blue-400 text-xs font-bold uppercase">
                                        <i data-lucide="disc" class="w-4 h-4"></i> Neumáticos
                                    </div>
                                    <div class="text-xs space-y-1 text-gray-300">
                                        <p class="flex justify-between"><span>Presión Del:</span> <strong
                                                class="text-white">{{ $res['metricas']['psi_delantero'] ?? 'N/A' }}</strong>
                                        </p>
                                        <p class="flex justify-between"><span>Presión Post:</span> <strong
                                                class="text-white">{{ $res['metricas']['psi_trasero'] ?? 'N/A' }}</strong>
                                        </p>
                                    </div>
                                </div>

                                <!-- 3. Batería / Carga -->
                                <div class="p-4 rounded-2xl bg-slate-950/40 border border-white/5 space-y-2">
                                    <div class="flex items-center gap-2 text-amber-400 text-xs font-bold uppercase">
                                        <i data-lucide="zap" class="w-4 h-4"></i> Sistema Eléctrico
                                    </div>
                                    <div class="text-xs space-y-1 text-gray-300">
                                        <p class="flex justify-between"><span>Voltaje Carga:</span> <strong
                                                class="text-white">{{ $res['metricas']['v_carga'] ?? 'N/A' }}</strong>
                                        </p>
                                        <p class="flex justify-between"><span>Voltaje Arranque:</span> <strong
                                                class="text-white">{{ $res['metricas']['v_arranque'] ?? 'N/A' }}</strong>
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <!-- EXTRAS DINÁMICOS SEGÚN EL TIPO -->
                            @if (!empty($res['extras']))
                                <div class="pt-4 border-t border-white/5">
                                    @if (isset($res['extras']['escaneo']))
                                        <div
                                            class="p-3 bg-brand-500/10 border border-brand-500/20 rounded-xl text-xs text-brand-300 flex justify-between">
                                            <span>Resultado Escaneo / Vida Útil:</span>
                                            <span class="font-bold">{{ $res['extras']['escaneo'] }}</span>
                                        </div>
                                    @endif

                                    @if (isset($res['extras']['detalle_trabajo']))
                                        <div
                                            class="p-3 bg-slate-950/60 border border-white/5 rounded-xl text-xs text-gray-300">
                                            <span class="block text-gray-400 font-bold mb-1 uppercase">Trabajos
                                                Realizados:</span>
                                            <p>{{ $res['extras']['detalle_trabajo'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>

                    </div>
                @endif

            </div>
        </section>

    </div>
</main>

<script></script>
