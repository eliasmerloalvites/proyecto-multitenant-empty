@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'App móvil')
@section('contenido')

    <div class="col-12">

        <div class="alert alert-info">
            <i class="fa fa-mobile-alt"></i>
            Aquí gestionas a los clientes que usan la app de tu taller. Cuando alguien pide vincular una moto, revisa
            <strong>a nombre de quién figura</strong> en tus registros antes de aprobar: al aprobar, esa cuenta podrá ver
            todo el historial de la moto.
        </div>

        {{-- SOLICITUDES PENDIENTES --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Solicitudes pendientes
                    <span class="badge badge-warning">{{ $pendientes->count() }}</span>
                </h5>

                @if ($pendientes->isEmpty())
                    <p class="text-muted mb-0">No hay solicitudes pendientes.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Placa</th>
                                    <th>Celular de la cuenta</th>
                                    <th>En tus registros</th>
                                    <th>Nota del cliente</th>
                                    <th>Solicitada</th>
                                    <th class="text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendientes as $p)
                                    <tr>
                                        <td><strong>{{ $p->placa }}</strong></td>
                                        <td>
                                            {{ \App\Http\Controllers\TenantTallerMotos\AppMovilController::celularLegible($p->celular) }}
                                            @if ($p->celular_coincide)
                                                <span class="badge badge-success" title="Ese celular figura en registros de esta moto">celular coincide</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($p->cantidad_registros === 0)
                                                <span class="text-muted">Sin registros en el taller</span>
                                            @else
                                                {{ $p->cantidad_registros }} registro(s) a nombre de:
                                                {{ implode(', ', $p->a_nombre_de) ?: '—' }}
                                            @endif
                                        </td>
                                        <td>{{ $p->nota ?: '—' }}</td>
                                        <td>{{ optional($p->fecha)->format('d/m/Y H:i') }}</td>
                                        <td class="text-right text-nowrap">
                                            @can('tenant.appmovil.vinculos')
                                                <button type="button" class="btn btn-success btn-sm accion-vinculo"
                                                    data-url="{{ tenant_url('tenant.appmovil.vinculos.aprobar', ['id' => $p->id]) }}"
                                                    data-confirmar="¿Aprobar el vínculo? Esa cuenta verá TODO el historial de la moto {{ $p->placa }}.">
                                                    <i class="fa fa-check"></i> Aprobar
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm accion-vinculo"
                                                    data-url="{{ tenant_url('tenant.appmovil.vinculos.rechazar', ['id' => $p->id]) }}"
                                                    data-confirmar="¿Rechazar la solicitud de la moto {{ $p->placa }}?">
                                                    <i class="fa fa-times"></i> Rechazar
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- CONFIGURACION --}}
        @can('tenant.appmovil.config')
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Qué ve el cliente</h5>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="cfg_precios" @checked($mostrarPrecios)>
                        <label class="custom-control-label" for="cfg_precios">Mostrar el precio de los repuestos en el detalle de la orden</label>
                    </div>
                </div>
            </div>
        @endcan

        {{-- INVITACIONES --}}
        @can('tenant.appmovil.invitaciones')
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Invitar a un cliente</h5>
                    <p class="text-muted small">
                        Es la forma más segura de vincular una moto: escribe la placa, entrégale el código al cliente en el
                        mostrador y él lo ingresa en la app. Sirve una sola vez y vence a los 7 días.
                    </p>

                    <form id="form_invitacion" class="form-inline">
                        <input type="text" id="invitacion_placa" class="form-control mr-2 text-uppercase"
                            placeholder="Placa (ej. ABC-123)" maxlength="15" required>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-key"></i> Generar código</button>
                    </form>

                    <div id="resultado_invitacion" class="mt-3" style="display:none;">
                        <div class="alert alert-success mb-0">
                            Código para la moto <strong id="inv_placa"></strong>:
                            <span id="inv_codigo" style="font-size:1.6rem; letter-spacing:.15em; font-weight:700;"></span>
                            <small class="d-block mt-1">Vence el <span id="inv_vence"></span>. Anótalo ahora: no se vuelve a mostrar.</small>
                        </div>
                    </div>

                    @if ($invitaciones->isNotEmpty())
                        <div class="table-responsive mt-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Placa</th><th>Creada</th><th>Vence</th><th>Estado</th><th></th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($invitaciones as $i)
                                        <tr>
                                            <td><strong>{{ $i->placa }}</strong></td>
                                            <td>{{ optional($i->creada)->format('d/m/Y H:i') }}</td>
                                            <td>{{ optional($i->vence)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge badge-{{ ['Vigente' => 'success', 'Usada' => 'secondary', 'Vencida' => 'light', 'Revocada' => 'dark'][$i->estado] }}">{{ $i->estado }}</span>
                                            </td>
                                            <td class="text-right">
                                                @if ($i->estado === 'Vigente')
                                                    <button type="button" class="btn btn-outline-danger btn-sm accion-vinculo"
                                                        data-url="{{ tenant_url('tenant.appmovil.invitaciones.revocar', ['id' => $i->id]) }}"
                                                        data-confirmar="¿Revocar esta invitación?">Revocar</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endcan

        {{-- ACCESOS ACTIVOS --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Motos vinculadas
                    <span class="badge badge-secondary">{{ $activos->count() }}</span>
                </h5>

                @if ($activos->isEmpty())
                    <p class="text-muted mb-0">Todavía no hay motos vinculadas desde la app.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr><th>Placa</th><th>Celular de la cuenta</th><th>Cómo se vinculó</th><th>Alcance</th><th>Desde</th><th></th></tr>
                            </thead>
                            <tbody>
                                @foreach ($activos as $a)
                                    <tr>
                                        <td><strong>{{ $a->placa }}</strong></td>
                                        <td>{{ \App\Http\Controllers\TenantTallerMotos\AppMovilController::celularLegible($a->celular) }}</td>
                                        <td>{{ ['TELEFONO' => 'Por su celular', 'INVITACION' => 'Invitación', 'APROBACION' => 'Aprobada por el taller'][$a->metodo] ?? '—' }}</td>
                                        <td>
                                            @if ($a->alcance === 'COMPLETO')
                                                Todo el historial
                                            @else
                                                <span title="Solo ve los registros hechos con su celular">Solo sus registros</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($a->fecha)->format('d/m/Y') }}</td>
                                        <td class="text-right">
                                            @can('tenant.appmovil.vinculos')
                                                <button type="button" class="btn btn-outline-danger btn-sm accion-vinculo"
                                                    data-url="{{ tenant_url('tenant.appmovil.vinculos.revocar', ['id' => $a->id]) }}"
                                                    data-confirmar="¿Quitar el acceso de esta cuenta a la moto {{ $a->placa }}?">
                                                    Quitar acceso
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            function mostrarError(xhr) {
                var msg = (xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message)) || 'No se pudo completar la acción.';
                Swal.fire({ icon: 'error', title: 'No se pudo', text: msg });
            }

            // Aprobar / rechazar / revocar: confirma y recarga la lista.
            $('body').on('click', '.accion-vinculo', function() {
                var url = $(this).data('url');
                Swal.fire({
                    icon: 'question', text: $(this).data('confirmar'),
                    showCancelButton: true, confirmButtonText: 'Sí, continuar', cancelButtonText: 'Cancelar'
                }).then(function(r) {
                    if (!r.isConfirmed && !r.value) { return; }
                    $.post(url, {}, function(data) {
                        Swal.fire({ icon: 'success', text: data.success, timer: 1600, showConfirmButton: false })
                            .then(function() { window.location.reload(); });
                    }).fail(mostrarError);
                });
            });

            $('#cfg_precios').on('change', function() {
                var caja = $(this);
                $.post("{{ route('tenant.appmovil.configuracion') }}", { mostrar_precios: caja.is(':checked') ? 1 : 0 }, function(data) {
                    Swal.fire({ icon: 'success', text: data.success, timer: 1200, showConfirmButton: false });
                }).fail(function(xhr) { caja.prop('checked', !caja.is(':checked')); mostrarError(xhr); });
            });

            // Generar invitación: el código se muestra una sola vez.
            $('#form_invitacion').on('submit', function(e) {
                e.preventDefault();
                $.post("{{ tenant_url('tenant.appmovil.invitaciones.store') }}", { placa: $('#invitacion_placa').val() }, function(data) {
                    $('#inv_placa').text(data.placa);
                    $('#inv_codigo').text(data.codigo);
                    $('#inv_vence').text(data.vence);
                    $('#resultado_invitacion').show();
                    $('#invitacion_placa').val('');
                }).fail(mostrarError);
            });
        });
    </script>
@endsection
