@extends('tenant_tallermoto.layout.appAdminLte')
@section('titulo','Inicio')

@section('contenido')

<style>

    .dashboard-wrapper{
      width:100%;
      max-width:100%;
      padding:20px 24px;
      margin:0;
    }

    .dashboard-title{
        font-size:28px;
        font-weight:700;
        color:#1E293B;
        margin-bottom:5px;
    }

    .dashboard-subtitle{
        color:#64748B;
        margin-bottom:25px;
    }

    /* ======================================================
       KPI CARDS
    ====================================================== */

    .kpi-card{
        position:relative;
        overflow:hidden;

        border-radius:20px;

        padding:22px;

        background:#fff;

        box-shadow:0 10px 30px rgba(15,23,42,.06);

        transition:all .25s ease;

        height:100%;
    }

    /* Resumen del mes (utilidad y flujo de caja) */
    .resumen-card{ background:#fff; border-radius:20px; padding:22px; box-shadow:0 10px 30px rgba(15,23,42,.06); height:100%; display:flex; flex-direction:column; gap:6px; }
    .resumen-head{ display:flex; justify-content:space-between; gap:12px; margin-bottom:8px; }
    .resumen-titulo{ font-weight:700; font-size:16px; color:#0F172A; }
    .resumen-ayuda{ font-size:12px; color:#64748B; margin-top:2px; max-width:42ch; }
    .resumen-icono{ width:42px; height:42px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
    .resumen-linea{ display:flex; justify-content:space-between; font-size:14px; color:#334155; font-variant-numeric:tabular-nums; }
    .resumen-resta{ color:#64748B; }
    .resumen-sub{ font-weight:600; color:#0F172A; border-top:1px dashed #E2E8F0; padding-top:6px; }
    .resumen-detalle{ font-size:11.5px; color:#94A3B8; margin:-2px 0 2px 10px; }
    .resumen-total{ display:flex; justify-content:space-between; align-items:baseline; margin-top:6px; padding:10px 12px; border-radius:12px; background:#F0FDF4; color:#166534; font-weight:700; font-size:18px; font-variant-numeric:tabular-nums; }
    .resumen-total.resumen-negativo{ background:#FEF2F2; color:#B91C1C; }
    .resumen-pie{ display:flex; flex-wrap:wrap; gap:6px 16px; font-size:12px; color:#64748B; margin-top:auto; padding-top:8px; }
    .resumen-pie a{ margin-left:auto; font-weight:600; }

    .kpi-card:hover{
        transform:translateY(-4px);
        box-shadow:0 20px 40px rgba(15,23,42,.10);
    }

    .kpi-card::before{
        content:'';
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:5px;
    }

    .kpi-primary::before{
        background:linear-gradient(135deg,#2563EB,#7C3AED);
    }

    .kpi-success::before{
        background:linear-gradient(135deg,#22C55E,#16A34A);
    }

    .kpi-warning::before{
        background:linear-gradient(135deg,#F59E0B,#F97316);
    }

    .kpi-danger::before{
        background:linear-gradient(135deg,#EF4444,#F43F5E);
    }

    .kpi-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:15px;
    }

    .kpi-title{
        color:#64748B;
        font-size:14px;
        font-weight:600;
    }

    .kpi-icon{
        width:50px;
        height:50px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        color:white;
        font-size:18px;
    }

    .bg-primary-gradient{
        background:linear-gradient(135deg,#2563EB,#7C3AED);
    }

    .bg-success-gradient{
        background:linear-gradient(135deg,#22C55E,#16A34A);
    }

    .bg-warning-gradient{
        background:linear-gradient(135deg,#F59E0B,#F97316);
    }

    .bg-danger-gradient{
        background:linear-gradient(135deg,#EF4444,#F43F5E);
    }

    .kpi-value{
        font-size:30px;
        font-weight:700;
        color:#0F172A;
        line-height:1;
    }

    .kpi-growth{
        margin-top:12px;
        display:inline-flex;
        align-items:center;
        gap:5px;
        font-size:13px;
        font-weight:600;
        color:#22C55E;
        background:rgba(34,197,94,.08);
        padding:5px 10px;
        border-radius:30px;
    }

    /* ======================================================
       CARDS
    ====================================================== */

    .dashboard-card{
        background:#fff;
        border-radius:22px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        padding:25px;
        margin-bottom:25px;
    }

    .dashboard-card-title{
        font-size:18px;
        font-weight:700;
        color:#0F172A;
        margin-bottom:20px;
    }

    /* ======================================================
       CHARTS
    ====================================================== */

    .chart-container{
        position:relative;
        height:380px;
    }

    .donut-container{
        position:relative;
        height:320px;
    }

    /* ======================================================
       TABLE
    ====================================================== */

    .dashboard-table thead{
        background:#F8FAFC;
    }

    .dashboard-table thead th{
        border:none;
        color:#64748B;
        font-size:13px;
        font-weight:700;
    }

    .dashboard-table tbody td{
        vertical-align:middle;
        border-top:1px solid #F1F5F9;
    }

    .dashboard-table tbody tr:hover{
        background:rgba(37,99,235,.03);
    }

    /* ======================================================
       PRODUCTS
    ====================================================== */

    .top-product{
        display:flex;
        align-items:center;
        gap:15px;
        margin-bottom:20px;
    }

    .top-product img{
        width:55px;
        height:55px;
        border-radius:14px;
        object-fit:cover;
    }

    .top-product-name{
        font-weight:600;
        color:#0F172A;
    }

    .top-product-sales{
        color:#64748B;
        font-size:13px;
    }

    .progress{
        height:8px;
        border-radius:20px;
        background:#E2E8F0;
        margin-top:8px;
    }

    .progress-bar{
        border-radius:20px;
    }

    /* ======================================================
       BADGES
    ====================================================== */

    .badge-soft-success{
        background:rgba(34,197,94,.12);
        color:#16A34A;
        padding:6px 10px;
        border-radius:30px;
        font-size:12px;
    }

    .badge-soft-danger{
        background:rgba(239,68,68,.12);
        color:#DC2626;
        padding:6px 10px;
        border-radius:30px;
        font-size:12px;
    }

    .badge-soft-warning{
        background:rgba(245,158,11,.12);
        color:#D97706;
        padding:6px 10px;
        border-radius:30px;
        font-size:12px;
    }

    .kpi-growth-negative{
        color:#EF4444;
        background:rgba(239,68,68,.08);
    }

    .empty-state{
        color:#94A3B8;
        text-align:center;
        padding:30px 10px;
    }

    .section-subtitle{
        font-size:15px;
        font-weight:700;
        color:#1E293B;
        margin:10px 0 15px;
    }

</style>

<div class="dashboard-wrapper">

@if (($bahiasActivas ?? 0) == 0)
<div class="mb-4" style="background:linear-gradient(135deg,#E52320,#C81B18); border-radius:14px; padding:1.5rem 1.8rem; color:#fff; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
    <div>
        <h5 style="font-weight:800; margin-bottom:.3rem;"><i class="fas fa-wand-magic-sparkles mr-1"></i> Tu taller todavía no tiene bahías ni horarios</h5>
        <p style="opacity:.92; margin-bottom:0;">Configúralos en 30 segundos para poder empezar a recibir reservas.</p>
    </div>
    <a href="{{ tenant_url('tenant.configuracion.asistente.index') }}" class="btn btn-light font-weight-bold">
        Configurar ahora <i class="fas fa-arrow-right ml-1"></i>
    </a>
</div>
@endif

{{-- HEADER --}}

<div class="mb-4">

    <h1 class="dashboard-title">
        Bienvenido 👋
    </h1>

    <p class="dashboard-subtitle">
        Aquí tienes el resumen general de tu negocio hoy.
    </p>

</div>

{{-- KPI CARDS (Ventas/Inventario/Compras: solo Plus/Empresarial, y solo para quien puede ver reportes financieros) --}}

@if($mostrarVentas && auth()->user()->hasAnyRole(['Admin', 'Gerente']))
<div class="row mb-4">

    <div class="col-lg-3 col-md-6 mb-3">

        <div class="kpi-card kpi-primary">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Ventas del Día</div>
                </div>

                <div class="kpi-icon bg-primary-gradient">
                    <i class="fas fa-chart-line"></i>
                </div>

            </div>

            <div class="kpi-value">S/ {{ number_format($ventasHoy, 2) }}</div>

            <div class="kpi-growth {{ $crecimientoVentas < 0 ? 'kpi-growth-negative' : '' }}">
                <i class="fas {{ $crecimientoVentas >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $crecimientoVentas >= 0 ? '+' : '' }}{{ $crecimientoVentas }}% vs ayer
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-3">

        <div class="kpi-card kpi-success">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Ingresos Mensuales</div>
                </div>

                <div class="kpi-icon bg-success-gradient">
                    <i class="fas fa-wallet"></i>
                </div>

            </div>

            <div class="kpi-value">S/ {{ number_format($ingresosMes, 2) }}</div>

            <div class="kpi-growth {{ $crecimientoIngresos < 0 ? 'kpi-growth-negative' : '' }}">
                <i class="fas {{ $crecimientoIngresos >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $crecimientoIngresos >= 0 ? '+' : '' }}{{ $crecimientoIngresos }}% vs mes anterior
            </div>

            {{-- Vendido (arriba) no es lo mismo que cobrado: una venta al
                 credito se cobra despues, como abono. --}}
            <div class="mt-2" style="font-size: 12px; line-height: 1.5; opacity: .9;">
                <div title="Ventas al contado + abonos de cuentas por cobrar + adelantos de bahía del mes">
                    <i class="fas fa-hand-holding-usd mr-1"></i>Cobrado: <strong>S/ {{ number_format($cobradoMes, 2) }}</strong>
                    @if ($abonosMes > 0)
                        <span class="text-muted">(incl. S/ {{ number_format($abonosMes, 2) }} de abonos)</span>
                    @endif
                </div>
                @if ($porCobrar > 0)
                    <div><i class="fas fa-file-invoice mr-1"></i>Por cobrar: <strong>S/ {{ number_format($porCobrar, 2) }}</strong></div>
                @endif
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-3">

        <div class="kpi-card kpi-warning">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Egresos del Mes</div>
                </div>

                <div class="kpi-icon bg-warning-gradient">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>

            </div>

            <div class="kpi-value">S/ {{ number_format($egresosMes, 2) }}</div>

            <div class="kpi-growth {{ $crecimientoEgresos > 0 ? 'kpi-growth-negative' : '' }}">
                <i class="fas {{ $crecimientoEgresos > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $crecimientoEgresos >= 0 ? '+' : '' }}{{ $crecimientoEgresos }}% vs mes anterior
            </div>

            {{-- Mismo total que "Egresos totales" en Reportes > Compras y Gastos. --}}
            <div class="mt-2" style="font-size: 12px; line-height: 1.5; opacity: .9;">
                <div><i class="fas fa-file-invoice-dollar mr-1"></i>Gastos: <strong>S/ {{ number_format($gastosMes, 2) }}</strong></div>
                <div title="Compras registradas en el mes, incluidas las al crédito"><i class="fas fa-dolly mr-1"></i>Compras: <strong>S/ {{ number_format($comprasMes, 2) }}</strong></div>
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-3">

        <div class="kpi-card kpi-danger">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Stock Bajo</div>
                </div>

                <div class="kpi-icon bg-danger-gradient">
                    <i class="fas fa-box-open"></i>
                </div>

            </div>

            <div class="kpi-value">{{ $stockBajo }}</div>

            <div class="kpi-growth {{ $stockBajo > 0 ? 'kpi-growth-negative' : '' }}">
                <i class="fas {{ $stockBajo > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                {{ $stockBajo > 0 ? 'Atención' : 'Todo en orden' }}
            </div>

        </div>

    </div>

</div>

{{-- RESUMEN DEL MES: utilidad (devengado) y flujo de caja, lado a lado --}}
<div class="section-subtitle">
    <i class="fas fa-chart-pie mr-1"></i>
    Resumen de {{ $nombreMes }}
</div>

<div class="row mb-4">

    {{-- UTILIDAD --}}
    <div class="col-lg-6 mb-3">
        <div class="resumen-card">
            <div class="resumen-head">
                <div>
                    <div class="resumen-titulo">Utilidad del mes</div>
                    <div class="resumen-ayuda">¿Gané con lo que vendí? Cuenta cada venta en su fecha, aunque sea al crédito.</div>
                </div>
                <span class="resumen-icono" style="background:#EEF2FF;color:#4338CA;"><i class="fas fa-chart-line"></i></span>
            </div>

            <div class="resumen-linea"><span>Vendido</span><span>S/ {{ number_format($utilidadMes['ingresos'], 2) }}</span></div>
            <div class="resumen-linea resumen-resta"><span>− Costo de lo vendido</span><span>S/ {{ number_format($utilidadMes['costo'], 2) }}</span></div>
            <div class="resumen-linea resumen-sub"><span>= Utilidad bruta</span><span>S/ {{ number_format($utilidadMes['utilidadBruta'], 2) }}</span></div>
            <div class="resumen-linea resumen-resta"><span>− Gastos</span><span>S/ {{ number_format($utilidadMes['gastos'], 2) }}</span></div>

            <div class="resumen-total {{ $utilidadMes['utilidadNeta'] < 0 ? 'resumen-negativo' : '' }}">
                <span>Utilidad neta</span>
                <span>S/ {{ number_format($utilidadMes['utilidadNeta'], 2) }}</span>
            </div>

            <div class="resumen-pie">
                <span>Margen <strong>{{ number_format($utilidadMes['margen'], 1) }}%</strong></span>
                @if (!is_null($utilidadMes['variacion']))
                    <span class="{{ $utilidadMes['variacion'] < 0 ? 'text-danger' : 'text-success' }}">
                        <i class="fas {{ $utilidadMes['variacion'] < 0 ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                        {{ $utilidadMes['variacion'] >= 0 ? '+' : '' }}{{ $utilidadMes['variacion'] }}% vs mes anterior
                    </span>
                @endif
                @can('tenant.reportes.rentabilidad')
                    <a href="{{ route('tenant.reportes.rentabilidad') }}">Ver rentabilidad <i class="fas fa-angle-right"></i></a>
                @endcan
            </div>
        </div>
    </div>

    {{-- FLUJO DE CAJA --}}
    <div class="col-lg-6 mb-3">
        <div class="resumen-card">
            <div class="resumen-head">
                <div>
                    <div class="resumen-titulo">Flujo de caja del mes</div>
                    <div class="resumen-ayuda">¿Cuánta plata entró y salió de verdad? Lo al crédito cuenta cuando se cobra o se paga.</div>
                </div>
                <span class="resumen-icono" style="background:#ECFDF5;color:#047857;"><i class="fas fa-cash-register"></i></span>
            </div>

            <div class="resumen-linea"><span>Cobrado</span><span>S/ {{ number_format($flujoCajaMes['cobrado'], 2) }}</span></div>
            <div class="resumen-detalle">
                Ventas S/ {{ number_format($flujoCajaMes['ventas'], 2) }}
                · Abonos S/ {{ number_format($flujoCajaMes['abonos'], 2) }}
                @if ($flujoCajaMes['adelantos'] > 0) · Adelantos S/ {{ number_format($flujoCajaMes['adelantos'], 2) }} @endif
            </div>
            <div class="resumen-linea resumen-resta"><span>− Pagado</span><span>S/ {{ number_format($flujoCajaMes['pagado'], 2) }}</span></div>
            <div class="resumen-detalle">
                Compras S/ {{ number_format($flujoCajaMes['compras'], 2) }}
                · Proveedores S/ {{ number_format($flujoCajaMes['pagosProveedor'], 2) }}
                · Gastos S/ {{ number_format($flujoCajaMes['gastos'], 2) }}
            </div>

            <div class="resumen-total {{ $flujoCajaMes['neto'] < 0 ? 'resumen-negativo' : '' }}">
                <span>Neto en caja</span>
                <span>S/ {{ number_format($flujoCajaMes['neto'], 2) }}</span>
            </div>

            <div class="resumen-pie">
                <span title="Saldo pendiente de cuentas por cobrar">Te deben <strong>S/ {{ number_format($flujoCajaMes['porCobrar'], 2) }}</strong></span>
                <span title="Saldo pendiente de cuentas por pagar">Debes a proveedores <strong>S/ {{ number_format($flujoCajaMes['debesProveedores'], 2) }}</strong></span>
                @can('tenant.reportes.caja')
                    <a href="{{ route('tenant.reportes.caja') }}">Ver por método <i class="fas fa-angle-right"></i></a>
                @endcan
            </div>
        </div>
    </div>

</div>
@endif

{{-- KPI TALLER --}}

<div class="section-subtitle">
    <i class="fas fa-motorcycle mr-1"></i>
    Taller
</div>

<div class="row mb-4">

    @can('tenant.reservaciones.administracion.index')
    <div class="col-lg-4 col-md-6 mb-3">

        <div class="kpi-card kpi-primary">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Reservas de Hoy</div>
                </div>

                <div class="kpi-icon bg-primary-gradient">
                    <i class="fas fa-calendar-check"></i>
                </div>

            </div>

            <div class="kpi-value">{{ $reservasHoy }}</div>

        </div>

    </div>
    @endcan

    <div class="col-lg-4 col-md-6 mb-3">

        <div class="kpi-card kpi-warning">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Mantenimientos Pendientes</div>
                </div>

                <div class="kpi-icon bg-warning-gradient">
                    <i class="fas fa-tools"></i>
                </div>

            </div>

            <div class="kpi-value">{{ $mantenimientosPendientes }}</div>

        </div>

    </div>

    @can('tenant.reservaciones.administracion.index')
    <div class="col-lg-4 col-md-6 mb-3">

        <div class="kpi-card kpi-success">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Bahías Activas</div>
                </div>

                <div class="kpi-icon bg-success-gradient">
                    <i class="fas fa-warehouse"></i>
                </div>

            </div>

            <div class="kpi-value">{{ $bahiasActivas }}</div>

        </div>

    </div>
    @endcan

</div>

{{-- REPORTES DE RESERVAS Y MANTENIMIENTOS (Taller: todos los planes) --}}

<div class="row mb-4">

    @can('tenant.reservaciones.administracion.index')
    <div class="col-lg-7 mb-3">

        <div class="dashboard-card mb-0" style="height:100%;">

            <div class="dashboard-card-title">
                Reservas de los Últimos 7 Días
            </div>

            <div class="chart-container" style="height:280px;">
                <canvas id="reservasChart"></canvas>
            </div>

        </div>

    </div>
    @endcan

    <div class="col-lg-5 mb-3">

        <div class="dashboard-card mb-0" style="height:100%;">

            <div class="dashboard-card-title">
                Mantenimientos por Estado
            </div>

            @if($mantenimientosPendientes + $mantenimientosAprobados + $mantenimientosObservados == 0)
                <div class="empty-state">
                    <i class="fas fa-tools fa-2x mb-2"></i>
                    <p class="mb-0">Aún no hay mantenimientos registrados.</p>
                </div>
            @else
                <div class="donut-container" style="height:280px;">
                    <canvas id="mantenimientoEstadoChart"></canvas>
                </div>
            @endif

        </div>

    </div>

</div>

<div class="row mb-4">

    <div class="col-lg-7 mb-3">

        <div class="dashboard-card mb-0" style="height:100%;">

            <div class="dashboard-card-title">
                Mantenimientos por Tipo
            </div>

            <div class="chart-container" style="height:280px;">
                <canvas id="mantenimientoTipoChart"></canvas>
            </div>

        </div>

    </div>

    @can('tenant.reservaciones.administracion.index')
    <div class="col-lg-5 mb-3">

        <div class="dashboard-card mb-0" style="height:100%;">

            <div class="dashboard-card-title">
                Próximas Reservas
            </div>

            @forelse($proximasReservas as $reserva)
                <div class="top-product" style="align-items:flex-start;">

                    <div class="kpi-icon bg-primary-gradient" style="width:44px;height:44px;font-size:15px;flex-shrink:0;">
                        <i class="fas fa-motorcycle"></i>
                    </div>

                    <div class="flex-grow-1">

                        <div class="top-product-name">{{ $reserva->RES_Cliente }}</div>

                        <div class="top-product-sales">
                            {{ \Carbon\Carbon::parse($reserva->RES_FechaProgramada)->translatedFormat('d M Y') }}
                            &middot; {{ $reserva->TUR_Nombre }}
                            &middot; {{ $reserva->BAH_Nombre }}
                        </div>

                    </div>

                    <span class="badge-soft-{{ $reserva->RES_State === 'APROBADO' ? 'success' : 'warning' }}">
                        {{ $reserva->RES_State === 'APROBADO' ? 'Aprobado' : 'Pendiente' }}
                    </span>

                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <p class="mb-0">No hay reservas próximas.</p>
                </div>
            @endforelse

        </div>

    </div>
    @endcan

</div>

{{-- CHARTS (Ventas/Inventario/Compras: solo Plus/Empresarial, y solo para quien puede ver reportes financieros) --}}

@if($mostrarVentas && auth()->user()->hasAnyRole(['Admin', 'Gerente']))
<div class="row">

    <div class="col-lg-8">

        <div class="dashboard-card">

            <div class="dashboard-card-title">
                Ventas vs Gastos Mensuales
            </div>

            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="dashboard-card">

            <div class="dashboard-card-title">
                Cobros por Método (mes actual)
            </div>

            @if($metodosPagoLabels->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-chart-pie fa-2x mb-2"></i>
                    <p class="mb-0">Aún no hay cobros este mes.</p>
                </div>
            @else
                <div class="donut-container">
                    <canvas id="paymentChart"></canvas>
                </div>
            @endif

        </div>

    </div>

</div>
@endif

{{-- TABLE + PRODUCTS (Ventas/Inventario/Compras: solo Plus/Empresarial, y solo para quien puede ver reportes financieros) --}}

@if($mostrarVentas && auth()->user()->hasAnyRole(['Admin', 'Gerente']))
<div class="row">

    <div class="col-lg-8">

        <div class="dashboard-card">

            <div class="dashboard-card-title">
                Últimos Movimientos
            </div>

            <div class="table-responsive">

                <table class="table dashboard-table">

                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($ultimasVentas as $mov)
                            <tr>
                                <td>{{ $mov->CLI_Nombre }}</td>
                                <td>{{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y') }}</td>
                                <td>S/ {{ number_format($mov->total, 2) }}</td>
                                <td>
                                    @if($mov->VEN_Status == 1)
                                        <span class="badge-soft-success">Completado</span>
                                    @else
                                        <span class="badge-soft-danger">Anulado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">Aún no hay ventas registradas.</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="dashboard-card">

            <div class="dashboard-card-title">
                Top Productos
            </div>

            @forelse($topProductos as $index => $prod)
                @php
                    $porcentajeTop = $maxUnidadesTop > 0 ? round(($prod->unidades / $maxUnidadesTop) * 100) : 0;
                    $coloresTop = ['bg-primary', 'bg-success', 'bg-warning'];
                    $imagenTop = $prod->PRO_Imagen
                        ? '/storage/' . tenant('tipo_negocio') . '/' . tenant('id') . '/archivos/producto/' . $prod->PRO_Imagen
                        : '/images/imagen_default.png';
                @endphp
                <div class="top-product">

                    <img src="{{ $imagenTop }}" alt="{{ $prod->PRO_Nombre }}">

                    <div class="flex-grow-1">

                        <div class="top-product-name">{{ $prod->PRO_Nombre }}</div>

                        <div class="top-product-sales">{{ (int) $prod->unidades }} unidades vendidas</div>

                        <div class="progress">
                            <div class="progress-bar {{ $coloresTop[$index % 3] }}" style="width:{{ $porcentajeTop }}%"></div>
                        </div>

                    </div>

                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <p class="mb-0">Aún no hay ventas de productos.</p>
                </div>
            @endforelse

        </div>

    </div>

</div>
@endif

</div>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Colores de texto/rejilla que se adaptan al tema claro/oscuro del panel --}}
<script>
    const chartTextColor = getComputedStyle(document.body).getPropertyValue('--text-muted').trim() || '#64748B';
    const chartGridColor = getComputedStyle(document.body).getPropertyValue('--bg-main').trim() === '#0F172A'
        ? 'rgba(255,255,255,.06)'
        : 'rgba(148,163,184,.15)';
</script>

@if($mostrarVentas)
<script>

    // ======================================================
    // SALES CHART
    // ======================================================

    const salesCtx = document.getElementById('salesChart');

    if (salesCtx) {
    new Chart(salesCtx, {

        type: 'line',

        data: {

            labels: @json($labelsMeses),

            datasets: [
                {
                    label: 'Ventas',
                    data: @json($serieVentasMensual),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37,99,235,.10)',
                    fill: true,
                    tension: .4,
                    borderWidth: 3
                },
                {
                    label: 'Gastos',
                    data: @json($serieGastosMensual),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239,68,68,.08)',
                    fill: true,
                    tension: .4,
                    borderWidth: 3
                }
            ]
        },

        options: {

            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'top'
                }
            },

            scales: {
                y: {
                    grid: {
                        color: 'rgba(148,163,184,.08)'
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
    }

    // ======================================================
    // PAYMENT CHART
    // ======================================================

    const paymentCtx = document.getElementById('paymentChart');

    if (paymentCtx) {
        new Chart(paymentCtx, {

            type: 'doughnut',

            data: {

                labels: @json($metodosPagoLabels),

                datasets: [{

                    data: @json($metodosPagoData),

                    backgroundColor: [
                        '#2563EB',
                        '#22C55E',
                        '#F59E0B',
                        '#A855F7',
                        '#EF4444'
                    ],

                    borderWidth: 0
                }]
            },

            options: {

                responsive: true,
                maintainAspectRatio: false,

                cutout: '70%',

                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

</script>
@endif

<script>

    // ======================================================
    // RESERVAS ÚLTIMOS 7 DÍAS
    // ======================================================

    if (document.getElementById('reservasChart')) {
    new Chart(document.getElementById('reservasChart'), {
        type: 'bar',
        data: {
            labels: @json($labelsReservas7d),
            datasets: [{
                label: 'Reservas',
                data: @json($serieReservas7d),
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
                        precision: 0,
                        color: chartTextColor
                    },
                    grid: {
                        color: chartGridColor
                    }
                },
                x: {
                    ticks: {
                        color: chartTextColor
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    }

    // ======================================================
    // MANTENIMIENTOS POR ESTADO
    // ======================================================

    const mantenimientoEstadoCtx = document.getElementById('mantenimientoEstadoChart');

    if (mantenimientoEstadoCtx) {
        new Chart(mantenimientoEstadoCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Aprobado', 'Observado'],
                datasets: [{
                    data: [
                        {{ $mantenimientosPendientes }},
                        {{ $mantenimientosAprobados }},
                        {{ $mantenimientosObservados }}
                    ],
                    backgroundColor: ['#F59E0B', '#22C55E', '#EF4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: chartTextColor
                        }
                    }
                }
            }
        });
    }

    // ======================================================
    // MANTENIMIENTOS POR TIPO
    // ======================================================

    new Chart(document.getElementById('mantenimientoTipoChart'), {
        type: 'bar',
        data: {
            labels: @json($mantenimientosPorTipoLabels),
            datasets: [{
                label: 'Mantenimientos',
                data: @json($mantenimientosPorTipoData),
                backgroundColor: 'rgba(124,58,237,.65)',
                borderRadius: 8,
                maxBarThickness: 40
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: chartTextColor
                    },
                    grid: {
                        color: chartGridColor
                    }
                },
                y: {
                    ticks: {
                        color: chartTextColor
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

</script>

@endsection
