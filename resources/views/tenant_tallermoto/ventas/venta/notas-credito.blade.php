@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Notas de crédito')
@section('contenido')

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Notas de crédito emitidas</h5>
                <a href="{{ tenant_url('tenant.ventas.venta.index') }}" class="btn btn-light btn-sm">
                    <i class="fa fa-arrow-left"></i> Volver a ventas
                </a>
            </div>

            <form method="GET" action="{{ tenant_url('tenant.ventas.notas-credito.index') }}" class="row g-2 mb-3">
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select name="estado" class="form-control">
                        <option value="">Estado: Todos</option>
                        @foreach (['ACEPTADO' => 'Aceptado', 'OBSERVADO' => 'Aceptado con obs.', 'RECHAZADO' => 'Rechazado', 'ERROR' => 'No enviado (error)', 'PENDIENTE' => 'Aun no enviado'] as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected(($filtros['estado'] ?? '') === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <select name="cod_motivo" class="form-control">
                        <option value="">Motivo: Todos</option>
                        @foreach ($motivos ?? [] as $codigo => $descripcion)
                            <option value="{{ $codigo }}" @selected(($filtros['cod_motivo'] ?? '') === (string) $codigo)>{{ $codigo }} - {{ $descripcion }}</option>
                        @endforeach
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
                        <a href="{{ tenant_url('tenant.ventas.notas-credito.index') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-eraser"></i> Quitar filtros
                        </a>
                    </div>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover" id="tablaNotas">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Afecta a</th>
                            <th>Motivo</th>
                            <th class="text-end">Total</th>
                            <th>Estado</th>
                            <th class="text-center">SUNAT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notas as $n)
                            @php
                                $estados = [
                                    'ACEPTADO'  => 'success',
                                    'OBSERVADO' => 'warning',
                                    'RECHAZADO' => 'danger',
                                    'ERROR'     => 'danger',
                                    'PENDIENTE' => 'secondary',
                                ];
                                $color = $estados[$n->DOV_Estado] ?? 'secondary';
                                $tipoAfectado = $n->DOV_TipoDocAfectado === '01' ? 'Factura' : 'Boleta';
                            @endphp
                            <tr>
                                <td><strong>{{ $n->DOV_Nombre }}</strong></td>
                                <td>{{ $n->fecha }}</td>
                                <td>{{ $n->CLI_Nombre }}</td>
                                <td>{{ $tipoAfectado }} {{ $n->DOV_NumDocAfectado }}</td>
                                <td title="{{ $n->DOV_DesMotivo }}">
                                    {{ $n->DOV_CodMotivo }} - {{ $n->DOV_DesMotivo }}
                                </td>
                                <td class="text-end">S/ {{ number_format($n->total, 2) }}</td>
                                <td><span class="badge badge-{{ $color }}">{{ $n->DOV_Estado }}</span></td>
                                <td class="text-center">
                                    @if (in_array($n->DOV_Estado, ['ACEPTADO', 'OBSERVADO']))
                                        <a class="btn btn-outline-info btn-sm" title="Descargar XML"
                                           href="/tenant/ventas/venta/{{ $n->VEN_Id }}/sunat/xml">
                                            <i class="fa fa-file-code"></i>
                                        </a>
                                        <a class="btn btn-outline-info btn-sm" title="Descargar CDR"
                                           href="/tenant/ventas/venta/{{ $n->VEN_Id }}/sunat/cdr">
                                            <i class="fa fa-file-archive"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-outline-primary btn-sm sunatConsultar"
                                            data-id="{{ $n->VEN_Id }}" title="Consultar estado en SUNAT">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Todavía no se ha emitido ninguna nota de crédito.
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
            $('#tablaNotas').DataTable({
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

            $('body').on('click', '.sunatConsultar', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Consultando a SUNAT...',
                    allowOutsideClick: false,
                    didOpen: function () { Swal.showLoading(); }
                });

                $.get('/tenant/ventas/venta/' + id + '/sunat/consultar')
                    .done(function (r) {
                        Swal.fire({
                            icon: r.success ? 'success' : 'info',
                            title: r.success ? 'Respuesta de SUNAT' : 'No se pudo consultar',
                            html: (r.codigo ? '<b>Codigo ' + r.codigo + '</b><br>' : '') + (r.descripcion || '')
                        });
                    })
                    .fail(function (xhr) {
                        var r = xhr.responseJSON || {};
                        Swal.fire({
                            icon: 'info',
                            title: 'No se pudo consultar',
                            text: r.descripcion || 'Error de conexión.'
                        });
                    });
            });
        });
    </script>
@endsection
