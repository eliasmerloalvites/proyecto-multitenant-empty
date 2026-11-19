@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Detalle de Cuenta por Pagar')
@section('contenido')

    <style>
        .cxp-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .cxp-badge-pendiente { background: #EDE9FE; color: #6C3BFF; }
        .cxp-badge-pagado { background: #DCFCE7; color: #16A34A; }

        .cxp-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .cxp-resumen-box {
            border-radius: 14px;
            background: #F9F7FF;
            border: 1px solid #EDE9FE;
            padding: 16px;
            text-align: center;
        }

        .cxp-resumen-box .valor {
            font-size: 22px;
            font-weight: 800;
            color: #1F2937;
        }

        .cxp-resumen-box.destacado {
            background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
            border: none;
        }

        .cxp-resumen-box.destacado .valor,
        .cxp-resumen-box.destacado .label {
            color: #fff;
        }

        .cxp-resumen-box .label {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
    </style>

    <div class="col-12">
        <div class="card cxp-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar mr-1" style="color:#6C3BFF;"></i>
                        CUENTA POR PAGAR — COMPRA #{{ $cuentaPagar->COM_Id }}
                    </h5>
                    <a href="{{ tenant_url('tenant.compras.cuentaspagar.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Proveedor:</strong> {{ $compra->PROV_RazonSocial ?? '-' }}
                        <div class="text-muted" style="font-size:12px;">{{ $compra->PROV_NumDocumento ?? '' }}</div>
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha de emisión:</strong> {{ $cuentaPagar->CXP_FechaEmision }}
                    </div>
                    <div class="col-md-4">
                        <strong>Estado:</strong>
                        @if ($cuentaPagar->CXP_Estado == \App\Models\Tenant\CuentaPagar::ESTADO_PAGADO)
                            <span class="cxp-badge cxp-badge-pagado">Pagado</span>
                        @else
                            <span class="cxp-badge cxp-badge-pendiente">Pendiente</span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxp-resumen-box">
                            <div class="label">Total</div>
                            <div class="valor">S/ {{ number_format($cuentaPagar->CXP_MontoTotal, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxp-resumen-box">
                            <div class="label">Adelanto</div>
                            <div class="valor">S/ {{ number_format($cuentaPagar->CXP_MontoAdelanto, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxp-resumen-box">
                            <div class="label">Abonado</div>
                            <div class="valor">S/ {{ number_format($cuentaPagar->CXP_MontoAbonado, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxp-resumen-box destacado">
                            <div class="label">Pendiente</div>
                            <div class="valor">S/ {{ number_format($cuentaPagar->CXP_MontoPendiente, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                @if ($cuentaPagar->CXP_TieneCuotas)
                    <div class="card cxp-card mb-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3">Plan de Cuotas</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Vencimiento</th>
                                            <th class="text-right">Programado</th>
                                            <th class="text-right">Abonado</th>
                                            <th>Estado</th>
                                            <th class="text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cuotas as $cuota)
                                            <tr>
                                                <td>{{ $cuota->CXPC_Numero }}</td>
                                                <td>{{ $cuota->CXPC_FechaVencimiento }}</td>
                                                <td class="text-right">S/ {{ number_format($cuota->CXPC_MontoProgramado, 2) }}</td>
                                                <td class="text-right">S/ {{ number_format($cuota->CXPC_MontoAbonado, 2) }}</td>
                                                <td>
                                                    @if ($cuota->CXPC_Estado == \App\Models\Tenant\CuentaPagarCuota::ESTADO_PAGADO)
                                                        <span class="cxp-badge cxp-badge-pagado">Pagado</span>
                                                    @else
                                                        <span class="cxp-badge cxp-badge-pendiente">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($cuota->CXPC_MontoAbonado > 0)
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            title="Anula los pagos de esta cuota y la deuda vuelve a ella"
                                                            onclick="anularPago('{{ tenant_url('tenant.compras.cuentaspagar.cuotas.anularpago', ['cuentaPagar' => $cuentaPagar->CXP_Id, 'cuota' => $cuota->CXPC_Id]) }}', 'Anular el pago de la cuota #{{ $cuota->CXPC_Numero }}', 'La cuota volverá a quedar pendiente por S/ {{ number_format($cuota->CXPC_MontoProgramado, 2) }}.')">
                                                            <i class="fas fa-undo"></i> Anular pago
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card cxp-card">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3">Historial de Abonos</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Método</th>
                                        <th class="text-right">Monto</th>
                                        <th>Cuota</th>
                                        <th class="text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($abonos as $abono)
                                        <tr>
                                            <td>{{ \Illuminate\Support\Carbon::parse($abono->CXPA_Fecha)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $abono->metodo }}</td>
                                            <td class="text-right">S/ {{ number_format($abono->CXPA_Monto, 2) }}</td>
                                            <td>{{ $abono->CXPC_Id ? '#' . $abono->CXPC_Id : 'Libre' }}</td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-outline-danger btn-sm" title="Anular este abono"
                                                    onclick="anularPago('{{ tenant_url('tenant.compras.cuentaspagar.abonos.anular', ['cuentaPagar' => $cuentaPagar->CXP_Id, 'abono' => $abono->CXPA_Id]) }}', 'Anular abono de S/ {{ number_format($abono->CXPA_Monto, 2) }}', 'La deuda vuelve a la cuenta{{ $abono->CXPC_Id ? ' y a la cuota que cubría' : '' }}.')">
                                                    <i class="fas fa-undo"></i> Anular
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">Todavía no hay abonos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($abonosAnulados->count())
                    <div class="card cxp-card mt-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3 text-muted">Abonos anulados</h6>
                            <div class="table-responsive">
                                <table class="table table-sm text-muted">
                                    <thead>
                                        <tr>
                                            <th>Abono</th>
                                            <th class="text-right">Monto</th>
                                            <th>Anulado</th>
                                            <th>Motivo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($abonosAnulados as $an)
                                            <tr>
                                                <td>{{ \Illuminate\Support\Carbon::parse($an->CXPA_Fecha)->format('d/m/Y H:i') }} · {{ $an->metodo }}{{ $an->CXPC_Id ? ' · cuota #' . $an->CXPC_Id : '' }}</td>
                                                <td class="text-right"><del>S/ {{ number_format($an->CXPA_Monto, 2) }}</del></td>
                                                <td>{{ \Illuminate\Support\Carbon::parse($an->CXPA_FechaAnulacion)->format('d/m/Y H:i') }}<br><small>{{ $an->anulado_por }}</small></td>
                                                <td>{{ $an->CXPA_MotivoAnulacion }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-5">
                <div class="card cxp-card">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3">Registrar Abono</h6>

                        @if ($cuentaPagar->CXP_Estado == \App\Models\Tenant\CuentaPagar::ESTADO_PAGADO)
                            <p class="text-muted mb-0">Esta cuenta ya está pagada por completo.</p>
                        @else
                            <div class="form-group">
                                <label>Monto (S/)</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="inputMontoAbono" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label>Método de Pago</label>
                                <select class="form-control" id="selectMetodoAbono">
                                    @foreach ($metodoPago as $mp)
                                        @if ($mp->MEP_Pago !== 'Mixto' && $mp->MEP_Pago !== 'Crédito')
                                            <option value="{{ $mp->MEP_Id }}">{{ $mp->MEP_Pago }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Observación (opcional)</label>
                                <input type="text" class="form-control" id="inputDescripcionAbono" maxlength="150">
                            </div>
                            <button type="button" class="btn btn-block" style="background:#6C3BFF;color:#fff;border-radius:10px;font-weight:700;" onclick="registrarAbono()">
                                <i class="fas fa-check-circle mr-1"></i> Registrar Abono
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        function anularPago(url, titulo, aviso) {
            Swal.fire({
                icon: 'warning',
                title: titulo,
                text: aviso,
                input: 'text',
                inputPlaceholder: 'Motivo de la anulación (obligatorio)',
                inputAttributes: { maxlength: 150 },
                showCancelButton: true,
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
                inputValidator: function(valor) {
                    if (!valor || !valor.trim()) return 'Escribe el motivo';
                }
            }).then(function(res) {
                if (!res.isConfirmed) return;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { motivo: res.value.trim(), _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(r) {
                        Swal.fire({ icon: 'success', title: 'Anulado', text: r.success }).then(function() { location.reload(); });
                    },
                    error: function(xhr) {
                        let d = xhr.responseJSON || {};
                        Swal.fire({ icon: 'error', title: 'No se pudo anular', text: d.error || d.message || 'Error inesperado.' });
                    }
                });
            });
        }

        function registrarAbono() {
            let monto = parseFloat($('#inputMontoAbono').val());

            if (!monto || monto <= 0) {
                Swal.fire({ icon: 'warning', title: 'Ingresa un monto válido' });
                return;
            }

            $.ajax({
                url: "{{ tenant_url('tenant.compras.cuentaspagar.abonos.store', ['cuentaPagar' => $cuentaPagar->CXP_Id]) }}",
                method: 'POST',
                data: {
                    monto: monto.toFixed(2),
                    metodo_pago_id: $('#selectMetodoAbono').val(),
                    descripcion: $('#inputDescripcionAbono').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Abono registrado',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let data = xhr.responseJSON || {};
                    let motivo = data.error
                        || (data.errors && Object.values(data.errors)[0] && Object.values(data.errors)[0][0])
                        || data.message
                        || 'No se pudo registrar el abono.';
                    Swal.fire({ icon: 'error', title: 'Error', text: motivo });
                }
            });
        }
    </script>
@endsection
