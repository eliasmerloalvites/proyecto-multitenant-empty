<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Orden de Servicio - Mantenimiento General Inyectada</title>

    @php
        // Paleta de marca configurada por el tenant en Configuracion >
        // Empresa (Color Marca Base/Hover = color_main/color_light).
        $paleta = paleta_documento($empresa);
    @endphp

    <style>

        :root {
            --primary: {{ $paleta['primary'] }};
            --primary-light: {{ $paleta['primary_light'] }};
            --primary-dark: {{ $paleta['primary_dark'] }};
        }

        @page {
            margin: 0px;
            padding: 10px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #0f172a;
            background: #f8fafc;
        }

        /* GLOBAL  */

        * {
            box-sizing: border-box;
        }

        table {
            border-collapse: collapse;
        }

        .w-100 {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
        }

        /* MAIN CONTAINER */

        .container {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 0px;
            overflow: hidden;
            background: white;
        }

        /* HEADER */

        .header {
            padding: 16px 22px 14px 22px;
            border-bottom: 4px solid var(--primary);
            position: relative;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo {
            width: 170px;
        }

        .title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
        }

        .header-info {
            font-size: 10.5px;
            line-height: 1.5;
        }

        .header-info strong {
            color: var(--primary);
        }

        .blue-shape {
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 180px;
            height: 14px;
            background: var(--primary);
            border-radius: 0 20px 20px 0;
        }

        /* SECTION */

        .section {
            margin: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .section-title {
            background: var(--primary);
            color: white;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: bold;
        }

        .section-body {
            padding: 12px;
        }

        /* COMPANY — barra tipo membrete corporativo */

        .empresa-bar {
            margin: 12px;
            background: var(--primary-dark);
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .empresa-bar-table {
            width: 100%;
        }

        .empresa-bar-table td {
            padding: 13px 18px;
            vertical-align: middle;
            border-left: 1px solid rgba(255, 255, 255, .22);
        }

        .empresa-bar-table td:first-child {
            border-left: none;
        }

        .empresa-item-label {
            color: rgba(255, 255, 255, .72);
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: .6px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .empresa-item-value {
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.35;
        }

        /* =========================================
   CLIENT
========================================= */

        .client-table {
            width: 100%;
        }

        .client-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }

        .client-label {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }

        .client-value {
            margin-top: 4px;
            font-size: 11px;
            color: #334155;
        }

        .client-value.blank {
            color: #94a3b8;
            font-style: italic;
        }

        /* =========================================
   DOUBLE BOX
========================================= */

        .double-table {
            width: 100%;
        }

        .double-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }

        .small-box {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .small-title {
            background: var(--primary);
            color: white;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: bold;
        }

        .small-body {
            padding:14px;
            min-height:40px;
            white-space:pre-line;
            line-height:1.7;
            color:#334155;
            font-size:11px;
        }

        /* =========================================
   LARGE BOX
========================================= */

        .large-body {
            padding: 14px;
            min-height: 40px;
            white-space:pre-line;
            line-height: 1.7;
            font-size: 11px;
            color: #334155;
        }

        /* =========================================
   PARTS
========================================= */

        .parts-wrapper {
            width: 100%;
        }

        .parts-wrapper td {
            vertical-align: top;
        }

        .parts-table {
            width: 100%;
        }

        .parts-table th {
            background-color: var(--primary);
            color: #ffffff !important;
            font-weight: bold;
            padding: 9px;
            border: 1px solid rgba(255, 255, 255, .35);
            font-size: 10px;
        }

        .parts-table td {
            border: 1px solid #e5e7eb;
            padding: 9px;
            font-size: 10px;
        }

        .parts-total {
            font-size: 12px;
            font-weight: bold;
            color: var(--primary);
        }

        /* =========================================
   SERVICE BOX
========================================= */

        .service-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .service-title {
            background: #f8fbff;
            color: var(--primary);
            padding: 10px 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .service-body {
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        /* =========================================
   EVIDENCE
========================================= */

        .evidence-grid{
            width:100%;
            border-collapse:collapse;
        }

        .evidence-grid td{
            width:50%;
            padding:6px;
            vertical-align:top;
        }

        .evidence-image{
            width:100%;
            height:400px;
            border-radius:8px;
            object-fit:cover;
        }

        /* =========================================
   SIGNATURE
========================================= */

        .sign-table {
            width: 100%;
            margin-top: 25px;
        }

        .sign-table td {
            width: 33.33%;
            text-align: center;
            padding: 0 10px;
        }

        .sign-line {
            border-top: 1px solid #cbd5e1;
            margin-bottom: 8px;
        }

        .sign-title {
            font-size: 10px;
            font-weight: bold;
        }

        .sign-sub {
            margin-top: 4px;
            font-size: 9px;
            color: #64748b;
        }

        /* =========================================
   FOOTER
========================================= */

        .footer {
            margin-top: 15px;
            background: var(--primary);
            color: white;
            padding: 12px 18px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-left {
            font-size: 10px;
        }

        .footer-right {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }


        .service-box {
            width: 100%;
            border: 2px solid var(--primary);
            border-radius: 6px;
            overflow: hidden;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .service-title {
            background: var(--primary);
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: .3px;
        }

        .service-content {
            padding: 12px;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
        }

        .service-table td {
            padding: 3px 4px;
            vertical-align: middle;
        }

        .number {
            width: 40px;
            font-weight: bold;
            color: var(--primary-dark);
        }

        .description {
            width: 320px;
            font-weight: 600;
        }

        .status {
            width: 70px;
            text-align: center;
        }

        .status-ok {
            display: inline-block;
            background: var(--primary);
            color: white;
            font-size: 10px;
            font-weight: bold;
            border-radius: 50px;
            padding: 2px 8px;
        }

        .status-no {
            color: #c62828;
            font-weight: bold;
            text-decoration: underline;
        }

        .reference {
            width: 220px;
        }

        .ref-inline {
            display: inline-block;
            min-width: 65px;
            margin-right: 10px;
            font-weight: bold;
            color: #111;
        }

        .ref-value {
            display: inline-block;
            min-width: 50px;
            border-bottom: 1px solid #d8d8d8;
            text-align: center;
            color: var(--primary-dark);
            font-weight: bold;
        }

        .separator td {
            padding-top: 10px;
        }

        /* =========================================
   ESTADO DE RECEPCION (inventario / inspeccion)
========================================= */

        .rec-cat-nombre {
            font-size: 11px;
            font-weight: bold;
            color: var(--primary-dark);
            margin: 10px 0 4px;
        }

        .rec-cat-nombre:first-child {
            margin-top: 0;
        }

        .rec-inventario-table {
            width: 100%;
            margin-bottom: 4px;
        }

        .rec-inventario-table td {
            padding: 3px 4px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10.5px;
        }

        .rec-inv-label {
            width: 32%;
            color: #334155;
        }

        .rec-inv-value {
            width: 18%;
        }

        .rec-tag {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: bold;
        }

        .rec-tag-ok {
            background: #dcfce7;
            color: #166534;
        }

        .rec-tag-warn {
            background: #fef9c3;
            color: #854d0e;
        }

        .rec-tag-bad {
            background: #fee2e2;
            color: #991b1b;
        }

        .rec-tag-info {
            background: #e0e7ff;
            color: #3730a3;
        }

        .rec-tag-blank {
            background: #f1f5f9;
            color: #94a3b8;
        }

        .rec-obs-general {
            margin-top: 8px;
            padding: 8px;
            background: #f8fafc;
            border-radius: 4px;
            font-size: 10.5px;
            color: #334155;
        }

        .rec-inspeccion-grid {
            width: 100%;
        }

        .rec-inspeccion-cell {
            width: 33.33%;
            vertical-align: top;
            padding: 4px;
        }

        .rec-inspeccion-box {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            min-height: 30px;
        }

        .rec-inspeccion-titulo {
            font-size: 10.5px;
            font-weight: bold;
            color: var(--primary-dark);
            margin-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }

        .rec-inspeccion-item {
            font-size: 9.5px;
            padding: 2px 0;
        }

        .rec-inspeccion-item-label {
            color: #334155;
            margin-right: 4px;
        }

        .rec-inspeccion-obs {
            margin-top: 6px;
            font-size: 9px;
            color: #64748b;
            font-style: italic;
        }

        /* =========================================
   CAMPOS EN BLANCO (para llenar a mano)
========================================= */

        .blank-grid {
            width: 100%;
        }

        .blank-grid td {
            width: 33.33%;
            padding: 4px 8px;
            font-size: 10.5px;
            vertical-align: top;
        }

        .checkbox-square {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #64748b;
            margin-right: 5px;
            vertical-align: middle;
        }

        .blank-line {
            display: inline-block;
            border-bottom: 1px solid #cbd5e1;
            min-width: 80px;
            height: 12px;
        }

        .badge-estado {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .legend {
            margin-top: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            display: flex;
            gap: 25px;
            font-size: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .circle-blue {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary);
        }

        .circle-empty {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid #999;
        }
    </style>

</head>

<body>

    @php
        $path = public_path(ltrim($empresa->logo ?? 'images/logo.png', '/'));
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $coloresEstado = [
            'APROBADO' => '#16a34a',
            'PENDIENTE' => '#d97706',
            'OBSERVADO' => '#dc2626',
        ];
    @endphp

    <div class="container">
        <div class="header no-break">
            <div class="blue-shape"></div>
            <table class="header-table">
                <tr>
                    <td width="28%">
                        <img src="{{ $logo }}" class="logo">
                    </td>
                    <td width="47%">
                        <div class="title">
                            ORDEN DE SERVICIO
                        </div>
                        <div class="subtitle">
                            Mantenimiento General Inyectada
                        </div>
                    </td>

                    <td width="25%">
                        <div class="header-info">
                            <strong>N° ORDEN:</strong> MGI-{{ str_pad($mttoPreventivo->MGI_Id, 6, '0', STR_PAD_LEFT) }} <br>
                            <strong>FECHA:</strong> {{ date('d/m/Y', strtotime($mttoPreventivo->MGI_FechaCreacion)) }} <br>
                            <strong>HORA:</strong> {{ date('H:i:s', strtotime($mttoPreventivo->MGI_FechaCreacion)) }} <br>
                            <strong>ASESOR/TÉCNICO:</strong> {{ $mttoPreventivo->personal }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- EMPRESA -->
        <div class="empresa-bar no-break">
            <table class="empresa-bar-table">
                <tr>
                    <td width="42%">
                        <div class="empresa-item-label">Razón Social</div>
                        <div class="empresa-item-value">{{ $empresa->razon_social }}</div>
                    </td>
                    <td width="20%">
                        <div class="empresa-item-label">RUC</div>
                        <div class="empresa-item-value">{{ $empresa->ruc }}</div>
                    </td>
                    <td width="38%">
                        <div class="empresa-item-label">Dirección</div>
                        <div class="empresa-item-value">
                            {{ trim(collect([$empresa->direccion, $empresa->provincia])->filter()->implode(' - ')) ?: 'Sin registrar' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- CLIENTE + SERVICIO (una fila, 2 columnas) -->
        <div style="padding-left: 5px; padding-right: 5px;">
            <table class="double-table">
                <tr>
                    <td>
                        <div class="small-box">
                            <div class="small-title">DATOS DEL CLIENTE</div>
                            <div class="section-body">
                                <table class="client-table">
                                    <tr>
                                        <td width="50%">
                                            <div class="client-label">PROPIETARIO</div>
                                            <div class="client-value">{{ $mttoPreventivo->MGI_Propietario }}</div>
                                        </td>
                                        <td width="50%">
                                            <div class="client-label">PLACA</div>
                                            <div class="client-value">{{ $mttoPreventivo->MGI_Placa }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="client-label">TELÉFONO</div>
                                            <div class="client-value">{{ $mttoPreventivo->MGI_celular }}</div>
                                        </td>
                                        <td>
                                            <div class="client-label">UNIDAD</div>
                                            <div class="client-value">{{ $mttoPreventivo->MGI_Unidad }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="client-label">KILOMETRAJE</div>
                                            <div class="client-value">{{ $mttoPreventivo->MGI_KMEntrada }}</div>
                                        </td>
                                        <td>
                                            <div class="client-label">DNI / RUC</div>
                                            <div class="client-value {{ empty($datosVenta['CLI_NumDocumento']) ? 'blank' : '' }}">
                                                {{ $datosVenta['CLI_NumDocumento'] ?? 'Sin venta asociada' }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="client-label">FORMA DE PAGO</div>
                                            <div class="client-value {{ empty($datosVenta['MEP_Pago']) ? 'blank' : '' }}">
                                                {{ $datosVenta['MEP_Pago'] ?? 'Sin venta asociada' }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small-box">
                            <div class="small-title">DATOS DEL SERVICIO</div>
                            <div class="section-body">
                                <table class="client-table">
                                    <tr>
                                        <td width="50%">
                                            <div class="client-label">TIPO DE MANTENIMIENTO</div>
                                            <div class="client-value">{{ $etiquetaTipo }}</div>
                                        </td>
                                        <td width="50%">
                                            <div class="client-label">ESTADO</div>
                                            <div class="client-value">
                                                <span class="badge-estado" style="background:{{ $coloresEstado[$mttoPreventivo->MGI_Estado] ?? '#64748b' }}">
                                                    {{ $mttoPreventivo->MGI_Estado }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="client-label">FECHA DE INICIO</div>
                                            <div class="client-value {{ empty($mttoPreventivo->MGI_FechaInicio) ? 'blank' : '' }}">
                                                {{ $mttoPreventivo->MGI_FechaInicio ? date('d/m/Y H:i', strtotime($mttoPreventivo->MGI_FechaInicio)) : 'Sin registrar' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="client-label">FECHA DE ENTREGA</div>
                                            <div class="client-value {{ empty($mttoPreventivo->MGI_FechaTermino) ? 'blank' : '' }}">
                                                {{ $mttoPreventivo->MGI_FechaTermino ? date('d/m/Y H:i', strtotime($mttoPreventivo->MGI_FechaTermino)) : 'Sin registrar' }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        @include('tenant_tallermoto.mantenimientos.partials.orden-servicio-recepcion', ['estadoRecepcion' => $estadoRecepcion])

        <!-- DOUBLE -->
        <div style="padding-left: 5px; padding-right: 5px;">
            <table class="double-table">
                <tr>
                    <td>
                        <div class="small-box">
                            <div class="small-title">INGRESO DE UNIDAD</div>
                            <div class="small-body">{{ $mttoPreventivo->MGI_DetalleIngreso }}</div>
                        </div>
                    </td>

                    <td>
                        <div class="small-box">
                            <div class="small-title">OBSERVACIONES</div>
                            <div class="small-body">{{ $mttoPreventivo->MGI_DetalleObservacion }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- REALIZADO -->
        <div class="section no-break">
            <div class="section-title">DETALLE DE LO REALIZADO</div>
            <div class="large-body">{{ $mttoPreventivo->MGI_DetalleRealizado }}</div>
        </div>

        <!-- CORRECCION -->
        <div class="section no-break">
            <div class="section-title">CORRECCIÓN DE OBSERVACIONES</div>
            <div class="large-body">{{ $mttoPreventivo->MGI_CorrecionObservacion }}</div>
        </div>

        <!-- PARTES -->
        <div class="section no-break">
            <table class="parts-wrapper">
                <tr>
                    <td width="68%">

                        <div class="section-title">REPUESTOS / MATERIALES</div>
                        <table class="parts-table">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>DESCRIPCIÓN</th>
                                    <th>CANT.</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalle as $det)
                                    <tr>
                                        <td class="text-center">{{ $det->MGID_Item }}</td>
                                        <td>{{ $det->MGID_Descripcion }}</td>
                                        <td class="text-center">1</td>
                                        <td class="text-right">S/ {{ number_format($det->MGI_Precio,2) }}</td>
                                    </tr>
                                @endforeach
                                @if($total_detalle > 0)
                                    <tr>
                                        <td colspan="3" class="text-right parts-total">
                                            TOTAL
                                        </td>
                                        <td class="text-right parts-total">
                                            S/ {{ number_format($total_detalle,2) }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                    <td width="32%" style="padding-left:10px">
                        <div class="service-box">
                            <div class="service-title">PRÓXIMO CAMBIO DE ACEITE</div>
                            <div class="service-body">{{ $mttoPreventivo->MGI_ProximoCambioAceite }}</div>
                        </div>
                        <div class="service-box">
                            <div class="service-title">PRÓXIMO SERVICIO</div>
                            <div class="service-body">{{ $mttoPreventivo->MGI_ProximoServicio }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section no-break">
            <div class="section-title">
                CHECK LIST DEL SERVICIO
            </div>
            <div class="service-content">

                <table class="service-table">
                    @php $itemNum = 1; @endphp
                    @if($mttoPreventivo->MGI_Det1 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">CAMBIO DE ACEITE</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det1 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det1 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference">
                                <span class="ref-value">{{ $mttoPreventivo->MGI_Det1Informacion }}</span>
                            </td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det2 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">CAMBIO DE FILTRO DE ACEITE</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det2 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det2 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det3 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LIMPIEZA DE CHASIS CON AIRE COMP.</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det3 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det3 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det4 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LIMPIEZA DE CABLES ELEC CON AIRE COMP.</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det4 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det4 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det5 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">DESENGRASE INTERNO DE LA UNIDAD</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det5 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det5 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det6 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">MANTENIMIENTO DE FILTRO DE AIRE</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det6 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det6 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det7 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LIMPIEZA DEL CUERPO DE ACELERACION</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det7 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det7 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det8 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LAVADO DE INYECTOR EN ULTRASONIDO</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det8 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det8 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det9 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">AJUSTE DE VÁLVULAS</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det9 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det9 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align:center; width:50%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">ADMISIÓN</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det9Admision }}
                                            </div>
                                        </td>
                                        <td style="text-align:center; width:50%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">ESCAPE</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det9Escape }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det10 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">REVISIÓN Y CALIBRACIÓN DE BUJÍA</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det10 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det10 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align:center; width:50%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">MEDIDA</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det10Medida }}
                                            </div>
                                        </td>
                                        <td style="text-align:center; width:50%;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det11 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">MEDICION DE COMPRESION DEL MOTOR</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det11 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det11 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align:center; width:50%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">MEDIDA</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det11Medida }}
                                            </div>
                                        </td>
                                        <td style="text-align:center; width:50%;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det12 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">AJUSTE DE LA BRIDA DEL TUBO DE ESCAPE</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det12 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det12 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det13 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LAVADO Y AJUSTE DEL SISTEMA DE ARRASTRE</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det13 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det13 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det14 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">MANTENIMIENTO DE FRENO DELANTERO</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det14 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det14 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det15 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">MANTENIMIENTO DE FRENO POSTERIOR</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det15 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det15 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det16 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">AJUSTE DE PERNOS DE CHASIS</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det16 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det16 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det17 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LIMPIEZA DE CONECTORES ELÉCTRICOS</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det17 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det17 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if(!empty($mttoPreventivo->MGI_Det18))
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">PRESIÓN DE NEUMÁTICO DELANTERO</td>
                            <td class="status">
                                <span class="ref-value">{{ $mttoPreventivo->MGI_Det18 }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if(!empty($mttoPreventivo->MGI_Det19))
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">PRESIÓN DE NEUMÁTICO POSTERIOR</td>
                            <td class="status">
                                <span class="ref-value">{{ $mttoPreventivo->MGI_Det19 }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det20 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">REVISION Y TEST DE LIQUIDO DE FRENOS</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det20 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det20 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align:center; width:50%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">HUMEDAD</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det20Humedad }}
                                            </div>
                                        </td>
                                        <td style="text-align:center; width:50%;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det21 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">REVISION Y TEST DE LIQUIDO REFRIGERANTE</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det21 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det21 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det22 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">REVISIÓN DEL SISTEMA DE ENFRIAMIENTO</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det22 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det22 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align:center; width:50%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">VENTILADOR</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det22Ventilador }}
                                            </div>
                                        </td>
                                        <td style="text-align:center; width:50%;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det23 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LUBRICACIÓN DEL SISTEMA DE ARRASTRE</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det23 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det23 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det24 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">TEST DE BATERIA</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det24 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det24 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align:center; width:33%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">% VIDA</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det24Vida }}
                                            </div>
                                        </td>
                                        <td style="text-align:center; width:33%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">V. CARGA</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det24Carga }}
                                            </div>
                                        </td>
                                        <td style="text-align:center; width:33%;">
                                            <div style="font-weight:bold; font-size:10px; color:#111; margin-bottom:4px;">V. ARRANQUE</div>
                                            <div style="border:1px solid #d8d8d8; border-radius:4px; padding:4px; font-weight:bold; color:var(--primary-dark); background:#fff;">
                                                {{ $mttoPreventivo->MGI_Det24Arranque }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det25 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">LIMPIEZA DEL SENSOR DE OXIGENO</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det25 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det25 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det26 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">ESCANEO</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det26 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det26 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                    @if($mttoPreventivo->MGI_Det27 == 'SI')
                        <tr>
                            <td class="number">{{ $itemNum++ }}.0</td>
                            <td class="description">VERIFICACIÓN DEL SISTEMA DE LUCES</td>
                            <td class="status">
                                <span class="status-{{ $mttoPreventivo->MGI_Det27 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGI_Det27 == 'SI' ? 'OK' : 'NO' }}</span>
                            </td>
                            <td class="reference"></td>
                        </tr>
                    @endif

                </table>
            </div>

        </div>

        <!-- EVIDENCIAS -->
        <div class="section no-break">
            <div class="section-title">
                EVIDENCIA FOTOGRÁFICA
            </div>
            <div class="section-body">
                <table class="evidence-grid">
                    @foreach ($imagenes->chunk(2) as $chunk)
                        <tr>
                            @foreach ($chunk as $img)
                                @php
                                    $path = public_path(ltrim($img->MGII_url, '/'));
                                    $base64 = null;
                                    if (file_exists($path)) {
                                        $type = pathinfo($path, PATHINFO_EXTENSION);
                                        $data = file_get_contents($path);
                                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                    }
                                @endphp
                                <td>
                                    @if ($base64)
                                        <img src="{{ $base64 }}" class="evidence-image">
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        @php
            $coloresPrioridad = ['ALTA' => '#dc2626', 'MEDIA' => '#d97706', 'BAJA' => '#16a34a'];
            $verifItems = [
                'MGI_VerifArranque' => 'Arranque correcto',
                'MGI_VerifLuces' => 'Luces',
                'MGI_VerifDireccionales' => 'Direccionales',
                'MGI_VerifNivelAceite' => 'Nivel de aceite',
                'MGI_VerifPruebaRuta' => 'Prueba de ruta',
                'MGI_VerifLavado' => 'Lavado',
            ];
        @endphp

        <!-- RECOMENDACIONES -->
        <div class="section no-break">
            <div class="section-title">RECOMENDACIONES</div>
            <div class="section-body">
                @if ($mttoPreventivo->MGI_Recomendacion)
                    @if ($mttoPreventivo->MGI_RecomendacionPrioridad)
                        <span class="badge-estado" style="background:{{ $coloresPrioridad[$mttoPreventivo->MGI_RecomendacionPrioridad] }}">
                            PRIORIDAD {{ $mttoPreventivo->MGI_RecomendacionPrioridad }}
                        </span>
                    @endif
                    <div style="margin-top:6px; font-size:11px; color:#334155;">{{ $mttoPreventivo->MGI_Recomendacion }}</div>
                @else
                    <div style="font-size:10.5px; color:#94a3b8; font-style:italic;">Sin recomendaciones registradas.</div>
                @endif
            </div>
        </div>

        <!-- VERIFICACION FINAL -->
        <div class="section no-break">
            <div class="section-title">VERIFICACIÓN FINAL (CONTROL DE CALIDAD)</div>
            <div class="section-body">
                <table class="blank-grid">
                    @foreach (array_chunk($verifItems, 3, true) as $fila)
                        <tr>
                            @foreach ($fila as $campo => $etiqueta)
                                <td>
                                    <span class="rec-tag {{ $mttoPreventivo->$campo ? 'rec-tag-ok' : 'rec-tag-blank' }}">
                                        {{ $mttoPreventivo->$campo ? '✓' : '—' }}
                                    </span>
                                    {{ $etiqueta }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
                @if ($mttoPreventivo->MGI_VerifOtros)
                    <div style="margin-top:6px; font-size:10.5px;"><strong>Otros:</strong> {{ $mttoPreventivo->MGI_VerifOtros }}</div>
                @endif
                <div style="margin-top:8px; font-size:10.5px;">
                    <strong>Conforme para entrega:</strong>
                    <span class="rec-tag {{ $mttoPreventivo->MGI_VerifConforme ? 'rec-tag-ok' : 'rec-tag-blank' }}">
                        {{ $mttoPreventivo->MGI_VerifConforme ? 'SÍ' : 'PENDIENTE' }}
                    </span>
                    &nbsp;&nbsp; <strong>Técnico:</strong> {{ $mttoPreventivo->personal }}
                    &nbsp;&nbsp; <strong>Fecha:</strong>
                    {{ $mttoPreventivo->MGI_FechaTermino ? date('d/m/Y', strtotime($mttoPreventivo->MGI_FechaTermino)) : 'Sin registrar' }}
                </div>
            </div>
        </div>

        @if(tenant('plan') === 'plus' || tenant('plan') === 'empresarial')
            <div class="section no-break">
                <div class="section-body">
                    <table class="sign-table">
                        <tr>
                            <td>
                                <div class="sign-line"></div>
                                <div class="sign-title">FIRMA DEL CLIENTE</div>
                                <div class="sign-sub">Conforme / entrega del vehículo</div>
                            </td>
                            <td>
                                <div class="sign-line"></div>
                                <div class="sign-title">FIRMA DEL TÉCNICO</div>
                                <div class="sign-sub">Responsable</div>
                            </td>
                            <td>
                                <div class="sign-line"></div>
                                <div class="sign-title">
                                    FIRMA DEL REPRESENTANTE
                                </div>
                                <div class="sign-sub">
                                    {{ $empresa->nombre_comercial ?: $empresa->razon_social }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        {{ $empresa->ruc }} &nbsp;&nbsp;&nbsp;
                        {{ $empresa->correo }} &nbsp;&nbsp;&nbsp;
                        {{ $url }}
                    </td>
                    <td class="footer-right">
                        Gracias por su confianza
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>

</html>
