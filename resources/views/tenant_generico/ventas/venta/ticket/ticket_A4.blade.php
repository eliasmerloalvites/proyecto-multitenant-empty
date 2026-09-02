<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Factura Electrónica
    </title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        :root{

            --primary:#0F2B70;
            --primary-light:#163c99;
            --gray:#64748b;
            --border:#e2e8f0;

        }

        body{
            font-family:'Segoe UI', sans-serif;
            background:#eef2f7;
            margin:0;
            padding:0;
            color:#1e293b;

        }

        .invoice{
            width:210mm;
            min-height:297mm;
            margin:auto;
            background:white;
            padding:10mm;
            border-radius:10px;
            box-shadow: 0 10px 35px rgba(15,43,112,.08);
            font-size:11.5px;
        }

        /* =======================================
            TOP
        ======================================= */

        .top-section{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:16px;
        }

        /* =======================================
            COMPANY
        ======================================= */

        .company{
            width:55%;
        }
        .logo{
            width:120px;
            margin-bottom:5px;
        }

        .company-name{
            font-size:20px;
            font-weight:900;
            color:var(--primary);
            margin-bottom:4px;
            line-height:1.1;
        }

        .company-subtitle{
            color:#2563eb;
            font-size:12px;
            font-weight:700;
            margin-bottom:8px;

        }

        .company-info{
            line-height:1.5;
            font-size:11px;
        }

        .company-info strong{
            color:#111827;
        }

        /* =======================================
            DOCUMENT BOX
        ======================================= */

        .document-box{
            width:45%;
            border-radius:10px;
            overflow:hidden;
            background:white;
            box-shadow: 0 6px 16px rgba(15,43,112,.15);

        }

        .document-header{
            background:linear-gradient(
                135deg,
                var(--primary),
                var(--primary-light)
            );
            color:white;
            padding:6px 10px;
            text-align:center;
            font-size:13px;
            font-weight:800;
            letter-spacing:.5px;
        }

        .document-body{
            padding:10px;
        }

        .document-ruc{
            font-size:12px;
            font-weight:800;
            text-align:center;
            margin-bottom:8px;
            color:#111827;
        }

        .document-number{
            font-size:15px;
            font-weight:900;
            text-align:center;
            line-height:1.05;
            color:var(--primary);
            margin-bottom:10px;

        }

        .document-detail{
            display:flex;
            justify-content:space-between;
            margin-bottom:6px;
            font-size:11.5px;

        }

        .document-detail strong{
            color:#111827;
        }

        /* =======================================
            SECTIONS
        ======================================= */

        .section{
            margin-top:8px;
            border:1px solid var(--border);
            border-radius:8px;
            overflow:hidden;
            background:white;
            box-shadow: 0 4px 12px rgba(15,43,112,.04);

        }

        .section-title{
            background:linear-gradient(
                135deg,
                var(--primary),
                var(--primary-light)
            );
            color:white;
            padding:5px 10px;
            font-size:12.5px;
            font-weight:800;

        }

        .section-body{
            padding:10px;
        }

        /* =======================================
            CLIENT
        ======================================= */

        .client-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;

        }

        .client-item{
            display:flex;
            gap:6px;
            margin-bottom:3px;
            font-size:11px;

        }

        .client-item strong{
            width:90px;
            color:#111827;
        }

        /* =======================================
            TABLE
        ======================================= */

        table{
            width:100%;
            border-collapse:separate;
            border-spacing:0;
            margin-top:8px;
            overflow:hidden;
            border-radius:8px;

        }

        thead{
            background:linear-gradient(
                135deg,
                var(--primary),
                var(--primary-light)
            );
            color:white;
        }

        th{
            padding:6px 6px;
            font-size:10px;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.3px;

        }

        td{
            padding:4px 6px;
            border-bottom:1px solid #edf2f7;
            font-size:10.5px;
            vertical-align:top;
            background:white;

        }

        tbody tr:nth-child(even){

            background:#f8fbff;

        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .product-name{
            font-weight:800;
            color:#111827;
            margin-bottom:2px;
            font-size:10.5px;
        }

        .product-category{
            color:#64748b;
            font-size:9px;
        }

        /* =======================================
            BOTTOM
        ======================================= */

        .bottom-grid{
            display:flex;
            justify-content:space-between;
            gap:14px;
            margin-top:8px;

        }

        /* =======================================
            LETTERS
        ======================================= */

        .amount-letters{
            width:50%;
            border:1px solid var(--border);
            border-radius:8px;
            padding:10px;
            background:white;
            box-shadow: 0 4px 12px rgba(15,43,112,.04);
        }

        .amount-letters h4{
            color:var(--primary);
            font-size:13px;
            margin-bottom:6px;
            font-weight:800;
        }

        .amount-letters p{
            line-height:1.4;
            font-size:11px;

        }

        /* =======================================
            TOTALS
        ======================================= */

        .totals{
            width:40%;
        }

        .totals table{
            margin-top:0;
            border-radius:0px;
            overflow:hidden;
        }

        .totals td{
            border:1px solid #edf2f7;
            padding:5px 8px;
            font-size:11.5px;
        }

        .total-final td{
            background:linear-gradient(
                135deg,
                var(--primary),
                var(--primary-light)
            );
            color:white;
            font-size:12.5px;
            font-weight:600;

        }

        /* =======================================
            FOOTER
        ======================================= */

        .footer-grid{
            display:flex;
            justify-content:space-between;
            gap:14px;
            margin-top:8px;

        }

        .qr-box{
            width:48%;
            border:1px solid var(--border);
            border-radius:8px;
            padding:8px;
            background:#f8fbff;
            display:flex;
            gap:10px;
            align-items:center;
        }

        .qr-text{
            line-height:1.4;
            font-size:10px;

        }
        .aditional-box{
            width:48%;
            border:1px solid var(--border);
            border-radius:8px;
            padding:8px;
            background:white;
            box-shadow:
                0 4px 12px rgba(15,43,112,.04);
        }

        .aditional-title{
            color:var(--primary);
            font-size:12.5px;
            font-weight:800;
            margin-bottom:6px;

        }

        .aditional-item{
            display:flex;
            gap:6px;
            margin-bottom:3px;
            font-size:11px;

        }

        .aditional-item strong{
            width:95px;
        }

        /* =======================================
            THANKS
        ======================================= */

        .thanks{
            margin-top:16px;
            border-top:1px solid var(--primary);
            padding-top:10px;
            text-align:center;
        }

        .thanks-title{
            font-size:18px;
            font-weight:800;
            color:var(--primary);
            margin-bottom:4px;
        }

        .thanks-subtitle{
            font-size:11px;
            color:#64748b;

        }
        @page{
            size:A4;
            margin:0;

        }

        *{

            -webkit-print-color-adjust: exact !important;

            print-color-adjust: exact !important;

        }

        @media print{
            body{
                background:white !important;
                padding:0 !important;
                margin:0 !important;
            }

            .invoice{
                width:100% !important;
                min-height:auto !important;
                margin:0 !important;
                border-radius:0 !important;
                box-shadow:none !important;
                padding:8mm !important;
            }

            .download-btn{
                display:none !important;
            }

        }

        .download-btn{
            position:fixed;
            top:16px;
            right:16px;
            display:flex;
            align-items:center;
            gap:6px;
            background:var(--primary);
            color:white;
            border:none;
            border-radius:8px;
            padding:10px 16px;
            font-size:13px;
            font-weight:700;
            font-family:'Segoe UI', sans-serif;
            cursor:pointer;
            box-shadow:0 6px 18px rgba(15,43,112,.25);
        }

        .download-btn:hover{
            background:var(--primary-light);
        }

    </style>

</head>

<body>

<div class="invoice">

    <!-- TOP -->
    <div class="top-section">

        <!-- COMPANY -->
        <div class="company">

            @php
                $logoPath = !empty($datosalmacen->logo_pdf) ? $datosalmacen->logo_pdf : '/images/logo.png';
            @endphp

            @if($generaimagen)

                <img src="{{ public_path($logoPath) }}" class="logo">

            @else

                <img src="{{ asset_root($logoPath) }}" class="logo">

            @endif

            <div class="company-name">

                {{ $datosalmacen->razon_social ?? 'EMPRESA X20' }}

            </div>

            <div class="company-subtitle">

                {{ $datosalmacen->nombre_comercial ?? $datosalmacen->razon_social ?? '' }}

            </div>

            <div class="company-info">

                <div>

                    <strong>RUC:</strong>

                    {{ $datosalmacen->ruc ?? '12345678901' }}

                </div>

                <div>

                    <strong>Dirección:</strong>

                    {{ $datosalmacen->ALM_Direccion ?? 'Las Lomas 26' }}

                </div>

                <div>

                    <strong>Ciudad:</strong>

                    {{ trim(($datosalmacen->ALM_Departamento ?? '') . ' - ' . ($datosalmacen->ALM_Provincia ?? ''), ' -') ?: '-' }}

                </div>

                <div>

                    <strong>Celular:</strong>

                    {{ $datosalmacen->ALM_Celular ?? '946526233' }}

                </div>

                <div>

                    <strong>Email:</strong>

                    ventas@empresa.com.pe

                </div>

            </div>

        </div>

        <!-- DOCUMENT -->
        <div class="document-box">

            <div class="document-header">

                {{ $ventae->tipoDoc == 'FAC'
                    ? 'FACTURA ELECTRÓNICA'
                    : 'BOLETA ELECTRÓNICA'
                }}

            </div>

            <div class="document-body">

                <div class="document-ruc">

                    RUC:
                    {{ $datosalmacen->ruc ?? '12345678901' }}

                </div>

                <div class="document-number">

                    {{ $UbiDoc ?? 'F001' }} - {{ $NumDoc ?? '00000001' }}

                </div>

                <div class="document-detail">

                    <strong>Fecha:</strong>

                    <span>
                        {{ $ventae->fechaVenta ?? now()->format('d/m/Y') }}
                    </span>

                </div>

                <div class="document-detail">

                    <strong>Hora:</strong>

                    <span>
                        {{ $ventae->fechaVentaT ?? now()->format('H:i:s') }}
                    </span>

                </div>

                <div class="document-detail">

                    <strong>Moneda:</strong>

                    <span>SOL (S/)</span>

                </div>

                <div class="document-detail">

                    <strong>Pago:</strong>

                    <span>

                        {{ $ventae->tipopago == '1'
                            ? 'CONTADO'
                            : 'CRÉDITO'
                        }}

                    </span>

                </div>

            </div>

        </div>

    </div>

    <!-- CLIENT -->
    <div class="section">

        <div class="section-title">

            DATOS DEL CLIENTE

        </div>

        <div class="section-body">

            <div class="client-grid">

                <div>

                    <div class="client-item">

                        <strong>Cliente:</strong>

                        <span>
                            {{ $ventae->cliente ?? 'Cliente Demo' }}
                        </span>

                    </div>

                    <div class="client-item">

                        <strong>Documento:</strong>

                        <span>
                            {{ $ventae->clienteNumero ?? '73662777' }}
                        </span>

                    </div>

                    <div class="client-item">

                        <strong>Dirección:</strong>

                        <span>
                            {{ $ventae->clienteDireccion ?? '-' }}
                        </span>

                    </div>

                    <div class="client-item">

                        <strong>Correo:</strong>

                        <span>
                            cliente@gmail.com
                        </span>

                    </div>

                </div>

                <div>

                    <div class="client-item">

                        <strong>Forma Pago:</strong>

                        <span>

                            {{ $ventae->tipopago == '1'
                                ? 'CONTADO'
                                : 'CRÉDITO'
                            }}

                        </span>

                    </div>

                    <div class="client-item">

                        <strong>Vendedor:</strong>

                        <span>
                            CAJERO {{ $ventae->EMP_Codigo ?? '1' }}
                        </span>

                    </div>

                    <div class="client-item">

                        <strong>Observación:</strong>

                        <span>-</span>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- PRODUCTS -->
    <table>

        <thead>

            <tr>

                <th width="8%">
                    CANT.
                </th>

                <th width="42%">
                    DESCRIPCIÓN
                </th>

                <th width="10%">
                    UNIDAD
                </th>

                <th width="15%">
                    P. UNITARIO
                </th>

                <th width="10%">
                    DTO.
                </th>

                <th width="15%">
                    TOTAL
                </th>

            </tr>

        </thead>

        <tbody>

        @foreach($detallese as $de)

            <tr>

                <td class="text-center">

                    {{ $de->cantidad }}

                </td>

                <td>

                    <div class="product-name">

                        {{ $de->articulo }}

                    </div>

                    <div class="product-category">

                        {{ $de->categoria }}

                    </div>

                </td>

                <td class="text-center">

                    UND

                </td>

                <td class="text-right">

                    S/
                    {{ number_format($de->precio_venta,2) }}

                </td>

                <td class="text-right">

                    S/
                    {{ number_format($de->descuento,2) }}

                </td>

                <td class="text-right">

                    S/
                    {{ number_format($de->subtotal,2) }}

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

    <!-- BOTTOM -->
    <div class="bottom-grid">

        <!-- LETTERS -->
        <div class="amount-letters">

            <h4>

                SON:

            </h4>

            <p>

                {{ $LetrasTotal ?? 'SETENTA Y SIETE CON 50/100 SOLES' }}

            </p>

        </div>

        <!-- TOTALS -->
        <div class="totals">

            <table>

                <tr>

                    <td>

                        SUBTOTAL

                    </td>

                    <td class="text-right">

                        S/
                        {{ number_format($Subtotal ?? 0,2) }}

                    </td>

                </tr>

                <tr>

                    <td>

                        IGV (18%)

                    </td>

                    <td class="text-right">

                        S/
                        {{ number_format($igv ?? 0,2) }}

                    </td>

                </tr>

                <tr>

                    <td>

                        DESCUENTO

                    </td>

                    <td class="text-right">

                        S/
                        {{ number_format($ventae->total_descuento ?? 0,2) }}

                    </td>

                </tr>

                <tr class="total-final">

                    <td>

                        TOTAL A PAGAR

                    </td>

                    <td class="text-right">

                        S/
                        {{ number_format(($ventae->total_venta ?? 0) - ($ventae->total_descuento ?? 0),2) }}

                    </td>

                </tr>

            </table>

        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer-grid">

        <!-- QR -->
        <div class="qr-box">

            <div>

                {!! QrCode::size(65)->generate(
                    'RUC: '.$datosalmacen->ruc.
                    ' | DOC: '.$UbiDoc.'-'.$NumDoc.
                    ' | TOTAL: S/'.$ventae->total_venta
                ) !!}

            </div>

            <div class="qr-text">

                Representación impresa de la
                <strong>
                    FACTURA ELECTRÓNICA
                </strong>

                <br><br>

                Este documento puede ser validado en:

                <br>

                <strong>
                    www.sunat.gob.pe
                </strong>

            </div>

        </div>

        <!-- EXTRA -->
        <div class="aditional-box">

            <div class="aditional-title">

                INFORMACIÓN ADICIONAL

            </div>

            <div class="aditional-item">

                <strong>Orden Venta:</strong>

                <span>
                    {{ $ventae->codigoVenta ?? '00000009' }}
                </span>

            </div>

            <div class="aditional-item">

                <strong>Guía Remisión:</strong>

                <span>-</span>

            </div>

            <div class="aditional-item">

                <strong>Observaciones:</strong>

                <span>-</span>

            </div>

            <div class="aditional-item">

                <strong>Cajero:</strong>

                <span>
                    {{ $ventae->EMP_Codigo ?? '1' }}
                </span>

            </div>

        </div>

    </div>

    <!-- THANKS -->
    <div class="thanks">

        <div class="thanks-title">

            ¡GRACIAS POR SU COMPRA!

        </div>

        <div class="thanks-subtitle">

            Esta es una representación impresa del comprobante electrónico.

        </div>

    </div>

</div>

<button type="button" class="download-btn" onclick="window.print()">
    &#128190; Descargar PDF
</button>

</body>

</html>