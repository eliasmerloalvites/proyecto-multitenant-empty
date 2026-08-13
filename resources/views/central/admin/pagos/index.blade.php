@extends('central.layout.appAdminLte')
@section('titulo', 'Cobros')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">COBROS Y FACTURACIÓN</h5>
                <p class="text-muted">
                    Ciclo de facturación actual de cada cliente activo, calculado a partir de su día de cobro
                    (billing_day). Registra el pago cuando el cliente cancele su mensualidad.
                </p>

                <div class="table-responsive" style="background:#FFF;">
                    <table class="table table-striped nowrap" id="table-cobros" name="table-cobros">
                        <thead>
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Plan</th>
                                <th scope="col">Fecha de Cobro</th>
                                <th scope="col">Monto</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Opciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL REGISTRAR PAGO --}}
    {{-- ===================================================== --}}

    <div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded">

                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title mb-0">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Registrar Pago — <span id="pago_cliente_nombre"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="RegistrarPagoForm">
                    @csrf
                    <input type="hidden" id="pago_client_id">

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Monto (S/)</label>
                                <input type="number" step="0.01" min="0" name="monto" id="pago_monto"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Fecha de Pago</label>
                                <input type="date" name="fecha_pago" id="pago_fecha" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Método de Pago</label>
                                <select class="form-control" name="metodo_pago" id="pago_metodo">
                                    <option value="transferencia">Transferencia</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="yape_plin">Yape / Plin</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-1">
                                <label>Nota (opcional)</label>
                                <textarea name="nota" id="pago_nota" class="form-control" rows="2"
                                    placeholder="Referencia, número de operación, etc."></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer bg-white border-0">
                        <button type="button" class="btn btn-light border px-4" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-check mr-1"></i>
                            Confirmar Pago
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL HISTORIAL DE PAGOS --}}
    {{-- ===================================================== --}}

    <div class="modal fade" id="modalHistorial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded">

                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title mb-0">
                        <i class="fas fa-history mr-2"></i>
                        Historial de Pagos — <span id="historial_cliente_nombre"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Fecha de Pago</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Registrado por</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody id="historial_tbody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-white border-0">
                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            var table = $('#table-cobros').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [3, 'asc']
                ],
                ajax: "{{ route('admin.cobros.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'razon_social', name: 'razon_social' },
                    { data: 'plan', name: 'plan' },
                    { data: 'fecha_cobro', name: 'fecha_cobro' },
                    { data: 'monto', name: 'monto' },
                    { data: 'estado', name: 'estado' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            // Registrar Pago: abrir modal precargado
            $('body').on('click', '.registrarPago', function() {
                $('#pago_client_id').val($(this).data('id'));
                $('#pago_cliente_nombre').text($(this).data('nombre'));
                $('#pago_monto').val($(this).data('monto'));
                $('#pago_fecha').val(new Date().toISOString().split('T')[0]);
                $('#pago_metodo').val('transferencia');
                $('#pago_nota').val('');
                $('#modalRegistrarPago').modal('show');
            });

            $('#RegistrarPagoForm').on('submit', function(e) {
                e.preventDefault();

                const clientId = $('#pago_client_id').val();

                $.ajax({
                    url: "{{ url('admin/cobros') }}/" + clientId,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(data) {
                        $('#modalRegistrarPago').modal('hide');
                        table.draw();
                        Toast.fire({
                            icon: 'success',
                            title: data.success
                        });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : 'No se pudo registrar el pago.';
                        Toast.fire({
                            icon: 'error',
                            title: msg
                        });
                    }
                });
            });

            // Ver historial
            $('body').on('click', '.verHistorial', function() {
                const clientId = $(this).data('id');
                $('#historial_cliente_nombre').text($(this).data('nombre'));
                $('#historial_tbody').html('<tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>');
                $('#modalHistorial').modal('show');

                $.get("{{ url('admin/cobros') }}/" + clientId + "/historial", function(data) {
                    if (!data.data.length) {
                        $('#historial_tbody').html('<tr><td colspan="6" class="text-center text-muted">Sin pagos registrados.</td></tr>');
                        return;
                    }

                    let rows = '';
                    data.data.forEach(function(p) {
                        rows += `<tr>
                            <td>${p.periodo}</td>
                            <td>${p.fecha_pago}</td>
                            <td>S/ ${p.monto}</td>
                            <td>${p.metodo_pago}</td>
                            <td>${p.registrado_por}</td>
                            <td>${p.nota ?? ''}</td>
                        </tr>`;
                    });

                    $('#historial_tbody').html(rows);
                });
            });

        });
    </script>
@endsection
