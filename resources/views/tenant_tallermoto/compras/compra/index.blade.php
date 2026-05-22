@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
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
                    <thead >
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
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detalle de la Compra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p ><b>Id Compra: </b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_VEN_Id"></p>
                                </div>                        
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Fecha Emision:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_VEN_FechaEmision"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Tipo Pago:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_VEN_TipoPago"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Metodo Pago:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_MEP_Pago"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Tipo Comprobante:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_DOV_TipoComprobante"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>N° Comprobante:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_NumComprobante"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Proveedor:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_Proveedor"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Empleado:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_Empleado"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="margin-top: 15px; ">
                                <div class="table-responsive"
                                    style="max-height: calc(55vh - 90px);   overflow-y:scroll;">
                                    <table
                                        style="width: 100%"
                                        class="table-sm table-striped table-bordered table-condensed table-hover"
                                        id="detalles">
                                        <thead style="background: #FF5F67;color: #fff;">
                                            <th style="text-align: left;width: 20px">
                                                N°
                                            </th>
                                            <th style="text-align: left; width: 350px">
                                                Producto
                                            </th>
                                            <th style="text-align: left; width: 100px">
                                                P.V
                                            </th>
                                            <th style="text-align: left; width: 100px">
                                                P.C
                                            </th>
                                            <th style="text-align: left; width: 50px">
                                                Cant
                                            </th>
                                            <th style="text-align: left; width: 50px">
                                                Importe
                                            </th>
                                            <th style="text-align: left; width: 150px">
                                                Almacen
                                            </th>
                                        </thead>
                                    </table>
                                </div>
                                <foot class="row mt-2">
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"
                                        style="text-align: center">
                                        TOTAL
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                        style="text-align: left; pl-3">
                                        <label id="total" name="total">
                                        </label>
                                    </div>
                                    <!--<th hidden="true"><input type="hidden" name="totalCompra" id="totalCompra"></th>-->
                                </foot>
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
                responsive: true, // Habilitar la opción responsive
                autoWidth: false,
                searchDelay: 2000,
                processing: true,
                serverSide: true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "Nada encontrado - disculpa",
                    "info": "Mostrando la página _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },

                order: [
                    [0, "asc"]
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
                window.location.href = "/compra/"+Compra_id+"/edit";
            });

            $('body').on('click', '.eyeCompra', function() {
                var Compra_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.compras.compra.show', ['compra' => ':compra', 'tenant' => tenant('id')]) }}'.replace(':compra',
                        Compra_id_ver),
                    function(data) {
                        console.log(data)
                        const dateObj = new Date(data.compra.created_at);
                        // Obtener fecha y hora por separado
                        const fecha = dateObj.toISOString().split("T")[0];
                        const hora = dateObj.toTimeString().split(" ")[0];
                        $('#ver_VEN_Id').text(data.compra.COM_Id);
                        $('#ver_VEN_FechaEmision').text(fecha+" "+hora);
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
                            producto = det.producto.categoria.CAT_Nombre+' - '+det.producto.PRO_Nombre;
                            pVenta = det.DCOM_PrecioVenta;
                            pCompra = det.DCOM_PrecioCompra;
                            cantidad = det.DCOM_Cantidad;
                            subtotal = parseFloat(cantidad * pCompra);
                            var fila1 = [idProducto, producto, pVenta, pCompra, cantidad, subtotal.toFixed(2), almacen, idalmacen];
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

        function actualizarTabla(){
                $("#detalles tbody").remove();

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

                    var fila = col1 + col2 + col3 + col4 + col5 + col6 + col7 + col8 ;
                    $("#detalles").append(fila);

                }
            }

    </script>
@endsection
