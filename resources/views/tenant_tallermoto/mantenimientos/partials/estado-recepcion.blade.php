{{--
    "Estado de Recepcion de la Motocicleta": checklist de condiciones al
    recibir la moto, sin importar si vino de un check-in, de aprobar una
    reserva, o de crearse directo. Reusado por los 5 tipos de
    mantenimiento — recibe $tabla (nombre de tabla, ej.
    "mantenimiento_general_carburada") y $id (el {prefijo}_Id de ese
    registro).

    Todo lo que se pinta adentro (categorias/items/tipos de campo) viene
    de la config dinamica (recepcion_categoria/recepcion_item) via AJAX,
    nada esta hardcodeado aqui — este partial es el mismo para los 5
    tipos, no se duplica.
--}}
<div class="row mt-3">
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card card-outline card-primary" id="estadoRecepcionCard" data-tabla="{{ $tabla }}" data-id="{{ $id }}">
            <div class="card-header" style="cursor:pointer;" data-toggle="collapse" data-target="#estadoRecepcionBody">
                <h3 class="card-title-custom">
                    <i class="fas fa-clipboard-check mr-1"></i>
                    Estado de Recepción de la Motocicleta
                </h3>
                <div class="card-tools">
                    <span class="badge badge-secondary" id="estadoRecepcionResumen">Sin cargar</span>
                    <i class="fas fa-chevron-down ml-2"></i>
                </div>
            </div>
            <div id="estadoRecepcionBody" class="collapse">
                <div class="card-body">
                    <div id="estadoRecepcionCargando" class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Cargando...
                    </div>
                    <div id="estadoRecepcionContenido" style="display:none;">

                        <div class="form-group">
                            <label class="font-weight-bold">Observaciones / Detalles — lo que manifiesta el cliente</label>
                            <textarea class="form-control" id="recObsGeneral" rows="2"
                                placeholder="Ej. El cliente indica ruido al frenar, luz de check encendida hace 2 dias..."></textarea>
                        </div>

                        <ul class="nav nav-tabs" id="recTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="rec-tab-inventario" data-toggle="tab" href="#rec-pane-inventario" role="tab">
                                    <i class="fas fa-motorcycle mr-1"></i> Inventario Visual
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="rec-tab-inspeccion" data-toggle="tab" href="#rec-pane-inspeccion" role="tab">
                                    <i class="fas fa-search mr-1"></i> Inspección de la Unidad
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content pt-3">
                            <div class="tab-pane fade show active" id="rec-pane-inventario" role="tabpanel">
                                <div id="recAcordeonInventario" class="rec-acordeon"></div>
                            </div>
                            <div class="tab-pane fade" id="rec-pane-inspeccion" role="tabpanel">
                                <div id="recAcordeonInspeccion" class="rec-acordeon"></div>
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-primary" id="btnGuardarRecepcion">
                                <i class="fas fa-save mr-1"></i> Guardar Estado de Recepción
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Pastillas de seleccion unica (STATUS/INSPECTION/SELECT) — reemplaza
       la tabla gigante de checkboxes clasica por controles modernos. */
    .rec-pill-group {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .rec-pill {
        border: 1px solid rgba(148, 163, 184, .4);
        background: transparent;
        color: var(--text-main, #343a40);
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: .15s ease;
        user-select: none;
    }

    .rec-pill:hover {
        border-color: var(--primary, #adb5bd);
    }

    .rec-pill.activa {
        color: #fff;
        border-color: transparent;
    }

    .rec-pill[data-valor="BUENO"].activa,
    .rec-pill[data-valor="OK"].activa {
        background: #28a745;
    }

    .rec-pill[data-valor="REGULAR"].activa {
        background: #ffc107;
        color: #212529;
    }

    .rec-pill[data-valor="MALO"].activa,
    .rec-pill[data-valor="REEMPLAZAR"].activa {
        background: #dc3545;
    }

    .rec-pill.rec-pill-select.activa {
        background: #6c3bff;
    }

    .rec-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 4px;
        border-bottom: 1px solid rgba(148, 163, 184, .18);
    }

    .rec-item-row:last-child {
        border-bottom: none;
    }

    .rec-item-label {
        font-size: 13.5px;
        color: var(--text-main, #343a40);
    }

    .rec-item-boolean .custom-switch {
        margin-bottom: 0;
    }

    .rec-categoria-card {
        border: 1px solid rgba(148, 163, 184, .3);
        border-radius: 10px;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .rec-categoria-header {
        background: rgba(148, 163, 184, .1);
        color: var(--text-main, #343a40);
        padding: 10px 14px;
        font-weight: 700;
        font-size: 13.5px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rec-categoria-body {
        padding: 10px 14px;
    }

    .rec-item-text-full {
        width: 100%;
    }

    #estadoRecepcionCard .card-title-custom {
        color: var(--text-main, #343a40);
    }
</style>

<script>
    // Este partial se incluye dentro del contenido principal de la ficha,
    // que en el layout se renderiza antes de que cargue jQuery (jQuery entra
    // recien casi al final del layout) — mismo patron de bug ya visto y
    // arreglado en anulaciones.blade.php. Se difiere con un poll en vez de
    // un listener de carga del documento, para no depender de si jQuery
    // carga de forma sincrona o asincrona.
    (function iniciarCuandoHayaJQuery() {
        if (!window.jQuery) {
            setTimeout(iniciarCuandoHayaJQuery, 30);
            return;
        }

        const $card = $('#estadoRecepcionCard');
        const tabla = $card.data('tabla');
        const mtoId = $card.data('id');
        let configCargada = false;

        function pillGroup(nombreCampo, opciones, valorActual) {
            let html = '<div class="rec-pill-group" data-campo="' + nombreCampo + '">';
            opciones.forEach(function (op) {
                const activa = (valorActual === op.valor) ? ' activa' : '';
                const claseSelect = op.esSelect ? ' rec-pill-select' : '';
                html += '<span class="rec-pill' + activa + claseSelect + '" data-valor="' + op.valor + '">' + op.etiqueta + '</span>';
            });
            html += '</div>';
            return html;
        }

        function pintarItem(item, valorActual) {
            const nombreCampo = 'item_' + item.RIT_Id;
            let control = '';

            if (item.RIT_TipoCampo === 'BOOLEAN') {
                const marcado = valorActual === 'SI' ? 'checked' : '';
                control = '<div class="custom-control custom-switch rec-item-boolean">' +
                    '<input type="checkbox" class="custom-control-input" id="' + nombreCampo + '" data-campo="' + nombreCampo + '" ' + marcado + '>' +
                    '<label class="custom-control-label" for="' + nombreCampo + '"></label>' +
                    '</div>';
            } else if (item.RIT_TipoCampo === 'STATUS') {
                control = pillGroup(nombreCampo, [
                    { valor: 'BUENO', etiqueta: 'Bueno' },
                    { valor: 'REGULAR', etiqueta: 'Regular' },
                    { valor: 'MALO', etiqueta: 'Malo' },
                ], valorActual);
            } else if (item.RIT_TipoCampo === 'INSPECTION') {
                control = pillGroup(nombreCampo, [
                    { valor: 'OK', etiqueta: 'OK' },
                    { valor: 'REEMPLAZAR', etiqueta: 'Reemplazar' },
                ], valorActual);
            } else if (item.RIT_TipoCampo === 'SELECT') {
                const opciones = (item.RIT_Opciones || []).map(function (op) {
                    return { valor: op, etiqueta: op, esSelect: true };
                });
                control = pillGroup(nombreCampo, opciones, valorActual);
            } else if (item.RIT_TipoCampo === 'TEXT') {
                control = '<textarea class="form-control rec-item-text-full" rows="2" data-campo="' + nombreCampo + '">' + (valorActual || '') + '</textarea>';
            }

            const filaClase = item.RIT_TipoCampo === 'TEXT' ? 'rec-item-row d-block' : 'rec-item-row';

            return '<div class="' + filaClase + '">' +
                '<span class="rec-item-label">' + item.RIT_Etiqueta + '</span>' +
                (item.RIT_TipoCampo === 'TEXT' ? '<div class="mt-1">' + control + '</div>' : control) +
                '</div>';
        }

        function pintarCategoria(categoria, respuestas, observaciones) {
            const idBody = 'rec-cat-' + categoria.RCT_Id;
            let itemsHtml = '';
            categoria.items.forEach(function (item) {
                itemsHtml += pintarItem(item, respuestas[item.RIT_Id] || null);
            });

            const obsPrevia = observaciones[categoria.RCT_Id] ? observaciones[categoria.RCT_Id].ROB_Texto : '';
            const mostrarObs = categoria.RCT_Grupo === 'INSPECCION';

            let html = '<div class="rec-categoria-card">' +
                '<div class="rec-categoria-header" data-toggle="collapse" data-target="#' + idBody + '">' +
                '<span>' + categoria.RCT_Nombre + '</span>' +
                '<i class="fas fa-chevron-down"></i>' +
                '</div>' +
                '<div class="collapse show" id="' + idBody + '"><div class="rec-categoria-body">' +
                itemsHtml;

            if (mostrarObs) {
                html += '<div class="form-group mt-2 mb-0">' +
                    '<label class="text-muted small mb-1">Observaciones de ' + categoria.RCT_Nombre + '</label>' +
                    '<textarea class="form-control form-control-sm" rows="2" data-campo="obs_cat_' + categoria.RCT_Id + '">' + obsPrevia + '</textarea>' +
                    '</div>';
            }

            html += '</div></div></div>';
            return html;
        }

        function mostrarErrorCarga(mensaje) {
            $('#estadoRecepcionCargando').html(
                '<i class="fas fa-exclamation-triangle text-warning mr-1"></i> ' +
                (mensaje || 'No se pudo cargar el estado de recepción.') +
                ' <a href="#" id="recReintentar">Reintentar</a>'
            );
            configCargada = false;
        }

        function cargarConfig() {
            if (configCargada) return;

            $.get('{{ tenant_url("tenant.mantenimientos.recepcion.mostrar", ["tabla" => ":tabla", "id" => ":id"]) }}'
                .replace(':tabla', tabla).replace(':id', mtoId))
                .done(function (r) {
                    if (!r.success) {
                        mostrarErrorCarga(r.message);
                        return;
                    }

                    const respuestas = r.respuestas || {};
                    const observaciones = r.observaciones || {};

                    if (observaciones.general) {
                        $('#recObsGeneral').val(observaciones.general.ROB_Texto);
                    }

                    let htmlInventario = '';
                    let htmlInspeccion = '';
                    let contestadas = 0;

                    r.categorias.forEach(function (categoria) {
                        const html = pintarCategoria(categoria, respuestas, observaciones);
                        if (categoria.RCT_Grupo === 'INVENTARIO') {
                            htmlInventario += html;
                        } else {
                            htmlInspeccion += html;
                        }
                        categoria.items.forEach(function (item) {
                            if (respuestas[item.RIT_Id]) contestadas++;
                        });
                    });

                    $('#recAcordeonInventario').html(htmlInventario);
                    $('#recAcordeonInspeccion').html(htmlInspeccion);
                    $('#estadoRecepcionCargando').hide();
                    $('#estadoRecepcionContenido').show();

                    const totalItems = r.categorias.reduce(function (acc, c) { return acc + c.items.length; }, 0);
                    $('#estadoRecepcionResumen')
                        .removeClass('badge-secondary badge-success badge-warning')
                        .addClass(contestadas === 0 ? 'badge-secondary' : (contestadas === totalItems ? 'badge-success' : 'badge-warning'))
                        .text(contestadas + ' / ' + totalItems + ' items');

                    configCargada = true;
                })
                .fail(function (xhr) {
                    const r = xhr.responseJSON || {};
                    mostrarErrorCarga(r.message);
                });
        }

        $('#estadoRecepcionCard .card-header').on('click', cargarConfig);
        $(document).on('click', '#recReintentar', function (e) {
            e.preventDefault();
            $('#estadoRecepcionCargando').html('<i class="fas fa-spinner fa-spin mr-1"></i> Cargando...');
            cargarConfig();
        });

        $(document).on('click', '.rec-pill', function () {
            const $grupo = $(this).closest('.rec-pill-group');
            $grupo.find('.rec-pill').removeClass('activa');
            $(this).addClass('activa');
        });

        $('#btnGuardarRecepcion').on('click', function () {
            const respuestas = {};
            const observaciones = { general: $('#recObsGeneral').val() };

            $('#estadoRecepcionContenido [data-campo]').each(function () {
                const campo = $(this).data('campo');

                if (String(campo).indexOf('obs_cat_') === 0) {
                    observaciones[campo.replace('obs_cat_', '')] = $(this).val();
                    return;
                }

                const itemId = String(campo).replace('item_', '');

                if ($(this).is('input[type=checkbox]')) {
                    respuestas[itemId] = $(this).is(':checked') ? 'SI' : 'NO';
                } else if ($(this).is('textarea')) {
                    respuestas[itemId] = $(this).val();
                }
            });

            $('#estadoRecepcionContenido .rec-pill-group').each(function () {
                const campo = $(this).data('campo');
                const itemId = String(campo).replace('item_', '');
                const $activa = $(this).find('.rec-pill.activa');

                if ($activa.length) {
                    respuestas[itemId] = $activa.data('valor');
                }
            });

            const $btn = $('#btnGuardarRecepcion').prop('disabled', true);
            const textoOriginal = $btn.html();
            $btn.html('Guardando...');

            $.ajax({
                url: '{{ tenant_url("tenant.mantenimientos.recepcion.guardar", ["tabla" => ":tabla", "id" => ":id"]) }}'
                    .replace(':tabla', tabla).replace(':id', mtoId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    respuestas: respuestas,
                    observaciones: observaciones,
                }
            }).done(function () {
                Swal.fire({ icon: 'success', title: 'Guardado', text: 'Estado de recepción actualizado.', timer: 1800, showConfirmButton: false });
                configCargada = false;
                cargarConfig();
            }).fail(function (xhr) {
                const r = xhr.responseJSON || {};
                Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: r.message || 'Error de conexión.' });
            }).always(function () {
                $btn.prop('disabled', false).html(textoOriginal);
            });
        });
    })();
</script>
