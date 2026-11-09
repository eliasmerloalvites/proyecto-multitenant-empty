@extends('central.layout.appAdminLte')
@section('titulo', 'Seguimiento de clientes')
@section('contenido')

    <style>
        .seg-kpi { cursor: pointer; transition: transform .1s; }
        .seg-kpi:hover { transform: translateY(-2px); }
        .seg-kpi.activo { outline: 2px solid #2563EB; }
        .seg-kpi .numero { font-size: 1.8rem; font-weight: 700; line-height: 1.1; }
        .seg-linea { border-left: 3px solid #CBD5E1; padding: 2px 0 10px 14px; position: relative; }
        .seg-linea::before { content: ''; position: absolute; left: -7px; top: 5px; width: 11px; height: 11px; border-radius: 50%; background: var(--c, #94A3B8); border: 2px solid #fff; }
        .seg-linea.t-correo { --c: #2563EB; } .seg-linea.t-auto { --c: #7C3AED; } .seg-linea.t-whatsapp { --c: #16A34A; }
        .seg-linea.t-llamada { --c: #F59E0B; } .seg-linea.t-etapa { --c: #0EA5E9; } .seg-linea.t-nota { --c: #94A3B8; }
        .seg-linea .detalle { white-space: pre-line; font-size: .85rem; color: #475569; }
        #tablaSeguimiento td { vertical-align: middle; }
    </style>

    <div class="col-12">

        <div class="row" id="kpis">
            @foreach ([
                ['en_prueba', 'En prueba', 'info', 'fa-hourglass-half', 'en_prueba'],
                ['vencidas', 'Prueba vencida', 'danger', 'fa-calendar-times', 'vencida'],
                ['sin_uso', 'Sin uso', 'warning', 'fa-ghost', 'pruebas'],
                ['para_hoy', 'Contactar hoy', 'primary', 'fa-bell', 'todos'],
                ['convertidos', 'Convertidos', 'success', 'fa-check-circle', 'convertido'],
            ] as [$id, $titulo, $color, $icono, $seg])
                <div class="col-6 col-md">
                    <div class="small-box bg-white shadow-sm seg-kpi mb-3" data-kpi="{{ $id }}" data-segmento="{{ $seg }}">
                        <div class="inner py-2">
                            <div class="numero text-{{ $color }}" id="kpi-{{ $id }}">–</div>
                            <div class="text-muted"><i class="fas {{ $icono }} mr-1"></i>{{ $titulo }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">SEGUIMIENTO DE CLIENTES Y PRUEBAS</h5>
                        <p class="text-muted mb-0">
                            Quién se registró en prueba, si realmente lo usó y qué falta para convertirlo.
                            Envío automático:
                            @if ($automaticoActivo)
                                <span class="badge badge-success">ACTIVO</span>
                            @else
                                <span class="badge badge-secondary" title="Se activa con SEGUIMIENTO_AUTOMATICO=true en el .env">APAGADO</span>
                            @endif
                        </p>
                    </div>
                    @can('admin.seguimiento.gestionar')
                        <div class="mt-2 mt-md-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnActualizarUso">
                                <i class="fas fa-sync-alt"></i> Actualizar uso
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="btnMasivo" disabled>
                                <i class="far fa-envelope"></i> Enviar correo a <span id="cantSel">0</span> seleccionados
                            </button>
                        </div>
                    @endcan
                </div>

                <div class="form-row mb-3">
                    <div class="col-6 col-md-3 mb-2">
                        <label class="small text-muted mb-0">Mostrar</label>
                        <select id="fSegmento" class="form-control form-control-sm">
                            <option value="pruebas">Pruebas sin pagar</option>
                            <option value="en_prueba">Solo en prueba vigente</option>
                            <option value="vencida">Solo prueba vencida</option>
                            <option value="convertido">Ya pagaron</option>
                            <option value="todos">Todos los clientes</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <label class="small text-muted mb-0">Etapa</label>
                        <select id="fEtapa" class="form-control form-control-sm">
                            <option value="">Todas</option>
                            @foreach ($etapas as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <label class="small text-muted mb-0">Uso del sistema</label>
                        <select id="fUso" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <option value="sin_uso">Sin uso</option>
                            <option value="poco_uso">Poco uso</option>
                            <option value="activo">Activo</option>
                            <option value="sin_medir">Sin medir</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 mb-2 d-flex align-items-end">
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="fHoy">
                            <label class="custom-control-label" for="fHoy">Solo para contactar hoy</label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="background:#FFF;">
                    <table class="table table-striped nowrap w-100" id="tablaSeguimiento">
                        <thead>
                            <tr>
                                <th style="width:30px"><input type="checkbox" id="chkTodos"></th>
                                <th>Negocio</th>
                                <th>Prueba</th>
                                <th>Uso</th>
                                <th>Etapa</th>
                                <th>Contacto</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== FICHA DEL CLIENTE ===================== --}}
    <div class="modal fade" id="modalFicha" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0" id="fNombre">Cliente</h5>
                        <small id="fSub" class="text-white-50"></small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-5 mb-3">
                            <div class="card card-outline card-secondary mb-3">
                                <div class="card-body py-2" id="fDatos"></div>
                            </div>

                            <div class="card card-outline card-info mb-3">
                                <div class="card-header py-2"><strong>¿Usó el sistema?</strong></div>
                                <div class="card-body py-2" id="fUsoBox"></div>
                            </div>

                            @can('admin.seguimiento.gestionar')
                                <div class="card card-outline card-primary mb-0">
                                    <div class="card-header py-2"><strong>Gestión</strong></div>
                                    <div class="card-body py-2">
                                        <label class="small text-muted mb-0">Etapa</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <select id="gEtapa" class="form-control">
                                                @foreach ($etapas as $k => $v)
                                                    <option value="{{ $k }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" id="btnEtapa" type="button">Guardar</button>
                                            </div>
                                        </div>
                                        <input type="text" id="gMotivo" class="form-control form-control-sm mb-2 d-none" maxlength="255" placeholder="¿Por qué se perdió? (precio, no lo usará, ya tiene otro sistema…)">

                                        <label class="small text-muted mb-0">Próximo contacto</label>
                                        <div class="form-row mb-2">
                                            <div class="col-5"><input type="date" id="gFecha" class="form-control form-control-sm"></div>
                                            <div class="col-7"><input type="text" id="gNota" class="form-control form-control-sm" maxlength="255" placeholder="Qué hacer (ej. llamar)"></div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary mb-2" id="btnProxima" type="button">Guardar próximo contacto</button>

                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="gNoContactar">
                                            <label class="custom-control-label small" for="gNoContactar">Pidió no recibir mensajes</label>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>

                        <div class="col-lg-7">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabMensaje" id="lnkMensaje">Escribir</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabHistorial">Historial <span class="badge badge-light" id="cantHist">0</span></a></li>
                            </ul>
                            <div class="tab-content pt-3">

                                <div class="tab-pane fade show active" id="tabMensaje">
                                    <div class="form-group mb-2">
                                        <label class="small text-muted mb-0">Plantilla</label>
                                        <select id="mPlantilla" class="form-control form-control-sm"></select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small text-muted mb-0">Asunto</label>
                                        <input type="text" id="mAsunto" class="form-control form-control-sm" maxlength="200">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small text-muted mb-0">Mensaje (puedes editarlo antes de enviar)</label>
                                        <textarea id="mCuerpo" class="form-control" rows="9" maxlength="5000"></textarea>
                                    </div>
                                    @can('admin.seguimiento.gestionar')
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-primary" id="btnCorreo"><i class="far fa-envelope"></i> Enviar por correo</button>
                                            <button type="button" class="btn btn-success" id="btnWhatsapp"><i class="fab fa-whatsapp"></i> Abrir WhatsApp</button>
                                        </div>

                                        <div class="card card-outline card-secondary mb-0">
                                            <div class="card-header py-2"><strong>Registrar algo que ya hiciste</strong></div>
                                            <div class="card-body py-2">
                                                <div class="form-row">
                                                    <div class="col-4">
                                                        <select id="nTipo" class="form-control form-control-sm">
                                                            <option value="llamada">Llamada</option>
                                                            <option value="whatsapp">WhatsApp</option>
                                                            <option value="nota">Nota interna</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8"><input type="text" id="nDetalle" class="form-control form-control-sm" maxlength="2000" placeholder="Qué pasó / qué dijo el cliente"></div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="btnNota">Registrar</button>
                                            </div>
                                        </div>
                                    @endcan
                                </div>

                                <div class="tab-pane fade" id="tabHistorial">
                                    <div id="fHistorial" class="pl-2"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== ENVÍO MASIVO ===================== --}}
    <div class="modal fade" id="modalMasivo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title mb-0"><i class="far fa-envelope mr-2"></i>Correo a <span id="masCant">0</span> clientes</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Se escribe a cada cliente por separado. Puedes usar <code>{negocio}</code>, <code>{dias}</code>, <code>{fecha_fin}</code> y <code>{asesor}</code>: se reemplazan con los datos de cada uno. Se omiten los que no tienen correo o pidieron no recibir mensajes.</p>
                    <div class="form-group mb-2">
                        <label class="small text-muted mb-0">Plantilla</label>
                        <select id="masPlantilla" class="form-control form-control-sm">
                            @foreach ($plantillas as $p)
                                <option value="{{ $p['clave'] }}">{{ $p['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="small text-muted mb-0">Asunto</label>
                        <input type="text" id="masAsunto" class="form-control form-control-sm" maxlength="200">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small text-muted mb-0">Mensaje</label>
                        <textarea id="masCuerpo" class="form-control" rows="9" maxlength="5000"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnMasivoEnviar"><i class="fas fa-paper-plane"></i> Enviar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @php
        $plantillasCrudas = collect(\App\Services\Seguimiento\SeguimientoPlantillas::PLANTILLAS)
            ->map(fn ($p, $k) => ['clave' => $k, 'asunto' => $p['asunto'], 'cuerpo' => $p['cuerpo']])->values();
    @endphp
    <script>
        $(document).ready(function() {
            const BASE = "{{ url('admin/seguimiento') }}";
            const PLANTILLAS_CRUDAS = @json($plantillasCrudas);
            const ETAPAS = @json($etapas);

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500 });
            const esc = (t) => $('<div>').text(t == null ? '' : t).html();

            function post(url, data) {
                return $.ajax({ url: url, type: 'POST', data: data || {} }).fail(function(xhr) {
                    const m = xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message);
                    Toast.fire({ icon: 'error', title: m || 'No se pudo completar la acción.' });
                });
            }

            // ---------------- Tabla ----------------
            const tabla = $('#tablaSeguimiento').DataTable({
                processing: true,
                order: [],
                pageLength: 25,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                ajax: {
                    url: "{{ route('admin.seguimiento.datos') }}",
                    data: function(d) {
                        d.segmento = $('#fSegmento').val();
                        d.etapa = $('#fEtapa').val();
                        d.uso = $('#fUso').val();
                        d.para_hoy = $('#fHoy').is(':checked') ? 1 : 0;
                    }
                },
                columns: [
                    { data: 'seleccion', orderable: false, searchable: false },
                    { data: 'negocio', name: 'razon_social' },
                    { data: 'prueba', orderable: false },
                    { data: 'uso', orderable: false },
                    { data: 'etapa', orderable: false },
                    { data: 'contacto', orderable: false },
                    { data: 'acciones', orderable: false, searchable: false },
                ],
                drawCallback: function() { contarSeleccion(); cargarKpis(); }
            });

            $('#fSegmento, #fEtapa, #fUso, #fHoy').on('change', function() {
                $('.seg-kpi').removeClass('activo');
                tabla.ajax.reload();
            });

            // ---------------- Tarjetas ----------------
            function cargarKpis() {
                $.get("{{ route('admin.seguimiento.resumen') }}", function(r) {
                    Object.keys(r).forEach(function(k) { $('#kpi-' + k).text(r[k]); });
                });
            }

            $('.seg-kpi').on('click', function() {
                const kpi = $(this).data('kpi');
                $('.seg-kpi').removeClass('activo'); $(this).addClass('activo');
                $('#fEtapa').val(''); $('#fUso').val(''); $('#fHoy').prop('checked', false);
                $('#fSegmento').val($(this).data('segmento'));
                if (kpi === 'sin_uso') $('#fUso').val('sin_uso');
                if (kpi === 'para_hoy') $('#fHoy').prop('checked', true);
                tabla.ajax.reload();
            });

            // ---------------- Selección ----------------
            function contarSeleccion() {
                const n = $('.chk-cliente:checked').length;
                $('#cantSel').text(n);
                $('#btnMasivo').prop('disabled', n === 0);
            }
            $('body').on('change', '.chk-cliente', contarSeleccion);
            $('#chkTodos').on('change', function() { $('.chk-cliente').prop('checked', this.checked); contarSeleccion(); });

            // ---------------- Actualizar uso (todos) ----------------
            $('#btnActualizarUso').on('click', function() {
                const $b = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Midiendo...');
                post("{{ route('admin.seguimiento.uso.todos') }}").done(function(r) {
                    Toast.fire({ icon: 'success', title: r.success });
                    tabla.ajax.reload(null, false);
                }).always(function() { $b.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Actualizar uso'); });
            });

            // ---------------- Ficha ----------------
            let fichaActual = null;

            function badgeUso(n) {
                const m = { sin_uso: ['danger', 'Sin uso'], poco_uso: ['warning', 'Poco uso'], activo: ['success', 'Activo'] };
                return n ? '<span class="badge badge-' + m[n][0] + '">' + m[n][1] + '</span>' : '<span class="badge badge-light border">Sin medir</span>';
            }

            function pintarFicha(d) {
                fichaActual = d;
                const c = d.cliente, f = d.ficha;

                $('#fNombre').text(c.razon_social);
                $('#fSub').text(c.segmento === 'convertido' ? 'Ya es cliente de pago'
                    : c.fin_prueba ? (c.dias_restantes > 0 ? 'Prueba: quedan ' + c.dias_restantes + ' días (hasta ' + c.fin_prueba + ')' : 'Prueba vencida el ' + c.fin_prueba) : 'Sin prueba registrada');

                $('#fDatos').html(
                    '<div class="small"><i class="far fa-envelope text-muted"></i> ' + (c.email ? esc(c.email) : '<span class="text-muted">sin correo</span>') + '</div>' +
                    '<div class="small"><i class="fab fa-whatsapp text-muted"></i> ' + (c.telefono ? esc(c.telefono) : '<span class="text-muted">sin teléfono</span>') + '</div>' +
                    '<div class="small"><i class="far fa-clock text-muted"></i> Registrado: ' + esc(c.registrado) + '</div>' +
                    (c.ruc ? '<div class="small"><i class="far fa-id-card text-muted"></i> RUC ' + esc(c.ruc) + '</div>' : '') +
                    (c.url ? '<div class="small"><i class="fas fa-link text-muted"></i> <a href="' + esc(c.url) + '" target="_blank" rel="noopener">' + esc(c.url) + '</a></div>' : '') +
                    '<div class="small mt-1"><i class="far fa-comments text-muted"></i> Último contacto: ' + (f.ultimo_contacto ? esc(f.ultimo_contacto) : '<span class="text-muted">nunca</span>') + '</div>'
                );

                const conteo = Object.keys(f.uso_conteo || {}).map(function(k) { return '<span class="badge badge-light border mr-1">' + esc(f.uso_conteo[k]) + ' ' + esc(k) + '</span>'; }).join('');
                $('#fUsoBox').html(
                    '<div class="d-flex justify-content-between align-items-center">' + badgeUso(f.uso_nivel) +
                    '@can('admin.seguimiento.gestionar')<button class="btn btn-xs btn-outline-secondary" id="btnUsoUno"><i class="fas fa-sync-alt"></i> Medir ahora</button>@endcan</div>' +
                    '<div class="small mt-2">' + (f.uso_resumen ? esc(f.uso_resumen) : 'Aún no se ha medido.') + '</div>' +
                    '<div class="mt-1">' + conteo + '</div>' +
                    '<div class="small text-muted mt-1">Última actividad: ' + (f.ultima_actividad ? esc(f.ultima_actividad) : 'ninguna detectada') +
                    (f.uso_medido ? ' · medido ' + esc(f.uso_medido) : '') + '</div>' +
                    '<div class="small text-muted">Solo cuenta lo registrado después de crear la cuenta (no los datos de ejemplo).</div>'
                );

                $('#gEtapa').val(f.etapa);
                $('#gMotivo').val(f.motivo_perdida || '').toggleClass('d-none', f.etapa !== 'perdido');
                $('#gFecha').val(f.proxima_accion_at || '');
                $('#gNota').val(f.proxima_accion_nota || '');
                $('#gNoContactar').prop('checked', !!f.no_contactar);

                // Plantillas (ya con los datos de este cliente)
                const $p = $('#mPlantilla').empty();
                d.plantillas.forEach(function(p) { $p.append($('<option>').val(p.clave).text(p.nombre)); });
                $p.val(d.sugerida);
                aplicarPlantilla();
                $('#btnCorreo').prop('disabled', !c.email || f.no_contactar);
                $('#btnWhatsapp').prop('disabled', !c.whatsapp);

                // Historial
                $('#cantHist').text(d.eventos.length);
                const iconos = { correo: 'Correo', auto: 'Correo automático', whatsapp: 'WhatsApp', llamada: 'Llamada', etapa: 'Etapa', nota: 'Nota' };
                $('#fHistorial').html(d.eventos.length ? d.eventos.map(function(e) {
                    return '<div class="seg-linea t-' + esc(e.tipo) + '"><div><strong>' + esc(iconos[e.tipo] || e.tipo) + '</strong>' +
                        (e.asunto && e.tipo !== 'nota' && e.tipo !== 'llamada' && e.tipo !== 'whatsapp' ? ' · ' + esc(e.asunto) : '') +
                        ' <small class="text-muted">· ' + esc(e.cuando) + ' · ' + esc(e.quien) + '</small></div>' +
                        (e.detalle ? '<div class="detalle">' + esc(e.detalle) + '</div>' : '') + '</div>';
                }).join('') : '<p class="text-muted">Todavía no hay movimientos con este cliente.</p>');
            }

            function aplicarPlantilla() {
                if (!fichaActual) return;
                const p = fichaActual.plantillas.find(function(x) { return x.clave === $('#mPlantilla').val(); });
                if (p) { $('#mAsunto').val(p.asunto); $('#mCuerpo').val(p.cuerpo); }
            }
            $('#mPlantilla').on('change', aplicarPlantilla);

            function cargarFicha(id, tab) {
                return $.get(BASE + '/' + id).done(function(d) {
                    pintarFicha(d);
                    if (!$('#modalFicha').hasClass('show')) $('#modalFicha').modal('show');
                    $('#lnkMensaje').tab('show');
                }).fail(function() { Toast.fire({ icon: 'error', title: 'No se pudo abrir el seguimiento.' }); });
            }

            $('body').on('click', '.abrirFicha', function() { cargarFicha($(this).data('id'), $(this).data('tab')); });

            function recargarTodo() { cargarFicha(fichaActual.cliente.id); tabla.ajax.reload(null, false); }

            $('body').on('click', '#btnUsoUno', function() {
                const $b = $(this).prop('disabled', true);
                post(BASE + '/' + fichaActual.cliente.id + '/uso').done(function(r) { Toast.fire({ icon: 'success', title: r.success }); recargarTodo(); })
                    .always(function() { $b.prop('disabled', false); });
            });

            $('#gEtapa').on('change', function() { $('#gMotivo').toggleClass('d-none', $(this).val() !== 'perdido'); });

            $('#btnEtapa').on('click', function() {
                post(BASE + '/' + fichaActual.cliente.id + '/etapa', { etapa: $('#gEtapa').val(), motivo_perdida: $('#gMotivo').val() })
                    .done(function(r) { Toast.fire({ icon: 'success', title: r.success }); recargarTodo(); });
            });

            $('#btnProxima').on('click', function() {
                post(BASE + '/' + fichaActual.cliente.id + '/proxima', { fecha: $('#gFecha').val(), nota: $('#gNota').val() })
                    .done(function(r) { Toast.fire({ icon: 'success', title: r.success }); recargarTodo(); });
            });

            $('#gNoContactar').on('change', function() {
                post(BASE + '/' + fichaActual.cliente.id + '/no-contactar', { valor: this.checked ? 1 : 0 })
                    .done(function(r) { Toast.fire({ icon: 'success', title: r.success }); recargarTodo(); });
            });

            $('#btnCorreo').on('click', function() {
                const $b = $(this).prop('disabled', true);
                post(BASE + '/' + fichaActual.cliente.id + '/correo', { asunto: $('#mAsunto').val(), cuerpo: $('#mCuerpo').val() })
                    .done(function(r) { Toast.fire({ icon: 'success', title: r.success }); recargarTodo(); })
                    .fail(function() { $b.prop('disabled', false); });
            });

            $('#btnWhatsapp').on('click', function() {
                const c = fichaActual.cliente;
                const texto = $('#mCuerpo').val();
                window.open('https://wa.me/' + c.whatsapp + '?text=' + encodeURIComponent(texto), '_blank', 'noopener');

                Swal.fire({
                    icon: 'question', title: '¿Enviaste el mensaje?',
                    text: 'Si lo enviaste, lo dejo anotado en el historial.',
                    showCancelButton: true, confirmButtonText: 'Sí, anotarlo', cancelButtonText: 'No'
                }).then(function(res) {
                    if (!res.isConfirmed) return;
                    post(BASE + '/' + c.id + '/nota', { tipo: 'whatsapp', detalle: texto })
                        .done(function(r) { Toast.fire({ icon: 'success', title: r.success }); recargarTodo(); });
                });
            });

            $('#btnNota').on('click', function() {
                const detalle = $('#nDetalle').val().trim();
                if (!detalle) { Toast.fire({ icon: 'warning', title: 'Escribe qué pasó.' }); return; }
                post(BASE + '/' + fichaActual.cliente.id + '/nota', { tipo: $('#nTipo').val(), detalle: detalle })
                    .done(function(r) { $('#nDetalle').val(''); Toast.fire({ icon: 'success', title: r.success }); recargarTodo(); });
            });

            // ---------------- Envío masivo ----------------
            function plantillaMasiva() {
                const p = PLANTILLAS_CRUDAS.find(function(x) { return x.clave === $('#masPlantilla').val(); });
                if (p) { $('#masAsunto').val(p.asunto); $('#masCuerpo').val(p.cuerpo); }
            }
            $('#masPlantilla').on('change', plantillaMasiva);

            $('#btnMasivo').on('click', function() {
                $('#masCant').text($('.chk-cliente:checked').length);
                plantillaMasiva();
                $('#modalMasivo').modal('show');
            });

            $('#btnMasivoEnviar').on('click', function() {
                const ids = $('.chk-cliente:checked').map(function() { return this.value; }).get();
                const $b = $(this).prop('disabled', true);

                post("{{ route('admin.seguimiento.masivo') }}", { ids: ids, asunto: $('#masAsunto').val(), cuerpo: $('#masCuerpo').val() })
                    .done(function(r) {
                        $('#modalMasivo').modal('hide');
                        if (r.fallidos && r.fallidos.length) {
                            Swal.fire({ icon: 'warning', title: r.success, html: '<div style="text-align:left;font-size:.85rem">' + r.fallidos.map(esc).join('<br>') + '</div>' });
                        } else {
                            Toast.fire({ icon: 'success', title: r.success });
                        }
                        $('#chkTodos').prop('checked', false);
                        tabla.ajax.reload(null, false);
                    })
                    .always(function() { $b.prop('disabled', false); });
            });
        });
    </script>
@endsection
