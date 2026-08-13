@extends('central.layout.appAdminLte')
@section('titulo', 'Inicio')
@section('contenido')

    <style>
        .dashboard-title {
            font-size: 24px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 4px;
        }

        .dashboard-subtitle {
            color: #64748B;
            margin-bottom: 20px;
        }

        .kpi-card {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            padding: 18px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
            height: 100%;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .kpi-primary::before {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
        }

        .kpi-success::before {
            background: linear-gradient(135deg, #22C55E, #16A34A);
        }

        .kpi-warning::before {
            background: linear-gradient(135deg, #F59E0B, #F97316);
        }

        .kpi-danger::before {
            background: linear-gradient(135deg, #EF4444, #F43F5E);
        }

        .kpi-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .kpi-title {
            color: #64748B;
            font-size: 13px;
            font-weight: 600;
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
        }

        .bg-success-gradient {
            background: linear-gradient(135deg, #22C55E, #16A34A);
        }

        .bg-warning-gradient {
            background: linear-gradient(135deg, #F59E0B, #F97316);
        }

        .bg-danger-gradient {
            background: linear-gradient(135deg, #EF4444, #F43F5E);
        }

        .kpi-value {
            font-size: 26px;
            font-weight: 700;
            color: #0F172A;
            line-height: 1;
        }

        .dashboard-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
            padding: 20px;
            margin-bottom: 20px;
        }

        .dashboard-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 16px;
        }

        .chart-container {
            position: relative;
            height: 280px;
        }

        .donut-container {
            position: relative;
            height: 240px;
        }

        .empty-state {
            color: #94A3B8;
            text-align: center;
            padding: 24px 10px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .badge-soft-success {
            background: rgba(34, 197, 94, .12);
            color: #16A34A;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-soft-warning {
            background: rgba(245, 158, 11, .12);
            color: #D97706;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-soft-danger {
            background: rgba(239, 68, 68, .12);
            color: #DC2626;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
        }

        .vencimiento-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #F1F5F9;
        }

        .vencimiento-item:last-child {
            border-bottom: none;
        }
    </style>
    <div class="col-lg-12 col-md-12 mb-12">
        <div class="mb-3">
            <div class="dashboard-title">Panel de administración</div>
            <p class="dashboard-subtitle">Resumen general de la plataforma Kael Tech.</p>
        </div>

        {{-- KPIs --}}

        <div class="row mb-2">

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-primary">
                    <div class="kpi-header">
                        <div class="kpi-title">Tenants Totales</div>
                        <div class="kpi-icon bg-primary-gradient"><i class="fas fa-building"></i></div>
                    </div>
                    <div class="kpi-value">{{ $totalTenants }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-success">
                    <div class="kpi-header">
                        <div class="kpi-title">Tenants Activos</div>
                        <div class="kpi-icon bg-success-gradient"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <div class="kpi-value">{{ $tenantsActivos }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-warning">
                    <div class="kpi-header">
                        <div class="kpi-title">Suspendidos / Cancelados</div>
                        <div class="kpi-icon bg-warning-gradient"><i class="fas fa-exclamation-triangle"></i></div>
                    </div>
                    <div class="kpi-value">{{ $tenantsSuspendidos + $tenantsCancelados }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-danger">
                    <div class="kpi-header">
                        <div class="kpi-title">MRR Estimado</div>
                        <div class="kpi-icon bg-danger-gradient"><i class="fas fa-sack-dollar"></i></div>
                    </div>
                    <div class="kpi-value">S/ {{ number_format($mrrEstimado, 2) }}</div>
                </div>
            </div>

        </div>

        {{-- CHARTS --}}

        <div class="row mb-1 align-items-stretch">

            <div class="col-lg-8 mb-4">
                <div class="dashboard-card h-100">
                    <div class="dashboard-card-title">Nuevos Tenants (últimos 6 meses)</div>
                    <div class="chart-container">
                        <canvas id="nuevosTenantsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="dashboard-card h-100">
                    <div class="dashboard-card-title">Tenants por Plan</div>

                    @if (array_sum($tenantsPorPlanData) == 0)
                        <div class="empty-state">
                            <i class="fas fa-building fa-2x mb-2"></i>
                            <p class="mb-0">Aún no hay tenants activos.</p>
                        </div>
                    @else
                        <div class="donut-container">
                            <canvas id="tenantsPorPlanChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- TABLAS --}}

        <div class="row align-items-stretch">

            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="dashboard-card-title mb-0">Cobros de la Semana</div>
                        <a href="{{ route('admin.cobros.index') }}" class="text-primary" style="font-size:12px;">
                            Ver todos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @forelse ($proximosVencimientos as $cliente)
                        <div class="vencimiento-item">
                            <div>
                                <div class="font-weight-bold">{{ $cliente->razon_social }}</div>
                                <div class="text-muted" style="font-size:12px;">
                                    Cobro el día {{ $cliente->billing_day }} de cada mes
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold">
                                    {{ $cliente->fecha_cobro->translatedFormat('d M Y') }}
                                </div>
                                @if ($cliente->estado_ciclo === 'vencido')
                                    <span class="badge-soft-danger">Vencido</span>
                                @else
                                    @php $diasRestantes = (int) $hoy->copy()->startOfDay()->diffInDays($cliente->fecha_cobro, false); @endphp
                                    <span class="badge-soft-warning">
                                        {{ $diasRestantes <= 0 ? 'Vence hoy' : 'En ' . $diasRestantes . ($diasRestantes === 1 ? ' día' : ' días') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <p class="mb-0">No hay cobros pendientes esta semana.</p>
                        </div>
                    @endforelse

                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="dashboard-card-title">Últimos Clientes Registrados</div>

                    @forelse ($ultimosClientes as $cliente)
                        @php $t = $tenantsIndex->get($cliente->tenant_id); @endphp
                        <div class="vencimiento-item">
                            <div>
                                <div class="font-weight-bold">{{ $cliente->razon_social }}</div>
                                <div class="text-muted" style="font-size:12px;">
                                    {{ $t->tipo_negocio ?? '—' }} · Plan {{ ucfirst($t->plan ?? '—') }}
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $estadoColor = match ($cliente->status) {
                                        'activo' => 'success',
                                        'suspendido' => 'warning',
                                        default => 'danger',
                                    };
                                @endphp
                                <span class="badge-soft-{{ $estadoColor }}">{{ ucfirst($cliente->status) }}</span>
                                <div class="text-muted" style="font-size:12px; margin-top:4px;">
                                    {{ $cliente->created_at?->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p class="mb-0">Aún no hay clientes registrados.</p>
                        </div>
                    @endforelse

                </div>
            </div>

        </div>
    </div>


@endsection

@section('script')

    <script>
        new Chart(document.getElementById('nuevosTenantsChart'), {
            type: 'bar',
            data: {
                labels: @json($labelsMeses),
                datasets: [{
                    label: 'Nuevos tenants',
                    data: @json($serieNuevosTenants),
                    backgroundColor: 'rgba(37,99,235,.65)',
                    borderRadius: 8,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        @if (array_sum($tenantsPorPlanData) > 0)
            new Chart(document.getElementById('tenantsPorPlanChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($tenantsPorPlanLabels),
                    datasets: [{
                        data: @json($tenantsPorPlanData),
                        backgroundColor: ['#2563EB', '#22C55E', '#F59E0B', '#7C3AED'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        @endif
    </script>

@endsection
