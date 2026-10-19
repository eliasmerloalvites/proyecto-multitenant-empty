@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')

@section('titulo', 'Desempaquetar Kits')

@section('contenido')
    <style>
        .card, .modal-content { border-radius: 18px; }
        #tabla_componentes td { vertical-align: middle; }
        .des-resumen { background: #F0F7FF; border: 1px solid #CFE3FA; border-radius: 12px; padding: 10px 12px; font-size: 13px; }
        .des-paso { font-size: 12px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
    </style>

    @can('tenant.inventario.producto.create')
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">DESEMPAQUETAR KIT</h5>
                    <p class="text-muted" style="font-size: 13px;">
                        Abre kits para vender sus piezas por separado: el kit sale del stock y cada pieza entra
                        con su propio stock. Queda registrado y se ve en el kardex de cada producto.
                    </p>

                    <form id="form_desempaquetado" autocomplete="off">
                        <div class="form-group">
                            <div class="des-paso">1. Almacén</div>
                            <select class="form-control select2" id="ALM_Id" style="width: 100%;">
                                <option value="">Seleccionar ...</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{ $alm->ALM_Id }}">{{ $alm->ALM_NombreAlmacen }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="des-paso">2. Kit a abrir</div>
                            <select class="form-control" id="selector_kit" style="width: 100%;" disabled>
                                <option value="">Elige primero el almacén ...</option>
                            </select>
                            <small class="text-muted">Solo aparecen productos con stock en el almacén. Los que ya tienen receta salen primero.</small>
                        </div>

                        <div class="form-group">
                            <div class="des-paso">3. ¿Cuántos kits?</div>
                            <input type="number" min="1" step="1" class="form-control" id="cantidad_kits" value="1" disabled>
                            <small class="text-muted" id="stock_kit_texto"></small>
                        </div>

                        <div class="form-group">
                            <div class="des-paso">4. Qué trae cada kit</div>
                            <div id="aviso_receta" class="mb-2" style="font-size: 13px;"></div>
                            <select class="form-control" id="selector_componente" style="width: 100%;" disabled>
                                <option value="">Agregar pieza ...</option>
                            </select>
                            <div class="table-responsive mt-2">
                                <table class="table table-sm table-bordered mb-0" id="tabla_componentes">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Pieza</th>
                                            <th style="width: 110px;">Por kit</th>
                                            <th style="width: 80px;">Entran</th>
                                            <th style="width: 36px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_componentes">
                                        <tr><td colspan="4" class="text-center text-muted">Elige un kit.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="custom-control custom-checkbox mt-2" id="wrap_guardar_receta" style="display: none;">
                                <input type="checkbox" class="custom-control-input" id="guardar_receta">
                                <label class="custom-control-label" for="guardar_receta" style="font-weight: normal;">
                                    Guardar estas piezas como la receta de este kit
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="observacion">Observación (opcional)</label>
                            <textarea class="form-control" id="observacion" rows="2" maxlength="500" placeholder="Ej. kit vino sin la llave 12"></textarea>
                        </div>

                        <div class="des-resumen mb-3" id="resumen" style="display: none;"></div>

                        <div class="text-right">
                            <button type="button" id="btnDesempaquetar" class="btn btn-primary">
                                <i class="fas fa-box-open"></i> Desempaquetar
                            </button>
                            <button type="button" id="btnLimpiar" class="btn btn-outline-secondary">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">HISTORIAL DE DESEMPAQUETADOS</h5>
                <div class="table-responsive" style="background:#FFF;">
                    <table class="table" id="lista_desempaquetados" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Kit</th>
                                <th>Kits</th>
                                <th>Piezas</th>
                                <th>Almacén</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-box-open mr-2"></i><span id="ver_titulo">Desempaquetado</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="ver_cuerpo"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500 });
            const URL_PRODUCTOS = "{{ tenant_url('tenant.inventario.desempaquetado.productos') }}";
            const URL_RECETA = "{{ tenant_url('tenant.inventario.desempaquetado.receta', ['kit' => '__ID__']) }}";
            const URL_SHOW = "{{ tenant_url('tenant.inventario.desempaquetado.show', ['id' => '__ID__']) }}";
            const URL_ANULAR = "{{ tenant_url('tenant.inventario.desempaquetado.anular', ['id' => '__ID__']) }}";

            let kit = null;          // {id, nombre, stock}
            let piezas = [];         // [{id, nombre, cantidad}]
            let recetaOriginal = ''; // para saber si se cambio

            const esc = (t) => $('<div>').text(t == null ? '' : t).html();
            const num = (n) => parseFloat((+n).toFixed(2)).toString();

            $('#ALM_Id').select2();

            const table = $('#lista_desempaquetados').DataTable({
                responsive: true, autoWidth: false, processing: true, serverSide: true,
                order: [[0, 'desc']],
                ajax: "{{ tenant_url('tenant.inventario.desempaquetado.index') }}",
                columns: [
                    { data: 'numero', name: 'd.DES_Id' },
                    { data: 'kit', name: 'kit' },
                    { data: 'DES_Cantidad', name: 'd.DES_Cantidad' },
                    { data: 'componentes', name: 'componentes', searchable: false, orderable: false },
                    { data: 'almacen', name: 'almacen' },
                    { data: 'usuario', name: 'usuario' },
                    { data: 'created_at', name: 'd.created_at' },
                    { data: 'estado', name: 'd.DES_Estado' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            function selectorProductos($el, soloKits) {
                if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
                $el.empty().prop('disabled', false).select2({
                    width: '100%',
                    placeholder: soloKits ? 'Buscar el kit ...' : 'Agregar pieza (buscar por nombre o código) ...',
                    ajax: {
                        url: URL_PRODUCTOS, dataType: 'json', delay: 250,
                        data: (p) => ({ ALM_Id: $('#ALM_Id').val(), search: p.term, kits: soloKits ? 1 : 0 }),
                        processResults: (lista) => ({
                            results: lista.map((p) => ({
                                id: p.PRO_Id, nombre: p.PRO_Nombre, stock: +p.stock,
                                text: p.PRO_Nombre + (p.PRO_CodigoInterno ? ' (' + p.PRO_CodigoInterno + ')' : '')
                                    + ' — stock: ' + num(p.stock) + (soloKits && +p.tiene_receta ? ' · con receta' : '')
                            }))
                        })
                    }
                });
            }

            $('#ALM_Id').on('change', function() {
                limpiarKit();
                if (!$(this).val()) {
                    $('#selector_kit').prop('disabled', true);
                    return;
                }
                selectorProductos($('#selector_kit'), true);
            });

            $('#selector_kit').on('select2:select', function(e) {
                const d = e.params.data;
                kit = { id: +d.id, nombre: d.nombre, stock: d.stock };
                $('#cantidad_kits').prop('disabled', false).attr('max', Math.floor(kit.stock)).val(1);
                $('#stock_kit_texto').text('Disponible en este almacén: ' + num(kit.stock));
                selectorProductos($('#selector_componente'), false);

                $.get(URL_RECETA.replace('__ID__', kit.id), function(r) {
                    piezas = r.componentes.map((c) => ({ id: c.PRO_Id, nombre: c.PRO_Nombre, cantidad: c.cantidad }));
                    recetaOriginal = firma();
                    $('#aviso_receta').html(piezas.length
                        ? '<span class="text-success"><i class="fas fa-check-circle"></i> Se cargó la receta guardada. Puedes corregirla si este kit vino distinto.</span>'
                        : '<span class="text-warning"><i class="fas fa-info-circle"></i> Este kit aún no tiene receta: agrega sus piezas. Puedes guardarlas para la próxima vez.</span>');
                    render();
                });
            });

            $('#selector_componente').on('select2:select', function(e) {
                const d = e.params.data;
                if (+d.id === kit.id) {
                    Toast.fire({ type: 'error', title: 'El kit no puede ser pieza de sí mismo.' });
                } else if (!piezas.some((p) => p.id === +d.id)) {
                    piezas.push({ id: +d.id, nombre: d.nombre, cantidad: 1 });
                    render();
                }
                $(this).val(null).trigger('change');
            });

            $('#cantidad_kits').on('input', render);

            $('body').on('input', '.cant_pieza', function() {
                piezas[$(this).closest('tr').data('i')].cantidad = $(this).val();
                render(false);
            });

            $('body').on('click', '.quitar_pieza', function() {
                piezas.splice($(this).closest('tr').data('i'), 1);
                render();
            });

            const firma = () => JSON.stringify(piezas.map((p) => [p.id, +p.cantidad]).sort((a, b) => a[0] - b[0]));

            function render(redibujar = true) {
                const kits = parseInt($('#cantidad_kits').val(), 10) || 0;
                if (redibujar) {
                    const $tb = $('#tbody_componentes').empty();
                    if (!kit) {
                        $tb.append('<tr><td colspan="4" class="text-center text-muted">Elige un kit.</td></tr>');
                    } else if (!piezas.length) {
                        $tb.append('<tr><td colspan="4" class="text-center text-muted">Agrega las piezas que trae el kit.</td></tr>');
                    }
                    piezas.forEach((p, i) => {
                        $tb.append('<tr data-i="' + i + '"><td>' + esc(p.nombre) + '</td>'
                            + '<td><input type="number" min="0.01" step="0.01" class="form-control form-control-sm cant_pieza" value="' + esc(p.cantidad) + '" aria-label="Cantidad por kit de ' + esc(p.nombre) + '"></td>'
                            + '<td class="text-center entran"></td>'
                            + '<td class="text-center"><a href="javascript:void(0)" class="text-danger quitar_pieza" title="Quitar"><i class="fa fa-trash"></i></a></td></tr>');
                    });
                }
                $('#tbody_componentes tr[data-i]').each(function() {
                    const p = piezas[$(this).data('i')];
                    $(this).find('.entran').text(num((+p.cantidad || 0) * kits));
                });

                const cambiada = kit && piezas.length && firma() !== recetaOriginal;
                $('#wrap_guardar_receta').toggle(!!cambiada);
                if (!cambiada) $('#guardar_receta').prop('checked', false);
                else if (recetaOriginal === '[]') $('#guardar_receta').prop('checked', true);

                if (kit && piezas.length && kits > 0) {
                    const total = piezas.reduce((s, p) => s + (+p.cantidad || 0) * kits, 0);
                    $('#resumen').show().html('Sale <strong>' + kits + '</strong> × ' + esc(kit.nombre)
                        + ' y entran <strong>' + num(total) + '</strong> piezas de <strong>' + piezas.length + '</strong> producto(s).');
                } else {
                    $('#resumen').hide();
                }
            }

            function limpiarKit() {
                kit = null; piezas = []; recetaOriginal = '';
                ['#selector_kit', '#selector_componente'].forEach((s) => {
                    const $s = $(s);
                    if ($s.hasClass('select2-hidden-accessible')) $s.select2('destroy');
                    $s.empty().prop('disabled', true);
                });
                $('#cantidad_kits').val(1).prop('disabled', true);
                $('#stock_kit_texto, #aviso_receta').empty();
                $('#guardar_receta').prop('checked', false);
                render();
            }

            $('#btnLimpiar').on('click', function() {
                $('#observacion').val('');
                $('#ALM_Id').val('').trigger('change');
            });

            $('#btnDesempaquetar').on('click', function() {
                const kits = parseInt($('#cantidad_kits').val(), 10);
                let error = null;
                if (!$('#ALM_Id').val()) error = 'Selecciona el almacén.';
                else if (!kit) error = 'Elige el kit a desempaquetar.';
                else if (!kits || kits < 1) error = 'La cantidad de kits debe ser 1 o más.';
                else if (kits > kit.stock) error = 'Solo hay ' + num(kit.stock) + ' kit(s) en este almacén.';
                else if (!piezas.length) error = 'Agrega al menos una pieza.';
                else if (piezas.some((p) => !(+p.cantidad > 0))) error = 'Cada pieza necesita una cantidad mayor a 0.';
                if (error) return Toast.fire({ type: 'error', title: error });

                Swal.fire({
                    title: '¿Desempaquetar ' + kits + ' kit(s)?',
                    html: $('#resumen').html() + '<br><small class="text-muted">Podrás anularlo mientras no se usen las piezas.</small>',
                    type: 'question', showCancelButton: true,
                    confirmButtonText: 'Sí, desempaquetar', cancelButtonText: 'Cancelar'
                }).then((r) => {
                    if (!r.value) return;
                    const $btn = $('#btnDesempaquetar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
                    $.ajax({
                        url: "{{ tenant_url('tenant.inventario.desempaquetado.store') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ALM_Id: $('#ALM_Id').val(), PRO_Id: kit.id, cantidad: kits,
                            observacion: $('#observacion').val(),
                            guardar_receta: $('#guardar_receta').is(':checked') ? 1 : 0,
                            componentes: piezas.map((p) => ({ PRO_Id: p.id, cantidad: p.cantidad }))
                        },
                        success: function(d) {
                            Toast.fire({ type: 'success', title: d.success });
                            $('#btnLimpiar').click();
                            table.draw();
                        },
                        error: function(x) {
                            const j = x.responseJSON || {};
                            Toast.fire({ type: 'error', title: j.error || j.message || 'No se pudo desempaquetar.' });
                        },
                        complete: () => $btn.prop('disabled', false).html('<i class="fas fa-box-open"></i> Desempaquetar')
                    });
                });
            });

            $('body').on('click', '.verDesempaquetado', function() {
                $('#ver_cuerpo').html('<p class="text-muted">Cargando…</p>');
                $('#modalVer').modal('show');
                $.get(URL_SHOW.replace('__ID__', $(this).data('id')), function(r) {
                    const d = r.desempaquetado;
                    $('#ver_titulo').text('Desempaquetado ' + d.numero);
                    let h = '<div class="row mb-3">'
                        + '<div class="col-md-4"><small class="text-muted d-block">Kit</small><strong>' + esc(d.kit) + '</strong></div>'
                        + '<div class="col-md-2"><small class="text-muted d-block">Kits abiertos</small><strong>' + num(d.DES_Cantidad) + '</strong></div>'
                        + '<div class="col-md-3"><small class="text-muted d-block">Almacén</small><strong>' + esc(d.almacen) + '</strong></div>'
                        + '<div class="col-md-3"><small class="text-muted d-block">Usuario · fecha</small><strong>' + esc(d.usuario) + '</strong><br><small>' + esc(d.created_at) + '</small></div>'
                        + '</div>';
                    if (d.DES_Observacion) h += '<p><small class="text-muted d-block">Observación</small>' + esc(d.DES_Observacion) + '</p>';
                    if (d.DES_Estado === 'ANULADO') {
                        h += '<div class="alert alert-dark py-2"><strong>Anulado</strong> por ' + esc(d.usuario_anula || '—') + ' el ' + esc(d.DES_AnuladoEn)
                            + '<br><small>' + esc(d.DES_MotivoAnulacion) + '</small></div>';
                    }
                    h += '<div class="table-responsive"><table class="table table-sm table-bordered"><thead class="bg-light"><tr>'
                        + '<th>Pieza</th><th>Por kit</th><th>Entraron</th><th>Costo unit.</th><th>Quedan del lote</th></tr></thead><tbody>';
                    r.detalle.forEach((x) => {
                        h += '<tr><td>' + esc(x.PRO_Nombre) + '</td><td>' + num(x.DSD_CantidadPorKit) + '</td><td>' + num(x.DSD_Cantidad)
                            + '</td><td>S/ ' + (+x.DSD_PrecioCompra).toFixed(2) + '</td><td>' + (x.disponible_lote == null ? '—' : num(x.disponible_lote)) + '</td></tr>';
                    });
                    h += '</tbody></table></div>';
                    $('#ver_cuerpo').html(h);
                });
            });

            $('body').on('click', '.anularDesempaquetado', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: '¿Anular el desempaquetado?',
                    text: 'Las piezas salen del stock y el kit vuelve. Solo se puede si ninguna pieza se usó.',
                    input: 'text', inputPlaceholder: 'Motivo (obligatorio)',
                    type: 'warning', showCancelButton: true,
                    confirmButtonText: 'Anular', cancelButtonText: 'Cancelar', confirmButtonColor: '#d33',
                    inputValidator: (v) => (!v || v.trim().length < 3) ? 'Escribe el motivo.' : undefined
                }).then((r) => {
                    if (!r.value) return;
                    $.post(URL_ANULAR.replace('__ID__', id), { _token: '{{ csrf_token() }}', motivo: r.value.trim() })
                        .done((d) => { Toast.fire({ type: 'success', title: d.success }); table.draw(false); })
                        .fail((x) => Swal.fire('No se pudo anular', (x.responseJSON || {}).error || 'Inténtalo de nuevo.', 'error'));
                });
            });
        });
    </script>
@endsection
