@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Editar Compra')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">EDITAR COMPRA #{{ $data->COM_Id }}</h5>

                <form id="compra_edit_form">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Tipo de documento:</label>
                                <select id="COM_TipoDocumento" name="COM_TipoDocumento" class="form-control" required>
                                    <option value="Boleta" {{ $data->COM_TipoDocumento === 'Boleta' ? 'selected' : '' }}>Boleta</option>
                                    <option value="Factura" {{ $data->COM_TipoDocumento === 'Factura' ? 'selected' : '' }}>Factura</option>
                                    <option value="Nota_Venta" {{ $data->COM_TipoDocumento === 'Nota_Venta' ? 'selected' : '' }}>Nota Venta</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>N° Documento:</label>
                                <input type="text" id="COM_NumDocumento" name="COM_NumDocumento" class="form-control"
                                    value="{{ $data->COM_NumDocumento }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Tipo de pago:</label>
                                <select id="COM_TipoPago" name="COM_TipoPago" class="form-control" required>
                                    <option value="Contado" {{ $data->COM_TipoPago === 'Contado' ? 'selected' : '' }}>Contado</option>
                                    <option value="Credito" {{ $data->COM_TipoPago === 'Credito' ? 'selected' : '' }}>Crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Método de pago:</label>
                                <select id="MEP_Id" name="MEP_Id" class="form-control select2" required>
                                    <option value="">Seleccione método</option>
                                    @foreach ($metodo_pago as $mep)
                                        <option value="{{ $mep->MEP_Id }}" {{ (int) $data->MEP_Id === $mep->MEP_Id ? 'selected' : '' }}>{{ $mep->MEP_Pago }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-11">
                            <div class="form-group">
                                <label>Proveedor:</label>
                                <select id="PROV_Id" name="PROV_Id" class="form-control select2" required>
                                    <option value="">Seleccione proveedor</option>
                                    @foreach ($proveedor as $itemproveedor)
                                        <option value="{{ $itemproveedor->PROV_Id }}" {{ (int) $data->PROV_Id === $itemproveedor->PROV_Id ? 'selected' : '' }}>
                                            {{ $itemproveedor->PROV_TipoDocumento }} -
                                            {{ $itemproveedor->PROV_NumDocumento }} -
                                            {{ $itemproveedor->PROV_RazonSocial }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-1 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button title="Nuevo Proveedor" type="button" id="btnNuevoProveedor"
                                    class="btn btn-primary" onclick="NuevoProveedor()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ tenant_url('tenant.compras.compra.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="button" onclick="GuardarCambiosCompra();" id="saveBtn" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-box mr-1"></i> Detalle de productos
                </h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    Los productos y cantidades de una compra ya registrada no se pueden editar aquí, porque ya generaron
                    movimiento de stock (parte puede estar vendida). Si necesitas corregir el stock, usa
                    <a href="{{ tenant_url('tenant.inventario.ajuste.index') }}">Ajustes de Inventario</a>.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Almacén</th>
                                <th>P. Compra</th>
                                <th>P. Venta</th>
                                <th>Cantidad</th>
                                <th>Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->detalle_compra as $det)
                                <tr>
                                    <td>{{ $det->producto->categoria->CAT_Nombre ?? '' }} - {{ $det->producto->PRO_Nombre ?? ('Producto #' . $det->PRO_Id) }}</td>
                                    <td>{{ $det->almacen->ALM_NombreAlmacen ?? '' }}</td>
                                    <td>S/ {{ number_format($det->DCOM_PrecioCompra, 2) }}</td>
                                    <td>S/ {{ number_format($det->DCOM_PrecioVenta, 2) }}</td>
                                    <td>{{ $det->DCOM_Cantidad }}</td>
                                    <td>S/ {{ number_format($det->DCOM_Cantidad * $det->DCOM_PrecioCompra, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
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
                                        <span class="input-group-text">TIPO DOCUMENTO</span>
                                    </div>
                                    <select class="form-control" onChange="Limitar()" id="idPROV_TipoDocumento"
                                        name="PROV_TipoDocumento"
                                        style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;">
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                        <option value="CE">Carnet Extrangeria</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Nº Doc</span>
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
                                        <span class="input-group-text">Razon social</span>
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
                                        <span class="input-group-text">Dirección</span>
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
                                        <span class="input-group-text">Descripción</span>
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
                                        <span class="input-group-text">Celular</span>
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
                                        <span class="input-group-text">Correo</span>
                                    </div>
                                    <input class="form-control input-sm" id="idPROV_Correo" name="PROV_Correo"
                                        onkeyup="this.value=this.value.toUpperCase();"
                                        placeholder="ingresa correo"
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

        $(document).ready(function() {
            $('.select2').select2();
            myModal = new bootstrap.Modal(document.getElementById('myModal'), { keyboard: false });
        });

        function NuevoProveedor() {
            myModal.show();
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
                    ocultarBuscarProveedor()
                    var url = '/consultardni/' + numdni + '?';
                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(dat) {
                            if (dat.success[1] == false) {
                                Swal.fire({
                                    title: "DNI Inválido",
                                    icon: 'error',
                                    confirmButtonColor: "#26BA9A",
                                    width: '350px',
                                    confirmButtonText: "Ok"
                                }).then(resultado => {
                                    if (resultado.value) $("#idPROV_RazonSocial").val("");
                                });
                            } else {
                                $('#idPROV_RazonSocial').val(dat.success[0].apellido + ' ' + dat.success[0].nombre);
                            }
                        },
                        complete: function() {
                            mostrarBuscarProveedor();
                        }
                    });
                } else {
                    alert('Escriba el DNI.!');
                    $('#idPROV_NumDocumento').focus();
                }
            } else if ($('#idPROV_TipoDocumento').val() == 'RUC') {
                var numdni = $('#idPROV_NumDocumento').val();
                if (numdni != '') {
                    ocultarBuscarProveedor()
                    var url = '/consultarruc/' + numdni + '?';
                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(dat) {
                            if (dat.success == false) {
                                Swal.fire({
                                    title: dat.message || "RUC Inválido",
                                    icon: 'error',
                                    confirmButtonColor: "#26BA9A",
                                    width: '350px',
                                    confirmButtonText: "Ok"
                                });
                            } else {
                                $('#idPROV_RazonSocial').val(dat.data.nombre);
                                $('#idPROV_Direccion').val(dat.data.direccion + ' ' + dat.data.ubicacion);
                            }
                        },
                        complete: function() {
                            mostrarBuscarProveedor();
                        }
                    });
                } else {
                    alert('Escriba el RUC.!');
                    $('#idPROV_NumDocumento').focus();
                }
            }
        }

        function ocultarBuscarProveedor() {
            document.getElementById('Buscar_Proveedor').style.display = 'none';
            document.getElementById('cargando').style.display = 'block';
        }

        function mostrarBuscarProveedor() {
            document.getElementById('Buscar_Proveedor').style.display = 'block';
            document.getElementById('cargando').style.display = 'none';
        }

        function vaciarCamposProveedor() {
            $('#proveedor_form').trigger("reset");
            $('#idPROV_TipoDocumento').val('DNI');
            $('#idPROV_NumDocumento').val('').attr('maxlength', '8');
            $('#idPROV_RazonSocial').val('');
            $('#idPROV_Direccion').val('');
            $('#idPROV_Descripcion').val('');
            $('#idPROV_Celular').val('');
            $('#idPROV_Correo').val('');
        }

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
                        var nuevaOpcion = new Option(
                            data.Proveedor.PROV_TipoDocumento + ' - ' + data.Proveedor.PROV_NumDocumento + ' - ' + data.Proveedor.PROV_RazonSocial,
                            data.Proveedor.PROV_Id,
                            true,
                            true
                        );
                        $('#PROV_Id').append(nuevaOpcion).trigger('change');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'success',
                            title: data.success
                        });
                        vaciarCamposProveedor();
                        myModal.hide();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'error',
                            title: xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo registrar el proveedor.'
                        });
                    }
                });
            } else {
                form.reportValidity();
            }
        });

        function GuardarCambiosCompra() {
            var formulario = document.getElementById("compra_edit_form");
            if (!formulario.reportValidity()) {
                return;
            }

            $('#saveBtn').prop('disabled', true);

            $.ajax({
                data: $('#compra_edit_form').serialize(),
                url: "{{ tenant_url('tenant.compras.compra.update', ['compra' => $data->COM_Id]) }}",
                type: "PUT",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                dataType: 'json',
                success: function(data) {
                    Swal.fire({
                        icon: "success",
                        title: "Compra actualizada",
                        text: "Los cambios se guardaron correctamente.",
                        confirmButtonText: "Aceptar"
                    }).then(function() {
                        window.location.href = "{{ tenant_url('tenant.compras.compra.index') }}";
                    });
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && (xhr.responseJSON.error || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors).flat().join(' ')))) || 'No se pudo actualizar la compra.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false);
                }
            });
        }
    </script>
@endsection
