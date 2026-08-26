@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Rendimiento de Mecánicos')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">RENDIMIENTO DE MECÁNICOS</h4>
                        <p class="text-muted mb-0">Mantenimientos completados por mecánico en el periodo seleccionado.</p>
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
                    </div>
                </div>

                <!-- RESUMEN -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-info"><i class="fas fa-tools"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total mantenimientos completados</span>
                                <span class="info-box-number" id="resumen_total">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success"><i class="fas fa-trophy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Mecánico con más trabajo</span>
                                <span class="info-box-number" id="resumen_top" style="font-size: 1rem;">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-stopwatch"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Periodo</span>
                                <span class="info-box-number" id="resumen_periodo" style="font-size: 1rem;">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRAFICO -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Mantenimientos completados por mecánico</h6>
                        <canvas id="chartMecanicos" height="90"></canvas>
                        <p id="sinDatosChart" class="text-muted text-center mt-3" style="display:none;">
                            No hay mantenimientos completados en este periodo.
                        </p>
                    </div>
                </div>

                <!-- TABLA -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="tabla_rendimiento">
                        <thead class="bg-light">
                            <tr>
                                <th>N°</th>
                                <th>Mecánico</th>
                                <th>Total</th>
                                <th>Actividad Variada</th>
                                <th>General Carburada</th>
                                <th>General Inyectada</th>
                                <th>Preventivo Carburada</th>
                                <th>Preventivo Inyectada</th>
                                <th>Tiempo Promedio</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_rendimiento_body">
                            <tr>
                                <td colspan="9" class="text-center text-muted">Cargando...</td>
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
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var chart = null;
            var tiposOrden = [
                'ACTIVIDADES VARIADAS',
                'MTTO GENERAL CARBURADAS',
                'MTTO GENERAL INYECTADAS',
                'MTTO PREVENTIVOS CARBURADAS',
                'MTTO PREVENTIVOS INYECTADAS'
            ];

            function periodoActual() {
                return $('.periodo-btn.active').data('periodo');
            }

            function cargarDatos() {
                var periodo = periodoActual();
                var params = { periodo: periodo };

                if (periodo === 'personalizado') {
                    if (!$('#fecha_inicio').val() || !$('#fecha_fin').val()) {
                        return;
                    }
                    params.fecha_inicio = $('#fecha_inicio').val();
                    params.fecha_fin = $('#fecha_fin').val();
                }

                $('#tabla_rendimiento_body').html(
                    '<tr><td colspan="9" class="text-center text-muted">Cargando...</td></tr>');

                $.get("{{ route('tenant.reportes.rendimientomecanicos') }}", params, function(data) {
                    pintarResumen(data);
                    pintarTabla(data.mecanicos);
                    pintarGrafico(data.mecanicos);
                }).fail(function() {
                    $('#tabla_rendimiento_body').html(
                        '<tr><td colspan="9" class="text-center text-danger">Error al cargar el reporte.</td></tr>');
                });
            }

            function pintarResumen(data) {
                $('#resumen_total').text(data.totalGeneral);
                $('#resumen_periodo').text(data.periodo.inicio + ' al ' + data.periodo.fin);

                if (data.mecanicos.length > 0) {
                    var top = data.mecanicos[0];
                    $('#resumen_top').text(top.personal + ' (' + top.total + ')');
                } else {
                    $('#resumen_top').text('—');
                }
            }

            function pintarTabla(mecanicos) {
                if (mecanicos.length === 0) {
                    $('#tabla_rendimiento_body').html(
                        '<tr><td colspan="9" class="text-center text-muted">Sin mantenimientos completados en este periodo.</td></tr>');
                    return;
                }

                var html = '';
                mecanicos.forEach(function(m, i) {
                    html += '<tr>';
                    html += '<td>' + (i + 1) + '</td>';
                    html += '<td>' + escapeHtml(m.personal) + '</td>';
                    html += '<td><strong>' + m.total + '</strong></td>';
                    tiposOrden.forEach(function(tipo) {
                        html += '<td class="text-center">' + (m.por_tipo[tipo] || 0) + '</td>';
                    });
                    html += '<td>' + (m.horas_promedio > 0 ? m.horas_promedio + ' h' : '—') + '</td>';
                    html += '</tr>';
                });

                $('#tabla_rendimiento_body').html(html);
            }

            function pintarGrafico(mecanicos) {
                var ctx = document.getElementById('chartMecanicos').getContext('2d');

                if (mecanicos.length === 0) {
                    $('#chartMecanicos').hide();
                    $('#sinDatosChart').show();
                    if (chart) {
                        chart.destroy();
                        chart = null;
                    }
                    return;
                }

                $('#chartMecanicos').show();
                $('#sinDatosChart').hide();

                var labels = mecanicos.map(function(m) { return m.personal; });
                var valores = mecanicos.map(function(m) { return m.total; });

                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Mantenimientos completados',
                            data: valores,
                            backgroundColor: 'rgba(60, 141, 188, 0.8)'
                        }]
                    },
                    options: {
                        legend: { display: false },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0
                                }
                            }]
                        }
                    }
                });
            }

            function escapeHtml(text) {
                return $('<div>').text(text ?? '').html();
            }

            $('.periodo-btn').click(function() {
                $('.periodo-btn').removeClass('active');
                $(this).addClass('active');

                if ($(this).data('periodo') === 'personalizado') {
                    $('.fecha-personalizada').show();
                } else {
                    $('.fecha-personalizada').hide();
                    cargarDatos();
                }
            });

            $('#fecha_inicio, #fecha_fin').change(function() {
                if (periodoActual() === 'personalizado') {
                    cargarDatos();
                }
            });

            cargarDatos();
        });
    </script>
@endsection
