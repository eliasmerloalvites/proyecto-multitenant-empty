@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Resumen de Caja')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1"><i class="fas fa-cash-register mr-2 text-secondary"></i>Resumen de Caja</h4>
                        <p class="text-muted mb-0">Sesiones de caja cerradas (para conciliar contra depósitos/banco) y cuentas por cobrar pendientes.</p>
                    </div>
                    <div>
                        <a href="{{ route('tenant.contabilidad.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Contabilidad
                        </a>
                        <button type="button" class="btn btn-success btn-sm" id="btnExportar">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </button>
                    </div>
                </div>

                <!-- FILTROS: solo aplica a las sesiones de caja (las cuentas por cobrar pendientes se muestran siempre, no dependen de un periodo) -->
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label class="mb-1">Periodo (sesiones de caja)</label>
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
                    <div class="col-md-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Diferencia acumulada de cuadres</span>
                                <span class="info-box-number" id="tot_diferencia">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-danger"><i class="fas fa-hand-holding-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total pendiente por cobrar</span>
                                <span class="info-box-number" id="tot_pendiente">S/ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="mb-2" id="contador_sesiones">Sesiones de caja cerradas</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Caja</th>
                                <th>Apertura</th>
                                <th>Cierre</th>
                                <th class="text-right">Monto Apertura</th>
                                <th class="text-right">Monto Esperado</th>
                                <th class="text-right">Monto Real</th>
                                <th class="text-right">Diferencia</th>
                                <th>Cerrado por</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_sesiones">
                            <tr><td colspan="8" class="text-center text-muted py-4">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-2">Cuentas por cobrar pendientes</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Cliente</th>
                                <th class="text-right">Monto Total</th>
                                <th class="text-right">Abonado</th>
                                <th class="text-right">Pendiente</th>
                                <th>Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_cxc">
                            <tr><td colspan="5" class="text-center text-muted py-4">Cargando...</td></tr>
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
            $('#tbody_sesiones').html('<tr><td colspan="8" class="text-center text-muted py-4">Cargando...</td></tr>');
            $('#tbody_cxc').html('<tr><td colspan="5" class="text-center text-muted py-4">Cargando...</td></tr>');

            $.get("{{ route('tenant.contabilidad.caja') }}", $.extend({}, paramsActuales()), function (resp) {
                $('#tot_diferencia').text(money(resp.totales.diferencia));
                $('#tot_pendiente').text(money(resp.totales.pendiente_cobrar));
                $('#contador_sesiones').text('Sesiones de caja cerradas (' + resp.sesiones.length + ')');

                if (resp.sesiones.length === 0) {
                    $('#tbody_sesiones').html('<tr><td colspan="8" class="text-center text-muted py-4">No hay sesiones cerradas en este periodo.</td></tr>');
                } else {
                    let html = '';
                    resp.sesiones.forEach(function (s) {
                        let claseDif = Math.abs(s.diferencia) > 0.009 ? (s.diferencia < 0 ? 'text-danger' : 'text-warning') : 'text-success';
                        html += `<tr>
                            <td>${s.caja}</td>
                            <td>${s.apertura}</td>
                            <td>${s.cierre}</td>
                            <td class="text-right">${money(s.monto_apertura)}</td>
                            <td class="text-right">${money(s.monto_esperado)}</td>
                            <td class="text-right">${money(s.monto_real)}</td>
                            <td class="text-right font-weight-bold ${claseDif}">${money(s.diferencia)}</td>
                            <td>${s.cerrado_por}</td>
                        </tr>`;
                    });
                    $('#tbody_sesiones').html(html);
                }

                if (resp.cuentasPorCobrar.length === 0) {
                    $('#tbody_cxc').html('<tr><td colspan="5" class="text-center text-muted py-4">No hay cuentas por cobrar pendientes.</td></tr>');
                } else {
                    let htmlCxc = '';
                    resp.cuentasPorCobrar.forEach(function (c) {
                        htmlCxc += `<tr>
                            <td>${c.cliente}</td>
                            <td class="text-right">${money(c.monto_total)}</td>
                            <td class="text-right">${money(c.monto_abonado)}</td>
                            <td class="text-right font-weight-bold text-danger">${money(c.monto_faltante)}</td>
                            <td>${c.vencimiento}</td>
                        </tr>`;
                    });
                    $('#tbody_cxc').html(htmlCxc);
                }
            }, 'json').fail(function () {
                $('#tbody_sesiones').html('<tr><td colspan="8" class="text-center text-danger py-4">No se pudo cargar el reporte.</td></tr>');
                $('#tbody_cxc').empty();
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
            window.location.href = "{{ route('tenant.contabilidad.caja') }}?" + params;
        });

        cargar();
    })();
</script>
@endpush
