@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Turno')
@section('contenido')

    @can('tenant.configuracion.sede.create')
    <div class="col-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">CREAR TURNO</h5>
                <p class="card-text"></p>
                <form method="POST" id="turno_form" action="{{ tenant_url('tenant.configuracion.turno.store') }}">
                    @csrf
                    <input type="text" id="turno_id_edit" hidden>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label" style=" text-align: left; display: block;">Turno:</label>
                            <input type="text" id="TUR_Nombre" name="TUR_Nombre"
                                class="form-control " placeholder="Turno" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label"
                                style=" text-align: left; display: block;">Descripción:</label>
                            <input type="text" id="TUR_Descripcion" name="TUR_Descripcion" class="form-control "
                                placeholder="Descripción" required >
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
                <h5 class="card-title">LISTA DE TURNO</h5>
                <p class="card-text">
                <div class="table-responsive" style="background:#FFF;">
                    <table class="table" id="tabla_turno">
                        <thead >
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">Turno</th>
                                <th scope="col">Descripcion</th>
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

            var table = $('#tabla_turno').DataTable({
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
                ajax: "{{ tenant_url('tenant.configuracion.turno.index') }}",
                columns: [{
                        data: 'TUR_Id',
                        name: 'TUR_Id'
                    },
                    {
                        data: 'TUR_Nombre',
                        name: 'TUR_Nombre'
                    },
                    {
                        data: 'TUR_Descripcion',
                        name: 'TUR_Descripcion'
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
                turno = $("#TUR_Nombre").val();
                descripcion = $("#TUR_Descripcion").val();

                if (descripcion == '' || turno == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                $.ajax({
                    data: $('#turno_form').serialize(),
                    url: "{{ tenant_url('tenant.configuracion.turno.store') }}",
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
                            title: 'Ya se encuentra registrado este turno con la misma empresa.'
                        })
                    }

                });
            });

            $('body').on('click', '.editTurno', function() {
                var Turno_id_edit = $(this).data('id');
                $.get('{{ tenant_url('tenant.configuracion.turno.edit', ['turno' => ':turno', 'tenant' => tenant('id')]) }}'.replace(':turno', Turno_id_edit),
                    function(result) {
                        console.log(result);
                        $('#turno_id_edit').val(result.data.TUR_Id);
                        $('#TUR_Nombre').val(result.data.TUR_Nombre);
                        $('#TUR_Descripcion').val(result.data.TUR_Descripcion);


                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Turno_id_update = $('#turno_id_edit').val();
                $.ajax({
                    data: $('#turno_form').serialize(),
                    url: '{{ tenant_url('tenant.configuracion.turno.update', ['turno' => ':turno', 'tenant' => tenant('id')]) }}'.replace(':turno', Turno_id_update),
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
                            title: 'Turno fallo al actualizarse.'
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

            $('body').on('click', '.deleteTurno', function() {
                var Turno_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",
                        url: '{{ tenant_url('tenant.configuracion.turno.destroy', ['turno' => ':turno', 'tenant' => tenant('id')]) }}'.replace(
                            ':turno', Turno_id_delete),
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                            console.log('success:', data);
                            Toast.fire({
                                type: 'success',
                                title: String(data.message),
                                icon: 'info'
                            });

                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Turno fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'El turno no ha sido eliminada.',
                        icon: 'info'
                    });
                }
            });

            $('body').on('click', '.activarTurno', function() {
                var Turno_id = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres activarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "PUT",url: '{{ tenant_url('tenant.configuracion.turno.activar', ['turno' => ':turno', 'tenant' => tenant('id')]) }}'.replace(
                            ':turno', Turno_id),
                        success: function(data) {
                            table.draw();
                            Toast.fire({
                                type: 'success',
                                title: String(data.message)
                            });

                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Turno fallo al Eliminarlo.'
                            })
                        }
                    });
                }
            });

        });

        function cancelarUpdate() {
            $('#turno_form').trigger("reset");
            $("#turno_id_edit").val('');
            $("#saveBtn").show(); // Mostrar botón Guardar
            $("#updateBtn").hide();
        }

    </script>
@endsection
