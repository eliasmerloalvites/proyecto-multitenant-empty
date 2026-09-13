@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Cotización COT-' . str_pad($cotizacion->COT_Id, 5, '0', STR_PAD_LEFT))

@section('contenido')

    <style>
        /* Usa las mismas variables de tema (--bg-card, --text-main, etc.)
           que ya definen kael-dark.css / kael-light.css para el resto del
           panel (Ventas y demas modulos): asi la tarjeta se oscurece sola
           en modo oscuro y queda blanca en modo claro, sin fondo fijo. */
        .cotshow-card { background: var(--bg-card, #fff); color: var(--text-main, #1F2937); border-radius: 18px; border: 1px solid rgba(127,127,127,.15); box-shadow: 0 4px 18px rgba(0,0,0,.08); padding: 22px; max-width: 820px; margin: 0 auto; }
        .cotshow-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid rgba(127,127,127,.15); padding-bottom: 16px; margin-bottom: 18px; }
        .cotshow-titulo { font-size: 22px; font-weight: 900; color: var(--text-main, #111827); }
        .cotshow-sub { font-size: 12.5px; color: var(--text-muted, #6B7280); margin-top: 3px; }
        .cotshow-estado { padding: 5px 14px; border-radius: 20px; font-size: 11.5px; font-weight: 700; }
        .cot-info-grid { display: flex; gap: 18px; margin-bottom: 18px; flex-wrap: wrap; }
        .cot-info-box { flex: 1; min-width: 200px; background: rgba(127,127,127,.10); border-radius: 12px; padding: 12px 16px; }
        .cot-info-label { font-size: 10px; color: var(--text-muted, #9CA3AF); font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
        .cot-info-value { font-size: 13.5px; font-weight: 700; color: var(--text-main, #111827); }
        .cot-info-sub { font-size: 12px; color: var(--text-muted, #6B7280) !important; margin-top: 2px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table.items th { background: rgba(127,127,127,.10); text-align: left; font-size: 11px; color: var(--text-muted, #6B7280); text-transform: uppercase; padding: 9px 12px; }
        table.items td { padding: 10px 12px; border-bottom: 1px solid rgba(127,127,127,.15); font-size: 13px; color: var(--text-main, #1F2937); }
        .cot-num { text-align: right; }
        .cot-total-fila { display: flex; justify-content: flex-end; font-size: 19px; font-weight: 900; color: #6C3BFF; padding: 10px 12px; }
        .cot-acciones { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 18px; }
    </style>

    <div class="cotshow-card">
        <div class="cotshow-header">
            <div>
                <div class="cotshow-titulo">COT-{{ str_pad($cotizacion->COT_Id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="cotshow-sub">Creada el {{ $cotizacion->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @php
                $badges = ['PENDIENTE' => ['#FEF3C7','#92400E'], 'APROBADA' => ['#DCFCE7','#15803D'], 'RECHAZADA' => ['#FEE2E2','#B91C1C'], 'VENCIDA' => ['#E5E7EB','#4B5563']];
                [$bg, $fg] = $badges[$cotizacion->estadoMostrar()] ?? ['#E5E7EB', '#4B5563'];
            @endphp
            <span class="cotshow-estado" style="background:{{ $bg }};color:{{ $fg }};">{{ $cotizacion->estadoMostrar() }}</span>
        </div>

        <div class="cot-info-grid">
            <div class="cot-info-box">
                <div class="cot-info-label">Cliente</div>
                <div class="cot-info-value">{{ $cotizacion->cliente->CLI_Nombre ?? '—' }}</div>
                <div class="cot-info-sub text-muted">{{ $cotizacion->cliente->CLI_NumDocumento ?? '' }} · {{ $cotizacion->cliente->CLI_Celular ?? '' }}</div>
            </div>
            <div class="cot-info-box">
                <div class="cot-info-label">Válida hasta</div>
                <div class="cot-info-value">{{ $cotizacion->COT_FechaVencimiento ? $cotizacion->COT_FechaVencimiento->format('d/m/Y') : 'Sin vencimiento' }}</div>
            </div>
            @if ($cotizacion->VEN_Id)
                <div class="cot-info-box">
                    <div class="cot-info-label">Venta generada</div>
                    <div class="cot-info-value">Venta #{{ $cotizacion->VEN_Id }}</div>
                </div>
            @endif
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Cant.</th>
                    <th>Descripción</th>
                    <th class="cot-num">P. Unit.</th>
                    <th class="cot-num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cotizacion->items as $item)
                    <tr>
                        <td>{{ rtrim(rtrim(number_format($item->COI_Cantidad, 2), '0'), '.') }}</td>
                        <td>{{ $item->nombre() }}</td>
                        <td class="cot-num">S/ {{ number_format($item->COI_PrecioUnitario, 2) }}</td>
                        <td class="cot-num">S/ {{ number_format($item->subtotal(), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cot-total-fila">S/ {{ number_format($cotizacion->subtotal(), 2) }}</div>

        @if ($cotizacion->COT_Observacion)
            <div class="cot-info-box mt-2">
                <div class="cot-info-label">Observación</div>
                <div style="color:var(--text-main, #1F2937);">{{ $cotizacion->COT_Observacion }}</div>
            </div>
        @endif

        <div class="cot-acciones">
            <a href="{{ tenant_url('tenant.ventas.cotizacion.index') }}" class="btn btn-light"><i class="fa fa-arrow-left"></i> Volver</a>
            <a href="{{ tenant_url('tenant.ventas.cotizacion.pdf', ['cotizacion' => $cotizacion->COT_Id]) }}" target="_blank" class="btn btn-outline-primary"><i class="fa fa-file-pdf"></i> Ver PDF</a>

            @if ($cotizacion->estaPendiente() && !$cotizacion->estaVencida())
                @can('tenant.ventas.cotizacion.edit')
                    <a href="{{ tenant_url('tenant.ventas.cotizacion.edit', ['cotizacion' => $cotizacion->COT_Id]) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Editar</a>
                @endcan
                <a href="{{ tenant_url('tenant.ventas.cotizacion.aprobar', ['cotizacion' => $cotizacion->COT_Id]) }}" class="btn btn-success"><i class="fa fa-check"></i> Aprobar y cobrar</a>
            @endif
        </div>
    </div>

@endsection
