@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Registro de Gastos')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1"><i class="fas fa-receipt mr-2 text-warning"></i>Registro de Gastos</h4>
                        <p class="text-muted mb-0">Gastos operativos (alquiler, servicios, planilla, etc.) por tipo y método de pago.</p>
                    </div>
                    <a href="{{ route('tenant.contabilidad.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Contabilidad
                    </a>
                </div>

                <!-- FILTROS -->
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label class="mb-1">Periodo</label>
                                <div class="btn-group btn-group-toggle d-flex">
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn active" data-periodo="hoy">Hoy</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="semana">Esta semana</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="mes">Este mes</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill periodo-btn" data-periodo="personalizado">Personalizado</button>
                                </div>
                            </div>
                            <div class="col-md-2 fecha-personalizada" style="display:none;">
                                <label>Desde</label>
                                <input type="date" class="form-control" id="fecha_inicio">
                            </div>
                            <div class="col-md-2 fecha-personalizada" style="display:none;">
                                <label>Hasta</label>
                                <input type="date" class="form-control" id="fecha_fin">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total de Gastos</span>
                                <span class="info-box-number" id="tot_monto">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0" id="contador_filas">0 gastos</h6>
                    <button type="button" class="btn btn-success btn-sm" id="btnExportar">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Proveedor</th>
                                <th>Método de Pago</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_filas">
                            <tr><td colspan="6" class="text-center text-muted py-4">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    (function () {
        let periodo = 'hoy';

        function money(n) {
            return 'S/ ' + Number(n || 0).toFixed(2);
        }

        function paramsActuales() {
            let p = { periodo: periodo };
            if (periodo === 'personalizado') {
                p.fecha_inicio = $('#fecha_inicio').val();
                p.fecha_fin = $('#fecha_fin').val();
            }
            return p;
        }

        function cargar() {
            $('#tbody_filas').html('<tr><td colspan="6" class="text-center text-muted py-4">Cargando...</td></tr>');

            $.get("{{ route('tenant.contabilidad.gastos') }}", $.extend({}, paramsActuales()), function (resp) {
                $('#tot_monto').text(money(resp.totales.monto));
                $('#contador_filas').text(resp.filas.length + ' gasto(s)');

                if (resp.filas.length === 0) {
                    $('#tbody_filas').html('<tr><td colspan="6" class="text-center text-muted py-4">No hay gastos en este periodo.</td></tr>');
                    return;
                }

                let html = '';
                resp.filas.forEach(function (f) {
                    html += `<tr>
                        <td>${f.fecha}</td>
                        <td>${f.tipo}</td>
                        <td>${f.descripcion ?? '-'}</td>
                        <td>${f.proveedor}</td>
                        <td>${f.metodo_pago}</td>
                        <td class="text-right font-weight-bold">${money(f.monto)}</td>
                    </tr>`;
                });
                $('#tbody_filas').html(html);
            }, 'json').fail(function () {
                $('#tbody_filas').html('<tr><td colspan="6" class="text-center text-danger py-4">No se pudo cargar el reporte.</td></tr>');
            });
        }

        $('.periodo-btn').on('click', function () {
            $('.periodo-btn').removeClass('active');
            $(this).addClass('active');
            periodo = $(this).data('periodo');
            $('.fecha-personalizada').toggle(periodo === 'personalizado');
            if (periodo !== 'personalizado') cargar();
        });

        $('#fecha_inicio, #fecha_fin').on('change', function () {
            if ($('#fecha_inicio').val() && $('#fecha_fin').val()) cargar();
        });

        $('#btnExportar').on('click', function () {
            let params = $.param($.extend({ export: 'xlsx' }, paramsActuales()));
            window.location.href = "{{ route('tenant.contabilidad.gastos') }}?" + params;
        });

        cargar();
    })();
</script>
@endpush
