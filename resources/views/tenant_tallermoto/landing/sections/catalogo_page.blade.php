<main class="relative min-h-screen overflow-hidden">
    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">
        <section id="catalogo-productos" class="py-20 relative overflow-hidden">
            <div class="absolute top-10 right-10 w-80 h-80 bg-brand-600/5 rounded-full blur-[100px] pointer-events-none">
            </div>
            <div
                class="absolute bottom-10 left-10 w-80 h-80 bg-purple-600/5 rounded-full blur-[100px] pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <!-- HEADER -->
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12 border-b {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-200' }} pb-8">
                    <div class="space-y-3">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 {{ $colorview == 'dark' ? 'bg-brand-950/40 border-brand-500/20 text-brand-400' : 'bg-red-50 border-red-200 text-red-600' }} border rounded-full text-[10px] font-bold uppercase tracking-widest">
                            <i data-lucide="package" class="w-3 h-3"></i> Componentes Originales & Performance
                        </div>
                        <h2
                            class="text-3xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }} uppercase tracking-tight">
                            Catálogo de <span
                                class="{{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}">Repuestos</span>
                        </h2>
                        <p class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-500' }} max-w-md">
                            Garantiza la longevidad de tu ruta con piezas certificadas de fábrica y fluidos de alta
                            competición.
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

                    <!-- SIDEBAR: FILTROS POR CLASE Y CATEGORÍA -->
                    <div class="lg:col-span-3 space-y-4 sticky top-24">
                        <div
                            class="{{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5' : 'bg-white border-slate-200/80 shadow-sm' }} backdrop-blur-md p-5 rounded-2xl border space-y-3">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 flex items-center gap-2">
                                <i data-lucide="sliders-horizontal"
                                    class="w-3.5 h-3.5 {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }}"></i>
                                Clasificación
                            </h4>

                            <div id="catalog-filters" class="flex flex-col gap-2">
                                <!-- Opción Todos -->
                                <button data-filter-class="all" data-active="true"
                                    class="catalog-filter-btn w-full text-left px-4 py-2.5 rounded-xl text-xs font-bold transition-all border
                            data-[active=true]:{{ $colorview == 'dark' ? 'bg-brand-600 border-brand-400/30 text-white' : 'bg-red-500 border-red-400/20 text-white' }}
                            data-[active=false]:border-transparent data-[active=false]:{{ $colorview == 'dark' ? 'text-gray-400 hover:text-white hover:bg-slate-900/50' : 'text-slate-600 hover:text-red-500 hover:bg-slate-100' }}">
                                    <span>Todos los productos</span>
                                </button>

                                <!-- Estructura Dinámica con Blade (Clases y Categorías) -->
                                @foreach ($dataClase as $clase)
                                    <div
                                        class="class-group border-t {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-100' }} pt-2">
                                        <!-- Título de Clase -->
                                        <button type="button"
                                            class="class-toggle-btn w-full flex items-center justify-between text-left px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-colors {{ $colorview == 'dark' ? 'text-gray-300 hover:text-white' : 'text-slate-700 hover:text-red-500' }}">

                                            <span class="text-left flex-1 pr-2">{{ $clase->CLA_Nombre }}</span>

                                            <i data-lucide="chevron-down"
                                                class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 text-gray-500"></i>
                                        </button>

                                        <!-- Subcategorías pertenecientes a la Clase -->
                                        <div class="category-list hidden flex-col gap-1 pl-3 mt-1">
                                            @foreach ($dataCategoria->where('CLA_Id', $clase->CLA_Id) as $categoria)
                                                <button data-filter-category="{{ $categoria->CAT_Id }}"
                                                    data-active="false"
                                                    class="catalog-filter-btn w-full text-left px-3 py-1.5 rounded-lg text-[11px] font-medium transition-all border flex items-center justify-between
            data-[active=true]:{{ $colorview == 'dark' ? 'bg-brand-600/20 text-brand-400 border-brand-500/30' : 'bg-red-50 text-red-600 border-red-200 font-bold' }}
            data-[active=false]:border-transparent data-[active=false]:{{ $colorview == 'dark' ? 'text-gray-400 hover:text-white hover:bg-slate-900/40' : 'text-slate-600 hover:text-red-500 hover:bg-slate-50' }}">
                                                    <span>{{ $categoria->CAT_Nombre }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                    <!-- GRID DE PRODUCTOS -->
                    <div class="lg:col-span-9">
                        <!-- Grid de Productos -->
                        <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @include('tenant_tallermoto.landing.sections.partials.product-cards', [
                                'dataProductos' => $dataProductos,
                                'colorview' => $colorview,
                                'tiponegocio' => $tiponegocio,
                                'tenantid' => $tenantid,
                            ])
                        </div>

                        <!-- Botón Cargar Más -->
                        <div class="mt-8 flex justify-center">
                            <button id="load-more-btn" type="button"
                                data-next-page="{{ $dataProductos->nextPageUrl() }}"
                                class="{{ $dataProductos->hasMorePages() ? '' : 'hidden' }} px-6 py-2.5 rounded-xl text-xs font-bold transition-all border cursor-pointer {{ $colorview == 'dark' ? 'bg-slate-900 border-white/10 text-white hover:bg-slate-800' : 'bg-slate-100 border-slate-200 text-slate-800 hover:bg-slate-200' }}">
                                <span>Cargar más repuestos</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div id="product-modal"
            class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
            <div id="modal-overlay"
                class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity duration-300 opacity-0"></div>

            <div id="modal-container"
                class="border rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative z-10 transition-all duration-300 scale-95 opacity-0 shadow-[0_0_50px_rgba(0,0,0,0.5)]
                {{ $colorview == 'dark' ? 'bg-slate-950/90 border-white/10' : 'bg-white border-slate-200' }}">

                <button id="close-modal-btn"
                    class="absolute top-4 right-4 w-9 h-9 border rounded-xl flex items-center justify-center transition-all z-20
                    {{ $colorview == 'dark' ? 'bg-slate-900 hover:bg-white/10 border-white/5 text-gray-400 hover:text-white' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-500 hover:text-slate-800' }}">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 p-6 sm:p-8">

                    <div class="md:col-span-5 space-y-4">
                        <div
                            class="w-full h-64 sm:h-72 border rounded-2xl flex items-center justify-center relative overflow-hidden bg-gradient-to-b {{ $colorview == 'dark' ? 'from-slate-900 to-slate-950 border-white/5' : 'from-slate-50 to-slate-100/50 border-slate-200' }}">
                            <div id="modal-stock-badge"
                                class="absolute top-3 left-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 text-[9px] font-black uppercase px-2 py-0.5 rounded-md">
                                En Stock</div>

                            <!-- Elemento para la imagen dinámica -->
                            <img id="modal-img" src="" alt="Producto" class="w-full h-full object-cover hidden">

                            <!-- Placeholder con ícono Lucide por defecto -->
                            <div id="modal-media-placeholder"
                                class="{{ $colorview == 'dark' ? 'text-brand-500/20' : 'text-red-500/20' }}">
                                <i data-lucide="package" class="w-20 h-20"></i>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <button
                                class="h-16 border rounded-xl flex items-center justify-center transition-all {{ $colorview == 'dark' ? 'bg-slate-900 border-brand-500/40 text-brand-400' : 'bg-white border-red-500/40 text-red-500' }}">
                                <i data-lucide="image" class="w-4 h-4"></i>
                            </button>
                            {{-- <button
                                class="h-16 border rounded-xl flex items-center justify-center transition-all {{ $colorview == 'dark' ? 'bg-slate-900/40 border-white/5 text-gray-500 hover:text-white hover:border-white/20' : 'bg-slate-50 border-slate-200 text-slate-400 hover:text-slate-700' }}">
                                <i data-lucide="image" class="w-4 h-4"></i>
                            </button>
                            <button
                                class="h-16 border rounded-xl flex items-center justify-center transition-all relative {{ $colorview == 'dark' ? 'bg-slate-900/40 border-white/5 text-purple-400 hover:text-purple-300 hover:border-purple-500/30' : 'bg-slate-50 border-slate-200 text-purple-600 hover:text-purple-800' }}">
                                <span
                                    class="absolute top-1 right-1 w-2 h-2 bg-purple-500 rounded-full animate-ping"></span>
                                <i data-lucide="play" class="w-4 h-4"></i>
                            </button> --}}
                        </div>
                    </div>

                    <div class="md:col-span-7 flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <div>
                                <span id="modal-brand"
                                    class="text-[10px] text-gray-400 font-bold uppercase font-mono tracking-widest">MARCA</span>
                                <h3 id="modal-title"
                                    class="text-xl font-black uppercase tracking-tight mt-0.5 {{ $colorview == 'dark' ? 'text-gray-200' : 'text-slate-800' }}">
                                    Título del Producto</h3>
                                <div id="modal-price"
                                    class="text-lg font-mono font-black mt-1 {{ $colorview == 'dark' ? 'text-brand-400' : 'text-red-500' }}">
                                    S/. 00.00</div>
                            </div>

                            <div class="space-y-1.5">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Descripción
                                    del Componente:</h4>
                                <p id="modal-desc"
                                    class="text-xs leading-relaxed {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-600' }}">
                                    Descripción detallada.</p>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    Especificaciones de Laboratorio:</h4>
                                <div id="modal-specs" class="grid grid-cols-2 gap-2 text-[11px]">
                                </div>
                            </div>
                        </div>

                        <div
                            class="pt-4 border-t flex flex-col sm:flex-row gap-3 {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-100' }}">
                            <a id="modal-whatsapp-btn" href="#" target="_blank"
                                class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white py-3.5 rounded-xl text-xs font-black uppercase tracking-wider text-center transition-all shadow-[0_0_20px_rgba(16,185,129,0.2)] flex items-center justify-center gap-2">
                                <i data-lucide="message-circle" class="w-4 h-4"></i> Consultar Instalación
                            </a>
                            <button id="modal-reserve-btn"
                                class="border px-6 py-3.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all
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

        // =========================================================================
        // 1. ACOPLE DE DELEGACIÓN PARA CLASES (ACORDEÓN)
        // =========================================================================
        const classToggles = document.querySelectorAll('.class-toggle-btn');
        classToggles.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const classGroup = btn.closest('.class-group');
                const categoryList = classGroup ? classGroup.querySelector('.category-list') :
                    null;
                const icon = btn.querySelector('i, svg');

                if (categoryList) {
                    const isHidden = categoryList.classList.contains('hidden') ||
                        categoryList.style.display === 'none' ||
                        window.getComputedStyle(categoryList).display === 'none';

                    if (isHidden) {
                        categoryList.classList.remove('hidden');
                        categoryList.style.display = 'flex';
                    } else {
                        categoryList.style.display = 'none';
                    }
                }

                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            });
        });

        // =========================================================================
        // 2. FILTRADO SERVIDOR VÍA AJAX (CATEGORÍAS)
        // =========================================================================
        const filterBtns = document.querySelectorAll('[data-filter-category], [data-filter-class="all"]');
        const grid = document.getElementById("products-grid");
        const loadMoreBtn = document.getElementById("load-more-btn");
        let currentCatId = 'all';

        filterBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                filterBtns.forEach(b => b.setAttribute('data-active', 'false'));
                btn.setAttribute('data-active', 'true');

                // Obtener ID de la categoría
                currentCatId = btn.getAttribute('data-filter-category') || 'all';

                // Realizar petición servidor enviando cat_id
                const fetchUrl = `{{ route('web.catalogo') }}?cat_id=${currentCatId}`;

                // Spinner temporal
                grid.innerHTML =
                    '<div class="col-span-full text-center py-12 text-xs font-bold opacity-60">Cargando repuestos...</div>';

                fetch(fetchUrl, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Reemplazar completamente el Grid
                        grid.innerHTML = data.html;

                        // Re-inicializar iconos de Lucide
                        if (typeof lucide !== 'undefined') lucide.createIcons();

                        // Ajustar el botón "Cargar más" a la nueva consulta de la DB
                        if (data.has_more) {
                            loadMoreBtn.setAttribute("data-next-page", data.next_page);
                            loadMoreBtn.classList.remove("hidden");
                        } else {
                            loadMoreBtn.classList.add("hidden");
                        }
                    })
                    .catch(err => console.error("Error al filtrar por categoría:", err));
            });
        });

        // =========================================================================
        // 3. BOTÓN "CARGAR MÁS" (PAGINACIÓN AJAX INCREMENTAL)
        // =========================================================================
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener("click", () => {
                const nextPageUrl = loadMoreBtn.getAttribute("data-next-page");
                if (!nextPageUrl) return;

                loadMoreBtn.disabled = true;
                loadMoreBtn.querySelector("span").innerText = "Cargando repuestos...";

                fetch(nextPageUrl, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Concatenar al final del Grid
                        grid.insertAdjacentHTML("beforeend", data.html);

                        if (typeof lucide !== 'undefined') lucide.createIcons();

                        if (data.has_more) {
                            loadMoreBtn.setAttribute("data-next-page", data.next_page);
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.querySelector("span").innerText = "Cargar más repuestos";
                        } else {
                            loadMoreBtn.classList.add("hidden");
                        }
                    })
                    .catch(err => {
                        console.error("Error al cargar más repuestos:", err);
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.querySelector("span").innerText = "Reintentar";
                    });
            });
        }

        // =========================================================================
        // 4. MODAL DETALLES (EVENT DELEGATION PARA TARJETAS DINÁMICAS)
        // =========================================================================
        const isDarkMode = "{{ $colorview }}" === "dark";
        const modal = document.getElementById("product-modal");
        const overlay = document.getElementById("modal-overlay");
        const container = document.getElementById("modal-container");
        const closeBtn = document.getElementById("close-modal-btn");

        const mTitle = document.getElementById("modal-title");
        const mBrand = document.getElementById("modal-brand");
        const mPrice = document.getElementById("modal-price");
        const mDesc = document.getElementById("modal-desc");
        const mStock = document.getElementById("modal-stock-badge");
        const mImg = document.getElementById("modal-img");
        const mPlaceholder = document.getElementById("modal-media-placeholder");
        const mSpecsContainer = document.getElementById("modal-specs");
        const mWhatsAppBtn = document.getElementById("modal-whatsapp-btn");

        // Escuchar en el contenedor 'grid' para atrapar clics en tarjetas creadas dinámicamente
        if (grid) {
            grid.addEventListener("click", (e) => {
                const card = e.target.closest(".product-card");
                if (!card) return;

                const title = card.getAttribute("data-title") || "";
                const brand = card.getAttribute("data-brand") || "";
                const price = card.getAttribute("data-price") || "";
                const desc = card.getAttribute("data-descripcion") || "";
                const stock = card.getAttribute("data-stock") || "";
                const imgUrl = card.getAttribute("data-url") || "";
                const hasImage = card.getAttribute("data-has-image") === "true";

                let specs = [];
                try {
                    specs = JSON.parse(card.getAttribute("data-specs") || "[]");
                } catch (err) {
                    console.error("Error al parsear data-specs JSON:", err);
                }

                if (hasImage && imgUrl !== "") {
                    mImg.src = imgUrl;
                    mImg.classList.remove("hidden");
                    mPlaceholder.classList.add("hidden");
                } else {
                    mImg.src = "";
                    mImg.classList.add("hidden");
                    mPlaceholder.classList.remove("hidden");
                }

                mTitle.textContent = title;
                mBrand.textContent = brand;
                mPrice.textContent = price;
                mDesc.textContent = desc;
                mStock.textContent = stock;
                mStock.textContent = stock;

                mSpecsContainer.innerHTML = "";
                if (Array.isArray(specs) && specs.length > 0) {
                    specs.forEach(spec => {
                        const block = document.createElement("div");
                        block.className = isDarkMode ?
                            "bg-slate-900/50 p-2.5 rounded-xl border border-white/5 text-gray-400 font-mono" :
                            "bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-slate-600 font-mono shadow-xs";

                        block.textContent = typeof spec === 'object' ? JSON.stringify(spec) :
                            spec;
                        mSpecsContainer.appendChild(block);
                    });
                } else {
                    mSpecsContainer.innerHTML =
                        `<span class="text-gray-500 italic text-xs col-span-2">Sin especificaciones disponibles</span>`;
                }

                mWhatsAppBtn.href =
                    `https://wa.me/TUNUMERO?text=Hola%20KAEL,%20deseo%20consultar%20por%20la%20instalacion%20de:%20${encodeURIComponent(title)}`;

                modal.classList.remove("hidden");
                setTimeout(() => {
                    overlay.classList.remove("opacity-0");
                    container.classList.remove("opacity-0", "scale-95");
                }, 10);
                document.body.classList.add("overflow-hidden");
            });
        }

        // Cerrar Modal
        const closeModal = () => {
            overlay.classList.add("opacity-0");
            container.classList.add("opacity-0", "scale-95");
            setTimeout(() => {
                modal.classList.add("hidden");
                document.body.classList.remove("overflow-hidden");
            }, 300);
        };

        if (closeBtn) closeBtn.addEventListener("click", closeModal);
        if (overlay) overlay.addEventListener("click", closeModal);
    });
</script>
