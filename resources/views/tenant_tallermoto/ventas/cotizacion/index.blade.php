@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Cotizaciones')

@section('contenido')

    <style>
        .cot-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px; margin-bottom: 16px;
        }
        .cot-header h4 { margin: 0; font-weight: 700; }
        /* Esta tarjeta es siempre clara (fondo blanco), independiente del
           tema oscuro/claro que tenga el panel: por eso fija su propio color
           de texto en vez de heredar el del body (gris muy claro en modo
           oscuro, ilegible sobre fondo blanco). */
        .cot-card { background: #fff; color: #1F2937; border-radius: 16px; border: 1px solid #EEF2F7; box-shadow: 0 4px 18px rgba(0,0,0,.04); padding: 18px; }
        .cot-card #tabla_cotizaciones { color: #1F2937; }
        .cot-card .dataTables_empty { color: #9CA3AF; }
        .filtro-estado { min-width: 190px; }
    </style>

    <div class="col-12">
        <div class="cot-header">
            <h4><i class="fa fa-file-invoice mr-2 text-primary"></i>Cotizaciones</h4>
            <div class="d-flex" style="gap:8px;">
                <select id="filtroEstado" class="form-control filtro-estado">
                    <option value="TODAS">Todos los estados</option>
                    <option value="PENDIENTE" selected>Pendientes</option>
                    <option value="VENCIDA">Vencidas</option>
                    <option value="APROBADA">Aprobadas</option>
                    <option value="RECHAZADA">Rechazadas</option>
                </select>
                @can('tenant.ventas.cotizacion.create')
                    <a href="{{ tenant_url('tenant.ventas.cotizacion.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Nueva cotización
                    </a>
                @endcan
            </div>
        </div>

        <div class="cot-card">
            <div class="table-responsive">
                <table id="tabla_cotizaciones" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Vence</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL: WhatsApp -->
    <div class="modal fade" id="modalWhatsappCot" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header" style="border:none;">
                    <h5 class="modal-title text-success"><i class="fab fa-whatsapp mr-2"></i>Enviar cotización</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cotWhatsappId">
                    <label class="small text-muted font-weight-bold">Celular (9 dígitos)</label>
                    <input type="text" id="numeroWhatsappCot" class="form-control" placeholder="9xxxxxxxx" maxlength="9">
                    <button type="button" class="btn btn-success btn-block mt-3" id="btnEnviarWhatsappCot">
                        <i class="fab fa-whatsapp"></i> Enviar por WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let tabla;

        $(document).ready(function () {
            tabla = $('#tabla_cotizaciones').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    zeroRecords: 'Nada encontrado',
                    info: 'Mostrando la página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar:',
                    paginate: { next: 'Siguiente', previous: 'Anterior' }
                },
                ajax: {
                    url: "{{ tenant_url('tenant.ventas.cotizacion.index') }}",
                    data: function (d) { d.estado = $('#filtroEstado').val(); }
                },
                columns: [
                    { data: 'numero', name: 'numero' },
                    { data: 'cliente', name: 'cliente' },
                    { data: 'fecha', name: 'fecha' },
                    { data: 'vencimiento', name: 'vencimiento' },
                    { data: 'total', name: 'total' },
                    { data: 'estado', name: 'estado', orderable: false },
                    { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-right' },
                ]
            });

            $('#filtroEstado').on('change', function () { tabla.ajax.reload(); });

            $('#tabla_cotizaciones').on('click', '.aprobarCotizacion', function () {
                let id = $(this).data('id');
                Swal.fire({
                    icon: 'question',
                    title: '¿Aprobar esta cotización?',
                    text: 'Se abrirá el punto de venta con el carrito precargado para completar el cobro.',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar'
                }).then(function (res) {
                    if (res.isConfirmed) {
                        window.location.href = '{{ tenant_url("tenant.ventas.cotizacion.aprobar", ["cotizacion" => ":id"]) }}'.replace(':id', id);
                    }
                });
            });

            $('#tabla_cotizaciones').on('click', '.rechazarCotizacion', function () {
                let id = $(this).data('id');
                Swal.fire({
                    icon: 'warning',
                    title: '¿Rechazar esta cotización?',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, rechazar',
                    cancelButtonText: 'Cancelar'
                }).then(function (res) {
                    if (!res.isConfirmed) return;
                    $.ajax({
                        url: '{{ tenant_url("tenant.ventas.cotizacion.rechazar", ["cotizacion" => ":id"]) }}'.replace(':id', id),
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' }
                    }).done(function () { tabla.ajax.reload(null, false); })
                      .fail(function (xhr) { Swal.fire({ icon: 'error', title: 'No se pudo rechazar', text: (xhr.responseJSON && xhr.responseJSON.message) || '' }); });
                });
            });

            $('#tabla_cotizaciones').on('click', '.eliminarCotizacion', function () {
                let id = $(this).data('id');
                Swal.fire({
                    icon: 'warning',
                    title: '¿Eliminar esta cotización?',
                    text: 'Esta acción no se puede deshacer.',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(function (res) {
                    if (!res.isConfirmed) return;
                    $.ajax({
                        url: '{{ tenant_url("tenant.ventas.cotizacion.destroy", ["cotizacion" => ":id"]) }}'.replace(':id', id),
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' }
                    }).done(function () { tabla.ajax.reload(null, false); })
                      .fail(function (xhr) { Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: (xhr.responseJSON && xhr.responseJSON.message) || '' }); });
                });
            });

            $('#tabla_cotizaciones').on('click', '.whatsappCotizacion', function () {
                let id = $(this).data('id');
                $('#cotWhatsappId').val(id);
                $('#numeroWhatsappCot').val('');

                $.get('/tenant/ventas/cotizacion/' + id + '/whatsapp').done(function (r) {
                    if (r.celular) {
                        $('#numeroWhatsappCot').val(String(r.celular).replace(/\D/g, '').slice(-9));
                    }
                });

                $('#modalWhatsappCot').modal('show');
            });

            $('#btnEnviarWhatsappCot').on('click', function () {
                let numero = ($('#numeroWhatsappCot').val() || '').replace(/\D/g, '');
                let id = $('#cotWhatsappId').val();

                if (numero.length !== 9) {
                    Swal.fire({ icon: 'warning', title: 'Número inválido', text: 'Ingresa los 9 dígitos del celular.' });
                    return;
                }

                let $btn = $(this).prop('disabled', true).html('Preparando...');

                $.get('/tenant/ventas/cotizacion/' + id + '/whatsapp')
                    .done(function (r) {
                        if (!r.success) {
                            Swal.fire({ icon: 'error', title: 'No se pudo preparar', text: r.descripcion || '' });
                            return;
                        }
                        let mensaje = encodeURIComponent(
                            'Hola' + (r.cliente ? ' ' + r.cliente : '') + ' 👋\n' +
                            'Aquí tienes tu cotización ' + r.numero + ':\n' + r.url
                        );
                        window.open('https://wa.me/51' + numero + '?text=' + mensaje, '_blank');
                        $('#modalWhatsappCot').modal('hide');
                    })
                    .fail(function (xhr) {
                        let r = xhr.responseJSON || {};
                        Swal.fire({ icon: 'error', title: 'No se pudo preparar la cotización', text: r.descripcion || 'Error de conexión.' });
                    })
                    .always(function () { $btn.prop('disabled', false).html('<i class="fab fa-whatsapp"></i> Enviar por WhatsApp'); });
            });
        });
    </script>
@endsection
