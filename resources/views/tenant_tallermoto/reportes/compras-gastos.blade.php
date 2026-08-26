@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Compras y Gastos')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">COMPRAS Y GASTOS</h4>
                        <p class="text-muted mb-0">A dónde se va la plata: compras de mercadería por proveedor y gastos operativos por tipo.</p>
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
                            <div class="col-md-5">
                                <label class="mb-1">Proveedor</label>
                                <select class="form-control" id="proveedor_id">
                                    <option value="">Todos los proveedores</option>
                                    @foreach ($proveedores as $prov)
                                        <option value="{{ $prov->PROV_Id }}">{{ $prov->PROV_RazonSocial }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="row mb-4">
                    <div class="col-md-4 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-dolly"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Compras del periodo</span>
                                <span class="info-box-number" id="kpi_compras">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Gastos del periodo</span>
                                <span class="info-box-number" id="kpi_gastos">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-danger"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Egresos totales</span>
                                <span class="info-box-number" id="kpi_egresos">S/ 0.00</span>
                                <span class="small" id="kpi_egresos_var"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRAFICO -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Compras vs. gastos por día</h6>
                        <canvas id="chartComprasGastos" height="90"></canvas>
                        <p id="sinDatosChart" class="text-muted text-center mt-3" style="display:none;">
                            No hay compras ni gastos registrados en este periodo.
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Compras por proveedor</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Proveedor</th>
                                        <th class="text-center">N° compras</th>
                                        <th class="text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_proveedores_body">
                                    <tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-2">Gastos por tipo</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tipo de gasto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_tipos_body">
                                    <tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h6 class="mb-2">Detalle de gastos (últimos 50 del periodo)</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Proveedor</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_detalle_body">
                            <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
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

            function escapeHtml(text) {
                return $('<div>').text(text ?? '').html();
            }

            function cargarDatos() {
                var periodo = periodoActual();
                var params = {
                    periodo: periodo,
                    almacen_id: $('#almacen_id').val(),
                    proveedor_id: $('#proveedor_id').val()
                };

                if (periodo === 'personalizado') {
                    if (!$('#fecha_inicio').val() || !$('#fecha_fin').val()) return;
                    params.fecha_inicio = $('#fecha_inicio').val();
                    params.fecha_fin = $('#fecha_fin').val();
                }

                $('#tabla_proveedores_body, #tabla_tipos_body').html('<tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr>');
                $('#tabla_detalle_body').html('<tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>');

                $.get("{{ route('tenant.reportes.comprasGastos') }}", params, function (data) {
                    pintarKpis(data);
                    pintarProveedores(data.comprasPorProveedor);
                    pintarTipos(data.gastosPorTipo);
                    pintarDetalle(data.detalleGastos);
                    pintarGrafico(data.serie);
                }).fail(function () {
                    $('#tabla_proveedores_body').html('<tr><td colspan="3" class="text-center text-danger">Error al cargar el reporte.</td></tr>');
                });
            }

            function pintarKpis(data) {
                $('#kpi_compras').text(fmt(data.actual.compras));
                $('#kpi_gastos').text(fmt(data.actual.gastos));
                $('#kpi_egresos').text(fmt(data.actual.egresos));

                var v = data.variacion.egresos;
                if (v > 0) {
                    $('#kpi_egresos_var').html('<i class="fa fa-arrow-up text-danger"></i> <span class="text-danger">' + v + '%</span> vs periodo anterior');
                } else if (v < 0) {
                    $('#kpi_egresos_var').html('<i class="fa fa-arrow-down text-success"></i> <span class="text-success">' + v + '%</span> vs periodo anterior');
                } else {
                    $('#kpi_egresos_var').html('<span class="text-muted">Sin variación vs periodo anterior</span>');
                }
            }

            function pintarProveedores(filas) {
                if (!filas.length) {
                    $('#tabla_proveedores_body').html('<tr><td colspan="3" class="text-center text-muted">Sin compras en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    html += '<tr><td>' + escapeHtml(f.PROV_RazonSocial) + '</td><td class="text-center">' + f.compras + '</td><td class="text-end">' + fmt(f.monto) + '</td></tr>';
                });
                $('#tabla_proveedores_body').html(html);
            }

            function pintarTipos(filas) {
                if (!filas.length) {
                    $('#tabla_tipos_body').html('<tr><td colspan="3" class="text-center text-muted">Sin gastos en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    html += '<tr><td>' + escapeHtml(f.TG_Descripcion) + '</td><td class="text-center">' + f.cantidad + '</td><td class="text-end">' + fmt(f.monto) + '</td></tr>';
                });
                $('#tabla_tipos_body').html(html);
            }

            function pintarDetalle(filas) {
                if (!filas.length) {
                    $('#tabla_detalle_body').html('<tr><td colspan="5" class="text-center text-muted">Sin gastos en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    html += '<tr>';
                    html += '<td>' + (f.GAS_Fecha ? f.GAS_Fecha.substring(0, 10) : '—') + '</td>';
                    html += '<td>' + escapeHtml(f.GAS_Descripcion) + '</td>';
                    html += '<td>' + escapeHtml(f.tipo) + '</td>';
                    html += '<td>' + escapeHtml(f.proveedor) + '</td>';
                    html += '<td class="text-end">' + fmt(f.GAS_Monto) + '</td>';
                    html += '</tr>';
                });
                $('#tabla_detalle_body').html(html);
            }

            function pintarGrafico(serie) {
                var ctx = document.getElementById('chartComprasGastos').getContext('2d');

                if (!serie.length) {
                    $('#chartComprasGastos').hide();
                    $('#sinDatosChart').show();
                    if (chart) { chart.destroy(); chart = null; }
                    return;
                }

                $('#chartComprasGastos').show();
                $('#sinDatosChart').hide();

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: serie.map(function (s) { return s.fecha; }),
                        datasets: [
                            { label: 'Compras', data: serie.map(s => s.compras), backgroundColor: 'rgba(60, 141, 188, 0.8)' },
                            { label: 'Gastos', data: serie.map(s => s.gastos), backgroundColor: 'rgba(221, 75, 57, 0.8)' }
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

            $('#almacen_id, #proveedor_id').change(cargarDatos);

            cargarDatos();
        });
    </script>
@endsection
