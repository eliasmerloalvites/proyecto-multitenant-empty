<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Formato Mantenimiento General Carburada</title>

    <style>

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
            border-bottom: 4px solid #00398A;
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
            font-size: 11px;
            line-height: 1.9;
        }

        .header-info strong {
            color: #00398A;
        }

        .blue-shape {
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 180px;
            height: 14px;
            background: #00398A;
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
            background: #00398A;
            color: white;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: bold;
        }

        .section-body {
            padding: 12px;
        }

        /* COMPANY */

        .company-table {
            width: 100%;
        }

        .company-table {
            width: 100%;
            border-collapse: collapse;
        }

        .company-table td {
            width: auto;
            padding: 10px 12px;
            vertical-align: middle;
        }

        .company-item {
            width: 100%;
        }

        .company-item td {
            padding: 0;
            vertical-align: middle;
        }

        .icon {
            width: 42px;
            height: 42px;
        }

        .company-label {
            color: #00398A;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .company-value {
            font-size: 12px;
            color: #0f172a;
            line-height: 1.4;
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
            background: #00398A;
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
            background: #00398A;
            /* Cabezeras Azul oscuro */
            color: white;
            padding: 9px;
            border: 1px solid #2563eb;
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
            color: #00398A;
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
            color: #00398A;
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
            background: #00398A;
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
            border: 2px solid #00398A;
            border-radius: 6px;
            overflow: hidden;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .service-title {
            background: #00398A;
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
            color: #0d47b5;
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
            background: #0d6efd;
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
            color: #0d47b5;
            font-weight: bold;
        }

        .separator td {
            padding-top: 10px;
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
            background: #0d6efd;
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
        //$path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pathBuilding = public_path('icons/building.png');
        $typeBuilding = pathinfo($pathBuilding, PATHINFO_EXTENSION);
        $dataBuilding = file_get_contents($pathBuilding);
        $buildingIcon = 'data:image/' . $typeBuilding . ';base64,' . base64_encode($dataBuilding);

        $pathIdentidad = public_path('icons/identidad.png');
        $typeIdentidad = pathinfo($pathIdentidad, PATHINFO_EXTENSION);
        $dataIdentidad = file_get_contents($pathIdentidad);
        $identidadIcon = 'data:image/' . $typeIdentidad . ';base64,' . base64_encode($dataIdentidad);

        $pathDireccion = public_path('icons/direccion.png');
        $typeDireccion = pathinfo($pathDireccion, PATHINFO_EXTENSION);
        $dataDireccion = file_get_contents($pathDireccion);
        $direccionIcon = 'data:image/' . $typeDireccion . ';base64,' . base64_encode($dataDireccion);
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
                            FORMATO DE MANTENIMIENTO GENERAL CARBURADA
                        </div>
                        <div class="subtitle">
                            Orden de Servicio / Mantenimiento
                        </div>
                    </td>

                    <td width="25%">
                        <div class="header-info">
                            <strong>CÓDIGO:</strong> AV-FO-001 <br>
                            <strong>VERSIÓN:</strong> 01 <br>
                            <strong>FECHA:</strong> {{ date('d/m/Y', strtotime($mttoPreventivo->MGC_FechaCreacion)) }} <br>
                            <strong>HORA:</strong> {{ date('H:i:s', strtotime($mttoPreventivo->MGC_FechaCreacion)) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- EMPRESA -->
        <div class="section no-break">
            <div class="section-body">
                <table class="company-table">
                    <tr>
                        <td>
                            <table class="company-item">
                                <tr>
                                    <td width="40">
                                        <img src="{{ $buildingIcon }}" class="icon">
                                    </td>
                                    <td>
                                        <div class="company-label">
                                            RAZÓN SOCIAL
                                        </div>
                                        <div class="company-value">
                                            {{ $empresa->razon_social }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="company-item">
                                <tr>
                                    <td width="40">
                                        <img src="{{ $identidadIcon }}" class="icon">
                                    </td>
                                    <td>
                                        <div class="company-label">RUC</div>
                                        <div class="company-value">{{ $empresa->ruc }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td>
                            <table class="company-item">
                                <tr>
                                    <td width="40">
                                        <img src="{{ $direccionIcon }}" class="icon">
                                    </td>
                                    <td>
                                        <div class="company-label">DIRECCIÓN</div>
                                        <div class="company-value">
                                            {{ $empresa->direccion }} - {{ $empresa->provincia }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- CLIENTE -->
        <div class="section no-break">
            <div class="section-title">
                DATOS DEL CLIENTE
            </div>
            <div class="section-body">
                <table class="client-table">
                    <tr>
                        <td width="50%">
                            <div class="client-label">PROPIETARIO</div>
                            <div class="client-value">{{ $mttoPreventivo->MGC_Propietario }}</div>

                        </td>

                        <td width="50%">
                            <div class="client-label">PLACA</div>
                            <div class="client-value">{{ $mttoPreventivo->MGC_Placa }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-label">TELÉFONO</div>
                            <div class="client-value">{{ $mttoPreventivo->MGC_celular }}</div>
                        </td>
                        <td>
                            <div class="client-label">UNIDAD</div>
                            <div class="client-value">{{ $mttoPreventivo->MGC_Unidad }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-label">KILOMETRAJE</div>
                            <div class="client-value">{{ $mttoPreventivo->MGC_KMEntrada }}</div>

                        </td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- DOUBLE -->
        <div style="padding-left: 5px; padding-right: 5px;">
            <table class="double-table">
                <tr>
                    <td>
                        <div class="small-box">
                            <div class="small-title">INGRESO DE UNIDAD</div>
                            <div class="small-body">{{ $mttoPreventivo->MGC_DetalleIngreso }}</div>
                        </div>
                    </td>

                    <td>
                        <div class="small-box">
                            <div class="small-title">OBSERVACIONES</div>
                            <div class="small-body">{{ $mttoPreventivo->MGC_DetalleObservacion }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- REALIZADO -->
        <div class="section no-break">
            <div class="section-title">DETALLE DE LO REALIZADO</div>
            <div class="large-body">{{ $mttoPreventivo->MGC_DetalleRealizado }}</div>

        </div>

        <!-- CORRECCION -->
        <div class="section no-break">
            <div class="section-title">CORRECCIÓN DE OBSERVACIONES</div>
            <div class="large-body">{{ $mttoPreventivo->MGC_CorrecionObservacion }}</div>
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
                                        <td class="text-center">{{ $det->MGCD_Item }}</td>
                                        <td>{{ $det->MGCD_Descripcion }}</td>
                                        <td class="text-center">1</td>
                                        <td class="text-right">S/ {{ number_format($det->MGC_Precio,2) }}</td>
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
                            <div class="service-body">{{ $mttoPreventivo->MGC_ProximoCambioAceite }}</div>
                        </div>
                        <div class="service-box">
                            <div class="service-title">PRÓXIMO SERVICIO</div>
                            <div class="service-body">{{ $mttoPreventivo->MGC_ProximoServicio }}</div>
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
                    <tr>
                        <td class="number">1.0</td>
                        <td class="description">CAMBIO DE ACEITE</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det1 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det1 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                            <span class="ref-value">{{ $mttoPreventivo->MGC_Det1Informacion }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="number">2.0</td>
                        <td class="description">CAMBIO DE FILTRO DE ACEITE</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det2 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det2 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">3.0</td>
                        <td class="description">LIMGCEZA DE CHASIS CON AIRE COMP.</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det3 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det3 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">4.0</td>
                        <td class="description">LIMGCEZA DE CABLES ELEC CON AIRE COMP.</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det4 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det4 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">5.0</td>
                        <td class="description">DESENGRASE INTERNO DE LA UNIDAD</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det5 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det5 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">6.0</td>
                        <td class="description">MANTENIMIENTO DE FILTRO DE AIRE</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det6 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det6 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">7.0</td>
                        <td class="description">MANTENIMIENTO DE CARBURADOR</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det7 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det7 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">8.0</td>
                        <td class="description">AJUSTE DE VÁLVULAS</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det8 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det8 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                            <table style="width: 100%;">
                                <tr>
                                    <!-- ADMISION -->
                                    <td style="text-align:center; width:50%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            ADMISIÓN
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det8Admision }}
                                        </div>
                                    </td>

                                    <!-- ESCAPE -->
                                    <td style="text-align:center; width:50%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            ESCAPE
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det8Escape }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="number">9.0</td>
                        <td class="description">REVISIÓN Y CALIBRACIÓN DE BUJÍA</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det9 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det9 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                            <table style="width: 100%;">
                                <tr>
                                    <!-- ADMISION -->
                                    <td style="text-align:center; width:50%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            MEDIDA
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det9Medida }}
                                        </div>
                                    </td>

                                    <!-- ESCAPE -->
                                    <td style="text-align:center; width:50%;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="number">10.0</td>
                        <td class="description">AJUSTE DE LA BRIDA DEL TUBO DE ESCAPE</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det10 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det10 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">11.0</td>
                        <td class="description">LAVADO Y AJUSTE DEL SISTEMA DE ARRASTRE</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det11 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det11 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">12.0</td>
                        <td class="description">MANTENIMIENTO DE FRENO DELANTERO</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det12 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det12 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">13.0</td>
                        <td class="description">MANTENIMIENTO DE FRENO POSTERIOR</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det13 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det13 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">14.0</td>
                        <td class="description">AJUSTE DE PERNOS DE CHASIS</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det14 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det14 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">15.0</td>
                        <td class="description">LIMGCEZA DE CONECTORES ELÉCTRICOS</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det15 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det15 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">16.0</td>
                        <td class="description">PRESIÓN DE NEUMÁTICO DELANTERO</td>
                        <td class="status">
                            <span class="ref-value">{{ $mttoPreventivo->MGC_Det16 }}</span>
                        </td>
                        <td class="reference">
                            
                        </td>
                    </tr>

                    <tr>
                        <td class="number">17.0</td>
                        <td class="description">PRESIÓN DE NEUMÁTICO POSTERIOR</td>
                        <td class="status">
                            <span class="ref-value">{{ $mttoPreventivo->MGC_Det17 }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">18.0</td>
                        <td class="description">REVISION Y TEST DE LIQUIDO DE FRENOS</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det18 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det18 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                            <table style="width: 100%;">
                                <tr>
                                    <!-- ADMISION -->
                                    <td style="text-align:center; width:50%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            HUMEDAD
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det18Humedad }}
                                        </div>
                                    </td>

                                    <!-- ESCAPE -->
                                    <td style="text-align:center; width:50%;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="number">19.0</td>
                        <td class="description">REVISION DEL SISTEMA DE ENFRIAMIENTO</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det19 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det19 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                            <table style="width: 100%;">
                                <tr>
                                    <!-- ADMISION -->
                                    <td style="text-align:center; width:50%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            VENTILADOR
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det19Ventilador }}
                                        </div>
                                    </td>

                                    <!-- ESCAPE -->
                                    <td style="text-align:center; width:50%;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="number">20.0</td>
                        <td class="description">LUBRICACIÓN DEL SISTEMA DE ARRASTRE</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det20 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det20 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        <td class="reference">
                        </td>
                    </tr>

                    <tr>
                        <td class="number">21.0</td>
                        <td class="description">TEST DE BATERIA</td>
                        <td class="status">
                            <span
                                class="status-{{ $mttoPreventivo->MGC_Det21 == 'SI' ? 'ok' : 'no' }}">{{ $mttoPreventivo->MGC_Det21 == 'SI' ? 'OK' : 'NO' }}</span>
                        </td>
                        
                        <td class="reference">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="text-align:center; width:33%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            % VIDA
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det21Vida }}
                                        </div>
                                    </td>

                                    <td style="text-align:center; width:33%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            V. CARGA
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det21Carga }}
                                        </div>
                                    </td>
                                    
                                    <td style="text-align:center; width:33%;">
                                        <div
                                            style="
                                                font-weight:bold;
                                                font-size:10px;
                                                color:#111;
                                                margin-bottom:4px;
                                            ">
                                            V. ARRANQUE
                                        </div>
                                        <div
                                            style="
                                                border:1px solid #d8d8d8;
                                                border-radius:4px;
                                                padding:4px;
                                                font-weight:bold;
                                                color:#0d47b5;
                                                background:#fff;
                                            ">
                                            {{ $mttoPreventivo->MGC_Det21Arranque }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

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
                                    $path = public_path(ltrim($img->MGCI_url, '/'));
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

        @if(tenant('plan') === 'plus' || tenant('plan') === 'empresarial')
            <div class="section no-break">
                <div class="section-body">
                    <table class="sign-table">
                        <tr>
                            <td>
                                <div class="sign-line"></div>
                                <div class="sign-title">FIRMA DEL CLIENTE</div>
                                <div class="sign-sub">Conforme</div>
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
                                    Kael Motorcycles S.A.C.
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
                        info@kael.pe &nbsp;&nbsp;&nbsp;
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
