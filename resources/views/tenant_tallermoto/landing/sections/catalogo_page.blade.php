<main class="relative min-h-screen overflow-hidden">
    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">
        <section id="catalogo-productos" class="py-20 relative overflow-hidden">
            <div class="absolute top-10 right-10 w-80 h-80 bg-brand-600/5 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute bottom-10 left-10 w-80 h-80 bg-purple-600/5 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12 border-b {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-200' }} pb-8">
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 {{ $colorview == 'dark' ? 'bg-brand-950/40 border-brand-500/20 text-brand-400' : 'bg-red-50 border-red-200 text-red-600' }} border rounded-full text-[10px] font-bold uppercase tracking-widest">
                            <i data-lucide="package" class="w-3 h-3"></i> Componentes Originales & Performance
                        </div>
                        <h2 class="text-3xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }} uppercase tracking-tight">
                            Catálogo de <span class="{{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}">Repuestos</span>
                        </h2>
                        <p class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-500' }} max-w-md">
                            Garantiza la longevidad de tu ruta con piezas certificadas de fábrica y fluidos de alta competición.
                        </p>
                    </div>
                    
                    <div class="w-full md:w-80 relative">
                        <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                        <input type="text" placeholder="Buscar repuesto..." 
                            class="w-full border rounded-xl pl-10 pr-4 py-2.5 text-xs focus:outline-none transition-all
                            {{ $colorview == 'dark' ? 'bg-slate-900/60 border-white/5 focus:border-brand-500/40 text-gray-300 placeholder-gray-600' : 'bg-white border-slate-200 focus:border-red-500/40 text-slate-800 placeholder-slate-400 shadow-sm' }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <div class="lg:col-span-3 space-y-4 sticky top-24">
                        <div class="{{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200/80 shadow-sm' }} backdrop-blur-md p-5 rounded-2xl border space-y-3">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 flex items-center gap-2">
                                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}"></i> Categorías
                            </h4>
                            
                            <div id="catalog-filters" class="flex flex-col gap-1.5">
                                <button data-filter="all" data-active="true" 
                                    class="catalog-filter-btn w-full text-left px-4 py-2.5 rounded-xl text-xs font-bold transition-all border
                                    data-[active=true]:{{ $colorview == 'dark' ? 'bg-brand-600 border-brand-400/30 text-white' : 'bg-red-500 border-red-400/20 text-white' }}
                                    data-[active=false]:border-transparent data-[active=false]:{{ $colorview == 'dark' ? 'text-gray-400 hover:text-white hover:bg-slate-900/50' : 'text-slate-600 hover:text-red-500 hover:bg-slate-100' }}">
                                    <span>Todos los productos</span>
                                </button>

                                <button data-filter="lubricantes" data-active="false" 
                                    class="catalog-filter-btn w-full text-left px-4 py-2.5 rounded-xl text-xs font-bold transition-all border
                                    data-[active=true]:{{ $colorview == 'dark' ? 'bg-brand-600 border-brand-400/30 text-white' : 'bg-red-500 border-red-400/20 text-white' }}
                                    data-[active=false]:border-transparent data-[active=false]:{{ $colorview == 'dark' ? 'text-gray-400 hover:text-white hover:bg-slate-900/50' : 'text-slate-600 hover:text-red-500 hover:bg-slate-100' }}">
                                    <span>Aceites y Lubricantes</span>
                                </button>

                                <button data-filter="frenos" data-active="false" 
                                    class="catalog-filter-btn w-full text-left px-4 py-2.5 rounded-xl text-xs font-bold transition-all border
                                    data-[active=true]:{{ $colorview == 'dark' ? 'bg-brand-600 border-brand-400/30 text-white' : 'bg-red-500 border-red-400/20 text-white' }}
                                    data-[active=false]:border-transparent data-[active=false]:{{ $colorview == 'dark' ? 'text-gray-400 hover:text-white hover:bg-slate-900/50' : 'text-slate-600 hover:text-red-500 hover:bg-slate-100' }}">
                                    <span>Sistema de Frenos</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-9">
                        <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            
                            <div data-prod-category="lubricantes" 
                                 data-title="Aceite Motul 7100 10W40"
                                 data-brand="MOTUL JAPAN"
                                 data-price="S/. 65.00"
                                 data-stock="En Stock"
                                 data-desc="Fluido de alto rendimiento 100% sintético con tecnología de Éster. Reduce la fricción interna del motor y mejora la respuesta del embrague húmedo bajo condiciones extremas de circuito o tráfico urbano pesado."
                                 data-specs='["Viscosidad: 10W40","Normas: API SN / JASO MA2","Base: 100% Sintético con Éster","Origen: Francia"]'
                                 class="product-card backdrop-blur-md rounded-2xl border p-4 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 group cursor-pointer
                                 {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5 hover:border-brand-500/30' : 'bg-white border-slate-200 hover:border-red-500/30 shadow-sm' }}">
                                
                                <div class="space-y-3">
                                    <div class="w-full h-44 rounded-xl border flex items-center justify-center relative overflow-hidden bg-gradient-to-b {{ $colorview == 'dark' ? 'from-slate-900 to-slate-950 border-white/5' : 'from-slate-50 to-slate-100/50 border-slate-200' }}">
                                        <div class="absolute top-2.5 right-2.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 text-[9px] font-black uppercase px-2 py-0.5 rounded-md">En Stock</div>
                                        <i data-lucide="droplet" class="w-12 h-12 {{ $colorview == 'dark' ? 'text-brand-500/20' : 'text-red-500/20' }} group-hover:scale-105 transition-transform duration-500"></i>
                                    </div>
                                    <div>
                                        <span class="text-[9px] {{ $colorview == 'dark' ? 'text-gray-600' : 'text-slate-400' }} font-bold uppercase font-mono">MOTUL 7100</span>
                                        <h3 class="text-xs font-black uppercase {{ $colorview == 'dark' ? 'text-gray-400 group-hover:text-brand-400' : 'text-slate-700 group-hover:text-red-500' }} mt-0.5">Aceite 100% Sintético 4T</h3>
                                    </div>
                                </div>
                                <div class="pt-4 border-t {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-100' }} mt-4 flex items-center justify-between">
                                    <span class="text-sm font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }} font-mono">S/. 65.00</span>
                                    <span class="text-[10px] {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }} font-bold flex items-center gap-1 group-hover:underline">Detalles <i data-lucide="eye" class="w-3 h-3"></i></span>
                                </div>
                            </div>

                            <div data-prod-category="frenos" 
                                 data-title="Pastillas de Freno Sinterizadas"
                                 data-brand="BREMBO ITALY"
                                 data-price="S/. 145.00"
                                 data-stock="En Stock"
                                 data-desc="Pastillas de freno compuestas por aleaciones metálicas sinterizadas de alta fricción. Ofrecen una mordida inicial agresiva y una excelente disipación térmica, evitando el efecto de fatiga ('fade') en frenadas prolongadas."
                                 data-specs='["Material: Sinterizado Metálico","Compatibilidad: ABS / Doble Disco","Uso: Street / Racing","Certificación: TÜV Approved"]'
                                 class="product-card backdrop-blur-md rounded-2xl border p-4 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 group cursor-pointer
                                 {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5 hover:border-brand-500/30' : 'bg-white border-slate-200 hover:border-red-500/30 shadow-sm' }}">
                                
                                <div class="space-y-3">
                                    <div class="w-full h-44 rounded-xl border flex items-center justify-center relative overflow-hidden bg-gradient-to-b {{ $colorview == 'dark' ? 'from-slate-900 to-slate-950 border-white/5' : 'from-slate-50 to-slate-100/50 border-slate-200' }}">
                                        <div class="absolute top-2.5 right-2.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 text-[9px] font-black uppercase px-2 py-0.5 rounded-md">En Stock</div>
                                        <i data-lucide="disc" class="w-12 h-12 {{ $colorview == 'dark' ? 'text-purple-500/20' : 'text-purple-600/20' }} group-hover:scale-105 transition-transform duration-500"></i>
                                    </div>
                                    <div>
                                        <span class="text-[9px] {{ $colorview == 'dark' ? 'text-gray-600' : 'text-slate-400' }} font-bold uppercase font-mono">BREMBO RACING</span>
                                        <h3 class="text-xs font-black uppercase {{ $colorview == 'dark' ? 'text-gray-400 group-hover:text-brand-400' : 'text-slate-700 group-hover:text-red-500' }} mt-0.5">Pastillas Sinterizadas</h3>
                                    </div>
                                </div>
                                <div class="pt-4 border-t {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-100' }} mt-4 flex items-center justify-between">
                                    <span class="text-sm font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }} font-mono">S/. 145.00</span>
                                    <span class="text-[10px] {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }} font-bold flex items-center gap-1 group-hover:underline">Detalles <i data-lucide="eye" class="w-3 h-3"></i></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div id="product-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
            <div id="modal-overlay" class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity duration-300 opacity-0"></div>

            <div id="modal-container" class="border rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative z-10 transition-all duration-300 scale-95 opacity-0 shadow-[0_0_50px_rgba(0,0,0,0.5)]
                {{ $colorview == 'dark' ? 'bg-slate-950/90 border-white/10' : 'bg-white border-slate-200' }}">
                
                <button id="close-modal-btn" class="absolute top-4 right-4 w-9 h-9 border rounded-xl flex items-center justify-center transition-all z-20
                    {{ $colorview == 'dark' ? 'bg-slate-900 hover:bg-white/10 border-white/5 text-gray-400 hover:text-white' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-500 hover:text-slate-800' }}">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 p-6 sm:p-8">
                    
                    <div class="md:col-span-5 space-y-4">
                        <div class="w-full h-64 sm:h-72 border rounded-2xl flex items-center justify-center relative overflow-hidden bg-gradient-to-b {{ $colorview == 'dark' ? 'from-slate-900 to-slate-950 border-white/5' : 'from-slate-50 to-slate-100/50 border-slate-200' }}">
                            <div id="modal-stock-badge" class="absolute top-3 left-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 text-[9px] font-black uppercase px-2 py-0.5 rounded-md">En Stock</div>
                            
                            <div id="modal-media-placeholder" class="{{ $colorview == 'dark' ? 'text-brand-500/20' : 'text-red-500/20' }}">
                                <i data-lucide="package" class="w-20 h-20"></i>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <button class="h-16 border rounded-xl flex items-center justify-center transition-all {{ $colorview == 'dark' ? 'bg-slate-900 border-brand-500/40 text-brand-400' : 'bg-white border-red-500/40 text-red-500' }}">
                                <i data-lucide="image" class="w-4 h-4"></i>
                            </button>
                            <button class="h-16 border rounded-xl flex items-center justify-center transition-all {{ $colorview == 'dark' ? 'bg-slate-900/40 border-white/5 text-gray-500 hover:text-white hover:border-white/20' : 'bg-slate-50 border-slate-200 text-slate-400 hover:text-slate-700' }}">
                                <i data-lucide="image" class="w-4 h-4"></i>
                            </button>
                            <button class="h-16 border rounded-xl flex items-center justify-center transition-all relative {{ $colorview == 'dark' ? 'bg-slate-900/40 border-white/5 text-purple-400 hover:text-purple-300 hover:border-purple-500/30' : 'bg-slate-50 border-slate-200 text-purple-600 hover:text-purple-800' }}">
                                <span class="absolute top-1 right-1 w-2 h-2 bg-purple-500 rounded-full animate-ping"></span>
                                <i data-lucide="play" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="md:col-span-7 flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <div>
                                <span id="modal-brand" class="text-[10px] text-gray-400 font-bold uppercase font-mono tracking-widest">MARCA</span>
                                <h3 id="modal-title" class="text-xl font-black uppercase tracking-tight mt-0.5 {{ $colorview == 'dark' ? 'text-gray-200' : 'text-slate-800' }}">Título del Producto</h3>
                                <div id="modal-price" class="text-lg font-mono font-black mt-1 {{ $colorview == 'dark' ? 'text-brand-400' : 'text-red-500' }}">S/. 00.00</div>
                            </div>

                            <div class="space-y-1.5">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Descripción del Componente:</h4>
                                <p id="modal-desc" class="text-xs leading-relaxed {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-600' }}">Descripción detallada.</p>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Especificaciones de Laboratorio:</h4>
                                <div id="modal-specs" class="grid grid-cols-2 gap-2 text-[11px]">
                                    </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t flex flex-col sm:flex-row gap-3 {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-100' }}">
                            <a id="modal-whatsapp-btn" href="#" target="_blank" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white py-3.5 rounded-xl text-xs font-black uppercase tracking-wider text-center transition-all shadow-[0_0_20px_rgba(16,185,129,0.2)] flex items-center justify-center gap-2">
                                <i data-lucide="message-circle" class="w-4 h-4"></i> Consultar Instalación
                            </a>
                            <button id="modal-reserve-btn" class="border px-6 py-3.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all
                                {{ $colorview == 'dark' ? 'bg-slate-900 hover:bg-white/5 border-white/10 text-gray-300' : 'bg-slate-100 hover:bg-slate-200 border-slate-300/60 text-slate-700' }}">
                                Reservar Cita
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        
        // Pasamos de forma segura el modo activo de PHP/Blade a una variable de JS
        const isDarkMode = "{{ $colorview }}" === "dark";

        // --- LÓGICA DE FILTRADO DE CATEGORÍAS ---
        const filterButtons = document.querySelectorAll(".catalog-filter-btn");
        const productCards = document.querySelectorAll(".product-card");

        filterButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                e.stopPropagation(); 
                
                // Resetear estado de activación en botones
                filterButtons.forEach(btn => btn.setAttribute("data-active", "false"));
                // Activar botón actual
                button.setAttribute("data-active", "true");

                const selectedFilter = button.getAttribute("data-filter");

                productCards.forEach(card => {
                    const productCategory = card.getAttribute("data-prod-category");
                    if (selectedFilter === "all" || productCategory === selectedFilter) {
                        card.classList.remove("hidden");
                    } else {
                        card.classList.add("hidden");
                    }
                });
            });
        });

        // --- LÓGICA DEL MODAL DINÁMICO ---
        const modal = document.getElementById("product-modal");
        const overlay = document.getElementById("modal-overlay");
        const container = document.getElementById("modal-container");
        const closeBtn = document.getElementById("close-modal-btn");

        const mTitle = document.getElementById("modal-title");
        const mBrand = document.getElementById("modal-brand");
        const mPrice = document.getElementById("modal-price");
        const mDesc = document.getElementById("modal-desc");
        const mStock = document.getElementById("modal-stock-badge");
        const mSpecsContainer = document.getElementById("modal-specs");
        const mWhatsAppBtn = document.getElementById("modal-whatsapp-btn");

        productCards.forEach(card => {
            card.addEventListener("click", () => {
                const title = card.getAttribute("data-title");
                const brand = card.getAttribute("data-brand");
                const price = card.getAttribute("data-price");
                const desc = card.getAttribute("data-desc");
                const stock = card.getAttribute("data-stock");
                const specs = JSON.parse(card.getAttribute("data-specs") || "[]");

                mTitle.textContent = title;
                mBrand.textContent = brand;
                mPrice.textContent = price;
                mDesc.textContent = desc;
                mStock.textContent = stock;

                // Limpiar y cargar especificaciones con adaptación cromática condicional
                mSpecsContainer.innerHTML = "";
                specs.forEach(spec => {
                    const block = document.createElement("div");
                    
                    // Asignamos estilos de tarjeta técnica basándonos en si es modo claro u oscuro
                    if (isDarkMode) {
                        block.className = "bg-slate-900/50 p-2.5 rounded-xl border border-white/5 text-gray-400 font-mono";
                    } else {
                        block.className = "bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-slate-600 font-mono shadow-xs";
                    }
                    
                    block.textContent = spec;
                    mSpecsContainer.appendChild(block);
                });

                mWhatsAppBtn.href = `https://wa.me/TUNUMERO?text=Hola%20KAEL,%20deseo%20consultar%20por%20la%20instalacion%20de:%20${encodeURIComponent(title)}`;

                modal.classList.remove("hidden");
                setTimeout(() => {
                    overlay.classList.remove("opacity-0");
                    container.classList.remove("opacity-0", "scale-95");
                }, 10);
                document.body.classList.add("overflow-hidden");
            });
        });

        const closeModal = () => {
            overlay.classList.add("opacity-0");
            container.classList.add("opacity-0", "scale-95");
            setTimeout(() => {
                modal.classList.add("hidden");
                document.body.classList.remove("overflow-hidden");
            }, 300);
        };

        closeBtn.addEventListener("click", closeModal);
        overlay.addEventListener("click", closeModal);
    });
</script>