@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Mi Facturación')
@section('contenido')

    @php
        $estilos = [
            'en_prueba' => ['label' => 'EN PRUEBA', 'class' => 'badge-info'],
            'pagado' => ['label' => 'PAGADO', 'class' => 'badge-success'],
            'vencido' => ['label' => 'VENCIDO', 'class' => 'badge-danger'],
            'por_vencer' => ['label' => 'POR VENCER', 'class' => 'badge-warning'],
            'pendiente' => ['label' => 'PENDIENTE', 'class' => 'badge-secondary'],
        ];
        $estilo = $estilos[$estadoCiclo] ?? $estilos['pendiente'];
    @endphp

    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title d-flex align-items-center justify-content-between">
                    Mi Plan
                    <span class="badge {{ $estilo['class'] }}">{{ $estilo['label'] }}</span>
                </h5>

                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:45%">Plan</td>
                        <td class="font-weight-bold">{{ strtoupper(tenant('plan')) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Monto de tu ciclo</td>
                        <td class="font-weight-bold">S/ {{ number_format($montoEsperado, 2) }}</td>
                    </tr>
                    @if ($estadoCiclo !== 'en_prueba')
                        <tr>
                            <td class="text-muted">Fecha de cobro</td>
                            <td class="font-weight-bold">{{ $fechaCobro->translatedFormat('d \d\e F') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-muted">Fin de tu prueba gratis</td>
                            <td class="font-weight-bold">{{ $client->trial_ends_at?->translatedFormat('d \d\e F') ?? '—' }}</td>
                        </tr>
                    @endif
                </table>

                @if (in_array($estadoCiclo, ['por_vencer', 'vencido']))
                    <a href="{{ tenant_url('tenant.facturacion.pagar') }}" class="btn btn-primary btn-block mt-3">
                        <i class="fas fa-credit-card mr-1"></i> Pagar ahora
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Historial de pagos</h5>

                @if ($historial->isEmpty())
                    <p class="text-muted mb-0">Todavía no tienes pagos registrados.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historial as $pago)
                                    <tr>
                                        <td>{{ $pago->periodo }}</td>
                                        <td>S/ {{ number_format($pago->monto, 2) }}</td>
                                        <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                        <td class="text-capitalize">{{ str_replace('_', ' ', $pago->metodo_pago) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
