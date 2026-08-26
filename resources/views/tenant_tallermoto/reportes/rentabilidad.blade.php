@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Rentabilidad')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">RENTABILIDAD</h4>
                        <p class="text-muted mb-0">Ingresos, costo real de lo vendido, gastos y utilidad neta del periodo.</p>
                    </div>
                </div>

                <!-- FILTROS -->
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="mb-1">Periodo</label>
                                <div class="btn-group btn-group-toggle d-flex">
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn active" data-periodo="hoy">Hoy</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="semana">Esta semana</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="mes">Este mes</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="personalizado">Personalizado</button>
                                </div>
                            </div>
                            <div class="col-md-3 fecha-personalizada" style="display:none;">
                                <label>Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio">
                            </div>
                            <div class="col-md-3 fecha-personalizada" style="display:none;">
                                <label>Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="mb-1">Sede</label>
                                <select class="form-control" id="almacen_id">
                                    <option value="">Todas las sedes</option>
                                    @foreach ($almacenes as $alm)
                                        <option value="{{ $alm->ALM_Id }}">{{ $alm->ALM_NombreAlmacen }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-primary"><i class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ingresos</span>
                                <span class="info-box-number" id="kpi_ingresos">S/ 0.00</span>
                                <span class="small" id="kpi_ingresos_var"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-truck-loading"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Costo de lo vendido</span>
                                <span class="info-box-number" id="kpi_costo">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Gastos del periodo</span>
                                <span class="info-box-number" id="kpi_gastos">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success"><i class="fas fa-hand-holding-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Utilidad neta</span>
                                <span class="info-box-number" id="kpi_utilidad">S/ 0.00</span>
                                <span class="small" id="kpi_utilidad_var"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRAFICO -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Ingresos, costo y utilidad por día</h6>
                        <canvas id="chartRentabilidad" height="90"></canvas>
                        <p id="sinDatosChart" class="text-muted text-center mt-3" style="display:none;">
                            No hay ventas registradas en este periodo.
                        </p>
                    </div>
                </div>

                <!-- TABLA POR PRODUCTO -->
                <h6 class="mb-2">Top 15 productos por ingreso (con su margen)</h6>
                <div class="table-responsive mb-2">
                    <table class="table table-hover table-bordered table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Unidades</th>
                                <th class="text-end">Ingreso</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Utilidad</th>
                                <th class="text-center">Margen</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_productos_body">
                            <tr>
                                <td colspan="6" class="text-center text-muted">Cargando...</td>
                            </tr>
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
            var chart = null;

            function periodoActual() {
                return $('.periodo-btn.active').data('periodo');
            }

            function fmt(n) {
                return 'S/ ' + (parseFloat(n) || 0).toFixed(2);
            }

            function cargarDatos() {
                var periodo = periodoActual();
                var params = { periodo: periodo, almacen_id: $('#almacen_id').val() };

                if (periodo === 'personalizado') {
                    if (!$('#fecha_inicio').val() || !$('#fecha_fin').val()) return;
                    params.fecha_inicio = $('#fecha_inicio').val();
                    params.fecha_fin = $('#fecha_fin').val();
                }

                $('#tabla_productos_body').html('<tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>');

                $.get("{{ route('tenant.reportes.rentabilidad') }}", params, function (data) {
                    pintarKpis(data);
                    pintarTabla(data.porProducto);
                    pintarGrafico(data.serie);
                }).fail(function () {
                    $('#tabla_productos_body').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar el reporte.</td></tr>');
                });
            }

            function pintarVariacion($el, valor) {
                if (valor > 0) {
                    $el.html('<i class="fa fa-arrow-up text-success"></i> <span class="text-success">' + valor + '%</span> vs periodo anterior');
                } else if (valor < 0) {
                    $el.html('<i class="fa fa-arrow-down text-danger"></i> <span class="text-danger">' + valor + '%</span> vs periodo anterior');
                } else {
                    $el.html('<span class="text-muted">Sin variación vs periodo anterior</span>');
                }
            }

            function pintarKpis(data) {
                $('#kpi_ingresos').text(fmt(data.actual.ingresos));
                $('#kpi_costo').text(fmt(data.actual.costo));
                $('#kpi_gastos').text(fmt(data.actual.gastos));
                $('#kpi_utilidad').text(fmt(data.actual.utilidadNeta));
                pintarVariacion($('#kpi_ingresos_var'), data.variacion.ingresos);
                pintarVariacion($('#kpi_utilidad_var'), data.variacion.utilidadNeta);
            }

            function pintarTabla(productos) {
                if (!productos.length) {
                    $('#tabla_productos_body').html('<tr><td colspan="6" class="text-center text-muted">Sin ventas en este periodo.</td></tr>');
                    return;
                }

                var html = '';
                productos.forEach(function (p) {
                    var colorMargen = p.margen_pct >= 30 ? 'success' : (p.margen_pct >= 10 ? 'warning' : 'danger');
                    html += '<tr>';
                    html += '<td>' + $('<div>').text(p.PRO_Nombre).html() + '</td>';
                    html += '<td class="text-center">' + p.unidades + '</td>';
                    html += '<td class="text-end">' + fmt(p.ingreso) + '</td>';
                    html += '<td class="text-end">' + fmt(p.costo) + '</td>';
                    html += '<td class="text-end"><strong>' + fmt(p.utilidad) + '</strong></td>';
                    html += '<td class="text-center"><span class="badge badge-' + colorMargen + '">' + p.margen_pct + '%</span></td>';
                    html += '</tr>';
                });
                $('#tabla_productos_body').html(html);
            }

            function pintarGrafico(serie) {
                var ctx = document.getElementById('chartRentabilidad').getContext('2d');

                if (!serie.length) {
                    $('#chartRentabilidad').hide();
                    $('#sinDatosChart').show();
                    if (chart) { chart.destroy(); chart = null; }
                    return;
                }

                $('#chartRentabilidad').show();
                $('#sinDatosChart').hide();

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: serie.map(function (s) { return s.fecha; }),
                        datasets: [
                            { label: 'Ingreso', data: serie.map(s => s.ingreso), borderColor: '#3c8dbc', fill: false },
                            { label: 'Costo', data: serie.map(s => s.costo), borderColor: '#dd4b39', fill: false },
                            { label: 'Utilidad', data: serie.map(s => s.utilidad), borderColor: '#00a65a', fill: false }
                        ]
                    },
                    options: {
                        scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
                    }
                });
            }

            $('.periodo-btn').click(function () {
                $('.periodo-btn').removeClass('active');
                $(this).addClass('active');
                if ($(this).data('periodo') === 'personalizado') {
                    $('.fecha-personalizada').show();
                } else {
                    $('.fecha-personalizada').hide();
                    cargarDatos();
                }
            });

            $('#fecha_inicio, #fecha_fin').change(function () {
                if (periodoActual() === 'personalizado') cargarDatos();
            });

            $('#almacen_id').change(cargarDatos);

            cargarDatos();
        });
    </script>
@endsection
