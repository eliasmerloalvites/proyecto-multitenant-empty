@extends('central.layout.appAdminLte')

@section('titulo', 'Roles')

@section('contenido')
    <style>

    </style>

    <div class="col-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">CREAR ROL</h5>
                <p class="card-text"></p>
                <form id="RoleForm" name="RoleForm"  >
                    @csrf
                    <!-- Nombre del rol -->
                    <input type="text" id="rol_id_edit" hidden>
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" id="name" name="name"
                            class="form-control input_user @error('name') is-invalid @enderror" placeholder="Nombre"
                            required>
                    </div>

                    <!-- Permiso Especial -->
                    <hr>
                    <div class="form-group text-center">
                        <h6>PERMISO ESPECIAL</h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="accesototal" name="accesototal">
                            <label class="form-check-label" for="accesototal">Acceso Total</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="accesocero" name="accesocero">
                            <label class="form-check-label" for="accesocero">Ningún Acceso</label>
                        </div>
                    </div>

                    <!-- Permisos por grupo -->
                    <div class="permissions-groups">
                        @if (count($permissionsGrouped) <= 0)
                            <p>No hay registros</p>
                        @else
                        
                            <div class="scroll-container" style="max-height: 600px; overflow-y: auto;">
                                @foreach ($permissionsGrouped as $groupName => $permissions)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="m-0" style="cursor: pointer;" data-toggle="collapse"
                                                data-target="#toggle{{ $groupName }}" aria-expanded="false"
                                                aria-controls="toggle{{ $groupName }}">
                                                <b>{{ $groupName }}</b>
                                            </h6>
                                            <button type="button" class="btn btn-link" data-toggle="collapse"
                                                data-target="#toggle{{ $groupName }}" aria-expanded="false"
                                                aria-controls="toggle{{ $groupName }}">
                                                <i class="fas fa-chevron-down"></i> 
                                            </button>
                                        </div>

                                        <!-- Contenido del grupo -->
                                        <div id="toggle{{ $groupName }}" class="collapse">
                                            <div class="mt-2">
                                                <!-- Checkbox para seleccionar todos -->
                                                <div class="form-check">
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <label class="form-check-label text-info" 
                                                                for="selectAll{{ $groupName }}">Seleccionar todo</label>
                                                        </div>
                                                        <div class="col-2 d-flex justify-content-end">
                                                            <input type="checkbox" class="form-check-input select-all"
                                                                id="selectAll{{ $groupName }}">
                                                        </div>
                                                    </div>
                                                </div>



                                                <!-- Permisos del grupo -->
                                                @foreach ($permissions as $permission)
                                                    <div class="form-check">
                                                        <div class="row">
                                                            <div class="col-10 d-flex justify-content-start">
                                                                <label class="form-check-label"
                                                                    for="permission{{ $permission->id }}">{{ $permission->nombre }}</label>
                                                            </div>
                                                            <div class="col-2 d-flex justify-content-end">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="permisos[]" value="{{ $permission->id }}"
                                                                    id="permission{{ $permission->id }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Botones -->
                    <div class="form-group text-right">
                        @can('seguridad.roles.create')
                            <button type="submit" id="saveBtn" name="saveBtn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        @endcan

                        @can('seguridad.roles.edit')
                            <button type="button" id="updateBtn" name="updateBtn" class="btn btn-info" disabled>
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        @endcan

                        <button type="button" id="btncancelar" class="btn btn-danger" onclick="cancelarFormulario()">
                            <i class="fas fa-ban"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>


        </div>
    </div>
    <div class="col-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">LISTA DE ROLES</h5>
                <p class="card-text">

                <table class="table" id="table-roles">
                    <thead style="background-color:#FF5F67;color: #fff;">
                        <tr>
                            <th scope="col">N°</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>


                </table>

            </div>
        </div>
    </div>

    <!-- Modal Ver detalles-->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <p class="col">id de rol: </p>
                        <p id="ver_id" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Nombre de rol:</p>
                        <p id="ver_name" class="col"></p>
                    </div>

                    <div class="row">
                        <p class="col">Descripción de rol: </p>
                        <p id="ver_descripcion" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Fecha de registro de rol: </p>
                        <p id="ver_fecha_registro" class="col"></p>
                    </div>
                    <div class="row">
                        <p class="col">Fecha de actualización de rol: </p>
                        <p id="ver_fecha_update" class="col"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                timer: 3000
            });

            // Evita que el formulario se envíe accidentalmente
            document.getElementById('RoleForm').addEventListener('submit', function(event) {
                event.preventDefault();
            });

            // Función para el botón de cancelar
            function cancelarFormulario() {
                // Puedes agregar lo que necesites hacer al cancelar
                document.getElementById('RoleForm').reset();
            }

            document.querySelectorAll('.btn-toggle').forEach(button => {
                button.addEventListener('click', function(event) {
                    event
                        .preventDefault(); // Evita que el clic recargue la página y no envíe el formulario
                });
            });

            document.querySelectorAll('.select-all').forEach(selectAllCheckbox => {
                selectAllCheckbox.addEventListener('change', function() {
                    const groupName = this.id.replace('selectAll', '');
                    const checkboxes = document.querySelectorAll(
                        `#toggle${groupName} .form-check-input`);
                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                });
            });

            $('#accesototal').change(function() {
                // Si el checkbox "Seleccionar Todo" está marcado
                if ($(this).prop('checked')) {
                    // Marcar todos los checkboxes de permisos
                    $('[name="permisos[]"]').prop('checked', true);
                } else {
                    // Desmarcar todos los checkboxes de permisos
                    $('[name="permisos[]"]').prop('checked', false);
                }
            });

            $('#accesocero').change(function() {
                // Si el checkbox "acceso cero" está marcado
                if ($(this).prop('checked')) {
                    // Marcar todos los checkboxes de permisos
                    $('[name="permisos[]"]').prop('checked', false);
                }
            });

            var table = $('#table-roles').DataTable({
                responsive: true, // Habilitar la opción responsive
                autoWidth: false,
                searchDelay: 2000,
                processing: true,
                serverSide: true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ ",
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
                ajax: "{{ route('role.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: null,
                        name: 'name',
                        'render': function(data, type, row) {
                        return @can('seguridad.roles.show') data.action3 +' '+ @endcan ''
                            @can('seguridad.roles.edit') + data.action1 +' '+ @endcan ''
                            @can('seguridad.roles.destroy') +data.action2 @endcan;
                        }
                    }
                ]
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                name = $("#name").val();
                descripcion = $("#descripcion").val();

                if (name == '' || descripcion == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                $.ajax({
                    data: $('#RoleForm').serialize(),
                    url: "{{ route('role.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        $('#RoleForm').trigger("reset");
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Role fallo al Registrarse.'
                        })
                    }
                });
            });

            $('body').on('click', '.deleteRole', function() {

                var Role_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ route('role.destroy', ['role' => ':role']) }}'.replace(':role',
                            Role_id_delete),
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                            Toast.fire({
                                type: 'success',
                                title: String(data.success)
                            });

                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Rol fallo al Eliminarlo.'
                            })
                        }
                    });
                }
            });

            $('body').on('click', '.editRole', function() {
                var Role_id_edit = $(this).data('id');
                $.get('{{ route('role.edit', ['role' => ':role']) }}'.replace(':role', Role_id_edit),
                    function(data) {
                        console.log(data);
                        $('#rol_id_edit').val(data.data.id);
                        $('#name').val(data.data.name);
                        $('#descripcion').val(data.data.descripcion);

                        // Desmarcar todos los checkboxes antes de marcar los nuevos
                        $('[name="permisos[]"]').prop('checked', false);
                        data.data2.forEach(function(permiso) {
                            // Marcar el checkbox correspondiente si el permiso está asociado al rol
                            $('[name="permisos[]"][value="' + permiso.id + '"]').prop('checked',
                                true);
                        });

                        $('#accesototal').prop('checked', false);
                        $('#accesocero').prop('checked', false);

                        if (verificarPermisosSeleccionados()) {
                            //console.log("Todos los permisos están seleccionados");
                            $('#accesototal').prop('checked', true);

                        } else {
                            //console.log("No todos los permisos están seleccionados"); 

                        }
                        if (verificarPermisosCero()) {
                            $('#accesocero').prop('checked', true);
                        }
                        //desactivar boton guardar
                        $("#saveBtn").prop("disabled", true);
                        //activar boton de actualizar
                        $("#updateBtn").prop("disabled", false);
                        //desactivar campo name
                        $("#name").prop("disabled", true);


                    })
            });

            function verificarPermisosSeleccionados() {
                var todosSeleccionados = true;

                // Iterar sobre cada checkbox de permiso
                $('[name="permisos[]"]').each(function() {
                    // Verificar si el checkbox está marcado
                    if (!$(this).prop('checked')) {
                        todosSeleccionados = false;
                        // Si encontramos un checkbox no marcado, salir del bucle
                        return false;
                    }
                });

                return todosSeleccionados;
            }

            function verificarPermisosCero() {
                var ceroSeleccionados = true;

                // Iterar sobre cada checkbox de permiso
                $('[name="permisos[]"]').each(function() {
                    // Verificar si el checkbox está marcado
                    if ($(this).prop('checked')) {
                        ceroSeleccionados = false;
                        // Si encontramos un checkbox marcado, salir del bucle
                        return false;
                    }
                });

                return ceroSeleccionados;
            }
            $('#btncancelar').click(function(e) {
                cancelarUpdate();
                $("#name").prop("disabled", false);
            });


            function cancelarUpdate() {
                $('#RoleForm').trigger("reset");
                $("#saveBtn").prop("disabled", false);
                $("#updateBtn").prop("disabled", true);

            }

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Role_id_update = $('#rol_id_edit').val();
                $.ajax({
                    data: $('#RoleForm').serialize(),
                    url: '{{ route('role.update', ['role' => ':role']) }}'.replace(':role',
                        Role_id_update),
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        });
                        cancelarUpdate();
                        $('#RoleForm').trigger("reset");
                        table.draw();

                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Rol fallo al Registrarse.'
                        })
                    }
                });
            });

            $('body').on('click', '.eyeRole', function() {
                var Role_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ route('role.show', ['role' => ':role']) }}'.replace(':role', Role_id_ver),
                    function(data) {
                        console.log(data);
                        $('#ver_id').text(data.data.id);
                        $('#ver_name').text(data.data.name);
                        $('#ver_descripcion').text(data.data.descripcion);
                        $('#ver_fecha_registro').text(moment(data.data.created_at).format(
                            'YYYY-MM-DD HH:mm:ss'));
                        $('#ver_fecha_update').text(moment(data.data.updated_at).format(
                            'YYYY-MM-DD HH:mm:ss'));

                    })

            });

        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@endsection
