@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Cuentas por Cobrar')
@section('contenido')

    <style>
        .cxc-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .cxc-badge-pendiente { background: #EDE9FE; color: #6C3BFF; }
        .cxc-badge-pagado { background: #DCFCE7; color: #16A34A; }
        .cxc-badge-vencida { background: #FEE2E2; color: #DC2626; }

        .cxc-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .cxc-card .card-title {
            font-weight: 800;
            color: #1F2937;
        }

        #tabla_cuentas_cobrar tbody tr {
            cursor: pointer;
        }

        #tabla_cuentas_cobrar tbody tr:hover {
            background: #F9F7FF;
        }

        .cxc-resumen-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        @media (max-width: 992px) {
            .cxc-resumen-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .cxc-resumen-tile {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            padding: 14px 16px;
        }

        .cxc-resumen-tile .cxc-resumen-label {
            font-size: 11px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 4px;
        }

        .cxc-resumen-tile .cxc-resumen-valor {
            font-size: 20px;
            font-weight: 800;
            color: #1F2937;
        }

        .cxc-resumen-tile.destacado {
            background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
        }

        .cxc-resumen-tile.destacado .cxc-resumen-label,
        .cxc-resumen-tile.destacado .cxc-resumen-valor {
            color: #fff;
        }

        .cxc-resumen-tile.alerta .cxc-resumen-valor {
            color: #DC2626;
        }

        .cxc-filtros {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            padding: 16px;
            margin-bottom: 16px;
        }

        .cxc-filtros label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 4px;
        }
    </style>

    <div class="col-12">

        <!-- RESUMEN (reacciona a lo que muestra la tabla filtrada) -->
        <div class="cxc-resumen-row" id="cxcResumen">
            <div class="cxc-resumen-tile">
                <div class="cxc-resumen-label">Cuentas</div>
                <div class="cxc-resumen-valor" id="resCantidad">{{ $resumen['cantidad'] }}</div>
            </div>
            <div class="cxc-resumen-tile">
                <div class="cxc-resumen-label">Total Vendido</div>
                <div class="cxc-resumen-valor" id="resTotal">S/ {{ number_format($resumen['total'], 2) }}</div>
            </div>
            <div class="cxc-resumen-tile">
                <div class="cxc-resumen-label">Adelantado</div>
                <div class="cxc-resumen-valor" id="resAdelanto">S/ {{ number_format($resumen['adelanto'], 2) }}</div>
            </div>
            <div class="cxc-resumen-tile">
                <div class="cxc-resumen-label">Abonado</div>
                <div class="cxc-resumen-valor" id="resAbonado">S/ {{ number_format($resumen['abonado'], 2) }}</div>
            </div>
            <div class="cxc-resumen-tile destacado">
                <div class="cxc-resumen-label">Pendiente</div>
                <div class="cxc-resumen-valor" id="resPendiente">S/ {{ number_format($resumen['pendiente'], 2) }}</div>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="cxc-filtros">
            <div class="row align-items-end">
                <div class="col-md-3 col-6 mb-2">
                    <label>Desde</label>
                    <input type="date" class="form-control" id="filtroFechaDesde" value="{{ $fechaDesde }}">
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <label>Hasta</label>
                    <input type="date" class="form-control" id="filtroFechaHasta" value="{{ $fechaHasta }}">
                </div>
                <div class="col-md-4 col-8 mb-2">
                    <label>Cliente (nombre o documento)</label>
                    <input type="text" class="form-control" id="filtroCliente" placeholder="Buscar cliente..." value="{{ $cliente }}">
                </div>
                <div class="col-md-2 col-4 mb-2">
                    <button type="button" class="btn btn-block" style="background:#6C3BFF;color:#fff;font-weight:700;" onclick="buscarCuentasCobrar()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltrosCuentasCobrar()">
                        <i class="fas fa-times mr-1"></i> Quitar fechas (ver todo el historial)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#EDE9FE;color:#6C3BFF;font-weight:700;" onclick="generarReportePdf()">
                        <i class="fas fa-file-pdf mr-1"></i> Generar PDF
                    </button>
                    <span class="text-muted" style="font-size:12px;">
                        Tip: para ver cuánto debe un cliente en total, busca su nombre y quita las fechas.
                    </span>
                </div>
            </div>
        </div>

        <div class="card cxc-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar mr-1" style="color:#6C3BFF;"></i>
                        CUENTAS POR COBRAR
                    </h5>
                    <a href="{{ tenant_url('tenant.ventas.venta.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a Ventas
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="tabla_cuentas_cobrar" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Venta</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Adelanto</th>
                                <th>Abonado</th>
                                <th>Pendiente</th>
                                <th>Próx. Vencimiento</th>
                                <th>Estado</th>
                                <th></th>
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
    <script>
        const URL_INDEX_CUENTAS_COBRAR = "{{ tenant_url('tenant.ventas.cuentascobrar.index') }}";
        const URL_SHOW_CUENTA_COBRAR = "{{ tenant_url('tenant.ventas.cuentascobrar.show', ['cuentaCobrar' => ':id']) }}";
        const URL_REPORTE_PDF_CUENTAS_COBRAR = "{{ tenant_url('tenant.ventas.cuentascobrar.reporte.pdf') }}";

        let tablaCuentasCobrar;

        function badgeEstado(cuenta) {
            if (cuenta.CXC_Estado == 2) {
                return '<span class="cxc-badge cxc-badge-pagado">Pagado</span>';
            }
            if (cuenta.vencida) {
                return '<span class="cxc-badge cxc-badge-vencida">Vencida</span>';
            }
            return '<span class="cxc-badge cxc-badge-pendiente">Pendiente</span>';
        }

        function moneyFmt(v) {
            return 'S/ ' + parseFloat(v || 0).toFixed(2);
        }

        $(document).ready(function() {
            tablaCuentasCobrar = $('#tabla_cuentas_cobrar').DataTable({
                data: @json($cuentas),
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros',
                    zeroRecords: 'No hay cuentas por cobrar con estos filtros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'No hay registros',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar en esta página:',
                    paginate: { next: 'Siguiente', previous: 'Anterior' }
                },
                columns: [
                    { data: 'VEN_Id', render: (d) => '#' + d },
                    {
                        data: null,
                        render: (row) => row.CLI_Nombre + '<div class="text-muted" style="font-size:11px;">' + (row.CLI_NumDocumento || '') + '</div>'
                    },
                    { data: 'CXC_MontoTotal', render: moneyFmt },
                    { data: 'CXC_MontoAdelanto', render: moneyFmt },
                    { data: 'CXC_MontoAbonado', render: moneyFmt },
                    { data: 'CXC_MontoPendiente', render: (v) => '<strong>' + moneyFmt(v) + '</strong>' },
                    {
                        data: null,
                        // CXC_TieneCuotas puede llegar como string "0"/"1"
                        // desde una consulta cruda (DB::table), y en JS el
                        // string "0" es verdadero (truthy) — por eso se
                        // compara con Number(...) en vez de usarlo directo.
                        render: (row) => Number(row.CXC_TieneCuotas) ? (row.CXC_FechaVencimiento || '-') : 'Libre'
                    },
                    { data: null, render: badgeEstado },
                    {
                        data: 'CXC_Id',
                        orderable: false,
                        searchable: false,
                        render: () => '<i class="fas fa-chevron-right text-muted"></i>'
                    },
                ]
            });

            $('#tabla_cuentas_cobrar tbody').on('click', 'tr', function() {
                let data = tablaCuentasCobrar.row(this).data();
                if (data) {
                    window.location = URL_SHOW_CUENTA_COBRAR.replace(':id', data.CXC_Id);
                }
            });

            // Buscar tambien al presionar Enter en cualquiera de los filtros.
            $('#filtroFechaDesde, #filtroFechaHasta, #filtroCliente').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    buscarCuentasCobrar();
                }
            });
        });

        function actualizarResumen(resumen) {
            $('#resCantidad').text(resumen.cantidad);
            $('#resTotal').text(moneyFmt(resumen.total));
            $('#resAdelanto').text(moneyFmt(resumen.adelanto));
            $('#resAbonado').text(moneyFmt(resumen.abonado));
            $('#resPendiente').text(moneyFmt(resumen.pendiente));
        }

        function buscarCuentasCobrar() {
            $.ajax({
                url: URL_INDEX_CUENTAS_COBRAR,
                method: 'GET',
                data: {
                    fecha_desde: $('#filtroFechaDesde').val(),
                    fecha_hasta: $('#filtroFechaHasta').val(),
                    cliente: $('#filtroCliente').val()
                },
                success: function(response) {
                    tablaCuentasCobrar.clear();
                    tablaCuentasCobrar.rows.add(response.cuentas);
                    tablaCuentasCobrar.draw();
                    actualizarResumen(response.resumen);
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'No se pudo aplicar el filtro' });
                }
            });
        }

        function limpiarFiltrosCuentasCobrar() {
            $('#filtroFechaDesde').val('');
            $('#filtroFechaHasta').val('');
            buscarCuentasCobrar();
        }

        // Abre el reporte PDF en una nueva pestaña, respetando exactamente
        // los mismos filtros que se ven en pantalla en ese momento.
        function generarReportePdf() {
            let params = new URLSearchParams({
                fecha_desde: $('#filtroFechaDesde').val() || '',
                fecha_hasta: $('#filtroFechaHasta').val() || '',
                cliente: $('#filtroCliente').val() || ''
            });

            window.open(URL_REPORTE_PDF_CUENTAS_COBRAR + '?' + params.toString(), '_blank');
        }
    </script>
@endsection
