@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Ventas')
@section('contenido')

    <style>
        .detalle-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f1f1;
            gap: 15px;
        }

        .detalle-item span {
            color: #6c757d;
            font-weight: 500;
        }

        .detalle-item strong {
            color: #1f2937;
            text-align: right;
        }

        #detallesVenta tbody tr {
            transition: 0.2s;
        }

        #detallesVenta tbody tr:hover {
            background: #f8f9fa;
        }

        .modal-content {
            overflow: hidden;
        }

        .whatsapp-circle{
            width:90px;
            height:90px;
            background:#25D366;
            color:white;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:auto;
            font-size:45px;
            box-shadow:0 10px 30px rgba(37,211,102,.3);
        }
    </style>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">LISTA DE CLIENTES</h5>
                <p class="card-text">
                    <button type="button" class="btn btn-primary btn-lg float-right"
                        onclick="window.location.href='{{ tenant_url('tenant.ventas.venta.create') }}'">
                        + Nuevo
                    </button>
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: #EDEDED;">Fecha Inicio y Limite</span>
                            </div>
                            <input name="nuevofiltro" type="text" class="form-control" id="date_range">
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
                        <span><button class="btn btn-primary" class="btn btn-icon btn-sm " onclick="buscar()" type="button"
                                id="btnbuscar"><i class="fa fa-search"></i></button></span>
                    </div>
                </div>
                <p class="card-text">
                <div class="table-responsive" style="background:#FFF;">
                    <table class="table" id="lista_ventas">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Almacen</th>
                                <th scope="col">Documento</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Met. Pago</th>
                                <th scope="col">Importe</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalVerDetalle" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">

                <!-- HEADER -->
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold">
                        <i class="fa fa-shopping-cart me-2 text-primary"></i>
                        Detalle de la Venta
                    </h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body pt-2">

                    <div class="row g-4">

                        <!-- INFORMACION -->
                        <div class="col-lg-4">

                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body">

                                    <h5 class="fw-bold mb-4 text-primary">
                                        Información General
                                    </h5>

                                    <div class="detalle-item">
                                        <span>Id Venta</span>
                                        <strong id="ver_VEN_Id"></strong>
                                    </div>

                                    <div class="detalle-item">
                                        <span>Fecha Emisión</span>
                                        <strong id="ver_VEN_FechaEmision"></strong>
                                    </div>

                                    <div class="detalle-item">
                                        <span>Tipo Pago</span>
                                        <strong id="ver_VEN_TipoPago"></strong>
                                    </div>

                                    <div class="detalle-item">
                                        <span>Método Pago</span>
                                        <strong id="ver_MEP_Pago"></strong>
                                    </div>

                                    <div class="detalle-item">
                                        <span>Comprobante</span>
                                        <strong id="ver_DOV_TipoComprobante"></strong>
                                    </div>

                                    <div class="detalle-item">
                                        <span>N° Comprobante</span>
                                        <strong id="ver_NumComprobante"></strong>
                                    </div>

                                    <div class="detalle-item">
                                        <span>Cliente</span>
                                        <strong id="ver_Cliente"></strong>
                                    </div>

                                    <div class="detalle-item border-bottom-0 pb-0">
                                        <span>Empleado</span>
                                        <strong id="ver_Empleado"></strong>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- DETALLE -->
                        <div class="col-lg-8">

                            <div class="card border-0 shadow-sm rounded-4">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold text-primary mb-0">
                                            Productos
                                        </h5>

                                        <span class="badge bg-primary fs-6">
                                            Venta
                                        </span>
                                    </div>

                                    <div class="table-responsive" style="max-height: 420px; overflow-y:auto;">

                                        <table id="detallesVenta" class="table align-middle">

                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th style="width:75%">
                                                        Detalle
                                                    </th>

                                                    <th class="text-end" style="width:25%">
                                                        Importe
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                        </table>
                                    </div>

                                    <!-- TOTAL -->
                                    <div class="mt-4">

                                        <div class="bg-light rounded-4 p-3">

                                            <div class="d-flex justify-content-between align-items-center">

                                                <span class="fw-bold fs-5">
                                                    TOTAL
                                                </span>

                                                <span class="fw-bold fs-4 text-success" id="total">
                                                    S/ 0.00
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0 pt-0">

                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">

                        Cerrar

                    </button>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalWhatsapp" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content border-0 shadow-lg rounded-4">

                <!-- HEADER -->
                <div class="modal-header border-0 pb-0">

                    <h4 class="modal-title fw-bold text-success">

                        <i class="fa fa-whatsapp me-2"></i>

                        Enviar Ticket

                    </h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <div class="text-center mb-4">

                        <div class="whatsapp-circle">

                            <i class="fa fa-whatsapp"></i>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-bold">

                            Número WhatsApp

                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                +51
                            </span>

                            <input type="text" id="numeroWhatsapp" class="form-control form-control-lg"
                                placeholder="999999999" maxlength="9">

                        </div>

                        <small class="text-muted">
                            Ingresa el número del cliente
                        </small>

                    </div>

                    <input type="hidden" id="ventaWhatsappId">

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0">

                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="button" class="btn btn-success rounded-pill px-4" id="btnEnviarWhatsapp">

                        <i class="fa fa-paper-plane me-2"></i>

                        Enviar

                    </button>

                </div>

            </div>

        </div>

    </div>



    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

@endsection
@section('script')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        var ListPedido = [];
        var table;
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            $('#date_range').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                "locale": {
                    "format": "YYYY-MM-DD",
                    "separator": " - ",
                    "applyLabel": "Guardar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "Desde",
                    "toLabel": "Hasta",
                    "customRangeLabel": "Personalizar",
                    "daysOfWeek": [
                        "Do",
                        "Lu",
                        "Ma",
                        "Mi",
                        "Ju",
                        "Vi",
                        "Sa"
                    ],
                    "monthNames": [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Setiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                    ],
                    "firstDay": 1
                },
            });

            $('.select2').select2();

            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            table = $('#lista_ventas').DataTable({
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
                    [0, "desc"]
                ],
                ajax: "{{ tenant_url('tenant.ventas.venta.index') }}",
                columns: [{
                        data: 'VEN_Id',
                        name: 'VEN_Id',
                        className: 'text-start'
                    },
                    {
                        data: 'ALM_NombreAlmacen',
                        name: 'ALM_NombreAlmacen',
                        className: 'text-start'
                    },
                    {
                        data: 'documento',
                        name: 'documento',
                        className: 'text-start'
                    },
                    {
                        data: 'CLI_Nombre',
                        name: 'CLI_Nombre',
                        className: 'text-start'
                    },
                    {
                        data: 'MEP_Pago',
                        name: 'MEP_Pago',
                        className: 'text-start'
                    },
                    {
                        data: 'importe',
                        name: 'importe',
                        className: 'text-start'
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        className: 'text-start'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return @can('tenant.ventas.venta.show')
                                    data.action3 + ' ' +
                                @endcan
                            ''
                            @can('tenant.ventas.venta.show')
                                +data.ticket + ' '+ data.pdf + ' ' + data.whatsapp
                            @endcan
                        }
                    }
                ],
            });

            $('body').on('click', '.eyeVenta', function() {
                var Venta_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.ventas.venta.show', ['venta' => ':venta', 'tenant' => tenant('id')]) }}'
                    .replace(':venta',
                        Venta_id_ver),
                    function(data) {
                        console.log(data)
                        $('#ver_VEN_Id').text(data.venta.codigoVenta);
                        $('#ver_VEN_FechaEmision').text(data.venta.fechaVenta + " " + data.venta
                            .fechaVentaT);
                        $('#ver_VEN_TipoPago').text(data.venta.tipopago == 1 ? "CONTADO" : "CREDITO");
                        $('#ver_MEP_Pago').text(data.venta.MEP_Pago);
                        $('#ver_DOV_TipoComprobante').text(data.venta.tipoDoc == "PRO" ? "NOTA VENTA" :
                            data.venta.tipoDoc == "BOL" ? "BOLETA" : "FACTURA");
                        $('#ver_NumComprobante').text(data.venta.serDoc + " - " + data.venta.numDoc);
                        $('#ver_Cliente').text(data.venta.cliente);
                        $('#ver_Empleado').text(data.venta.empleado);

                        data.detalle.forEach(det => {
                            idProducto = det.PRO_Id;
                            producto = det.PRO_Nombre;
                            pVenta = det.precio_venta;
                            cantidad = det.cantidad;
                            subtotal = parseFloat(cantidad * pVenta);
                            var fila1 = [idProducto, producto, pVenta, cantidad, subtotal
                                .toFixed(1), "0"
                            ];
                            ListPedido.push(fila1);
                        });

                        $("#detallesVenta tbody").remove();
                        for (var i = 0; i < ListPedido.length; i++) {
                            var fila = '<tr  id="fila' + i + '" onclick="posicionamiento(' + i +
                                ');"><td style="text-align: left; padding:0px 7px" >' +
                                '<div style="display: flex; flex-direction: column; align-items: flex-start; width: 100%;">' +
                                '<input readonly="true"   hidden name="PRO_Id[]" value="' + ListPedido[
                                    i][0] + '">' +
                                '<span style="font-weight: bold;">' + ListPedido[i][1] + '</span>' +
                                '<div style="display: flex; gap: 10px; align-items: center;">' +
                                '<input readonly="true" type="number" onkeyup="Valida2(' + i +
                                ');" name="DEV_Cantidad[]" hidden id="cantidad' + i + '" value="' +
                                ListPedido[i][3] + '">' +
                                '<label id="cantidadLabel' + i +
                                '" style="font-size: 11px;display: inline;">' + ListPedido[i][3] +
                                '</label><small style="color: gray;display: inline; "> Unit x S/ <label id="precioUnitLabel' +
                                i + '" style="font-size: 11px;">' + ListPedido[i][2] +
                                '</label></small> ';
                            fila += '<label id="descuentoLabel' + i +
                                '" style="font-size: 11px;"></label> ';
                            fila +=
                                '<input readonly="true" hidden  type="number"   name="DEV_PrecioUnitario[]" id="precioUnit' +
                                i + '" value="' + ListPedido[i][2] + '" >' +
                                '<input readonly="true" hidden name="DEV_Descuento[]" onkeyup="Valida2(' +
                                i + ');" id="descuento' + i + '"  value="' + ListPedido[i][5] + '">' +
                                '</div></div></td><td style="vertical-align: top; text-align: right;">' +
                                '<input disabled name="subTot' + i + '" hidden id="subTot' + i +
                                '"  value="' + ListPedido[i][4] + '">' +
                                '<label id="subTotLabel' + i +
                                '" style="font-size: 13px; font-weight: bold;"> S/ ' + ListPedido[i][
                                    4
                                ] + '</label>' +
                                '</td></tr>';
                            $("#detallesVenta").append(fila);
                            $POS = i;
                        }

                        $("#detallesVenta").on("click", "tr", function() {
                            // Remueve la clase 'selected' de todas las filas
                            $("#detallesVenta tr").removeClass("selected");
                            // Añade la clase 'selected' a la fila clickeada
                            $(this).addClass("selected");
                        });

                        total1 = 0;
                        for (var i = 0; i < ListPedido.length; i++) {
                            total1 = (parseFloat(total1) + parseFloat(ListPedido[i][4])).toFixed(1);
                            //alert(ListPedido[i][5]);

                        }
                        $("#total").html("S/. " + total1);
                        $("#total").val(total1);


                    })
            });

            $('body').on('click', '.envioWhatsapp', function () {
                console.log("click en whatsapp")
                let Venta_id_ver = $(this).data('id');

                $('#ventaWhatsappId').val(Venta_id_ver);

                $('#modalWhatsapp').modal('show');

            });

            $('#btnEnviarWhatsapp').click(async function () {

                let numero = $('#numeroWhatsapp').val();

                if(numero.length !== 9){

                    alert('Ingrese un número válido');

                    return;
                }

                // GENERAR IMAGEN
                const ticket = document.getElementById('ticket');

                const canvas = await html2canvas(ticket, {
                    scale: 3,
                    backgroundColor: '#ffffff'

                });

                const imagen = canvas.toDataURL('image/png');

                // DESCARGAR AUTOMÁTICAMENTE
                const link = document.createElement('a');

                link.href = imagen;

                link.download = 'ticket.png';

                link.click();

                // MENSAJE
                let mensaje = encodeURIComponent(
                    'Hola 😊\nGracias por su compra.\nAdjuntamos su ticket.'
                );

                // ABRIR WHATSAPP
                window.open(`https://wa.me/51${numero}?text=${mensaje}`,'_blank');

            });

        })

        

        function buscar() {
            mostrarSeleccionado();
        }

        function mostrarSeleccionado() {
            $("#lista_ventas").dataTable().fnDestroy();
            table = $('#lista_ventas').DataTable({
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
                    [0, "desc"]
                ],
                ajax: "{{ tenant_url('tenant.ventas.venta.index') }}" + "/filtro/" + $('#date_range')
                    .val(),
                columns: [{
                        data: 'VEN_Id',
                        name: 'VEN_Id',
                        className: 'text-start'
                    },
                    {
                        data: 'ALM_NombreAlmacen',
                        name: 'ALM_NombreAlmacen',
                        className: 'text-start'
                    },
                    {
                        data: 'documento',
                        name: 'documento',
                        className: 'text-start'
                    },
                    {
                        data: 'CLI_Nombre',
                        name: 'CLI_Nombre',
                        className: 'text-start'
                    },
                    {
                        data: 'MEP_Pago',
                        name: 'MEP_Pago',
                        className: 'text-start'
                    },
                    {
                        data: 'importe',
                        name: 'importe',
                        className: 'text-start'
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        className: 'text-start'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return
                            @can('tenant.ventas.venta.show')
                                data.action3 + ' ' +
                            @endcan
                            ''
                            @can('tenant.ventas.venta.show')
                                +data.ticket
                            @endcan
                        }
                    }
                ],
            });
        }
    </script>
@endsection
