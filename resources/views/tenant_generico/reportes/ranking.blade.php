@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Reportes - Productos y Clientes')
@section('contenido')

    @include('tenant_generico.reportes._estilos')

    <div class="col-12">

        @include('tenant_generico.reportes._filtros')

        <!-- Destacados: el producto mas vendido y el cliente que mas compra, bien a la vista -->
        <div class="row mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="rpt-destacado-card" id="cardProductoEstrella">
                    <div class="rpt-destacado-etiqueta"><i class="fas fa-star mr-1"></i> Producto Estrella</div>
                    <div class="rpt-destacado-nombre" id="destNombreProducto">-</div>
                    <span class="rpt-destacado-metrica" id="destMetricaProducto">-</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="rpt-destacado-card oro" id="cardMejorCliente">
                    <div class="rpt-destacado-etiqueta"><i class="fas fa-crown mr-1"></i> Mejor Cliente</div>
                    <div class="rpt-destacado-nombre" id="destNombreCliente">-</div>
                    <span class="rpt-destacado-metrica" id="destMetricaCliente">-</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="card rpt-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-box mr-1" style="color:#6C3BFF;"></i>
                                Ranking de Productos
                            </h6>
                            <button type="button" class="btn btn-sm rpt-btn-exportar" onclick="exportarCsv('productos')">
                                <i class="fas fa-file-excel mr-1"></i> Exportar
                            </button>
                        </div>
                        <div class="rpt-chart-wrap chico">
                            <canvas id="chartTopProductos"></canvas>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm rpt-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-right">Unid.</th>
                                        <th class="text-right">Vendido</th>
                                        <th class="text-right">Utilidad</th>
                                        <th class="text-right">Margen</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaTopProductos"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card rpt-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-users mr-1" style="color:#6C3BFF;"></i>
                                Ranking de Clientes
                            </h6>
                            <button type="button" class="btn btn-sm rpt-btn-exportar" onclick="exportarCsv('clientes')">
                                <i class="fas fa-file-excel mr-1"></i> Exportar
                            </button>
                        </div>
                        <div class="rpt-chart-wrap chico">
                            <canvas id="chartTopClientes"></canvas>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm rpt-table">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th class="text-right">Compras</th>
                                        <th class="text-right">Total</th>
                                        <th>Última compra</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaTopClientes"></tbody>
                            </table>
                        </div>
                    </div>
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
        const URL_EXPORTAR_PRODUCTOS = "{{ tenant_url('tenant.reportes.generico.exportar', ['tipo' => 'productos']) }}";
        const URL_EXPORTAR_CLIENTES = "{{ tenant_url('tenant.reportes.generico.exportar', ['tipo' => 'clientes']) }}";

        const COLOR_MORADO = '#6C3BFF';

        let charts = {};

        function destruirChart(id) {
            if (charts[id]) {
                charts[id].destroy();
                delete charts[id];
            }
        }

        function pintarChartBarras(id, canvasId, labels, data, color) {
            destruirChart(id);
            charts[id] = new Chart(document.getElementById(canvasId), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{ data: data, backgroundColor: color, borderRadius: 6 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                }
            });
        }

        function pintarTablaProductos(topProductos) {
            let html = '';

            if (!topProductos.length) {
                html = '<tr><td colspan="5" class="rpt-empty">Sin ventas en este rango.</td></tr>';
            }

            topProductos.forEach(p => {
                html += '<tr>' +
                    '<td>' + p.nombre + '</td>' +
                    '<td class="text-right">' + p.unidades + '</td>' +
                    '<td class="text-right">' + moneyFmt(p.importe) + '</td>' +
                    '<td class="text-right">' + moneyFmt(p.utilidad) + '</td>' +
                    '<td class="text-right"><span class="rpt-pill">' + p.margen + '%</span></td>' +
                    '</tr>';
            });

            $('#tablaTopProductos').html(html);
        }

        function pintarTablaClientes(topClientes) {
            let html = '';

            if (!topClientes.length) {
                html = '<tr><td colspan="4" class="rpt-empty">Sin ventas en este rango.</td></tr>';
            }

            topClientes.forEach(c => {
                let fecha = c.ultimaCompra ? new Date(c.ultimaCompra.replace(' ', 'T')).toLocaleDateString('es-PE') : '-';
                html += '<tr>' +
                    '<td>' + c.nombre + '<div class="text-muted" style="font-size:11px;">' + (c.documento || '') + '</div></td>' +
                    '<td class="text-right">' + c.numCompras + '</td>' +
                    '<td class="text-right">' + moneyFmt(c.montoTotal) + '</td>' +
                    '<td>' + fecha + '</td>' +
                    '</tr>';
            });

            $('#tablaTopClientes').html(html);
        }

        function actualizarDestacados(topProductos, topClientes) {
            if (topProductos.length) {
                $('#destNombreProducto').text(topProductos[0].nombre);
                $('#destMetricaProducto').text(topProductos[0].unidades + ' unid. vendidas · ' + moneyFmt(topProductos[0].importe));
            } else {
                $('#destNombreProducto').text('Sin datos en este rango');
                $('#destMetricaProducto').text('-');
            }

            if (topClientes.length) {
                $('#destNombreCliente').text(topClientes[0].nombre);
                $('#destMetricaCliente').text(topClientes[0].numCompras + ' compras · ' + moneyFmt(topClientes[0].montoTotal));
            } else {
                $('#destNombreCliente').text('Sin datos en este rango');
                $('#destMetricaCliente').text('-');
            }
        }

        function pintarReporte(data) {
            actualizarDestacados(data.topProductos, data.topClientes);
            pintarChartBarras('topProductos', 'chartTopProductos', data.topProductos.map(p => p.nombre), data.topProductos.map(p => p.unidades), COLOR_MORADO);
            pintarChartBarras('topClientes', 'chartTopClientes', data.topClientes.map(c => c.nombre), data.topClientes.map(c => c.montoTotal), '#8B5CF6');
            pintarTablaProductos(data.topProductos);
            pintarTablaClientes(data.topClientes);
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

        function exportarCsv(tipo) {
            let url = tipo === 'clientes' ? URL_EXPORTAR_CLIENTES : URL_EXPORTAR_PRODUCTOS;
            window.open(urlConFiltros(url), '_blank');
        }

        $(document).ready(function() {
            pintarReporte({
                topProductos: @json($topProductos),
                topClientes: @json($topClientes),
            });
        });
    </script>
@endsection
