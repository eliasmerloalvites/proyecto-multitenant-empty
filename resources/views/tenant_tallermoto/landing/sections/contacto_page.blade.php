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
                        Conéctate con <span class="{{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}">KAEL</span>
                    </h2>
                    <p class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-500' }}">
                        Visita nuestros laboratorios mecánicos o agenda una consultoría digitalizada inmediata vía canales oficiales.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

                    <div class="lg:col-span-5 flex flex-col justify-between space-y-6">

                        <div class="backdrop-blur-md border p-4 rounded-3xl space-y-3
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200 shadow-xs' }}">
                            <span class="block text-[9px] font-black uppercase tracking-widest px-2 {{ $colorview == 'dark' ? 'text-gray-500' : 'text-slate-400' }}">
                                Selecciona un Laboratorio
                            </span>

                            <div class="grid grid-cols-2 gap-2">
                                <button id="btn-sede-principal"
                                    class="sede-tab-btn px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-left transition-all border flex flex-col gap-0.5
                                    {{ $colorview == 'dark' 
                                        ? 'bg-brand-600 text-gray-400 border-brand-400/30 shadow-[0_0_15px_rgba(37,99,235,0.15)]' 
                                        : 'bg-red-500 text-white border-red-400 shadow-[0_0_15px_rgba(239,68,68,0.15)]' }}">
                                    <span>Sede Sur</span>
                                    <span class="text-[9px] font-mono font-medium {{ $colorview == 'dark' ? 'text-brand-200' : 'text-red-100' }}">Sede Principal</span>
                                </button>
                                
                                <button id="btn-sede-norte"
                                    class="sede-tab-btn px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-left transition-all border flex flex-col gap-0.5
                                    {{ $colorview == 'dark' 
                                        ? 'bg-slate-900/60 text-gray-400 border-white/5 hover:text-white hover:border-white/10' 
                                        : 'bg-slate-50 text-slate-600 border-slate-200 hover:text-slate-900 hover:bg-slate-100' }}">
                                    <span>Sede Norte</span>
                                    <span class="text-[9px] text-gray-500 font-mono font-medium">Sucursal Express</span>
                                </button>
                            </div>
                        </div>

                        <div class="backdrop-blur-md border p-6 rounded-3xl flex-1 flex flex-col justify-between space-y-6
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200 shadow-xs' }}">

                            <div id="info-sede-content" class="space-y-4">
                                <div>
                                    <h3 id="sede-nombre" class="text-base font-black uppercase tracking-wide {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">
                                        Sede Sur - Principal
                                    </h3>
                                    <p id="sede-direccion" class="text-xs mt-1 flex items-start gap-2 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-600' }}">
                                        <i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}"></i>
                                        Av. Paseo de la República 4560, Surquillo, Lima
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                    <div class="border p-3 rounded-2xl {{ $colorview == 'dark' ? 'bg-slate-900/40 border-white/5' : 'bg-slate-50 border-slate-100' }}">
                                        <span class="block text-[9px] text-gray-500 font-bold uppercase">Soporte Técnico</span>
                                        <span id="sede-telefono" class="text-xs font-mono font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }}">
                                            +51 987 654 321
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
                                <a id="sede-waze-link" href="https://maps.google.com" target="_blank"
                                    class="w-full border py-3 rounded-xl text-xs font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2
                                    {{ $colorview == 'dark' ? 'bg-slate-900 hover:bg-white/5 border-white/10 text-gray-300 hover:text-white' : 'bg-slate-900 hover:bg-slate-800 border-transparent text-white' }}">
                                    <i data-lucide="navigation" class="w-4 h-4 {{ $colorview == 'dark' ? 'text-brand-400' : 'text-red-400' }}"></i> 
                                    Iniciar Ruta GPS (Waze / Maps)
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7">
                        <div class="backdrop-blur-md border p-3 rounded-3xl h-[380px] sm:h-full min-h-[380px] relative overflow-hidden group
                            {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200 shadow-xs' }}">
                            
                            <div class="absolute -inset-px bg-gradient-to-r rounded-3xl blur opacity-30 group-hover:opacity-50 transition duration-500
                                {{ $colorview == 'dark' ? 'from-brand-500/10 to-brand-500/10' : 'from-red-500/5 to-red-500/5' }}">
                            </div>

                            <div class="w-full h-full rounded-2xl overflow-hidden relative border {{ $colorview == 'dark' ? 'border-white/5 bg-slate-900' : 'border-slate-100 bg-slate-50' }}">
                                <iframe id="sede-map-iframe"
                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAIPaCF5yU1abK-ZHAVY3nQ4dJuX9mLZ-Q&q=Av.+Paseo+de+la+Republica+4560,+Surquillo,+Lima"
                                    class="w-full h-full border-0 transition-all duration-300 {{ $colorview == 'dark' ? 'grayscale invert opacity-80' : 'grayscale-0 opacity-100' }}" 
                                    allowfullscreen=""
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade">
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
        const btnPrincipal = document.getElementById("btn-sede-principal");
        const btnNorte = document.getElementById("btn-sede-norte");
        const tabs = document.querySelectorAll(".sede-tab-btn");

        const sNombre = document.getElementById("sede-nombre");
        const sDireccion = document.getElementById("sede-direccion");
        const sTelefono = document.getElementById("sede-telefono");
        const sHorario = document.getElementById("sede-horario");
        const sWaze = document.getElementById("sede-waze-link");
        const sIframe = document.getElementById("sede-map-iframe");

        // Variable global del entorno Blade inyectada limpiamente en JS
        const currentTheme = "{{ $colorview }}";
        const MI_API_KEY = "AIzaSyAIPaCF5yU1abK-ZHAVY3nQ4dJuX9mLZ-Q";

        const datosSedes = {
            principal: {
                nombre: "Sede Sur - Principal",
                direccion: "Av. Paseo de la República 4560, Surquillo, Lima",
                telefono: "+51 987 654 321",
                horario: "Lun - Sáb: 8AM - 6PM",
                waze: "https://waze.com/ul?q=Av.+Paseo+de+la+Republica+4560,+Surquillo",
                query: "Av.+Paseo+de+la+Republica+4560,+Surquillo,+Lima"
            },
            norte: {
                nombre: "Sede Norte - Sucursal Express",
                direccion: "Av. Alfredo Mendiola 3500, Los Olivos, Lima",
                telefono: "+51 912 345 678",
                horario: "Lun - Sáb: 8AM - 5PM",
                waze: "https://waze.com/ul?q=Av.+Alfredo+Mendiola+3500,+Los+Olivos",
                query: "Av.+Alfredo+Mendiola+3500,+Los+Olivos,+Lima"
            }
        };

        function switchSede(key, activeBtn) {
            // Remover estilos activos y aplicar inactivos dinámicamente según el tema de color
            tabs.forEach(btn => {
                btn.className = "sede-tab-btn px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-left transition-all border flex flex-col gap-0.5";
                
                if (currentTheme === 'dark') {
                    btn.classList.add("bg-slate-900/60", "text-gray-400", "border-white/5", "hover:text-white", "hover:border-white/10");
                } else {
                    btn.classList.add("bg-slate-50", "text-slate-600", "border-slate-200", "hover:text-slate-900", "hover:bg-slate-100");
                }
            });

            // Aplicar clases de estado Activo considerando el tema gráfico
            activeBtn.className = "sede-tab-btn px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-left transition-all border flex flex-col gap-0.5";
            if (currentTheme === 'dark') {
                activeBtn.classList.add("bg-brand-600", "text-white", "border-brand-400/30", "shadow-[0_0_15px_rgba(37,99,235,0.15)]");
            } else {
                activeBtn.classList.add("bg-red-500", "text-white", "border-red-400", "shadow-[0_0_15px_rgba(239,68,68,0.15)]");
            }

            const content = document.getElementById("info-sede-content");
            content.style.opacity = "0";
            content.style.transform = "translateY(5px)";

            setTimeout(() => {
                sNombre.textContent = datosSedes[key].nombre;
                
                // Color dinámico para el ícono pin inyectado vía JS
                const iconColor = currentTheme === 'dark' ? 'text-brand-500' : 'text-red-500';
                sDireccion.innerHTML = `<i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 ${iconColor}"></i> ${datosSedes[key].direccion}`;
                
                sTelefono.textContent = datosSedes[key].telefono;
                sHorario.textContent = datosSedes[key].horario;
                sWaze.href = datosSedes[key].waze;

                sIframe.src = `https://www.google.com/maps/embed/v1/place?key=${MI_API_KEY}&q=${datosSedes[key].query}`;

                if (typeof lucide !== 'undefined') lucide.createIcons();

                content.style.opacity = "1";
                content.style.transform = "translateY(0)";
            }, 200);
        }

        btnPrincipal.addEventListener("click", () => switchSede("principal", btnPrincipal));
        btnNorte.addEventListener("click", () => switchSede("norte", btnNorte));
    });
</script>