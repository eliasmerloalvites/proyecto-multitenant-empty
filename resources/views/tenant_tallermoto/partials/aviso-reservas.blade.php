@if (($avisoReservasPendientes ?? 0) > 0)
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3" role="alert">
        <div>
            <i class="fab fa-whatsapp mr-2"></i>
            Tienes <strong>{{ $avisoReservasPendientes }}</strong>
            {{ $avisoReservasPendientes === 1 ? 'reserva' : 'reservas' }} de mañana sin notificar.
        </div>
        <a href="{{ tenant_url('tenant.reservaciones.notificaciones.index') }}" class="btn btn-sm btn-outline-dark">
            Notificar ahora
        </a>
    </div>
@endif
