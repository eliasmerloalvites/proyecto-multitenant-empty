@if (session('warning'))
    <div class="alert alert-danger d-flex align-items-center justify-content-between mb-3" role="alert">
        <div><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}</div>
        <a href="{{ tenant_url('tenant.facturacion.index') }}" class="btn btn-sm btn-outline-light">
            Ir a Facturación
        </a>
    </div>
@elseif (($estadoCicloPago ?? null) === 'vencido')
    <div class="alert alert-danger d-flex align-items-center justify-content-between mb-3" role="alert">
        <div>
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Tu pago venció el {{ $fechaCicloPago->translatedFormat('d \d\e F') }}. Regulariza tu cuenta para evitar la suspensión del servicio.
        </div>
        <a href="{{ tenant_url('tenant.facturacion.index') }}" class="btn btn-sm btn-light">
            Pagar ahora
        </a>
    </div>
@elseif (($estadoCicloPago ?? null) === 'por_vencer')
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3" role="alert">
        <div>
            <i class="fas fa-clock mr-2"></i>
            Tu próximo pago vence el {{ $fechaCicloPago->translatedFormat('d \d\e F') }}.
        </div>
        <a href="{{ tenant_url('tenant.facturacion.index') }}" class="btn btn-sm btn-outline-dark">
            Ver facturación
        </a>
    </div>
@endif
