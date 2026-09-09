@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', $cotizacion ? 'Editar Cotización' : 'Nueva Cotización')

@section('contenido')

    <style>
        :root {
            --primary: #6C3BFF;
            --primary-light: #8B5CF6;
            --success: #22C55E;
            --danger: #EF4444;
            --dark: #111827;
            --gray: #6B7280;
            --border: #E5E7EB;
            --bg: #F4F7FB;
        }

        body { background: var(--bg); font-family: 'Inter', sans-serif; }

        .pos-wrapper { padding: 10px; }

        .pos-panel {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #EEF2F7;
            box-shadow: 0 4px 18px rgba(0,0,0,.04);
            height: calc(100vh - 70px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 14px;
        }

        .pos-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .pos-title { font-size: 18px; font-weight: 800; color: var(--dark); }
        .pos-subtitle { font-size: 12px; color: var(--gray); }

        .reserva-banner {
            background: #EEF2FF;
            border: 1px solid #C7D2FE;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12.5px;
            color: #3730A3;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* SEARCH + CATEGORIES */
        .search-box {
            height: 46px;
            background: #F9FAFB;
            border: 1px solid var(--border);
            border-radius: 14px;
            display: flex;
            align-items: center;
            padding: 0 14px;
        }
        .search-box input { border: none; background: transparent; width: 100%; outline: none; font-size: 14px; margin-left: 10px; }

        .categories { display: flex; gap: 8px; overflow-x: auto; padding: 10px 0 6px; scrollbar-width: none; }
        .categories::-webkit-scrollbar { display: none; }
        .category-btn {
            background: #fff; border: 1px solid var(--border); border-radius: 12px;
            height: 36px; padding: 0 16px; font-size: 12.5px; font-weight: 700;
            display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;
        }
        .category-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        .item-libre-btn {
            border: 1px dashed #c7cdd6; background: #fff; border-radius: 12px;
            font-size: 12.5px; font-weight: 700; color: var(--primary);
            padding: 0 14px; height: 36px; flex-shrink: 0; white-space: nowrap;
        }
        .item-libre-btn:hover { background: #F5F3FF; }

        /* PRODUCTS */
        .products-grid {
            flex: 1; align-content: start; overflow-y: auto; overflow-x: hidden;
            display: grid; grid-template-columns: repeat(auto-fill, minmax(170px,1fr));
            gap: 12px; padding-right: 4px;
        }
        .product-card { background: #fff; border: 1px solid #EEF2F7; border-radius: 16px; padding: 10px; transition: .2s ease; }
        .product-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,.06); }
        .product-image { width: 100%; height: 100px; background: #F9FAFB; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }
        .product-image img { width: 62px; height: 62px; object-fit: contain; }
        .product-name { font-size: 12.5px; font-weight: 700; color: var(--dark); line-height: 1.3; height: 32px; overflow: hidden; }
        .product-footer { margin-top: 8px; display: flex; justify-content: space-between; align-items: center; }
        .product-price { font-size: 16px; font-weight: 800; color: var(--primary); }
        .product-stock { font-size: 10px; color: var(--success); font-weight: 700; }

        .load-more-container { padding: 16px 0; display: flex; justify-content: center; }
        .btn-load-more { border: 1px solid var(--border); background: #fff; height: 40px; padding: 0 20px; border-radius: 14px; font-weight: 700; }
        .btn-load-more:hover { background: #F9FAFB; }

        /* CART */
        .cart-list { flex: 1; overflow-y: auto; padding-right: 4px; }
        .cart-item { display: flex; gap: 10px; background: #fff; border: 1px solid #EEF2F7; border-radius: 14px; padding: 10px; margin-bottom: 9px; }
        .cart-image { width: 50px; height: 50px; background: #F9FAFB; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cart-image img { width: 36px; height: 36px; object-fit: contain; }
        .cart-info { flex: 1; min-width: 0; }
        .cart-name { font-size: 12.5px; font-weight: 700; color: var(--dark); line-height: 1.3; }
        .cart-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 6px; gap: 6px; }
        .qty-control { display: flex; align-items: center; background: #F9FAFB; border-radius: 10px; overflow: hidden; border: 1px solid var(--border); flex-shrink: 0; }
        .qty-btn { width: 24px; height: 24px; border: none; background: #fff; font-weight: 700; font-size: 12px; }
        .qty-value { width: 24px; text-align: center; font-size: 11.5px; font-weight: 700; }
        .cart-price-input {
            width: 66px; font-size: 12px; font-weight: 700; border: 1px solid var(--border);
            border-radius: 8px; padding: 2px 5px; text-align: right; color: var(--dark);
        }
        .cart-price-input:focus { outline: none; border-color: var(--primary); }
        .cart-subtotal { font-size: 13px; font-weight: 800; color: var(--primary); white-space: nowrap; }
        .btn-remove { width: 24px; height: 24px; border: none; border-radius: 8px; background: #FEF2F2; color: var(--danger); font-size: 11px; flex-shrink: 0; }

        .empty-cart { text-align: center; color: #9CA3AF; font-size: 13px; padding: 40px 10px; }

        /* SIDE FIELDS */
        .field-block { margin-top: 10px; }
        .field-label { font-size: 11px; color: var(--gray); font-weight: 700; text-transform: uppercase; letter-spacing: .03em; margin-bottom: 4px; display: block; }
        .field-input {
            width: 100%; border: 1px solid var(--border); border-radius: 12px; padding: 9px 12px;
            font-size: 13px; background: #F9FAFB;
        }
        .field-input:focus { outline: none; border-color: var(--primary); background: #fff; }

        .client-selector {
            display: flex; align-items: center; justify-content: space-between;
            background: #F9FAFB; border: 1px solid var(--border); border-radius: 12px;
            padding: 9px 12px; cursor: pointer;
        }
        .client-selector:hover { border-color: var(--primary); }
        .client-left { display: flex; align-items: center; gap: 9px; min-width: 0; }
        .client-avatar {
            width: 30px; height: 30px; border-radius: 50%; background: #EEF2FF; color: var(--primary);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .client-name { font-size: 12.5px; font-weight: 700; color: var(--dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px; }
        .client-subtitle { font-size: 10.5px; color: var(--gray); }

        .total-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 16px; padding: 14px; color: #fff; margin-top: 12px;
        }
        .total-label { font-size: 11px; opacity: .8; }
        .total-amount { font-size: 28px; font-weight: 900; }
        .total-items { background: rgba(255,255,255,.18); padding: 6px 11px; border-radius: 14px; font-size: 11.5px; font-weight: 700; }
        .btn-guardar {
            width: 100%; height: 48px; border: none; border-radius: 14px; background: #fff;
            color: var(--primary); font-size: 14.5px; font-weight: 800; margin-top: 10px;
        }
        .btn-guardar:hover { transform: translateY(-1px); }
        .btn-guardar:disabled { opacity: .6; }

        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 10px; }

        /* CLIENT MODAL */
        .client-search { display: flex; align-items: center; gap: 8px; background: #F9FAFB; border: 1px solid var(--border); border-radius: 12px; padding: 10px 14px; margin-bottom: 10px; }
        .client-search input { border: none; background: transparent; outline: none; width: 100%; font-size: 13.5px; }
        .client-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 4px; border-bottom: 1px solid #F1F2F5; }
        .client-row-name { font-size: 13px; font-weight: 700; color: var(--dark); }
        .client-row-meta { font-size: 11px; color: var(--gray); }
    </style>

    <div class="container-fluid pos-wrapper">
        <div class="row">
            <!-- LEFT: cotizacion panel -->
            <div class="col-lg-4">
                <div class="pos-panel">
                    <div class="pos-header">
                        <div>
                            <div class="pos-title">{{ $cotizacion ? 'Editando COT-' . str_pad($cotizacion->COT_Id, 5, '0', STR_PAD_LEFT) : 'Cotización' }}</div>
                            <div class="pos-subtitle">No descuenta stock ni genera comprobante</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-light" onclick="limpiarCarrito()">Limpiar</button>
                    </div>

                    @if ($reservaId)
                        <div class="reserva-banner">
                            <i class="fa fa-motorcycle"></i>
                            <span>Cotizando para la reserva en curso{{ $prefillCliente['moto'] ?? null ? ' — ' . $prefillCliente['moto'] : '' }}{{ $prefillCliente['placa'] ?? null ? ' (' . $prefillCliente['placa'] . ')' : '' }}</span>
                        </div>
                    @endif

                    <div class="cart-list" id="cartItems"></div>

                    <div class="field-block">
                        <label class="field-label">Cliente</label>
                        <input type="hidden" id="cliente_id">
                        <div class="client-selector" onclick="loadClients(); $('#modalClientes').modal('show')">
                            <div class="client-left">
                                <div class="client-avatar"><i class="fas fa-user"></i></div>
                                <div>
                                    <div class="client-name" id="clientName">Seleccionar cliente</div>
                                    <div class="client-subtitle" id="clientSubtitle">Obligatorio</div>
                                </div>
                            </div>
                            <i class="fas fa-search text-muted"></i>
                        </div>
                    </div>

                    <div class="field-block">
                        <label class="field-label">Válida hasta (opcional)</label>
                        <input type="date" id="fechaVencimiento" class="field-input">
                    </div>

                    <div class="field-block">
                        <label class="field-label">Observación (opcional)</label>
                        <textarea id="observacion" class="field-input" rows="2" placeholder="Ej. Precio válido por 7 días, incluye instalación..."></textarea>
                    </div>

                    <div class="total-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="total-label">TOTAL COTIZADO</div>
                                <div class="total-amount" id="cartTotal">S/ 0.00</div>
                            </div>
                            <div class="total-items" id="cartItemsCount">0 items</div>
                        </div>
                        <button type="button" class="btn-guardar" id="btnGuardar" onclick="guardarCotizacion()">
                            <i class="fas fa-file-invoice mr-2"></i>{{ $cotizacion ? 'Guardar cambios' : 'Guardar cotización' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT: products -->
            <div class="col-lg-8">
                <div class="pos-panel">
                    <div class="pos-header">
                        <div>
                            <div class="pos-title">Productos y servicios</div>
                            <div class="pos-subtitle">Catálogo disponible</div>
                        </div>
                        <a href="{{ tenant_url('tenant.ventas.cotizacion.index') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>

                    <div class="search-box">
                        <i class="fas fa-search text-muted"></i>
                        <input class="search-input" type="text" placeholder="Buscar producto...">
                    </div>

                    <div class="categories">
                        <button class="category-btn active" data-id="all">Todas</button>
                        <button type="button" class="item-libre-btn" onclick="abrirItemLibre()">
                            <i class="fa fa-plus"></i> Item libre / mano de obra
                        </button>
                    </div>

                    <div class="products-grid" id="productsGrid"></div>
                    <div class="load-more-container">
                        <button id="btnLoadMore" class="btn-load-more">Cargar más</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: item libre -->
    <div class="modal fade" id="modalItemLibre" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header" style="border:none;">
                    <h5 class="modal-title"><i class="fa fa-pen mr-2"></i>Item libre</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="field-block mt-0">
                        <label class="field-label">Nombre</label>
                        <input type="text" id="libreNombre" class="field-input" placeholder="Ej. Mano de obra, instalación...">
                    </div>
                    <div class="row">
                        <div class="col-6 field-block">
                            <label class="field-label">Cantidad</label>
                            <input type="number" id="libreCantidad" class="field-input" value="1" min="0.01" step="0.01">
                        </div>
                        <div class="col-6 field-block">
                            <label class="field-label">Precio unitario (S/)</label>
                            <input type="number" id="librePrecio" class="field-input" placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-block mt-2" onclick="agregarItemLibre()">
                        <i class="fa fa-plus"></i> Agregar a la cotización
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: clientes -->
    <div class="modal fade" id="modalClientes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header" style="border:none;">
                    <h5 class="modal-title"><i class="fas fa-users mr-2"></i>Seleccionar cliente</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="client-search">
                        <i class="fas fa-search text-muted"></i>
                        <input type="text" id="searchClient" placeholder="Buscar por nombre, documento o celular...">
                    </div>
                    <div id="clientList" style="max-height:280px;overflow-y:auto;"></div>

                    <hr>
                    <div class="pos-subtitle mb-2">¿No está en la lista? Regístralo rápido:</div>
                    <div class="row">
                        <div class="col-4"><input type="text" id="nuevoDocumento" class="field-input" placeholder="DNI/RUC"></div>
                        <div class="col-5"><input type="text" id="nuevoNombre" class="field-input" placeholder="Nombre"></div>
                        <div class="col-3"><input type="text" id="nuevoCelular" class="field-input" placeholder="Celular"></div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-block mt-2" onclick="crearCliente()">
                        <i class="fa fa-plus"></i> Crear y seleccionar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let currentPage = 1;
        let currentCategory = 'all';
        let currentSearch = '';
        let cart = [];

        @php
            $itemsParaJs = $cotizacion ? $cotizacion->items->map(function ($i) {
                return [
                    'PRO_Id' => $i->PRO_Id,
                    'PRO_Nombre' => $i->nombre(),
                    'PRO_PrecioBaseVenta' => $i->COI_PrecioUnitario,
                    'PRO_Imagen' => $i->producto->PRO_Imagen ?? null,
                    'quantity' => (float) $i->COI_Cantidad,
                    'descuento' => (float) $i->COI_Descuento,
                    'esLibre' => $i->esLibre(),
                ];
            })->values() : [];
        @endphp
        const EDITANDO_ID = @json($cotizacion->COT_Id ?? null);
        const RESERVA_ID = @json($reservaId);
        const PREFILL_CLIENTE = @json($prefillCliente);
        const COTIZACION_ITEMS = @json($itemsParaJs);

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 1800, timerProgressBar: true
        });

        function imagenProducto(nombreArchivo) {
            return nombreArchivo
                ? `/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/producto/${nombreArchivo}`
                : `/images/imagen_default.png`;
        }

        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');

            if (COTIZACION_ITEMS.length) {
                cart = COTIZACION_ITEMS;
                renderCart();
            }

            @if ($cotizacion)
                $('#fechaVencimiento').val(@json($cotizacion->COT_FechaVencimiento?->toDateString()));
                $('#observacion').val(@json($cotizacion->COT_Observacion));
                selectClient(@json($cotizacion->cliente->CLI_Nombre ?? ''), @json($cotizacion->cliente->CLI_NumDocumento ?? ''), @json($cotizacion->CLI_Id));
            @else
                if (PREFILL_CLIENTE && PREFILL_CLIENTE.cliente_id) {
                    selectClient(PREFILL_CLIENTE.nombre, PREFILL_CLIENTE.documento || PREFILL_CLIENTE.celular || '', PREFILL_CLIENTE.cliente_id);
                }
            @endif

            $('.category-btn').on('click', function () {
                $('.category-btn').removeClass('active');
                $(this).addClass('active');
                currentCategory = $(this).data('id');
                currentPage = 1;
                loadProducts();
            });

            let buscarTimeout = null;
            $('.search-input').on('keyup', function () {
                clearTimeout(buscarTimeout);
                let valor = $(this).val();
                buscarTimeout = setTimeout(function () {
                    currentSearch = valor;
                    currentPage = 1;
                    loadProducts();
                }, 300);
            });

            $('#btnLoadMore').on('click', function () {
                currentPage++;
                loadProducts(currentPage);
            });

            $('#searchClient').on('keyup', function () {
                loadClients($(this).val());
            });

            loadProducts();
        });

        function loadProducts(page = 1) {
            $.ajax({
                url: "{{ route('tenant.ventas.venta.productos') }}",
                method: 'GET',
                data: { page: page, categoria: currentCategory, search: currentSearch }
            }).done(function (response) {
                renderProducts(response.data, page);
                $('#btnLoadMore').closest('.load-more-container').toggle(page < response.last_page);
            });
        }

        function renderProducts(productos, page) {
            let html = '';
            productos.forEach(function (p) {
                html += `
                    <div class="product-card">
                        <div class="product-image"><img src="${imagenProducto(p.PRO_Imagen)}" onerror="this.src='/images/imagen_default.png'"></div>
                        <div class="product-name">${p.PRO_Nombre}</div>
                        <div class="product-footer">
                            <div>
                                <div class="product-price">S/ ${parseFloat(p.PRO_PrecioBaseVenta).toFixed(2)}</div>
                                <div class="product-stock">Stock ${p.PRO_Cantidad}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary rounded-circle" onclick='agregarProducto(${JSON.stringify(p)})'>
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>`;
            });
            $('#productsGrid').html(page == 1 ? html : $('#productsGrid').html() + html);
        }

        function agregarProducto(p) {
            let existente = cart.find(i => i.PRO_Id == p.PRO_Id);
            if (existente) {
                existente.quantity++;
            } else {
                cart.push({
                    PRO_Id: p.PRO_Id,
                    PRO_Nombre: p.PRO_Nombre,
                    PRO_PrecioBaseVenta: p.PRO_PrecioBaseVenta,
                    PRO_Imagen: p.PRO_Imagen,
                    quantity: 1,
                    descuento: 0,
                    esLibre: false,
                });
            }
            renderCart();
        }

        function abrirItemLibre() {
            $('#libreNombre').val('');
            $('#libreCantidad').val(1);
            $('#librePrecio').val('');
            $('#modalItemLibre').modal('show');
        }

        function agregarItemLibre() {
            let nombre = ($('#libreNombre').val() || '').trim();
            let cantidad = parseFloat($('#libreCantidad').val());
            let precio = parseFloat($('#librePrecio').val());

            if (!nombre || !(cantidad > 0) || !(precio >= 0)) {
                Toast.fire({ icon: 'warning', title: 'Completa nombre, cantidad y precio' });
                return;
            }

            cart.push({
                PRO_Id: null,
                PRO_Nombre: nombre,
                PRO_PrecioBaseVenta: precio,
                PRO_Imagen: null,
                quantity: cantidad,
                descuento: 0,
                esLibre: true,
            });
            renderCart();
            $('#modalItemLibre').modal('hide');
        }

        function renderCart() {
            let html = '';
            let total = 0;
            let totalItems = 0;

            cart.forEach(function (item, idx) {
                let precio = parseFloat(item.PRO_PrecioBaseVenta) || 0;
                let subtotal = (item.quantity * precio) - (item.descuento || 0);
                total += subtotal;
                totalItems += parseFloat(item.quantity);

                html += `
                    <div class="cart-item">
                        <div class="cart-image"><img src="${imagenProducto(item.PRO_Imagen)}" onerror="this.src='/images/imagen_default.png'"></div>
                        <div class="cart-info">
                            <div class="cart-name">${item.PRO_Nombre}${item.esLibre ? ' <span class="badge badge-light">libre</span>' : ''}</div>
                            <div class="cart-bottom">
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" onclick="cambiarCantidad(${idx}, -1)">-</button>
                                    <span class="qty-value">${formatoCantidad(item.quantity)}</span>
                                    <button type="button" class="qty-btn" onclick="cambiarCantidad(${idx}, 1)">+</button>
                                </div>
                                <span>S/ <input type="number" class="cart-price-input" value="${precio}" step="0.01" min="0" onchange="cambiarPrecio(${idx}, this.value)"></span>
                                <span class="cart-subtotal">S/ ${subtotal.toFixed(2)}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-remove" onclick="quitarItem(${idx})"><i class="fas fa-trash"></i></button>
                    </div>`;
            });

            $('#cartItems').html(cart.length ? html : '<div class="empty-cart"><i class="fa fa-file-invoice fa-2x mb-2"></i><br>Agrega productos o items libres</div>');
            $('#cartTotal').html('S/ ' + total.toFixed(2));
            $('#cartItemsCount').html(formatoCantidad(totalItems) + ' items');
        }

        function formatoCantidad(n) {
            return parseFloat(n).toFixed(2).replace(/\.?0+$/, '') || '0';
        }

        function cambiarCantidad(idx, delta) {
            let nueva = Math.round((parseFloat(cart[idx].quantity) + delta) * 100) / 100;
            if (nueva <= 0) {
                cart.splice(idx, 1);
            } else {
                cart[idx].quantity = nueva;
            }
            renderCart();
        }

        function cambiarPrecio(idx, valor) {
            let precio = parseFloat(valor);
            if (!(precio >= 0)) {
                Toast.fire({ icon: 'warning', title: 'Precio inválido' });
                renderCart();
                return;
            }
            cart[idx].PRO_PrecioBaseVenta = precio;
            renderCart();
        }

        function quitarItem(idx) {
            cart.splice(idx, 1);
            renderCart();
        }

        function limpiarCarrito() {
            if (!cart.length) return;
            Swal.fire({
                icon: 'question', title: '¿Vaciar la cotización?', showCancelButton: true,
                confirmButtonText: 'Sí, vaciar', cancelButtonText: 'Cancelar'
            }).then(function (res) {
                if (res.isConfirmed) { cart = []; renderCart(); }
            });
        }

        function selectClient(nombre, documento, id) {
            $('#cliente_id').val(id);
            $('#clientName').text(nombre);
            $('#clientSubtitle').text(documento || 'Sin documento');
            $('#modalClientes').modal('hide');
        }

        function loadClients(search = '') {
            $.get("{{ route('tenant.ventas.venta.searchClientes') }}", { search: search }).done(function (clientes) {
                let html = '';
                clientes.forEach(function (c) {
                    html += `
                        <div class="client-row" style="cursor:pointer;" onclick='selectClient(${JSON.stringify(c.CLI_Nombre)}, ${JSON.stringify(c.CLI_NumDocumento)}, ${c.CLI_Id})'>
                            <div>
                                <div class="client-row-name">${c.CLI_Nombre}</div>
                                <div class="client-row-meta">${c.CLI_NumDocumento || '-'} · ${c.CLI_Celular || '-'}</div>
                            </div>
                            <i class="fa fa-chevron-right text-muted"></i>
                        </div>`;
                });
                $('#clientList').html(html || '<p class="text-muted text-center py-3">Sin resultados.</p>');
            });
        }

        function crearCliente() {
            let documento = $('#nuevoDocumento').val();
            let nombre = ($('#nuevoNombre').val() || '').trim();
            let celular = $('#nuevoCelular').val();

            if (!nombre) {
                Toast.fire({ icon: 'warning', title: 'Falta el nombre del cliente' });
                return;
            }

            $.ajax({
                url: "{{ route('tenant.ventas.venta.createCliente') }}",
                method: 'POST',
                data: {
                    CLI_TipoDocumento: (documento || '').length > 8 ? 'RUC' : 'DNI',
                    CLI_NumDocumento: documento,
                    CLI_Nombre: nombre,
                    CLI_Celular: celular,
                    _token: '{{ csrf_token() }}'
                }
            }).done(function (response) {
                selectClient(response.CLI_Nombre, response.CLI_NumDocumento, response.CLI_Id);
            }).fail(function () {
                Toast.fire({ icon: 'error', title: 'No se pudo crear el cliente' });
            });
        }

        function guardarCotizacion() {
            if (!cart.length) {
                Toast.fire({ icon: 'warning', title: 'Agrega al menos un producto' });
                return;
            }
            if (!$('#cliente_id').val()) {
                Toast.fire({ icon: 'warning', title: 'Selecciona un cliente' });
                return;
            }

            let items = cart.map(function (item) {
                return {
                    pro_id: item.PRO_Id,
                    nombre: item.esLibre ? item.PRO_Nombre : null,
                    cantidad: item.quantity,
                    precio: item.PRO_PrecioBaseVenta,
                    descuento: item.descuento || 0,
                };
            });

            let data = {
                cliente_id: $('#cliente_id').val(),
                fecha_vencimiento: $('#fechaVencimiento').val(),
                observacion: $('#observacion').val(),
                res_id: RESERVA_ID,
                items: items,
                _token: '{{ csrf_token() }}'
            };

            let url = EDITANDO_ID
                ? '{{ tenant_url("tenant.ventas.cotizacion.update", ["cotizacion" => ":id"]) }}'.replace(':id', EDITANDO_ID)
                : '{{ route("tenant.ventas.cotizacion.store") }}';

            if (EDITANDO_ID) { data._method = 'PUT'; }

            $('#btnGuardar').prop('disabled', true).html('Guardando...');

            $.ajax({ url: url, method: 'POST', data: data })
                .done(function () {
                    Swal.fire({
                        icon: 'success',
                        title: EDITANDO_ID ? 'Cotización actualizada' : 'Cotización guardada',
                        confirmButtonText: 'Ver cotizaciones'
                    }).then(function () {
                        window.location.href = "{{ tenant_url('tenant.ventas.cotizacion.index') }}";
                    });
                })
                .fail(function (xhr) {
                    Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Revisa los datos e intenta de nuevo.' });
                    $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-file-invoice mr-2"></i>' + (EDITANDO_ID ? 'Guardar cambios' : 'Guardar cotización'));
                });
        }
    </script>
@endsection
