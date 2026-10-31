@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')

@section('titulo', 'Ajuste de Inventario')

@section('contenido')
    <style>
        .card{
            border-radius: 18px;
        }

        .modal-content{
            border-radius: 18px;
        }

        #tabla_items_ajuste td{
            vertical-align: middle;
        }
    </style>

    @can('tenant.inventario.producto.create')
        <div class="col-12 col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 8px;">
                        <h5 class="card-title mb-0">NUEVO AJUSTE</h5>
                        <button type="button" class="btn btn-outline-success btn-sm" id="btnAbrirMasivo">
                            <i class="fas fa-file-excel"></i> Conteo físico masivo (Excel)
                        </button>
                    </div>
                    <div class="mb-2"></div>
                    <p class="text-muted" style="font-size: 13px;">
                        Usa esto para corregir el stock cuando no viene de una compra, venta o traslado:
                        mermas, roturas, vencimientos o diferencias de un conteo físico.
                    </p>

                    <form id="form_ajuste">
                        @csrf

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Almacén:</label>
                                <select class="form-control select2" id="ALM_Id" name="ALM_Id" style="width: 100%;" required>
                                    <option value="">Seleccionar ...</option>
                                    @foreach ($almacenes as $itemAlmacen)
                                        <option value="{{ $itemAlmacen->ALM_Id }}">{{ $itemAlmacen->ALM_NombreAlmacen }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Motivo:</label>
                                <select class="form-control select2" id="AJU_Motivo" name="AJU_Motivo" style="width: 100%;" required>
                                    <option value="">Seleccionar ...</option>
                                    <option value="MERMA">Merma</option>
                                    <option value="CONSUMIBLES">Consumibles</option>
                                    <option value="ROTURA">Rotura</option>
                                    <option value="VENCIMIENTO">Vencimiento</option>
                                    <option value="CONTEO_FISICO">Diferencia de conteo físico</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Agregar producto:</label>
                                <select class="form-control select2" id="selector_producto_ajuste" style="width: 100%;" disabled>
                                    <option value="">Elige primero el almacén ...</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered" id="tabla_items_ajuste">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width: 100px;">Stock Actual</th>
                                        <th style="width: 130px;">Tipo</th>
                                        <th style="width: 100px;">Cantidad</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_items_ajuste">
                                    <tr id="fila_vacia_ajuste">
                                        <td colspan="5" class="text-center text-muted">Aún no agregas productos.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Observación (opcional):</label>
                                <textarea class="form-control" id="AJU_Observacion" name="AJU_Observacion" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="button" id="btnGuardarAjuste" class="btn btn-primary">
                                <i class="fas fa-balance-scale"></i> Registrar Ajuste
                            </button>
                            <button type="button" id="btnLimpiarAjuste" class="btn btn-danger">
                                <i class="fas fa-ban"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('tenant.inventario.producto.index')
        <div class="col-12 col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">HISTORIAL DE AJUSTES</h5>
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="lista_ajustes">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Almacén</th>
                                    <th scope="col">Motivo</th>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Items</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('tenant.inventario.producto.create')
        <div class="modal fade" id="modalMasivo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i>Conteo físico masivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted" style="font-size: 13px;">
                            Descarga la lista de productos del almacén, escribe en <strong>Stock contado</strong> lo que realmente hay
                            y súbela. El sistema calcula solo lo que se suma o se resta y te lo muestra antes de aplicar.
                            Lo que dejes vacío no se modifica.
                        </p>

                        <div class="form-group">
                            <label class="control-label">1. Almacén:</label>
                            <div class="input-group">
                                <select class="form-control" id="masivo_almacen">
                                    <option value="">Seleccionar ...</option>
                                    @foreach ($almacenes as $itemAlmacen)
                                        <option value="{{ $itemAlmacen->ALM_Id }}">{{ $itemAlmacen->ALM_NombreAlmacen }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success" id="btnPlantillaMasivo" disabled>
                                        <i class="fas fa-download"></i> Descargar plantilla
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">2. Archivo con el conteo:</label>
                            <div class="input-group">
                                <input type="file" class="form-control-file" id="masivo_archivo" accept=".xlsx,.xls,.csv">
                                <button type="button" class="btn btn-primary btn-sm ml-auto" id="btnRevisarMasivo" disabled>
                                    <i class="fas fa-search"></i> Revisar diferencias
                                </button>
                            </div>
                        </div>

                        <div id="masivo_resultado" style="display:none;">
                            <hr>
                            <div id="masivo_resumen" class="mb-2"></div>

                            <div id="masivo_errores_wrap" style="display:none;">
                                <h6 class="text-danger">Filas con error (corrígelas y vuelve a subir)</h6>
                                <ul id="masivo_errores" class="small text-danger"></ul>
                            </div>

                            <div class="table-responsive" id="masivo_items_wrap" style="max-height: 320px; overflow:auto;">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Sistema</th>
                                            <th class="text-center">Contado</th>
                                            <th class="text-center">Se hará</th>
                                        </tr>
                                    </thead>
                                    <tbody id="masivo_items"></tbody>
                                </table>
                            </div>

                            <div class="form-group mt-3 mb-0">
                                <label class="control-label">Observación (opcional):</label>
                                <input type="text" class="form-control" id="masivo_observacion" maxlength="200">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="btnAplicarMasivo" style="display:none;">
                            <i class="fas fa-check"></i> Aplicar conteo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <div class="modal fade" id="modalVerAjuste" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-balance-scale mr-2"></i>Detalle del Ajuste</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Almacén</small>
                            <strong id="ver_aju_almacen"></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Motivo</small>
                            <strong id="ver_aju_motivo"></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Usuario</small>
                            <strong id="ver_aju_usuario"></strong>
                        </div>
                    </div>
                    <div id="ver_aju_observacion_wrapper" class="mb-3">
                        <small class="text-muted d-block">Observación</small>
                        <span id="ver_aju_observacion"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_ver_ajuste"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            $('.select2').select2();

            var itemsAjuste = {};

            var table = $('#lista_ajustes').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: "{{ tenant_url('tenant.inventario.ajuste.index') }}",
                columns: [
                    { data: 'AJU_Id', name: 'AJU_Id' },
                    { data: 'almacen', name: 'almacen' },
                    { data: 'AJU_Motivo', name: 'AJU_Motivo' },
                    { data: 'usuario', name: 'usuario' },
                    { data: 'items', name: 'items' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // El selector busca contra el backend a medida que se escribe
            // (en vez de cargar una sola vez los primeros 30 productos y
            // filtrar solo esos en el navegador), para que cualquier
            // producto sea encontrable sin importar cuantos haya. El
            // backend (AjusteController::productos) ya busca por nombre,
            // codigo interno y codigo de fabricacion -- aqui solo se
            // muestran esos codigos en el resultado.
            function initSelectorProductoAjuste(almacenId) {
                var $selector = $('#selector_producto_ajuste');

                if ($selector.hasClass('select2-hidden-accessible')) {
                    $selector.select2('destroy');
                }

                $selector.empty();

                $selector.select2({
                    width: '100%',
                    placeholder: 'Buscar por nombre o código ...',
                    minimumInputLength: 0,
                    ajax: {
                        url: "{{ tenant_url('tenant.inventario.ajuste.productos') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return { ALM_Id: almacenId, search: params.term };
                        },
                        processResults: function(productos) {
                            return {
                                results: productos.map(function(p) {
                                    var codigos = [
                                        p.PRO_CodigoInterno ? 'Cód. interno: ' + p.PRO_CodigoInterno : null,
                                        p.PRO_CodigoFabricacion ? 'Cód. fabricación: ' + p.PRO_CodigoFabricacion : null
                                    ].filter(Boolean).join(' · ');

                                    var texto = p.PRO_Nombre + (codigos ? ' (' + codigos + ')' : '') + ' — stock: ' + p.stock;

                                    return { id: p.PRO_Id, text: texto, nombre: p.PRO_Nombre, stock: p.stock };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }

            $('#ALM_Id').on('change', function() {
                var almacenId = $(this).val();
                itemsAjuste = {};
                renderItemsAjuste();

                var $selector = $('#selector_producto_ajuste');

                if (!almacenId) {
                    if ($selector.hasClass('select2-hidden-accessible')) {
                        $selector.select2('destroy');
                    }
                    $selector.empty().append('<option value="">Elige primero el almacén ...</option>');
                    $selector.prop('disabled', true);
                    return;
                }

                $selector.prop('disabled', false);
                initSelectorProductoAjuste(almacenId);
            });

            $('#selector_producto_ajuste').on('select2:select', function(e) {
                var data = e.params.data;
                var proId = data.id;

                if (!itemsAjuste[proId]) {
                    itemsAjuste[proId] = { nombre: data.nombre, stock: parseFloat(data.stock), tipo: 'DECREMENTO', cantidad: 1 };
                }

                renderItemsAjuste();
                $(this).val(null).trigger('change');
            });

            function renderItemsAjuste() {
                var $tbody = $('#tbody_items_ajuste');
                $tbody.empty();

                var keys = Object.keys(itemsAjuste);

                if (keys.length === 0) {
                    $tbody.append('<tr id="fila_vacia_ajuste"><td colspan="5" class="text-center text-muted">Aún no agregas productos.</td></tr>');
                    return;
                }

                keys.forEach(function(proId) {
                    var item = itemsAjuste[proId];
                    var fila = $(
                        '<tr data-pro-id="' + proId + '">' +
                            '<td>' + item.nombre + '</td>' +
                            '<td>' + item.stock + '</td>' +
                            '<td>' +
                                '<select class="form-control form-control-sm select_tipo_ajuste">' +
                                    '<option value="DECREMENTO"' + (item.tipo === 'DECREMENTO' ? ' selected' : '') + '>Quitar (-)</option>' +
                                    '<option value="INCREMENTO"' + (item.tipo === 'INCREMENTO' ? ' selected' : '') + '>Agregar (+)</option>' +
                                '</select>' +
                            '</td>' +
                            '<td><input type="number" min="0.01" step="0.01" class="form-control form-control-sm input_cantidad_ajuste" value="' + item.cantidad + '"></td>' +
                            '<td class="text-center"><a href="javascript:void(0)" class="text-danger btn_quitar_item_ajuste"><i class="fa fa-trash"></i></a></td>' +
                        '</tr>'
                    );
                    $tbody.append(fila);
                });
            }

            $('body').on('change', '.select_tipo_ajuste', function() {
                var proId = $(this).closest('tr').data('pro-id');
                itemsAjuste[proId].tipo = $(this).val();
            });

            $('body').on('input', '.input_cantidad_ajuste', function() {
                var proId = $(this).closest('tr').data('pro-id');
                itemsAjuste[proId].cantidad = $(this).val();
            });

            $('body').on('click', '.btn_quitar_item_ajuste', function() {
                var proId = $(this).closest('tr').data('pro-id');
                delete itemsAjuste[proId];
                renderItemsAjuste();
            });

            $('#btnLimpiarAjuste').on('click', function() {
                itemsAjuste = {};
                renderItemsAjuste();
                $('#form_ajuste')[0].reset();
                $('.select2').val('').trigger('change');
                $('#selector_producto_ajuste').prop('disabled', true);
            });

            $('#btnGuardarAjuste').on('click', function() {
                var almacen = $('#ALM_Id').val();
                var motivo = $('#AJU_Motivo').val();
                var keys = Object.keys(itemsAjuste);

                if (!almacen) {
                    Toast.fire({ type: 'error', title: 'Selecciona el almacén.' });
                    return;
                }

                if (!motivo) {
                    Toast.fire({ type: 'error', title: 'Selecciona el motivo del ajuste.' });
                    return;
                }

                if (keys.length === 0) {
                    Toast.fire({ type: 'error', title: 'Agrega al menos un producto.' });
                    return;
                }

                var proIds = [];
                var tipos = [];
                var cantidades = [];
                var error = null;

                keys.forEach(function(proId) {
                    var item = itemsAjuste[proId];
                    var cantidad = parseFloat(item.cantidad);

                    if (!cantidad || cantidad <= 0) {
                        error = '"' + item.nombre + '": la cantidad debe ser mayor a 0.';
                        return;
                    }

                    if (item.tipo === 'DECREMENTO' && cantidad > item.stock) {
                        error = '"' + item.nombre + '": no hay stock suficiente para quitar (disponible: ' + item.stock + ').';
                        return;
                    }

                    proIds.push(proId);
                    tipos.push(item.tipo);
                    cantidades.push(cantidad);
                });

                if (error) {
                    Toast.fire({ type: 'error', title: error });
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Guardando...');

                $.ajax({
                    url: "{{ tenant_url('tenant.inventario.ajuste.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ALM_Id: almacen,
                        AJU_Motivo: motivo,
                        AJU_Observacion: $('#AJU_Observacion').val(),
                        PRO_Id: proIds,
                        AJD_Tipo: tipos,
                        AJD_Cantidad: cantidades
                    },
                    success: function(data) {
                        Toast.fire({ type: 'success', title: data.success });
                        $('#btnLimpiarAjuste').click();
                        table.draw();
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'No se pudo registrar el ajuste.';
                        Toast.fire({ type: 'error', title: msg });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-balance-scale"></i> Registrar Ajuste');
                    }
                });
            });

            // ---- Conteo fisico masivo (Excel) ----
            var masivoListo = false;
            var esc = function(v) { return $('<div>').text(v == null ? '' : v).html(); };

            function masivoReiniciar() {
                masivoListo = false;
                $('#masivo_resultado, #btnAplicarMasivo, #masivo_errores_wrap').hide();
                $('#btnRevisarMasivo').prop('disabled', !($('#masivo_almacen').val() && $('#masivo_archivo').val()));
            }

            $('#btnAbrirMasivo').on('click', function() {
                $('#masivo_almacen').val('');
                $('#masivo_archivo').val('');
                $('#masivo_observacion').val('');
                $('#btnPlantillaMasivo').prop('disabled', true);
                masivoReiniciar();
                $('#modalMasivo').modal('show');
            });

            $('#masivo_almacen').on('change', function() {
                $('#btnPlantillaMasivo').prop('disabled', !$(this).val());
                masivoReiniciar();
            });
            $('#masivo_archivo').on('change', masivoReiniciar);

            $('#btnPlantillaMasivo').on('click', function() {
                window.location = "{{ tenant_url('tenant.inventario.ajuste.masivo.plantilla') }}?ALM_Id=" + encodeURIComponent($('#masivo_almacen').val());
            });

            function masivoDatos() {
                var fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');
                fd.append('ALM_Id', $('#masivo_almacen').val());
                fd.append('archivo', $('#masivo_archivo')[0].files[0]);
                fd.append('AJU_Observacion', $('#masivo_observacion').val());
                return fd;
            }

            function masivoError(xhr) {
                var j = xhr.responseJSON || {};
                var msg = j.error || (j.errors && Object.values(j.errors)[0][0]) || j.message || 'No se pudo procesar el archivo.';
                Toast.fire({ type: 'error', title: msg });
            }

            $('#btnRevisarMasivo').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Revisando...');
                $.ajax({
                    url: "{{ tenant_url('tenant.inventario.ajuste.masivo.preview') }}",
                    type: 'POST', data: masivoDatos(), processData: false, contentType: false,
                    success: function(r) {
                        var x = r.resumen;
                        $('#masivo_resumen').html(
                            '<span class="badge badge-success mr-1">' + x.incrementos + ' se suman</span>' +
                            '<span class="badge badge-danger mr-1">' + x.decrementos + ' se restan</span>' +
                            '<span class="badge badge-secondary mr-1">' + x.sin_cambio + ' sin cambio</span>' +
                            (x.errores ? '<span class="badge badge-warning">' + x.errores + ' con error</span>' : '')
                        );

                        var $e = $('#masivo_errores').empty();
                        r.errores.forEach(function(e) { $e.append('<li>Fila ' + e.fila + ': ' + esc(e.detalle) + '</li>'); });
                        $('#masivo_errores_wrap').toggle(r.errores.length > 0);

                        var $t = $('#masivo_items').empty();
                        r.items.forEach(function(i) {
                            var inc = i.tipo === 'INCREMENTO';
                            $t.append('<tr><td>' + esc(i.producto) + (i.codigo_interno ? ' <small class="text-muted">' + esc(i.codigo_interno) + '</small>' : '') + '</td>' +
                                '<td class="text-center">' + i.stock_sistema + '</td><td class="text-center">' + i.contado + '</td>' +
                                '<td class="text-center"><span class="badge badge-' + (inc ? 'success' : 'danger') + '">' + (inc ? '+' : '-') + i.cantidad + '</span></td></tr>');
                        });
                        $('#masivo_items_wrap').toggle(r.items.length > 0);

                        masivoListo = r.items.length > 0 && r.errores.length === 0;
                        $('#btnAplicarMasivo').toggle(masivoListo);
                        $('#masivo_resultado').show();
                        if (!r.items.length && !r.errores.length) {
                            Toast.fire({ type: 'info', title: 'El conteo coincide con el sistema: no hay nada que ajustar.' });
                        }
                    },
                    error: masivoError,
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-search"></i> Revisar diferencias');
                    }
                });
            });

            $('#btnAplicarMasivo').on('click', function() {
                if (!masivoListo) { return; }
                var $btn = $(this);
                Swal.fire({
                    title: '¿Aplicar el conteo?',
                    text: 'Se registrará un ajuste que modifica el stock. Esto no se puede deshacer.',
                    type: 'warning', showCancelButton: true,
                    confirmButtonText: 'Sí, aplicar', cancelButtonText: 'Cancelar'
                }).then(function(res) {
                    if (!res.value) { return; }
                    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Aplicando...');
                    $.ajax({
                        url: "{{ tenant_url('tenant.inventario.ajuste.masivo.store') }}",
                        type: 'POST', data: masivoDatos(), processData: false, contentType: false,
                        success: function(r) {
                            Toast.fire({ type: 'success', title: r.success });
                            $('#modalMasivo').modal('hide');
                            table.draw();
                        },
                        error: masivoError,
                        complete: function() {
                            $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Aplicar conteo');
                        }
                    });
                });
            });

            $('body').on('click', '.verAjuste', function() {
                var ajusteId = $(this).data('id');
                $('#modalVerAjuste').modal('show');

                $.get("{{ tenant_url('tenant.inventario.ajuste.show', ['ajuste' => ':ajuste']) }}".replace(':ajuste', ajusteId), function(data) {
                    $('#ver_aju_almacen').text(data.ajuste.almacen);
                    $('#ver_aju_motivo').text(data.ajuste.AJU_Motivo);
                    $('#ver_aju_usuario').text(data.ajuste.usuario);

                    if (data.ajuste.AJU_Observacion) {
                        $('#ver_aju_observacion_wrapper').show();
                        $('#ver_aju_observacion').text(data.ajuste.AJU_Observacion);
                    } else {
                        $('#ver_aju_observacion_wrapper').hide();
                    }

                    var $tbody = $('#tbody_ver_ajuste');
                    $tbody.empty();
                    data.detalle.forEach(function(d) {
                        var badge = d.AJD_Tipo === 'INCREMENTO'
                            ? '<span class="badge badge-success">Agregado (+)</span>'
                            : '<span class="badge badge-danger">Quitado (-)</span>';
                        $tbody.append('<tr><td>' + d.PRO_Nombre + '</td><td>' + badge + '</td><td>' + d.AJD_Cantidad + '</td></tr>');
                    });
                });
            });
        });
    </script>
@endsection
