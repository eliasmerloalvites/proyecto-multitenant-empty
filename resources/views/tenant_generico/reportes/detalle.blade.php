@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Reportes - Detalle de Ventas')
@section('contenido')

    @include('tenant_generico.reportes._estilos')

    <div class="col-12">

        @include('tenant_generico.reportes._filtros')

        <div class="card rpt-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt mr-1" style="color:#6C3BFF;"></i>
                        DETALLE DE VENTAS
                    </h5>
                    <button type="button" class="btn btn-sm rpt-btn-exportar" onclick="exportarCsv()">
                        <i class="fas fa-file-excel mr-1"></i> Exportar a Excel
                    </button>
                </div>
                <p class="text-muted" style="font-size:12px;">
                    Una fila por cada producto vendido en cada venta del rango seleccionado.
                </p>

                <div class="table-responsive">
                    <table class="table table-hover rpt-table" id="tabla_detalle_ventas" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Venta</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-right">Cant.</th>
                                <th class="text-right">P. Venta</th>
                                <th class="text-right">P. Compra</th>
                                <th class="text-right">Importe</th>
                                <th class="text-right">Costo</th>
                                <th class="text-right">Utilidad</th>
                                <th class="text-right">Margen</th>
                                <th>Método</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    @include('tenant_generico.reportes._reportes_helpers')
    <script>
        const URL_DATOS_DETALLE = "{{ tenant_url('tenant.reportes.generico.detalle.datos') }}";
        const URL_EXPORTAR = "{{ tenant_url('tenant.reportes.generico.exportar', ['tipo' => 'detalle']) }}";

        let tablaDetalle;

        function buscarReporte() {
            $.ajax({
                url: URL_DATOS_DETALLE,
                method: 'GET',
                data: {
                    fecha_desde: $('#filtroFechaDesde').val(),
                    fecha_hasta: $('#filtroFechaHasta').val(),
                    almacen_id: $('#filtroAlmacen').length ? $('#filtroAlmacen').val() : ''
                },
                success: function(response) {
                    tablaDetalle.clear();
                    tablaDetalle.rows.add(response.lineas);
                    tablaDetalle.draw();
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'No se pudo generar el reporte' });
                }
            });
        }

        function exportarCsv() {
            window.open(urlConFiltros(URL_EXPORTAR), '_blank');
        }

        $(document).ready(function() {
            tablaDetalle = $('#tabla_detalle_ventas').DataTable({
                data: @json($lineasDetalle),
                pageLength: 25,
                lengthMenu: [25, 50, 100, 250],
                order: [],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros',
                    zeroRecords: 'Sin ventas en este rango',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'No hay registros',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar:',
                    paginate: { next: 'Siguiente', previous: 'Anterior' }
                },
                columns: [
                    { data: 'fecha' },
                    { data: 'ventaId', render: (d) => '#' + d },
                    {
                        data: null,
                        render: (row) => row.cliente + '<div class="text-muted" style="font-size:11px;">' + (row.documento || '') + '</div>'
                    },
                    { data: 'producto' },
                    { data: 'categoria' },
                    { data: 'cantidad', className: 'text-right' },
                    { data: 'precioVenta', className: 'text-right', render: moneyFmt },
                    { data: 'precioCompra', className: 'text-right', render: moneyFmt },
                    { data: 'importe', className: 'text-right', render: moneyFmt },
                    { data: 'costo', className: 'text-right', render: moneyFmt },
                    { data: 'utilidad', className: 'text-right', render: (v) => '<strong>' + moneyFmt(v) + '</strong>' },
                    { data: 'margen', className: 'text-right', render: (v) => '<span class="rpt-pill">' + v + '%</span>' },
                    { data: 'metodoPago' },
                ]
            });
        });
    </script>
@endsection
