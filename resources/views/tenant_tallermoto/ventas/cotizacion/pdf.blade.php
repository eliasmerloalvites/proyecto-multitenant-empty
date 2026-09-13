<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Cotización COT-{{ str_pad($cotizacion->COT_Id, 5, '0', STR_PAD_LEFT) }}</title>
@php
    // Paleta de marca configurada por el tenant en Configuracion > Empresa
    // (Color Marca Base/Hover = color_main/color_light).
    $paleta = paleta_documento($empresa);
@endphp
<style>
    * { box-sizing: border-box; }
    :root {
        --primary: {{ $paleta['primary'] }};
        --primary-dark: {{ $paleta['primary_dark'] }};
        --primary-soft: {{ $paleta['primary_soft'] }};
    }
    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        color: #1F2937;
        margin: 0;
        padding: 36px 42px;
        font-size: 13px;
    }
    .encabezado { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid var(--primary); padding-bottom: 16px; margin-bottom: 20px; }
    .empresa-nombre { font-size: 19px; font-weight: 800; color: #111827; }
    .empresa-meta { font-size: 11.5px; color: #6B7280; margin-top: 3px; line-height: 1.5; }
    .cot-badge { text-align: right; }
    .cot-titulo { font-size: 22px; font-weight: 900; color: var(--primary-dark); letter-spacing: .02em; }
    .cot-numero { font-size: 13px; color: #6B7280; margin-top: 2px; }
    .cot-estado {
        display: inline-block; margin-top: 8px; padding: 4px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 700; background: #FEF3C7; color: #92400E;
    }

    .info-grid { display: flex; justify-content: space-between; gap: 24px; margin-bottom: 22px; }
    .info-box { flex: 1; background: var(--primary-soft); border-radius: 12px; padding: 12px 16px; }
    .info-label { font-size: 10px; color: #9CA3AF; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
    .info-value { font-size: 13px; font-weight: 700; color: #111827; }
    .info-sub { font-size: 11.5px; color: #6B7280; margin-top: 2px; }

    table.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    table.items th {
        background: var(--primary); color: #fff; text-align: left; font-size: 10.5px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .03em; padding: 9px 12px;
    }
    table.items th.num, table.items td.num { text-align: right; }
    table.items td { padding: 9px 12px; border-bottom: 1px solid #F1F2F5; font-size: 12.5px; }
    table.items tr:nth-child(even) td { background: #FBFBFD; }

    .totales { display: flex; justify-content: flex-end; margin-bottom: 22px; }
    .totales-box { width: 260px; }
    .totales-fila { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
    .totales-fila.total { border-top: 2px solid #111827; margin-top: 4px; padding-top: 10px; font-size: 17px; font-weight: 900; color: var(--primary-dark); }

    .observacion { background: #F9FAFB; border-radius: 12px; padding: 12px 16px; font-size: 12px; color: #374151; margin-bottom: 18px; }
    .observacion-label { font-size: 10px; color: #9CA3AF; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }

    .pie { border-top: 1px solid #E5E7EB; padding-top: 12px; font-size: 10.5px; color: #9CA3AF; text-align: center; }
    .pie strong { color: #6B7280; }
</style>
</head>
<body>

    <div class="encabezado">
        <div>
            <div class="empresa-nombre">{{ $empresa->nombre_comercial ?? $empresa->razon_social ?? 'Mi Empresa' }}</div>
            <div class="empresa-meta">
                @if ($empresa)
                    RUC {{ $empresa->ruc }}<br>
                    {{ $empresa->direccion ?? '' }}<br>
                    {{ $empresa->telefono ?? '' }} {{ $empresa->correo ? '· ' . $empresa->correo : '' }}
                @endif
            </div>
        </div>
        <div class="cot-badge">
            <div class="cot-titulo">COTIZACIÓN</div>
            <div class="cot-numero">N° COT-{{ str_pad($cotizacion->COT_Id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="cot-estado">{{ $cotizacion->estadoMostrar() }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $cotizacion->cliente->CLI_Nombre ?? '—' }}</div>
            <div class="info-sub">{{ $cotizacion->cliente->CLI_TipoDocumento ?? '' }} {{ $cotizacion->cliente->CLI_NumDocumento ?? '' }}</div>
            <div class="info-sub">{{ $cotizacion->cliente->CLI_Celular ?? '' }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Fecha de emisión</div>
            <div class="info-value">{{ $cotizacion->created_at->format('d/m/Y') }}</div>
            <div class="info-label" style="margin-top:8px;">Válida hasta</div>
            <div class="info-value">{{ $cotizacion->COT_FechaVencimiento ? $cotizacion->COT_FechaVencimiento->format('d/m/Y') : 'Sin vencimiento' }}</div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:50px;">Cant.</th>
                <th>Descripción</th>
                <th class="num" style="width:100px;">P. Unit.</th>
                <th class="num" style="width:110px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->items as $item)
                <tr>
                    <td>{{ rtrim(rtrim(number_format($item->COI_Cantidad, 2), '0'), '.') }}</td>
                    <td>{{ $item->nombre() }}</td>
                    <td class="num">S/ {{ number_format($item->COI_PrecioUnitario, 2) }}</td>
                    <td class="num">S/ {{ number_format($item->subtotal(), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <div class="totales-box">
            <div class="totales-fila total">
                <span>TOTAL</span>
                <span>S/ {{ number_format($cotizacion->subtotal(), 2) }}</span>
            </div>
        </div>
    </div>

    @if ($cotizacion->COT_Observacion)
        <div class="observacion">
            <div class="observacion-label">Observación</div>
            {{ $cotizacion->COT_Observacion }}
        </div>
    @endif

    <div class="pie">
        Este documento es una <strong>cotización referencial</strong> y no constituye un comprobante de pago.
        Los precios pueden variar según disponibilidad al momento de la compra.
    </div>

</body>
</html>
