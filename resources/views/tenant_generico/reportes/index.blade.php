@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Reportes - Resumen General')
@section('contenido')

    @include('tenant_generico.reportes._estilos')

    <div class="col-12">

        @include('tenant_generico.reportes._filtros')

        <!-- KPIs -->
        <div class="rpt-kpi-row" id="rptKpis">
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">N° Ventas</div>
                <div class="rpt-kpi-valor" id="kpiNumVentas">{{ $resumen['numVentas'] }}</div>
            </div>
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
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Ticket Promedio</div>
                <div class="rpt-kpi-valor" id="kpiTicketPromedio">S/ {{ number_format($resumen['ticketPromedio'], 2) }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-3">
                <div class="card rpt-card h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-chart-line mr-1" style="color:#6C3BFF;"></i>
                            Ventas vs Utilidad
                        </h6>
                        <div class="rpt-chart-wrap">
                            <canvas id="chartTiempo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card rpt-card h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-wallet mr-1" style="color:#6C3BFF;"></i>
                            Métodos de Pago
                        </h6>
                        <div class="rpt-chart-wrap chico">
                            <canvas id="chartMetodosPago"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <p class="text-muted" style="font-size:13px;">
                    ¿Buscas el detalle por producto, el ranking de clientes o el detalle línea por línea?
                    Usa las demás opciones del menú "REPORTES".
                </p>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @include('tenant_generico.reportes._reportes_helpers')
    <script>
        const URL_DATOS_REPORTES = "{{ tenant_url('tenant.reportes.generico.datos') }}";

        const COLOR_MORADO = '#6C3BFF';
        const COLOR_MORADO_CLARO = 'rgba(108, 59, 255, 0.15)';
        const COLOR_VERDE = '#16A34A';
        const PALETA = ['#6C3BFF', '#8B5CF6', '#16A34A', '#F59E0B', '#EF4444', '#0EA5E9', '#EC4899', '#14B8A6', '#F97316', '#64748B'];

        let charts = {};

        function destruirChart(id) {
            if (charts[id]) {
                charts[id].destroy();
                delete charts[id];
            }
        }

        function pintarChartTiempo(serieTiempo) {
            destruirChart('tiempo');
            charts.tiempo = new Chart(document.getElementById('chartTiempo'), {
                type: 'line',
                data: {
                    labels: serieTiempo.puntos.map(p => p.etiqueta),
                    datasets: [
                        {
                            label: 'Ventas',
                            data: serieTiempo.puntos.map(p => p.ventas),
                            borderColor: COLOR_MORADO,
                            backgroundColor: COLOR_MORADO_CLARO,
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: 'Utilidad',
                            data: serieTiempo.puntos.map(p => p.utilidad),
                            borderColor: COLOR_VERDE,
                            backgroundColor: 'rgba(22, 163, 74, 0.12)',
                            fill: true,
                            tension: 0.3,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: {
                            ticks: { callback: (v) => 'S/ ' + v }
                        }
                    }
                }
            });
        }

        function pintarChartDoughnut(id, canvasId, labels, data) {
            destruirChart(id);
            charts[id] = new Chart(document.getElementById(canvasId), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: PALETA,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                }
            });
        }

        function actualizarKpis(resumen) {
            $('#kpiNumVentas').text(resumen.numVentas);
            $('#kpiTotalVendido').text(moneyFmt(resumen.totalVendido));
            $('#kpiTotalCosto').text(moneyFmt(resumen.totalCosto));
            $('#kpiUtilidad').text(moneyFmt(resumen.utilidad));
            $('#kpiMargen').text(resumen.margen + '%');
            $('#kpiTicketPromedio').text(moneyFmt(resumen.ticketPromedio));
        }

        function pintarReporte(data) {
            actualizarKpis(data.resumen);
            pintarChartTiempo(data.serieTiempo);
            pintarChartDoughnut('metodosPago', 'chartMetodosPago', data.metodosPago.map(m => m.nombre), data.metodosPago.map(m => m.importe));
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

        $(document).ready(function() {
            pintarReporte({
                resumen: @json($resumen),
                serieTiempo: @json($serieTiempo),
                metodosPago: @json($metodosPago),
            });
        });
    </script>
@endsection
