@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')

@section('titulo', 'Roles')

@section('contenido')
    
    @can('tenant.seguridad.roles.create')
    <div class="col-12 col-md-5">
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

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">
                                    Total seleccionados: <strong id="totalSeleccionadosGlobal">0</strong> / {{ $permissionsGrouped->flatten()->count() }}
                                </span>
                                <div>
                                    <button type="button" id="btnExpandirTodo" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-expand-alt"></i> Expandir todo
                                    </button>
                                    <button type="button" id="btnColapsarTodo" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-compress-alt"></i> Colapsar todo
                                    </button>
                                </div>
                            </div>

                            <div class="scroll-container" style="max-height: 600px; overflow-y: auto;">
                                @foreach ($permissionsGrouped as $groupName => $permissions)
                                    @php($groupSlug = str_replace(' ', '', $groupName))
                                    <div class="mb-3 permission-group" data-group="{{ $groupSlug }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="m-0" style="cursor: pointer;" data-toggle="collapse"
                                                data-target="#toggle{{ $groupSlug }}" aria-expanded="false"
                                                aria-controls="toggle{{ $groupSlug }}">
                                                <b>{{ $groupName }}</b>
                                                <span class="badge badge-secondary group-count-badge" data-group="{{ $groupSlug }}">0/{{ count($permissions) }}</span>
                                            </h6>
                                            <button type="button" class="btn btn-link" data-toggle="collapse"
                                                data-target="#toggle{{ $groupSlug }}" aria-expanded="false"
                                                aria-controls="toggle{{ $groupSlug }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>

                                        <!-- Contenido del grupo -->
                                        <div id="toggle{{ $groupSlug }}" class="collapse">
                                            <div class="mt-2">
                                                <!-- Checkbox para seleccionar todos -->
                                                <div class="form-check">
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <label class="form-check-label text-info"
                                                                for="selectAll{{ $groupSlug }}">Seleccionar todo</label>
                                                        </div>
                                                        <div class="col-2 d-flex justify-content-end">
                                                            <input type="checkbox" class="form-check-input select-all"
                                                                id="selectAll{{ $groupSlug }}">
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
                        @can('tenant.seguridad.roles.create')
                            <button type="submit" id="saveBtn" name="saveBtn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        @endcan

                        @can('tenant.seguridad.roles.edit')
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
    @endcan
    @can('tenant.seguridad.roles.index')
    <div class="col-12 col-md-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">LISTA DE ROLES</h5>
                <p class="card-text">

                <table class="table" id="table-roles">
                    <thead>
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
    @endcan

    <!-- Modal Ver detalles-->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="modalVerDetalleLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVerDetalleLabel">
                        Rol: <strong id="ver_name"></strong>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-6"><strong>ID de rol:</strong> <span id="ver_id"></span></div>
                        <div class="col-6">
                            <strong>Permisos asignados:</strong>
                            <span id="ver_total_permisos" class="badge badge-primary"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Creado:</strong> <span id="ver_fecha_registro"></span></div>
                        <div class="col-6"><strong>Actualizado:</strong> <span id="ver_fecha_update"></span></div>
                    </div>
                    <hr>
                    <h6 class="mb-2">Detalle de permisos por módulo</h6>
                    <div id="ver_permissions_container" style="max-height: 420px; overflow-y: auto;">
                        <!-- Contenido generado dinámicamente por JS -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
                    updateGroupBadges();
                });
            });

            // Actualiza el badge "X/Y" de cada grupo y el contador global,
            // para saber cuántos permisos tiene marcados un grupo sin desplegarlo.
            function updateGroupBadges() {
                let totalGlobal = 0;
                document.querySelectorAll('.permission-group').forEach(function(group) {
                    const groupName = group.getAttribute('data-group');
                    const checkboxes = document.querySelectorAll(
                        `#toggle${groupName} [name="permisos[]"]`);
                    const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
                    totalGlobal += checked;

                    const badge = document.querySelector(
                        `.group-count-badge[data-group="${groupName}"]`);
                    if (badge) {
                        badge.textContent = checked + '/' + checkboxes.length;
                        badge.classList.toggle('badge-secondary', checked === 0);
                        badge.classList.toggle('badge-success', checked > 0 && checked === checkboxes.length);
                        badge.classList.toggle('badge-info', checked > 0 && checked < checkboxes.length);
                    }
                });
                const totalBadge = document.getElementById('totalSeleccionadosGlobal');
                if (totalBadge) {
                    totalBadge.textContent = totalGlobal;
                }
            }

            $(document).on('change', '[name="permisos[]"]', updateGroupBadges);

            $('#btnExpandirTodo').click(function() {
                $('.permissions-groups .collapse').collapse('show');
            });

            $('#btnColapsarTodo').click(function() {
                $('.permissions-groups .collapse').collapse('hide');
            });

            updateGroupBadges();

            $('#accesototal').change(function() {
                // Si el checkbox "Seleccionar Todo" está marcado
                if ($(this).prop('checked')) {
                    // Marcar todos los checkboxes de permisos
                    $('[name="permisos[]"]').prop('checked', true);
                } else {
                    // Desmarcar todos los checkboxes de permisos
                    $('[name="permisos[]"]').prop('checked', false);
                }
                updateGroupBadges();
            });

            $('#accesocero').change(function() {
                // Si el checkbox "acceso cero" está marcado
                if ($(this).prop('checked')) {
                    // Marcar todos los checkboxes de permisos
                    $('[name="permisos[]"]').prop('checked', false);
                }
                updateGroupBadges();
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
                ajax: "{{ tenant_url('tenant.seguridad.role.index') }}",
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
                        return @can('tenant.seguridad.roles.show') data.action3 +' '+ @endcan ''
                            @can('tenant.seguridad.roles.edit') + data.action1 +' '+ @endcan ''
                            @can('tenant.seguridad.roles.destroy') +data.action2 @endcan;
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
                    url: "{{ tenant_url('tenant.seguridad.role.store') }}",
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

                        url: '{{ tenant_url('tenant.seguridad.role.destroy',  ['rol' => ':rol']) }}'.replace(':rol',
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
                $.get('{{ tenant_url('tenant.seguridad.role.edit', ['rol' => ':rol']) }}'.replace(':rol', Role_id_edit),
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

                        updateGroupBadges();

                        // Auto-expandir solo los grupos que tienen algún permiso
                        // marcado, para que se vea de inmediato qué tiene el rol
                        // sin tener que desplegar cada grupo manualmente.
                        $('.permission-group').each(function() {
                            const groupName = $(this).data('group');
                            const tieneMarcados = $(`#toggle${groupName} [name="permisos[]"]:checked`).length > 0;
                            $(`#toggle${groupName}`).collapse(tieneMarcados ? 'show' : 'hide');
                        });

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
                    url: '{{ tenant_url('tenant.seguridad.role.update', ['rol' => ':rol']) }}'.replace(':rol',
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

            function escapeHtml(text) {
                return $('<div>').text(text ?? '').html();
            }

            $('body').on('click', '.eyeRole', function() {
                var Role_id_ver = $(this).data('id');
                $('#ver_permissions_container').html(
                    '<p class="text-muted">Cargando...</p>');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.seguridad.role.show', ['rol' => ':rol']) }}'.replace(':rol', Role_id_ver),
                    function(data) {
                        $('#ver_id').text(data.data.id);
                        $('#ver_name').text(data.data.name);
                        $('#ver_fecha_registro').text(moment(data.data.created_at).format(
                            'YYYY-MM-DD HH:mm:ss'));
                        $('#ver_fecha_update').text(moment(data.data.updated_at).format(
                            'YYYY-MM-DD HH:mm:ss'));

                        var totalBadge = $('#ver_total_permisos');
                        var esAccesoTotal = data.totalPermisos > 0 && data.totalPermisos === data.totalPermisosApp;
                        totalBadge.text(data.totalPermisos + ' de ' + data.totalPermisosApp +
                            (esAccesoTotal ? ' (Acceso Total)' : ''));
                        totalBadge.toggleClass('badge-success', esAccesoTotal);
                        totalBadge.toggleClass('badge-primary', !esAccesoTotal);

                        var groups = data.permissionsGrouped;
                        var groupNames = Object.keys(groups);

                        if (groupNames.length === 0) {
                            $('#ver_permissions_container').html(
                                '<p class="text-muted mb-0">Este rol no tiene ningún permiso asignado.</p>'
                            );
                            return;
                        }

                        var html = '';
                        groupNames.forEach(function(groupName) {
                            var permisos = groups[groupName];
                            html += '<div class="mb-3">';
                            html += '<h6 class="mb-1">' + escapeHtml(groupName) +
                                ' <span class="badge badge-light">' + permisos.length + '</span></h6>';
                            html += '<ul class="list-unstyled mb-0 pl-3">';
                            permisos.forEach(function(permiso) {
                                html += '<li><i class="fas fa-check text-success mr-1"></i>' +
                                    escapeHtml(permiso.nombre) + '</li>';
                            });
                            html += '</ul></div>';
                        });

                        $('#ver_permissions_container').html(html);
                    })

            });

        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@endsection
