@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Ventas por Bahía')
@section('contenido')

    <style>
        .bahias-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }
        .bahias-header h4 {
            margin: 0;
            font-weight: 700;
        }
        .bahias-header .fecha-hoy {
            color: #6c757d;
            font-size: .9rem;
            text-transform: capitalize;
        }
        .bahias-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
        }
        .bahia-col {
            background: #f4f6f9;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .bahia-col-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1.05rem;
            color: #1f2937;
        }
        .bahia-col-header .icon-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
        }
        .res-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            padding: 14px;
            border-left: 5px solid #cbd5e1;
            transition: box-shadow .15s ease;
        }
        .res-card:hover {
            box-shadow: 0 4px 14px rgba(0,0,0,.10);
        }
        .res-card.estado-sin-iniciar { border-left-color: #94a3b8; }
        .res-card.estado-en-atencion { border-left-color: #f59e0b; }
        .res-card.estado-cerrada { border-left-color: #22c55e; opacity: .75; }
        .res-turno {
            font-size: .78rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: .03em;
        }
        .res-cliente {
            font-weight: 700;
            font-size: 1rem;
            color: #111827;
            margin-top: 2px;
        }
        .res-moto {
            font-size: .85rem;
            color: #6b7280;
        }
        .badge-estado {
            font-size: .72rem;
            padding: 4px 9px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-sin-iniciar { background: #e2e8f0; color: #475569; }
        .badge-en-atencion { background: #fef3c7; color: #92400e; }
        .badge-cerrada { background: #dcfce7; color: #15803d; }
        .res-total {
            font-size: 1.15rem;
            font-weight: 800;
            color: #111827;
        }
        .items-container {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .item-mini {
            background: #f8fafc;
            border: 1px solid #eef1f4;
            border-radius: 9px;
            padding: 7px 9px;
        }
        .item-mini-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 6px;
        }
        .item-mini-nombre {
            font-size: .81rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.2;
        }
        .item-mini .item-quitar {
            color: #ef4444;
            cursor: pointer;
            font-size: .78rem;
            padding: 2px 3px;
            flex-shrink: 0;
        }
        .item-mini .item-quitar:hover { color: #b91c1c; }
        .item-mini-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }
        .item-mini-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .qty-btn {
            width: 21px;
            height: 21px;
            border-radius: 50%;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #374151;
            line-height: 1;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qty-btn i { font-size: .6rem; }
        .qty-btn:hover { background: #eef2ff; border-color: #a5b4fc; }
        .qty-val {
            min-width: 20px;
            text-align: center;
            font-size: .8rem;
            font-weight: 700;
            color: #111827;
        }
        .precio-x {
            color: #9ca3af;
            font-size: .75rem;
        }
        .precio-wrap {
            font-size: .78rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .precio-input {
            width: 58px;
            font-size: .78rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 1px 4px;
            text-align: right;
            color: #1f2937;
        }
        .precio-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99,102,241,.15);
        }
        .item-mini-subtotal {
            font-weight: 700;
            font-size: .85rem;
            color: #111827;
        }
        .sin-reserva {
            text-align: center;
            color: #9ca3af;
            font-size: .85rem;
            padding: 18px 0;
        }
        .btn-agregar-item {
            border: 1px dashed #c7cdd6;
            background: #fff;
            border-radius: 8px;
            font-size: .82rem;
            width: 100%;
            padding: 6px;
            color: #4b5563;
        }
        .btn-agregar-item:hover { background: #f8fafc; }
    </style>

    <div class="col-12">
        <div class="bahias-header">
            <div>
                <h4><i class="fa fa-oil-can mr-2 text-primary"></i>Ventas por Bahía</h4>
                <div class="fecha-hoy">{{ $fechaHoy }}</div>
            </div>
            <a href="{{ tenant_url('tenant.ventas.venta.index') }}" class="btn btn-light btn-sm">
                <i class="fa fa-arrow-left"></i> Volver a Ventas
            </a>
        </div>

        <div class="bahias-board">
            @forelse ($tablero as $col)
                <div class="bahia-col">
                    <div class="bahia-col-header">
                        <span class="icon-circle"><i class="fa fa-warehouse"></i></span>
                        {{ $col['bahia']->BAH_Nombre }}
                    </div>

                    @forelse ($col['reservas'] as $r)
                        @php
                            $reserva = $r['reserva'];
                            $cuenta = $r['cuenta'];
                            $estado = $cuenta
                                ? ($cuenta->BCT_Estado === 'ABIERTA' ? 'en-atencion' : 'cerrada')
                                : 'sin-iniciar';
                            $etiquetaEstado = [
                                'sin-iniciar' => 'Sin iniciar',
                                'en-atencion' => 'En atención',
                                'cerrada' => $cuenta && $cuenta->BCT_Estado === 'CERRADA_SIN_COBRO' ? 'Cerrada sin cobro' : 'Cobrada',
                            ][$estado];
                        @endphp
                        <div class="res-card estado-{{ $estado }}"
                             id="res-card-{{ $reserva->RES_Id }}"
                             data-res-id="{{ $reserva->RES_Id }}"
                             data-cuenta-id="{{ $cuenta->BCT_Id ?? '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="res-turno">{{ $reserva->TUR_Descripcion }}</div>
                                    <div class="res-cliente">{{ $reserva->RES_Cliente }}</div>
                                    <div class="res-moto">{{ $reserva->RES_Moto }} · {{ $reserva->RES_Placa }}</div>
                                </div>
                                <span class="badge-estado badge-{{ $estado }}">{{ $etiquetaEstado }}</span>
                            </div>

                            <div class="items-container mt-2">
                                @if ($cuenta)
                                    @foreach ($cuenta->items as $item)
                                        <div class="item-mini" data-item-id="{{ $item->BCI_Id }}"
                                             data-cantidad="{{ $item->BCI_Cantidad }}" data-precio="{{ $item->BCI_PrecioUnitario }}">
                                            <div class="item-mini-top">
                                                <span class="item-mini-nombre">{{ $item->producto->PRO_Nombre ?? ('Producto #' . $item->PRO_Id) }}</span>
                                                @if ($cuenta->BCT_Estado === 'ABIERTA')
                                                    <i class="fa fa-times item-quitar" onclick="quitarItem({{ $cuenta->BCT_Id }}, {{ $item->BCI_Id }})"></i>
                                                @endif
                                            </div>
                                            <div class="item-mini-bottom">
                                                @if ($cuenta->BCT_Estado === 'ABIERTA')
                                                    <div class="item-mini-controls">
                                                        <button type="button" class="qty-btn" onclick="cambiarCantidad({{ $cuenta->BCT_Id }}, {{ $item->BCI_Id }}, -1)"><i class="fa fa-minus"></i></button>
                                                        <span class="qty-val">{{ rtrim(rtrim(number_format($item->BCI_Cantidad, 2), '0'), '.') }}</span>
                                                        <button type="button" class="qty-btn" onclick="cambiarCantidad({{ $cuenta->BCT_Id }}, {{ $item->BCI_Id }}, 1)"><i class="fa fa-plus"></i></button>
                                                        <span class="precio-x">×</span>
                                                        <span class="precio-wrap">S/
                                                            <input type="number" class="precio-input" value="{{ $item->BCI_PrecioUnitario }}"
                                                                step="0.01" min="0"
                                                                onchange="cambiarPrecio({{ $cuenta->BCT_Id }}, {{ $item->BCI_Id }}, this.value)">
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">{{ $item->BCI_Cantidad }} x S/ {{ number_format($item->BCI_PrecioUnitario, 2) }}</span>
                                                @endif
                                                <span class="item-mini-subtotal">S/ {{ number_format($item->subtotal(), 2) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-muted small">Total</span>
                                <span class="res-total">S/ {{ number_format($r['total'], 2) }}</span>
                            </div>

                            <div class="mt-2 acciones-container">
                                @if ($estado === 'sin-iniciar')
                                    <button class="btn btn-primary btn-sm btn-block" onclick="abrirCuenta({{ $reserva->RES_Id }})">
                                        <i class="fa fa-play"></i> Abrir cuenta
                                    </button>
                                @elseif ($estado === 'en-atencion')
                                    <button type="button" class="btn-agregar-item mb-2" onclick="abrirModalItem({{ $cuenta->BCT_Id }})">
                                        <i class="fa fa-plus"></i> Agregar producto / item rápido
                                    </button>
                                    <div class="d-flex" style="gap:6px">
                                        <button class="btn btn-light btn-sm flex-fill"
                                            onclick="cerrarSinCobrar({{ $cuenta->BCT_Id }})"
                                            @disabled($r['items'] > 0)
                                            title="{{ $r['items'] > 0 ? 'Solo disponible si la cuenta está vacía: quita los productos o cóbralos' : 'Libera esta bahía sin generar ninguna venta' }}">
                                            Cerrar sin cobrar
                                        </button>
                                        <a class="btn btn-success btn-sm flex-fill"
                                            href="{{ tenant_url('tenant.ventas.bahias.cobrar', ['cuenta' => $cuenta->BCT_Id]) }}">
                                            <i class="fa fa-cash-register"></i> Cobrar
                                        </a>
                                    </div>
                                    <div class="text-muted text-center mt-1" style="font-size:.7rem;">
                                        <i class="fa fa-check-circle text-success"></i> Lo que agregas se guarda al instante — no hace falta cerrar nada mientras se sigue trabajando.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="sin-reserva">
                            <i class="fa fa-calendar-times mb-1"></i><br>
                            Sin reservas para hoy
                        </div>
                    @endforelse
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    No hay bahías activas configuradas en esta sede.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL: agregar producto / item rapido -->
    <div class="modal fade" id="modalAgregarItemBahia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-cart-plus mr-2"></i>Agregar a la cuenta</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabCatalogo">Del catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabRapido">Item rápido</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabCatalogo">
                            <input type="text" id="buscadorItemBahia" class="form-control mb-2"
                                placeholder="Buscar producto por nombre...">
                            <div id="resultadosItemBahia" style="max-height:320px; overflow-y:auto;"></div>
                        </div>
                        <div class="tab-pane fade" id="tabRapido">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" id="rapidoNombreBahia" class="form-control" placeholder="Ej. Mano de obra...">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label>Cantidad</label>
                                    <input type="number" id="rapidoCantidadBahia" class="form-control" value="1" min="0.01" step="0.01">
                                </div>
                                <div class="form-group col-6">
                                    <label>Precio unitario (S/)</label>
                                    <input type="number" id="rapidoPrecioBahia" class="form-control" placeholder="0.00" min="0" step="0.01">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-block" id="btnAgregarRapidoBahia">
                                <i class="fa fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        let cuentaActivaModal = null;
        let buscarTimeout = null;

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1600,
            timerProgressBar: true
        });

        function abrirCuenta(resId) {
            $.ajax({
                url: '{{ tenant_url("tenant.ventas.bahias.abrir", ["reservacion" => ":id"]) }}'.replace(':id', resId),
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            }).done(function () {
                location.reload();
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: 'No se pudo abrir la cuenta', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
            });
        }

        function abrirModalItem(cuentaId) {
            cuentaActivaModal = cuentaId;
            $('#resultadosItemBahia').html('<p class="text-muted text-center py-3">Escribe para buscar...</p>');
            $('#buscadorItemBahia').val('');
            $('#rapidoNombreBahia').val('');
            $('#rapidoCantidadBahia').val(1);
            $('#rapidoPrecioBahia').val('');
            $('#modalAgregarItemBahia').modal('show');
            $('#buscadorItemBahia').trigger('focus');
        }

        function imagenProducto(p) {
            return p.PRO_Imagen
                ? `/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/producto/${p.PRO_Imagen}`
                : `/images/imagen_default.png`;
        }

        $('#buscadorItemBahia').on('keyup', function () {
            let texto = $(this).val();
            clearTimeout(buscarTimeout);

            if (texto.length < 2) {
                $('#resultadosItemBahia').html('<p class="text-muted text-center py-3">Escribe al menos 2 letras...</p>');
                return;
            }

            buscarTimeout = setTimeout(function () {
                $.get('{{ tenant_url("tenant.ventas.venta.productos") }}', { search: texto, categoria: 'all' })
                    .done(function (res) {
                        let productos = res.data || [];
                        if (!productos.length) {
                            $('#resultadosItemBahia').html('<p class="text-muted text-center py-3">Sin resultados.</p>');
                            return;
                        }
                        let html = '';
                        productos.forEach(function (p) {
                            html += `
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div class="d-flex align-items-center" style="gap:10px; min-width:0;">
                                        <img src="${imagenProducto(p)}" style="width:44px;height:44px;object-fit:cover;border-radius:8px;flex-shrink:0;" onerror="this.src='/images/imagen_default.png'">
                                        <div style="min-width:0;">
                                            <div class="font-weight-bold text-truncate" style="max-width:280px;">${p.PRO_Nombre}</div>
                                            <small class="text-muted">S/ ${p.PRO_PrecioBaseVenta} · Stock ${p.PRO_Cantidad}</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary" style="flex-shrink:0;" onclick='agregarProductoCatalogo(${JSON.stringify(p)})'>
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>`;
                        });
                        $('#resultadosItemBahia').html(html);
                    });
            }, 300);
        });

        /**
         * Repinta solo la tarjeta de la cuenta afectada (items + total + el
         * boton de "cerrar sin cobrar") sin recargar toda la pagina, para
         * poder seguir agregando productos sin perder el modal ni la
         * busqueda ya escrita.
         */
        function formatoCantidad(n) {
            // Sin decimales de sobra: 1 en vez de 1.00, pero respeta 1.5.
            return parseFloat(n).toFixed(2).replace(/\.?0+$/, '');
        }

        function actualizarTarjetaCuenta(resumen) {
            let $card = $('.res-card[data-cuenta-id="' + resumen.cuenta_id + '"]');
            if (!$card.length) return;

            let html = '';
            resumen.items.forEach(function (item) {
                html += `
                    <div class="item-mini" data-item-id="${item.id}" data-cantidad="${item.cantidad}" data-precio="${item.precio}">
                        <div class="item-mini-top">
                            <span class="item-mini-nombre">${item.nombre}</span>
                            <i class="fa fa-times item-quitar" onclick="quitarItem(${resumen.cuenta_id}, ${item.id})"></i>
                        </div>
                        <div class="item-mini-bottom">
                            <div class="item-mini-controls">
                                <button type="button" class="qty-btn" onclick="cambiarCantidad(${resumen.cuenta_id}, ${item.id}, -1)"><i class="fa fa-minus"></i></button>
                                <span class="qty-val">${formatoCantidad(item.cantidad)}</span>
                                <button type="button" class="qty-btn" onclick="cambiarCantidad(${resumen.cuenta_id}, ${item.id}, 1)"><i class="fa fa-plus"></i></button>
                                <span class="precio-x">×</span>
                                <span class="precio-wrap">S/
                                    <input type="number" class="precio-input" value="${item.precio}" step="0.01" min="0"
                                        onchange="cambiarPrecio(${resumen.cuenta_id}, ${item.id}, this.value)">
                                </span>
                            </div>
                            <span class="item-mini-subtotal">S/ ${item.subtotal.toFixed(2)}</span>
                        </div>
                    </div>`;
            });
            $card.find('.items-container').html(html);
            $card.find('.res-total').text('S/ ' + resumen.total.toFixed(2));

            let hayItems = resumen.items.length > 0;
            let $cerrarBtn = $card.find('.acciones-container button:contains("Cerrar sin cobrar")');
            $cerrarBtn.prop('disabled', hayItems).attr('title', hayItems
                ? 'Solo disponible si la cuenta está vacía: quita los productos o cóbralos'
                : 'Libera esta bahía sin generar ninguna venta');
        }

        function guardarItem(cuentaId, itemId, cantidad, precio) {
            $.ajax({
                url: '{{ tenant_url("tenant.ventas.bahias.items.update", ["cuenta" => ":cuenta", "item" => ":item"]) }}'
                    .replace(':cuenta', cuentaId).replace(':item', itemId),
                method: 'PUT',
                data: { cantidad: cantidad, precio: precio, _token: '{{ csrf_token() }}' }
            }).done(function (resumen) {
                actualizarTarjetaCuenta(resumen);
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: 'No se pudo actualizar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
            });
        }

        function cambiarCantidad(cuentaId, itemId, delta) {
            let $item = $('.item-mini[data-item-id="' + itemId + '"]');
            let cantidadActual = parseFloat($item.data('cantidad'));
            let precioActual = parseFloat($item.data('precio'));
            let nueva = Math.round((cantidadActual + delta) * 100) / 100;

            if (nueva <= 0) {
                quitarItem(cuentaId, itemId);
                return;
            }

            guardarItem(cuentaId, itemId, nueva, precioActual);
        }

        function cambiarPrecio(cuentaId, itemId, valor) {
            let precio = parseFloat(valor);
            if (!(precio >= 0)) {
                Swal.fire({ icon: 'warning', title: 'Precio inválido' });
                return;
            }
            let $item = $('.item-mini[data-item-id="' + itemId + '"]');
            let cantidadActual = parseFloat($item.data('cantidad'));

            guardarItem(cuentaId, itemId, cantidadActual, precio);
        }

        function agregarProductoCatalogo(producto) {
            $.ajax({
                url: '{{ tenant_url("tenant.ventas.bahias.items.store", ["cuenta" => ":id"]) }}'.replace(':id', cuentaActivaModal),
                method: 'POST',
                data: {
                    pro_id: producto.PRO_Id,
                    cantidad: 1,
                    precio: producto.PRO_PrecioBaseVenta,
                    _token: '{{ csrf_token() }}'
                }
            }).done(function (resumen) {
                actualizarTarjetaCuenta(resumen);
                Toast.fire({ icon: 'success', title: producto.PRO_Nombre + ' agregado' });
                // El modal se queda abierto: se puede seguir buscando y
                // agregando mas productos sin repetir la busqueda.
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: 'No se pudo agregar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
            });
        }

        $('#btnAgregarRapidoBahia').on('click', function () {
            let nombre = ($('#rapidoNombreBahia').val() || '').trim();
            let cantidad = parseFloat($('#rapidoCantidadBahia').val());
            let precio = parseFloat($('#rapidoPrecioBahia').val());

            if (!nombre || !(cantidad > 0) || !(precio >= 0)) {
                Swal.fire({ icon: 'warning', title: 'Faltan datos', text: 'Completa nombre, cantidad y precio.' });
                return;
            }

            $.ajax({
                url: '{{ tenant_url("tenant.ventas.bahias.items.rapido", ["cuenta" => ":id"]) }}'.replace(':id', cuentaActivaModal),
                method: 'POST',
                data: { nombre: nombre, cantidad: cantidad, precio: precio, _token: '{{ csrf_token() }}' }
            }).done(function (resumen) {
                actualizarTarjetaCuenta(resumen);
                Toast.fire({ icon: 'success', title: nombre + ' agregado' });
                $('#rapidoNombreBahia').val('');
                $('#rapidoCantidadBahia').val(1);
                $('#rapidoPrecioBahia').val('');
                $('#rapidoNombreBahia').trigger('focus');
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: 'No se pudo agregar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
            });
        });

        function quitarItem(cuentaId, itemId) {
            $.ajax({
                url: '{{ tenant_url("tenant.ventas.bahias.items.destroy", ["cuenta" => ":cuenta", "item" => ":item"]) }}'
                    .replace(':cuenta', cuentaId).replace(':item', itemId),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' }
            }).done(function (resumen) {
                actualizarTarjetaCuenta(resumen);
            });
        }

        function cerrarSinCobrar(cuentaId) {
            Swal.fire({
                icon: 'question',
                title: 'Cerrar sin cobrar',
                text: 'Esta bahía quedará libre sin generar ninguna venta.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar',
                cancelButtonText: 'Cancelar'
            }).then(function (res) {
                if (!res.isConfirmed) return;

                $.ajax({
                    url: '{{ tenant_url("tenant.ventas.bahias.cerrarSinCobrar", ["cuenta" => ":id"]) }}'.replace(':id', cuentaId),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' }
                }).done(function () {
                    location.reload();
                }).fail(function (xhr) {
                    Swal.fire({ icon: 'error', title: 'No se pudo cerrar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
                });
            });
        }
    </script>
@endsection
