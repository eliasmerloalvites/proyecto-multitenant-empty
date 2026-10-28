@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Operación del Taller')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">OPERACIÓN DEL TALLER</h4>
                        <p class="text-muted mb-0">Cumplimiento de reservas y ocupación de bahías y turnos en el periodo.</p>
                    </div>
                </div>

                <!-- FILTROS -->
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="mb-1">Periodo</label>
                                <div class="btn-group btn-group-toggle d-flex">
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="hoy">Hoy</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="semana">Esta semana</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn active" data-periodo="mes">Este mes</button>
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
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Reservas</span>
                                <span class="info-box-number" id="kpi_total">0</span>
                                <span class="small text-muted" id="kpi_total_sub">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Aprobadas</span>
                                <span class="info-box-number" id="kpi_aprobadas">0</span>
                                <span class="small text-muted" id="kpi_aprobadas_sub">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" title="Rechazadas por el taller">Rechazadas</span>
                                <span class="info-box-number" id="kpi_rechazadas">0</span>
                                <span class="small text-muted" id="kpi_rechazadas_sub">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sin gestionar</span>
                                <span class="info-box-number" id="kpi_sin_gestionar">0</span>
                                <span class="small text-muted" id="kpi_sin_gestionar_sub">Pendientes con fecha ya pasada</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-info"><i class="fas fa-thumbs-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" title="Tasa de aprobación">% Aprobación</span>
                                <span class="info-box-number" id="kpi_tasa_aprobacion">0</span>
                                <span class="small text-muted" id="kpi_tasa_aprobacion_sub">Aprobadas ÷ (aprobadas + rechazadas)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-primary"><i class="fas fa-motorcycle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" title="Tasa de cumplimiento">% Cumplimiento</span>
                                <span class="info-box-number" id="kpi_tasa_cumplimiento">0</span>
                                <span class="small text-muted" id="kpi_tasa_cumplimiento_sub">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRAFICO -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Reservas por día, según estado</h6>
                        <canvas id="chartReservas" height="90"></canvas>
                        <p id="sinDatosChart" class="text-muted text-center mt-3" style="display:none;">
                            No hay reservas en este periodo.
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Ocupación por bahía</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Bahía</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Aprobadas</th>
                                        <th class="text-center">Rechazadas</th>
                                        <th class="text-center" title="Canceladas por el cliente desde la app">Canceladas</th>
                                        <th class="text-center" title="Pendientes con fecha ya pasada">Sin gestionar</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_bahias_body">
                                    <tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-2">Reservas por turno</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Turno</th>
                                        <th class="text-center">Reservas</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_turnos_body">
                                    <tr><td colspan="2" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <h6 class="mb-2">Mantenimientos creados en el periodo, por estado</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Estado</th>
                                        <th class="text-center">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_mtto_body">
                                    <tr><td colspan="2" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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

            function escapeHtml(text) {
                return $('<div>').text(text ?? '').html();
            }

            function cargarDatos() {
                var periodo = periodoActual();
                var params = { periodo: periodo, almacen_id: $('#almacen_id').val() };

                if (periodo === 'personalizado') {
                    if (!$('#fecha_inicio').val() || !$('#fecha_fin').val()) return;
                    params.fecha_inicio = $('#fecha_inicio').val();
                    params.fecha_fin = $('#fecha_fin').val();
                }

                $('#tabla_bahias_body').html('<tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>');
                $('#tabla_turnos_body, #tabla_mtto_body').html('<tr><td colspan="2" class="text-center text-muted">Cargando...</td></tr>');

                $.get("{{ route('tenant.reportes.operacionTaller') }}", params, function (data) {
                    pintarKpis(data.totales);
                    pintarBahias(data.porBahia);
                    pintarTurnos(data.porTurno);
                    pintarMtto(data.mantenimientos);
                    pintarGrafico(data.serie);
                }).fail(function () {
                    var error = '<td class="text-center text-danger">Error al cargar el reporte. Vuelve a intentarlo.</td>';
                    $('#tabla_bahias_body').html('<tr>' + error.replace('<td', '<td colspan="6"') + '</tr>');
                    $('#tabla_turnos_body, #tabla_mtto_body').html('<tr>' + error.replace('<td', '<td colspan="2"') + '</tr>');
                });
            }

            function pct(v) {
                return v === null || v === undefined ? '—' : v + '%';
            }

            function pintarKpis(t) {
                $('#kpi_total').text(t.totalReservas);
                $('#kpi_total_sub').text(t.pendientes + ' pendiente(s) por venir');
                $('#kpi_aprobadas').text(t.aprobadas);
                $('#kpi_aprobadas_sub').text(t.atendidas + ' llegaron al taller');
                $('#kpi_rechazadas').text(t.rechazadas);
                $('#kpi_rechazadas_sub').text('Por el taller' + (t.canceladas > 0 ? ' · + ' + t.canceladas + ' cancelada(s) por el cliente' : ''));
                $('#kpi_sin_gestionar').text(t.sinGestionar);
                $('#kpi_tasa_aprobacion').text(pct(t.tasaAprobacion));
                $('#kpi_tasa_cumplimiento').text(pct(t.tasaCumplimiento));
                $('#kpi_tasa_cumplimiento_sub').text(t.atendidas + ' de ' + t.aprobadasYaLlegaron + ' aprobadas ya en fecha llegaron al taller');
            }

            function pintarBahias(filas) {
                if (!filas.length) {
                    $('#tabla_bahias_body').html('<tr><td colspan="6" class="text-center text-muted">Sin reservas en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    html += '<tr><td>' + escapeHtml(f.BAH_Nombre) + '</td><td class="text-center">' + f.total + '</td>'
                        + '<td class="text-center text-success">' + f.aprobadas + '</td>'
                        + '<td class="text-center text-danger">' + f.rechazadas + '</td>'
                        + '<td class="text-center text-muted">' + f.canceladas + '</td>'
                        + '<td class="text-center text-warning">' + f.sin_gestionar + '</td></tr>';
                });
                $('#tabla_bahias_body').html(html);
            }

            function pintarTurnos(filas) {
                if (!filas.length) {
                    $('#tabla_turnos_body').html('<tr><td colspan="2" class="text-center text-muted">Sin reservas en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    html += '<tr><td>' + escapeHtml(f.turno) + '</td><td class="text-center">' + f.total + '</td></tr>';
                });
                $('#tabla_turnos_body').html(html);
            }

            function pintarMtto(m) {
                var etiquetas = { PENDIENTE: 'Pendiente', APROBADO: 'Aprobado', OBSERVADO: 'Observado' };
                var html = '';
                Object.keys(etiquetas).forEach(function (k) {
                    html += '<tr><td>' + etiquetas[k] + '</td><td class="text-center">' + (m[k] || 0) + '</td></tr>';
                });
                $('#tabla_mtto_body').html(html);
            }

            function pintarGrafico(serie) {
                var ctx = document.getElementById('chartReservas').getContext('2d');

                if (!serie.length) {
                    $('#chartReservas').hide();
                    $('#sinDatosChart').show();
                    if (chart) { chart.destroy(); chart = null; }
                    return;
                }

                $('#chartReservas').show();
                $('#sinDatosChart').hide();

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: serie.map(function (s) { return s.fecha; }),
                        datasets: [
                            { label: 'Aprobadas', data: serie.map(s => s.aprobadas), backgroundColor: 'rgba(0, 166, 90, 0.8)' },
                            { label: 'Rechazadas (taller)', data: serie.map(s => s.rechazadas), backgroundColor: 'rgba(221, 75, 57, 0.8)' },
                            { label: 'Canceladas (cliente)', data: serie.map(s => s.canceladas), backgroundColor: 'rgba(108, 117, 125, 0.7)' },
                            { label: 'Sin gestionar', data: serie.map(s => s.sin_gestionar), backgroundColor: 'rgba(243, 156, 18, 0.85)' },
                            { label: 'Pendientes (por venir)', data: serie.map(s => s.pendientes), backgroundColor: 'rgba(60, 141, 188, 0.75)' }
                        ]
                    },
                    options: {
                        scales: {
                            xAxes: [{ stacked: true }],
                            yAxes: [{ stacked: true, ticks: { beginAtZero: true, precision: 0 } }]
                        }
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
