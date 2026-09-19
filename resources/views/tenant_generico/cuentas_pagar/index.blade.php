@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Cuentas por Pagar')
@section('contenido')

    <style>
        .cxp-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .cxp-badge-pendiente { background: #EDE9FE; color: #6C3BFF; }
        .cxp-badge-pagado { background: #DCFCE7; color: #16A34A; }
        .cxp-badge-vencida { background: #FEE2E2; color: #DC2626; }

        .cxp-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .cxp-card .card-title {
            font-weight: 800;
            color: #1F2937;
        }

        #tabla_cuentas_pagar tbody tr {
            cursor: pointer;
        }

        #tabla_cuentas_pagar tbody tr:hover {
            background: #F9F7FF;
        }

        .cxp-resumen-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        @media (max-width: 992px) {
            .cxp-resumen-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .cxp-resumen-tile {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            padding: 14px 16px;
        }

        .cxp-resumen-tile .cxp-resumen-label {
            font-size: 11px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 4px;
        }

        .cxp-resumen-tile .cxp-resumen-valor {
            font-size: 20px;
            font-weight: 800;
            color: #1F2937;
        }

        .cxp-resumen-tile.destacado {
            background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
        }

        .cxp-resumen-tile.destacado .cxp-resumen-label,
        .cxp-resumen-tile.destacado .cxp-resumen-valor {
            color: #fff;
        }

        .cxp-resumen-tile.alerta .cxp-resumen-valor {
            color: #DC2626;
        }

        .cxp-filtros {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            padding: 16px;
            margin-bottom: 16px;
        }

        .cxp-filtros label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 4px;
        }
    </style>

    <div class="col-12">

        <!-- RESUMEN (reacciona a lo que muestra la tabla filtrada) -->
        <div class="cxp-resumen-row" id="cxpResumen">
            <div class="cxp-resumen-tile">
                <div class="cxp-resumen-label">Cuentas</div>
                <div class="cxp-resumen-valor" id="resCantidad">{{ $resumen['cantidad'] }}</div>
            </div>
            <div class="cxp-resumen-tile">
                <div class="cxp-resumen-label">Total Comprado</div>
                <div class="cxp-resumen-valor" id="resTotal">S/ {{ number_format($resumen['total'], 2) }}</div>
            </div>
            <div class="cxp-resumen-tile">
                <div class="cxp-resumen-label">Adelantado</div>
                <div class="cxp-resumen-valor" id="resAdelanto">S/ {{ number_format($resumen['adelanto'], 2) }}</div>
            </div>
            <div class="cxp-resumen-tile">
                <div class="cxp-resumen-label">Abonado</div>
                <div class="cxp-resumen-valor" id="resAbonado">S/ {{ number_format($resumen['abonado'], 2) }}</div>
            </div>
            <div class="cxp-resumen-tile destacado">
                <div class="cxp-resumen-label">Pendiente</div>
                <div class="cxp-resumen-valor" id="resPendiente">S/ {{ number_format($resumen['pendiente'], 2) }}</div>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="cxp-filtros">
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
                    <label>Proveedor (razón social o documento)</label>
                    <input type="text" class="form-control" id="filtroProveedor" placeholder="Buscar proveedor..." value="{{ $proveedor }}">
                </div>
                <div class="col-md-2 col-4 mb-2">
                    <button type="button" class="btn btn-block" style="background:#6C3BFF;color:#fff;font-weight:700;" onclick="buscarCuentasPagar()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltrosCuentasPagar()">
                        <i class="fas fa-times mr-1"></i> Quitar fechas (ver todo el historial)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#EDE9FE;color:#6C3BFF;font-weight:700;" onclick="generarReportePdf()">
                        <i class="fas fa-file-pdf mr-1"></i> Generar PDF
                    </button>
                    <span class="text-muted" style="font-size:12px;">
                        Tip: para ver cuánto se le debe a un proveedor en total, busca su nombre y quita las fechas.
                    </span>
                </div>
            </div>
        </div>

        <div class="card cxp-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar mr-1" style="color:#6C3BFF;"></i>
                        CUENTAS POR PAGAR
                    </h5>
                    <a href="{{ tenant_url('tenant.compras.compra.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a Compras
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="tabla_cuentas_pagar" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Compra</th>
                                <th>Proveedor</th>
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
        const URL_INDEX_CUENTAS_PAGAR = "{{ tenant_url('tenant.compras.cuentaspagar.index') }}";
        const URL_SHOW_CUENTA_PAGAR = "{{ tenant_url('tenant.compras.cuentaspagar.show', ['cuentaPagar' => ':id']) }}";
        const URL_REPORTE_PDF_CUENTAS_PAGAR = "{{ tenant_url('tenant.compras.cuentaspagar.reporte.pdf') }}";

        let tablaCuentasPagar;

        function badgeEstado(cuenta) {
            if (cuenta.CXP_Estado == 2) {
                return '<span class="cxp-badge cxp-badge-pagado">Pagado</span>';
            }
            if (cuenta.vencida) {
                return '<span class="cxp-badge cxp-badge-vencida">Vencida</span>';
            }
            return '<span class="cxp-badge cxp-badge-pendiente">Pendiente</span>';
        }

        function moneyFmt(v) {
            return 'S/ ' + parseFloat(v || 0).toFixed(2);
        }

        $(document).ready(function() {
            tablaCuentasPagar = $('#tabla_cuentas_pagar').DataTable({
                data: @json($cuentas),
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros',
                    zeroRecords: 'No hay cuentas por pagar con estos filtros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'No hay registros',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar en esta página:',
                    paginate: { next: 'Siguiente', previous: 'Anterior' }
                },
                columns: [
                    { data: 'COM_Id', render: (d) => '#' + d },
                    {
                        data: null,
                        render: (row) => row.PROV_RazonSocial + '<div class="text-muted" style="font-size:11px;">' + (row.PROV_NumDocumento || '') + '</div>'
                    },
                    { data: 'CXP_MontoTotal', render: moneyFmt },
                    { data: 'CXP_MontoAdelanto', render: moneyFmt },
                    { data: 'CXP_MontoAbonado', render: moneyFmt },
                    { data: 'CXP_MontoPendiente', render: (v) => '<strong>' + moneyFmt(v) + '</strong>' },
                    {
                        data: null,
                        // CXP_TieneCuotas puede llegar como string "0"/"1"
                        // desde una consulta cruda (DB::table), y en JS el
                        // string "0" es verdadero (truthy) — por eso se
                        // compara con Number(...) en vez de usarlo directo.
                        render: (row) => Number(row.CXP_TieneCuotas) ? (row.CXP_FechaVencimiento || '-') : 'Libre'
                    },
                    { data: null, render: badgeEstado },
                    {
                        data: 'CXP_Id',
                        orderable: false,
                        searchable: false,
                        render: () => '<i class="fas fa-chevron-right text-muted"></i>'
                    },
                ]
            });

            $('#tabla_cuentas_pagar tbody').on('click', 'tr', function() {
                let data = tablaCuentasPagar.row(this).data();
                if (data) {
                    window.location = URL_SHOW_CUENTA_PAGAR.replace(':id', data.CXP_Id);
                }
            });

            // Buscar tambien al presionar Enter en cualquiera de los filtros.
            $('#filtroFechaDesde, #filtroFechaHasta, #filtroProveedor').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    buscarCuentasPagar();
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

        function buscarCuentasPagar() {
            $.ajax({
                url: URL_INDEX_CUENTAS_PAGAR,
                method: 'GET',
                data: {
                    fecha_desde: $('#filtroFechaDesde').val(),
                    fecha_hasta: $('#filtroFechaHasta').val(),
                    proveedor: $('#filtroProveedor').val()
                },
                success: function(response) {
                    tablaCuentasPagar.clear();
                    tablaCuentasPagar.rows.add(response.cuentas);
                    tablaCuentasPagar.draw();
                    actualizarResumen(response.resumen);
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'No se pudo aplicar el filtro' });
                }
            });
        }

        function limpiarFiltrosCuentasPagar() {
            $('#filtroFechaDesde').val('');
            $('#filtroFechaHasta').val('');
            buscarCuentasPagar();
        }

        // Abre el reporte PDF en una nueva pestaña, respetando exactamente
        // los mismos filtros que se ven en pantalla en ese momento.
        function generarReportePdf() {
            let params = new URLSearchParams({
                fecha_desde: $('#filtroFechaDesde').val() || '',
                fecha_hasta: $('#filtroFechaHasta').val() || '',
                proveedor: $('#filtroProveedor').val() || ''
            });

            window.open(URL_REPORTE_PDF_CUENTAS_PAGAR + '?' + params.toString(), '_blank');
        }
    </script>
@endsection
