@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Pedidos de la app')
@section('contenido')

    <div class="col-12">

        <div class="alert alert-info">
            <i class="fa fa-shopping-bag"></i>
            Pedidos que tus clientes hacen desde la app. Se <strong>pagan y recogen en el taller</strong>.
            Al <strong>confirmar</strong> un pedido, el stock queda apartado para ese cliente; al <strong>cobrarlo</strong>
            se abre el punto de venta con todo cargado y el pedido queda entregado.
        </div>

        <ul class="nav nav-pills mb-3">
            @foreach ([
                'pendientes' => ['Por confirmar', 'warning'],
                'en-curso' => ['En preparación / listos', 'primary'],
                'entregados' => ['Entregados', 'success'],
                'cancelados' => ['Cancelados', 'secondary'],
            ] as $clave => [$nombre, $color])
                <li class="nav-item mr-2 mb-2">
                    <a class="nav-link {{ $pestana === $clave ? 'active' : 'bg-white' }}" href="{{ tenant_url('tenant.appmovil.pedidos.index', ['estado' => $clave]) }}">
                        {{ $nombre }}
                        <span class="badge badge-{{ $color }} ml-1">{{ $porPestana[$clave] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        @forelse ($pedidos as $p)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:12px;">
                        <div>
                            <h5 class="mb-1">
                                {{ $p->numero }}
                                @php
                                    $badge = [
                                        'PENDIENTE' => ['warning', 'Por confirmar'],
                                        'CONFIRMADO' => ['primary', 'Confirmado · stock apartado'],
                                        'LISTO' => ['info', 'Listo para recoger'],
                                        'ENTREGADO' => ['success', 'Entregado'],
                                        'CANCELADO' => ['secondary', 'Cancelado'],
                                    ][$p->estado];
                                @endphp
                                <span class="badge badge-{{ $badge[0] }} ml-1">{{ $badge[1] }}</span>
                            </h5>
                            <div class="text-muted small">
                                <i class="fa fa-user"></i> {{ $p->cliente }} · {{ $p->celular }}
                                &nbsp;·&nbsp; <i class="fa fa-store"></i> Recoge en: <strong>{{ $p->sede }}</strong>
                                &nbsp;·&nbsp; <i class="fa fa-clock"></i> {{ $p->fecha?->format('d/m/Y H:i') }}
                            </div>
                            @if ($p->nota)
                                <div class="small mt-1"><i class="fa fa-comment text-muted"></i> “{{ $p->nota }}”</div>
                            @endif
                            @if ($p->estado === 'CANCELADO')
                                <div class="small mt-1 text-danger">
                                    Cancelado por {{ $p->cancelado_por === 'CLIENTE' ? 'el cliente' : 'el taller' }}{{ $p->motivo ? ': ' . $p->motivo : '' }}
                                </div>
                            @endif
                            @if ($p->venta)
                                <div class="small mt-1 text-success"><i class="fa fa-receipt"></i> Cobrado en la venta #{{ $p->venta }}</div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-muted small">Total</div>
                            <div class="h4 mb-0">S/ {{ number_format($p->total, 2) }}</div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-right">Precio</th><th class="text-right">Subtotal</th></tr></thead>
                            <tbody>
                                @foreach ($p->items as $i)
                                    <tr>
                                        <td>{{ $i['nombre'] }}</td>
                                        <td class="text-center">{{ rtrim(rtrim(number_format($i['cantidad'], 2, '.', ''), '0'), '.') }}</td>
                                        <td class="text-right">S/ {{ number_format($i['precio'], 2) }}</td>
                                        <td class="text-right">S/ {{ number_format($i['subtotal'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if (in_array($p->estado, ['PENDIENTE', 'CONFIRMADO', 'LISTO']))
                        <div class="d-flex flex-wrap justify-content-end mt-3" style="gap:8px;">
                            <button class="btn btn-outline-danger btn-sm" onclick="cancelarPedido({{ $p->id }}, '{{ $p->numero }}')">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                            @if ($p->estado === 'PENDIENTE')
                                <button class="btn btn-primary btn-sm" onclick="accionPedido('confirmar', {{ $p->id }}, '¿Confirmar el pedido {{ $p->numero }}? El stock quedará apartado para el cliente.')">
                                    <i class="fa fa-check"></i> Confirmar
                                </button>
                            @endif
                            @if ($p->estado === 'CONFIRMADO')
                                <button class="btn btn-info btn-sm" onclick="accionPedido('listo', {{ $p->id }}, '¿Marcar {{ $p->numero }} como listo para recoger?')">
                                    <i class="fa fa-box"></i> Listo para recoger
                                </button>
                            @endif
                            @if (in_array($p->estado, ['CONFIRMADO', 'LISTO']))
                                <a class="btn btn-success btn-sm" href="{{ tenant_url('tenant.appmovil.pedidos.cobrar', ['id' => $p->id]) }}">
                                    <i class="fa fa-cash-register"></i> Cobrar
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="fa fa-shopping-bag fa-2x mb-2"></i><br>
                    No hay pedidos en esta sección.
                </div>
            </div>
        @endforelse
    </div>
@endsection

@section('script')
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        @if (session('error'))
            Swal.fire({ icon: 'warning', title: 'No se pudo cobrar', text: @json(session('error')) });
        @endif

        function mostrarError(xhr) {
            var msg = (xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message)) || 'No se pudo completar la acción.';
            Swal.fire({ icon: 'error', title: 'No se pudo', text: msg });
        }

        function urlAccion(accion, id) {
            return '{{ tenant_url("tenant.appmovil.pedidos.confirmar", ["id" => "__ID__"]) }}'
                .replace('__ID__', id).replace('/confirmar', '/' + accion);
        }

        function accionPedido(accion, id, pregunta) {
            Swal.fire({ icon: 'question', text: pregunta, showCancelButton: true, confirmButtonText: 'Sí, continuar', cancelButtonText: 'Volver' })
                .then(function (r) {
                    if (!r.isConfirmed && !r.value) { return; }
                    $.post(urlAccion(accion, id), {}, function (data) {
                        Swal.fire({ icon: 'success', text: data.success, timer: 1600, showConfirmButton: false })
                            .then(function () { window.location.reload(); });
                    }).fail(mostrarError);
                });
        }

        function cancelarPedido(id, numero) {
            Swal.fire({
                icon: 'warning',
                title: 'Cancelar ' + numero,
                input: 'text',
                inputPlaceholder: 'Motivo (lo verá el cliente en la app)',
                showCancelButton: true,
                confirmButtonText: 'Cancelar pedido',
                cancelButtonText: 'Volver'
            }).then(function (r) {
                if (!r.isConfirmed && r.value === undefined) { return; }
                $.post(urlAccion('cancelar', id), { motivo: r.value || '' }, function (data) {
                    Swal.fire({ icon: 'success', text: data.success, timer: 1600, showConfirmButton: false })
                        .then(function () { window.location.reload(); });
                }).fail(mostrarError);
            });
        }
    </script>
@endsection
