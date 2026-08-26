@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Guías de remisión')
@section('contenido')

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Guías de remisión emitidas</h5>
                <a href="{{ tenant_url('tenant.ventas.venta.index') }}" class="btn btn-light btn-sm">
                    <i class="fa fa-arrow-left"></i> Volver a ventas
                </a>
            </div>

            <div class="alert alert-warning py-2 px-3 small mb-3">
                <i class="fa fa-flask"></i>
                El botón <strong>"Simular"</strong> marca la guía como aceptada sin enviarla a SUNAT — solo para
                probar el registro y el documento impreso mientras el envío real está pendiente de configurar
                (client_id/client_secret de la API GRE). Solo funciona en ambiente <strong>BETA</strong>.
            </div>

            <form method="GET" action="{{ tenant_url('tenant.ventas.guiaremision.index') }}" class="row g-2 mb-3">
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select name="estado" class="form-control">
                        <option value="">Estado: Todos</option>
                        @foreach (['ACEPTADO' => 'Aceptado', 'RECHAZADO' => 'Rechazado', 'ERROR' => 'Error', 'PENDIENTE' => 'Pendiente'] as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected(($filtros['estado'] ?? '') === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select name="modo_transporte" class="form-control">
                        <option value="">Transporte: Todos</option>
                        <option value="01" @selected(($filtros['modo_transporte'] ?? '') === '01')>Publico</option>
                        <option value="02" @selected(($filtros['modo_transporte'] ?? '') === '02')>Privado</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <input type="date" name="fecha_inicio" class="form-control" placeholder="Desde"
                        value="{{ $filtros['fecha_inicio'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <input type="date" name="fecha_fin" class="form-control" placeholder="Hasta"
                        value="{{ $filtros['fecha_fin'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <input type="text" name="cliente" class="form-control" placeholder="Cliente: nombre o documento"
                        value="{{ $filtros['cliente'] ?? '' }}">
                </div>
                <div class="col-lg-1 col-md-2 col-sm-6">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i></button>
                </div>
                @if (array_filter($filtros ?? []))
                    <div class="col-12">
                        <a href="{{ tenant_url('tenant.ventas.guiaremision.index') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-eraser"></i> Quitar filtros
                        </a>
                    </div>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover" id="tablaGuias">
                    <thead>
                        <tr>
                            <th>Guía</th>
                            <th>Fecha traslado</th>
                            <th>Cliente</th>
                            <th>Transporte</th>
                            <th>Estado</th>
                            <th class="text-center">SUNAT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guias as $g)
                            @php
                                $estados = [
                                    'ACEPTADO'  => 'success',
                                    'RECHAZADO' => 'danger',
                                    'ERROR'     => 'danger',
                                    'PENDIENTE' => 'secondary',
                                ];
                                $color = $estados[$g->GRM_Estado] ?? 'secondary';
                                $transporte = $g->GRM_ModoTransporte === '01' ? 'Público' : 'Privado';
                            @endphp
                            <tr>
                                <td><strong>{{ $g->GRM_Nombre }}</strong></td>
                                <td>{{ \Illuminate\Support\Carbon::parse($g->GRM_FechaTraslado)->format('d/m/Y') }}</td>
                                <td>{{ $g->CLI_Nombre }}</td>
                                <td>{{ $transporte }}</td>
                                <td><span class="badge badge-{{ $color }}">{{ $g->GRM_Estado }}</span></td>
                                <td class="text-center">
                                    @if ($g->GRM_Estado === 'ACEPTADO')
                                        <a class="btn btn-outline-secondary btn-sm" title="Ver / imprimir documento"
                                           href="{{ tenant_url('tenant.ventas.guiaremision.imprimir', ['id' => $g->GRM_Id]) }}" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <a class="btn btn-outline-info btn-sm" title="Descargar XML"
                                           href="{{ tenant_url('tenant.ventas.guiaremision.sunat.xml', ['id' => $g->GRM_Id]) }}">
                                            <i class="fa fa-file-code"></i>
                                        </a>
                                        <a class="btn btn-outline-info btn-sm" title="Descargar CDR"
                                           href="{{ tenant_url('tenant.ventas.guiaremision.sunat.cdr', ['id' => $g->GRM_Id]) }}">
                                            <i class="fa fa-file-archive"></i>
                                        </a>
                                    @elseif ($g->GRM_Estado === 'PENDIENTE')
                                        <button type="button" class="btn btn-outline-primary btn-sm consultarTicket"
                                                data-id="{{ $g->GRM_Id }}" title="Consultar ticket en SUNAT">
                                            <i class="fa fa-search"></i> Consultar
                                        </button>
                                    @endif

                                    @if ($g->GRM_Estado !== 'ACEPTADO')
                                        <button type="button" class="btn btn-outline-warning btn-sm simularAceptar"
                                                data-id="{{ $g->GRM_Id }}" title="Simular aceptación (solo pruebas, ambiente BETA)">
                                            <i class="fa fa-flask"></i> Simular
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Todavía no se ha emitido ninguna guía de remisión.
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
            // Los handlers de los botones van primero: si la inicializacion de
            // DataTable de mas abajo llegara a fallar por lo que sea, los
            // clicks igual quedan funcionando (un error ahi no debe tumbar
            // el resto del script).
            $('body').on('click', '.consultarTicket', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Consultando el ticket en SUNAT...',
                    allowOutsideClick: false,
                    didOpen: function () { Swal.showLoading(); }
                });

                $.ajax({
                    url: '{{ tenant_url("tenant.ventas.guiaremision.consultar", ["id" => ":id"]) }}'.replace(':id', id),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' }
                }).done(function (r) {
                    if (r.estado === 'PENDIENTE') {
                        Swal.fire({ icon: 'info', title: 'Todavía en proceso', text: 'SUNAT aún no resuelve esta guía; vuelve a intentar en un momento.' });
                        return;
                    }

                    Swal.fire({
                        icon: r.success ? 'success' : 'error',
                        title: r.success ? 'Guía aceptada' : 'Guía rechazada',
                        text: r.descripcion || ''
                    }).then(function () {
                        window.location.reload();
                    });
                }).fail(function (xhr) {
                    var r = xhr.responseJSON || {};
                    Swal.fire({ icon: 'error', title: 'No se pudo consultar', text: r.descripcion || 'Error de conexión.' });
                });
            });

            $('body').on('click', '.simularAceptar', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Simulando aceptación en SUNAT...',
                    allowOutsideClick: false,
                    didOpen: function () { Swal.showLoading(); }
                });

                $.ajax({
                    url: '{{ tenant_url("tenant.ventas.guiaremision.simular", ["id" => ":id"]) }}'.replace(':id', id),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' }
                }).done(function (r) {
                    Swal.fire({
                        icon: r.success ? 'success' : 'error',
                        title: r.success ? 'Guía marcada como aceptada (SIMULADO)' : 'No se pudo simular',
                        text: r.descripcion || ''
                    }).then(function () {
                        window.location.reload();
                    });
                }).fail(function (xhr) {
                    var r = xhr.responseJSON || {};
                    Swal.fire({ icon: 'error', title: 'No se pudo simular', text: r.descripcion || 'Error de conexión.' });
                });
            });

            try {
                $('#tablaGuias').DataTable({
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
            } catch (e) {
                console.error('No se pudo inicializar la tabla de guías (los botones de acción igual funcionan):', e);
            }
        });
    </script>
@endsection
