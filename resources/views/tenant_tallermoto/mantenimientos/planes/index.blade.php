@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Planes de Mantenimiento')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-1">PLANES DE MANTENIMIENTO</h4>
                        <p class="text-muted mb-0">
                            Arma paquetes con solo los items de checklist que necesitas. Al crear un mantenimiento,
                            se elige el plan y el formulario muestra únicamente esos items.
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnNuevoPlan">
                        <i class="fa fa-plus"></i> Nuevo plan
                    </button>
                </div>

                <ul class="nav nav-tabs mb-3">
                    @foreach ($tipos as $codigo => $nombre)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#tab-{{ $codigo }}">
                                {{ $nombre }}
                                <span class="badge badge-secondary">{{ $planes->get($codigo, collect())->count() }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach ($tipos as $codigo => $nombre)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $codigo }}">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Nombre del plan</th>
                                            <th class="text-center">Items incluidos</th>
                                            <th class="text-center">Activo</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($planes->get($codigo, collect()) as $plan)
                                            <tr>
                                                <td>{{ $plan->PLAN_Nombre }}</td>
                                                <td class="text-center">{{ count($plan->PLAN_Items) }} / {{ count($catalogos[$codigo]) }}</td>
                                                <td class="text-center">
                                                    @if ($plan->PLAN_Activo)
                                                        <span class="badge badge-success">Activo</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactivo</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-primary btn-sm btnEditarPlan"
                                                        data-plan="{{ $plan->toJson() }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm btnEliminarPlan"
                                                        data-id="{{ $plan->PLAN_Id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">
                                                    Todavía no hay planes de este tipo. Se sigue mostrando el checklist completo.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR/EDITAR PLAN --}}
    <div class="modal fade" id="modalPlan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPlanTitulo">Nuevo plan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="plan_id">
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label>Tipo de mantenimiento</label>
                            <select class="form-control" id="plan_tipo">
                                @foreach ($tipos as $codigo => $nombre)
                                    <option value="{{ $codigo }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5">
                            <label>Nombre del plan</label>
                            <input type="text" class="form-control" id="plan_nombre" placeholder="Ej. Cambio de aceite básico">
                        </div>
                        <div class="form-group col-md-2 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="plan_activo" checked>
                                <label class="form-check-label" for="plan_activo">Activo</label>
                            </div>
                        </div>
                    </div>
                    <label>Items del checklist a incluir</label>
                    <div id="plan_items_container" class="border rounded p-2" style="max-height: 320px; overflow-y:auto;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarPlan">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        const CATALOGOS = @json($catalogos);
        let planEditandoId = null;

        function pintarItems(tipo, seleccionados) {
            let html = '';
            (CATALOGOS[tipo] || []).forEach(function (item) {
                let checked = seleccionados.includes(item.codigo) ? 'checked' : '';
                html += `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input plan-item-check" value="${item.codigo}" id="item_${item.codigo}" ${checked}>
                        <label class="form-check-label" for="item_${item.codigo}">${item.etiqueta}</label>
                    </div>`;
            });
            $('#plan_items_container').html(html);
        }

        $('#plan_tipo').on('change', function () {
            pintarItems($(this).val(), []);
        });

        $('#btnNuevoPlan').on('click', function () {
            planEditandoId = null;
            $('#modalPlanTitulo').text('Nuevo plan');
            $('#plan_id').val('');
            $('#plan_nombre').val('');
            $('#plan_activo').prop('checked', true);
            $('#plan_tipo').prop('disabled', false).val('MGC');
            pintarItems('MGC', []);
            $('#modalPlan').modal('show');
        });

        $('body').on('click', '.btnEditarPlan', function () {
            let plan = $(this).data('plan');
            planEditandoId = plan.PLAN_Id;
            $('#modalPlanTitulo').text('Editar plan');
            $('#plan_id').val(plan.PLAN_Id);
            $('#plan_nombre').val(plan.PLAN_Nombre);
            $('#plan_activo').prop('checked', !!plan.PLAN_Activo);
            $('#plan_tipo').prop('disabled', true).val(plan.PLAN_Tipo);
            pintarItems(plan.PLAN_Tipo, plan.PLAN_Items || []);
            $('#modalPlan').modal('show');
        });

        $('body').on('click', '.btnEliminarPlan', function () {
            let id = $(this).data('id');
            Swal.fire({
                icon: 'question',
                title: '¿Eliminar este plan?',
                text: 'Los mantenimientos ya creados con este plan no se ven afectados.',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(function (res) {
                if (!res.isConfirmed) return;

                $.ajax({
                    url: '{{ url("tenant/mantenimientos/planes") }}/' + id,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' }
                }).done(function () {
                    location.reload();
                });
            });
        });

        $('#btnGuardarPlan').on('click', function () {
            let items = $('.plan-item-check:checked').map(function () { return this.value; }).get();

            if (!$('#plan_nombre').val().trim()) {
                Swal.fire({ icon: 'warning', title: 'Falta el nombre del plan' });
                return;
            }
            if (!items.length) {
                Swal.fire({ icon: 'warning', title: 'Selecciona al menos un item del checklist' });
                return;
            }

            let data = {
                PLAN_Tipo: $('#plan_tipo').val(),
                PLAN_Nombre: $('#plan_nombre').val(),
                PLAN_Items: items,
                PLAN_Activo: $('#plan_activo').is(':checked') ? 1 : 0,
                _token: '{{ csrf_token() }}'
            };

            let url = '{{ url("tenant/mantenimientos/planes") }}';
            let method = 'POST';
            if (planEditandoId) {
                url += '/' + planEditandoId;
                data._method = 'PUT';
            }

            $.ajax({ url: url, method: method, data: data })
                .done(function () {
                    location.reload();
                })
                .fail(function (xhr) {
                    Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
                });
        });
    </script>
@endsection
