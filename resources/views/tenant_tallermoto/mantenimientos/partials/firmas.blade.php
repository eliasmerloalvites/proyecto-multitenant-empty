{{--
    Seccion "Firmas" de un mantenimiento: tecnico -> representante ->
    cliente, firmando con el dedo o el mouse. Reglas en
    FirmasMantenimientoService; con la firma del cliente el documento queda
    cerrado (la pantalla se bloquea y el servidor rechaza cambios).

    Se incluye al final de @section('script') de cada edit (necesita
    id_mpi). Parametro: $tipo = slug de la ruta (generalcarburada, ...).
    Solo se muestra en planes Plus/Empresarial, igual que las lineas de
    firma de los PDF.
--}}
@if (\App\Services\TenantTallerMotos\FirmasMantenimientoService::disponible())
<style>
    .firmas-pasos { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    @media (max-width: 767px) { .firmas-pasos { grid-template-columns: 1fr; } }
    .firma-paso { border: 1.5px solid #dee2e6; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; background: #fff; }
    .firma-paso.hecho { border-color: #28a745; background: #f6fff8; }
    .firma-paso.siguiente { border-color: #1f6feb; box-shadow: 0 0 0 3px rgba(31, 111, 235, .12); }
    .firma-paso-cab { display: flex; align-items: center; gap: 10px; }
    .firma-num { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; background: #e9ecef; color: #495057; flex-shrink: 0; }
    .firma-paso.hecho .firma-num { background: #28a745; color: #fff; }
    .firma-paso.siguiente .firma-num { background: #1f6feb; color: #fff; }
    .firma-rol { font-weight: 700; line-height: 1.1; }
    .firma-quien { font-size: 12px; color: #6c757d; }
    .firma-img { height: 86px; border: 1px dashed #ced4da; border-radius: 10px; background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .firma-img img { max-height: 80px; max-width: 100%; }
    .firma-motivo { font-size: 12px; color: #6c757d; }
    .firmas-cerrado { border-radius: 12px; background: #e8f5e9; border: 1px solid #b7dfbf; padding: 10px 14px; display: flex; gap: 10px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
    #firmaLienzoCaja { border: 2px dashed #adb5bd; border-radius: 12px; background: #fff; position: relative; touch-action: none; }
    #firmaLienzo { width: 100%; height: 220px; display: block; cursor: crosshair; }
    #firmaLienzoCaja .firma-guia { position: absolute; left: 8%; right: 8%; bottom: 46px; border-bottom: 1px solid #ced4da; pointer-events: none; }
    #firmaLienzoCaja .firma-x { position: absolute; left: 4%; bottom: 48px; color: #adb5bd; font-size: 18px; pointer-events: none; }
    #modalFirma .modal-content { border-radius: 18px; }
    .mantenimiento-cerrado .fileforImagen, .mantenimiento-cerrado .eliminarImagen { display: none !important; }
</style>

<div id="firmasSeccion" class="row mt-3" style="display: none;">
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title-custom"><i class="fas fa-signature mr-1"></i> Firmas del documento</h3>
            </div>
            <div class="card-body">
                <div id="firmasCerrado" class="firmas-cerrado mb-3" style="display: none;">
                    <div><i class="fas fa-lock text-success mr-1"></i> <strong>Documento firmado y cerrado.</strong> Ya no se puede modificar.</div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="firmasReabrir" style="display: none;">
                        <i class="fas fa-unlock mr-1"></i> Reabrir
                    </button>
                </div>
                <p class="text-muted mb-3" id="firmasAyuda" style="font-size: 13px;">
                    Firman en orden: primero el técnico asignado, luego un Admin o Gerente como representante y al final el cliente.
                    Al firmar el cliente, el mantenimiento queda cerrado.
                </p>
                <div class="firmas-pasos" id="firmasPasos"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFirma" tabindex="-1" aria-labelledby="modalFirmaTitulo" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFirmaTitulo">Firmar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row" id="firmaDatosCliente" style="display: none;">
                    <div class="form-group col-md-8">
                        <label for="firmaNombre">Nombre de quien firma</label>
                        <input type="text" class="form-control" id="firmaNombre" maxlength="150" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="firmaDocumento">DNI (opcional)</label>
                        <input type="text" class="form-control" id="firmaDocumento" maxlength="20" inputmode="numeric" autocomplete="off">
                    </div>
                </div>
                <p class="text-muted mb-2" id="firmaInstruccion" style="font-size: 13px;"></p>
                <div id="firmaLienzoCaja">
                    <canvas id="firmaLienzo" aria-label="Recuadro para firmar"></canvas>
                    <span class="firma-x">✕</span>
                    <div class="firma-guia"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" id="firmaLimpiar"><i class="fas fa-eraser mr-1"></i> Borrar</button>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="firmaGuardar"><i class="fas fa-check mr-1"></i> Guardar firma</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const URL_ESTADO = {!! json_encode(tenant_url('tenant.mantenimientos.firmas.estado', ['tipo' => $tipo, 'id' => '__ID__'])) !!};
        const URL_FIRMAR = {!! json_encode(tenant_url('tenant.mantenimientos.firmas.firmar', ['tipo' => $tipo, 'id' => '__ID__'])) !!};
        const URL_IMAGEN = {!! json_encode(tenant_url('tenant.mantenimientos.firmas.imagen', ['tipo' => $tipo, 'id' => '__ID__', 'rol' => '__ROL__'])) !!};
        const URL_REABRIR = {!! json_encode(tenant_url('tenant.mantenimientos.firmas.reabrir', ['tipo' => $tipo, 'id' => '__ID__'])) !!};
        const TOKEN = '{{ csrf_token() }}';
        const PASOS = [
            { rol: 'TECNICO', titulo: 'Técnico', sub: 'Responsable del trabajo' },
            { rol: 'REPRESENTANTE', titulo: 'Representante', sub: 'Admin o Gerente del taller' },
            { rol: 'CLIENTE', titulo: 'Cliente', sub: 'Conforme / entrega del vehículo' },
        ];

        let estado = null;
        let rolActual = null;
        let hayTrazo = false;

        const url = (u, rol) => u.replace('__ID__', id_mpi).replace('__ROL__', (rol || '').toLowerCase());
        const esc = (t) => $('<div>').text(t == null ? '' : t).html();
        const aviso = (tipo, texto) => Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, type: tipo, title: texto });

        $(function() {
            // La seccion va justo encima de los botones Regresar/Aprobar.
            const $botones = $('button[onclick="Regresar()"]').first().closest('.row');
            const $form = $('.form-scroll-container form').first();
            if ($botones.length) {
                $('#firmasSeccion').insertBefore($botones);
            } else if ($form.length) {
                $('#firmasSeccion').appendTo($form);
            }
            $('#modalFirma').appendTo('body');
            cargar();
        });

        function cargar() {
            $.get(url(URL_ESTADO)).done(function(r) {
                estado = r;
                pintar();
            });
        }

        function pintar() {
            const siguiente = PASOS.find((p) => !estado.roles[p.rol].firmado);
            const $pasos = $('#firmasPasos').empty();

            PASOS.forEach(function(p, i) {
                const f = estado.roles[p.rol];
                const clase = f.firmado ? 'hecho' : (siguiente && siguiente.rol === p.rol ? 'siguiente' : '');
                let cuerpo;
                if (f.firmado) {
                    cuerpo = '<div class="firma-img"><img alt="Firma de ' + esc(f.nombre) + '" src="' + url(URL_IMAGEN, p.rol) + '?v=' + encodeURIComponent(f.fecha) + '"></div>'
                        + '<div><strong>' + esc(f.nombre) + '</strong>' + (f.documento ? ' · DNI ' + esc(f.documento) : '') + '</div>'
                        + '<div class="firma-quien">' + esc(f.fecha) + (p.rol === 'CLIENTE' && f.usuario ? ' · registró ' + esc(f.usuario) : '') + '</div>';
                } else {
                    const quien = p.rol === 'TECNICO' && estado.tecnico_asignado ? 'Asignado: ' + esc(estado.tecnico_asignado) : esc(p.sub);
                    cuerpo = '<div class="firma-img text-muted"><i class="fas fa-pen-nib"></i></div>'
                        + '<div class="firma-quien">' + quien + '</div>'
                        + (f.puede_firmar
                            ? '<button type="button" class="btn btn-primary btn-sm firmar-rol" data-rol="' + p.rol + '"><i class="fas fa-signature mr-1"></i> Firmar como ' + p.titulo.toLowerCase() + '</button>'
                            : '<div class="firma-motivo"><i class="fas fa-info-circle mr-1"></i>' + esc(f.motivo) + '</div>');
                }
                $pasos.append('<div class="firma-paso ' + clase + '"><div class="firma-paso-cab"><span class="firma-num">' + (f.firmado ? '<i class="fas fa-check"></i>' : (i + 1)) + '</span>'
                    + '<div><div class="firma-rol">' + p.titulo + '</div><div class="firma-quien">' + esc(p.sub) + '</div></div></div>' + cuerpo + '</div>');
            });

            $('#firmasCerrado').toggle(estado.cerrado);
            $('#firmasReabrir').toggle(!!estado.puede_reabrir);
            $('#firmasAyuda').toggle(!estado.cerrado);
            $('#firmasSeccion').show();
            bloquear(estado.cerrado);
        }

        // Con la firma del cliente: todo el formulario queda de solo lectura
        // (el servidor igual rechaza cualquier cambio; esto es para que se vea).
        function bloquear(cerrado) {
            const $form = $('.form-scroll-container form').first();
            $('body').toggleClass('mantenimiento-cerrado', cerrado);
            $('#updateBtn, #updateNotificarBtn, #activarBtn').toggle(!cerrado);
            if (cerrado) {
                $form.find(':input:not(:disabled)').not('#firmasSeccion :input, button[onclick="Regresar()"]')
                    .attr('data-firma-bloqueo', '1').prop('disabled', true);
            } else {
                // Solo se reactiva lo que bloqueo esta seccion.
                $form.find('[data-firma-bloqueo]').removeAttr('data-firma-bloqueo').prop('disabled', false);
            }
        }

        $('body').on('click', '.firmar-rol', function() {
            rolActual = $(this).data('rol');
            const paso = PASOS.find((p) => p.rol === rolActual);
            $('#modalFirmaTitulo').text('Firma del ' + paso.titulo.toLowerCase());
            $('#firmaDatosCliente').toggle(rolActual === 'CLIENTE');
            $('#firmaInstruccion').text(rolActual === 'CLIENTE'
                ? 'Pide al cliente que revise el trabajo y firme con el dedo dentro del recuadro.'
                : 'Firma con el dedo o el mouse dentro del recuadro.');
            if (rolActual === 'CLIENTE') {
                $('#firmaNombre').val(estado.cliente_sugerido || '');
                $('#firmaDocumento').val('');
            }
            $('#modalFirma').modal('show');
        });

        // ---- Lienzo de firma (Pointer Events: dedo, lapiz o mouse) ----
        const lienzo = document.getElementById('firmaLienzo');
        const ctx = lienzo.getContext('2d');
        let dibujando = false;
        let ultimo = null;

        function prepararLienzo() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const ancho = lienzo.clientWidth;
            const alto = lienzo.clientHeight;
            lienzo.width = Math.round(ancho * ratio);
            lienzo.height = Math.round(alto * ratio);
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#0b2a6b';
            ctx.lineWidth = 2.4;
            ctx.clearRect(0, 0, ancho, alto);
            hayTrazo = false;
        }

        $('#modalFirma').on('shown.bs.modal', prepararLienzo);
        $('#firmaLimpiar').on('click', prepararLienzo);

        const punto = (e) => {
            const r = lienzo.getBoundingClientRect();
            return { x: e.clientX - r.left, y: e.clientY - r.top };
        };
        lienzo.addEventListener('pointerdown', function(e) {
            e.preventDefault();
            lienzo.setPointerCapture(e.pointerId);
            dibujando = true;
            ultimo = punto(e);
            ctx.beginPath();
            ctx.arc(ultimo.x, ultimo.y, ctx.lineWidth / 2, 0, Math.PI * 2);
            ctx.fillStyle = ctx.strokeStyle;
            ctx.fill();
        });
        lienzo.addEventListener('pointermove', function(e) {
            if (!dibujando) return;
            e.preventDefault();
            const p = punto(e);
            ctx.beginPath();
            ctx.moveTo(ultimo.x, ultimo.y);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            if (Math.abs(p.x - ultimo.x) + Math.abs(p.y - ultimo.y) > 0) hayTrazo = true;
            ultimo = p;
        });
        ['pointerup', 'pointercancel', 'pointerleave'].forEach((ev) => lienzo.addEventListener(ev, () => { dibujando = false; }));

        // PNG final de tamano fijo (liviano y siempre igual en el PDF).
        function exportar() {
            const salida = document.createElement('canvas');
            salida.width = 600;
            salida.height = 220;
            const c = salida.getContext('2d');
            c.fillStyle = '#ffffff';
            c.fillRect(0, 0, salida.width, salida.height);
            c.drawImage(lienzo, 0, 0, salida.width, salida.height);
            return salida.toDataURL('image/png');
        }

        $('#firmaGuardar').on('click', function() {
            if (!hayTrazo) return aviso('warning', 'Firma dentro del recuadro antes de guardar.');
            if (rolActual === 'CLIENTE' && $('#firmaNombre').val().trim().length < 3) {
                $('#firmaNombre').trigger('focus');
                return aviso('warning', 'Escribe el nombre de quien firma.');
            }
            const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');
            $.post(url(URL_FIRMAR), {
                _token: TOKEN, rol: rolActual, imagen: exportar(),
                nombre: $('#firmaNombre').val(), documento: $('#firmaDocumento').val()
            }).done(function(r) {
                $('#modalFirma').modal('hide');
                estado = r.estado;
                pintar();
                if (estado.cerrado) {
                    Swal.fire({ type: 'success', title: 'Documento firmado', text: r.success });
                } else {
                    aviso('success', r.success);
                }
            }).fail(function(x) {
                const j = x.responseJSON || {};
                aviso('error', j.error || j.message || 'No se pudo guardar la firma.');
            }).always(function() {
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Guardar firma');
            });
        });

        $('#firmasReabrir').on('click', function() {
            Swal.fire({
                title: '¿Reabrir el mantenimiento?',
                text: 'Se borrarán las 3 firmas y deberán firmar de nuevo. Queda registrado quién lo reabrió y por qué.',
                input: 'text', inputPlaceholder: 'Motivo (obligatorio)',
                type: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                confirmButtonText: 'Reabrir', cancelButtonText: 'Cancelar',
                inputValidator: (v) => (!v || v.trim().length < 5) ? 'Escribe el motivo (mínimo 5 letras).' : undefined
            }).then(function(r) {
                if (!r.value) return;
                $.post(url(URL_REABRIR), { _token: TOKEN, motivo: r.value.trim() })
                    .done(function(d) { estado = d.estado; pintar(); aviso('success', d.success); })
                    .fail(function(x) { aviso('error', (x.responseJSON || {}).error || 'No se pudo reabrir.'); });
            });
        });
    })();
</script>
@endif
