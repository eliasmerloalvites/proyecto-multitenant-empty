<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Recepción e Inspección de Motocicleta</title>

    @php
        // Paleta de marca configurada por el tenant en Configuracion >
        // Empresa (Color Marca Base/Hover = color_main/color_light).
        $paleta = paleta_documento($empresa);

        $recRespuestas = $estadoRecepcion['respuestas'] ?? collect();
        $recObservaciones = $estadoRecepcion['observaciones'] ?? collect();
        $categoriasInventario = $estadoRecepcion ? $estadoRecepcion['categorias']->where('RCT_Grupo', 'INVENTARIO') : collect();
        $categoriasInspeccion = $estadoRecepcion ? $estadoRecepcion['categorias']->where('RCT_Grupo', 'INSPECCION') : collect();

        // Mapa RIT_Codigo => [valor, tipo, etiqueta], para poder ubicar
        // cada respuesta real por su codigo estable sin volver a consultar
        // la base de datos item por item.
        $porCodigo = [];
        foreach ($categoriasInventario as $categoria) {
            foreach ($categoria->items as $item) {
                $porCodigo[$item->RIT_Codigo] = [
                    'valor' => $recRespuestas[$item->RIT_Id] ?? null,
                    'tipo' => $item->RIT_TipoCampo,
                    'etiqueta' => $item->RIT_Etiqueta,
                ];
            }
        }

        $colorEstado = ['BUENO' => 'ok', 'REGULAR' => 'warn', 'MALO' => 'bad', 'SI' => 'ok', 'NO' => 'bad'];

        // Posicion exacta de cada boton B/R/M (o SI/NO en los booleanos) de
        // cada componente ya dibujado en inspeccion_moto.png. Se detectaron
        // automaticamente sobre la imagen fuente (1536x1024) ubicando los 3
        // recuadros blancos de cada fila (deteccion de componentes conexos,
        // no una aproximacion visual), asi que cada letra tiene su propio
        // centro medido en vez de derivarse de una fraccion de la fila.
        $diagramaSlots = [
            ['codigo' => 'elementos_generales_faro_delantero', 'boolean' => true, 'b' => [7.71, 21.83], 'r' => [10.87, 21.83], 'm' => [14.10, 21.83], 'w' => 2.60, 'h' => 2.64],
            ['codigo' => 'parte_delantera_luces_delanteras', 'b' => [5.60, 30.27], 'r' => [8.95, 30.27], 'm' => [12.30, 30.27], 'w' => 2.80, 'h' => 2.54],
            ['codigo' => 'parte_delantera_direccionales_delanteras', 'b' => [5.14, 39.01], 'r' => [8.33, 39.01], 'm' => [11.49, 39.01], 'w' => 2.60, 'h' => 2.64],
            ['codigo' => 'parte_delantera_guardabarro_delantero', 'b' => [4.33, 47.31], 'r' => [7.49, 47.31], 'm' => [10.68, 47.31], 'w' => 2.60, 'h' => 2.44],
            ['codigo' => 'parte_delantera_llanta_delantera', 'b' => [4.52, 56.01], 'r' => [7.29, 56.01], 'm' => [9.99, 56.01], 'w' => 2.21, 'h' => 2.44],
            ['codigo' => 'parte_delantera_rin_delantero', 'b' => [4.30, 65.23], 'r' => [7.06, 65.23], 'm' => [9.83, 65.23], 'w' => 2.28, 'h' => 2.54],
            ['codigo' => 'parte_delantera_freno_delantero', 'b' => [4.36, 75.20], 'r' => [7.26, 75.20], 'm' => [10.19, 75.20], 'w' => 2.41, 'h' => 2.54],
            ['codigo' => 'parte_delantera_suspension_delantera', 'b' => [5.47, 84.47], 'r' => [8.66, 84.47], 'm' => [11.85, 84.47], 'w' => 2.60, 'h' => 2.54],

            ['codigo' => 'parte_delantera_tablero', 'b' => [17.51, 12.26], 'r' => [20.48, 12.26], 'm' => [23.37, 12.26], 'w' => 2.41, 'h' => 2.64],
            ['codigo' => 'parte_delantera_retrovisor', 'b' => [34.60, 10.69], 'r' => [37.53, 10.69], 'm' => [40.46, 10.69], 'w' => 2.41, 'h' => 2.64],
            ['codigo' => 'parte_delantera_tapa_tanque_combustible_estado', 'b' => [49.06, 16.55], 'r' => [52.47, 16.55], 'm' => [55.76, 16.55], 'w' => 2.73, 'h' => 2.64],
            ['codigo' => 'parte_trasera_direccionales_traseras', 'b' => [56.09, 25.05], 'r' => [58.63, 25.05], 'm' => [61.07, 25.05], 'w' => 2.02, 'h' => 2.44],
            ['codigo' => 'parte_delantera_luces_delanteras', 'b' => [66.67, 14.40], 'r' => [69.86, 14.40], 'm' => [72.98, 14.40], 'w' => 2.60, 'h' => 2.64],
            ['codigo' => 'parte_trasera_parrilla', 'b' => [85.68, 12.21], 'r' => [88.80, 12.21], 'm' => [91.93, 12.21], 'w' => 2.47, 'h' => 2.73],

            ['codigo' => 'parte_trasera_luces_de_freno', 'b' => [88.90, 21.73], 'r' => [92.12, 21.73], 'm' => [95.38, 21.73], 'w' => 2.73, 'h' => 2.64],
            ['codigo' => 'elementos_generales_luz_trasera', 'boolean' => true, 'b' => [88.25, 30.37], 'r' => [91.47, 30.37], 'm' => [94.66, 30.37], 'w' => 2.73, 'h' => 2.54],
            ['codigo' => 'parte_trasera_guardabarro_trasero', 'b' => [89.52, 38.77], 'r' => [92.61, 38.77], 'm' => [95.70, 38.82], 'w' => 2.54, 'h' => 2.54],
            ['codigo' => 'parte_trasera_stop', 'b' => [90.14, 46.63], 'r' => [93.10, 46.63], 'm' => [96.06, 46.63], 'w' => 2.34, 'h' => 2.44],
            ['codigo' => 'parte_trasera_llanta_trasera', 'b' => [89.19, 56.15], 'r' => [92.15, 56.15], 'm' => [95.02, 56.15], 'w' => 2.41, 'h' => 2.54],
            ['codigo' => 'parte_trasera_rin_trasero', 'b' => [89.00, 65.14], 'r' => [91.93, 65.14], 'm' => [94.79, 65.14], 'w' => 2.34, 'h' => 2.54],
            ['codigo' => 'parte_trasera_kit_de_arrastre', 'b' => [89.03, 74.22], 'r' => [92.19, 74.22], 'm' => [95.35, 74.22], 'w' => 2.60, 'h' => 2.54],
            ['codigo' => 'parte_trasera_suspension_trasera', 'b' => [87.21, 84.42], 'r' => [90.53, 84.42], 'm' => [93.88, 84.42], 'w' => 2.80, 'h' => 2.44],
            ['codigo' => 'parte_trasera_freno_trasero', 'b' => [82.52, 91.80], 'r' => [85.45, 91.80], 'm' => [88.41, 91.80], 'w' => 2.54, 'h' => 2.34],

            ['codigo' => 'motor_y_transmision_inventario_aceite_motor', 'b' => [36.10, 80.66], 'r' => [39.23, 80.66], 'm' => [42.25, 80.66], 'w' => 2.54, 'h' => 2.54],
            ['codigo' => 'motor_y_transmision_inventario_bateria', 'b' => [50.62, 80.76], 'r' => [53.55, 80.76], 'm' => [56.45, 80.76], 'w' => 2.41, 'h' => 2.54],
            ['codigo' => 'motor_y_transmision_inventario_caja_de_cambios', 'b' => [33.27, 90.14], 'r' => [36.46, 90.14], 'm' => [39.68, 90.14], 'w' => 2.73, 'h' => 2.54],
            ['codigo' => 'motor_y_transmision_inventario_cadena_caton', 'b' => [48.86, 90.33], 'r' => [52.05, 90.33], 'm' => [55.31, 90.33], 'w' => 2.67, 'h' => 2.54],
            ['codigo' => 'motor_y_transmision_inventario_gato', 'b' => [63.67, 90.09], 'r' => [66.80, 90.09], 'm' => [70.02, 90.09], 'w' => 2.73, 'h' => 2.64],
        ];

        // Items reales que no tienen una posicion clara en el diagrama
        // (no inventados: si el catalogo cambia y agrega/renombra algo,
        // simplemente aparece aqui en vez de perderse).
        $codigosEnDiagrama = collect($diagramaSlots)->pluck('codigo')->unique();
        $itemsFueraDeDiagrama = [];
        foreach ($categoriasInventario as $categoria) {
            foreach ($categoria->items as $item) {
                if (!$codigosEnDiagrama->contains($item->RIT_Codigo) && $item->RIT_TipoCampo !== 'SELECT') {
                    $itemsFueraDeDiagrama[] = $porCodigo[$item->RIT_Codigo];
                }
            }
        }

        $nivelCombustible = $porCodigo['datos_de_ingreso_nivel_de_combustible']['valor'] ?? null;
        $nivelAceite = $porCodigo['datos_de_ingreso_nivel_de_aceite']['valor'] ?? null;
    @endphp

    <style>
        :root {
            --primary: {{ $paleta['primary'] }};
            --primary-light: {{ $paleta['primary_light'] }};
            --primary-dark: {{ $paleta['primary_dark'] }};
        }

        @page {
            margin: 0px;
            padding: 6px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #1e293b;
            background: #ffffff;
        }

        table {
            border-collapse: collapse;
        }

        .container {
            width: 100%;
            border: 1px solid #d1d5db;
            background: white;
        }

        /* HEADER */
        .header {
            padding: 10px 16px 8px 16px;
            border-bottom: 2px solid var(--primary);
        }

        .header-table {
            width: 100%;
        }

        .logo {
            width: 110px;
        }

        .company-name {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
        }

        .title {
            font-size: 15px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: .3px;
            margin-top: 2px;
        }

        .header-info {
            font-size: 8px;
            line-height: 1.5;
            text-align: right;
            color: #334155;
        }

        .header-info strong {
            color: var(--primary-dark);
        }

        /* CLIENTE / VEHICULO */
        .info-bar {
            margin: 6px 12px 0 12px;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            background: #f8fafc;
        }

        .info-bar-table {
            width: 100%;
        }

        .info-bar-table td {
            padding: 5px 10px;
            vertical-align: middle;
            border-left: 1px solid #e2e8f0;
        }

        .info-bar-table td:first-child {
            border-left: none;
        }

        .info-label {
            color: #64748b;
            font-size: 6.5px;
            font-weight: bold;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .info-value {
            color: #0f172a;
            font-size: 9px;
            font-weight: bold;
            margin-top: 1px;
        }

        .info-value.blank {
            color: #cbd5e1;
            font-weight: normal;
            font-style: italic;
        }

        /* SECTIONS */
        .section {
            margin: 7px 12px 0 12px;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            page-break-inside: avoid;
        }

        .section-title {
            background: var(--primary);
            color: white;
            padding: 4px 9px;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: .2px;
        }

        .section-body {
            padding: 7px;
        }

        /* DIAGRAMA */
        .diagrama-wrap {
            width: 700px;
            margin: 0 auto;
        }

        .diagrama-img {
            width: 700px;
            height: 467px;
            display: block;
        }

        /* Los 28 resaltados B/R/M se dibujan como <rect> dentro de un unico
           <svg> (incrustado como imagen, ver comentario mas abajo) apilado
           SOBRE el PNG de la moto con margin-top negativo, en vez de
           position:absolute — dompdf no coloca de forma confiable ni
           siquiera a un UNICO hijo position:absolute en este contexto (el
           bloque entero terminaba desplazado respecto a su contenedor;
           confirmado inspeccionando las coordenadas reales del PDF
           generado, es un bug conocido de dompdf). Apilar en flujo normal
           con margen negativo es el mismo truco ya usado y probado en el
           resto del documento, sin depender de position:absolute.         */
        .diagrama-overlay {
            width: 700px;
            height: 467px;
            display: block;
            margin-top: -467px;
        }

        /* NIVELES */
        .niveles-table {
            width: 100%;
        }

        .niveles-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 6px;
        }

        .nivel-titulo {
            font-size: 7.5px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .nivel-combustible-row {
            width: 100%;
        }

        .nivel-celda {
            display: inline-block;
            width: 22%;
            margin-right: 1%;
            text-align: center;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 4px 0;
            font-size: 7.5px;
            font-weight: bold;
            color: #475569;
        }

        .nivel-celda.activo {
            background: var(--primary);
            border-color: var(--primary-dark);
            color: white;
        }

        .nivel-aceite-bar {
            position: relative;
            height: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            background: #f1f5f9;
            margin-top: 6px;
        }

        .nivel-aceite-marca {
            position: absolute;
            top: -3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary);
            border: 2px solid white;
        }

        .nivel-aceite-labels {
            width: 100%;
            font-size: 7px;
            color: #64748b;
            font-weight: bold;
            margin-top: 2px;
        }

        /* OBSERVACIONES */
        .obs-box {
            min-height: 26px;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 5px 7px;
            font-size: 8px;
            color: #334155;
            background: #fff;
        }

        .obs-box.vacio {
            color: #cbd5e1;
            font-style: italic;
        }

        /* OTROS ITEMS (fuera del diagrama) */
        .otros-table {
            width: 100%;
        }

        .otros-table td {
            padding: 1.5px 4px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 7.5px;
        }

        .otros-label {
            width: 60%;
            color: #334155;
        }

        .rec-tag {
            display: inline-block;
            padding: 0px 6px;
            border-radius: 20px;
            font-size: 7px;
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

        .rec-tag-blank {
            background: #f1f5f9;
            color: #94a3b8;
        }

        /* INSPECCION */
        .inspeccion-grid {
            width: 100%;
        }

        .inspeccion-cell {
            width: 16.66%;
            vertical-align: top;
            padding: 2px;
        }

        .inspeccion-box {
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 5px;
            min-height: 20px;
        }

        .inspeccion-titulo {
            font-size: 7.5px;
            font-weight: bold;
            color: var(--primary-dark);
            margin-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            text-transform: uppercase;
        }

        .inspeccion-item {
            font-size: 6.8px;
            padding: 1px 0;
        }

        .inspeccion-item-label {
            color: #334155;
        }

        .inspeccion-obs {
            margin-top: 3px;
            font-size: 6.3px;
            color: #64748b;
            font-style: italic;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            background: var(--primary);
            color: white;
            padding: 5px 14px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-left {
            font-size: 7px;
        }

        .footer-right {
            text-align: right;
            font-size: 7px;
        }

        .page_num:before {
            content: counter(page);
        }

        .page_count:before {
            content: counter(pages);
        }

        .container {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

    @php
        $path = public_path(ltrim($empresa->logo ?? 'images/logo.png', '/'));
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pathMoto = public_path('images/recepcion/inspeccion_moto.png');
        $motoDiagrama = file_exists($pathMoto)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathMoto))
            : null;
    @endphp

    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td width="18%">
                        <img src="{{ $logo }}" class="logo">
                    </td>
                    <td width="52%">
                        <div class="company-name">{{ $empresa->razon_social }}</div>
                        <div class="title">RECEPCIÓN E INSPECCIÓN DE MOTOCICLETA</div>
                    </td>
                    <td width="30%">
                        <div class="header-info">
                            <strong>N° ORDEN:</strong> {{ $numeroOrden }}<br>
                            <strong>FECHA:</strong> {{ $fecha }} &nbsp; <strong>HORA:</strong> {{ $hora }}<br>
                            <strong>ASESOR/TÉCNICO:</strong> {{ $personal }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="info-bar">
            <table class="info-bar-table">
                <tr>
                    <td width="24%">
                        <div class="info-label">Cliente</div>
                        <div class="info-value {{ $propietario ? '' : 'blank' }}">{{ $propietario ?: 'Sin registrar' }}</div>
                    </td>
                    <td width="14%">
                        <div class="info-label">Placa</div>
                        <div class="info-value {{ $placa ? '' : 'blank' }}">{{ $placa ?: '—' }}</div>
                    </td>
                    <td width="24%">
                        <div class="info-label">Unidad</div>
                        <div class="info-value {{ $unidad ? '' : 'blank' }}">{{ $unidad ?: 'Sin registrar' }}</div>
                    </td>
                    <td width="14%">
                        <div class="info-label">Kilometraje</div>
                        <div class="info-value {{ $km ? '' : 'blank' }}">{{ $km ?: '—' }}</div>
                    </td>
                    <td width="24%">
                        <div class="info-label">Tipo de mantenimiento</div>
                        <div class="info-value">{{ $etiquetaTipo }}</div>
                    </td>
                </tr>
            </table>
        </div>

        @if ($motoDiagrama)
            <div class="section">
                <div class="section-title">INVENTARIO VISUAL DEL VEHÍCULO</div>
                <div class="section-body">
                    <div class="diagrama-wrap">
                        <img src="{{ $motoDiagrama }}" class="diagrama-img">
                        @php
                            // Contenedor a tamaño fijo (700x467px).
                            $DIAG_W = 700;
                            $DIAG_H = 467;

                            $svgColores = [
                                'ok' => ['fill' => 'rgb(22,163,74)', 'stroke' => '#15803d'],
                                'warn' => ['fill' => 'rgb(217,119,6)', 'stroke' => '#b45309'],
                                'bad' => ['fill' => 'rgb(220,38,38)', 'stroke' => '#b91c1c'],
                            ];

                            $rects = [];
                            foreach ($diagramaSlots as $slot) {
                                $dato = $porCodigo[$slot['codigo']] ?? null;
                                $valor = $dato['valor'] ?? null;
                                $claseSufijo = $colorEstado[$valor] ?? null;
                                if (!$claseSufijo) {
                                    continue;
                                }

                                // Boton a marcar (B, R o M — o SI/NO en los booleanos),
                                // cada uno con su centro medido directamente sobre la
                                // imagen fuente (ver comentario del array de arriba).
                                $letra = 'b';
                                if (!empty($slot['boolean'])) {
                                    $letra = $valor === 'SI' ? 'b' : 'm';
                                } elseif ($valor === 'REGULAR') {
                                    $letra = 'r';
                                } elseif ($valor === 'MALO') {
                                    $letra = 'm';
                                }

                                [$cx, $cy] = $slot[$letra];
                                $celdaAncho = $slot['w'] / 100 * $DIAG_W;
                                $celdaAlto = $slot['h'] / 100 * $DIAG_H;
                                $celdaLeft = $cx / 100 * $DIAG_W - $celdaAncho / 2;
                                $celdaTop = $cy / 100 * $DIAG_H - $celdaAlto / 2;

                                $rects[] = [
                                    'x' => $celdaLeft, 'y' => $celdaTop,
                                    'w' => $celdaAncho, 'h' => $celdaAlto,
                                    'color' => $svgColores[$claseSufijo],
                                ];
                            }

                            // El overlay se arma como un SVG standalone y se
                            // incrusta como <img src="data:image/svg+xml;...">
                            // en vez de un <svg> inline en el HTML: dompdf no
                            // renderiza <svg> embebido directamente en el body
                            // (se ignora en silencio), y tampoco coloca bien
                            // a 28 <div class="slot"> hermanos con
                            // position:absolute (cada uno terminaba
                            // referenciando un "containing block" distinto —
                            // confirmado inspeccionando las coordenadas reales
                            // del PDF generado; es un bug conocido de dompdf).
                            // Como imagen, en cambio, dompdf aplica una unica
                            // matriz de transformacion a todo el SVG, igual
                            // que ya hace con el PNG de la moto.
                            $svgRectsMarkup = '';
                            foreach ($rects as $rect) {
                                $svgRectsMarkup .= sprintf(
                                    '<rect x="%s" y="%s" width="%s" height="%s" rx="3" fill="%s" fill-opacity="0.55" stroke="%s" stroke-width="2" />',
                                    $rect['x'], $rect['y'], $rect['w'], $rect['h'], $rect['color']['fill'], $rect['color']['stroke']
                                );
                            }
                            $svgOverlay = '<svg width="' . $DIAG_W . '" height="' . $DIAG_H . '" viewBox="0 0 ' . $DIAG_W . ' ' . $DIAG_H . '" xmlns="http://www.w3.org/2000/svg">' . $svgRectsMarkup . '</svg>';
                            $overlayDataUri = 'data:image/svg+xml;base64,' . base64_encode($svgOverlay);
                        @endphp
                        <img src="{{ $overlayDataUri }}" class="diagrama-overlay">
                    </div>
                </div>
            </div>
        @endif

        <div class="section">
            <div class="section-title">NIVELES Y OBSERVACIONES DEL CLIENTE</div>
            <div class="section-body">
                <table class="niveles-table">
                    <tr>
                        <td>
                            <div class="nivel-titulo">Nivel de Combustible</div>
                            @foreach (['1/4', '1/2', '3/4', 'Full'] as $opcion)
                                <span class="nivel-celda {{ $nivelCombustible === $opcion ? 'activo' : '' }}">{{ $opcion }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div class="nivel-titulo">Nivel de Aceite</div>
                            <div class="nivel-aceite-bar">
                                @if ($nivelAceite === 'Mínimo')
                                    <div class="nivel-aceite-marca" style="left:0%;"></div>
                                @elseif ($nivelAceite === 'Máximo')
                                    <div class="nivel-aceite-marca" style="left:calc(100% - 16px);"></div>
                                @endif
                            </div>
                            <table class="nivel-aceite-labels">
                                <tr>
                                    <td style="text-align:left;">MÍN</td>
                                    <td style="text-align:right;">MÁX</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                @if (count($itemsFueraDeDiagrama))
                    <div class="nivel-titulo" style="margin-top:8px;">Otros elementos verificados</div>
                    <table class="otros-table">
                        @foreach (array_chunk($itemsFueraDeDiagrama, 2) as $fila)
                            <tr>
                                @foreach ($fila as $dato)
                                    @php
                                        $valor = $dato['valor'];
                                        $clase = $valor === null || $valor === ''
                                            ? 'rec-tag-blank'
                                            : ('rec-tag-' . ($colorEstado[$valor] ?? 'blank'));
                                        $texto = $valor === null || $valor === '' ? '—' : $valor;
                                    @endphp
                                    <td class="otros-label">{{ $dato['etiqueta'] }}</td>
                                    <td><span class="rec-tag {{ $clase }}">{{ $texto }}</span></td>
                                @endforeach
                                @if (count($fila) === 1)
                                    <td class="otros-label"></td>
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endif

                <div class="nivel-titulo" style="margin-top:8px;">Observaciones / Detalles — lo que manifiesta el cliente</div>
                @if ($recObservaciones->get('general'))
                    <div class="obs-box">{{ $recObservaciones->get('general')->ROB_Texto }}</div>
                @else
                    <div class="obs-box vacio">Sin observaciones registradas.</div>
                @endif
            </div>
        </div>

        @if ($categoriasInspeccion->isNotEmpty())
            <div class="section">
                <div class="section-title">INSPECCIÓN DE LA UNIDAD</div>
                <div class="section-body">
                    <table class="inspeccion-grid">
                        <tr>
                            @foreach ($categoriasInspeccion as $categoria)
                                <td class="inspeccion-cell">
                                    <div class="inspeccion-box">
                                        <div class="inspeccion-titulo">{{ $categoria->RCT_Nombre }}</div>
                                        @foreach ($categoria->items as $item)
                                            @php
                                                $valor = $recRespuestas[$item->RIT_Id] ?? null;
                                                $clase = $valor === null || $valor === ''
                                                    ? 'rec-tag-blank'
                                                    : ($valor === 'OK' ? 'rec-tag-ok' : 'rec-tag-bad');
                                                $texto = $valor === null || $valor === '' ? '—' : ($valor === 'OK' ? 'OK' : 'X');
                                            @endphp
                                            <div class="inspeccion-item">
                                                <span class="inspeccion-item-label">{{ $item->RIT_Etiqueta }}</span>
                                                <span class="rec-tag {{ $clase }}">{{ $texto }}</span>
                                            </div>
                                        @endforeach
                                        @if ($recObservaciones->get($categoria->RCT_Id))
                                            <div class="inspeccion-obs">{{ $recObservaciones->get($categoria->RCT_Id)->ROB_Texto }}</div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        @if (!$estadoRecepcion || ($categoriasInventario->isEmpty() && $categoriasInspeccion->isEmpty()))
            <div class="section">
                <div class="section-body" style="text-align:center; color:#94a3b8; padding:16px;">
                    Este mantenimiento todavía no tiene una ficha de Estado de Recepción guardada.
                </div>
            </div>
        @endif
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    {{ $empresa->ruc }} &nbsp;·&nbsp; Generado: {{ $fecha }} {{ $hora }} &nbsp;·&nbsp; Orden {{ $numeroOrden }}
                </td>
                <td class="footer-right">
                    Página <span class="page_num"></span> de <span class="page_count"></span>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
