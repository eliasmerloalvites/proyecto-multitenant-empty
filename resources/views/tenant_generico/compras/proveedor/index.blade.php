@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Proveedores')
@section('contenido')
<head>
    <!-- Ruta para incluir el archivo CSS -->
<link href="{{ asset('css/stylemodal.css') }}" rel="stylesheet">

</head>
    @can('tenant.compras.proveedor.create')
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR PROVEEDOR</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="proveedor_form" action="{{ tenant_url('tenant.compras.proveedor.store') }}">
                        @csrf
                        <input type="text" id="proveedor_id_edit" hidden>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Tipo de
                                    documento:</label>
                                <select id="PROV_TipoDocumento" name="PROV_TipoDocumento" class="form-control "
                                    onchange="Limitar()" required>

                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Carnet de extranjería">Carnet de extranjería</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Número de
                                    documento:</label>
                                <div class="input-group ">
                                    <input type="number" class="form-control sm" id="PROV_NumDocumento"
                                        name="PROV_NumDocumento" placeholder="Ingrese Nº Documento" maxlength="8"
                                        required>
                                    <div class="input-group-append">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="Buscar_Cliente" style="display: block;"
                                                onclick="buscarCliente()"><i class="fas fa-search"></i></span>
                                            <span class="input-group-text hide" id="cargando"
                                                style="display: none;"><img width="15px"
                                                    src="{{ asset_root('images/gif/cargando1.gif') }}"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Razón
                                    social:</label>
                                <input type="text" id="PROV_RazonSocial" name="PROV_RazonSocial"
                                    class="form-control " placeholder="Razón social" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label"
                                    style=" text-align: left; display: block;">Dirección:</label>
                                <input type="text" id="PROV_Direccion" name="PROV_Direccion" class="form-control "
                                    placeholder="Dirección">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label"
                                    style=" text-align: left; display: block;">Descripción:</label>
                                <input type="text" id="PROV_Descripcion" name="PROV_Descripcion"
                                    class="form-control " placeholder="Descripción">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Celular:</label>
                                <input type="number" id="PROV_Celular" name="PROV_Celular" class="form-control "
                                    placeholder="Celular">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">E-mail:</label>
                                <input type="email" id="PROV_Correo" name="PROV_Correo" class="form-control "
                                    placeholder="Email">
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

    @can('tenant.compras.proveedor.create')
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE PROVEEDORES</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="tabla_proveedor">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">documento</th>
                                    <th scope="col">N° documento</th>
                                    <th scope="col">Razón social</th>
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
    
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detalles del Proveedor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <p class="col">Id Proveedor: </p>
                        <p id="ver_PROV_Id" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Tipo Documento:</p>
                        <p id="ver_PROV_TipoDocumento" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Numero Documento:</p>
                        <p id="ver_PROV_NumDocumento" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Nombre: </p>
                        <p id="ver_PROV_RazonSocial" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Dirección: </p>
                        <p id="ver_PROV_Direccion" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Celular: </p>
                        <p id="ver_PROV_Celular" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Correo: </p>
                        <p id="ver_PROV_Correo" class="col"></p>
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

            var table = $('#tabla_proveedor').DataTable({
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
                ajax: "{{ tenant_url('tenant.compras.proveedor.index') }}",
                columns: [{
                        data: 'PROV_Id',
                        name: 'PROV_Id'
                    },
                    {
                        data: 'PROV_TipoDocumento',
                        name: 'PROV_TipoDocumento'
                    },
                    {
                        data: 'PROV_NumDocumento',
                        name: 'PROV_NumDocumento'
                    },
                    {
                        data: 'PROV_RazonSocial',
                        name: 'PROV_RazonSocial'
                    },
                    {
                        data: 'PROV_Celular',
                        name: 'PROV_Celular'
                    },

                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return data.action3 + ' ' + data.action1 + ' ' + data.action2;
                        }
                    }
                ]
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                tipoDoc = $("#PROV_TipoDocumento").val();
                numDoc = $("#PROV_NumDocumento").val();
                razSoci = $("#PROV_RazonSocial").val();

                if (tipoDoc == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Seleccione el Tipo de documento por favor'
                    });
                    return false;
                } else if (numDoc == '' || numDoc.length > 12) {
                    Toast.fire({
                        type: 'error',
                        title: 'Digite correctamente el número de documento por favor'
                    });
                    return false;
                } else if (razSoci == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Digite la razón social por favor'
                    });
                    return false;
                }
                $.ajax({
                    data: $('#proveedor_form').serialize(),
                    url: "{{ tenant_url('tenant.compras.proveedor.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })

                        $('#proveedor_form').trigger("reset");
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Proveedor fallo al Registrarse.'
                        })
                    }
                });
            });

            $('body').on('click', '.editProveedor', function() {
                var Proveedor_id_edit = $(this).data('identificador');
                $.get('{{ tenant_url('tenant.compras.proveedor.edit', ['proveedor' => ':proveedor']) }}'.replace(':proveedor',
                        Proveedor_id_edit),
                    function(result) {
                        console.log(result);
                        $('#proveedor_id_edit').val(result.data.PROV_Id);
                        $('#PROV_TipoDocumento').val(result.data.PROV_TipoDocumento).prop('disabled',
                            true);
                        $('#PROV_NumDocumento').val(result.data.PROV_NumDocumento).prop('disabled',
                            true);
                        $('#PROV_RazonSocial').val(result.data.PROV_RazonSocial).prop('disabled', true);
                        $('#PROV_Direccion').val(result.data.PROV_Direccion);
                        $('#PROV_Descripcion').val(result.data.PROV_Descripcion);
                        $('#PROV_Celular').val(result.data.PROV_Celular);
                        $('#PROV_Correo').val(result.data.PROV_Correo);

                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    })
            });

            $('body').on('click', '.eyeProveedor', function() {
                var Proveedor_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.compras.proveedor.show', ['proveedor' => ':proveedor']) }}'.replace(':proveedor',
                        Proveedor_id_ver),
                    function(data) {
                        $('#ver_PROV_Id').text(data.data.PROV_Id);
                        $('#ver_PROV_TipoDocumento').text(data.data.PROV_TipoDocumento);
                        $('#ver_PROV_NumDocumento').text(data.data.PROV_NumDocumento);
                        $('#ver_PROV_RazonSocial').text(data.data.PROV_RazonSocial);
                        $('#ver_PROV_Direccion').text(data.data.PROV_Direccion);
                        $('#ver_PROV_Celular').text(data.data.PROV_Celular);
                        $('#ver_PROV_Correo').text(data.data.PROV_Correo);
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Proveedor_id_update = $('#proveedor_id_edit').val();
                $.ajax({
                    data: $('#proveedor_form').serialize(),
                    url: '{{ tenant_url('tenant.compras.proveedor.update', ['proveedor' => ':proveedor']) }}'.replace(':proveedor',
                        Proveedor_id_update),
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
                            title: 'Proveedor fallo al actualizarse.'
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

            $('body').on('click', '.deleteProveedor', function() {

                var Proveedor_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ tenant_url('tenant.compras.proveedor.destroy', ['proveedor' => ':proveedor']) }}'.replace(
                            ':proveedor', Proveedor_id_delete),
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
                                title: 'Proveedor fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'Proveedor no ha sido eliminado.',
                        icon: 'info'
                    });
                }
            });
        });

        function cancelarUpdate() {
            $('#proveedor_form').trigger("reset");
            $("#proveedor_id_edit").val('');
            $("#saveBtn").show(); // Mostrar botón Guardar
            $("#updateBtn").hide();
            $('#PROV_TipoDocumento').prop('disabled', false); // Habilitar Tipo
            $('#PROV_NumDocumento').prop('disabled', false); // Habilitar N° doc
            $('#PROV_RazonSocial').prop('disabled', false); // Habilitar Nombre
        }

        function Limitar() {
            var cod = document.getElementById("PROV_TipoDocumento").value;
            if (cod == 'DNI') {
                $("#PROV_NumDocumento").val("");
                $("#PROV_NumDocumento").attr('maxlength', '8');
            } else if (cod == 'RUC') {
                $("#PROV_NumDocumento").val("");
                $("#PROV_NumDocumento").attr('maxlength', '11');
            }
        }

        function buscarCliente() {
            if ($('#PROV_TipoDocumento').val() == 'DNI') {
                var numdni = $('#PROV_NumDocumento').val();
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
                                            $("#PROV_RazonSocial").val("");
                                        } else {}
                                    });
                                $('#proveedor_form').trigger("reset");
                                $('#cargando').hide();
                                $('#Buscar_Cliente').show();
                                table.draw();
                            } else {
                                $('#PROV_RazonSocial').val(dat.success[0].apellido + ' ' + dat.success[0]
                                    .nombre);
                            }
                        }

                    });
                } else {
                    //mostrar()
                    alert('Escriba el DNI.!');
                    $('#PROV_NumDocumento').focus();
                }
            } else if ($('#PROV_TipoDocumento').val() == 'RUC') {
                var numdni = $('#PROV_NumDocumento').val();
                if (numdni != '') {
                    ocultar()
                    var url = '/consultarruc/' + numdni + '?';
                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(dat) {
                            console.log(dat)
                            if (dat.success === false) {

                                Swal
                                    .fire({
                                        title: "RUC Inválido",
                                        text: dat.message,
                                        icon: 'error',
                                        confirmButtonColor: "#26BA9A",
                                        width: '350px',
                                        confirmButtonText: "Ok"
                                    })
                                    .then(resultado => {
                                        if (resultado.value) {
                                            $("#PROV_RazonSocial").val("");
                                        }
                                    });
                                $('#proveedor_form').trigger("reset");
                                $('#cargando').hide();
                                $('#Buscar_Cliente').show();
                                table.draw();
                            } else {
                                $('#PROV_RazonSocial').val(dat.data.nombre);
                            }
                        },
                    });
                } else {
                    alert('Escriba el RUC.!');
                    $('#PROV_NumDocumento').focus();
                }
            }
        }

        function ocultar() {
            document.getElementById('Buscar_Cliente').style.display = 'none';
            document.getElementById('cargando').style.display = 'block';
            setInterval('mostrar()', 1000);
        }

        function mostrar() {
            $valorcito = $('#PROV_RazonSocial').val();
            $valor = $valorcito.length;
            if ($valor > 0) {
                document.getElementById('Buscar_Cliente').style.display = 'block';
                document.getElementById('cargando').style.display = 'none';
            }
        }
    </script>

@endsection
