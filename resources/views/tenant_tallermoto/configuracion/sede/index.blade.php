@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Almacen')
@section('contenido')

    @can('tenant.configuracion.sede.create')
    <div class="col-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">CREAR SEDE</h5>
                <p class="card-text"></p>
                <form method="POST" id="almacen_form" action="{{ tenant_url('tenant.configuracion.sede.store') }}">
                    @csrf
                    <input type="text" id="almacen_id_edit" hidden>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label" style=" text-align: left; display: block;">RUC:</label>
                            <div class="input-group ">
                                <input type="number" id="ALM_Ruc" name="ALM_Ruc"
                                    class="form-control "maxlength="11" placeholder="Ruc" required>
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
                            <label class="control-label" style=" text-align: left; display: block;">Nombre Empresa:</label>
                            <input type="text" id="ALM_Nombre" name="ALM_Nombre" class="form-control "
                                placeholder="Nombre" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label" style=" text-align: left; display: block;">Sede:</label>
                            <input type="text" id="ALM_NombreAlmacen" name="ALM_NombreAlmacen"
                                class="form-control " placeholder="Sede" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label"
                                style=" text-align: left; display: block;">Direccion:</label>
                            <input type="text" id="ALM_Direccion" name="ALM_Direccion" class="form-control "
                                placeholder="Dirección" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label" style=" text-align: left; display: block;">CELULAR:</label>
                            <input type="number" id="ALM_Celular" name="ALM_Celular" class="form-control "
                                placeholder="Celular" >
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

    @can('tenant.configuracion.sede.index')
    <div class="col-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">LISTA DE SEDE</h5>
                <p class="card-text">
                <div class="table-responsive" style="background:#FFF;">
                    <table class="table" id="tabla_almacen">
                        <thead >
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">RUC</th>
                                <th scope="col">Empresa</th>
                                <th scope="col">Sede</th>
                                <th scope="col">Dirección</th>
                                <th scope="col">N° celular</th>
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
                ajax: "{{ tenant_url('tenant.configuracion.sede.index') }}",
                columns: [{
                        data: 'ALM_Id',
                        name: 'ALM_Id'
                    },
                    {
                        data: 'ALM_Ruc',
                        name: 'ALM_Ruc'
                    },
                    {
                        data: 'ALM_Nombre',
                        name: 'ALM_Nombre'
                    },
                    {
                        data: 'ALM_NombreAlmacen',
                        name: 'ALM_Nombrelmacen'
                    },
                    {
                        data: 'ALM_Direccion',
                        name: 'ALM_Direccion'
                    },
                    {
                        data: 'ALM_Celular',
                        name: 'ALM_Celular'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return data.action1 + ' ' + data.action2;
                        }
                    }
                ]
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                nombre = $("#ALM_Nombre").val();
                nombreAlmacen = $("#ALM_NombreAlmacen").val();
                direccion = $("#ALM_Direccion").val();
                ruc = $("#ALM_Ruc").val();
                celular = $("#ALM_Celular").val();

                if (nombre == '' || nombreAlmacen == '' || ruc == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                $.ajax({
                    data: $('#almacen_form').serialize(),
                    url: "{{ tenant_url('tenant.configuracion.sede.store') }}",
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
                            title: 'Ya se encuentra registrado este almacen con la misma empresa.'
                        })
                    }

                });
            });

            $('body').on('click', '.editAlmacen', function() {
                var Almacen_id_edit = $(this).data('identificador');
                $.get('{{ tenant_url('tenant.configuracion.sede.edit', ['sede' => ':sede', 'tenant' => tenant('id')]) }}'.replace(':sede', Almacen_id_edit),
                    function(result) {
                        console.log(result);
                        $('#almacen_id_edit').val(result.data.ALM_Id);
                        $('#ALM_Ruc').val(result.data.ALM_Ruc).prop('disabled', true);
                        $('#ALM_Nombre').val(result.data.ALM_Nombre).prop('disabled', true);
                        $('#ALM_NombreAlmacen').val(result.data.ALM_NombreAlmacen);
                        $('#ALM_Direccion').val(result.data.ALM_Direccion);
                        $('#ALM_Celular').val(result.data.ALM_Celular);


                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Almacen_id_update = $('#almacen_id_edit').val();
                $.ajax({
                    data: $('#almacen_form').serialize(),
                    url: '{{ tenant_url('tenant.configuracion.sede.update', ['sede' => ':sede', 'tenant' => tenant('id')]) }}'.replace(':sede', Almacen_id_update),
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
                            title: 'Almacen fallo al actualizarse.'
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

            $('body').on('click', '.deleteAlmacen', function() {

                var Almacen_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ tenant_url('tenant.configuracion.sede.destroy', ['sede' => ':sede', 'tenant' => tenant('id')]) }}'.replace(
                            ':sede', Almacen_id_delete),
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
                                title: 'Almacen fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'El almacen no ha sido eliminada.',
                        icon: 'info'
                    });
                }
            });
        });

        function cancelarUpdate() {
            $('#almacen_form').trigger("reset");
            $("#almacen_id_edit").val('');
            $("#saveBtn").show(); // Mostrar botón Guardar
            $("#updateBtn").hide();
            $('#ALM_Ruc').prop('disabled', false); // Habilitar RUC
            $('#ALM_Nombre').prop('disabled', false); // Habilitar Nombre
        }

        function buscarCliente() {
            var numruc = $('#ALM_Ruc').val();
            if (numruc != '') {
                ocultar()
                var url = '/consultarruc/' + numruc + '?';
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
                                        $("#ALM_Nombre").val("");
                                    }
                                });
                            $('#almacen_form').trigger("reset");
                            $('#cargando').hide();
                            $('#Buscar_Cliente').show();
                            table.draw();
                        } else {
                            $('#ALM_Nombre').val(dat.data.nombre);
                        }
                    },
                });
            } else {
                alert('Escriba el RUC.!');
                $('#ALM_Ruc').focus();
            }

        }

        function ocultar() {
            document.getElementById('Buscar_Cliente').style.display = 'none';
            document.getElementById('cargando').style.display = 'block';
            setInterval('mostrar()', 1000);
        }

        function mostrar() {
            $valorcito = $('#ALM_Nombre').val();
            $valor = $valorcito.length;
            if ($valor > 0) {
                document.getElementById('Buscar_Cliente').style.display = 'block';
                document.getElementById('cargando').style.display = 'none';
            }
        }
    </script>
@endsection
