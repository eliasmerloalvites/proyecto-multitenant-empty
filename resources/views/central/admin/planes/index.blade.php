@extends('central.layout.appAdminLte')
@section('titulo', 'Planes')
@section('contenido')

    <div class="col-12">
        <div class="mb-3">
            <h4 class="font-weight-bold mb-1">PLANES COMERCIALES</h4>
            <p class="text-muted mb-0">
                Edita precio, límites y módulos habilitados de cada plan. Los cambios aquí se aplican
                inmediatamente a los tenants nuevos; para un cliente ya existente, cambia su plan desde
                <a href="{{ route('admin.clients.index') }}">Clientes</a> para resincronizarlo con estos valores.
            </p>
        </div>
    </div>

    @foreach ($planes as $plan)
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary text-uppercase">{{ $plan->nombre }}</h5>
                    <span class="badge badge-light border">{{ $plan->key }}</span>
                </div>

                <div class="card-body">
                    <form class="form-plan" data-key="{{ $plan->key }}"
                        action="{{ route('admin.planes.update', $plan->key) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nombre visible</label>
                                <input type="text" name="nombre" class="form-control" value="{{ $plan->nombre }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Precio (S/ / mes)</label>
                                <input type="number" step="0.01" min="0" name="price" class="form-control"
                                    value="{{ $plan->price }}" required>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-muted small text-uppercase mt-2 mb-2">Límites técnicos</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Usuarios máx.</label>
                                <input type="number" min="1" name="max_users" class="form-control"
                                    value="{{ $plan->max_users }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Imágenes máx.</label>
                                <input type="number" min="1" name="max_images" class="form-control"
                                    value="{{ $plan->max_images }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Storage (MB)</label>
                                <input type="number" min="1" name="storage_limit_mb" class="form-control"
                                    value="{{ $plan->storage_limit_mb }}" required>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-muted small text-uppercase mt-2 mb-2">Límites de negocio</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Locales/Sedes</label>
                                <input type="number" min="1" name="branches" class="form-control"
                                    value="{{ $plan->limits['branches'] ?? 1 }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Almacenes</label>
                                <input type="number" min="1" name="warehouses" class="form-control"
                                    value="{{ $plan->limits['warehouses'] ?? 1 }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Cajas</label>
                                <input type="number" min="1" name="cash_registers" class="form-control"
                                    value="{{ $plan->limits['cash_registers'] ?? 1 }}" required>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-muted small text-uppercase mt-2 mb-2">Módulos del panel</h6>
                        <div class="row mb-2">
                            @foreach ($modulos as $clave => $etiqueta)
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" name="modules[]"
                                            value="{{ $clave }}" id="mod_{{ $plan->key }}_{{ $clave }}"
                                            {{ ($plan->modules[$clave] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mod_{{ $plan->key }}_{{ $clave }}">
                                            {{ $etiqueta }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <h6 class="font-weight-bold text-muted small text-uppercase mt-2 mb-2">Otras características</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="custom_domain_enabled"
                                        value="1" id="dom_{{ $plan->key }}"
                                        {{ $plan->custom_domain_enabled ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="dom_{{ $plan->key }}">Dominio propio</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="custom_branding"
                                        value="1" id="brand_{{ $plan->key }}"
                                        {{ $plan->custom_branding ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="brand_{{ $plan->key }}">Branding propio</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="customizable"
                                        value="1" id="custom_{{ $plan->key }}"
                                        {{ $plan->customizable ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="custom_{{ $plan->key }}">Personalizable</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-1"></i>
                            Guardar cambios de {{ $plan->nombre }}
                        </button>

                    </form>
                </div>
            </div>
        </div>
    @endforeach

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

            $('.form-plan').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $form.find('button[type=submit]');
                $btn.prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize() + '&_method=PUT',
                    success: function(data) {
                        Toast.fire({
                            icon: 'success',
                            title: data.success
                        });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'No se pudo guardar el plan.';
                        Toast.fire({
                            icon: 'error',
                            title: msg
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
