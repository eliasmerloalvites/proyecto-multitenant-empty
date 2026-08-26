@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Clientes')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">CLIENTES</h4>
                        <p class="text-muted mb-0">Quiénes compran más (para fidelizar) y cuántos clientes nuevos se registraron en el periodo.</p>
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
                            <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Clientes que compraron</span>
                                <span class="info-box-number" id="kpi_compraron">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success"><i class="fas fa-user-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Clientes nuevos</span>
                                <span class="info-box-number" id="kpi_nuevos">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-address-book"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Clientes activos (total)</span>
                                <span class="info-box-number" id="kpi_activos">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-receipt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ticket promedio</span>
                                <span class="info-box-number" id="kpi_ticket">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRAFICO -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Top 10 clientes por monto comprado</h6>
                        <canvas id="chartClientes" height="90"></canvas>
                        <p id="sinDatosChart" class="text-muted text-center mt-3" style="display:none;">
                            No hay ventas registradas en este periodo.
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <h6 class="mb-2">Top clientes por monto comprado (máx. 20)</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Documento</th>
                                        <th class="text-center">Compras</th>
                                        <th class="text-end">Monto total</th>
                                        <th class="text-end">Ticket prom.</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_top_body">
                                    <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h6 class="mb-2">Clientes nuevos en el periodo</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Celular</th>
                                        <th>Registrado</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_nuevos_body">
                                    <tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr>
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

                $('#tabla_top_body').html('<tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>');
                $('#tabla_nuevos_body').html('<tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr>');

                $.get("{{ route('tenant.reportes.clientes') }}", params, function (data) {
                    pintarKpis(data.totales);
                    pintarTop(data.topClientes);
                    pintarNuevos(data.clientesNuevos);
                    pintarGrafico(data.topClientes);
                }).fail(function () {
                    $('#tabla_top_body').html('<tr><td colspan="5" class="text-center text-danger">Error al cargar el reporte.</td></tr>');
                });
            }

            function pintarKpis(t) {
                $('#kpi_compraron').text(t.clientesQueCompraron);
                $('#kpi_nuevos').text(t.clientesNuevos);
                $('#kpi_activos').text(t.clientesActivos);
                $('#kpi_ticket').text(fmt(t.ticketPromedioGeneral));
            }

            function pintarTop(filas) {
                if (!filas.length) {
                    $('#tabla_top_body').html('<tr><td colspan="5" class="text-center text-muted">Sin ventas en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    html += '<tr>';
                    html += '<td>' + escapeHtml(f.CLI_Nombre) + '</td>';
                    html += '<td>' + escapeHtml(f.CLI_NumDocumento) + '</td>';
                    html += '<td class="text-center">' + f.compras + '</td>';
                    html += '<td class="text-end"><strong>' + fmt(f.monto) + '</strong></td>';
                    html += '<td class="text-end">' + fmt(f.ticket_promedio) + '</td>';
                    html += '</tr>';
                });
                $('#tabla_top_body').html(html);
            }

            function pintarNuevos(filas) {
                if (!filas.length) {
                    $('#tabla_nuevos_body').html('<tr><td colspan="3" class="text-center text-muted">Sin clientes nuevos en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                filas.forEach(function (f) {
                    html += '<tr>';
                    html += '<td>' + escapeHtml(f.CLI_Nombre) + '</td>';
                    html += '<td>' + escapeHtml(f.CLI_Celular || '—') + '</td>';
                    html += '<td>' + (f.created_at ? f.created_at.substring(0, 10) : '—') + '</td>';
                    html += '</tr>';
                });
                $('#tabla_nuevos_body').html(html);
            }

            function pintarGrafico(clientes) {
                var ctx = document.getElementById('chartClientes').getContext('2d');
                var top10 = clientes.slice(0, 10);

                if (!top10.length) {
                    $('#chartClientes').hide();
                    $('#sinDatosChart').show();
                    if (chart) { chart.destroy(); chart = null; }
                    return;
                }

                $('#chartClientes').show();
                $('#sinDatosChart').hide();

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: top10.map(function (c) { return c.CLI_Nombre; }),
                        datasets: [{
                            label: 'Monto comprado',
                            data: top10.map(function (c) { return c.monto; }),
                            backgroundColor: 'rgba(60, 141, 188, 0.8)'
                        }]
                    },
                    options: {
                        legend: { display: false },
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
