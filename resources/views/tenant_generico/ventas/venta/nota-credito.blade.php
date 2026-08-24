@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Nota de crédito')
@section('contenido')

    <style>
        .nc-resumen dt { color: var(--text-muted, #94A3B8); font-weight: 500; font-size: .85rem; }
        .nc-resumen dd { margin-bottom: .6rem; }
        #tablaItems td { vertical-align: middle; }
        #tablaItems input[type="number"] { width: 100px; }
        .nc-total { font-size: 1.15rem; font-weight: 700; }
    </style>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1">Emitir nota de crédito</h5>
                    <p class="text-muted mb-4">
                        Afecta a <strong>{{ $documento->DOV_Nombre }}</strong>
                        ({{ $documento->DOV_Tipo === 'FAC' ? 'Factura' : 'Boleta' }})
                    </p>

                    @if (!empty($problemasSede))
                        <div class="alert alert-warning">
                            @foreach ($problemasSede as $problema)
                                <div>{{ $problema }}</div>
                            @endforeach
                            <a href="{{ tenant_url('tenant.configuracion.sede.index') }}" class="alert-link">
                                Configurar en Sedes
                            </a>
                        </div>
                    @endif

                    <form id="formNotaCredito">
                        @csrf

                        <div class="form-group">
                            <label>Motivo</label>
                            <select class="form-control" id="cod_motivo" name="cod_motivo" required>
                                <option value="">Seleccione el motivo...</option>
                                @foreach ($motivos as $codigo => $etiqueta)
                                    <option value="{{ $codigo }}" data-etiqueta="{{ $etiqueta }}">
                                        {{ $codigo }} - {{ $etiqueta }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Descripción del motivo</label>
                            <textarea class="form-control" id="des_motivo" name="des_motivo" rows="2"
                                placeholder="Se completa sola al elegir el motivo; puedes editarla"></textarea>
                        </div>

                        <label class="d-block mb-2">Productos a acreditar</label>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered" id="tablaItems">
                                <thead>
                                    <tr>
                                        <th style="width:36px"></th>
                                        <th>Producto</th>
                                        <th class="text-center">Vendido</th>
                                        <th class="text-center">A acreditar</th>
                                        <th class="text-end">P. Unitario</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $it)
                                        <tr data-precio="{{ $it->DEV_PrecioUnitario }}">
                                            <td class="text-center">
                                                <input type="checkbox" class="chkItem"
                                                    name="incluir[{{ $it->DEV_Item }}]" value="1" checked>
                                            </td>
                                            <td>{{ $it->PRO_Nombre }}</td>
                                            <td class="text-center">{{ rtrim(rtrim(number_format($it->DEV_Cantidad, 2), '0'), '.') }}</td>
                                            <td class="text-center">
                                                <input type="number" class="form-control form-control-sm inpCantidad"
                                                    name="cantidad[{{ $it->DEV_Item }}]"
                                                    value="{{ $it->DEV_Cantidad }}"
                                                    max="{{ $it->DEV_Cantidad }}" min="0" step="0.01">
                                            </td>
                                            <td class="text-end">S/ {{ number_format($it->DEV_PrecioUnitario, 2) }}</td>
                                            <td class="text-end fila-subtotal">
                                                S/ {{ number_format($it->DEV_Cantidad * $it->DEV_PrecioUnitario, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end nc-total">Total estimado</td>
                                        <td class="text-end nc-total" id="totalEstimado">S/ 0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="form-group form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="devolver_stock" name="devolver_stock" value="1">
                            <label class="form-check-label" for="devolver_stock">
                                Devolver estos productos al stock del almacén
                            </label>
                            <small class="form-text text-muted">
                                Actívalo solo si el cliente devolvió la mercadería. Para descuentos, correcciones
                                de datos o anulaciones sin devolución física, déjalo desactivado.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end" style="gap:.5rem">
                            <a href="{{ tenant_url('tenant.ventas.venta.index') }}" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="btnEmitir"
                                {{ !empty($problemasSede) ? 'disabled' : '' }}>
                                <i class="fa fa-file-invoice"></i> Emitir nota de crédito
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body nc-resumen">
                    <h6 class="mb-3">Comprobante afectado</h6>
                    <dl class="row mb-0">
                        <dt class="col-5">Documento</dt>
                        <dd class="col-7">{{ $documento->DOV_Nombre }}</dd>

                        <dt class="col-5">Tipo</dt>
                        <dd class="col-7">{{ $documento->DOV_Tipo === 'FAC' ? 'Factura' : 'Boleta' }}</dd>

                        <dt class="col-5">Cliente</dt>
                        <dd class="col-7">{{ $venta->CLI_Nombre }}</dd>

                        <dt class="col-5">Documento cliente</dt>
                        <dd class="col-7">{{ $venta->CLI_NumDocumento }}</dd>

                        <dt class="col-5">Sede</dt>
                        <dd class="col-7">{{ $venta->ALM_NombreAlmacen }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            const etiquetas = {};
            $('#cod_motivo option').each(function () {
                if ($(this).val()) etiquetas[$(this).val()] = $(this).data('etiqueta');
            });

            $('#cod_motivo').on('change', function () {
                const cod = $(this).val();
                if (cod && $('#des_motivo').val().trim() === '') {
                    $('#des_motivo').val(etiquetas[cod] || '');
                }
            });

            function recalcularTotales() {
                let total = 0;
                $('#tablaItems tbody tr').each(function () {
                    const $fila = $(this);
                    const activo = $fila.find('.chkItem').is(':checked');
                    const precio = parseFloat($fila.data('precio')) || 0;
                    let cantidad = parseFloat($fila.find('.inpCantidad').val()) || 0;

                    $fila.find('.inpCantidad').prop('disabled', !activo);

                    const subtotal = activo ? cantidad * precio : 0;
                    $fila.find('.fila-subtotal').text('S/ ' + subtotal.toFixed(2));
                    total += subtotal;
                });
                $('#totalEstimado').text('S/ ' + total.toFixed(2));
            }

            $('#tablaItems').on('change input', '.chkItem, .inpCantidad', recalcularTotales);
            recalcularTotales();

            $('#formNotaCredito').on('submit', function (e) {
                e.preventDefault();

                const algunoMarcado = $('#tablaItems .chkItem:checked').length > 0;
                if (!algunoMarcado) {
                    Swal.fire({ icon: 'warning', title: 'Selecciona al menos un producto' });
                    return;
                }
                if (!$('#cod_motivo').val()) {
                    Swal.fire({ icon: 'warning', title: 'Selecciona un motivo' });
                    return;
                }

                const $btn = $('#btnEmitir').prop('disabled', true);
                const textoOriginal = $btn.html();
                $btn.html('Emitiendo...');

                $.ajax({
                    url: window.location.href,
                    method: 'POST',
                    data: $(this).serialize()
                }).done(function (r) {
                    if (!r.success) {
                        Swal.fire({ icon: 'error', title: 'No se pudo emitir', text: r.error || '' });
                        $btn.prop('disabled', false).html(textoOriginal);
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Nota de crédito creada',
                        text: r.documento + ' se está enviando a SUNAT.',
                        confirmButtonText: 'Ir a ventas'
                    }).then(function () {
                        window.location.href = '{{ tenant_url("tenant.ventas.venta.index") }}';
                    });
                }).fail(function (xhr) {
                    const r = xhr.responseJSON || {};
                    Swal.fire({ icon: 'error', title: 'No se pudo emitir', text: r.error || 'Error de conexión.' });
                    $btn.prop('disabled', false).html(textoOriginal);
                });
            });
        });
    </script>
@endsection
