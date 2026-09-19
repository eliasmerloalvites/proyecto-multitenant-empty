@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Reportes - Utilidad por Producto')
@section('contenido')

    @include('tenant_generico.reportes._estilos')

    <div class="col-12">

        @include('tenant_generico.reportes._filtros')

        <!-- Tarjetas resumen: de que es la suma que se ve abajo -->
        <div class="rpt-kpi-row cuatro" id="rptKpis">
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Total Vendido</div>
                <div class="rpt-kpi-valor" id="kpiTotalVendido">S/ {{ number_format($resumen['totalVendido'], 2) }}</div>
            </div>
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Costo</div>
                <div class="rpt-kpi-valor" id="kpiTotalCosto">S/ {{ number_format($resumen['totalCosto'], 2) }}</div>
            </div>
            <div class="rpt-kpi-tile destacado">
                <div class="rpt-kpi-label">Utilidad</div>
                <div class="rpt-kpi-valor" id="kpiUtilidad">S/ {{ number_format($resumen['utilidad'], 2) }}</div>
            </div>
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Margen</div>
                <div class="rpt-kpi-valor" id="kpiMargen">{{ $resumen['margen'] }}%</div>
            </div>
        </div>

        <div class="card rpt-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box-open mr-1" style="color:#6C3BFF;"></i>
                        UTILIDAD POR PRODUCTO
                    </h5>
                    <button type="button" class="btn btn-sm rpt-btn-exportar" onclick="exportarCsv()">
                        <i class="fas fa-file-excel mr-1"></i> Exportar a Excel
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover rpt-table" id="tabla_utilidad_productos" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-right">Unidades</th>
                                <th class="text-right">P. Venta Prom.</th>
                                <th class="text-right">P. Compra Prom.</th>
                                <th class="text-right">Vendido</th>
                                <th class="text-right">Costo</th>
                                <th class="text-right">Utilidad</th>
                                <th class="text-right">Margen</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    @include('tenant_generico.reportes._reportes_helpers')
    <script>
        const URL_DATOS_REPORTES = "{{ tenant_url('tenant.reportes.generico.datos') }}";
        const URL_EXPORTAR = "{{ tenant_url('tenant.reportes.generico.exportar', ['tipo' => 'utilidad']) }}";

        let tablaUtilidad;

        function actualizarKpis(resumen) {
            $('#kpiTotalVendido').text(moneyFmt(resumen.totalVendido));
            $('#kpiTotalCosto').text(moneyFmt(resumen.totalCosto));
            $('#kpiUtilidad').text(moneyFmt(resumen.utilidad));
            $('#kpiMargen').text(resumen.margen + '%');
        }

        function pintarReporte(data) {
            actualizarKpis(data.resumen);
            tablaUtilidad.clear();
            tablaUtilidad.rows.add(data.todosProductos);
            tablaUtilidad.draw();
        }

        function buscarReporte() {
            $.ajax({
                url: URL_DATOS_REPORTES,
                method: 'GET',
                data: {
                    fecha_desde: $('#filtroFechaDesde').val(),
                    fecha_hasta: $('#filtroFechaHasta').val(),
                    almacen_id: $('#filtroAlmacen').length ? $('#filtroAlmacen').val() : ''
                },
                success: function(response) {
                    pintarReporte(response);
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'No se pudo generar el reporte' });
                }
            });
        }

        function exportarCsv() {
            window.open(urlConFiltros(URL_EXPORTAR), '_blank');
        }

        $(document).ready(function() {
            tablaUtilidad = $('#tabla_utilidad_productos').DataTable({
                data: @json($todosProductos),
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[7, 'desc']],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros',
                    zeroRecords: 'Sin ventas en este rango',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'No hay registros',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar:',
                    paginate: { next: 'Siguiente', previous: 'Anterior' }
                },
                columns: [
                    { data: 'nombre' },
                    { data: 'categoria' },
                    { data: 'unidades', className: 'text-right' },
                    { data: 'precioVentaProm', className: 'text-right', render: moneyFmt },
                    { data: 'precioCompraProm', className: 'text-right', render: moneyFmt },
                    { data: 'importe', className: 'text-right', render: moneyFmt },
                    { data: 'costo', className: 'text-right', render: moneyFmt },
                    { data: 'utilidad', className: 'text-right', render: (v) => '<strong>' + moneyFmt(v) + '</strong>' },
                    { data: 'margen', className: 'text-right', render: (v) => '<span class="rpt-pill">' + v + '%</span>' },
                ]
            });
        });
    </script>
@endsection
