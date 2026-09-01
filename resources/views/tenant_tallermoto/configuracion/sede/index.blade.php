@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Sede')
@section('contenido')

    @can('tenant.configuracion.sede.create')
        <div class="col-12 col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR / EDITAR SEDE</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="almacen_form" action="{{ tenant_url('tenant.configuracion.sede.store') }}">
                        @csrf
                        <input type="text" id="almacen_id_edit" name="almacen_id_edit" hidden>

                        <!-- Datos Principales -->
                        <div class="form-group col-12 p-0">
                            <label class="control-label" style="text-align: left; display: block;">Empresa <span
                                    class="text-danger">*</span>:</label>
                            <select class="form-control select2 select2-info" id="EMP_Id" name="EMP_Id"
                                data-dropdown-css-class="select2-info" style="width: 100%;">
                                <option value="{{ $empresa->id }}">{{ $empresa->ruc }} - {{ $empresa->razon_social }}</option>
                            </select>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Nombre de Sede <span
                                        class="text-danger">*</span>:</label>
                                <input type="text" id="ALM_NombreAlmacen" name="ALM_NombreAlmacen" class="form-control"
                                    placeholder="Nombre de la Sede / Almacún" required>
                            </div>
                        </div>

                        <!-- Dirección y Referencia -->
                        <div class="form-group row">
                            <div class="col-md-7 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Dirección:</label>
                                <input type="text" id="ALM_Direccion" name="ALM_Direccion" class="form-control"
                                    placeholder="Dirección física de la sede">
                            </div>
                            <div class="col-md-5 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Referencia:</label>
                                <input type="text" id="ALM_Referencia" name="ALM_Referencia" class="form-control"
                                    placeholder="Ej. Frente al parque principal">
                            </div>
                        </div>

                        <!-- Ubicación Geográfica / Ubigeo -->
                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Departamento:</label>
                                <input type="text" id="ALM_Departamento" name="ALM_Departamento" class="form-control"
                                    placeholder="Ej. La Libertad">
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Provincia:</label>
                                <input type="text" id="ALM_Provincia" name="ALM_Provincia" class="form-control"
                                    placeholder="Ej. Trujillo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Distrito:</label>
                                <input type="text" id="ALM_Distrito" name="ALM_Distrito" class="form-control"
                                    placeholder="Ej. Trujillo">
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Código Ubigeo:</label>
                                <input type="text" id="ALM_Ubigeo" name="ALM_Ubigeo" class="form-control"
                                    placeholder="Ej. 130101" maxlength="6">
                            </div>
                        </div>

                        <!-- Coordenadas Geográficas (GPS) -->
                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Latitud:</label>
                                <input type="text" id="ALM_Latitud" name="ALM_Latitud" class="form-control"
                                    placeholder="Ej. -8.111677">
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Longitud:</label>
                                <input type="text" id="ALM_Longitud" name="ALM_Longitud" class="form-control"
                                    placeholder="Ej. -79.028600">
                            </div>
                        </div>

                        <!-- Datos de Contacto -->
                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Teléfono:</label>
                                <input type="text" id="ALM_Telefono" name="ALM_Telefono" class="form-control"
                                    placeholder="Teléfono fijo">
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Celular:</label>
                                <input type="number" id="ALM_Celular" name="ALM_Celular" class="form-control"
                                    placeholder="N° Celular">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Correo
                                    Electrónico:</label>
                                <input type="email" id="ALM_Email" name="ALM_Email" class="form-control"
                                    placeholder="correo@ejemplo.com">
                            </div>
                        </div>

                        <!-- Configuración SUNAT y Facturación -->
                        <hr>
                        <h6 class="font-weight-bold text-secondary">Configuración Facturación / SUNAT</h6>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Cód. Establecimiento
                                    Anexo SUNAT:</label>
                                <input type="text" id="ALM_CodigoSunat" name="ALM_CodigoSunat" class="form-control"
                                    placeholder="Ej. 0000 o 0001">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Serie Factura:</label>
                                <input type="text" id="ALM_SerieFactura" name="ALM_SerieFactura" class="form-control"
                                    placeholder="Ej. F001" maxlength="4">
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Serie Boleta:</label>
                                <input type="text" id="ALM_SerieBoleta" name="ALM_SerieBoleta" class="form-control"
                                    placeholder="Ej. B001" maxlength="4">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Serie Nota
                                    Crédito (Boleta):</label>
                                <input type="text" id="ALM_SerieNotaCreditoBoleta" name="ALM_SerieNotaCreditoBoleta"
                                    class="form-control" placeholder="Ej. BC01" maxlength="4">
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Serie Nota
                                    Crédito (Factura):</label>
                                <input type="text" id="ALM_SerieNotaCreditoFactura" name="ALM_SerieNotaCreditoFactura"
                                    class="form-control" placeholder="Ej. FC01" maxlength="4">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <label class="control-label" style="text-align: left; display: block;">Serie Nota
                                    Débito:</label>
                                <input type="text" id="ALM_SerieNotaDebito" name="ALM_SerieNotaDebito"
                                    class="form-control" placeholder="Ej. FD01 / BD01" maxlength="4">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-6">
                                <label class="control-label" style="text-align: left; display: block;">Serie Guía
                                    Remisión:</label>
                                <input type="text" id="ALM_SerieGuiaRemision" name="ALM_SerieGuiaRemision"
                                    class="form-control" placeholder="Ej. T001" maxlength="4">
                            </div>
                            <div class="col-6">
                                <label class="control-label" style="text-align: left; display: block;">Serie Nota
                                    Venta:</label>
                                <input type="text" id="ALM_SerieNotaVenta" name="ALM_SerieNotaVenta" class="form-control"
                                    placeholder="Ej. NV01" maxlength="4">
                            </div>
                        </div>

                        <!-- Parámetros de Operación y Estado -->
                        <hr>
                        <h6 class="font-weight-bold text-secondary">Parámetros Operativos</h6>

                        <div class="form-group row">
                            <div class="col-md-6 col-12">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="ALM_EsPrincipal"
                                        name="ALM_EsPrincipal" value="1">
                                    <label class="custom-control-label" for="ALM_EsPrincipal">¿Almacén Principal?</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="ALM_PermitirVentaSinStock"
                                        name="ALM_PermitirVentaSinStock" value="1">
                                    <label class="custom-control-label" for="ALM_PermitirVentaSinStock">¿Permitir ventas sin
                                        stock?</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Estado:</label>
                                <select class="form-control" id="ALM_Status" name="ALM_Status">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group text-right mt-3">
                            <button id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                            <button id="updateBtn" class="btn btn-info" style="display: none;"><i class="fas fa-save"></i>
                                Actualizar</button>
                            <button type="button" id="btncancelar" class="btn btn-danger"><i class="fas fa-ban"></i>
                                Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('tenant.configuracion.sede.index')
        <div class="col-12 col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE SEDES</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table table-striped table-bordered" id="tabla_almacen">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Empresa</th>
                                    <th scope="col">Sede</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col">Ubigeo</th>
                                    <th scope="col">Contacto</th>
                                    <th scope="col">Est. SUNAT</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                icon: 'info'
            });

            var table = $('#tabla_almacen').DataTable({
                responsive: true,
                autoWidth: false,
                searchDelay: 800,
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                dom: 'Blfrtip',
                buttons: ['copyHtml5', 'excelHtml5', 'pdfHtml5'],
                ajax: "{{ tenant_url('tenant.configuracion.sede.index') }}",
                columns: [{
                        data: 'ALM_Id',
                        name: 'ALM_Id'
                    },
                    {
                        data: 'ALM_Nombre',
                        name: 'ALM_Nombre'
                    },
                    {
                        data: 'ALM_NombreAlmacen',
                        name: 'ALM_NombreAlmacen'
                    },
                    {
                        data: 'ALM_Direccion',
                        name: 'ALM_Direccion'
                    },
                    {
                        data: 'ALM_Ubigeo',
                        name: 'ALM_Ubigeo',
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        name: 'ALM_Celular',
                        render: function(data, type, row) {
                            return (row.ALM_Celular || row.ALM_Telefono || row.ALM_Email) ?
                                (row.ALM_Celular || row.ALM_Telefono || '') + '<br><small>' + (row
                                    .ALM_Email || '') + '</small>' :
                                '-';
                        }
                    },
                    {
                        data: 'ALM_CodigoSunat',
                        name: 'ALM_CodigoSunat',
                        defaultContent: '-'
                    },
                    {
                        data: 'ALM_Status',
                        name: 'ALM_Status',
                        render: function(data) {
                            return data == 1 ?
                                '<span class="badge badge-success">Activo</span>' :
                                '<span class="badge badge-danger">Inactivo</span>';
                        }
                    },
                    {
                        data: null,
                        name: '',
                        render: function(data, type, row) {
                            return data.action1 + ' ' + data.action2;
                        }
                    }
                ]
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                let empresaId = $("#EMP_Id").val();
                let nombreAlmacen = $("#ALM_NombreAlmacen").val();

                if (empresaId == '' || nombreAlmacen == '') {
                    Toast.fire({
                        type: 'error',
                        icon: 'error',
                        title: 'Por favor, complete los campos obligatorios (*).'
                    });
                    return false;
                }

                $.ajax({
                    data: $('#almacen_form').serialize(),
                    url: "{{ tenant_url('tenant.configuracion.sede.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            type: 'success',
                            icon: 'success',
                            title: data.success || 'Sede guardada con éxito.'
                        });
                        cancelarUpdate();
                        table.draw();
                    },
                    error: function(data) {
                        Toast.fire({
                            type: 'error',
                            icon: 'error',
                            title: 'Error al registrar la sede o ya se encuentra registrada.'
                        });
                    }
                });
            });

            $('body').on('click', '.editAlmacen', function() {
                var Almacen_id_edit = $(this).data('identificador');
                $.get('{{ tenant_url('tenant.configuracion.sede.edit', ['sede' => ':sede']) }}'.replace(
                        ':sede', Almacen_id_edit),
                    function(result) {
                        var d = result.data;
                        console.log(d);
                        $('#almacen_id_edit').val(d.ALM_Id);
                        $('#EMP_Id').val(d.EMP_Id).trigger('change');
                        $('#ALM_NombreAlmacen').val(d.ALM_NombreAlmacen);
                        $('#ALM_Direccion').val(d.ALM_Direccion);

                        // Nuevos / Ubicación
                        $('#ALM_Referencia').val(d.ALM_Referencia);
                        $('#ALM_Departamento').val(d.ALM_Departamento);
                        $('#ALM_Provincia').val(d.ALM_Provincia);
                        $('#ALM_Distrito').val(d.ALM_Distrito);
                        $('#ALM_Ubigeo').val(d.ALM_Ubigeo);
                        $('#ALM_Latitud').val(d.ALM_Latitud);
                        $('#ALM_Longitud').val(d.ALM_Longitud);

                        // Contacto
                        $('#ALM_Telefono').val(d.ALM_Telefono);
                        $('#ALM_Celular').val(d.ALM_Celular);
                        $('#ALM_Email').val(d.ALM_Email);

                        // Configuración SUNAT
                        $('#ALM_CodigoSunat').val(d.ALM_CodigoSunat);
                        $('#ALM_SerieFactura').val(d.ALM_SerieFactura);
                        $('#ALM_SerieBoleta').val(d.ALM_SerieBoleta);
                        $('#ALM_SerieNotaCreditoBoleta').val(d.ALM_SerieNotaCreditoBoleta);
                        $('#ALM_SerieNotaCreditoFactura').val(d.ALM_SerieNotaCreditoFactura);
                        $('#ALM_SerieNotaDebito').val(d.ALM_SerieNotaDebito);
                        $('#ALM_SerieGuiaRemision').val(d.ALM_SerieGuiaRemision);
                        $('#ALM_SerieNotaVenta').val(d.ALM_SerieNotaVenta);

                        // Checkboxes y Estado
                        $('#ALM_EsPrincipal').prop('checked', d.ALM_EsPrincipal == 1);
                        $('#ALM_PermitirVentaSinStock').prop('checked', d.ALM_PermitirVentaSinStock ==
                            1);
                        var statusVal = (d.ALM_Status !== null && d.ALM_Status !== undefined) ? Number(d
                            .ALM_Status) : 1;
                        $('#ALM_Status').val(statusVal);

                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    }
                );
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                var Almacen_id_update = $('#almacen_id_edit').val();
                $.ajax({
                    data: $('#almacen_form').serialize(),
                    url: '{{ tenant_url('tenant.configuracion.sede.update', ['sede' => ':sede']) }}'
                        .replace(':sede', Almacen_id_update),
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            type: 'success',
                            icon: 'success',
                            title: data.success || 'Sede actualizada con éxito.'
                        });
                        cancelarUpdate();
                        table.draw();
                    },
                    error: function(data) {
                        Toast.fire({
                            type: 'error',
                            icon: 'error',
                            title: 'Error al actualizar la sede.'
                        });
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

            $('body').on('click', '.deleteAlmacen', function() {
                var Almacen_id_delete = $(this).data("id");
                var confirmacion = confirm("¿Estás seguro de que quieres eliminar esta sede?");
                if (confirmacion) {
                    $.ajax({
                        type: "DELETE",
                        url: '{{ tenant_url('tenant.configuracion.sede.destroy', ['sede' => ':sede']) }}'
                            .replace(':sede', Almacen_id_delete),
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                            Toast.fire({
                                type: 'success',
                                icon: 'success',
                                title: String(data.success)
                            });
                        },
                        error: function(data) {
                            Toast.fire({
                                type: 'error',
                                icon: 'error',
                                title: 'Error al eliminar la sede.'
                            });
                        }
                    });
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'La sede no ha sido eliminada.',
                        icon: 'info'
                    });
                }
            });
        });

        function cancelarUpdate() {
            $('#almacen_form').trigger("reset");
            $("#almacen_id_edit").val('');
            $('#ALM_EsPrincipal').prop('checked', false);
            $('#ALM_PermitirVentaSinStock').prop('checked', false);
            $("#saveBtn").show();
            $("#updateBtn").hide();
            if ($('#EMP_Id').hasClass("select2-hidden-accessible")) {
                $('#EMP_Id').val($('#EMP_Id option:first').val()).trigger('change');
            }
        }
    </script>
@endsection
