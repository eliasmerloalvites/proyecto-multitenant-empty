@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Gastos')
@section('contenido')

    @can('tenant.compras.gasto.create')
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR GASTO</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="gasto_form" action="{{ tenant_url('tenant.compras.gasto.store') }}">
                        @csrf
                        <input type="text" id="gasto_id_edit" hidden>
                        <div class="col-12">
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Fecha
                                    </span>
                                </div>
                                <input class="form-control" id="idGAS_Fecha" name="GAS_Fecha" required placeholder="Fecha" type="date">
                            </div>
                        </div>
    
                        <div class="col-12 mt-3">
                            <div class="input-group ">
                                <span class="input-group-text" style="background: #EDEDED;">
                                    <i class="fa fa-file">
                                    </i>
                                </span>
                                <select class="input-group-addon" id="CTipoDoc" name="TG_Comprobante" required="">
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
                                <input class="form-control input-group-addon" disabled="" id="CNumDoc" maxlength="30" name="TG_ComprobanteNum" placeholder="#N Doc" required="" style="border-bottom-right-radius:10px; border-top-right-radius: 10px;" type="text">
                                </input>
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
                                        <option value="">
                                            Seleccione Proveedor
                                        </option>
                                        @foreach($proveedor as $pro)
                                        <option value="{{$pro->PROV_Id }}">
                                            {{$pro->PROV_RazonSocial }} - {{$pro->PROV_NumDocumento}}
                                        </option>
                                        @endforeach
        
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-top: 1px">
                                <button type="button" class="btn btn-primary" onclick="NuevoProveedor()" title="Agregar Nuevo Proveedor"><i class="fa fa-plus" ></i></button>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <select class="select2 form-control" id="TG_Id" name="TG_Id" required="">
                                <option value="">
                                    Seleccione Tipo Gasto
                                </option>   
                                @foreach($tipo_gasto as $tg)
                                <option value="{{$tg->TG_Id}}">
                                    {{$tg->TG_Descripcion}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    Gasto
                                </span>
                                <select class="form-control" id="idGAS_Afecta" name="GAS_Afecta" required="">
                                    <option value="SI">
                                        Afecta a Caja
                                    </option>
                                    <option value="NO">
                                        No Afecta a Caja
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Descripcion
                                    </span>
                                </div>
                                <input class="form-control" name="GAS_Descripcion" id="idGAS_Descripcion">
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Monto
                                    </span>
                                    <div class="input-group-text">
                                        S/.
                                    </div>
                                </div>
                                <input type="number" class="form-control" id="idGAS_Monto" name="GAS_Monto">
                                </input>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="custom-file center">
                                <input type="file" class="custom-file-input"
                                    accept="image/*"
                                    name="file" id="fileImagen">
                                <label class="custom-file-label" id="idFileImagen">Añadir Imagen</label>
                            </div>
                        </div>

                        <p></p>
                        <div class="form-group text-right">
                            <button id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                            <button id="updateBtn" class="btn btn-info" style="display: none;"><i
                                    class="fas fa-save"></i> Actualizar</button>
                            <button type="reset" id="btncancelar" class="btn btn-danger"> <i
                                    class="fas fa-ban"></i> Cancelar </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('tenant.compras.gasto.index')
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE GASTOS</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;" >
                        <table class="table" id="tabla_gasto">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Descripcion</th>
                                    <th scope="col">Tipo Gasto</th>
                                    <th scope="col">M. Pago</th>
                                    <th scope="col">Monto</th>
                                    <th scope="col">Monto</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan



    <div class="modal fade " id="myModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
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
                                    <input class="form-control input-sm" id="idPROV_RazonSocial" name="PROV_RazonSocial"
                                        onkeyup="this.value=this.value.toUpperCase();" required placeholder="Ingresa Nombre / Razon Social"
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
                                    <input class="form-control input-sm" id="idPROV_Descripcion" name="PROV_Descripcion"
                                        onkeyup="this.value=this.value.toUpperCase();" placeholder="ingresa descripción"
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
                                        onkeyup="this.value=this.value.toUpperCase();" placeholder="ingresa descripción"
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


    <div class="modal fade" id="modalVerGasto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detalle de la Gasto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p ><b>Id Gasto: </b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_GAS_Id"></p>
                                </div>                        
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Fecha Emision:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_GAS_FechaEmision"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Documento:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_GAS_Documento"></p>
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
                                    <p><b>Proveedor:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_Proveedor"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Tipo Gasto:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_TipoGasto"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Gasto afecta acaja:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_Gasto"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Descripcion:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_Descripción"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4" style="text-align: left;" >
                                    <p><b>Monto:</b></p>
                                </div>
                                <div class="col-8" style="text-align: left;" >
                                    <p id="ver_Monto"></p>
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
        var myModal
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.select2').select2()

            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            myModal = new bootstrap.Modal(document.getElementById('myModal'), {
                keyboard: false
            })

            $("#fileImagen").change(function() {
                $nombre = document.getElementById('fileImagen').files[0].name;
                document.querySelector('#idFileImagen').innerText = $nombre;
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                icon: 'info'
            });

            $("#CTipoDoc").change(mostrarDoc);

            var table = $('#tabla_gasto').DataTable({
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
                ajax: "{{ tenant_url('tenant.compras.gasto.index') }}",
                columns: [{
                        data: 'GAS_Id',
                        name: 'GAS_Id',
                        className: 'text-start'
                    },
                    {
                        data: 'GAS_Descripcion',
                        name: 'GAS_Descripcion',
                        className: 'text-start'
                    },
                    {
                        data: 'TG_Descripcion',
                        name: 'TG_Descripcion',
                        className: 'text-start'
                    },
                    {
                        data: 'MEP_Pago',
                        name: 'MEP_Pago',
                        className: 'text-start'
                    },
                    {
                        data: 'GAS_Monto',
                        name: 'GAS_Monto',
                        className: 'text-start'
                    },
                    {
                        data: 'GAS_Fecha',
                        name: 'GAS_Fecha',
                        className: 'text-start'
                    },
                    
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return @can('tenant.compras.gasto.show')
                                    data.action3 + ' ' +
                                @endcan

                            ''+@can('tenant.compras.gasto.edit')
                                    data.action1 + ' ' +
                                @endcan
                            ''
                            @can('tenant.compras.gasto.destroy')
                                +data.action2
                            @endcan ;
                        }
                    }
                ]
            });

            $('#SaveProveedor').click(function(e) {
                e.preventDefault();
                const form = document.getElementById('proveedor_form');
                if (form.checkValidity()) {
                    $.ajax({
                        data: $('#proveedor_form').serialize(),
                        url: "{{ tenant_url('tenant.compras.proveedor.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            console.log('Success:', data);
                            document.getElementById("selectedCliente").textContent = data[0].CLI_Nombre;
                            document.querySelector("#hiddenSelectedIdCliente").value = data[0].CLI_Id;
                            Toast.fire({
                                type: 'success',
                                title: data.success
                            })
                            vaciarCamposProveedor();
                            myModal.hide();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: data.responseText
                            })
                        }
                    });
                } else {
                    form.reportValidity();
                }

            });


            $('#saveBtn').click(function(e) {
                e.preventDefault();             
                const form = document.getElementById('gasto_form');
                if (form.checkValidity()) {
                    let formData = new FormData($('#gasto_form')[0]);
                    $.ajax({
                        url: "{{ tenant_url('tenant.compras.gasto.store') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log('Success:', data);
                            Toast.fire({
                                type: 'success',
                                title: data.success
                            })
                            vaciarCamposGasto();
                            cancelarUpdate();
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Gasto fallo al Registrarse.'
                            })
                        }
                    });
                } else {
                    form.reportValidity();
                }
            });

            $('body').on('click', '.editGasto', function() {
                var Gasto_id_edit = $(this).data('identificador');
                $.get('{{ tenant_url('tenant.compras.gasto.edit', ['gasto' => ':gasto', 'tenant' => tenant('id')]) }}'.replace(':gasto', Gasto_id_edit),
                    function(result) {
                        console.log(result);
                        const fechaString = result.data.GAS_Fecha.split(" ");

                        $('#gasto_id_edit').val(result.data.GAS_Id);
                        $('#idGAS_Fecha').val(fechaString[0]);
                        $('#CTipoDoc').val(result.data.TG_Comprobante);
                        $('#CNumDoc').val(result.data.TG_ComprobanteNum);
                        $('#idMEP_Id').val(result.data.MEP_Id);
                        $('#idMEP_Id').change();
                        $('#PROV_Id').val(result.data.PROV_Id);
                        $('#PROV_Id').change();
                        $('#TG_Id').val(result.data.TG_Id);
                        $('#TG_Id').change();
                        $('#idGAS_Afecta').val(result.data.GAS_Afecta);
                        $('#idGAS_Descripcion').val(result.data.GAS_Descripcion);
                        $('#idGAS_Monto').val(result.data.GAS_Monto);


                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    })
            });

            $('body').on('click', '.eyeGasto', function() {
                var Gasto_id_ver = $(this).data('id');
                $('#modalVerGasto').modal('show');
                $.get('{{ tenant_url('tenant.compras.gasto.show', ['gasto' => ':gasto', 'tenant' => tenant('id')]) }}'.replace(':gasto',
                        Gasto_id_ver),
                    function(data) {
                        console.log(data)
                        $('#ver_GAS_Id').text(data.data.GAS_Id);
                        $('#ver_GAS_FechaEmision').text(data.data.GAS_Fecha);
                        $('#ver_GAS_Documento').text((data.data.TG_Comprobante == "PRO"?"NOTA VENTA":data.data.TG_Comprobante == "BOL"?"BOLETA":"FACTURA") +" "+ data.data.TG_ComprobanteNum );
                        $('#ver_MEP_Pago').text(data.data.MEP_Pago);
                        $('#ver_Proveedor').text(data.data.PROV_RazonSocial);
                        $('#ver_TipoGasto').text(data.data.TG_Descripcion);
                        $('#ver_Gasto').text(data.data.GAS_Afecta);
                        $('#ver_Descripción').text(data.data.GAS_Descripcion);
                        $('#ver_Monto').text("S/ "+data.data.GAS_Monto);

                        data.detalle.forEach(det => {
                            idProducto = det.PRO_Id;
                            producto = det.PRO_Nombre;
                            pGasto = det.precio_venta;
                            cantidad = det.cantidad;
                            subtotal = parseFloat(cantidad * pGasto);
                            var fila1 = [idProducto, producto, pGasto, cantidad, subtotal.toFixed(1), "0"];
                            ListPedido.push(fila1);
                        });

                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Gasto_id_update = $('#gasto_id_edit').val();
                $.ajax({
                    data: $('#gasto_form').serialize(),
                    url: '{{ tenant_url('tenant.compras.gasto.update', ['gasto' => ':gasto', 'tenant' => tenant('id')]) }}'.replace(
                        ':gasto', Gasto_id_update),
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        });
                        cancelarUpdate();
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Gasto fallo al actualizarse.'
                        })
                    }
                });
            });

            $('#btncancelar').click(function(e) {
                cancelarUpdate();
                Swal.fire({
                    icon: 'info',
                    title: 'Registro cancelado',
                    text: 'El formulario se ha reiniciado correctamente.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            });

            function cancelarUpdate() {
                $('#gasto_form').trigger("reset");
                $("#gasto_id_edit").val('');
                $("#saveBtn").show(); // Mostrar botón Guardar
                $("#updateBtn").hide();
            }

            $('body').on('click', '.deleteGasto', function() {

                var Gasto_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ tenant_url('tenant.compras.gasto.destroy', ['gasto' => ':gasto', 'tenant' => tenant('id')]) }}'.replace(
                            ':gasto', Gasto_id_delete),
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                            console.log('success:', data);
                            Toast.fire({
                                type: 'success',
                                title: String(data.success),
                                icon: 'info'
                            });

                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Gasto fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                }else{
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'La gasto no ha sido eliminada.',
                        icon: 'info'
                    });
                 }
            });
        });

        function mostrarDoc() {
            $fil = $('#CTipoDoc').val();
            if ($fil == "") {
                $('#CNumDoc').attr('disabled', true);
                $('#idgenerarventa').attr('disabled', true);
            } else {
                $('#CNumDoc').attr('disabled', false);
                $('#idgenerarventa').attr('disabled', false);

            }

            if ($fil == "NOV") {
                /* $('#CNumDoc').val(numero);
                $('#idgenerarventa').attr('disabled', false); */
            } else {
                $('#CNumDoc').val('');
            }

        }

        function vaciarCamposProveedor() {
            $('#proveedor_form').trigger("reset");
            $('#idPROV_TipoDocumento').val('DNI');
            $('#idPROV_NumDocumento').val('');
            $('#idPROV_RazonSocial').val('');
            $('#idPROV_Direccion').val('');
            $('#idPROV_Descripcion').val('');
            $('#idPROV_Celular').val('');
            $('#idPROV_Correo').val('');
        }

        function vaciarCamposGasto() {
            $('#gasto_form').trigger("reset");
            $('#PROV_Id').val('');
            $('#PROV_Id').change(); 
            $('#TG_Id').val('');
            $('#TG_Id').change();
            $('#MEP_Id').val('');
            $('#MEP_Id').change();
            $('#fileImagen').val("")
            document.querySelector('#idFileImagen').innerText = "Añadir Imagen";
            $("#saveBtn").show();
        }

        function NuevoProveedor(){            
            myModal.show()
        }

        function Limitar() {
            var cod = document.getElementById("idPROV_TipoDocumento").value;
            if (cod == 'DNI') {
                $("#idPROV_NumDocumento").val("");
                $("#idPROV_NumDocumento").attr('maxlength', '8');
            } else {
                $("#idPROV_NumDocumento").val("");
                $("#idPROV_NumDocumento").attr('maxlength', '11');
            }
        }

        function buscarProveedor() {
            if ($('#idPROV_TipoDocumento').val() == 'DNI') {
                var numdni = $('#idPROV_NumDocumento').val();
                if (numdni != '') {
                    ocultar()
                    var url = '/consultardni/' + numdni + '?';
                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(dat) {
                            if (dat.success[1] == false) {
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
                                            $("#idPROV_RazonSocial").val("");
                                        } else {}
                                    });
                            } else {
                                $('#idPROV_RazonSocial').val(dat.success[0].apellido + ' ' + dat.success[0].nombre);
                            }
                        }

                    });
                } else {
                    //mostrar()
                    alert('Escriba el DNI.!');
                    $('#idPROV_NumDocumento').focus();
                }
            } else if ($('#idPROV_TipoDocumento').val() == 'RUC') {
                var numdni = $('#idPROV_NumDocumento').val();
                if (numdni != '') {
                    ocultar()
                    var url = '/consultarruc/' + numdni + '?';
                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(dat) {
                            console.log(dat)
                            if (dat.success[0] == "") {
                                $('#idPROV_RazonSocial').val(dat.success[1]);
                                $('#idPROV_Direccion').val(dat.success[2] + ' ' + dat.success[3]);
                            } else {
                                $('#idPROV_RazonSocial').val(dat.success[0].apellido + ' ' + dat.success[0].nombre);
                            }

                        }

                    });
                } else {
                    alert('Escriba el RUC.!');
                    $('#idPROV_NumDocumento').focus();
                }
            }
        }

        function ocultar() {
            document.getElementById('Buscar_Proveedor').style.display = 'none';
            document.getElementById('cargando').style.display = 'block';
            setInterval('mostrar()', 1000);
        }

        function mostrar() {
            $valorcito = $('#idPROV_RazonSocial').val();
            $valor = $valorcito.length;
            if ($valor > 0) {
                document.getElementById('Buscar_Proveedor').style.display = 'block';
                document.getElementById('cargando').style.display = 'none';
            }
        }

    </script>
@endsection
