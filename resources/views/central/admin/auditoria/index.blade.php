@extends('central.layout.appAdminLte')
@section('titulo', 'Auditoría')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">AUDITORÍA — ACCIONES DEL PANEL</h5>
                <p class="text-muted">
                    Historial de todo lo que se crea, edita, suspende o cobra desde este panel, para saber
                    quién hizo qué y cuándo.
                </p>

                <form method="GET" class="row mb-4">
                    <div class="col-md-4 mb-2">
                        <label class="small text-muted mb-1">Acción</label>
                        <select name="accion" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach ($acciones as $accion)
                                <option value="{{ $accion }}" {{ request('accion') === $accion ? 'selected' : '' }}>
                                    {{ $accion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small text-muted mb-1">Desde</label>
                        <input type="date" name="desde" value="{{ request('desde') }}" class="form-control" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small text-muted mb-1">Hasta</label>
                        <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <a href="{{ route('admin.auditoria.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-times mr-1"></i> Limpiar
                        </a>
                    </div>
                </form>

                <div class="table-responsive" style="background:#FFF;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Quién</th>
                                <th>Acción</th>
                                <th>Detalle</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($log->user_name)
                                            {{ $log->user_name }}
                                        @else
                                            <span class="badge badge-secondary">Sistema</span>
                                        @endif
                                    </td>
                                    <td><code class="small">{{ $log->accion }}</code></td>
                                    <td>{{ $log->descripcion }}</td>
                                    <td class="text-muted small">{{ $log->ip ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aún no hay acciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>
    </div>

@endsection
