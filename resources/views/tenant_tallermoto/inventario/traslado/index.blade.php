@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')

@section('titulo', 'Traslado de Stock')

@section('contenido')
    <style>
        .card{
            border-radius: 18px;
        }

        .modal-content{
            border-radius: 18px;
        }

        #tabla_items_traslado td{
            vertical-align: middle;
        }
    </style>

    @can('tenant.inventario.producto.create')
        <div class="col-12 col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">NUEVO TRASLADO</h5>

                    <form id="form_traslado">
                        @csrf

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Almacén Origen:</label>
                                <select class="form-control select2" id="ALM_OrigenId" name="ALM_OrigenId" style="width: 100%;" required>
                                    <option value="">Seleccionar ...</option>
                                    @foreach ($almacenes as $itemAlmacen)
                                        <option value="{{ $itemAlmacen->ALM_Id }}">{{ $itemAlmacen->ALM_NombreAlmacen }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Almacén Destino:</label>
                                <select class="form-control select2" id="ALM_DestinoId" name="ALM_DestinoId" style="width: 100%;" required>
                                    <option value="">Seleccionar ...</option>
                                    @foreach ($almacenes as $itemAlmacen)
                                        <option value="{{ $itemAlmacen->ALM_Id }}">{{ $itemAlmacen->ALM_NombreAlmacen }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Agregar producto:</label>
                                <select class="form-control select2" id="selector_producto" style="width: 100%;" disabled>
                                    <option value="">Elige primero el almacén origen ...</option>
                                </select>
                                <small class="form-text text-muted">Solo se listan productos con stock en el almacén origen.</small>
                            </div>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered" id="tabla_items_traslado">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width: 110px;">Stock Origen</th>
                                        <th style="width: 110px;">Cantidad</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_items_traslado">
                                    <tr id="fila_vacia_traslado">
                                        <td colspan="4" class="text-center text-muted">Aún no agregas productos.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label">Observación (opcional):</label>
                                <textarea class="form-control" id="TRA_Observacion" name="TRA_Observacion" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="button" id="btnGuardarTraslado" class="btn btn-primary">
                                <i class="fas fa-truck-loading"></i> Trasladar
                            </button>
                            <button type="button" id="btnLimpiarTraslado" class="btn btn-danger">
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
                    <h5 class="card-title">HISTORIAL DE TRASLADOS</h5>
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="lista_traslados">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Origen</th>
                                    <th scope="col">Destino</th>
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

    <div class="modal fade" id="modalVerTraslado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-truck-loading mr-2"></i>Detalle del Traslado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Origen</small>
                            <strong id="ver_tra_origen"></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Destino</small>
                            <strong id="ver_tra_destino"></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Usuario</small>
                            <strong id="ver_tra_usuario"></strong>
                        </div>
                    </div>
                    <div id="ver_tra_observacion_wrapper" class="mb-3">
                        <small class="text-muted d-block">Observación</small>
                        <span id="ver_tra_observacion"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_ver_traslado"></tbody>
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

            var itemsTraslado = {};

            var table = $('#lista_traslados').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: "{{ tenant_url('tenant.inventario.traslado.index') }}",
                columns: [
                    { data: 'TRA_Id', name: 'TRA_Id' },
                    { data: 'origen', name: 'origen' },
                    { data: 'destino', name: 'destino' },
                    { data: 'usuario', name: 'usuario' },
                    { data: 'items', name: 'items' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#ALM_OrigenId').on('change', function() {
                var almacenId = $(this).val();
                itemsTraslado = {};
                renderItemsTraslado();

                var $selector = $('#selector_producto');
                $selector.empty().append('<option value="">Buscar producto ...</option>');

                if (!almacenId) {
                    $selector.prop('disabled', true);
                    return;
                }

                $selector.prop('disabled', false);

                $.get("{{ tenant_url('tenant.inventario.traslado.stock') }}", { ALM_Id: almacenId }, function(productos) {
                    productos.forEach(function(p) {
                        $selector.append(
                            $('<option>').val(p.PRO_Id).text(p.PRO_Nombre + ' (stock: ' + p.stock + ')').data('stock', p.stock).data('nombre', p.PRO_Nombre)
                        );
                    });
                });
            });

            $('#selector_producto').on('change', function() {
                var proId = $(this).val();
                if (!proId) return;

                var $option = $(this).find('option:selected');
                var stock = parseFloat($option.data('stock'));
                var nombre = $option.data('nombre');

                if (!itemsTraslado[proId]) {
                    itemsTraslado[proId] = { nombre: nombre, stock: stock, cantidad: 1 };
                }

                renderItemsTraslado();
                $(this).val('').trigger('change.select2');
            });

            function renderItemsTraslado() {
                var $tbody = $('#tbody_items_traslado');
                $tbody.empty();

                var keys = Object.keys(itemsTraslado);

                if (keys.length === 0) {
                    $tbody.append('<tr id="fila_vacia_traslado"><td colspan="4" class="text-center text-muted">Aún no agregas productos.</td></tr>');
                    return;
                }

                keys.forEach(function(proId) {
                    var item = itemsTraslado[proId];
                    var fila = $(
                        '<tr data-pro-id="' + proId + '">' +
                            '<td>' + item.nombre + '</td>' +
                            '<td>' + item.stock + '</td>' +
                            '<td><input type="number" min="0.01" max="' + item.stock + '" step="0.01" class="form-control form-control-sm input_cantidad_traslado" value="' + item.cantidad + '"></td>' +
                            '<td class="text-center"><a href="javascript:void(0)" class="text-danger btn_quitar_item_traslado"><i class="fa fa-trash"></i></a></td>' +
                        '</tr>'
                    );
                    $tbody.append(fila);
                });
            }

            $('body').on('input', '.input_cantidad_traslado', function() {
                var proId = $(this).closest('tr').data('pro-id');
                itemsTraslado[proId].cantidad = $(this).val();
            });

            $('body').on('click', '.btn_quitar_item_traslado', function() {
                var proId = $(this).closest('tr').data('pro-id');
                delete itemsTraslado[proId];
                renderItemsTraslado();
            });

            $('#btnLimpiarTraslado').on('click', function() {
                itemsTraslado = {};
                renderItemsTraslado();
                $('#form_traslado')[0].reset();
                $('.select2').val('').trigger('change');
                $('#selector_producto').prop('disabled', true);
            });

            $('#btnGuardarTraslado').on('click', function() {
                var almOrigen = $('#ALM_OrigenId').val();
                var almDestino = $('#ALM_DestinoId').val();
                var keys = Object.keys(itemsTraslado);

                if (!almOrigen || !almDestino) {
                    Toast.fire({ type: 'error', title: 'Selecciona el almacén origen y destino.' });
                    return;
                }

                if (almOrigen === almDestino) {
                    Toast.fire({ type: 'error', title: 'El origen y el destino no pueden ser el mismo almacén.' });
                    return;
                }

                if (keys.length === 0) {
                    Toast.fire({ type: 'error', title: 'Agrega al menos un producto.' });
                    return;
                }

                var proIds = [];
                var cantidades = [];
                var error = null;

                keys.forEach(function(proId) {
                    var item = itemsTraslado[proId];
                    var cantidad = parseFloat(item.cantidad);

                    if (!cantidad || cantidad <= 0) {
                        error = '"' + item.nombre + '": la cantidad debe ser mayor a 0.';
                        return;
                    }

                    if (cantidad > item.stock) {
                        error = '"' + item.nombre + '": no hay stock suficiente en el almacén origen (disponible: ' + item.stock + ').';
                        return;
                    }

                    proIds.push(proId);
                    cantidades.push(cantidad);
                });

                if (error) {
                    Toast.fire({ type: 'error', title: error });
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Guardando...');

                $.ajax({
                    url: "{{ tenant_url('tenant.inventario.traslado.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ALM_OrigenId: almOrigen,
                        ALM_DestinoId: almDestino,
                        TRA_Observacion: $('#TRA_Observacion').val(),
                        PRO_Id: proIds,
                        TRD_Cantidad: cantidades
                    },
                    success: function(data) {
                        Toast.fire({ type: 'success', title: data.success });
                        $('#btnLimpiarTraslado').click();
                        table.draw();
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'No se pudo registrar el traslado.';
                        Toast.fire({ type: 'error', title: msg });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-truck-loading"></i> Trasladar');
                    }
                });
            });

            $('body').on('click', '.verTraslado', function() {
                var trasladoId = $(this).data('id');
                $('#modalVerTraslado').modal('show');

                $.get("{{ tenant_url('tenant.inventario.traslado.show', ['traslado' => ':traslado']) }}".replace(':traslado', trasladoId), function(data) {
                    $('#ver_tra_origen').text(data.traslado.origen);
                    $('#ver_tra_destino').text(data.traslado.destino);
                    $('#ver_tra_usuario').text(data.traslado.usuario);

                    if (data.traslado.TRA_Observacion) {
                        $('#ver_tra_observacion_wrapper').show();
                        $('#ver_tra_observacion').text(data.traslado.TRA_Observacion);
                    } else {
                        $('#ver_tra_observacion_wrapper').hide();
                    }

                    var $tbody = $('#tbody_ver_traslado');
                    $tbody.empty();
                    data.detalle.forEach(function(d) {
                        $tbody.append('<tr><td>' + d.PRO_Nombre + '</td><td>' + d.TRD_Cantidad + '</td></tr>');
                    });
                });
            });
        });
    </script>
@endsection
