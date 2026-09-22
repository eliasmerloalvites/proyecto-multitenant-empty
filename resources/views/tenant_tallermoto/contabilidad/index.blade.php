@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Contabilidad')
@section('contenido')

    <style>
        .contab-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, .06);
            padding: 28px 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform .15s ease, box-shadow .15s ease;
            text-decoration: none;
            color: inherit;
        }
        .contab-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .12);
            color: inherit;
            text-decoration: none;
        }
        .contab-card .icono {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            margin-bottom: 16px;
        }
        .contab-card h5 {
            font-weight: 800;
            margin-bottom: 8px;
        }
        .contab-card p {
            color: #64748b;
            font-size: 13.5px;
            margin-bottom: 0;
        }
    </style>

    <div class="col-12">
        <div class="mb-4">
            <h4 class="mb-1"><i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Contabilidad</h4>
            <p class="text-muted mb-0">Reportes listos para descargar en Excel: lo que tu contador necesita para declarar y conciliar.</p>
        </div>

        <div class="row">
            @can('tenant.contabilidad.libroventas')
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('tenant.contabilidad.libroventas') }}" class="contab-card">
                    <div class="icono" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h5>Libro de Ventas</h5>
                    <p>Boletas y facturas emitidas, con base imponible e IGV desglosados.</p>
                </a>
            </div>
            @endcan

            @can('tenant.contabilidad.librocompras')
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('tenant.contabilidad.librocompras') }}" class="contab-card">
                    <div class="icono" style="background: linear-gradient(135deg, #0891b2, #0e7490);">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <h5>Libro de Compras</h5>
                    <p>Compras registradas a proveedores, con base imponible e IGV desglosados.</p>
                </a>
            </div>
            @endcan

            @can('tenant.contabilidad.gastos')
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('tenant.contabilidad.gastos') }}" class="contab-card">
                    <div class="icono" style="background: linear-gradient(135deg, #d97706, #b45309);">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h5>Registro de Gastos</h5>
                    <p>Gastos operativos (alquiler, servicios, planilla, etc.) por tipo y método de pago.</p>
                </a>
            </div>
            @endcan

            @can('tenant.contabilidad.caja')
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('tenant.contabilidad.caja') }}" class="contab-card">
                    <div class="icono" style="background: linear-gradient(135deg, #64748b, #334155);">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <h5>Resumen de Caja</h5>
                    <p>Sesiones de caja cerradas (con diferencias de cuadre) y cuentas por cobrar pendientes.</p>
                </a>
            </div>
            @endcan
        </div>
    </div>

@endsection
