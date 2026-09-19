@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Reportes - Utilidad por Categoría')
@section('contenido')

    @include('tenant_generico.reportes._estilos')

    <div class="col-12">

        @include('tenant_generico.reportes._filtros')

        <div class="card rpt-card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="fas fa-tags mr-1" style="color:#6C3BFF;"></i>
                    Utilidad por Categoría
                </h6>
                <div class="rpt-chart-wrap">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

        <div class="card rpt-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list mr-1" style="color:#6C3BFF;"></i>
                        DETALLE POR CATEGORÍA
                    </h5>
                    <button type="button" class="btn btn-sm rpt-btn-exportar" onclick="exportarCsv()">
                        <i class="fas fa-file-excel mr-1"></i> Exportar a Excel
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover rpt-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th class="text-right">Vendido</th>
                                <th class="text-right">Costo</th>
                                <th class="text-right">Utilidad</th>
                                <th class="text-right">Margen</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCategorias"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @include('tenant_generico.reportes._reportes_helpers')
    <script>
        const URL_DATOS_REPORTES = "{{ tenant_url('tenant.reportes.generico.datos') }}";
        const URL_EXPORTAR = "{{ tenant_url('tenant.reportes.generico.exportar', ['tipo' => 'categorias']) }}";

        const COLOR_MORADO = '#6C3BFF';
        const COLOR_MORADO_CLARO = 'rgba(108, 59, 255, 0.15)';
        const COLOR_VERDE = '#16A34A';

        let chartCategorias = null;

        function pintarChartCategorias(utilidadPorCategoria) {
            if (chartCategorias) {
                chartCategorias.destroy();
            }

            chartCategorias = new Chart(document.getElementById('chartCategorias'), {
                type: 'bar',
                data: {
                    labels: utilidadPorCategoria.map(c => c.nombre),
                    datasets: [
                        { label: 'Vendido', data: utilidadPorCategoria.map(c => c.importe), backgroundColor: COLOR_MORADO_CLARO, borderColor: COLOR_MORADO, borderWidth: 1 },
                        { label: 'Utilidad', data: utilidadPorCategoria.map(c => c.utilidad), backgroundColor: COLOR_VERDE },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                }
            });
        }

        function pintarTablaCategorias(utilidadPorCategoria) {
            let html = '';

            if (!utilidadPorCategoria.length) {
                html = '<tr><td colspan="5" class="rpt-empty">Sin ventas en este rango.</td></tr>';
            }

            utilidadPorCategoria.forEach(c => {
                html += '<tr>' +
                    '<td>' + c.nombre + '</td>' +
                    '<td class="text-right">' + moneyFmt(c.importe) + '</td>' +
                    '<td class="text-right">' + moneyFmt(c.costo) + '</td>' +
                    '<td class="text-right"><strong>' + moneyFmt(c.utilidad) + '</strong></td>' +
                    '<td class="text-right"><span class="rpt-pill">' + c.margen + '%</span></td>' +
                    '</tr>';
            });

            $('#tablaCategorias').html(html);
        }

        function pintarReporte(data) {
            pintarChartCategorias(data.utilidadPorCategoria);
            pintarTablaCategorias(data.utilidadPorCategoria);
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
            pintarReporte({
                utilidadPorCategoria: @json($utilidadPorCategoria),
            });
        });
    </script>
@endsection
