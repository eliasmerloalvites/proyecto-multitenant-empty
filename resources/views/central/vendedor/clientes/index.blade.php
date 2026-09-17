@extends('central.layout.appAdminLte')
@section('titulo', 'Mis Clientes')
@section('contenido')
    @can('vendedor.clientes.create')
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <h4 class="mb-1 font-weight-bold text-primary">REGISTRAR CLIENTE</h4>
                    <small class="text-muted">Se asocia automáticamente a ti para tu comisión</small>
                </div>
                <div class="card-body">
                    <form id="ClienteForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>RUC</label>
                                <input type="text" name="ruc" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Razón Social</label>
                                <input type="text" name="razon_social" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Tipo de Negocio</label>
                                <select class="form-control" name="tipo_negocio">
                                    <option value="tallermoto">Taller de Motos</option>
                                    <option value="generico">Genérico</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Plan</label>
                                <select class="form-control" name="plan">
                                    <option value="start">START</option>
                                    <option value="basic">BASIC</option>
                                    <option value="plus">PLUS</option>
                                    <option value="empresarial">EMPRESARIAL</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Día de Facturación</label>
                                <input type="number" min="1" max="28" name="billing_day" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="v_use_subdomain" name="domain_type" class="custom-control-input"
                                    value="subdomain" checked>
                                <label class="custom-control-label" for="v_use_subdomain">Subdominio</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="v_use_custom_domain" name="domain_type"
                                    class="custom-control-input" value="custom_domain">
                                <label class="custom-control-label" for="v_use_custom_domain">Dominio propio</label>
                            </div>
                        </div>

                        <div class="mb-3" id="v_subdomainContainer">
                            <label>Subdominio</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="subdomain" placeholder="ejemplo">
                                <div class="input-group-append">
                                    <span class="input-group-text">.{{ config('app.central_domain') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="v_customDomainContainer">
                            <label>Dominio Personalizado</label>
                            <input type="text" class="form-control" name="custom_domain" placeholder="midominio.com">
                        </div>

                        <div class="col-md-12 mb-3 p-0">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3 p-0">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>

                        <button id="saveBtn" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Registrar Cliente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('vendedor.clientes.index')
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">MIS CLIENTES</h5>
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table table-striped nowrap" id="table-mis-clientes">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Razón Social</th>
                                    <th>Plan</th>
                                    <th>Mi Esquema</th>
                                    <th>Referido</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('script')
    <script>
        $('input[name="domain_type"]').on('change', function() {
            if ($(this).val() === 'subdomain') {
                $('#v_subdomainContainer').removeClass('d-none');
                $('#v_customDomainContainer').addClass('d-none');
            } else {
                $('#v_subdomainContainer').addClass('d-none');
                $('#v_customDomainContainer').removeClass('d-none');
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

            var table = $('#table-mis-clientes').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [4, 'desc']
                ],
                ajax: "{{ route('vendedor.clientes.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'razon_social', name: 'razon_social' },
                    { data: 'plan', name: 'plan' },
                    { data: 'esquema', name: 'esquema' },
                    { data: 'referido_en', name: 'referido_en' },
                    { data: 'estado', name: 'estado' },
                ]
            });

            $('#ClienteForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('vendedor.clientes.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(data) {
                        Toast.fire({ icon: 'success', title: data.success });
                        $('#ClienteForm').trigger('reset');
                        table.draw();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo registrar el cliente.';
                        Toast.fire({ icon: 'error', title: msg });
                    }
                });
            });
        });
    </script>
@endsection
