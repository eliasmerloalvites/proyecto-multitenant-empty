@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Clientes')
@section('contenido')

    <head>
        <!-- Ruta para incluir el archivo CSS -->
        <link href="{{ asset('css/stylemodal.css') }}" rel="stylesheet">

    </head>

    @can('tenant.ventas.cliente.create')
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR CLIENTE</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="cliente_form" action="{{ tenant_url('tenant.ventas.cliente.store') }}">
                        @csrf
                        <input type="text" id="cliente_id_edit" hidden>
                        <div class="form-group row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label" style="text-align: left; display: block;">Tipo
                                    Documento:</label>
                                <select class="form-control" id="idCLI_TipoDocumento" name="CLI_TipoDocumento"
                                    onchange="Limitar()" required>
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CE">CE</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: left;">
                                <label for="name" class=" control-label">Numero Documento</label>
                                <div class="input-group ">
                                    <input type="text" class="form-control sm" id="idCLI_NumDocumento"
                                        name="CLI_NumDocumento" placeholder="Ingrese Nº Documento" maxlength="8" required>
                                    <div class="input-group-append">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="Buscar_Cliente" style="display: block;"
                                                onclick="buscarCliente()"><i class="fas fa-search"></i></span>
                                            <span class="input-group-text hide" id="cargando" style="display: none;"><img
                                                    width="15px" src="{{ asset_root('images/gif/cargando1.gif') }}"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label" style="text-align: left; display: block;">Nombre:</label>
                                <input type="text" id="idCLI_Nombre" name="CLI_Nombre" class="form-control "
                                    placeholder="Ingrese Nombre" required>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label" style="text-align: left; display: block;">Dirección:</label>
                                <input type="text" id="idCLI_Direccion" name="CLI_Direccion" class="form-control "
                                    placeholder="Ingrese Dirección">
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label" style="text-align: left; display: block;">Celular:</label>
                                <input type="number" id="idCLI_Celular" name="CLI_Celular" class="form-control "
                                    placeholder="Ingrese Celular">
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label" style="text-align: left; display: block;">Correo:</label>
                                <input type="email" id="idCLI_Correo" name="CLI_Correo" class="form-control "
                                    placeholder="Ingrese correo">
                            </div>
                        </div>
                        <p></p>
                        <div class="form-group text-right">
                            <button id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                            <button id="updateBtn" class="btn btn-info" style="display: none;"><i class="fas fa-save"></i>
                                Actualizar</button>
                            <button type="reset" id="btncancelar" class="btn btn-danger"> <i class="fas fa-ban"></i> Cancelar
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('tenant.ventas.cliente.index')
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE CLIENTES</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="tabla_cliente">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">N° Doc</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Celular</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan




    <!-- Modal Ver detalles-->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">
                            <i class="fas fa-user-circle me-2"></i>
                            Detalles del Cliente
                        </h5>
                        <small class="opacity-75">
                            Información general del cliente registrado
                        </small>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body bg-light">

                    <!-- CARD CLIENTE -->
                    <div class="card border-0 shadow-sm rounded-4">

                        <div class="card-body">

                            <!-- ID -->
                            <div class="d-flex justify-content-between align-items-center mb-4">

                                <div>
                                    <small class="text-muted d-block">
                                        ID CLIENTE
                                    </small>

                                    <h4 class="fw-bold text-primary mb-0" id="ver_CLI_Id">
                                    </h4>
                                </div>

                                <div>
                                    <span class="badge bg-success px-3 py-2">
                                        ACTIVO
                                    </span>
                                </div>

                            </div>

                            <!-- GRID -->
                            <div class="row g-4">

                                <!-- TIPO DOCUMENTO -->
                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Tipo Documento
                                    </small>

                                    <div class="fw-semibold fs-6" id="ver_CLI_TipoDocumento">
                                    </div>
                                </div>

                                <!-- NUMERO DOCUMENTO -->
                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Número Documento
                                    </small>

                                    <div class="fw-semibold fs-6" id="ver_CLI_NumDocumento">
                                    </div>
                                </div>

                                <!-- NOMBRE -->
                                <div class="col-md-12">
                                    <small class="text-muted d-block">
                                        Nombre Completo
                                    </small>

                                    <div class="fw-bold fs-5 text-dark" id="ver_CLI_Nombre">
                                    </div>
                                </div>

                                <!-- DIRECCION -->
                                <div class="col-md-12">
                                    <small class="text-muted d-block">
                                        Dirección
                                    </small>

                                    <div class="fw-semibold" id="ver_CLI_Direccion">
                                    </div>
                                </div>

                                <!-- CELULAR -->
                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Celular
                                    </small>

                                    <div class="fw-semibold" id="ver_CLI_Celular">
                                    </div>
                                </div>

                                <!-- CORREO -->
                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Correo Electrónico
                                    </small>

                                    <div class="fw-semibold text-primary" id="ver_CLI_Correo">
                                    </div>
                                </div>

                            </div>

                            <!-- FECHAS -->
                            <hr class="my-4">

                            <div class="row">

                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Fecha Registro
                                    </small>

                                    <div class="fw-semibold" id="ver_fecha_registro">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Última Actualización
                                    </small>

                                    <div class="fw-semibold" id="ver_fecha_update">
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white border-0">

                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal">

                        <i class="fas fa-times me-1"></i>
                        Cerrar

                    </button>

                </div>

            </div>
        </div>
    </div>
    </div>

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

            var table = $('#tabla_cliente').DataTable({
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
                ajax: "{{ tenant_url('tenant.ventas.cliente.index') }}",
                columns: [{
                        data: 'CLI_Id',
                        name: 'CLI_Id'
                    },
                    {
                        data: 'CLI_NumDocumento',
                        name: 'CLI_NumDocumento'
                    },
                    {
                        data: 'CLI_Nombre',
                        name: 'CLI_Nombre'
                    },
                    {
                        data: 'CLI_Celular',
                        name: 'CLI_Celular'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return @can('tenant.ventas.cliente.show')
                                    data.action3 + ' ' +
                                @endcan
                            ''
                            @can('tenant.ventas.cliente.edit')
                                +data.action1 + ' ' +
                            @endcan
                            ''
                            @can('tenant.ventas.cliente.destroy')
                                +data.action2
                            @endcan ;
                        }
                    }
                ]
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                const form = document.getElementById('cliente_form');
                if (form.checkValidity()) {
                    $.ajax({
                        data: $('#cliente_form').serialize(),
                        url: "{{ tenant_url('tenant.ventas.cliente.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            console.log('Success:', data);
                            Toast.fire({
                                type: 'success',
                                title: data.success
                            })
                            cancelarUpdate();
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'cliente fallo al Registrarse.'
                            })
                        }
                    });
                } else {
                    form.reportValidity();
                }

            });

            $('body').on('click', '.editCliente', function() {
                var Cliente_id_edit = $(this).data('identificador');
                $.get('{{ tenant_url('tenant.ventas.cliente.edit', ['cliente' => ':cliente']) }}'.replace(
                        ':cliente',
                        Cliente_id_edit),
                    function(result) {
                        console.log(result);
                        $('#cliente_id_edit').val(result.data.CLI_Id);
                        $('#idCLI_TipoDocumento').val(result.data.CLI_TipoDocumento);
                        $('#idCLI_NumDocumento').val(result.data.CLI_NumDocumento);
                        $('#idCLI_Nombre').val(result.data.CLI_Nombre);
                        $('#idCLI_Direccion').val(result.data.CLI_Direccion);
                        $('#idCLI_Celular').val(result.data.CLI_Celular);
                        $('#idCLI_Correo').val(result.data.CLI_Correo);


                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                const form = document.getElementById('cliente_form');
                if (form.checkValidity()) {
                    Cliente_id_update = $('#cliente_id_edit').val();
                    $.ajax({
                        data: $('#cliente_form').serialize(),
                        url: '{{ tenant_url('tenant.ventas.cliente.update', ['cliente' => ':cliente']) }}'
                            .replace(
                                ':cliente', Cliente_id_update),
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
                                title: 'Cliente fallo al actualizarse.'
                            })
                        }
                    });
                } else {
                    form.reportValidity();
                }
            });

            $('body').on('click', '.eyeCliente', function() {
                var Cliente_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.ventas.cliente.show', ['cliente' => ':cliente']) }}'.replace(
                        ':cliente',
                        Cliente_id_ver),
                    function(data) {
                        $('#ver_CLI_Id').text(data.data.CLI_Id);
                        $('#ver_CLI_TipoDocumento').text(data.data.CLI_TipoDocumento);
                        $('#ver_CLI_NumDocumento').text(data.data.CLI_NumDocumento);
                        $('#ver_CLI_Nombre').text(data.data.CLI_Nombre);
                        $('#ver_CLI_Direccion').text(data.data.CLI_Direccion);
                        $('#ver_CLI_Celular').text(data.data.CLI_Celular);
                        $('#ver_CLI_Correo').text(data.data.CLI_Correo);
                        $('#ver_fecha_registro').text(moment(data.data.created_at).format(
                            'YYYY-MM-DD HH:mm:ss'));
                        $('#ver_fecha_update').text(moment(data.data.updated_at).format(
                            'YYYY-MM-DD HH:mm:ss'));
                    })
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

            $('body').on('click', '.deleteCliente', function() {

                var Cliente_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ tenant_url('tenant.ventas.cliente.destroy', ['cliente' => ':cliente']) }}'
                            .replace(
                                ':cliente', Cliente_id_delete),
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
                                title: 'Cliente fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'La cliente no ha sido eliminada.',
                        icon: 'info'
                    });
                }
            });
        });

        function cancelarUpdate() {
            $('#cliente_form').trigger("reset");
            $("#cliente_id_edit").val('');
            $("#saveBtn").show(); // Mostrar botón Guardar
            $("#updateBtn").hide();
        }

        function Limitar() {
            var cod = document.getElementById("idCLI_TipoDocumento").value;
            if (cod == 'DNI') {
                $("#idCLI_NumDocumento").val("");
                $("#idCLI_NumDocumento").attr('maxlength', '8');
            } else {
                $("#idCLI_NumDocumento").val("");
                $("#idCLI_NumDocumento").attr('maxlength', '11');
            }
        }

        function buscarCliente() {
            if ($('#idCLI_TipoDocumento').val() == 'DNI') {
                var numdni = $('#idCLI_NumDocumento').val();
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
                                            $("#idCLI_Nombre").val("");
                                        } else {}
                                    });
                            } else {
                                $('#idCLI_Nombre').val(dat.success[0].apellido + ' ' + dat.success[0].nombre);
                            }
                        }

                    });
                } else {
                    //mostrar()
                    alert('Escriba el DNI.!');
                    $('#idCLI_NumDocumento').focus();
                }
            } else if ($('#idCLI_TipoDocumento').val() == 'RUC') {
                var numdni = $('#idCLI_NumDocumento').val();
                if (numdni != '') {
                    ocultar()
                    var url = '/consultarruc/' + numdni + '?';
                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(dat) {
                            console.log(dat)
                            if (dat.success[0] == "") {
                                $('#idCLI_Nombre').val(dat.success[1]);
                                $('#idCLI_Direccion').val(dat.success[2] + ' ' + dat.success[3]);
                            } else {
                                $('#idCLI_Nombre').val(dat.success[0].apellido + ' ' + dat.success[0].nombre);
                            }

                        }

                    });
                } else {
                    alert('Escriba el RUC.!');
                    $('#idCLI_NumDocumento').focus();
                }
            }
        }

        function ocultar() {
            document.getElementById('Buscar_Cliente').style.display = 'none';
            document.getElementById('cargando').style.display = 'block';
            setInterval('mostrar()', 1000);
        }

        function mostrar() {
            $valorcito = $('#idCLI_Nombre').val();
            $valor = $valorcito.length;
            if ($valor > 0) {
                document.getElementById('Buscar_Cliente').style.display = 'block';
                document.getElementById('cargando').style.display = 'none';
            }
        }
    </script>
@endsection
