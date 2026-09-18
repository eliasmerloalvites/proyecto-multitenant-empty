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

        .codigo-referido-box {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            border-radius: 18px;
            padding: 20px;
            color: #fff;
            margin-bottom: 20px;
        }

        .codigo-referido-link {
            background: rgba(255, 255, 255, .15);
            border-radius: 10px;
            padding: 10px 14px;
            font-family: monospace;
            font-size: 13px;
            word-break: break-all;
            margin-top: 8px;
        }
    </style>

    <div class="col-lg-12 col-md-12 mb-12">
        <div class="mb-3">
            <div class="dashboard-title">Mi Panel de Vendedor</div>
            <p class="dashboard-subtitle">Resumen de tus clientes referidos y tus comisiones.</p>
        </div>

        {{-- LINK DE REFERIDO --}}

        <div class="codigo-referido-box">
            <div style="font-size:13px; opacity:.85; font-weight:600;">TU LINK DE REFERIDO</div>
            <div class="codigo-referido-link" id="linkReferido">{{ url('/crear-empresa?ref=' . $vendedor->codigo_referido) }}</div>
            <button type="button" class="btn btn-sm btn-light mt-2" onclick="copiarLinkReferido()">
                <i class="fas fa-copy mr-1"></i>Copiar link
            </button>
        </div>

        {{-- KPIs --}}

        <div class="row mb-2">

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-primary">
                    <div class="kpi-header">
                        <div class="kpi-title">Mis Clientes</div>
                        <div class="kpi-icon bg-primary-gradient"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="kpi-value">{{ $totalClientes }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-success">
                    <div class="kpi-header">
                        <div class="kpi-title">Clientes Activos</div>
                        <div class="kpi-icon bg-success-gradient"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <div class="kpi-value">{{ $clientesActivos }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-warning">
                    <div class="kpi-header">
                        <div class="kpi-title">Comisión de Este Mes</div>
                        <div class="kpi-icon bg-warning-gradient"><i class="fas fa-calendar-check"></i></div>
                    </div>
                    <div class="kpi-value">S/ {{ number_format($comisionMesActual, 2) }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="kpi-card kpi-primary">
                    <div class="kpi-header">
                        <div class="kpi-title">Comisión Acumulada</div>
                        <div class="kpi-icon bg-primary-gradient"><i class="fas fa-sack-dollar"></i></div>
                    </div>
                    <div class="kpi-value">S/ {{ number_format($comisionAcumulada, 2) }}</div>
                </div>
            </div>

        </div>

        {{-- CHART --}}

        <div class="row mb-1 align-items-stretch">

            <div class="col-lg-12 mb-4">
                <div class="dashboard-card h-100">
                    <div class="dashboard-card-title">Mi Comisión (últimos 6 meses)</div>
                    <div class="chart-container">
                        <canvas id="miComisionChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLAS --}}

        <div class="row align-items-stretch">

            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="dashboard-card-title mb-0">Mis Últimos Clientes</div>
                        <a href="{{ route('vendedor.clientes.index') }}" class="text-primary" style="font-size:12px;">
                            Ver todos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @forelse ($misUltimosClientes as $cliente)
                        <div class="vencimiento-item">
                            <div>
                                <div class="font-weight-bold">{{ $cliente->razon_social }}</div>
                                <div class="text-muted" style="font-size:12px;">
                                    Referido el {{ \Carbon\Carbon::parse($cliente->referido_en)->translatedFormat('d M Y') }} · {{ $cliente->porcentaje_congelado }}%
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
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p class="mb-0">Aún no has registrado ningún cliente.</p>
                        </div>
                    @endforelse

                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="dashboard-card-title mb-0">Comisiones Recientes</div>
                        <a href="{{ route('vendedor.comisiones.index') }}" class="text-primary" style="font-size:12px;">
                            Ver todas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @forelse ($comisionesRecientes as $fila)
                        <div class="vencimiento-item">
                            <div>
                                <div class="font-weight-bold">{{ $fila->periodo }}</div>
                                <div class="text-muted" style="font-size:12px;">
                                    {{ $fila->clientes_pagaron }} {{ $fila->clientes_pagaron == 1 ? 'cliente pagó' : 'clientes pagaron' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold">S/ {{ number_format($fila->total_comision, 2) }}</div>
                                @if ($fila->liquidacion_id)
                                    <span class="badge-soft-success">Liquidado</span>
                                @else
                                    <span class="badge-soft-warning">Pendiente</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-sack-dollar fa-2x mb-2"></i>
                            <p class="mb-0">Aún no tienes comisiones generadas.</p>
                        </div>
                    @endforelse

                </div>
            </div>

        </div>
    </div>

@endsection

@section('script')

    <script>
        function copiarLinkReferido() {
            const link = document.getElementById('linkReferido').innerText;
            navigator.clipboard.writeText(link).then(function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Link copiado',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        }

        new Chart(document.getElementById('miComisionChart'), {
            type: 'bar',
            data: {
                labels: @json($labelsMeses),
                datasets: [{
                    label: 'Comisión (S/)',
                    data: @json($serieComision),
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
                            callback: function(value) {
                                return 'S/ ' + value;
                            }
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
    </script>

@endsection
