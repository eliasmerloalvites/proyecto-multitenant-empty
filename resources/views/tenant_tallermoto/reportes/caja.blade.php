@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Reporte de Caja')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">CUADRE DE CAJA</h4>
                        <p class="text-muted mb-0">Diferencias de cierre acumuladas por cajero, para detectar patrones que un cierre individual no muestra.</p>
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
                <div class="row mb-4">
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-cash-register"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cierres en el periodo</span>
                                <span class="info-box-number" id="kpi_sesiones">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-primary"><i class="fas fa-wallet"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Monto manejado</span>
                                <span class="info-box-number" id="kpi_monto">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon" id="icono_diferencia"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Diferencia acumulada</span>
                                <span class="info-box-number" id="kpi_diferencia">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cierres con descuadre</span>
                                <span class="info-box-number" id="kpi_descuadres">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRAFICO -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Diferencia de caja por día (positivo = sobrante, negativo = faltante)</h6>
                        <canvas id="chartCaja" height="90"></canvas>
                        <p id="sinDatosChart" class="text-muted text-center mt-3" style="display:none;">
                            No hay cierres de caja en este periodo.
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <h6 class="mb-2">Por cajero</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Cajero</th>
                                        <th class="text-center">Cierres</th>
                                        <th class="text-center">Con descuadre</th>
                                        <th class="text-end">Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_cajeros_body">
                                    <tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h6 class="mb-2">Detalle de cierres (últimos 50 del periodo)</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Caja</th>
                                        <th>Cajero</th>
                                        <th>Cierre</th>
                                        <th class="text-end">Esperado</th>
                                        <th class="text-end">Real</th>
                                        <th class="text-end">Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_detalle_body">
                                    <tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>
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

            function fmt(n) {
                return 'S/ ' + (parseFloat(n) || 0).toFixed(2);
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

                $('#tabla_cajeros_body').html('<tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>');
                $('#tabla_detalle_body').html('<tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>');

                $.get("{{ route('tenant.reportes.caja') }}", params, function (data) {
                    pintarKpis(data.totales);
                    pintarCajeros(data.porCajero);
                    pintarDetalle(data.detalle);
                    pintarGrafico(data.serie);
                }).fail(function () {
                    $('#tabla_cajeros_body').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar el reporte.</td></tr>');
                });
            }

            function pintarKpis(t) {
                $('#kpi_sesiones').text(t.sesiones);
                $('#kpi_monto').text(fmt(t.montoManejado));
                $('#kpi_diferencia').text(fmt(t.diferenciaTotal));
                $('#kpi_descuadres').text(t.conDescuadre);

                var $icono = $('#icono_diferencia');
                $icono.removeClass('bg-success bg-danger bg-secondary');
                if (t.diferenciaTotal > 0) $icono.addClass('bg-success');
                else if (t.diferenciaTotal < 0) $icono.addClass('bg-danger');
                else $icono.addClass('bg-secondary');
            }

            function pintarCajeros(filas) {
                if (!filas.length) {
                    $('#tabla_cajeros_body').html('<tr><td colspan="4" class="text-center text-muted">Sin cierres en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    var clase = f.diferencia_total < 0 ? 'text-danger' : (f.diferencia_total > 0 ? 'text-success' : '');
                    html += '<tr>';
                    html += '<td>' + escapeHtml(f.name) + '</td>';
                    html += '<td class="text-center">' + f.sesiones + '</td>';
                    html += '<td class="text-center">' + f.con_descuadre + '</td>';
                    html += '<td class="text-end ' + clase + '">' + fmt(f.diferencia_total) + '</td>';
                    html += '</tr>';
                });
                $('#tabla_cajeros_body').html(html);
            }

            function pintarDetalle(filas) {
                if (!filas.length) {
                    $('#tabla_detalle_body').html('<tr><td colspan="6" class="text-center text-muted">Sin cierres en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    var clase = f.CS_Diferencia < 0 ? 'text-danger' : (f.CS_Diferencia > 0 ? 'text-success' : '');
                    html += '<tr>';
                    html += '<td>' + escapeHtml(f.CAJ_Nombre) + '</td>';
                    html += '<td>' + escapeHtml(f.cajero) + '</td>';
                    html += '<td>' + (f.CS_FechaCierre ? f.CS_FechaCierre.substring(0, 16).replace('T', ' ') : '—') + '</td>';
                    html += '<td class="text-end">' + fmt(f.CS_MontoEsperado) + '</td>';
                    html += '<td class="text-end">' + fmt(f.CS_MontoReal) + '</td>';
                    html += '<td class="text-end ' + clase + '">' + fmt(f.CS_Diferencia) + '</td>';
                    html += '</tr>';
                });
                $('#tabla_detalle_body').html(html);
            }

            function pintarGrafico(serie) {
                var ctx = document.getElementById('chartCaja').getContext('2d');

                if (!serie.length) {
                    $('#chartCaja').hide();
                    $('#sinDatosChart').show();
                    if (chart) { chart.destroy(); chart = null; }
                    return;
                }

                $('#chartCaja').show();
                $('#sinDatosChart').hide();

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: serie.map(function (s) { return s.fecha; }),
                        datasets: [{
                            label: 'Diferencia',
                            data: serie.map(function (s) { return s.diferencia; }),
                            backgroundColor: serie.map(function (s) { return s.diferencia < 0 ? 'rgba(221, 75, 57, 0.8)' : 'rgba(0, 166, 90, 0.8)'; })
                        }]
                    },
                    options: {
                        legend: { display: false }
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
