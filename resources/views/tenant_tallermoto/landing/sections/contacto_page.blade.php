<main class="relative min-h-screen overflow-hidden">
    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">
        <section id="contacto-main" class="py-24 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-brand-600/5 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-0 left-10 w-80 h-80 bg-purple-600/5 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 border rounded-full text-[10px] font-bold uppercase tracking-widest
                        {{ $colorview == 'dark' ? 'bg-brand-950/40 border-brand-500/20 text-brand-400' : 'bg-red-50 border-red-200 text-red-600' }}">
                        <i data-lucide="map-pin" class="w-3 h-3 animate-pulse"></i> Cobertura Técnica Autorizada
                    </div>
                    <h2 class="text-3xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }} uppercase tracking-tight">
                        Conéctate con <span class="{{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}">{{ $empresa->nombre_comercial }}</span>
                    </h2>
                    <p class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-500' }}">
                        Visita nuestros laboratorios mecánicos o agenda una consultoría digitalizada inmediata vía canales oficiales.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

                    {{-- Columna Izquierda: Botones e Información --}}
                    <div class="lg:col-span-5 flex flex-col justify-between space-y-6">

                        {{-- Lista de Sedes --}}
                        <div class="backdrop-blur-md border p-4 rounded-3xl space-y-3
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200 shadow-xs' }}">
                            <span class="block text-[9px] font-black uppercase tracking-widest px-2 {{ $colorview == 'dark' ? 'text-gray-500' : 'text-slate-400' }}">
                                Selecciona un Laboratorio
                            </span>

                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($sede as $index => $item)
                                    @php
                                        $almacen = explode('-', $item->ALM_NombreAlmacen);
                                        $direccionCompleta = trim(($item->ALM_Direccion ?? '') . ' ' . ($item->ALM_Distrito ?? '') . ' ' . ($item->ALM_Provincia ?? ''));
                                    @endphp
                                    <button 
                                        type="button"
                                        data-sede-id="{{ $item->ALM_Id }}"
                                        data-nombre="{{ $item->ALM_NombreAlmacen }}"
                                        data-direccion="{{ $item->ALM_Direccion ?? 'Dirección no especificada' }}"
                                        data-referencia="{{ $item->ALM_Referencia ?? '' }}"
                                        data-telefono="{{ $item->ALM_Telefono ?? $item->ALM_Celular ?? 'Sin teléfono' }}"
                                        data-latitud="{{ $item->ALM_Latitud }}"
                                        data-longitud="{{ $item->ALM_Longitud }}"
                                        data-direccion-query="{{ urlencode($direccionCompleta) }}"
                                        class="sede-tab-btn px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-left transition-all border flex flex-col gap-0.5
                                        {{ $colorview == 'dark'
                                            ? 'bg-slate-900/60 text-gray-400 border-white/5 hover:text-white hover:border-white/10'
                                            : 'bg-slate-50 text-slate-600 border-slate-200 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <span>{{ trim($almacen[0]) }}</span>
                                        <span class="text-[9px] font-mono font-medium {{ $colorview == 'dark' ? 'text-brand-200' : 'text-red-500' }}">
                                            {{ trim($almacen[1] ?? '') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Detalle de la Sede Seleccionada --}}
                        <div class="backdrop-blur-md border p-6 rounded-3xl flex-1 flex flex-col justify-between space-y-6
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200 shadow-xs' }}">

                            <div id="info-sede-content" class="space-y-4 transition-all duration-200">
                                <div>
                                    <h3 id="sede-nombre" class="text-base font-black uppercase tracking-wide {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">
                                        ---
                                    </h3>
                                    <p id="sede-direccion" class="text-xs mt-1 flex items-start gap-2 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-600' }}">
                                        <i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}"></i>
                                        <span>---</span>
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                    <div class="border p-3 rounded-2xl {{ $colorview == 'dark' ? 'bg-slate-900/40 border-white/5' : 'bg-slate-50 border-slate-100' }}">
                                        <span class="block text-[9px] text-gray-500 font-bold uppercase">Soporte Técnico</span>
                                        <span id="sede-telefono" class="text-xs font-mono font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">
                                            ---
                                        </span>
                                    </div>
                                    <div class="border p-3 rounded-2xl {{ $colorview == 'dark' ? 'bg-slate-900/40 border-white/5' : 'bg-slate-50 border-slate-100' }}">
                                        <span class="block text-[9px] text-gray-500 font-bold uppercase">Horario de Operación</span>
                                        <span id="sede-horario" class="text-xs font-mono {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-700' }}">
                                            Lun - Sáb: 8AM - 6PM
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-100' }}">
                                <a id="sede-waze-link" href="#" target="_blank" rel="noopener noreferrer"
                                    class="w-full border py-3 rounded-xl text-xs font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2
                                    {{ $colorview == 'dark' ? 'bg-slate-900 hover:bg-white/5 border-white/10 text-gray-300 hover:text-white' : 'bg-slate-900 hover:bg-slate-800 border-transparent text-white' }}">
                                    <i data-lucide="navigation" class="w-4 h-4 {{ $colorview == 'dark' ? 'text-brand-400' : 'text-red-400' }}"></i>
                                    Iniciar Ruta GPS (Waze / Maps)
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Columna Derecha: Mapa dinámico --}}
                    <div class="lg:col-span-7">
                        <div class="backdrop-blur-md border p-3 rounded-3xl h-[380px] sm:h-full min-h-[380px] relative overflow-hidden group
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200 shadow-xs' }}">

                            <div class="absolute -inset-px bg-gradient-to-r rounded-3xl blur opacity-30 group-hover:opacity-50 transition duration-500
                                {{ $colorview == 'dark' ? 'from-brand-500/10 to-brand-500/10' : 'from-red-500/5 to-red-500/5' }}">
                            </div>

                            <div class="w-full h-full rounded-2xl overflow-hidden relative border {{ $colorview == 'dark' ? 'border-white/5 bg-slate-900' : 'border-slate-100 bg-slate-50' }}">
                                <iframe id="sede-map-iframe"
                                    src="about:blank"
                                    class="w-full h-full border-0 transition-all duration-300 {{ $colorview == 'dark' ? 'grayscale invert opacity-80' : 'grayscale-0 opacity-100' }}"
                                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tabs = document.querySelectorAll(".sede-tab-btn");
        const sNombre = document.getElementById("sede-nombre");
        const sDireccion = document.getElementById("sede-direccion");
        const sTelefono = document.getElementById("sede-telefono");
        const sHorario = document.getElementById("sede-horario");
        const sWaze = document.getElementById("sede-waze-link");
        const sIframe = document.getElementById("sede-map-iframe");

        const currentTheme = @json($colorview);
        const MI_API_KEY = "AIzaSyAIPaCF5yU1abK-ZHAVY3nQ4dJuX9mLZ-Q";

        function switchSede(button) {
            if (!button) return;

            const dataset = button.dataset;

            // 1. Resetear estilos de todos los botones
            tabs.forEach(btn => {
                btn.className = "sede-tab-btn px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-left transition-all border flex flex-col gap-0.5";
                if (currentTheme === 'dark') {
                    btn.classList.add("bg-slate-900/60", "text-gray-400", "border-white/5", "hover:text-white", "hover:border-white/10");
                } else {
                    btn.classList.add("bg-slate-50", "text-slate-600", "border-slate-200", "hover:text-slate-900", "hover:bg-slate-100");
                }
            });

            // 2. Aplicar estado activo al botón seleccionado
            button.className = "sede-tab-btn px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-left transition-all border flex flex-col gap-0.5";
            if (currentTheme === 'dark') {
                button.classList.add("bg-brand-600", "text-white", "border-brand-400/30", "shadow-[0_0_15px_rgba(37,99,235,0.15)]");
            } else {
                button.classList.add("bg-red-500", "text-white", "border-red-400", "shadow-[0_0_15px_rgba(239,68,68,0.15)]");
            }

            // 3. Transición de desvanecimiento para actualización de datos
            const content = document.getElementById("info-sede-content");
            content.style.opacity = "0";
            content.style.transform = "translateY(5px)";

            setTimeout(() => {
                sNombre.textContent = dataset.nombre || 'Sede no especificada';

                const iconColor = currentTheme === 'dark' ? 'text-brand-500' : 'text-red-500';
                sDireccion.innerHTML = `<i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 ${iconColor}"></i> ${dataset.direccion}`;

                sTelefono.textContent = dataset.telefono || 'Sin teléfono';

                // Generación dinámica de la URL del Mapa y de Waze
                let mapQuery = "";
                let wazeUrl = "";

                if (dataset.latitud && dataset.longitud) {
                    mapQuery = `${dataset.latitud},${dataset.longitud}`;
                    wazeUrl = `https://waze.com/ul?ll=${dataset.latitud},${dataset.longitud}&navigate=yes`;
                } else {
                    mapQuery = dataset.direccionQuery;
                    wazeUrl = `https://waze.com/ul?q=${dataset.direccionQuery}`;
                }

                sIframe.src = `https://www.google.com/maps/embed/v1/place?key=${MI_API_KEY}&q=${mapQuery}`;
                sWaze.href = wazeUrl;

                if (typeof lucide !== 'undefined') lucide.createIcons();

                content.style.opacity = "1";
                content.style.transform = "translateY(0)";
            }, 200);
        }

        // Asignación del listener de clic a cada botón generado dinámicamente
        tabs.forEach(btn => {
            btn.addEventListener("click", () => switchSede(btn));
        });

        // Cargar por defecto la primera sede en la lista
        if (tabs.length > 0) {
            switchSede(tabs[0]);
        }
    });
</script>