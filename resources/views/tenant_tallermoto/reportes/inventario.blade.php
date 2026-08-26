@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Inventario Valorizado')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">INVENTARIO VALORIZADO</h4>
                        <p class="text-muted mb-0">
                            Cuánto capital tienes inmovilizado en stock ahora mismo, y qué productos no se han movido en el periodo elegido.
                        </p>
                    </div>
                </div>

                <!-- FILTROS -->
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="mb-1">Periodo para "sin movimiento"</label>
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
                        <p class="text-muted small mb-0 mt-2">
                            <i class="fa fa-info-circle"></i> El valor del stock es siempre "en este momento" (no depende del periodo); el periodo solo se usa para saber qué productos no se vendieron en ese rango.
                        </p>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-warehouse"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Valor a costo</span>
                                <span class="info-box-number" id="kpi_costo">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-primary"><i class="fas fa-tags"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Valor a precio de venta</span>
                                <span class="info-box-number" id="kpi_venta">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success"><i class="fas fa-hand-holding-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ganancia potencial</span>
                                <span class="info-box-number" id="kpi_ganancia">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Stock crítico / sin stock</span>
                                <span class="info-box-number" id="kpi_critico">0 / 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabTodos">Todo el inventario</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabSinMovimiento">Sin movimiento en el periodo <span class="badge badge-warning" id="badge_sin_mov">0</span></a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabTodos">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-end">Costo prom.</th>
                                        <th class="text-end">Valor costo</th>
                                        <th class="text-end">Valor venta</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_todos_body">
                                    <tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabSinMovimiento">
                        <p class="text-muted small">Productos con stock disponible que no tuvieron ni una venta en el periodo seleccionado — candidatos a promoción o revisión.</p>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-end">Valor costo inmovilizado</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_sinmov_body">
                                    <tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>
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

                $('#tabla_todos_body, #tabla_sinmov_body').html('<tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>');

                $.get("{{ route('tenant.reportes.inventario') }}", params, function (data) {
                    pintarKpis(data.totales);
                    pintarTodos(data.productos);
                    pintarSinMovimiento(data.sinMovimiento);
                }).fail(function () {
                    $('#tabla_todos_body').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar el reporte.</td></tr>');
                });
            }

            function pintarKpis(t) {
                $('#kpi_costo').text(fmt(t.valor_costo));
                $('#kpi_venta').text(fmt(t.valor_venta));
                $('#kpi_ganancia').text(fmt(t.ganancia_potencial));
                $('#kpi_critico').text(t.stock_critico + ' / ' + t.sin_stock);
            }

            function pintarTodos(productos) {
                if (!productos.length) {
                    $('#tabla_todos_body').html('<tr><td colspan="6" class="text-center text-muted">Sin productos activos.</td></tr>');
                    return;
                }
                var html = '';
                productos.forEach(function (p) {
                    var claseStock = p.stock <= 0 ? 'text-danger font-weight-bold' : (p.stock <= 5 ? 'text-warning font-weight-bold' : '');
                    html += '<tr>';
                    html += '<td>' + escapeHtml(p.PRO_Nombre) + '</td>';
                    html += '<td>' + escapeHtml(p.categoria) + '</td>';
                    html += '<td class="text-center ' + claseStock + '">' + p.stock + '</td>';
                    html += '<td class="text-end">' + fmt(p.costo_promedio) + '</td>';
                    html += '<td class="text-end">' + fmt(p.valor_costo) + '</td>';
                    html += '<td class="text-end">' + fmt(p.valor_venta) + '</td>';
                    html += '</tr>';
                });
                $('#tabla_todos_body').html(html);
            }

            function pintarSinMovimiento(productos) {
                $('#badge_sin_mov').text(productos.length);
                if (!productos.length) {
                    $('#tabla_sinmov_body').html('<tr><td colspan="4" class="text-center text-muted">Todo el stock tuvo movimiento en este periodo.</td></tr>');
                    return;
                }
                var html = '';
                productos.forEach(function (p) {
                    html += '<tr>';
                    html += '<td>' + escapeHtml(p.PRO_Nombre) + '</td>';
                    html += '<td>' + escapeHtml(p.categoria) + '</td>';
                    html += '<td class="text-center">' + p.stock + '</td>';
                    html += '<td class="text-end">' + fmt(p.valor_costo) + '</td>';
                    html += '</tr>';
                });
                $('#tabla_sinmov_body').html(html);
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
