@extends('central.layout.appAdminLte')
@section('titulo', 'Clientes')
@section('contenido')
    @can('admin.clients.create')
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white border-0 pb-0">
                    <h4 class="mb-1 font-weight-bold text-primary">
                        CREAR CLIENTE
                    </h4>
                    <small class="text-muted">
                        Registra un nuevo cliente en la plataforma SaaS
                    </small>
                </div>

                <div class="card-body">

                    <form id="ClienteForm" name="ClienteForm" action="{{ route('admin.clients.store') }}">
                        @csrf

                        {{-- ===================================================== --}}
                        {{-- INFORMACION CLIENTE --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-user-circle mr-1"></i>
                                Información del Cliente
                            </h6>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>RUC</label>
                                    <input type="text" name="ruc" id="ruc" class="form-control"
                                        placeholder="Ingrese RUC">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Razón Social</label>
                                    <input type="text" name="razon_social" id="razon_social" class="form-control"
                                        placeholder="Ingrese razón social" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label>Tipo de Negocio</label>

                                    <select class="form-control select2" id="tipo_negocio" name="tipo_negocio">
                                        <option value="generico">Genérico</option>
                                        <option value="tallermoto">Taller de Motos</option>
                                        <option value="optica">Óptica</option>
                                        <option value="ferreteria">Ferretería</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="hotel">Hotel</option>
                                    </select>
                                </div>

                            </div>
                        </div>


                        {{-- ===================================================== --}}
                        {{-- PLAN Y FACTURACION --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">

                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-file-invoice-dollar mr-1"></i>
                                Plan y Facturación
                            </h6>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>Plan SaaS</label>

                                    <select class="form-control" name="plan" id="plan">
                                        <option value="start">START</option>
                                        <option value="basic">BASIC</option>
                                        <option value="plus">PLUS</option>
                                        <option value="empresarial">EMPRESARIAL</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Día de Facturación</label>

                                    <input type="number" min="1" max="28" name="billing_day" class="form-control"
                                        placeholder="Día del mes">
                                </div>

                            </div>
                        </div>


                        {{-- ===================================================== --}}
                        {{-- DOMINIO Y ACCESO --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">

                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-globe mr-1"></i>
                                Dominio y Acceso
                            </h6>

                            <div class="alert alert-light border small">
                                El subdominio se utiliza para crear la base de datos y el identificador interno del tenant.
                                <br>
                                El dominio personalizado es opcional y solo aplica para planes premium.
                            </div>


                            {{-- TIPO DOMINIO --}}

                            <div class="mb-3">

                                <label class="d-block mb-2">
                                    Tipo de Dominio
                                </label>

                                <div class="custom-control custom-radio custom-control-inline">

                                    <input type="radio" id="use_subdomain" name="domain_type" class="custom-control-input"
                                        value="subdomain" checked>

                                    <label class="custom-control-label" for="use_subdomain">
                                        Subdominio KaelTech
                                    </label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline">

                                    <input type="radio" id="use_custom_domain" name="domain_type" class="custom-control-input"
                                        value="custom_domain">

                                    <label class="custom-control-label" for="use_custom_domain">
                                        Dominio Personalizado
                                    </label>
                                </div>
                            </div>


                            {{-- SUBDOMAIN --}}

                            <div class="mb-3" id="subdomainContainer">

                                <label>Subdominio</label>

                                <div class="input-group">

                                    <input type="text" class="form-control" name="subdomain" id="subdomain"
                                        placeholder="ejemplo">

                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            .{{ config('app.central_domain') }}
                                        </span>
                                    </div>
                                </div>

                                <small class="text-muted">
                                    Solo letras y números.
                                </small>
                            </div>


                            {{-- CUSTOM DOMAIN --}}

                            <div class="mb-3 d-none" id="customDomainContainer">

                                <label>Dominio Personalizado</label>

                                <input type="text" class="form-control" name="custom_domain" id="custom_domain"
                                    placeholder="midominio.com">

                                <small class="text-muted">
                                    Debe apuntar correctamente a tu servidor.
                                </small>
                            </div>

                        </div>


                        {{-- ===================================================== --}}
                        {{-- ACCESO SISTEMA --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">

                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-user-lock mr-1"></i>
                                Acceso al Sistema
                            </h6>

                            <div class="row">

                                <div class="col-md-12 mb-3">
                                    <label>Email</label>

                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="correo@empresa.com" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label>Contraseña</label>

                                    <div class="input-group">

                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Ingrese contraseña" required>

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        {{-- BOTONES --}}

                        <div class="d-flex align-items-center">

                            <button id="saveBtn" class="btn btn-primary mr-2">
                                <i class="fas fa-save mr-1"></i>
                                Guardar Cliente
                            </button>

                            <button type="reset" class="btn btn-danger">
                                <i class="fas fa-ban mr-1"></i>
                                Cancelar
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endcan
    @can('admin.clients.index')
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE CLIENTES</h5>
                    <p class="card-text">

                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table table-striped nowrap" id="table-users" name="table-users">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Ruc</th>
                                    <th scope="col">Razon Social</th>
                                    <th scope="col">Negocio</th>
                                    <th scope="col">Plan</th>
                                    <th scope="col">Dominio</th>
                                    <th scope="col">Día Fact</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- ===================================================== --}}
    {{-- MODAL EDITAR CLIENTE --}}
    {{-- ===================================================== --}}

    <div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-labelledby="modalEditarClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded">

                <div class="modal-header bg-primary text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">
                            <i class="fas fa-edit mr-2"></i>
                            Editar Cliente
                        </h5>
                        <small class="opacity-75">
                            El tipo de negocio y el dominio no se pueden modificar desde aquí
                        </small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="EditarClienteForm" name="EditarClienteForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_client_id" name="client_id">

                    <div class="modal-body">

                        <div class="alert alert-light border small mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Tenant ID:</strong> <span id="edit_info_id"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Negocio:</strong> <span id="edit_info_tipo_negocio"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Dominio:</strong> <span id="edit_info_domain"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>RUC</label>
                                <input type="text" name="ruc" id="edit_ruc" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Razón Social</label>
                                <input type="text" name="razon_social" id="edit_razon_social" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Plan SaaS</label>
                                <select class="form-control" name="plan" id="edit_plan">
                                    <option value="start">START</option>
                                    <option value="basic">BASIC</option>
                                    <option value="plus">PLUS</option>
                                    <option value="empresarial">EMPRESARIAL</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Día de Facturación</label>
                                <input type="number" min="1" max="28" name="billing_day" id="edit_billing_day" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Próximo Pago</label>
                                <input type="date" name="next_payment_date" id="edit_next_payment_date" class="form-control">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Estado</label>
                                <select class="form-control" name="status" id="edit_status">
                                    <option value="activo">Activo</option>
                                    <option value="suspendido">Suspendido</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer bg-white border-0">
                        <button type="button" class="btn btn-light border px-4" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" id="updateClientBtn" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-1"></i>
                            Guardar Cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL VER DETALLE --}}
    {{-- ===================================================== --}}

    <div class="modal fade" id="modalVerCliente" tabindex="-1" aria-labelledby="modalVerClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded">

                <div class="modal-header bg-primary text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">
                            <i class="fas fa-building mr-2"></i>
                            Detalle del Cliente
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body bg-light">
                    <div class="card border-0 shadow-sm rounded">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <small class="text-muted d-block">Tenant ID</small>
                                    <h5 class="fw-bold text-primary mb-0" id="ver_id"></h5>
                                </div>
                                <div>
                                    <span class="badge px-3 py-2" id="ver_status_badge"></span>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">RUC</small>
                                    <div class="fw-semibold" id="ver_ruc"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Razón Social</small>
                                    <div class="fw-semibold" id="ver_razon_social"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tipo de Negocio</small>
                                    <div class="fw-semibold" id="ver_tipo_negocio"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Dominio</small>
                                    <div class="fw-semibold" id="ver_domain"></div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Plan</small>
                                    <div class="fw-semibold" id="ver_plan"></div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Día de Facturación</small>
                                    <div class="fw-semibold" id="ver_billing_day"></div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Próximo Pago</small>
                                    <div class="fw-semibold" id="ver_next_payment_date"></div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="font-weight-bold text-primary mb-3">Límites del Plan</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Usuarios Máx.</small>
                                    <div class="fw-semibold" id="ver_max_users"></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Imágenes Máx.</small>
                                    <div class="fw-semibold" id="ver_max_images"></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Storage (MB)</small>
                                    <div class="fw-semibold" id="ver_storage_limit_mb"></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Dominio Propio</small>
                                    <div class="fw-semibold" id="ver_custom_domain_enabled"></div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Fecha de Registro</small>
                                    <div class="fw-semibold" id="ver_created_at"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Última Actualización</small>
                                    <div class="fw-semibold" id="ver_updated_at"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-0">
                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $('input[name="domain_type"]').on('change', function() {

            const value = $(this).val();

            if (value === 'subdomain') {

                $('#subdomainContainer').removeClass('d-none');
                $('#customDomainContainer').addClass('d-none');

            } else {

                $('#subdomainContainer').addClass('d-none');
                $('#customDomainContainer').removeClass('d-none');
            }
        });

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
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    return xhr.responseJSON.error;
                }
                return fallback;
            }

            var table = $('#table-users').DataTable({
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
                ajax: "{{ route('admin.clients.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'ruc',
                        name: 'ruc'
                    },
                    {
                        data: 'razon_social',
                        name: 'razon_social'
                    },
                    {
                        data: 'tipo_negocio',
                        name: 'tipo_negocio'
                    },
                    {
                        data: 'plan',
                        name: 'plan'
                    },
                    {
                        data: 'domain',
                        name: 'domain'
                    },
                    {
                        data: 'billing_day',
                        name: 'billing_day'
                    },
                    {
                        data: 'estado',
                        name: 'estado'
                    },
                    {
                        data: null,
                        name: 'name',
                        'render': function(data, type, row) {
                            return @can('admin.clients.show')
                                    data.action3 + ' ' +
                                @endcan
                            ''
                            @can('admin.clients.edit')
                                +data.action1 + ' ' +data.action4 + ' ' +
                            @endcan
                            ''
                            @can('admin.clients.destroy')
                                +data.action2
                            @endcan ;
                        }
                    }
                ]
            });


            $('#saveBtn').click(function(e) {
                e.preventDefault();
                const form = document.getElementById('ClienteForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                $.ajax({
                    data: $('#ClienteForm').serialize(),
                    url: "{{ route('admin.clients.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        $('#ClienteForm').trigger("reset");
                        table.draw();
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        Toast.fire({
                            type: 'error',
                            title: errorMessage(xhr, 'Cliente falló al registrarse.')
                        })
                    }
                });
            });


            // ================= EDITAR =================

            $('body').on('click', '.editClient', function() {
                var clientId = $(this).data('id');

                $.get('{{ route('admin.clients.edit', ['client' => ':client']) }}'.replace(':client', clientId),
                    function(result) {
                        var data = result.data;
                        $('#edit_client_id').val(data.id);
                        $('#edit_info_id').text(data.id);
                        $('#edit_info_tipo_negocio').text(data.tipo_negocio);
                        $('#edit_info_domain').text(data.domain);
                        $('#edit_ruc').val(data.ruc);
                        $('#edit_razon_social').val(data.razon_social);
                        $('#edit_plan').val(data.plan);
                        $('#edit_billing_day').val(data.billing_day);
                        $('#edit_next_payment_date').val(data.next_payment_date);
                        $('#edit_status').val(data.status);
                        $('#modalEditarCliente').modal('show');
                    }
                ).fail(function(xhr) {
                    Toast.fire({
                        type: 'error',
                        title: errorMessage(xhr, 'No se pudo cargar el cliente.')
                    });
                });
            });

            $('#EditarClienteForm').on('submit', function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }

                var clientId = $('#edit_client_id').val();

                $.ajax({
                    data: $('#EditarClienteForm').serialize(),
                    url: '{{ route('admin.clients.update', ['client' => ':client']) }}'.replace(':client',
                        clientId),
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        });
                        $('#modalEditarCliente').modal('hide');
                        table.draw();
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        Toast.fire({
                            type: 'error',
                            title: errorMessage(xhr, 'Cliente falló al actualizarse.')
                        })
                    }
                });
            });

            // ================= VER DETALLE =================

            $('body').on('click', '.eyeClient', function() {
                var clientId = $(this).data('id');

                $.get('{{ route('admin.clients.show', ['client' => ':client']) }}'.replace(':client', clientId),
                    function(result) {
                        var data = result.data;
                        $('#ver_id').text(data.id);
                        $('#ver_ruc').text(data.ruc || '—');
                        $('#ver_razon_social').text(data.razon_social);
                        $('#ver_tipo_negocio').text(data.tipo_negocio);
                        $('#ver_domain').text(data.domain);
                        $('#ver_plan').text(String(data.plan).toUpperCase());
                        $('#ver_billing_day').text(data.billing_day);
                        $('#ver_next_payment_date').text(data.next_payment_date || 'Sin programar');
                        $('#ver_max_users').text(data.max_users ?? '—');
                        $('#ver_max_images').text(data.max_images ?? '—');
                        $('#ver_storage_limit_mb').text(data.storage_limit_mb ?? '—');
                        $('#ver_custom_domain_enabled').text(data.custom_domain_enabled ? 'Sí' : 'No');
                        $('#ver_created_at').text(data.created_at ? moment(data.created_at).format(
                            'YYYY-MM-DD HH:mm') : '—');
                        $('#ver_updated_at').text(data.updated_at ? moment(data.updated_at).format(
                            'YYYY-MM-DD HH:mm') : '—');

                        var badges = {
                            activo: 'badge-success',
                            suspendido: 'badge-warning',
                            cancelado: 'badge-danger'
                        };
                        $('#ver_status_badge')
                            .attr('class', 'badge px-3 py-2 ' + (badges[data.status] || 'badge-secondary'))
                            .text(String(data.status).toUpperCase());

                        $('#modalVerCliente').modal('show');
                    }
                ).fail(function(xhr) {
                    Toast.fire({
                        type: 'error',
                        title: errorMessage(xhr, 'No se pudo cargar el detalle.')
                    });
                });
            });

            // ================= DAR DE BAJA / REACTIVAR =================

            $('body').on('click', '.toggleStatusClient', function() {
                var clientId = $(this).data('id');
                var willActivate = $(this).find('i').hasClass('fa-check');

                Swal.fire({
                    title: willActivate ? '¿Reactivar cliente?' : '¿Dar de baja al cliente?',
                    text: willActivate ?
                        'El cliente volverá a tener acceso a su sistema.' :
                        'El cliente perderá temporalmente el acceso a su sistema. Podrás reactivarlo cuando quieras.',
                    icon: willActivate ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: willActivate ? '#198754' : '#f59e0b',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: willActivate ? 'Sí, reactivar' : 'Sí, dar de baja',
                    cancelButtonText: 'Cancelar'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        type: 'PATCH',
                        url: '{{ route('admin.clients.toggleStatus', ['client' => ':client']) }}'.replace(
                            ':client', clientId),
                        success: function(data) {
                            Toast.fire({
                                type: 'success',
                                title: data.success
                            });
                            table.draw();
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr);
                            Toast.fire({
                                type: 'error',
                                title: errorMessage(xhr, 'No se pudo cambiar el estado del cliente.')
                            })
                        }
                    });
                });
            });

            // ================= ELIMINAR DEFINITIVAMENTE =================

            $('body').on('click', '.deleteClient', function() {
                var clientId = $(this).data('id');

                Swal.fire({
                    title: '¿Eliminar cliente definitivamente?',
                    html: 'Esta acción <strong>borra la base de datos del tenant</strong> y todo su contenido. No se puede deshacer.<br><br>Si solo quieres bloquear el acceso, usa mejor "Dar de baja".',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar definitivamente',
                    cancelButtonText: 'Cancelar'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        type: "DELETE",
                        url: '{{ route('admin.clients.destroy', ['client' => ':client']) }}'
                            .replace(':client', clientId),
                        success: function(data) {
                            table.draw();
                            Toast.fire({
                                type: 'success',
                                title: String(data.success)
                            });
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr);
                            Toast.fire({
                                type: 'error',
                                title: errorMessage(xhr, 'Cliente falló al eliminarse.')
                            })
                        }
                    });
                });
            });

        });
    </script>

@endsection
