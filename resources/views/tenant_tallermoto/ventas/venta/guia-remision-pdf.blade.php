<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Guía de Remisión {{ $guia->GRM_Serie }}-{{ str_pad($guia->GRM_Numero, 8, '0', STR_PAD_LEFT) }}</title>

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

        .container {
            width: 100%;
            border: 1px solid #e5e7eb;
            background: white;
        }

        .header {
            padding: 16px 22px 14px 22px;
            border-bottom: 4px solid #00398A;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo {
            max-width: 170px;
            max-height: 70px;
        }

        .title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .doc-box {
            border: 2px solid #00398A;
            border-radius: 4px;
            padding: 10px 14px;
            text-align: center;
        }

        .doc-box .doc-title {
            font-size: 10px;
            font-weight: bold;
            color: #00398A;
        }

        .doc-box .doc-numero {
            font-size: 16px;
            font-weight: 800;
            margin-top: 4px;
        }

        .simulado-banner {
            background: #fef3c7;
            color: #92400e;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 6px;
            border-bottom: 1px solid #fcd34d;
        }

        .section {
            margin: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .section-title {
            background: #00398A;
            color: white;
            padding: 8px 14px;
            font-size: 11px;
            font-weight: bold;
        }

        .section-body {
            padding: 12px;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 11px;
        }

        .info-label {
            color: #64748b;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 11px;
            color: #0f172a;
        }

        .double-table {
            width: 100%;
        }

        .double-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }

        .items-table {
            width: 100%;
        }

        .items-table th {
            background: #00398A;
            color: white;
            padding: 8px;
            border: 1px solid #2563eb;
            font-size: 10px;
        }

        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 10px;
        }

        .badge-estado {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .footer {
            margin-top: 15px;
            background: #00398A;
            color: white;
            padding: 10px 18px;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $logo = null;
        if (!empty($empresa->logo)) {
            $path = public_path(ltrim($empresa->logo, '/'));
            if (is_file($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $logo = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        $numero = $guia->GRM_Serie . '-' . str_pad($guia->GRM_Numero, 8, '0', STR_PAD_LEFT);
        $esSimulado = str_contains((string) $guia->GRM_DescripcionSunat, 'SIMULADO');
        $colores = ['ACEPTADO' => '#16a34a', 'RECHAZADO' => '#dc2626', 'ERROR' => '#dc2626', 'PENDIENTE' => '#64748b'];
        $colorEstado = $colores[$guia->GRM_Estado] ?? '#64748b';
    @endphp

    <div class="container">
        @if ($esSimulado)
            <div class="simulado-banner">
                ⚠ DOCUMENTO SIMULADO — NO ENVIADO A SUNAT, SOLO PARA PRUEBAS INTERNAS
            </div>
        @endif

        <div class="header">
            <table class="header-table">
                <tr>
                    <td width="45%">
                        @if ($logo)
                            <img src="{{ $logo }}" class="logo">
                        @endif
                        <div class="title">{{ $empresa->razon_social ?? '' }}</div>
                        <div class="subtitle">RUC {{ $empresa->ruc ?? '' }}</div>
                        <div class="subtitle">{{ $empresa->direccion ?? '' }}</div>
                    </td>
                    <td width="30%"></td>
                    <td width="25%">
                        <div class="doc-box">
                            <div class="doc-title">GUÍA DE REMISIÓN ELECTRÓNICA<br>REMITENTE</div>
                            <div class="doc-numero">{{ $numero }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">DATOS DEL TRASLADO</div>
            <div class="section-body">
                <table class="info-table">
                    <tr>
                        <td width="25%">
                            <div class="info-label">Fecha de emisión</div>
                            <div class="info-value">{{ \Illuminate\Support\Carbon::parse($guia->created_at)->format('d/m/Y') }}</div>
                        </td>
                        <td width="25%">
                            <div class="info-label">Fecha de traslado</div>
                            <div class="info-value">{{ \Illuminate\Support\Carbon::parse($guia->GRM_FechaTraslado)->format('d/m/Y') }}</div>
                        </td>
                        <td width="25%">
                            <div class="info-label">Motivo</div>
                            <div class="info-value">{{ $guia->GRM_DesMotivo }} ({{ $guia->GRM_MotivoTraslado }})</div>
                        </td>
                        <td width="25%">
                            <div class="info-label">Peso total</div>
                            <div class="info-value">{{ number_format($guia->GRM_PesoTotal, 3) }} {{ $guia->GRM_UndPeso }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="info-label">Destinatario</div>
                            <div class="info-value">{{ $guia->CLI_Nombre }}</div>
                        </td>
                        <td>
                            <div class="info-label">Documento</div>
                            <div class="info-value">{{ $guia->CLI_TipoDocumento }} {{ $guia->CLI_NumDocumento }}</div>
                        </td>
                        <td colspan="2">
                            <div class="info-label">Estado SUNAT</div>
                            <div class="info-value">
                                <span class="badge-estado" style="background:{{ $colorEstado }}">{{ $guia->GRM_Estado }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="double-table">
            <tr>
                <td>
                    <div class="section">
                        <div class="section-title">PUNTO DE PARTIDA</div>
                        <div class="section-body">
                            <div class="info-value">{{ $guia->GRM_DireccionPartida }}</div>
                            <div class="info-label" style="margin-top:6px;">Ubigeo: {{ $guia->GRM_UbigeoPartida }}</div>
                            <div class="info-label">Sede: {{ $guia->ALM_NombreAlmacen }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="section">
                        <div class="section-title">PUNTO DE LLEGADA</div>
                        <div class="section-body">
                            <div class="info-value">{{ $guia->GRM_DireccionLlegada }}</div>
                            <div class="info-label" style="margin-top:6px;">Ubigeo: {{ $guia->GRM_UbigeoLlegada }}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">TRANSPORTE — {{ $guia->GRM_ModoTransporte === '01' ? 'PÚBLICO' : 'PRIVADO' }}</div>
            <div class="section-body">
                @if ($guia->GRM_ModoTransporte === '01')
                    <table class="info-table">
                        <tr>
                            <td width="20%">
                                <div class="info-label">Transportista</div>
                                <div class="info-value">{{ $guia->GRM_TransportistaRazonSocial }}</div>
                            </td>
                            <td width="20%">
                                <div class="info-label">RUC</div>
                                <div class="info-value">{{ $guia->GRM_TransportistaNumero }}</div>
                            </td>
                        </tr>
                    </table>
                @else
                    <table class="info-table">
                        <tr>
                            <td width="20%">
                                <div class="info-label">Placa</div>
                                <div class="info-value">{{ $guia->GRM_VehiculoPlaca }}</div>
                            </td>
                            <td width="20%">
                                <div class="info-label">Conductor</div>
                                <div class="info-value">{{ $guia->GRM_ConductorNombres }} {{ $guia->GRM_ConductorApellidos }}</div>
                            </td>
                            <td width="20%">
                                <div class="info-label">Documento</div>
                                <div class="info-value">{{ $guia->GRM_ConductorTipoDoc }} {{ $guia->GRM_ConductorNumero }}</div>
                            </td>
                            <td width="20%">
                                <div class="info-label">Licencia</div>
                                <div class="info-value">{{ $guia->GRM_ConductorLicencia ?: '-' }}</div>
                            </td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>

        <div class="section">
            <div class="section-title">PRODUCTOS TRASLADADOS</div>
            <div class="section-body" style="padding:0;">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="15%">CÓDIGO</th>
                            <th>DESCRIPCIÓN</th>
                            <th width="15%">CANTIDAD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $it)
                            <tr>
                                <td>{{ $it->PRO_Id }}</td>
                                <td>{{ $it->PRO_Nombre }}</td>
                                <td class="text-center">{{ rtrim(rtrim(number_format($it->DEV_Cantidad, 2), '0'), '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            Representación gráfica de la Guía de Remisión Electrónica {{ $numero }} —
            Consulta la validez de este documento en sunat.gob.pe
        </div>
    </div>
</body>

</html>
