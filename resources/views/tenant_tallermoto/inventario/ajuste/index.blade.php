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
                    <h5 class="card-title">NUEVO AJUSTE</h5>
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

            $('#ALM_Id').on('change', function() {
                var almacenId = $(this).val();
                itemsAjuste = {};
                renderItemsAjuste();

                var $selector = $('#selector_producto_ajuste');
                $selector.empty().append('<option value="">Buscar producto ...</option>');

                if (!almacenId) {
                    $selector.prop('disabled', true);
                    return;
                }

                $selector.prop('disabled', false);

                $.get("{{ tenant_url('tenant.inventario.ajuste.productos') }}", { ALM_Id: almacenId }, function(productos) {
                    productos.forEach(function(p) {
                        $selector.append(
                            $('<option>').val(p.PRO_Id).text(p.PRO_Nombre + ' (stock: ' + p.stock + ')').data('stock', p.stock).data('nombre', p.PRO_Nombre)
                        );
                    });
                });
            });

            $('#selector_producto_ajuste').on('change', function() {
                var proId = $(this).val();
                if (!proId) return;

                var $option = $(this).find('option:selected');
                var stock = parseFloat($option.data('stock'));
                var nombre = $option.data('nombre');

                if (!itemsAjuste[proId]) {
                    itemsAjuste[proId] = { nombre: nombre, stock: stock, tipo: 'DECREMENTO', cantidad: 1 };
                }

                renderItemsAjuste();
                $(this).val('').trigger('change.select2');
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
