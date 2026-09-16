{{--
    Secciones "Inventario Visual del Vehiculo" e "Inspeccion de la Unidad"
    para el PDF de Orden de Servicio. Reusado por los 5 tipos de
    mantenimiento (mismo partial, nada hardcodeado: las categorias/items
    vienen de $estadoRecepcion, calculado con
    GestionProcesoService::estadoRecepcion($tabla, $id)).

    Hecho con tablas/divs (nada de flexbox/grid) porque el PDF se genera
    con dompdf, que no soporta esas propiedades de forma confiable — mismo
    criterio que ya usa pdf.blade.php para el resto del documento.

    Espera en el scope: $estadoRecepcion = ['categorias'=>, 'respuestas'=>,
    'observaciones'=>] (o null si el mantenimiento nunca tuvo una ficha de
    recepcion guardada, en cuyo caso no se imprime nada de esto).

    Nota: 'categorias' siempre trae el catalogo completo activo (existe
    siempre, no depende de si se guardo algo), asi que NO alcanza con
    revisar si las categorias estan vacias para decidir si mostrar estas
    secciones — hay que revisar si hay al menos una respuesta u
    observacion realmente GUARDADA para este mantenimiento, si no, se
    ocultan por completo en vez de imprimir el checklist entero en blanco.
--}}
@php
    $categoriasInventario = $estadoRecepcion ? $estadoRecepcion['categorias']->where('RCT_Grupo', 'INVENTARIO') : collect();
    $categoriasInspeccion = $estadoRecepcion ? $estadoRecepcion['categorias']->where('RCT_Grupo', 'INSPECCION') : collect();
    $recRespuestas = $estadoRecepcion['respuestas'] ?? collect();
    $recObservaciones = $estadoRecepcion['observaciones'] ?? collect();
    $tieneRegistro = $recRespuestas->isNotEmpty() || $recObservaciones->isNotEmpty();

    $recEtiquetaValor = function ($item) use ($recRespuestas) {
        $valor = $recRespuestas[$item->RIT_Id] ?? null;

        if ($valor === null || $valor === '') {
            return ['texto' => 'Sin registrar', 'clase' => 'rec-tag-blank'];
        }

        return match (true) {
            in_array($valor, ['BUENO', 'OK', 'SI'], true) => ['texto' => $valor, 'clase' => 'rec-tag-ok'],
            in_array($valor, ['REGULAR'], true) => ['texto' => $valor, 'clase' => 'rec-tag-warn'],
            in_array($valor, ['MALO', 'REEMPLAZAR', 'NO'], true) => ['texto' => $valor, 'clase' => 'rec-tag-bad'],
            default => ['texto' => $valor, 'clase' => 'rec-tag-info'],
        };
    };
@endphp

@if ($estadoRecepcion && $tieneRegistro && ($categoriasInventario->isNotEmpty() || $categoriasInspeccion->isNotEmpty()))

    @if ($categoriasInventario->isNotEmpty())
        <div class="section no-break">
            <div class="section-title">5. INVENTARIO VISUAL DEL VEHÍCULO</div>
            <div class="section-body">
                @foreach ($categoriasInventario as $categoria)
                    @if ($categoria->items->isNotEmpty())
                        <div class="rec-cat-nombre">{{ $categoria->RCT_Nombre }}</div>
                        <table class="rec-inventario-table">
                            @foreach ($categoria->items->chunk(2) as $fila)
                                <tr>
                                    @foreach ($fila as $item)
                                        @php $rv = $recEtiquetaValor($item); @endphp
                                        <td class="rec-inv-label">{{ $item->RIT_Etiqueta }}</td>
                                        <td class="rec-inv-value"><span class="rec-tag {{ $rv['clase'] }}">{{ $rv['texto'] }}</span></td>
                                    @endforeach
                                    @if ($fila->count() === 1)
                                        <td class="rec-inv-label"></td>
                                        <td class="rec-inv-value"></td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    @endif
                @endforeach

                @if ($recObservaciones->get('general'))
                    <div class="rec-obs-general">
                        <strong>Observaciones / lo que manifiesta el cliente:</strong>
                        {{ $recObservaciones->get('general')->ROB_Texto }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($categoriasInspeccion->isNotEmpty())
        <div class="section no-break">
            <div class="section-title">6. INSPECCIÓN DE LA UNIDAD</div>
            <div class="section-body">
                <table class="rec-inspeccion-grid">
                    @foreach ($categoriasInspeccion->chunk(3) as $fila)
                        <tr>
                            @foreach ($fila as $categoria)
                                <td class="rec-inspeccion-cell">
                                    <div class="rec-inspeccion-box">
                                        <div class="rec-inspeccion-titulo">{{ $categoria->RCT_Nombre }}</div>
                                        @foreach ($categoria->items as $item)
                                            @php $rv = $recEtiquetaValor($item); @endphp
                                            <div class="rec-inspeccion-item">
                                                <span class="rec-inspeccion-item-label">{{ $item->RIT_Etiqueta }}</span>
                                                <span class="rec-tag {{ $rv['clase'] }}">{{ $rv['texto'] }}</span>
                                            </div>
                                        @endforeach
                                        @if ($recObservaciones->get($categoria->RCT_Id))
                                            <div class="rec-inspeccion-obs">
                                                {{ $recObservaciones->get($categoria->RCT_Id)->ROB_Texto }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            @for ($i = $fila->count(); $i < 3; $i++)
                                <td class="rec-inspeccion-cell"></td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endif
@endif
