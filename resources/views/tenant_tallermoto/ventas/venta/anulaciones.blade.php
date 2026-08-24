@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Anulaciones')
@section('contenido')

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Comunicaciones de baja y resúmenes diarios</h5>
                <a href="{{ tenant_url('tenant.ventas.venta.index') }}" class="btn btn-light btn-sm">
                    <i class="fa fa-arrow-left"></i> Volver a ventas
                </a>
            </div>
            <p class="text-muted small mb-3">
                Cada anulación que se solicita, aceptada o no. SUNAT usa un mecanismo distinto
                segun el documento: resumen diario para boletas, comunicación de baja para
                facturas y notas de crédito.
            </p>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover" id="tablaAnulaciones">
                    <thead>
                        <tr>
                            <th>Documento anulado</th>
                            <th>Mecanismo</th>
                            <th>Cliente</th>
                            <th>Motivo</th>
                            <th>Ticket</th>
                            <th>Estado</th>
                            <th>Solicitado</th>
                            <th>Resuelto</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anulaciones as $a)
                            @php
                                $estados = [
                                    'ACEPTADO'  => 'success',
                                    'RECHAZADO' => 'danger',
                                    'ERROR'     => 'danger',
                                    'PENDIENTE' => 'secondary',
                                ];
                                $color = $estados[$a->DOV_EstadoBaja] ?? 'secondary';
                                $mecanismo = $a->DOV_Tipo === 'BOL' ? 'Resumen diario (RC)' : 'Comunicación de baja (RA)';
                            @endphp
                            <tr>
                                <td><strong>{{ $a->DOV_Nombre }}</strong></td>
                                <td>{{ $mecanismo }}</td>
                                <td>{{ $a->CLI_Nombre }}</td>
                                <td title="{{ $a->DOV_MotivoBaja }}">{{ $a->DOV_MotivoBaja }}</td>
                                <td><code>{{ $a->DOV_TicketBaja ?: '—' }}</code></td>
                                <td>
                                    <span class="badge badge-{{ $color }}" title="{{ $a->DOV_DescripcionBaja }}">
                                        {{ $a->DOV_EstadoBaja }}
                                    </span>
                                </td>
                                <td>{{ $a->DOV_FechaSolicitudBaja }}</td>
                                <td>{{ $a->DOV_FechaRespuestaBaja ?: '—' }}</td>
                                <td class="text-center">
                                    @if ($a->DOV_EstadoBaja === 'PENDIENTE')
                                        <button type="button" class="btn btn-outline-info btn-sm bajaConsultar"
                                                data-id="{{ $a->VEN_Id }}" title="Consultar resultado">
                                            <i class="fa fa-history"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Todavía no se ha solicitado ninguna anulación.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('#tablaAnulaciones').DataTable({
                responsive: true,
                autoWidth: false,
                order: [],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    zeroRecords: 'Nada encontrado',
                    info: 'Mostrando la página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar:',
                    paginate: { next: 'Siguiente', previous: 'Anterior' }
                }
            });

            $('body').on('click', '.bajaConsultar', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Consultando a SUNAT...',
                    allowOutsideClick: false,
                    didOpen: function () { Swal.showLoading(); }
                });

                $.ajax({
                    url: '/tenant/ventas/venta/' + id + '/anular/consultar',
                    method: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') }
                }).done(function (r) {
                    Swal.fire({
                        icon: r.estado === 'ACEPTADO' ? 'success' : (r.estado === 'RECHAZADO' ? 'error' : 'info'),
                        title: 'Anulación: ' + (r.estado || 'sin resultado aún'),
                        text: r.descripcion || ''
                    }).then(function () { location.reload(); });
                }).fail(function (xhr) {
                    var r = xhr.responseJSON || {};
                    Swal.fire({
                        icon: 'info',
                        title: 'Aún sin resultado',
                        text: r.descripcion || 'SUNAT todavía no responde; intenta de nuevo en unos minutos.'
                    });
                });
            });
        });
    </script>
@endsection
