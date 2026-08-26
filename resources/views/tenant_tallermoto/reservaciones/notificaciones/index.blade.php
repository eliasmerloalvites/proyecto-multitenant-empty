@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Notificar reservas')
@section('contenido')

    <div class="col-12">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Configuracion del recordatorio</h5>
                <p class="text-muted small">
                    El envio sigue siendo manual: a cada cliente le das clic a su boton de WhatsApp con el
                    mensaje ya listo. La hora de aqui abajo solo marca desde que momento del dia el panel
                    resalta que ya toca avisar a los clientes de mañana.
                </p>

                <form method="POST" action="{{ tenant_url('tenant.reservaciones.notificaciones.configuracion') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label>Hora de aviso</label>
                            <input type="time" name="reserva_notif_hora" class="form-control"
                                value="{{ old('reserva_notif_hora', substr($empresa->reserva_notif_hora ?? '18:00', 0, 5)) }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="reserva_notif_activo" value="1" class="form-check-input"
                                    id="reserva_notif_activo"
                                    @checked(old('reserva_notif_activo', $empresa->reserva_notif_activo ?? false))>
                                <label class="form-check-label" for="reserva_notif_activo">
                                    Activar recordatorio
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Mensaje predeterminado</label>
                            <textarea name="reserva_notif_mensaje" class="form-control" rows="2">{{ old('reserva_notif_mensaje', $empresa->reserva_notif_mensaje ?? \App\Models\Tenant\EmpresaFacturacion::MENSAJE_RESERVA_DEFECTO) }}</textarea>
                            <small class="text-muted">
                                Marcadores disponibles: {cliente}, {moto}, {placa}, {fecha}, {turno}, {sede}, {empresa}
                            </small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-save"></i> Guardar</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($yaEsHora && ($empresa->reserva_notif_activo ?? false) && $pendientes > 0)
                    <div class="alert alert-warning">
                        <i class="fa fa-bell"></i> Ya es hora de avisar: tienes {{ $pendientes }}
                        {{ $pendientes === 1 ? 'reserva' : 'reservas' }} para mañana ({{ $fechaManana }})
                        pendientes de notificar.
                    </div>
                @endif

                <h5 class="card-title">Reservas de mañana ({{ $fechaManana }})</h5>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Celular</th>
                                <th>Moto / Placa</th>
                                <th>Sede / Turno</th>
                                <th>Mensaje</th>
                                <th>Notificado</th>
                                <th class="text-center">Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reservas as $r)
                                <tr id="fila-reserva-{{ $r->RES_Id }}">
                                    <td>{{ $r->RES_Cliente }}</td>
                                    <td>{{ $r->RES_Celular }}</td>
                                    <td>{{ $r->RES_Moto }} ({{ $r->RES_Placa }})</td>
                                    <td>{{ $r->ALM_NombreAlmacen }} - {{ $r->TUR_Descripcion }}</td>
                                    <td class="small">{{ $r->mensaje }}</td>
                                    <td>
                                        <span class="badge badge-{{ $r->RES_Notificado ? 'success' : 'secondary' }}"
                                            id="badge-notificado-{{ $r->RES_Id }}">
                                            {{ $r->RES_Notificado ? 'Notificado' : 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-success btn-sm enviarWhatsapp" target="_blank"
                                            data-id="{{ $r->RES_Id }}"
                                            href="https://wa.me/51{{ preg_replace('/\D/', '', $r->RES_Celular) }}?text={{ urlencode($r->mensaje) }}">
                                            <i class="fab fa-whatsapp"></i> Enviar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No hay reservas activas para mañana.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(function () {
            $('body').on('click', '.enviarWhatsapp', function () {
                var id = $(this).data('id');

                $.ajax({
                    url: '{{ tenant_url("tenant.reservaciones.notificaciones.marcar", ["id" => ":id"]) }}'.replace(':id', id),
                    method: 'POST',
                    data: { notificado: 1, _token: '{{ csrf_token() }}' }
                }).done(function () {
                    $('#badge-notificado-' + id).removeClass('badge-secondary').addClass('badge-success').text('Notificado');
                });
            });
        });
    </script>
@endsection
