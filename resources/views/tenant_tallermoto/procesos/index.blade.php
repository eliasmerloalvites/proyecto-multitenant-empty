@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Gestión de Proceso')
@section('contenido')

    <style>
        .proceso-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }
        .proceso-header h4 {
            margin: 0;
            font-weight: 700;
        }
        .proceso-header .fecha-hoy {
            color: #6c757d;
            font-size: .9rem;
            text-transform: capitalize;
        }
        .proceso-header .modo-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 4px 10px;
            border-radius: 20px;
            margin-left: 10px;
            vertical-align: middle;
        }
        .modo-completa { background: #ede9fe; color: #6d28d9; }
        .modo-mecanico { background: #dbeafe; color: #1d4ed8; }

        .proceso-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 16px;
        }
        .proceso-col {
            background: #f4f6f9;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .proceso-col-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1.05rem;
            color: #1f2937;
        }
        .proceso-col-header .icon-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            flex-shrink: 0;
        }

        .proc-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            padding: 14px;
            border-left: 5px solid #cbd5e1;
            transition: box-shadow .15s ease;
        }
        .proc-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.10); }
        .proc-card.estado-sin-checkin { border-left-color: #94a3b8; }
        .proc-card.estado-asignado { border-left-color: #0284c7; }
        .proc-card.estado-con-aviso { border-left-color: #f59e0b; }
        .proc-card.estado-aprobado { border-left-color: #16a34a; }

        .proc-turno {
            font-size: .78rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: .03em;
        }
        .proc-cliente { font-weight: 700; font-size: 1rem; color: #111827; margin-top: 2px; }
        .proc-moto { font-size: .85rem; color: #6b7280; }

        .badge-estado-proc {
            font-size: .72rem;
            padding: 4px 9px;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-sin-checkin { background: #e2e8f0; color: #475569; }
        .badge-asignado { background: #dbeafe; color: #1d4ed8; }
        .badge-aprobado { background: #dcfce7; color: #15803d; }
        .badge-observado { background: #fee2e2; color: #b91c1c; }

        .proc-mecanico {
            font-size: .82rem;
            color: #374151;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .proc-mecanico i { color: #0284c7; }
        .proc-tipo {
            font-size: .74rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .02em;
            font-weight: 600;
            margin-top: 6px;
        }
        .proc-detalle {
            font-size: .84rem;
            color: #374151;
            background: #f8fafc;
            border: 1px solid #eef1f4;
            border-radius: 9px;
            padding: 8px 10px;
            margin-top: 6px;
            white-space: pre-line;
        }
        .proc-detalle.truncado {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .aviso-mecanico-banner {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            color: #78350f;
            border-radius: 9px;
            padding: 8px 10px;
            font-size: .82rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
            animation: pulso-aviso 1.8s ease-in-out infinite;
        }
        @keyframes pulso-aviso {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,.35); }
            50% { box-shadow: 0 0 0 6px rgba(245,158,11,0); }
        }
        .aviso-mecanico-banner span { display: flex; align-items: center; gap: 6px; }

        .proc-acciones {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .proc-acciones .fila-btns {
            display: flex;
            gap: 6px;
        }
        .proc-acciones .fila-btns .btn { flex: 1; }

        .sin-reserva-proc {
            text-align: center;
            color: #9ca3af;
            font-size: .85rem;
            padding: 18px 0;
        }
    </style>

    <div class="col-12">
        <div class="proceso-header">
            <div>
                <h4>
                    <i class="fa fa-clipboard-list mr-2 text-primary"></i>Gestión de Proceso
                    @if ($vistaCompleta)
                        <span class="modo-badge modo-completa"><i class="fa fa-user-tie"></i> Recepción</span>
                    @else
                        <span class="modo-badge modo-mecanico"><i class="fa fa-wrench"></i> Mis trabajos de hoy</span>
                    @endif
                </h4>
                <div class="fecha-hoy">{{ $fechaHoy }}</div>
            </div>
        </div>

        <div class="proceso-board">
            @forelse ($tablero as $col)
                <div class="proceso-col">
                    <div class="proceso-col-header">
                        <span class="icon-circle"><i class="fa fa-warehouse"></i></span>
                        {{ $col['bahia']->BAH_Nombre }}
                    </div>

                    @forelse ($col['reservas'] as $r)
                        @php
                            $reserva = $r['reserva'];
                            $mtto = $r['mantenimiento'];
                            $prefijo = $mtto['prefijo'] ?? null;
                            $registro = $mtto['registro'] ?? null;
                            $tieneAviso = $registro && $registro->{$prefijo . '_AvisoMecanico'};
                            $estadoNegocio = $registro->{$prefijo . '_Estado'} ?? null;

                            $estadoVisual = 'sin-checkin';
                            if ($tieneAviso) {
                                $estadoVisual = 'con-aviso';
                            } elseif ($estadoNegocio === 'APROBADO') {
                                $estadoVisual = 'aprobado';
                            } elseif ($registro) {
                                $estadoVisual = 'asignado';
                            }

                            $mecanicoNombre = $registro
                                ? ($mecanicos->firstWhere('id', $registro->PER_Id)->name ?? 'Usuario #' . $registro->PER_Id)
                                : null;

                            $detalleIngreso = $registro ? $registro->{$prefijo . '_DetalleIngreso'} : '';
                        @endphp
                        <div class="proc-card estado-{{ $estadoVisual }}"
                             id="res-card-{{ $reserva->RES_Id }}"
                             data-res-id="{{ $reserva->RES_Id }}"
                             data-tipo="{{ $mtto['tipo'] ?? '' }}"
                             data-detalle="{{ $detalleIngreso }}"
                             data-mecanico-id="{{ $registro->PER_Id ?? '' }}"
                             data-mtto-tabla="{{ $mtto['tabla'] ?? '' }}"
                             data-mtto-id="{{ $registro->{$prefijo.'_Id'} ?? '' }}">

                            @if ($tieneAviso)
                                <div class="aviso-mecanico-banner">
                                    <span><i class="fa fa-bell"></i> Recepción actualizó este trabajo</span>
                                    <button type="button" class="btn btn-sm btn-dark"
                                        onclick="marcarEntendido('{{ $mtto['tabla'] }}', {{ $registro->{$prefijo.'_Id'} }}, {{ $reserva->RES_Id }})">
                                        Entendido
                                    </button>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="proc-turno">{{ $reserva->TUR_Descripcion }}</div>
                                    <div class="proc-cliente">{{ $reserva->RES_Cliente }}</div>
                                    <div class="proc-moto">{{ $reserva->RES_Moto }} · {{ $reserva->RES_Placa }}</div>
                                </div>
                                <span class="badge-estado-proc badge-{{ $estadoVisual === 'con-aviso' ? 'asignado' : ($estadoVisual === 'sin-checkin' ? 'sin-checkin' : $estadoVisual) }}">
                                    {{ match(true) {
                                        $estadoVisual === 'sin-checkin' => 'Sin check-in',
                                        $estadoNegocio === 'OBSERVADO' => 'Observado',
                                        $estadoNegocio === 'APROBADO' => 'Aprobado',
                                        default => 'Asignado',
                                    } }}
                                </span>
                            </div>

                            @if ($registro)
                                <div class="proc-mecanico"><i class="fa fa-user-gear"></i> {{ $mecanicoNombre }}</div>
                                <div class="proc-tipo">{{ $mtto['etiqueta'] }}</div>
                                <div class="proc-detalle {{ $vistaCompleta ? 'truncado' : '' }}">{{ $detalleIngreso }}</div>
                            @endif

                            <div class="proc-acciones">
                                @if ($vistaCompleta)
                                    @if (!$registro)
                                        <button class="btn btn-primary btn-sm btn-block" onclick="abrirModalCheckIn({{ $reserva->RES_Id }})">
                                            <i class="fa fa-right-to-bracket"></i> Recibir moto (check-in)
                                        </button>
                                    @else
                                        <div class="fila-btns">
                                            <button class="btn btn-outline-secondary btn-sm" onclick="abrirModalCheckIn({{ $reserva->RES_Id }})">
                                                <i class="fa fa-pen"></i> Editar detalle
                                            </button>
                                            <a class="btn btn-outline-primary btn-sm" target="_blank"
                                                href="{{ tenant_url($mtto['ruta_edit'], [$mtto['ruta_param'] => $registro->{$prefijo.'_Id'}]) }}">
                                                <i class="fa fa-list-check"></i> Ver todo
                                            </a>
                                        </div>
                                        <button class="btn btn-success btn-sm btn-block" onclick="cargarProductos({{ $reserva->RES_Id }})">
                                            <i class="fa fa-cart-plus"></i> Cargar productos (bahía)
                                        </button>
                                    @endif
                                @else
                                    <div class="fila-btns">
                                        <a class="btn btn-outline-primary btn-sm" target="_blank"
                                            href="{{ tenant_url($mtto['ruta_edit'], [$mtto['ruta_param'] => $registro->{$prefijo.'_Id'}]) }}">
                                            <i class="fa fa-list-check"></i> Abrir formulario
                                        </a>
                                        <button class="btn btn-success btn-sm" onclick="cargarProductos({{ $reserva->RES_Id }})">
                                            <i class="fa fa-cart-plus"></i> Productos
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="sin-reserva-proc">
                            <i class="fa fa-calendar-check mb-1"></i><br>
                            {{ $vistaCompleta ? 'Sin reservas para hoy' : 'No tienes trabajos asignados aquí' }}
                        </div>
                    @endforelse
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    @if ($vistaCompleta)
                        No hay bahías activas configuradas en esta sede.
                    @else
                        No tienes trabajos asignados para hoy todavía.
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    @if ($vistaCompleta)
        <!-- MODAL: check-in / editar detalle -->
        <div class="modal fade" id="modalCheckIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCheckInTitulo"><i class="fa fa-right-to-bracket mr-2"></i>Recibir moto</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tipo de mantenimiento</label>
                            <select id="checkinTipo" class="form-control">
                                @foreach ($tipos as $tipo => $meta)
                                    <option value="{{ $tipo }}">{{ $meta['etiqueta'] }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" id="checkinTipoAviso" style="display:none;">
                                El tipo no se puede cambiar una vez asignado.
                            </small>
                        </div>
                        <div class="form-group">
                            <label>Detalle de lo que hay que hacer</label>
                            <textarea id="checkinDetalle" class="form-control" rows="4"
                                placeholder="Ej. Cambio de aceite, revisar frenos delanteros, ruido raro en la cadena..."></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label>Mecánico responsable</label>
                            <select id="checkinMecanico" class="form-control">
                                <option value="">Selecciona...</option>
                                @foreach ($mecanicos as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardarCheckIn">
                            <i class="fa fa-check"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
@section('script')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true
        });

        function cargarProductos(resId) {
            $.ajax({
                url: '{{ tenant_url("tenant.ventas.bahias.abrir", ["reservacion" => ":id"]) }}'.replace(':id', resId),
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            }).done(function () {
                window.location.href = '{{ tenant_url("tenant.ventas.bahias.index") }}';
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: 'No se pudo abrir la cuenta', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
            });
        }

        function marcarEntendido(tabla, id, resId) {
            $.ajax({
                url: '{{ tenant_url("tenant.procesos.entendido", ["tabla" => ":tabla", "id" => ":id"]) }}'
                    .replace(':tabla', tabla).replace(':id', id),
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            }).done(function () {
                $('#res-card-' + resId + ' .aviso-mecanico-banner').remove();
                $('#res-card-' + resId).removeClass('estado-con-aviso').addClass('estado-asignado');
                Toast.fire({ icon: 'success', title: 'Marcado como visto' });
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: 'No se pudo confirmar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
            });
        }

        @if ($vistaCompleta)
            let checkinResIdActivo = null;

            function abrirModalCheckIn(resId) {
                checkinResIdActivo = resId;
                let $card = $('#res-card-' + resId);
                let tipo = $card.data('tipo');
                let detalle = $card.data('detalle');
                let mecanicoId = $card.data('mecanico-id');
                let yaExiste = !!tipo;

                $('#checkinTipo').val(yaExiste ? tipo : $('#checkinTipo option:first').val());
                $('#checkinTipo').prop('disabled', yaExiste);
                $('#checkinTipoAviso').toggle(yaExiste);
                $('#checkinDetalle').val(detalle || '');
                $('#checkinMecanico').val(mecanicoId || '');
                $('#modalCheckInTitulo').html(yaExiste
                    ? '<i class="fa fa-pen mr-2"></i>Editar detalle de atención'
                    : '<i class="fa fa-right-to-bracket mr-2"></i>Recibir moto');
                $('#modalCheckIn').modal('show');
            }

            $('#btnGuardarCheckIn').on('click', function () {
                let tipo = $('#checkinTipo').val();
                let detalle = ($('#checkinDetalle').val() || '').trim();
                let mecanicoId = $('#checkinMecanico').val();

                if (!detalle) {
                    Swal.fire({ icon: 'warning', title: 'Falta el detalle', text: 'Escribe qué hay que hacer con la moto.' });
                    return;
                }
                if (!mecanicoId) {
                    Swal.fire({ icon: 'warning', title: 'Falta el mecánico', text: 'Selecciona quién va a atenderlo.' });
                    return;
                }

                let $btn = $(this).prop('disabled', true);

                $.ajax({
                    url: '{{ tenant_url("tenant.procesos.checkin", ["reservacionId" => ":id"]) }}'.replace(':id', checkinResIdActivo),
                    method: 'POST',
                    data: {
                        tipo_mantenimiento: tipo,
                        detalle: detalle,
                        mecanico_id: mecanicoId,
                        _token: '{{ csrf_token() }}'
                    }
                }).done(function () {
                    $('#modalCheckIn').modal('hide');
                    location.reload();
                }).fail(function (xhr) {
                    Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Error de conexión.' });
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });
        @else
            // Vista mecanico: polling para detectar en vivo cuando recepcion
            // agrega/cambia algo en un trabajo que ya tengo asignado.
            let alertasConocidas = new Set(
                $('.aviso-mecanico-banner').closest('.proc-card').map(function () {
                    return $(this).data('mtto-tabla') + '-' + $(this).data('mtto-id');
                }).get()
            );

            function revisarAlertas() {
                $.get('{{ tenant_url("tenant.procesos.alertas") }}').done(function (res) {
                    (res.alertas || []).forEach(function (a) {
                        let key = a.tabla + '-' + a.id;
                        if (alertasConocidas.has(key)) return;
                        alertasConocidas.add(key);

                        let $card = $('#res-card-' + a.res_id);
                        if ($card.length && !$card.find('.aviso-mecanico-banner').length) {
                            $card.prepend(
                                '<div class="aviso-mecanico-banner">' +
                                    '<span><i class="fa fa-bell"></i> Recepción actualizó este trabajo</span>' +
                                    '<button type="button" class="btn btn-sm btn-dark" onclick="marcarEntendido(\'' + a.tabla + '\', ' + a.id + ', ' + a.res_id + ')">Entendido</button>' +
                                '</div>'
                            );
                            $card.removeClass('estado-asignado').addClass('estado-con-aviso');
                            Toast.fire({ icon: 'info', title: 'Cambio nuevo en ' + a.placa });
                        }
                    });
                });
            }

            setInterval(revisarAlertas, 25000);
        @endif
    </script>
@endsection
