@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')

@section('titulo', 'Editar Cotización')

@section('contenido')

<style>
    .content-wrapper .container-fluid,
    .content-wrapper .container-fluid > .row {
        max-width: none !important;
        width: 100% !important;
    }

    .cot-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        margin-bottom: 20px;
    }

    .cot-card .card-body {
        padding: 22px 24px;
    }

    .cot-card-title {
        font-weight: 800;
        color: #1F2937;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cot-card-title i {
        color: #6C3BFF;
    }

    .cot-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 4px;
        display: block;
    }

    .cot-label small {
        font-weight: 400;
        color: #9CA3AF;
    }

    .cot-form-group {
        margin-bottom: 16px;
    }

    .cot-card select.form-control,
    .cot-card input.form-control,
    .cot-card textarea.form-control,
    .cot-card .select2-selection {
        border-radius: 10px !important;
        border: 1px solid #E5E7EB !important;
        min-height: 42px;
    }

    .cot-card .select2-selection__rendered {
        line-height: 40px !important;
    }

    .cot-card .select2-selection__arrow {
        height: 40px !important;
    }

    .cot-btn-agregar {
        background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
        color: #fff;
        border: none;
        font-weight: 700;
        border-radius: 10px;
        padding: 10px 22px;
        box-shadow: 0 2px 8px rgba(108,59,255,.35);
    }

    .cot-btn-agregar:hover {
        color: #fff;
        opacity: .92;
    }

    #cotTablaItems {
        border-radius: 12px;
        overflow: hidden;
        margin-top: 18px;
    }

    #cotTablaItems thead th {
        background: #F9F7FF;
        color: #6C3BFF;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .3px;
        border: none;
        padding: 10px;
    }

    #cotTablaItems tbody tr td {
        vertical-align: middle;
        padding: 10px;
        font-size: 13px;
    }

    #cotTablaItems tfoot th {
        background: #6C3BFF;
        color: #fff;
        font-size: 14px;
        border: none;
        padding: 12px 10px;
    }

    .cot-btn-quitar {
        border-radius: 8px;
    }

    .cot-acciones-footer {
        display: flex;
        gap: 10px;
        margin-top: 18px;
        flex-wrap: wrap;
    }

    .cot-btn-guardar {
        background: #16A34A;
        color: #fff;
        border: none;
        font-weight: 700;
        border-radius: 10px;
        padding: 10px 26px;
        box-shadow: 0 2px 8px rgba(22,163,74,.3);
    }

    .cot-btn-guardar:hover {
        color: #fff;
        opacity: .92;
    }

    .cot-btn-cancelar {
        border-radius: 10px;
        font-weight: 700;
        padding: 10px 22px;
    }

    .cot-aviso-edicion {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        color: #92400E;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 12.5px;
        margin-bottom: 20px;
    }

    @media (max-width: 767px) {
        .cot-card .card-body {
            padding: 16px;
        }

        .cot-acciones-footer {
            flex-direction: column;
        }

        .cot-acciones-footer .btn {
            width: 100%;
            text-align: center;
        }

        #cotTablaItems {
            font-size: 12px;
        }
    }
</style>

<div class="col-12">
    <div class="cot-aviso-edicion">
        <i class="fas fa-triangle-exclamation mr-1"></i>
        Estás editando la cotización <strong>#{{ str_pad($cotizacion->COT_Id, 6, '0', STR_PAD_LEFT) }}</strong>.
        Al guardar se reemplaza el detalle completo (productos/cantidades/precios) y se recalcula el total.
        Solo se puede editar mientras esté <strong>Pendiente</strong>.
    </div>

    <div class="card cot-card">
        <div class="card-body">
            <div class="cot-card-title"><i class="fas fa-file-signature"></i> Datos de la Cotización</div>

            <div class="row">
                <div class="col-12 col-md-5 cot-form-group">
                    <label class="cot-label">Cliente <small>(opcional)</small></label>
                    <select class="form-control" id="cotCliente" style="width:100%">
                        @if($cliente)
                            <option value="{{ $cliente->CLI_Id }}" selected>
                                {{ $cliente->CLI_Nombre }}{{ $cliente->CLI_NumDocumento ? ' - ' . $cliente->CLI_NumDocumento : '' }}
                            </option>
                        @else
                            <option value=""></option>
                        @endif
                    </select>
                </div>

                <div class="col-6 col-md-4 cot-form-group">
                    <label class="cot-label">Sede / Almacén <span class="text-danger">*</span></label>
                    <select class="form-control" id="cotAlmacen" required>
                        <option value="">Seleccione...</option>
                        @foreach ($almacen as $itemAlmacen)
                            <option value="{{ $itemAlmacen->ALM_Id }}" {{ $cotizacion->ALM_Id == $itemAlmacen->ALM_Id ? 'selected' : '' }}>
                                {{ $itemAlmacen->ALM_NombreAlmacen }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3 cot-form-group">
                    <label class="cot-label">Vence <small>(opcional)</small></label>
                    <input type="date" class="form-control" id="cotFechaVencimiento" value="{{ $cotizacion->COT_FechaVencimiento }}">
                </div>
            </div>

            <div class="row">
                <div class="col-12 cot-form-group mb-0">
                    <label class="cot-label">Observaciones <small>(opcional)</small></label>
                    <textarea class="form-control" id="cotObservaciones" rows="2" maxlength="500">{{ $cotizacion->COT_Observaciones }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card cot-card">
        <div class="card-body">
            <div class="cot-card-title"><i class="fas fa-boxes"></i> Productos</div>

            <div class="row align-items-end">
                <div class="col-12 col-md-5 cot-form-group">
                    <label class="cot-label">Producto</label>
                    <select class="form-control" id="cotProducto" style="width:100%">
                        <option value="" disabled selected>Escriba para buscar un producto...</option>
                    </select>
                </div>
                <div class="col-4 col-md-2 cot-form-group">
                    <label class="cot-label">Cantidad</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" id="cotCantidad" value="1">
                </div>
                <div class="col-4 col-md-2 cot-form-group">
                    <label class="cot-label">P. Unitario</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="cotPrecioUnitario">
                </div>
                <div class="col-4 col-md-2 cot-form-group">
                    <label class="cot-label">Descuento</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="cotDescuento" value="0">
                </div>
                <div class="col-12 col-md-1 cot-form-group">
                    <button type="button" class="cot-btn-agregar w-100" id="cotBtnAgregar" title="Agregar">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="cotTablaItems">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">P. Unitario</th>
                            <th class="text-center">Descuento</th>
                            <th class="text-center">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cotItemsBody">
                        <tr id="cotSinItems">
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox mb-2 d-block" style="font-size:22px;"></i>
                                Aún no agregaste productos.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th class="text-center" id="cotTotal">S/ 0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="cot-acciones-footer">
                <button type="button" class="btn cot-btn-guardar" id="cotBtnGuardar">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                </button>
                <a href="{{ tenant_url('tenant.ventas.cotizacion.index.generico') }}" class="btn btn-light border cot-btn-cancelar">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Precarga de los items ya guardados (los mismos datos que enviamos al
    // crear, para poder reusar exactamente el mismo render/guardar de
    // create.blade.php).
    //
    // OJO: el array se arma antes en un bloque de PHP aparte (directiva
    // "php" de Blade, no dentro del "json(...)" directamente) porque el
    // compilador de Blade extrae el argumento de esa directiva buscando el
    // primer parentesis que cierra, y el "function ($d)" de un closure
    // adentro lo cortaba ahi mismo -- truncaba la expresion y rompia el PHP
    // compilado (ParseError: Unclosed '[' ... does not match ')').
    // Separandolo en una variable se evita ese problema de parseo del
    // propio Blade.
    @php
        $cotItemsPrecarga = $detalles->map(function ($d) {
            return [
                'PRO_Id' => $d->PRO_Id,
                'nombre' => $d->PRO_Nombre,
                'cantidad' => (float) $d->DCOT_Cantidad,
                'precio_unitario' => (float) $d->DCOT_PrecioUnitario,
                'descuento' => (float) $d->DCOT_Descuento,
            ];
        })->values();
    @endphp
    var cotItems = @json($cotItemsPrecarga);

    $(document).ready(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        // Cliente: busqueda AJAX (reutiliza el mismo endpoint que ya usa
        // Ventas, no se crea nada nuevo para esto).
        $('#cotCliente').select2({
            placeholder: 'Sin cliente (opcional)',
            allowClear: true,
            width: '100%',
            minimumInputLength: 0,
            ajax: {
                url: "{{ tenant_url('tenant.ventas.venta.searchClientes') }}",
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { search: params.term || '' };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(c) {
                            return { id: c.CLI_Id, text: c.CLI_Nombre + (c.CLI_NumDocumento ? ' - ' + c.CLI_NumDocumento : '') };
                        })
                    };
                },
                cache: true
            }
        });

        $('#cotAlmacen').select2({ width: '100%' });

        // Producto: misma busqueda paginada que usa el catalogo de Ventas
        // (VentaController::getProductos), asi que aunque haya miles de
        // productos, esto solo pide 20 a la vez a medida que se escribe.
        $('#cotProducto').select2({
            placeholder: 'Escriba para buscar un producto...',
            width: '100%',
            minimumInputLength: 0,
            ajax: {
                url: "{{ tenant_url('tenant.ventas.venta.productos') }}",
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        search: params.term || '',
                        categoria: 'all',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(function(p) {
                            return {
                                id: p.PRO_Id,
                                text: p.PRO_Nombre + ' (stock: ' + p.PRO_Cantidad + ')',
                                nombre: p.PRO_Nombre,
                                precio: p.PRO_PrecioBaseVenta
                            };
                        }),
                        pagination: { more: data.current_page < data.last_page }
                    };
                },
                cache: true
            }
        });

        $('#cotProducto').on('select2:select', function(e) {
            var data = e.params.data;
            $('#cotPrecioUnitario').val(parseFloat(data.precio || 0).toFixed(2));
        });

        $('#cotBtnAgregar').on('click', agregarItemCotizacion);
        $('#cotBtnGuardar').on('click', guardarCotizacion);

        // Pinta los items precargados apenas carga la pagina.
        renderCotItems();
    });

    function agregarItemCotizacion() {
        var select = $('#cotProducto');
        var selected = select.select2('data')[0];

        if (!selected || !selected.id) {
            Swal.fire({ icon: 'warning', title: 'Seleccione un producto' });
            return;
        }

        var cantidad = parseFloat($('#cotCantidad').val());
        var precio = parseFloat($('#cotPrecioUnitario').val());
        var descuento = parseFloat($('#cotDescuento').val()) || 0;

        if (!cantidad || cantidad <= 0) {
            Swal.fire({ icon: 'warning', title: 'La cantidad debe ser mayor a 0' });
            return;
        }

        if (isNaN(precio) || precio < 0) {
            Swal.fire({ icon: 'warning', title: 'El precio unitario no es válido' });
            return;
        }

        cotItems.push({
            PRO_Id: selected.id,
            nombre: selected.nombre || selected.text,
            cantidad: cantidad,
            precio_unitario: precio,
            descuento: descuento
        });

        renderCotItems();

        select.val(null).trigger('change');
        $('#cotCantidad').val(1);
        $('#cotPrecioUnitario').val('');
        $('#cotDescuento').val(0);
    }

    function quitarItemCotizacion(index) {
        cotItems.splice(index, 1);
        renderCotItems();
    }

    function renderCotItems() {
        var body = $('#cotItemsBody');
        body.html('');

        if (cotItems.length === 0) {
            body.html(`
                <tr id="cotSinItems">
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-inbox mb-2 d-block" style="font-size:22px;"></i>
                        Aún no agregaste productos.
                    </td>
                </tr>
            `);
            $('#cotTotal').text('S/ 0.00');
            return;
        }

        var total = 0;

        cotItems.forEach(function(item, index) {
            var subtotal = (item.cantidad * item.precio_unitario) - item.descuento;
            total += subtotal;

            body.append(`
                <tr>
                    <td>${item.nombre}</td>
                    <td class="text-center">${item.cantidad}</td>
                    <td class="text-center">S/ ${item.precio_unitario.toFixed(2)}</td>
                    <td class="text-center">S/ ${item.descuento.toFixed(2)}</td>
                    <td class="text-center">S/ ${subtotal.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm cot-btn-quitar" onclick="quitarItemCotizacion(${index})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $('#cotTotal').text('S/ ' + total.toFixed(2));
    }

    function guardarCotizacion() {
        if (cotItems.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Agregue al menos un producto' });
            return;
        }

        if (!$('#cotAlmacen').val()) {
            Swal.fire({ icon: 'warning', title: 'Seleccione una sede/almacén' });
            return;
        }

        var $btn = $('#cotBtnGuardar');
        $btn.prop('disabled', true);

        $.ajax({
            type: 'PUT',
            url: "{{ tenant_url('tenant.ventas.cotizacion.update.generico', ['cotizacion' => $cotizacion->COT_Id]) }}",
            data: {
                _token: '{{ csrf_token() }}',
                CLI_Id: $('#cotCliente').val(),
                ALM_Id: $('#cotAlmacen').val(),
                COT_FechaVencimiento: $('#cotFechaVencimiento').val(),
                COT_Observaciones: $('#cotObservaciones').val(),
                items: cotItems
            },
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Cotización actualizada',
                    text: data.success
                }).then(function() {
                    window.location = "{{ tenant_url('tenant.ventas.cotizacion.index.generico') }}";
                });
            },
            error: function(xhr) {
                var data = xhr.responseJSON || {};
                var motivo = data.error || (data.errors ? Object.values(data.errors)[0][0] : 'No se pudo actualizar la cotización.');
                Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: motivo }).then(function() {
                    // Si ya no esta Pendiente (la anularon/aprobaron desde
                    // otra pestaña mientras se editaba), no tiene sentido
                    // seguir en este formulario.
                    if (xhr.status === 422) {
                        window.location = "{{ tenant_url('tenant.ventas.cotizacion.index.generico') }}";
                    }
                });
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    }
</script>
@endsection
