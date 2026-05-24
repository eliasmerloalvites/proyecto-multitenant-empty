@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Registro Compra')
@section('contenido')

<form class="row" method="POST" id="compra_form" action="{{ tenant_url('tenant.compras.compra.store') }}">
    @csrf
    <div class="col-12 col-md-4">
        <div class="card">
            <div class="card-body" >
                <h5 class="card-title">COMPRA REALIZADA</h5>
                <p class="card-text"></p>

                <div class="col-12 mt-3">
                    <div class="input-group ">
                        <span class="input-group-text" style="background: #EDEDED;">
                            <i class="fa fa-file">
                            </i>
                        </span>
                        <select class="input-group-addon" id="COM_TipoDocumento" name="COM_TipoDocumento" required="">
                            <option value="">
                                Tipo Documento
                            </option>
                            <option value="BOL">
                                BOLETA
                            </option>
                            <option value="FAC">
                                FACTURA
                            </option>
                            <option value="NOV">
                                NOTA DE VENTA
                            </option>
                        </select>
                        <span class="input-group-addon" style="background: #EDEDED;">
                        </span>
                        <input class="form-control input-group-addon" disabled="" id="COM_NumDocumento" maxlength="30" name="COM_NumDocumento" placeholder="#N Doc" required="" style="border-bottom-right-radius:10px; border-top-right-radius: 10px;" type="text">
                        </input>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                Tipo pago
                            </span>
                        </div>
                        <select class="form-control" id="idCOM_TipoPago" name="COM_TipoPago"
                            required>
                            <option value="">Seleccione tipo de pago
                            </option>
                            <option value="Contado">Contado</option>
                            <option value="Credito">Crédito</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                Metodo de pago
                            </span>
                        </div>
                        <select class="form-control" id="idMEP_Id" name="MEP_Id" required="">
                            <option value="">Seleccione metodo</option>
                            @foreach ($metodo_pago as $mep)
                                <option value="{{ $mep->MEP_Id }}">{{ $mep->MEP_Pago }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row col-12 mt-3">
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 " >
                        <div class="input-group">
                            <select class="select2 form-control" data-size="5" style="width: 100%" data-live-search="true" id="PROV_Id" name="PROV_Id" required="">
                                <option value="">Seleccione proveedor
                                </option>
                                @foreach ($proveedor as $itemproveedor)
                                    <option value="{{ $itemproveedor->PROV_Id }}">
                                        {{ $itemproveedor->PROV_TipoDocumento }} -
                                        {{ $itemproveedor->PROV_NumDocumento }} -
                                        {{ $itemproveedor->PROV_RazonSocial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-top: 1px">
                        <button title="Nuevo Proveedor" type="button" id="btnNuevoProveedor"
                            class="btn btn-icon btn-primary" onclick="NuevoProveedor()"
                            title="Agregar Nuevo Proveedor">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">INFORMACIÓN DEL PRODUCTO</h5>
                <p class="card-text">
                <div class="form-group row mb-3">
                    <!-- Columna Producto -->
                    <div class="col-md-8 d-flex align-items-center">
                        <div class="col-md-12 pl-0 pr-0">
                            <select class=" select2 form-control form-control-sm"
                                id="productoVenta">
                                <option value="" disabled selected>Seleccione Producto
                                </option>
                                @foreach ($producto as $itemProducto)
                                    <option
                                        value="{{ $itemProducto->PRO_Id }}_{{ $itemProducto->PRO_PrecioCompra }}_{{ $itemProducto->PRO_PrecioVenta }}">
                                        {{ $itemProducto->CAT_Nombre }} -
                                        {{ $itemProducto->PRO_Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Columna Almacén -->
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    Almacén
                                </span>
                            </div>
                            <select class="form-control" id="Almacenid" required>
                                <option value="">Seleccione Almacén</option>
                                @foreach ($almacen as $itemAlmacen)
                                    <option value="{{ $itemAlmacen->ALM_Id }}">
                                        {{ $itemAlmacen->ALM_NombreAlmacen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <!-- Precio de compra -->
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    P. Compra
                                </span>
                            </div>
                            <input type="number" id="precioCompra" class="form-control"
                                placeholder="Precio Compra">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    P. Venta
                                </span>
                            </div>
                            <input type="number" id="precioVenta" class="form-control"
                                placeholder="Precio Venta">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    Cantidad
                                </span>
                            </div>
                            <input type="number" id="cantidadCompra" class="form-control"
                                placeholder="Cantidad">
                            <button title="Agregar al carrito" type="button"
                                id="btnAñadirCarrito" class="btn btn-icon btn-success"
                                onclick="Añadir()" title="Agregar al carrito">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="margin-top: 15px; ">
                    <div class="table-responsive"
                        style="max-height: calc(55vh - 90px);   overflow-y:scroll;">
                        <table
                            style="width: 100%"
                            class="table-sm table-striped table-bordered table-condensed table-hover"
                            id="detalles">
                            <thead>
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
                                <th style="width:80px; text-align: center">
                                    Quitar
                                </th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <foot class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"
                            style="text-align: center">
                            TOTAL
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                            style="text-align: left;">
                            <label id="total" name="total">
                            </label>
                            <input id="total_venta" name="total_venta" type="hidden" />
                        </div>
                        <!--<th hidden="true"><input type="hidden" name="totalVenta" id="totalVenta"></th>-->
                    </foot>
                </div>


                <p></p>
                <div class="form-group text-right">
                    <button type="reset" id="btncancelar" class="btn btn-danger"> <i
                            class="fas fa-ban"></i> Vaciar Compra </button>
                    <button type="button" disabled onclick="GenerarCompra();" id="saveBtn" class="btn btn-success"><i class="fas fa-save"></i> Generar
                        Compra</button>
                </div>
            </div>
        </div>
    </div>

</form>



<div class="modal fade " id="myModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
    tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Proveedor</h5>
            </div>
            <div class="modal-body">

                <form method="POST" id="proveedor_form" action="{{ tenant_url('tenant.compras.proveedor.store') }}">
                    <div class="modal-body panel-body" style="max-height: calc(90vh - 90px);">
                        <div class="form-group col-lg-12  col-md-12 col-sm-12 col-xs-12 input-group-sm">
                            <label>TIPO DOCUMENTO</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        TIPO DOCUMENTO
                                    </span>
                                </div>
                                <select class="form-control" onChange="Limitar()" id="idPROV_TipoDocumento"
                                    name="PROV_TipoDocumento"
                                    style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;">
                                    <option value="DNI">
                                        DNI
                                    </option>
                                    <option value="RUC">
                                        RUC
                                    </option>
                                    <option value="CE">
                                        Carnet Extrangeria
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Nº Doc
                                    </span>
                                </div>
                                <input class="form-control input-sm" maxlength="8" id="idPROV_NumDocumento"
                                    name="PROV_NumDocumento" required placeholder="Ingresa Numero de Documento"
                                    type="text">
                                <span class="input-group-append btn btn-primary btn-sm" id="Buscar_Proveedor"
                                    style="display: block; border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                    onclick="buscarProveedor()"><i class="fas fa-search"></i></span>
                                <span class="input-group-append btn btn-primary btn-sm hide" id="cargando"
                                    style="display: none; border-bottom-right-radius:10px; border-top-right-radius: 10px;"><img
                                        width="15px" src="{{ asset_root('images/gif/cargando1.gif') }}"></span>
                            </div>
                        </div>

                        <div class="form-group col-md-12 input-group-sm">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Razon social
                                    </span>
                                </div>
                                <input class="form-control input-sm" id="idPROV_RazonSocial"
                                    name="PROV_RazonSocial" onkeyup="this.value=this.value.toUpperCase();"
                                    required placeholder="Ingresa Nombre / Razon Social"
                                    style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                    type="text">
                            </div>
                        </div>
                        <div class="form-group col-md-12 input-group-sm">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Dirección
                                    </span>
                                </div>
                                <input class="form-control input-sm" id="idPROV_Direccion" name="PROV_Direccion"
                                    onkeyup="this.value=this.value.toUpperCase();" placeholder="ingresa dirección"
                                    style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                    type="text">
                            </div>
                        </div>
                        <div class="form-group col-md-12 input-group-sm">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Descripción
                                    </span>
                                </div>
                                <input class="form-control input-sm" id="idPROV_Descripcion"
                                    name="PROV_Descripcion" onkeyup="this.value=this.value.toUpperCase();"
                                    placeholder="ingresa descripción"
                                    style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                    type="text">
                            </div>
                        </div>
                        <div class="form-group col-md-12 input-group-sm">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Celular
                                    </span>
                                </div>
                                <input class="form-control input-sm" id="idPROV_Celular" name="PROV_Celular"
                                    onkeyup="this.value=this.value.toUpperCase();" placeholder="ingresa celular"
                                    style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                    type="number">
                            </div>
                        </div>
                        <div class="form-group col-md-12 input-group-sm">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Correo
                                    </span>
                                </div>
                                <input class="form-control input-sm" id="idPROV_Correo" name="PROV_Correo"
                                    onkeyup="this.value=this.value.toUpperCase();"
                                    placeholder="ingresa descripción"
                                    style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                    type="email">
                            </div>
                        </div>

                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" id="SaveProveedor" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>


    @endsection
    @section('script')
        <script>
            var myModal
            var idproductoalmacen = 0;
            var ListPedido = []
            $(document).ready(function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'info'
                });

                $('.select2').select2()
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })
                myModal = new bootstrap.Modal(document.getElementById('myModal'), {
                    keyboard: false
                })
                $("#COM_TipoDocumento").change(mostrarDoc);
                $("#productoVenta").change(mostrarValores);
                $('#cantidadCompra').keyup(function(e) {
                    if (e.keyCode == 13) {
                        añadir();
                    }
                });
                $('#precioVenta').keyup(function(e) {
                    if (e.keyCode == 13) {
                        añadir();
                    }
                });
                $('#COM_Moneda').keyup(function(e) {
                    if (e.keyCode == 13) {
                        añadir();
                    }
                });

            })

            function mostrarDoc() {
                $fil = $('#COM_TipoDocumento').val();
                if ($fil == "") {
                    $('#COM_NumDocumento').attr('disabled', true);
                    $('#saveBtn').attr('disabled', true);
                } else {
                    $('#COM_NumDocumento').attr('disabled', false);
                    $('#saveBtn').attr('disabled', false);
                }

                if ($fil == "NOV") {
                    /* $('#COM_NumDocumento').val(numero);
                    $('#saveBtn').attr('disabled', false); */
                } else {
                    $('#COM_NumDocumento').val('');
                }

            }

            function mostrarValores() {
                /* $('#Almacenid').find('option').remove();
                $('#Almacenid').selectpicker('refresh'); */

                datosArticulo = document.getElementById('productoVenta').value.split('_');

                idproductoalmacen = datosArticulo[0];
                $precioc = parseFloat(datosArticulo[1]);
                $preciov = parseFloat(datosArticulo[2]);
                // filtrar();

                if ($precioc > 0.00 || $preciov > 0.00) {
                    $("#precioCompra").val($precioc.toFixed(2));
                    $("#precioVenta").val($preciov.toFixed(2));
                } else {
                    $("#precioCompra").val('');
                    $("#precioVenta").val('');
                }
            }

            function Añadir() {
                const selectedAlmacenValue = $('#Almacenid').val(); // Obtiene el ID del cliente
                const selectedAlmacenLabel = $('#Almacenid option:selected').text();

                datosArticulo = document.getElementById('productoVenta').value.split('_');
                producto = $("#productoVenta option:selected").text();

                idProducto = datosArticulo[0];

                pCompra = $("#precioCompra").val();
                pVenta = $("#precioVenta").val();
                cantidad = $("#cantidadCompra").val();

                idalmacen = selectedAlmacenValue;
                almacen = selectedAlmacenLabel;

                if (idProducto != "" && cantidad > 0 && pVenta > 0 && idalmacen != "") {
                    subtotal = parseFloat(cantidad * pCompra);
                    total = parseFloat(total + subtotal);
                    $("#total").html("S/ " + total.toFixed(1)); //,pVentaB,pVentaC,pVentaD
                    var fila1 = [idProducto, producto, pVenta, pCompra, cantidad, subtotal.toFixed(2), almacen, idalmacen];
                    ListPedido.push(fila1);

                    limpiar();
                    actualizarTabla()
                    
                    total1 = 0;
                    for (var i = 0; i < ListPedido.length; i++) {
                        total1 = (parseFloat(total1) + parseFloat(ListPedido[i][5])).toFixed(3);
                    }

                    $("#total").html("S/. " + total1);
                    $("#total").val(total1);

                } else {
                    alert("Error, Revise los datos del Producto");
                }
            }

            function actualizarTabla(){
                console.log("ListPedido", ListPedido);
                $("#detalles tbody").html('');

                for (var i = ListPedido.length - 1; i >= 0; i--) {
                    var col1 = '<tr class="selected" id="fila' + i + '">'
                    var col2 = '<td style="text-align: center; width: 20px">' + (i + 1) + '</td>'
                    var col3 =
                        '<td style=width: 20px"><input style="width:80px" type="hidden" name="PRO_Id[]" value="' +
                        ListPedido[i][0] + '">' + ListPedido[i][1] + '</td>'
                    var col4 =
                        '<td style=width: 20px"><input style="width:80px" type="number" ;" name="DEC_PrecioUnitarioV[]" id="precioUnitV' +
                        i + '" value="' + ListPedido[i][2] + '" step="0.01"></td>'
                    var col5 = '<td style=width: 20px"><input style="width:80px" type="number"  name="DEC_PrecioUnitario[]" onkeyup="Valida2(' + i +
                        ');" id="precioUnitC' + i + '" value="' + ListPedido[i][3] + '" step="0.01"></td>'
                    var col6 = '<td style=width: 20px"><input style="width:80px" type="number" onkeyup="Valida2(' + i +
                        ');" name="DEC_Cantidad[]" id="cantidad' + i + '" value="' + ListPedido[i][4] + '"></td>'
                    var col7 = '<td style="text-align: right;"><input style="width:80px" disabled name="subTot' + i +
                        '" id="subTot' + i + '" value="' + ListPedido[i][5] + '"></td>'
                    var col8 = '<td style=width: 20px">' + ListPedido[i][6] + '</td>'
                    var col9 = '<td hidden style=width: 20px"><input style="width:80px" type="text" name="idalmacen[]' + i +
                        '" idtipo="idalmacen' + i + '" value="' + ListPedido[i][7] + '"></td>'
                    var col10 =
                        '<td style="width:80px; text-align: center;"><button   type="button" class="btn btn-danger  "onclick="eliminar(' +
                        i + ');" style="border-radius: 10px;"><i class="fa fa-trash"></button></td></tr>';

                    var fila = col1 + col2 + col3 + col4 + col5 + col6 + col7 + col8 + col9 + col10;
                    console.log("fila", fila);
                    $("#detalles").append(fila);

                }
            }

            function NuevoProveedor() {
                myModal.show()
            }

            function mostrarformulario() {
                // Muestra el formulario debajo de la fila de proveedor
                var formulario = document.getElementById('formProveedorRow');
                formulario.style.display = 'block'; // Muestra el formulario
            }

            function cerrarFormulario() {
                // Oculta el formulario cuando el usuario hace clic en "Cerrar"
                var formulario = document.getElementById('formProveedorRow');
                formulario.style.display = 'none'; // Oculta el formulario
            }

            function Valida2(ind) {
                var cantid = $('#cantidad' + ind).val();
                var pcomp = $('#precioUnitC' + ind).val();

                if (cantid.length >= 0 && pcomp >= 0.00 && cantid.length > 0 && pcomp.length > 0) {
                    imp = (parseFloat(cantid * pcomp)).toFixed(1);

                    ListPedido[ind][3] = pcomp;
                    ListPedido[ind][4] = cantid;
                    subtot = (parseFloat(ListPedido[ind][3] * ListPedido[ind][4])).toFixed(1);
                    ListPedido[ind][5] = subtot;

                    $('#subTot' + ind).val(subtot);

                } else {
                    imp = (0).toFixed(1);
                }

                total1 = 0;

                for (var i = 0; i < ListPedido.length; i++) {
                    total1 = (parseFloat(total1) + parseFloat(ListPedido[i][5])).toFixed(3);
                }
                $("#total").html("S/. " + total1);

            }

            function eliminar(index) {
                var tipocambio = $('#tipocambio').val();
                ListPedido.splice(index, 1);

                actualizarTabla()
                
                total1 = 0;
                for (var i = 0; i < ListPedido.length; i++) {
                    total1 = (parseFloat(total1) + parseFloat(ListPedido[i][5])).toFixed(3);
                }
                $("#total").html("S/. " + total1);
            }

            function limpiar() {
                $("#cantidadCompra").val("");
                $("#precioCompra").val("");
                $("#precioVenta").val("");

                $("#productoVenta").val('');
                $("#productoVenta").change();
            }

            function limitar() {
                var cod = document.getElementById("ProvTDoc").value;

                if (cod == 'DNI') {
                    $("#ProvNDoc").val("");
                    $("#ProvNDoc").attr('maxlength', '8');
                } else if (cod == 'RUC') { // Cambié 'else' por 'else if'
                    $("#ProvNDoc").val("");
                    $("#ProvNDoc").attr('maxlength', '11');
                }
            }


            function BuscarCliente() {
                if ($('#ProvTDoc').val() == 'DNI') {
                    var cod = document.getElementById("ProvTDoc").value;
                    $numero = $("#ProvNDoc").val();
                    if ($numero.length < 8) {
                        Swal
                            .fire({
                                title: "Falta Números en el DNI",
                                icon: 'error',
                                confirmButtonColor: "#26BA9A",
                                confirmButtonText: "Ok"
                            })
                            .then(resultado => {
                                if (resultado.value) {
                                    $("#ProvNDoc").val("");
                                } else {}
                            });
                    } else {
                        $('#Buscar_Cliente').addClass('hide');

                        var numdni = $('#ProvNDoc').val();
                        var url = 'https://www.buqkly.com/api/consultadni/' + numdni + '?';
                        $.ajax({
                            type: 'GET',
                            url: url,
                            //data: { "_token": "{{ csrf_token() }}"},
                            // headers: {  'Access-Control-Allow-Origin': 'https://www.buqkly.com' },
                            success: function(dat) {

                                if (dat.success[1] == false) {
                                    $('#Buscar_Cliente').removeClass('hide');

                                    Swal
                                        .fire({
                                            title: "DNI Inválido",
                                            icon: 'error',
                                            confirmButtonColor: "#26BA9A",
                                            width: '350px',
                                            confirmButtonText: "Ok"
                                        })
                                        .then(resultado => {
                                            if (resultado.value) {
                                                $("#ProvNDoc").val("");
                                            } else {}
                                        });
                                } else {
                                    $('#ProvRSocial').val(dat.success[0]);
                                    $('#Buscar_Cliente').removeClass('hide');
                                }
                            }
                        });
                    }
                } else {
                    var cod = document.getElementById("ProvTDoc").value;
                    $numero = $("#ProvNDoc").val();
                    if ($numero.length < 11) {
                        Swal
                            .fire({
                                title: "Falta Números en el RUC",
                                icon: 'error',
                                confirmButtonColor: "#26BA9A",
                                confirmButtonText: "Ok"
                            })
                            .then(resultado => {
                                if (resultado.value) {
                                    $("#ProvNDoc").val("");
                                } else {

                                }
                            });
                    } else {
                        $('#Buscar_Cliente').addClass('hide');
                        var numdni = $('#ProvNDoc').val();
                        var url = 'https://www.buqkly.com/api/consultaruc/' + numdni + '?';
                        $.ajax({
                            type: 'GET',
                            url: url,
                            //data: { "_token": "{{ csrf_token() }}"},
                            // headers: {  'Access-Control-Allow-Origin': 'https://www.buqkly.com' },
                            success: function(dat) {
                                //console.log(dat);
                                if (dat.success[1] == null) {
                                    $('#Buscar_Cliente').removeClass('hide');
                                    Swal
                                        .fire({
                                            title: "Ruc Inválido",
                                            icon: 'error',
                                            confirmButtonColor: "#26BA9A",
                                            width: '350px',
                                            heigth: '100px',
                                            confirmButtonText: "Ok"
                                        })
                                        .then(resultado => {
                                            if (resultado.value) {
                                                $("#ProvNDoc").val("");
                                            } else {}
                                        });
                                } else {
                                    $('#ProvRSocial').val(dat.success[1]);
                                    $('#ProvDir').val(dat.success[2]);
                                    $('#Buscar_Cliente').removeClass('hide');
                                }
                            }
                        });
                    }
                }
            }

            function GenerarCompra() {
                var formulario = document.getElementById("compra_form");
                $numDocumento = $('#COM_NumDocumento').val();
                $tipoDocumento = $('#COM_TipoDocumento').val();
                $tipoPago = $('#idCOM_TipoPago').val();
                $proveedor = $('#PROV_Id').val();
                $metodoPago = $('#idMEP_Id').val();
                console.log("numDocumento", $numDocumento);
                console.log("tipoDocumento", $tipoDocumento);
                console.log("tipoPago", $tipoPago);
                console.log("proveedor", $proveedor);
                console.log("metodoPago", $metodoPago);
                if($numDocumento == "" || $tipoDocumento == "" || $tipoPago == "" || $proveedor == "" || $metodoPago == "" ){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor complete todos los campos requeridos.'
                    });
                    return; // Detiene la ejecución si falta algún campo
                }

                if(ListPedido.length === 0){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Agregue al menos un producto a la compra.'
                    });
                    return; // Detiene la ejecución si no hay productos en la compra
                }

                $.ajax({
                    data: $('#compra_form').serialize(),
                    url: "{{ tenant_url('tenant.compras.compra.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Swal.fire({
                            icon: "success",
                            title: "Compra Generada",
                            text: "Se realizo la compra correctamente!",
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false, // Deshabilita clics fuera del alert
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            } else if (result.isDenied) {
                                // Swal.fire("Changes are not saved", "", "info");
                            }
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.responseJSON.message || 'Ocurrió un error al generar la compra.'
                        });
                    }
                });
            }

        </script>
    @endsection
