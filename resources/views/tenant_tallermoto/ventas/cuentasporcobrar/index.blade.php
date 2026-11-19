@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')

@section('titulo', 'Cuentas por Cobrar')

@section('contenido')
    <style>
        .card{
            border-radius: 18px;
        }

        .modal-content{
            border-radius: 18px;
        }
    </style>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">CUENTAS POR COBRAR</h5>
                <p class="text-muted" style="font-size: 13px;">
                    Ventas al credito (Nota, Boleta o Factura) que todavia no se cobraron por completo.
                </p>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-control" id="filtro_estado">
                            <option value="">Estado: Todos</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="VENCIDA">Vencida</option>
                            <option value="PAGADA">Pagada</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="filtro_cliente" placeholder="Cliente: nombre o numero de documento">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btnFiltrar"><i class="fa fa-search mr-1"></i>Filtrar</button>
                    </div>
                </div>

                <div class="table-responsive" style="background:#FFF;">
                    <table class="table" id="lista_cuentas">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Documento</th>
                                <th scope="col">Total</th>
                                <th scope="col">Abonado</th>
                                <th scope="col">Saldo</th>
                                <th scope="col">Vencimiento</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ABONAR -->
    <div class="modal fade" id="modalAbonar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-usd mr-2"></i>Registrar Abono</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Saldo pendiente: <strong>S/ <span id="abonar_saldo">0.00</span></strong>
                    </div>
                    <input type="hidden" id="abonar_cpc_id">
                    <div class="form-group">
                        <label>Monto a abonar</label>
                        <input type="number" min="0.01" step="0.01" class="form-control" id="abonar_monto">
                    </div>
                    <div class="form-group">
                        <label>Metodo de pago</label>
                        <select class="form-control" id="abonar_metodo">
                            <option value="">Seleccionar ...</option>
                            @foreach ($metodosPago as $mp)
                                <option value="{{ $mp->MEP_Id }}">{{ $mp->MEP_Pago }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Observacion (opcional)</label>
                        <textarea class="form-control" id="abonar_observacion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarAbono"><i class="fa fa-save mr-1"></i>Registrar Abono</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VER -->
    <div class="modal fade" id="modalVerCuenta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-invoice-dollar mr-2"></i>Detalle de la Cuenta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><small class="text-muted d-block">Cliente</small><strong id="ver_cpc_cliente"></strong></div>
                        <div class="col-md-4"><small class="text-muted d-block">Documento</small><strong id="ver_cpc_documento"></strong></div>
                        <div class="col-md-4"><small class="text-muted d-block">Vencimiento</small><strong id="ver_cpc_vencimiento"></strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><small class="text-muted d-block">Total</small><strong>S/ <span id="ver_cpc_total"></span></strong></div>
                        <div class="col-md-4"><small class="text-muted d-block">Abonado</small><strong class="text-success">S/ <span id="ver_cpc_abonado"></span></strong></div>
                        <div class="col-md-4"><small class="text-muted d-block">Saldo</small><strong class="text-danger">S/ <span id="ver_cpc_saldo"></span></strong></div>
                    </div>
                    <h6 class="font-weight-bold">Historial de Abonos</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Metodo</th>
                                    <th>Usuario</th>
                                    <th>Observacion</th>
                                    <th class="text-right">Accion</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_ver_abonos"></tbody>
                        </table>
                    </div>
                    <div id="bloque_abonos_anulados" style="display:none;">
                        <h6 class="font-weight-bold text-muted">Abonos anulados</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-muted">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Abono</th>
                                        <th>Monto</th>
                                        <th>Anulado</th>
                                        <th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_ver_abonos_anulados"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            var table = $('#lista_cuentas').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: {
                    url: "{{ tenant_url('tenant.ventas.cuentasporcobrar.index') }}",
                    data: function(d) {
                        d.estado = $('#filtro_estado').val();
                        d.cliente = $('#filtro_cliente').val();
                    }
                },
                columns: [
                    { data: 'CPC_Id', name: 'CPC_Id' },
                    { data: 'CLI_Nombre', name: 'CLI_Nombre' },
                    { data: 'documento', name: 'documento' },
                    { data: 'CPC_MontoTotal', name: 'CPC_MontoTotal' },
                    { data: 'CPC_MontoAbonado', name: 'CPC_MontoAbonado' },
                    { data: 'CPC_MontoFaltante', name: 'CPC_MontoFaltante' },
                    { data: 'CPC_FechaVencimiento', name: 'CPC_FechaVencimiento' },
                    { data: 'estado_real', name: 'estado_real', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#btnFiltrar').on('click', function() {
                table.ajax.reload();
            });

            $('body').on('click', '.abonarCuenta', function() {
                $('#abonar_cpc_id').val($(this).data('id'));
                $('#abonar_saldo').text(parseFloat($(this).data('saldo')).toFixed(2));
                $('#abonar_monto').val('');
                $('#abonar_metodo').val('');
                $('#abonar_observacion').val('');
                $('#modalAbonar').modal('show');
            });

            $('#btnGuardarAbono').on('click', function() {
                var id = $('#abonar_cpc_id').val();
                var monto = parseFloat($('#abonar_monto').val());
                var metodo = $('#abonar_metodo').val();

                if (!monto || monto <= 0) {
                    Toast.fire({ type: 'error', title: 'Indica un monto valido.' });
                    return;
                }
                if (!metodo) {
                    Toast.fire({ type: 'error', title: 'Selecciona el metodo de pago.' });
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ tenant_url('tenant.ventas.cuentasporcobrar.abonar', ['id' => ':id']) }}".replace(':id', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        monto: monto,
                        metodo_pago: metodo,
                        observacion: $('#abonar_observacion').val()
                    },
                    success: function(data) {
                        Toast.fire({ type: 'success', title: data.success });
                        $('#modalAbonar').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        var r = xhr.responseJSON || {};
                        Toast.fire({ type: 'error', title: r.error || 'No se pudo registrar el abono.' });
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });

            function esc(t) {
                return $('<div>').text(t == null ? '' : t).html();
            }

            $('body').on('click', '.verCuenta', function() {
                cargarCuenta($(this).data('id'));
                $('#modalVerCuenta').modal('show');
            });

            $('body').on('click', '.anularAbono', function() {
                var cuentaId = $(this).data('cuenta');
                var abonoId = $(this).data('abono');
                var monto = parseFloat($(this).data('monto')).toFixed(2);

                Swal.fire({
                    icon: 'warning',
                    title: 'Anular abono de S/ ' + monto,
                    text: 'La deuda vuelve a la cuenta y el abono deja de contar en caja y reportes.',
                    input: 'text',
                    inputPlaceholder: 'Motivo de la anulacion (obligatorio)',
                    inputAttributes: { maxlength: 150 },
                    showCancelButton: true,
                    confirmButtonText: 'Si, anular',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    // Este dialogo se abre encima del modal de detalle y el
                    // modal de Bootstrap le roba el foco al campo de texto
                    // (no deja escribir el motivo). Se desactiva su control
                    // de foco; Bootstrap lo vuelve a poner la proxima vez
                    // que abre un modal.
                    didOpen: function() {
                        $(document).off('focusin.bs.modal');
                        $('.swal2-input').trigger('focus');
                    },
                    inputValidator: function(valor) {
                        if (!valor || !valor.trim()) return 'Escribe el motivo';
                    }
                }).then(function(res) {
                    if (!res.isConfirmed) return;

                    $.ajax({
                        url: "{{ tenant_url('tenant.ventas.cuentasporcobrar.abonos.anular', ['id' => ':id', 'abonoId' => ':abono']) }}"
                            .replace(':id', cuentaId).replace(':abono', abonoId),
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', motivo: res.value.trim() },
                        success: function(r) {
                            Toast.fire({ type: 'success', icon: 'success', title: r.success });
                            cargarCuenta(cuentaId);
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            var r = xhr.responseJSON || {};
                            var msg = r.error || (r.errors && Object.values(r.errors)[0][0]) || 'No se pudo anular el abono.';
                            Swal.fire({ icon: 'error', title: 'No se pudo anular', text: msg });
                        }
                    });
                });
            });

            function cargarCuenta(id) {
                $.get("{{ tenant_url('tenant.ventas.cuentasporcobrar.show', ['id' => ':id']) }}".replace(':id', id), function(data) {
                    var c = data.cuenta;
                    var tipos = { 'PRO': 'Nota', 'BOL': 'Boleta', 'FAC': 'Factura' };
                    $('#ver_cpc_cliente').text(c.CLI_Nombre);
                    $('#ver_cpc_documento').text((tipos[c.DOV_Tipo] || c.DOV_Tipo) + ' ' + c.DOV_Serie + '-' + c.DOV_Numero);
                    $('#ver_cpc_vencimiento').text(c.CPC_FechaVencimiento);
                    $('#ver_cpc_total').text(parseFloat(c.CPC_MontoTotal).toFixed(2));
                    $('#ver_cpc_abonado').text(parseFloat(c.CPC_MontoAbonado).toFixed(2));
                    $('#ver_cpc_saldo').text(parseFloat(c.CPC_MontoFaltante).toFixed(2));

                    var $tbody = $('#tbody_ver_abonos');
                    $tbody.empty();
                    if (!data.abonos.length) {
                        $tbody.append('<tr><td colspan="6" class="text-center text-muted">Todavia no hay abonos.</td></tr>');
                    }
                    data.abonos.forEach(function(a) {
                        $tbody.append('<tr><td>' + esc(a.created_at) + '</td><td>S/ ' + parseFloat(a.CPA_Monto).toFixed(2) + '</td><td>' + esc(a.MEP_Pago) + '</td><td>' + esc(a.usuario) + '</td><td>' + esc(a.CPA_Observacion || '—') + '</td>'
                            + '<td class="text-right"><button type="button" class="btn btn-outline-danger btn-sm anularAbono" data-cuenta="' + id + '" data-abono="' + a.CPA_Id + '" data-monto="' + a.CPA_Monto + '" title="Anular este abono"><i class="fa fa-undo"></i> Anular</button></td></tr>');
                    });

                    var $anulados = $('#tbody_ver_abonos_anulados');
                    $anulados.empty();
                    (data.abonos_anulados || []).forEach(function(a) {
                        $anulados.append('<tr><td>' + esc(a.created_at) + ' · ' + esc(a.MEP_Pago) + '</td><td><del>S/ ' + parseFloat(a.CPA_Monto).toFixed(2) + '</del></td><td>' + esc(a.CPA_FechaAnulacion) + '<br><small>' + esc(a.anulado_por) + '</small></td><td>' + esc(a.CPA_MotivoAnulacion) + '</td></tr>');
                    });
                    $('#bloque_abonos_anulados').toggle((data.abonos_anulados || []).length > 0);
                });
            }
        });
    </script>
@endsection
