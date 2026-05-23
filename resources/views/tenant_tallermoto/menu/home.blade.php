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

</style>

<div class="dashboard-wrapper">
{{-- HEADER --}}

<div class="mb-4">

    <h1 class="dashboard-title">
        Bienvenido 👋
    </h1>

    <p class="dashboard-subtitle">
        Aquí tienes el resumen general de tu negocio hoy.
    </p>

</div>

{{-- KPI CARDS --}}

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

            <div class="kpi-value">S/ 12,580</div>

            <div class="kpi-growth">
                <i class="fas fa-arrow-up"></i>
                +12.5%
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

            <div class="kpi-value">S/ 45,920</div>

            <div class="kpi-growth">
                <i class="fas fa-arrow-up"></i>
                +8.2%
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-3">

        <div class="kpi-card kpi-warning">

            <div class="kpi-header">

                <div>
                    <div class="kpi-title">Gastos del Mes</div>
                </div>

                <div class="kpi-icon bg-warning-gradient">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>

            </div>

            <div class="kpi-value">S/ 8,420</div>

            <div class="kpi-growth">
                <i class="fas fa-arrow-up"></i>
                +3.1%
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

            <div class="kpi-value">18</div>

            <div class="kpi-growth">
                <i class="fas fa-exclamation-circle"></i>
                Atención
            </div>

        </div>

    </div>

</div>

{{-- CHARTS --}}

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
                Métodos de Pago
            </div>

            <div class="donut-container">
                <canvas id="paymentChart"></canvas>
            </div>

        </div>

    </div>

</div>

{{-- TABLE + PRODUCTS --}}

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

                        <tr>
                            <td>Juan Perez</td>
                            <td>12/05/2026</td>
                            <td>S/ 250.00</td>
                            <td>
                                <span class="badge-soft-success">
                                    Completado
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>Maria Lopez</td>
                            <td>12/05/2026</td>
                            <td>S/ 120.00</td>
                            <td>
                                <span class="badge-soft-success">
                                    Completado
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>Carlos Diaz</td>
                            <td>11/05/2026</td>
                            <td>S/ 580.00</td>
                            <td>
                                <span class="badge-soft-danger">
                                    Pendiente
                                </span>
                            </td>
                        </tr>

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

            <div class="top-product">

                <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=300&auto=format&fit=crop" alt="">

                <div class="flex-grow-1">

                    <div class="top-product-name">iPhone 15 Pro</div>

                    <div class="top-product-sales">120 ventas</div>

                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width:85%"></div>
                    </div>

                </div>

            </div>

            <div class="top-product">

                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=300&auto=format&fit=crop" alt="">

                <div class="flex-grow-1">

                    <div class="top-product-name">Nike Air Max</div>

                    <div class="top-product-sales">95 ventas</div>

                    <div class="progress">
                        <div class="progress-bar bg-success" style="width:70%"></div>
                    </div>

                </div>

            </div>

            <div class="top-product">

                <img src="https://images.unsplash.com/photo-1517336714739-489689fd1ca8?q=80&w=300&auto=format&fit=crop" alt="">

                <div class="flex-grow-1">

                    <div class="top-product-name">Macbook Pro</div>

                    <div class="top-product-sales">75 ventas</div>

                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width:55%"></div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
```

</div>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // ======================================================
    // SALES CHART
    // ======================================================

    const salesCtx = document.getElementById('salesChart');

    new Chart(salesCtx, {

        type: 'line',

        data: {

            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],

            datasets: [
                {
                    label: 'Ventas',
                    data: [12000, 19000, 15000, 24000, 21000, 28000],
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37,99,235,.10)',
                    fill: true,
                    tension: .4,
                    borderWidth: 3
                },
                {
                    label: 'Gastos',
                    data: [8000, 12000, 10000, 14000, 12000, 16000],
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

    // ======================================================
    // PAYMENT CHART
    // ======================================================

    const paymentCtx = document.getElementById('paymentChart');

    new Chart(paymentCtx, {

        type: 'doughnut',

        data: {

            labels: ['Yape', 'Efectivo', 'Tarjeta'],

            datasets: [{

                data: [45, 30, 25],

                backgroundColor: [
                    '#2563EB',
                    '#22C55E',
                    '#F59E0B'
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

</script>

@endsection
