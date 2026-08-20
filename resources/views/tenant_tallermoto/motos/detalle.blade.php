@extends('tenant_tallermoto.layout.appAdminLte')
@section('titulo', 'Historial de ' . $placa)
@section('contenido')

<style>
    .moto-hero {
        background: linear-gradient(135deg, #E52320 0%, #C81B18 100%);
        border-radius: 14px; padding: 1.8rem 2rem; color: #fff; margin-bottom: 1.5rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    }
    .moto-hero .placa-box {
        background: rgba(255,255,255,.15); border: 2px solid rgba(255,255,255,.4);
        border-radius: 8px; padding: .5rem 1.1rem; font-weight: 800; font-size: 1.4rem; letter-spacing: .05em;
    }
    .moto-dato { font-size: .82rem; opacity: .9; }
    .moto-dato strong { display: block; font-size: 1rem; opacity: 1; }

    .moto-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); background: var(--bg-card,#fff); margin-bottom: 1.25rem; }
    .moto-card .card-body { padding: 1.5rem; }

    .historial-item {
        display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid rgba(128,128,128,.15);
    }
    .historial-item:last-child { border-bottom: none; }
    .historial-icon {
        width: 42px; height: 42px; border-radius: 10px; background: rgba(229,35,32,.1); color: #E52320;
        display: flex; align-items: center; justify-content: center; flex: none; font-size: 1.1rem;
    }
    .historial-body { flex: 1; }
    .historial-tipo { font-weight: 700; color: var(--text-main,#1E293B); }
    .historial-fecha { color: var(--text-muted,#6c757d); font-size: .82rem; }
</style>

<div class="col-12">
    <div class="moto-hero">
        <div style="display:flex; align-items:center; gap:1rem;">
            <div class="placa-box">{{ $placa }}</div>
            <div>
                <div class="moto-dato">Propietario actual<strong>{{ $ultimo->propietario ?? '—' }}</strong></div>
            </div>
            <div>
                <div class="moto-dato">Modelo<strong>{{ $ultimo->unidad ?? '—' }}</strong></div>
            </div>
            <div>
                <div class="moto-dato">Celular<strong>{{ $ultimo->celular ?? '—' }}</strong></div>
            </div>
        </div>
        <a href="{{ tenant_url('tenant.motos.index') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Motos
        </a>
    </div>
</div>

<div class="col-12 col-md-4">
    <div class="moto-card card">
        <div class="card-body">
            <h6 class="font-weight-bold mb-3" style="color:var(--text-main,#1E293B);">
                <i class="fas fa-clock-rotate-left mr-1" style="color:#E52320;"></i> Último mantenimiento
            </h6>
            <p class="small mb-1"><strong>Tipo:</strong> {{ $ultimo->tipo }}</p>
            <p class="small mb-1"><strong>Fecha:</strong> {{ $ultimo->fecha ? \Carbon\Carbon::parse($ultimo->fecha)->translatedFormat('d \d\e F, Y') : '—' }}</p>
            <p class="small mb-1"><strong>Estado:</strong> {{ ucfirst(strtolower($ultimo->estado)) }}</p>
            <p class="small mb-0"><strong>Próximo servicio:</strong> {{ $ultimo->proximo_servicio ?: 'No especificado' }}</p>
        </div>
    </div>

    <div class="moto-card card">
        <div class="card-body text-center">
            <div style="font-size:2rem; font-weight:800; color:#E52320;">{{ $historial->count() }}</div>
            <div class="text-muted small">Mantenimiento(s) registrado(s) en total para esta placa</div>
        </div>
    </div>
</div>

<div class="col-12 col-md-8">
    <div class="moto-card card">
        <div class="card-body">
            <h6 class="font-weight-bold mb-2" style="color:var(--text-main,#1E293B);">
                <i class="fas fa-list-ul mr-1" style="color:#E52320;"></i> Historial completo
            </h6>

            @forelse ($historial as $item)
                @php
                    $rutaInfo = $rutasPorTipo->get($item->tipo);
                @endphp
                <div class="historial-item">
                    <div class="historial-icon"><i class="fas fa-wrench"></i></div>
                    <div class="historial-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <div class="historial-tipo">{{ $item->tipo }}</div>
                                <div class="historial-fecha">
                                    {{ $item->fecha ? \Carbon\Carbon::parse($item->fecha)->translatedFormat('d M Y, h:i A') : 'Sin fecha' }}
                                    &middot; {{ ucfirst(strtolower($item->estado)) }}
                                </div>
                            </div>
                            @if ($rutaInfo)
                                <a href="{{ tenant_url($rutaInfo['ruta'], [$rutaInfo['param'] => $item->registro_id]) }}"
                                    class="btn btn-sm btn-outline-primary mt-1">
                                    Ver detalle completo
                                </a>
                            @endif
                        </div>
                        @if ($item->proximo_servicio)
                            <div class="small text-muted mt-1">Próximo servicio sugerido: {{ $item->proximo_servicio }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted">Sin historial registrado.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
