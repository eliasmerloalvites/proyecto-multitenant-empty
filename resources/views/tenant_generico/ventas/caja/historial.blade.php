@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Historial de Cajas')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">HISTORIAL DE ARQUEOS DE CAJA</h5>
                    <a href="{{ tenant_url('tenant.ventas.caja.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a Cajas
                    </a>
                </div>

                <div class="table-responsive" style="background:#FFF;">
                    <table class="table" id="tabla_historial">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Caja</th>
                                <th>Apertura</th>
                                <th>Cierre</th>
                                <th>Monto Apertura</th>
                                <th>Monto Esperado</th>
                                <th>Monto Real</th>
                                <th>Diferencia</th>
                                <th>Estado</th>
                                <th>Usuarios (apertura/cierre)</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE -->
    <div class="modal fade" id="modalDetalleSesion" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del turno</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="detalleSesionBody">
                    <p class="text-muted">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            var table = $('#tabla_historial').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                order: [[0, "desc"]],
                dom: 'Blfrtip',
                buttons: ['copyHtml5', 'excelHtml5', 'pdfHtml5'],
                ajax: "{{ tenant_url('tenant.ventas.caja.historial') }}",
                columns: [
                    { data: 'CS_Id', name: 'CS_Id' },
                    { data: 'caja', name: 'caja' },
                    { data: 'apertura', name: 'apertura' },
                    { data: 'cierre', name: 'cierre' },
                    { data: 'monto_apertura', name: 'monto_apertura' },
                    { data: 'monto_esperado', name: 'monto_esperado' },
                    { data: 'monto_real', name: 'monto_real' },
                    { data: 'diferencia', name: 'diferencia' },
                    { data: 'estado', name: 'estado' },
                    { data: 'usuarios', name: 'usuarios' },
                    { data: 'action', name: 'action' },
                ]
            });

            $('body').on('click', '.verDetalleSesion', function() {
                var id = $(this).data('id');
                $('#detalleSesionBody').html('<p class="text-muted">Cargando...</p>');
                $('#modalDetalleSesion').modal('show');

                $.get('{{ tenant_url('tenant.ventas.caja.historial.detalle', ['id' => ':id']) }}'.replace(':id', id), function(data) {
                    var html = '';

                    html += '<div class="row mb-3">';
                    html += '<div class="col-4"><strong>Apertura:</strong><br>S/ ' + parseFloat(data.sesion.CS_MontoApertura).toFixed(2) + '</div>';
                    html += '<div class="col-4"><strong>Esperado:</strong><br>S/ ' + parseFloat(data.montoEsperado).toFixed(2) + (data.sesion.CS_Estado === 'abierta' ? ' <span class="badge badge-info">en vivo</span>' : '') + '</div>';
                    html += '<div class="col-4"><strong>Real:</strong><br>' + (data.sesion.CS_MontoReal !== null ? 'S/ ' + parseFloat(data.sesion.CS_MontoReal).toFixed(2) : '—') + '</div>';
                    html += '</div>';

                    // RESUMEN POR MÉTODO DE PAGO
                    var r = data.resumenPorMetodo;
                    html += '<div class="table-responsive mb-3">';
                    html += '<table class="table table-sm table-bordered mb-0">';
                    html += '<thead><tr style="background:#111827;color:#fff;">';
                    html += '<th>TIPO DE MOVIMIENTO</th>';
                    r.columnas.forEach(function(c) {
                        html += '<th class="text-center">' + c.nombre.toUpperCase() + '</th>';
                    });
                    html += '<th class="text-center">TOTAL</th>';
                    html += '</tr></thead><tbody>';

                    html += '<tr><td><strong>Ingreso x Ventas</strong></td>';
                    r.columnas.forEach(function(c) { html += '<td class="text-center text-primary">' + c.ventas.toFixed(2) + '</td>'; });
                    html += '<td class="text-center"><strong>' + r.total_ventas.toFixed(2) + '</strong></td></tr>';

                    html += '<tr><td><strong>Egresos x Compras</strong></td>';
                    r.columnas.forEach(function(c) { html += '<td class="text-center">' + c.compras.toFixed(2) + '</td>'; });
                    html += '<td class="text-center"><strong>' + r.total_compras.toFixed(2) + '</strong></td></tr>';

                    html += '<tr><td><strong>Egresos x Gastos</strong></td>';
                    r.columnas.forEach(function(c) { html += '<td class="text-center">' + c.gastos.toFixed(2) + '</td>'; });
                    html += '<td class="text-center"><strong>' + r.total_gastos.toFixed(2) + '</strong></td></tr>';

                    html += '<tr style="border-top:2px solid #DC2626;background:#FEF2F2;"><td class="text-danger"><strong>Totales Netos</strong></td>';
                    r.columnas.forEach(function(c) { html += '<td class="text-center text-danger"><strong>' + c.neto.toFixed(2) + '</strong></td>'; });
                    html += '<td class="text-center text-danger"><strong>' + r.total_neto.toFixed(2) + '</strong></td></tr>';

                    html += '</tbody></table></div>';

                    html += '<h6 class="font-weight-bold">Ventas (' + data.ventas.length + ')</h6>';
                    if (data.ventas.length) {
                        html += '<div class="table-responsive mb-3"><table class="table table-sm"><thead><tr><th>#</th><th>Cliente</th><th>Método</th><th>Total</th><th>Fecha</th><th>Estado</th></tr></thead><tbody>';
                        data.ventas.forEach(function(v) {
                            // Anulada: no cuenta en los totales de arriba (ya
                            // sale con VEN_Status=0), pero se sigue mostrando
                            // aqui para no perder el historial del turno.
                            var anulada = String(v.DOV_Anulado) === '1';
                            var estilo = anulada ? ' style="opacity:.6;text-decoration:line-through;"' : '';
                            var badge = anulada ? '<span class="badge badge-dark" style="text-decoration:none;display:inline-block;">ANULADA</span>' : '<span class="badge badge-success">Activa</span>';
                            html += '<tr' + estilo + '><td>' + v.VEN_Id + '</td><td>' + v.CLI_Nombre + '</td><td>' + v.MEP_Pago + '</td><td>S/ ' + parseFloat(v.total).toFixed(2) + '</td><td>' + v.created_at + '</td><td style="text-decoration:none;">' + badge + '</td></tr>';
                        });
                        html += '</tbody></table></div>';
                    } else {
                        html += '<p class="text-muted mb-3">Sin ventas en este turno.</p>';
                    }

                    html += '<h6 class="font-weight-bold">Compras (' + data.compras.length + ')</h6>';
                    if (data.compras.length) {
                        html += '<div class="table-responsive mb-3"><table class="table table-sm"><thead><tr><th>#</th><th>Proveedor</th><th>Método</th><th>Total</th><th>Fecha</th></tr></thead><tbody>';
                        data.compras.forEach(function(c) {
                            html += '<tr><td>' + c.COM_Id + '</td><td>' + c.PROV_RazonSocial + '</td><td>' + c.MEP_Pago + '</td><td>S/ ' + parseFloat(c.total).toFixed(2) + '</td><td>' + c.created_at + '</td></tr>';
                        });
                        html += '</tbody></table></div>';
                    } else {
                        html += '<p class="text-muted mb-3">Sin compras en este turno.</p>';
                    }

                    html += '<h6 class="font-weight-bold">Gastos (' + data.gastos.length + ')</h6>';
                    if (data.gastos.length) {
                        html += '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>#</th><th>Descripción</th><th>Método</th><th>Monto</th><th>Fecha</th><th>¿Afecta caja?</th></tr></thead><tbody>';
                        data.gastos.forEach(function(g) {
                            var afecta = g.GAS_Afecta === 'SI'
                                ? '<span class="badge badge-success">Sí</span>'
                                : '<span class="badge badge-secondary">No (no se cuenta en el arqueo)</span>';
                            html += '<tr><td>' + g.GAS_Id + '</td><td>' + (g.GAS_Descripcion || '—') + '</td><td>' + g.MEP_Pago + '</td><td>S/ ' + parseFloat(g.GAS_Monto).toFixed(2) + '</td><td>' + g.GAS_Fecha + '</td><td>' + afecta + '</td></tr>';
                        });
                        html += '</tbody></table></div>';
                    } else {
                        html += '<p class="text-muted">Sin gastos en este turno.</p>';
                    }

                    $('#detalleSesionBody').html(html);
                }).fail(function() {
                    $('#detalleSesionBody').html('<p class="text-danger">No se pudo cargar el detalle.</p>');
                });
            });
        });
    </script>
@endsection
