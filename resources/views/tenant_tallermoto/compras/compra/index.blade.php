@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Compra')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">COMPRA REALIZADA</h5>
                <button type="button" class="btn btn-primary btn-lg float-right"
                    onclick="window.location.href='{{ tenant_url('tenant.compras.compra.create') }}'">
                    + Nuevo
                </button>
                <div class="table-responsive" style="background:#FFF;">
                    <table class="table" id="tabla_compra">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">DOC</th>
                                <th scope="col">N° DOC.</th>
                                <th scope="col">NOMBRE DE PROVEEDOR</th>
                                <th scope="col">TIPO DE PAGO</th>
                                <th scope="col">MÉTODO DE PAGO</th>
                                <th scope="col">ESTADO</th>
                                <th scope="col">OPCIONES</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>





    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content ">
                <div class="modal-header bg-primary {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} border-0">
                    <div>
                        <h5 class="modal-title mb-0">
                            <i class="fas fa-user-circle me-2"></i>
                            Detalles de la compra
                        </h5>
                        <small class="opacity-75">
                            Información general de la compra registrada
                        </small>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">

                    <div class="row g-3">

                        <!-- INFORMACION COMPRA -->
                        <div class="col-lg-4">

                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-invoice"></i>
                                        Información de Compra
                                    </h6>
                                </div>

                                <div class="card-body">

                                    <div class="mb-3">
                                        <small class="text-muted">ID Compra</small>
                                        <h5 class="mb-0 fw-bold" id="ver_VEN_Id"></h5>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Fecha Emisión</small>
                                        <div id="ver_VEN_FechaEmision" class="fw-semibold"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <small class="text-muted">Tipo Pago</small>
                                            <div id="ver_VEN_TipoPago" class="fw-semibold"></div>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <small class="text-muted">Método Pago</small>
                                            <div id="ver_MEP_Pago" class="fw-semibold"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Tipo Comprobante</small>
                                        <div id="ver_DOV_TipoComprobante" class="fw-semibold"></div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">N° Comprobante</small>
                                        <div id="ver_NumComprobante" class="fw-semibold"></div>
                                    </div>

                                    <hr>

                                    <div class="mb-3">
                                        <small class="text-muted">Proveedor</small>
                                        <div id="ver_Proveedor" class="fw-bold text-dark"></div>
                                    </div>

                                    <div>
                                        <small class="text-muted">Empleado</small>
                                        <div id="ver_Empleado" class="fw-semibold"></div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- DETALLE -->
                        <div class="col-lg-8">

                            <div class="card shadow-sm border-0">

                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-box"></i>
                                        Detalle de Productos
                                    </h6>

                                    <span class="badge bg-success fs-6">
                                        TOTAL:
                                        <span id="total"></span>
                                    </span>
                                </div>

                                <div class="card-body p-0">

                                    <div class="table-responsive" style="max-height: 500px; overflow-y:auto;">

                                        <table class="table table-hover align-middle mb-0">

                                            <thead class="table-light sticky-top">

                                                <tr>
                                                    <th>#</th>
                                                    <th>Producto</th>
                                                    <th>P.V</th>
                                                    <th>P.C</th>
                                                    <th>Cantidad</th>
                                                    <th>Importe</th>
                                                    <th>Almacén</th>
                                                </tr>

                                            </thead>

                                            <tbody id="detalles">

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        var ListPedido = []
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            var table = $('#tabla_compra').DataTable({
                responsive: true,
                autoWidth: false,
                searchDelay : 800,
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                dom: 'Blfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'pdfHtml5'
                ],
                ajax: {
                    url: "{{ tenant_url('tenant.compras.compra.index') }}",
                    type: "GET",
                    error: function(xhr, error) {
                        console.error("Error en la carga de datos:", error);
                    }
                },
                columns: [{
                        data: 'COM_Id',
                        name: 'COM_Id'
                    },
                    {
                        data: 'COM_TipoDocumento',
                        name: 'COM_TipoDocumento',
                    },
                    {
                        data: 'COM_NumDocumento',
                        name: 'COM_NumDocumento',
                    },
                    {
                        data: 'PROV_RazonSocial',
                        name: 'PROV_RazonSocial',
                    },
                    {
                        data: 'COM_TipoPago',
                        name: 'COM_TipoPago',
                    },
                    {
                        data: 'MEP_Pago',
                        name: 'MEP_Pago',
                    },
                    {
                        data: 'COM_Status',
                        name: 'COM_Status',
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return data.action3 + ' ' + data.action1 + ' ' + data.action2;
                        }
                    }
                ],
            });

            $('body').on('click', '.editCompra', function() {
                var Compra_id = $(this).data('id');
                window.location.href = "/compra/" + Compra_id + "/edit";
            });

            $('body').on('click', '.eyeCompra', function() {
                ListPedido = []
                var Compra_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.compras.compra.show', ['compra' => ':compra']) }}'.replace(
                        ':compra',
                        Compra_id_ver),
                    function(data) {
                        console.log(data)
                        const dateObj = new Date(data.compra.created_at);
                        // Obtener fecha y hora por separado
                        const fecha = dateObj.toISOString().split("T")[0];
                        const hora = dateObj.toTimeString().split(" ")[0];
                        $('#ver_VEN_Id').text(data.compra.COM_Id);
                        $('#ver_VEN_FechaEmision').text(fecha + " " + hora);
                        $('#ver_VEN_TipoPago').text(data.compra.COM_TipoPago);
                        $('#ver_MEP_Pago').text(data.compra.metodo_pago.MEP_Pago);
                        $('#ver_DOV_TipoComprobante').text(data.compra.COM_TipoDocumento);
                        $('#ver_NumComprobante').text(data.compra.COM_NumDocumento);
                        $('#ver_Proveedor').text(data.compra.proveedor.PROV_RazonSocial);
                        $('#ver_Empleado').text(data.compra.users.name);

                        data.compra.detalle_compra.forEach(det => {
                            idProducto = det.PRO_Id;
                            almacen = det.almacen.ALM_NombreAlmacen;
                            idalmacen = det.ALM_Id;
                            producto = det.producto.categoria.CAT_Nombre + ' - ' + det.producto
                                .PRO_Nombre;
                            pVenta = det.DCOM_PrecioVenta;
                            pCompra = det.DCOM_PrecioCompra;
                            cantidad = det.DCOM_Cantidad;
                            subtotal = parseFloat(cantidad * pCompra);
                            var fila1 = [idProducto, producto, pVenta, pCompra, cantidad,
                                subtotal.toFixed(2), almacen, idalmacen
                            ];
                            ListPedido.push(fila1);
                        });

                        actualizarTabla()

                        total1 = 0;
                        for (var i = 0; i < ListPedido.length; i++) {
                            total1 = (parseFloat(total1) + parseFloat(ListPedido[i][5])).toFixed(2);
                        }
                        $("#total").html("S/. " + total1);

                    })
            });

        })

        function actualizarTabla() {
            $("#detalles tbody").html('');

            for (var i = ListPedido.length - 1; i >= 0; i--) {
                var col1 = '<tr class="selected" id="fila' + i + '">'
                var col2 = '<td style="text-align: center; width: 20px">' + (i + 1) + '</td>'
                var col3 =
                    '<td style=width: 20px"><input style="width:80px" type="hidden" name="PRO_Id[]" value="' +
                    ListPedido[i][0] + '">' + ListPedido[i][1] + '</td>'
                var col4 =
                    '<td style=width: 20px">' + ListPedido[i][2] + '</td>'
                var col5 = '<td style=width: 20px">' + ListPedido[i][3] + '</td>'
                var col6 = '<td style=width: 20px">' + ListPedido[i][4] + '</td>'
                var col7 = '<td style="text-align: right;">' + ListPedido[i][5] + '</td>'
                var col8 = '<td style=width: 20px">' + ListPedido[i][6] + '</td></tr>';

                var fila = col1 + col2 + col3 + col4 + col5 + col6 + col7 + col8;
                $("#detalles").append(fila);

            }
        }
    </script>
@endsection
