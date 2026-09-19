<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cuentas por Pagar</title>
    <style>
        @page {
            margin: 90px 28px 60px 28px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1F2937;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 70px;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 9px;
            color: #6B7280;
            text-align: center;
            border-top: 1px solid #E5E7EB;
            padding-top: 6px;
        }

        .titulo-reporte {
            font-size: 18px;
            font-weight: 700;
            color: #6C3BFF;
            margin: 0 0 2px 0;
        }

        .subtitulo-reporte {
            font-size: 10px;
            color: #6B7280;
            margin: 0 0 8px 0;
        }

        .filtros-aplicados {
            font-size: 10px;
            color: #374151;
            background: #F9F7FF;
            border: 1px solid #EDE9FE;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 12px;
        }

        table.resumen {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.resumen td {
            width: 20%;
            border: 1px solid #EDE9FE;
            background: #F9F7FF;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
        }

        table.resumen td.destacado {
            background: #6C3BFF;
            color: #fff;
            border-color: #6C3BFF;
        }

        table.resumen .label {
            display: block;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #6B7280;
            margin-bottom: 3px;
        }

        table.resumen td.destacado .label {
            color: #EDE9FE;
        }

        table.resumen .valor {
            display: block;
            font-size: 13px;
            font-weight: 700;
        }

        .cuenta-bloque {
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .cuenta-header {
            width: 100%;
            margin-bottom: 6px;
        }

        .cuenta-header .compra-titulo {
            font-size: 12.5px;
            font-weight: 700;
            color: #1F2937;
        }

        .cuenta-header .proveedor-info {
            font-size: 10px;
            color: #4B5563;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pendiente { background: #EDE9FE; color: #6C3BFF; }
        .badge-pagado { background: #DCFCE7; color: #16A34A; }
        .badge-vencida { background: #FEE2E2; color: #DC2626; }

        table.montos-cuenta {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        table.montos-cuenta td {
            width: 25%;
            font-size: 10px;
            padding: 4px 6px;
            border: 1px solid #F3F4F6;
        }

        table.montos-cuenta td .m-label {
            display: block;
            color: #6B7280;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        table.montos-cuenta td .m-valor {
            display: block;
            font-weight: 700;
            font-size: 11px;
        }

        table.detalle {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        table.detalle th {
            background: #F3F4F6;
            font-size: 9px;
            text-transform: uppercase;
            color: #374151;
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #E5E7EB;
        }

        table.detalle td {
            font-size: 9.5px;
            padding: 4px 6px;
            border: 1px solid #E5E7EB;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: #9CA3AF; }
        .sub-titulo { font-size: 10px; font-weight: 700; color: #374151; margin: 8px 0 3px 0; }

        .sin-resultados {
            text-align: center;
            color: #6B7280;
            padding: 30px 0;
        }
    </style>
</head>
<body>

    <header>
        <div class="titulo-reporte">Reporte de Cuentas por Pagar</div>
        <div class="subtitulo-reporte">Generado el {{ $fechaGeneracion->format('d/m/Y H:i') }}</div>
    </header>

    <footer>
        Reporte de Cuentas por Pagar &middot; Generado el {{ $fechaGeneracion->format('d/m/Y H:i') }}
    </footer>

    <div class="filtros-aplicados">
        <strong>Filtros aplicados:</strong>
        Desde: {{ $fechaDesde ?: 'sin límite' }}
        &nbsp;|&nbsp; Hasta: {{ $fechaHasta ?: 'sin límite' }}
        &nbsp;|&nbsp; Proveedor: {{ $proveedor !== '' ? $proveedor : 'todos' }}
    </div>

    <table class="resumen">
        <tr>
            <td>
                <span class="label">Cuentas</span>
                <span class="valor">{{ $resumen['cantidad'] }}</span>
            </td>
            <td>
                <span class="label">Total Comprado</span>
                <span class="valor">S/ {{ number_format($resumen['total'], 2) }}</span>
            </td>
            <td>
                <span class="label">Adelantado</span>
                <span class="valor">S/ {{ number_format($resumen['adelanto'], 2) }}</span>
            </td>
            <td>
                <span class="label">Abonado</span>
                <span class="valor">S/ {{ number_format($resumen['abonado'], 2) }}</span>
            </td>
            <td class="destacado">
                <span class="label">Pendiente</span>
                <span class="valor">S/ {{ number_format($resumen['pendiente'], 2) }}</span>
            </td>
        </tr>
    </table>

    @forelse ($cuentas as $cuenta)
        <div class="cuenta-bloque">
            <table class="cuenta-header">
                <tr>
                    <td style="width:70%;">
                        <span class="compra-titulo">Compra #{{ $cuenta->COM_Id }} &mdash; {{ $cuenta->PROV_RazonSocial }}</span><br>
                        <span class="proveedor-info">Doc: {{ $cuenta->PROV_NumDocumento ?: '-' }} &nbsp;|&nbsp; Emisión: {{ $cuenta->CXP_FechaEmision }}</span>
                    </td>
                    <td style="width:30%; text-align:right; vertical-align:middle;">
                        @if ($cuenta->CXP_Estado == \App\Models\Tenant\CuentaPagar::ESTADO_PAGADO)
                            <span class="badge badge-pagado">Pagado</span>
                        @elseif ($cuenta->vencida)
                            <span class="badge badge-vencida">Vencida</span>
                        @else
                            <span class="badge badge-pendiente">Pendiente</span>
                        @endif
                    </td>
                </tr>
            </table>

            <table class="montos-cuenta">
                <tr>
                    <td>
                        <span class="m-label">Total</span>
                        <span class="m-valor">S/ {{ number_format($cuenta->CXP_MontoTotal, 2) }}</span>
                    </td>
                    <td>
                        <span class="m-label">Adelanto</span>
                        <span class="m-valor">S/ {{ number_format($cuenta->CXP_MontoAdelanto, 2) }}</span>
                    </td>
                    <td>
                        <span class="m-label">Abonado</span>
                        <span class="m-valor">S/ {{ number_format($cuenta->CXP_MontoAbonado, 2) }}</span>
                    </td>
                    <td>
                        <span class="m-label">Pendiente</span>
                        <span class="m-valor">S/ {{ number_format($cuenta->CXP_MontoPendiente, 2) }}</span>
                    </td>
                </tr>
            </table>

            @if ((int) ($cuenta->CXP_TieneCuotas ?? 0))
                @if ($cuenta->cuotas->count())
                    <div class="sub-titulo">Plan de Cuotas</div>
                    <table class="detalle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vencimiento</th>
                                <th class="text-right">Programado</th>
                                <th class="text-right">Abonado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cuenta->cuotas as $cuota)
                                <tr>
                                    <td>{{ $cuota->CXPC_Numero }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($cuota->CXPC_FechaVencimiento)->format('d/m/Y') }}</td>
                                    <td class="text-right">S/ {{ number_format($cuota->CXPC_MontoProgramado, 2) }}</td>
                                    <td class="text-right">S/ {{ number_format($cuota->CXPC_MontoAbonado, 2) }}</td>
                                    <td>{{ $cuota->CXPC_Estado == \App\Models\Tenant\CuentaPagarCuota::ESTADO_PAGADO ? 'Pagado' : 'Pendiente' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

            <div class="sub-titulo">Historial de Abonos (lo ya pagado)</div>
            @if ($cuenta->abonos->count())
                <table class="detalle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th class="text-right">Monto</th>
                            <th>Cuota</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cuenta->abonos as $abono)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($abono->CXPA_Fecha)->format('d/m/Y H:i') }}</td>
                                <td>{{ $abono->metodo }}</td>
                                <td class="text-right">S/ {{ number_format($abono->CXPA_Monto, 2) }}</td>
                                <td>{{ $abono->CXPC_Id ? '#' . $abono->CXPC_Id : 'Libre' }}</td>
                                <td>{{ $abono->CXPA_Descripcion ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted" style="margin:2px 0 0 0;">Todavía no hay abonos registrados para esta cuenta.</p>
            @endif
        </div>
    @empty
        <div class="sin-resultados">No hay cuentas por pagar que coincidan con los filtros seleccionados.</div>
    @endforelse

</body>
</html>
