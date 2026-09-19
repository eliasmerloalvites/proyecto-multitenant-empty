@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Detalle de Cuenta por Cobrar')
@section('contenido')

    <style>
        .cxc-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .cxc-badge-pendiente { background: #EDE9FE; color: #6C3BFF; }
        .cxc-badge-pagado { background: #DCFCE7; color: #16A34A; }

        .cxc-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .cxc-resumen-box {
            border-radius: 14px;
            background: #F9F7FF;
            border: 1px solid #EDE9FE;
            padding: 16px;
            text-align: center;
        }

        .cxc-resumen-box .valor {
            font-size: 22px;
            font-weight: 800;
            color: #1F2937;
        }

        .cxc-resumen-box.destacado {
            background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
            border: none;
        }

        .cxc-resumen-box.destacado .valor,
        .cxc-resumen-box.destacado .label {
            color: #fff;
        }

        .cxc-resumen-box .label {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
    </style>

    <div class="col-12">
        <div class="card cxc-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar mr-1" style="color:#6C3BFF;"></i>
                        CUENTA POR COBRAR — VENTA #{{ $cuentaCobrar->VEN_Id }}
                    </h5>
                    <a href="{{ tenant_url('tenant.ventas.cuentascobrar.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Cliente:</strong> {{ $venta->CLI_Nombre ?? '-' }}
                        <div class="text-muted" style="font-size:12px;">{{ $venta->CLI_NumDocumento ?? '' }}</div>
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha de emisión:</strong> {{ $cuentaCobrar->CXC_FechaEmision }}
                    </div>
                    <div class="col-md-4">
                        <strong>Estado:</strong>
                        @if ($cuentaCobrar->CXC_Estado == \App\Models\Tenant\CuentaCobrar::ESTADO_PAGADO)
                            <span class="cxc-badge cxc-badge-pagado">Pagado</span>
                        @else
                            <span class="cxc-badge cxc-badge-pendiente">Pendiente</span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxc-resumen-box">
                            <div class="label">Total</div>
                            <div class="valor">S/ {{ number_format($cuentaCobrar->CXC_MontoTotal, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxc-resumen-box">
                            <div class="label">Adelanto</div>
                            <div class="valor">S/ {{ number_format($cuentaCobrar->CXC_MontoAdelanto, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxc-resumen-box">
                            <div class="label">Abonado</div>
                            <div class="valor">S/ {{ number_format($cuentaCobrar->CXC_MontoAbonado, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="cxc-resumen-box destacado">
                            <div class="label">Pendiente</div>
                            <div class="valor">S/ {{ number_format($cuentaCobrar->CXC_MontoPendiente, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                @if ($cuentaCobrar->CXC_TieneCuotas)
                    <div class="card cxc-card mb-3">
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cuotas as $cuota)
                                            <tr>
                                                <td>{{ $cuota->CCC_Numero }}</td>
                                                <td>{{ $cuota->CCC_FechaVencimiento }}</td>
                                                <td class="text-right">S/ {{ number_format($cuota->CCC_MontoProgramado, 2) }}</td>
                                                <td class="text-right">S/ {{ number_format($cuota->CCC_MontoAbonado, 2) }}</td>
                                                <td>
                                                    @if ($cuota->CCC_Estado == \App\Models\Tenant\CuentaCobrarCuota::ESTADO_PAGADO)
                                                        <span class="cxc-badge cxc-badge-pagado">Pagado</span>
                                                    @else
                                                        <span class="cxc-badge cxc-badge-pendiente">Pendiente</span>
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

                <div class="card cxc-card">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($abonos as $abono)
                                        <tr>
                                            <td>{{ \Illuminate\Support\Carbon::parse($abono->CCA_Fecha)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $abono->metodo }}</td>
                                            <td class="text-right">S/ {{ number_format($abono->CCA_Monto, 2) }}</td>
                                            <td>{{ $abono->CCC_Id ? '#' . $abono->CCC_Id : 'Libre' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Todavía no hay abonos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card cxc-card">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3">Registrar Abono</h6>

                        @if ($cuentaCobrar->CXC_Estado == \App\Models\Tenant\CuentaCobrar::ESTADO_PAGADO)
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
        function registrarAbono() {
            let monto = parseFloat($('#inputMontoAbono').val());

            if (!monto || monto <= 0) {
                Swal.fire({ icon: 'warning', title: 'Ingresa un monto válido' });
                return;
            }

            $.ajax({
                url: "{{ tenant_url('tenant.ventas.cuentascobrar.abonos.store', ['cuentaCobrar' => $cuentaCobrar->CXC_Id]) }}",
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
