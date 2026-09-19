@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Reportes - Movimientos de Producto')
@section('contenido')

    @include('tenant_generico.reportes._estilos')

    <style>
        .rpt-mov-filtros label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 4px;
        }

        .rpt-mov-ficha {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            padding: 16px 20px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            align-items: center;
        }

        .rpt-mov-ficha .rpt-mov-nombre {
            font-size: 17px;
            font-weight: 800;
            color: #1F2937;
        }

        .rpt-mov-ficha .rpt-mov-dato-label {
            font-size: 10px;
            font-weight: 700;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .rpt-mov-ficha .rpt-mov-dato-valor {
            font-size: 14px;
            font-weight: 700;
            color: #1F2937;
        }

        .rpt-pill-tipo {
            display: inline-block;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .rpt-pill-compra { background: #DCFCE7; color: #16A34A; }
        .rpt-pill-anulacion_compra { background: #FEE2E2; color: #DC2626; }
        .rpt-pill-venta { background: #EDE9FE; color: #6C3BFF; }
        .rpt-pill-ajuste_ingreso { background: #DBEAFE; color: #2563EB; }
        .rpt-pill-ajuste_salida { background: #FEF3C7; color: #B45309; }

        #tabla_movimientos.table td {
            vertical-align: middle;
        }
    </style>

    <div class="col-12">

        <div class="rpt-filtros rpt-mov-filtros">
            <div class="row align-items-end">
                <div class="col-md-3 col-6 mb-2">
                    <label>Desde</label>
                    <input type="date" class="form-control" id="filtroFechaDesde" value="{{ $fechaDesde }}">
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <label>Hasta</label>
                    <input type="date" class="form-control" id="filtroFechaHasta" value="{{ $fechaHasta }}">
                </div>
                @if ($almacenes->count() > 1)
                    <div class="col-md-3 col-6 mb-2">
                        <label>Almacén</label>
                        <select class="form-control" id="filtroAlmacen">
                            <option value="">Todos</option>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->ALM_Id }}" {{ (string) $almacenId === (string) $almacen->ALM_Id ? 'selected' : '' }}>
                                    {{ $almacen->ALM_NombreAlmacen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-3 col-6 mb-2">
                    <label>Tipo de Movimiento</label>
                    <select class="form-control" id="filtroTipo">
                        <option value="">Todos</option>
                        <option value="compra" {{ $tipo === 'compra' ? 'selected' : '' }}>Compra</option>
                        <option value="anulacion_compra" {{ $tipo === 'anulacion_compra' ? 'selected' : '' }}>Anulación de Compra</option>
                        <option value="venta" {{ $tipo === 'venta' ? 'selected' : '' }}>Venta</option>
                        <option value="ajuste_ingreso" {{ $tipo === 'ajuste_ingreso' ? 'selected' : '' }}>Ajuste de Ingreso</option>
                        <option value="ajuste_salida" {{ $tipo === 'ajuste_salida' ? 'selected' : '' }}>Ajuste de Salida</option>
                    </select>
                </div>
            </div>
            <div class="row align-items-end">
                <div class="col-md-8 col-12 mb-2">
                    <label>Producto</label>
                    <select class="form-control" id="filtroProducto" style="width:100%;">
                        @if ($productoId)
                            <option value="{{ $productoId }}" selected>{{ $productoNombre }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <button type="button" class="btn btn-block" style="background:#6C3BFF;color:#fff;font-weight:700;" onclick="buscarReporte()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <button type="button" class="btn btn-block rpt-btn-exportar" onclick="exportarCsv()">
                        <i class="fas fa-file-excel mr-1"></i> Exportar
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('hoy')">Hoy</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('semana')">Esta semana</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('mes')">Este mes</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('anio')">Este año</button>
                </div>
            </div>
        </div>

        <!-- Ficha del producto seleccionado: solo aparece cuando se filtra uno -->
        <div class="rpt-mov-ficha" id="fichaProducto" style="display:none;">
            <div>
                <div class="rpt-mov-dato-label">Producto</div>
                <div class="rpt-mov-nombre" id="fichaNombre"></div>
                <div class="text-muted" style="font-size:12px;" id="fichaCategoria"></div>
            </div>
            <div>
                <div class="rpt-mov-dato-label">Creado el</div>
                <div class="rpt-mov-dato-valor" id="fichaCreado"></div>
            </div>
            <div>
                <div class="rpt-mov-dato-label">Precio Compra Actual</div>
                <div class="rpt-mov-dato-valor" id="fichaPrecioCompra"></div>
            </div>
            <div>
                <div class="rpt-mov-dato-label">Precio Venta Actual</div>
                <div class="rpt-mov-dato-valor" id="fichaPrecioVenta"></div>
            </div>
            <div>
                <div class="rpt-mov-dato-label">Stock Antes del Rango</div>
                <div class="rpt-mov-dato-valor" id="fichaStockPrevio"></div>
            </div>
            <div>
                <div class="rpt-mov-dato-label">Stock Actual</div>
                <div class="rpt-mov-dato-valor" id="fichaStockActual" style="color:#6C3BFF;"></div>
            </div>
        </div>

        <!-- KPIs -->
        <div class="rpt-kpi-row" id="rptKpis">
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">N° Movimientos</div>
                <div class="rpt-kpi-valor" id="kpiNumMovimientos">0</div>
            </div>
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Unid. Entradas</div>
                <div class="rpt-kpi-valor" id="kpiTotalEntradas">0</div>
            </div>
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Unid. Salidas</div>
                <div class="rpt-kpi-valor" id="kpiTotalSalidas">0</div>
            </div>
            <div class="rpt-kpi-tile destacado">
                <div class="rpt-kpi-label">Movimiento Neto</div>
                <div class="rpt-kpi-valor" id="kpiMovimientoNeto">0</div>
            </div>
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Valor Entradas</div>
                <div class="rpt-kpi-valor" id="kpiValorEntradas">S/ 0.00</div>
            </div>
            <div class="rpt-kpi-tile">
                <div class="rpt-kpi-label">Valor Salidas</div>
                <div class="rpt-kpi-valor" id="kpiValorSalidas">S/ 0.00</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-3">
                <div class="card rpt-card h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-chart-line mr-1" style="color:#6C3BFF;"></i>
                            Entradas vs Salidas
                        </h6>
                        <div class="rpt-chart-wrap">
                            <canvas id="chartMovTiempo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card rpt-card h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-chart-pie mr-1" style="color:#6C3BFF;"></i>
                            Por Tipo de Movimiento
                        </h6>
                        <div class="rpt-chart-wrap chico">
                            <canvas id="chartMovTipo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking de productos con mas movimiento: solo con "Todos los productos" -->
        <div class="card rpt-card mb-3" id="cardTopProductos" style="display:none;">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="fas fa-boxes mr-1" style="color:#6C3BFF;"></i>
                    Productos con Más Movimiento
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm rpt-table" id="tabla_top_productos" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-right">Entradas</th>
                                <th class="text-right">Salidas</th>
                                <th class="text-right">Movimientos</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card rpt-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exchange-alt mr-1" style="color:#6C3BFF;"></i>
                        MOVIMIENTOS
                    </h5>
                </div>
                <p class="text-muted" style="font-size:12px;">
                    Todo el flujo de stock del rango seleccionado: compras, anulaciones de compra, ventas y ajustes manuales.
                    @if ($productoId)
                        El stock mostrado es el acumulado real después de cada movimiento (orden cronológico).
                    @endif
                </p>

                <div class="table-responsive">
                    <table class="table table-hover rpt-table" id="tabla_movimientos" style="width:100%;">
                        <thead>
                            <tr id="filaEncabezadosMov">
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Documento</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Almacén</th>
                                <th class="text-right">Entrada</th>
                                <th class="text-right">Salida</th>
                                <th class="text-right">Costo Unit.</th>
                                <th class="text-right">Precio Unit.</th>
                                <th>Referencia</th>
                                <th class="text-right">Stock</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
        const URL_DATOS_MOVIMIENTOS = "{{ tenant_url('tenant.reportes.generico.movimientos.datos') }}";
        const URL_BUSCAR_PRODUCTOS_MOV = "{{ tenant_url('tenant.reportes.generico.movimientos.productos') }}";
        const URL_EXPORTAR_MOVIMIENTOS = "{{ tenant_url('tenant.reportes.generico.exportar', ['tipo' => 'movimientos']) }}";

        const COLOR_MORADO = '#6C3BFF';
        const COLOR_MORADO_CLARO = 'rgba(108, 59, 255, 0.15)';
        const COLOR_VERDE = '#16A34A';
        const COLOR_VERDE_CLARO = 'rgba(22, 163, 74, 0.12)';
        const PALETA = ['#6C3BFF', '#8B5CF6', '#16A34A', '#F59E0B', '#EF4444', '#0EA5E9', '#EC4899', '#14B8A6', '#F97316', '#64748B'];
        const CLASE_PILL_TIPO = {
            compra: 'rpt-pill-compra',
            anulacion_compra: 'rpt-pill-anulacion_compra',
            venta: 'rpt-pill-venta',
            ajuste_ingreso: 'rpt-pill-ajuste_ingreso',
            ajuste_salida: 'rpt-pill-ajuste_salida',
        };

        let charts = {};
        let tablaMovimientos;
        let tablaTopProductos;
        let hayProductoSeleccionado = {{ $productoId ? 'true' : 'false' }};

        function destruirChart(id) {
            if (charts[id]) {
                charts[id].destroy();
                delete charts[id];
            }
        }

        function unidadFmt(v) {
            return parseFloat(v || 0).toFixed(2).replace(/\.00$/, '');
        }

        function urlConFiltrosMovimientos(urlBase) {
            let params = new URLSearchParams({
                fecha_desde: $('#filtroFechaDesde').val() || '',
                fecha_hasta: $('#filtroFechaHasta').val() || '',
                almacen_id: $('#filtroAlmacen').length ? ($('#filtroAlmacen').val() || '') : '',
                producto_id: $('#filtroProducto').val() || '',
                tipo: $('#filtroTipo').val() || '',
            });

            return urlBase + '?' + params.toString();
        }

        function exportarCsv() {
            window.open(urlConFiltrosMovimientos(URL_EXPORTAR_MOVIMIENTOS), '_blank');
        }

        function pintarChartTiempo(serieTiempo) {
            destruirChart('tiempo');
            charts.tiempo = new Chart(document.getElementById('chartMovTiempo'), {
                type: 'line',
                data: {
                    labels: serieTiempo.puntos.map(p => p.etiqueta),
                    datasets: [
                        {
                            label: 'Entradas',
                            data: serieTiempo.puntos.map(p => p.entradas),
                            borderColor: COLOR_VERDE,
                            backgroundColor: COLOR_VERDE_CLARO,
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: 'Salidas',
                            data: serieTiempo.puntos.map(p => p.salidas),
                            borderColor: COLOR_MORADO,
                            backgroundColor: COLOR_MORADO_CLARO,
                            fill: true,
                            tension: 0.3,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                }
            });
        }

        function pintarChartTipo(porTipo) {
            destruirChart('tipo');
            charts.tipo = new Chart(document.getElementById('chartMovTipo'), {
                type: 'doughnut',
                data: {
                    labels: porTipo.map(t => t.nombre),
                    datasets: [{
                        data: porTipo.map(t => t.unidades),
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
            $('#kpiNumMovimientos').text(resumen.numMovimientos);
            $('#kpiTotalEntradas').text(unidadFmt(resumen.totalEntradas));
            $('#kpiTotalSalidas').text(unidadFmt(resumen.totalSalidas));
            $('#kpiMovimientoNeto').text(unidadFmt(resumen.movimientoNeto));
            $('#kpiValorEntradas').text(moneyFmt(resumen.valorEntradas));
            $('#kpiValorSalidas').text(moneyFmt(resumen.valorSalidas));
        }

        function actualizarFichaProducto(producto) {
            if (!producto) {
                $('#fichaProducto').hide();
                return;
            }

            $('#fichaNombre').text(producto.nombre);
            $('#fichaCategoria').text(producto.categoria);
            $('#fichaCreado').text(producto.creadoEl);
            $('#fichaPrecioCompra').text(moneyFmt(producto.precioCompra));
            $('#fichaPrecioVenta').text(moneyFmt(producto.precioVenta));
            $('#fichaStockPrevio').text(unidadFmt(producto.stockPrevio));
            $('#fichaStockActual').text(unidadFmt(producto.stockActual));
            $('#fichaProducto').show();
        }

        function actualizarTopProductos(topProductos) {
            if (!topProductos || topProductos.length === 0) {
                $('#cardTopProductos').hide();
                return;
            }

            $('#cardTopProductos').show();
            tablaTopProductos.clear();
            tablaTopProductos.rows.add(topProductos);
            tablaTopProductos.draw();
        }

        // Siempre se declaran las 12 columnas (una por cada <th> fijo del
        // encabezado): la visibilidad de "Stock" se controla con la opcion
        // nativa "visible" de DataTables, no ocultando el <th> con CSS —
        // si se hiciera con CSS, el <th> seguiria contando como columna
        // para DataTables y el arreglo "columns" quedaria corto, lo que
        // revienta la tabla ("Incorrect column count").
        function columnasMovimientos() {
            return [
                { data: 'fecha' },
                {
                    data: null,
                    render: (row) => '<span class="rpt-pill-tipo ' + (CLASE_PILL_TIPO[row.tipoClave] || '') + '">' + row.tipo + '</span>'
                },
                { data: 'documento' },
                { data: 'producto' },
                { data: 'categoria' },
                { data: 'almacen' },
                { data: 'entrada', className: 'text-right', render: (v) => v > 0 ? unidadFmt(v) : '-' },
                { data: 'salida', className: 'text-right', render: (v) => v > 0 ? unidadFmt(v) : '-' },
                { data: 'costoUnitario', className: 'text-right', render: (v) => v !== null ? moneyFmt(v) : '-' },
                { data: 'precioUnitario', className: 'text-right', render: (v) => v !== null ? moneyFmt(v) : '-' },
                { data: 'referencia' },
                {
                    data: 'stockDespues',
                    className: 'text-right',
                    visible: hayProductoSeleccionado,
                    render: (v) => v !== null ? '<strong>' + unidadFmt(v) + '</strong>' : '-'
                },
            ];
        }

        function pintarReporte(data) {
            actualizarKpis(data.resumen);
            actualizarFichaProducto(data.producto);
            pintarChartTiempo(data.serieTiempo);
            pintarChartTipo(data.porTipo);
            actualizarTopProductos(data.topProductos);

            tablaMovimientos.clear();
            tablaMovimientos.rows.add(data.movimientos);
            tablaMovimientos.draw();
        }

        function buscarReporte() {
            hayProductoSeleccionado = !!$('#filtroProducto').val();

            // Se recrea la tabla (no solo se llama a columns.visible()) porque
            // columnasMovimientos() tambien cambia el "render" de la columna
            // Stock segun haya o no producto seleccionado.
            if (tablaMovimientos) {
                tablaMovimientos.destroy();
                $('#tabla_movimientos tbody').empty();
                inicializarTablaMovimientos();
            }

            $.ajax({
                url: URL_DATOS_MOVIMIENTOS,
                method: 'GET',
                data: {
                    fecha_desde: $('#filtroFechaDesde').val(),
                    fecha_hasta: $('#filtroFechaHasta').val(),
                    almacen_id: $('#filtroAlmacen').length ? $('#filtroAlmacen').val() : '',
                    producto_id: $('#filtroProducto').val() || '',
                    tipo: $('#filtroTipo').val() || '',
                },
                success: function(response) {
                    pintarReporte(response);
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'No se pudo generar el reporte' });
                }
            });
        }

        function inicializarTablaMovimientos() {
            tablaMovimientos = $('#tabla_movimientos').DataTable({
                data: [],
                pageLength: 25,
                lengthMenu: [25, 50, 100, 250],
                order: [],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros',
                    zeroRecords: 'Sin movimientos en este rango',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'No hay registros',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar:',
                    paginate: { next: 'Siguiente', previous: 'Anterior' }
                },
                columns: columnasMovimientos(),
            });
        }

        $(document).ready(function() {
            $('#filtroProducto').select2({
                placeholder: 'Todos los productos',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: URL_BUSCAR_PRODUCTOS_MOV,
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                }
            });

            $('#filtroProducto, #filtroTipo').on('change', function() {
                buscarReporte();
            });

            inicializarTablaMovimientos();

            tablaTopProductos = $('#tabla_top_productos').DataTable({
                data: [],
                paging: false,
                searching: false,
                info: false,
                order: [],
                language: {
                    zeroRecords: 'Sin datos',
                },
                columns: [
                    { data: 'nombre' },
                    { data: 'categoria' },
                    { data: 'entradas', className: 'text-right', render: unidadFmt },
                    { data: 'salidas', className: 'text-right', render: unidadFmt },
                    { data: 'movimientos', className: 'text-right' },
                ],
            });

            buscarReporte();
        });
    </script>
@endsection
