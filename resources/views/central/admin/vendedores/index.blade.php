@extends('central.layout.appAdminLte')
@section('titulo', 'Vendedores')
@section('contenido')
    @can('admin.vendedores.create')
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <h4 class="mb-1 font-weight-bold text-primary">CREAR VENDEDOR</h4>
                    <small class="text-muted">Da de alta a un vendedor comercial con su primer esquema de comisión</small>
                </div>
                <div class="card-body">
                    <form id="VendedorForm">
                        @csrf

                        <div class="mb-3">
                            <label>Nombre completo</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email (login)</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-primary mb-3">Esquema de Comisión Inicial</h6>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Porcentaje (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="porcentaje"
                                    class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Meses de duración</label>
                                <input type="number" min="1" max="60" name="meses_duracion" class="form-control"
                                    required>
                            </div>
                        </div>
                        <small class="text-muted d-block mb-3">
                            El vendedor gana este % de la mensualidad de cada cliente que refiera, durante estos meses
                            desde el primer pago real de ese cliente.
                        </small>

                        <button id="saveBtn" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Guardar Vendedor
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('admin.vendedores.index')
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE VENDEDORES</h5>
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table table-striped nowrap" id="table-vendedores">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Código Referido</th>
                                    <th>Esquema Vigente</th>
                                    <th>Clientes</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- MODAL EDITAR --}}
    <div class="modal fade" id="modalEditarVendedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title mb-0"><i class="fas fa-edit mr-2"></i>Editar Vendedor</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="EditarVendedorForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_vendedor_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Código de Referido</label>
                            <input type="text" id="edit_codigo_referido" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Estado</label>
                            <select name="estado" id="edit_estado" class="form-control">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-0">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CAMBIAR ESQUEMA --}}
    <div class="modal fade" id="modalCambiarEsquema" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title mb-0"><i class="fas fa-percentage mr-2"></i>Cambiar Esquema de Comisión</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="CambiarEsquemaForm">
                    @csrf
                    <input type="hidden" id="esquema_vendedor_id">
                    <div class="modal-body">
                        <div class="alert alert-light border small">
                            El % nuevo aplica solo a los clientes que este vendedor traiga de ahora en adelante — los
                            que ya tiene siguen generando comisión con su esquema anterior hasta agotar su ventana de
                            meses.
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Nuevo Porcentaje (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="porcentaje"
                                    class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Nuevos Meses</label>
                                <input type="number" min="1" max="60" name="meses_duracion" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-0">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info">Actualizar Esquema</button>
                    </div>
                </form>
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

            function errorMessage(xhr, fallback) {
                return (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : fallback;
            }

            var table = $('#table-vendedores').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('admin.vendedores.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nombre', name: 'nombre' },
                    { data: 'email', name: 'email' },
                    { data: 'codigo_referido', name: 'codigo_referido' },
                    { data: 'esquema', name: 'esquema' },
                    { data: 'clientes_count', name: 'clientes_count' },
                    { data: 'estado', name: 'estado' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return @can('admin.vendedores.edit')
                                    data.action1 + ' ' + data.action2 +
                                @endcan
                                ''
                                @can('admin.vendedores.destroy')
                                    +' ' + data.action3
                                @endcan
                                ;
                        }
                    }
                ]
            });

            $('#VendedorForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.vendedores.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(data) {
                        Toast.fire({ icon: 'success', title: data.success });
                        $('#VendedorForm').trigger('reset');
                        table.draw();
                    },
                    error: function(xhr) {
                        Toast.fire({ icon: 'error', title: errorMessage(xhr, 'No se pudo crear el vendedor.') });
                    }
                });
            });

            $('body').on('click', '.editVendedor', function() {
                var id = $(this).data('id');
                $.get('{{ url('admin/vendedores') }}/' + id + '/edit', function(result) {
                    var data = result.data;
                    $('#edit_vendedor_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_codigo_referido').val(data.codigo_referido);
                    $('#edit_estado').val(data.estado);
                    $('#modalEditarVendedor').modal('show');
                }).fail(function(xhr) {
                    Toast.fire({ icon: 'error', title: errorMessage(xhr, 'No se pudo cargar el vendedor.') });
                });
            });

            $('#EditarVendedorForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#edit_vendedor_id').val();
                $.ajax({
                    url: '{{ url('admin/vendedores') }}/' + id,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(data) {
                        Toast.fire({ icon: 'success', title: data.success });
                        $('#modalEditarVendedor').modal('hide');
                        table.draw();
                    },
                    error: function(xhr) {
                        Toast.fire({ icon: 'error', title: errorMessage(xhr, 'No se pudo actualizar.') });
                    }
                });
            });

            $('body').on('click', '.cambiarEsquemaVendedor', function() {
                $('#esquema_vendedor_id').val($(this).data('id'));
                $('#CambiarEsquemaForm')[0].reset();
                $('#modalCambiarEsquema').modal('show');
            });

            $('#CambiarEsquemaForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#esquema_vendedor_id').val();
                $.ajax({
                    url: '{{ url('admin/vendedores') }}/' + id + '/esquema',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(data) {
                        Toast.fire({ icon: 'success', title: data.success });
                        $('#modalCambiarEsquema').modal('hide');
                        table.draw();
                    },
                    error: function(xhr) {
                        Toast.fire({ icon: 'error', title: errorMessage(xhr, 'No se pudo actualizar el esquema.') });
                    }
                });
            });

            $('body').on('click', '.deactivateVendedor', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: '¿Desactivar vendedor?',
                    text: 'Ya no podrá iniciar sesión ni traer clientes nuevos. Sus clientes y comisiones ya generadas se conservan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, desactivar',
                    cancelButtonText: 'Cancelar'
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '{{ url('admin/vendedores') }}/' + id,
                        type: 'DELETE',
                        success: function(data) {
                            Toast.fire({ icon: 'success', title: data.success });
                            table.draw();
                        },
                        error: function(xhr) {
                            Toast.fire({ icon: 'error', title: errorMessage(xhr, 'No se pudo desactivar.') });
                        }
                    });
                });
            });
        });
    </script>
@endsection
