@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Caja cerrada')
@section('contenido')

<style>
    .caja-requerida-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
        width: 100%;
    }
    .caja-requerida-card {
        max-width: 460px;
        width: 100%;
        text-align: center;
        background: #1a1f2b;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 16px;
        padding: 3rem 2.5rem;
        box-shadow: 0 20px 50px -20px rgba(0,0,0,.5);
    }
    .caja-requerida-icon {
        width: 76px;
        height: 76px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        background: rgba(245, 158, 11, .12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #F59E0B;
    }
    .caja-requerida-card h4 {
        color: #fff;
        font-weight: 800;
        margin-bottom: .5rem;
    }
    .caja-requerida-card p {
        color: #9CA3AF;
        margin-bottom: 1.75rem;
        font-size: .92rem;
    }
    .btn-aperturar-caja {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: .85rem 1.1rem;
        margin-bottom: .6rem;
        border-radius: 10px;
        border: 1px solid rgba(34,197,94,.35);
        background: rgba(34,197,94,.1);
        color: #22C55E;
        font-weight: 600;
        transition: background .15s ease;
    }
    .btn-aperturar-caja:hover {
        background: rgba(34,197,94,.2);
        color: #22C55E;
    }
    .caja-requerida-link {
        display: inline-block;
        margin-top: 1rem;
        color: #6B7280;
        font-size: .85rem;
    }
</style>

<div class="caja-requerida-wrap">
    <div class="caja-requerida-card">
        <div class="caja-requerida-icon">
            <i class="fas fa-cash-register"></i>
        </div>
        <h4>No hay ninguna caja abierta</h4>
        <p>Para registrar {{ $accion ?? 'este movimiento' }} primero necesitas aperturar una caja. Cada venta, compra o gasto queda ligado al turno activo.</p>

        @forelse ($cajasCerradas ?? [] as $cj)
            <button type="button" class="btn-aperturar-caja aperturarCajaNavbar"
                data-id="{{ $cj->CAJ_Id }}" data-nombre="{{ $cj->CAJ_Nombre }}" data-monto="{{ $cj->CAJ_MontoApertura }}">
                <span><i class="fas fa-unlock mr-2"></i>{{ $cj->CAJ_Nombre }}</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        @empty
            <p class="text-danger small">No tienes ninguna caja creada todavía.</p>
        @endforelse

        <a href="{{ tenant_url('tenant.ventas.caja.index') }}" class="caja-requerida-link">
            <i class="fas fa-gear mr-1"></i> Ir a Configuración de Cajas
        </a>
    </div>
</div>

@endsection
