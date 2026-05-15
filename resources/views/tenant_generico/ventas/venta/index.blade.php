@extends('tenant_generico.layout.appAdminLte')
@section('titulo', 'Ventas')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">LISTA DE CLIENTES</h5>
                <p class="card-text">
                <button type="button" class="btn btn-primary btn-lg float-right"
                    onclick="window.location.href='{{ route('tenant.ventas.venta.create', tenant('id')) }}'">
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
                        <span><button class="btn btn-primary" class="btn btn-icon btn-sm " onclick="buscar()" type="button" id="btnbuscar"  ><i class="fa fa-search"></i></button></span>
                    </div>
                </div>
                <p class="card-text">
                <div class="table-responsive" style="background:#FFF;" >
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


    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detalle de la Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p ><b>Id Venta: </b></p>
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
                                    <p><b>Cliente:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_Cliente"></p>
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
                        <div class="col-7">
                            <div class="table-responsive" style="height: calc(60vh - 90px);  overflow-y:scroll;">
                                <table id="detallesVenta" class=" table-sm table-condensed " style="width: 100%" >
                                    <thead style="background: #f58489; color:white">
                                        <th style="text-align: left;  width: 80%;">Detalle</th>
                                        <th style="text-align: right;  width: 20%;">Importe</th>
                                    </thead>
                                </table>
                            </div>
                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background-color: #DDD;margin-top:10px;margin-left:1px">

                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="text-align: center;font-weight: bold">
                                    TOTAL</div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="text-align: right; padding-right:10px"><label
                                        id="total" name="total">S/ 0.00</label><input name="total_venta" hidden
                                        id="total_venta"></div>
                                <!--<th hidden="true"><input hidden name="totalVenta" id="totalVenta"></th>-->
                            </div>
                        </div>
                    </div>
                    

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

@endsection
@section('script')
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
                ajax: "{{ route('tenant.ventas.venta.index', tenant('id')) }}",
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
                                + data.ticket 
                            @endcan
                        }
                    }
                ],
            });

            $('body').on('click', '.eyeVenta', function() {
                var Venta_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ route('tenant.ventas.venta.show' , ['venta' => ':venta', 'tenant' => tenant('id')]) }}'.replace(':venta',
                        Venta_id_ver),
                    function(data) {
                        console.log(data)
                        $('#ver_VEN_Id').text(data.venta.codigoVenta);
                        $('#ver_VEN_FechaEmision').text(data.venta.fechaVenta+" "+data.venta.fechaVentaT);
                        $('#ver_VEN_TipoPago').text(data.venta.tipopago == 1?"CONTADO":"CREDITO");
                        $('#ver_MEP_Pago').text(data.venta.MEP_Pago);
                        $('#ver_DOV_TipoComprobante').text(data.venta.tipoDoc == "PRO"?"NOTA VENTA":data.venta.tipoDoc == "BOL"?"BOLETA":"FACTURA");
                        $('#ver_NumComprobante').text(data.venta.serDoc+" - "+data.venta.numDoc);
                        $('#ver_Cliente').text(data.venta.cliente);
                        $('#ver_Empleado').text(data.venta.empleado);

                        data.detalle.forEach(det => {
                            idProducto = det.PRO_Id;
                            producto = det.PRO_Nombre;
                            pVenta = det.precio_venta;
                            cantidad = det.cantidad;
                            subtotal = parseFloat(cantidad * pVenta);
                            var fila1 = [idProducto, producto, pVenta, cantidad, subtotal.toFixed(1), "0"];
                            ListPedido.push(fila1);
                        });

                        $("#detallesVenta tbody").remove();
                        for (var i = 0; i < ListPedido.length; i++) {
                            var fila = '<tr  id="fila' + i + '" onclick="posicionamiento(' + i +
                                ');"><td style="text-align: left; padding:0px 7px" >'+
                                '<div style="display: flex; flex-direction: column; align-items: flex-start; width: 100%;">'+
                                    '<input readonly="true"   hidden name="PRO_Id[]" value="' +ListPedido[i][0] + '">'+
                                    '<span style="font-weight: bold;">' + ListPedido[i][1] +'</span>'+
                                    '<div style="display: flex; gap: 10px; align-items: center;">'+
                                    '<input readonly="true" type="number" onkeyup="Valida2('+i+');" name="DEV_Cantidad[]" hidden id="cantidad'+i+'" value="'+ListPedido[i][3]+'">'+
                                    '<label id="cantidadLabel' + i + '" style="font-size: 11px;display: inline;">' + ListPedido[i][3] + '</label><small style="color: gray;display: inline; "> Unit x S/ <label id="precioUnitLabel' + i +'" style="font-size: 11px;">' + ListPedido[i][2] +'</label></small> ';                            
                                fila += '<label id="descuentoLabel' + i + '" style="font-size: 11px;"></label> ';
                                fila += '<input readonly="true" hidden  type="number"   name="DEV_PrecioUnitario[]" id="precioUnit'+ i +'" value="' + ListPedido[i][2] + '" >'+
                                    '<input readonly="true" hidden name="DEV_Descuento[]" onkeyup="Valida2('+i+ ');" id="descuento'+i+'"  value="' + ListPedido[i][5] + '">'+
                                '</div></div></td><td style="vertical-align: top; text-align: right;">'+
                                '<input disabled name="subTot' + i + '" hidden id="subTot' + i + '"  value="' + ListPedido[i][4] + '">'+
                                '<label id="subTotLabel' + i +
                                '" style="font-size: 13px; font-weight: bold;"> S/ ' + ListPedido[i][4] + '</label>'+
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

        })

        function buscar (){
          mostrarSeleccionado();
        }

        function mostrarSeleccionado()
        {
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
                ajax: "{{ route('tenant.ventas.venta.index', tenant('id')) }}"+ "/filtro/" + $('#date_range').val(),
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
                                + data.ticket 
                            @endcan
                        }
                    }
                ],
            });
        }
    </script>
@endsection
