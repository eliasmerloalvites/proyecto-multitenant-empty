{{--
    Detalle de una venta al credito para el comprobante.
    Variables: $datosdecuenta (cuenta por cobrar con abonos, ver
    VentaController::datosCuentaCredito) y $formato ('a4' | 'ticket').
    Solo tablas y estilos en linea: tiene que verse igual en el PDF (dompdf).
--}}
@if (is_object($datosdecuenta) && isset($datosdecuenta->CPC_MontoTotal))
    @php
        $cr = $datosdecuenta;
        $esTicket = ($formato ?? 'a4') === 'ticket';
        $hoyCr = \Carbon\Carbon::now('America/Lima')->startOfDay();
        $venceCr = \Carbon\Carbon::parse($cr->CPC_FechaVencimiento)->startOfDay();
        $pagadaCr = $cr->CPC_Estado === 'PAGADA' || (float) $cr->CPC_MontoFaltante <= 0.009;
        $vencidaCr = ! $pagadaCr && $venceCr->lt($hoyCr);
        $estadoCr = $pagadaCr ? 'PAGADA' : ($vencidaCr ? 'VENCIDA' : 'PENDIENTE');
        $colorCr = $pagadaCr ? '#15803d' : ($vencidaCr ? '#b91c1c' : '#b45309');
        $fs = $esTicket ? '11px' : '10.5px';
        $pagosCr = collect($cr->abonos ?? []);
    @endphp

    <style>
        .cr-credito td { font-size: inherit !important; text-align: left; vertical-align: top; }
        .cr-credito td:last-child { text-align: right; white-space: nowrap; padding-left: 6px !important; }
    </style>
    <div class="cr-credito" style="margin-top:{{ $esTicket ? '10px' : '8px' }};border:1px solid {{ $esTicket ? '#999' : '#d9e2ef' }};border-radius:6px;overflow:hidden;font-size:{{ $fs }};">

        <div style="background:var(--primary);color:#fff;padding:5px 8px;font-weight:700;letter-spacing:.3px;">
            DETALLE DEL CRÉDITO
            <span style="float:right;background:#fff;color:{{ $colorCr }};border-radius:3px;padding:0 6px;font-size:{{ $esTicket ? '10px' : '9.5px' }};">{{ $estadoCr }}</span>
        </div>

        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="padding:3px 8px;">Forma de pago</td>
                <td style="padding:3px 8px;text-align:right;font-weight:700;">CRÉDITO</td>
            </tr>
            <tr>
                <td style="padding:3px 8px;">Fecha de emisión</td>
                <td style="padding:3px 8px;text-align:right;">{{ \Carbon\Carbon::parse($cr->FECHAEMISION)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td style="padding:3px 8px;">Fecha de vencimiento</td>
                <td style="padding:3px 8px;text-align:right;font-weight:700;">
                    {{ $venceCr->format('d/m/Y') }}
                    @if ($cr->dias_credito > 0)
                        <span style="font-weight:400;color:#666;">({{ $cr->dias_credito }} {{ $cr->dias_credito == 1 ? 'día' : 'días' }})</span>
                    @endif
                </td>
            </tr>
            <tr style="border-top:1px solid #e5e7eb;">
                <td style="padding:3px 8px;">Total de la venta</td>
                <td style="padding:3px 8px;text-align:right;">S/ {{ number_format((float) $cr->CPC_MontoTotal, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:3px 8px;">Pagado a la fecha</td>
                <td style="padding:3px 8px;text-align:right;">S/ {{ number_format((float) $cr->CPC_MontoAbonado, 2) }}</td>
            </tr>
            <tr style="border-top:1px solid #e5e7eb;">
                <td style="padding:4px 8px;font-weight:700;">Saldo pendiente</td>
                <td style="padding:4px 8px;text-align:right;font-weight:700;color:{{ $colorCr }};">S/ {{ number_format((float) $cr->CPC_MontoFaltante, 2) }}</td>
            </tr>
        </table>

        @if (! $pagadaCr)
            <div style="padding:4px 8px;border-top:1px solid #e5e7eb;background:#f8fafc;">
                <strong>Cronograma:</strong> 1 cuota de S/ {{ number_format((float) $cr->CPC_MontoFaltante, 2) }}
                con vencimiento el {{ $venceCr->format('d/m/Y') }}.
            </div>
        @endif

        @if ($pagosCr->isNotEmpty() || $cr->adelanto_previo > 0)
            <div style="padding:4px 8px 2px;border-top:1px solid #e5e7eb;font-weight:700;">Pagos registrados</div>
            <table style="width:100%;border-collapse:collapse;margin-bottom:4px;">
                @if ($cr->adelanto_previo > 0)
                    <tr>
                        <td style="padding:2px 8px;">Adelanto cobrado antes de la venta</td>
                        <td style="padding:2px 8px;text-align:right;">S/ {{ number_format((float) $cr->adelanto_previo, 2) }}</td>
                    </tr>
                @endif
                @foreach ($pagosCr as $p)
                    <tr>
                        <td style="padding:2px 8px;">
                            {{ \Carbon\Carbon::parse($p->created_at)->setTimezone('America/Lima')->format('d/m/Y') }}
                            · {{ $p->MEP_Pago ?: 'Pago' }}
                            @if ($p->CPA_Observacion)
                                <span style="color:#666;">— {{ \Illuminate\Support\Str::limit($p->CPA_Observacion, 60) }}</span>
                            @endif
                        </td>
                        <td style="padding:2px 8px;text-align:right;">S/ {{ number_format((float) $p->CPA_Monto, 2) }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

        <div style="padding:3px 8px;border-top:1px solid #e5e7eb;color:#666;font-size:{{ $esTicket ? '9.5px' : '9px' }};">
            Situación al {{ $hoyCr->format('d/m/Y') }}. El saldo y los pagos se actualizan en cada impresión.
        </div>
    </div>
@endif
