@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')

@section('titulo', 'Cotizaciones')

@section('contenido')

    <style>
        .cot-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .cot-card .card-title {
            font-weight: 800;
            color: #1F2937;
        }

        .cot-btn-nueva {
            background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
            color: #fff;
            border: none;
            font-weight: 700;
            border-radius: 10px;
            padding: 8px 18px;
            box-shadow: 0 2px 8px rgba(108,59,255,.35);
        }

        .cot-btn-nueva:hover {
            color: #fff;
            opacity: .92;
        }

        .cot-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .cot-badge-pendiente { background: #EDE9FE; color: #6C3BFF; }
        .cot-badge-aprobada { background: #DCFCE7; color: #16A34A; }
        .cot-badge-rechazada { background: #F3F4F6; color: #6B7280; }
        .cot-badge-anulada { background: #FEE2E2; color: #DC2626; }

        #tabla_cotizaciones.table td {
            vertical-align: middle;
        }

        .cot-btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 4px;
        }

        @media (max-width: 576px) {
            .cot-header-row {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .cot-btn-nueva {
                margin-top: 10px;
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <div class="col-12">
        <div class="card cot-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 cot-header-row">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-signature mr-1" style="color:#6C3BFF;"></i>
                        COTIZACIONES
                    </h5>
                    <a href="{{ tenant_url('tenant.ventas.cotizacion.create.generico') }}" class="cot-btn-nueva">
                        <i class="fas fa-plus mr-1"></i> Nueva Cotización
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="tabla_cotizaciones" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Cliente</th>
                                <th>Sede</th>
                                <th>Total</th>
                                <th>Vence</th>
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

    <!-- MODAL VER COTIZACION -->
    <div class="modal fade" id="modalVerCotizacion" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header" style="background:linear-gradient(135deg, #6C3BFF, #8B5CF6); color:#fff;">
                    <h5 class="modal-title font-weight-bold">Cotización #<span id="cotVerId"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Cliente</small>
                            <strong id="cotVerCliente"></strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Sede</small>
                            <strong id="cotVerAlmacen"></strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Vence</small>
                            <strong id="cotVerVence"></strong>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered bg-white">
                            <thead class="bg-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">P. Unitario</th>
                                    <th class="text-center">Descuento</th>
                                    <th class="text-center">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="cotVerDetalle"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th class="text-center" id="cotVerTotal"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="cotVerObservacionesBox" style="display:none;">
                        <small class="text-muted d-block">Observaciones</small>
                        <p id="cotVerObservaciones"></p>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cerrar</button>
                    <a href="javascript:void(0)" class="btn btn-warning" id="cotVerBtnEditar" style="display:none;">
                        <i class="fas fa-pen mr-1"></i> Editar
                    </a>
                    <a href="javascript:void(0)" class="btn btn-success" id="cotVerBtnConvertir" style="display:none;">
                        <i class="fas fa-cash-register mr-1"></i> Convertir a Venta
                    </a>
                    <button type="button" class="btn cot-btn-nueva" id="cotVerBtnPdf">
                        <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
                    </button>
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

            var table = $('#tabla_cotizaciones').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: "{{ tenant_url('tenant.ventas.cotizacion.index.generico') }}",
                columns: [
                    { data: 'COT_Id', name: 'cot.COT_Id', render: (d) => '#' + d },
                    { data: 'CLI_Nombre', name: 'CLI_Nombre' },
                    { data: 'ALM_NombreAlmacen', name: 'ALM_NombreAlmacen' },
                    { data: 'COT_Total', name: 'cot.COT_Total', render: (v) => 'S/ ' + parseFloat(v).toFixed(2) },
                    { data: 'COT_FechaVencimiento', name: 'cot.COT_FechaVencimiento', render: (v) => v || '-' },
                    { data: 'created_at', name: 'cot.created_at' },
                    { data: 'COT_Estado', name: 'cot.COT_Estado' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('body').on('click', '.eyeCotizacion', function() {
                var id = $(this).data('id');
                $('#cotVerBtnPdf').data('id', id);

                $.get('{{ tenant_url('tenant.ventas.cotizacion.show.generico', ['cotizacion' => ':id']) }}'.replace(':id', id),
                    function(data) {
                        $('#cotVerId').text(data.cotizacion.COT_Id);
                        $('#cotVerCliente').text(data.cotizacion.CLI_Nombre || 'Sin cliente');
                        $('#cotVerAlmacen').text(data.cotizacion.ALM_NombreAlmacen);
                        $('#cotVerVence').text(data.cotizacion.COT_FechaVencimiento || '-');
                        $('#cotVerTotal').text('S/ ' + parseFloat(data.cotizacion.COT_Total).toFixed(2));

                        // "Editar" y "Convertir a Venta" solo tienen sentido
                        // mientras esta Pendiente (COT_Estado == 1), igual
                        // que valida el backend.
                        if (parseInt(data.cotizacion.COT_Estado) === 1) {
                            var urlEditar = '{{ tenant_url('tenant.ventas.cotizacion.edit.generico', ['cotizacion' => ':id']) }}'.replace(':id', id);
                            $('#cotVerBtnEditar').attr('href', urlEditar).show();

                            var urlConvertir = '{{ tenant_url('tenant.ventas.venta.create') }}?desde_cotizacion=' + id;
                            $('#cotVerBtnConvertir').attr('href', urlConvertir).show();
                        } else {
                            $('#cotVerBtnEditar').hide();
                            $('#cotVerBtnConvertir').hide();
                        }

                        if (data.cotizacion.COT_Observaciones) {
                            $('#cotVerObservaciones').text(data.cotizacion.COT_Observaciones);
                            $('#cotVerObservacionesBox').show();
                        } else {
                            $('#cotVerObservacionesBox').hide();
                        }

                        $('#cotVerDetalle').html('');
                        data.detalles.forEach(function(d) {
                            var subtotal = (d.DCOT_Cantidad * d.DCOT_PrecioUnitario) - parseFloat(d.DCOT_Descuento || 0);
                            $('#cotVerDetalle').append(`
                                <tr>
                                    <td>${d.PRO_Nombre}</td>
                                    <td class="text-center">${d.DCOT_Cantidad}</td>
                                    <td class="text-center">S/ ${parseFloat(d.DCOT_PrecioUnitario).toFixed(2)}</td>
                                    <td class="text-center">S/ ${parseFloat(d.DCOT_Descuento || 0).toFixed(2)}</td>
                                    <td class="text-center">S/ ${subtotal.toFixed(2)}</td>
                                </tr>
                            `);
                        });

                        $('#modalVerCotizacion').modal('show');
                    }
                ).fail(function() {
                    Toast.fire({ icon: 'error', title: 'No se pudo cargar la cotización.' });
                });
            });

            $('#cotVerBtnPdf').on('click', function() {
                var id = $(this).data('id');
                abrirPdfCotizacion(id);
            });

            $('body').on('click', '.pdfCotizacion', function() {
                abrirPdfCotizacion($(this).data('id'));
            });

            function abrirPdfCotizacion(id) {
                var url = '{{ tenant_url('tenant.ventas.cotizacion.pdf.generico', ['cotizacion' => ':id']) }}'.replace(':id', id);
                window.open(url, '_blank');
            }

            $('body').on('click', '.anularCotizacion', function() {
                var id = $(this).data('id');

                Swal.fire({
                    icon: 'warning',
                    title: '¿Anular esta cotización?',
                    text: 'Quedará marcada como Anulada. No se puede deshacer.',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, anular',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: '{{ tenant_url('tenant.ventas.cotizacion.anular.generico', ['cotizacion' => ':id']) }}'.replace(':id', id),
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(data) {
                            table.draw(false);
                            Toast.fire({ icon: 'success', title: data.success });
                        },
                        error: function(xhr) {
                            var data = xhr.responseJSON || {};
                            Toast.fire({ icon: 'error', title: data.error || 'No se pudo anular la cotización.' });
                        }
                    });
                });
            });
        });
    </script>
@endsection
