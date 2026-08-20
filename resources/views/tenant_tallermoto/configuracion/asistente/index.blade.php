@extends('tenant_tallermoto.layout.appAdminLte')
@section('titulo', 'Asistente de Configuración')
@section('contenido')

<style>
    .asis-hero {
        background: linear-gradient(135deg, #E52320 0%, #C81B18 100%);
        border-radius: 14px;
        padding: 2.2rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
    }
    .asis-hero h3 { font-weight: 800; margin-bottom: .4rem; }
    .asis-hero p { opacity: .92; margin-bottom: 0; max-width: 640px; }

    .asis-card {
        border: 1px solid rgba(128,128,128,.15);
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
        margin-bottom: 1.25rem;
        background: var(--bg-card, #fff);
        color: var(--text-main, #1E293B);
    }
    .asis-card .card-body { padding: 1.6rem; }
    .asis-step-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 50%;
        background: #E52320; color: #fff; font-weight: 700; font-size: .85rem;
        margin-right: .6rem; flex: none;
    }
    .asis-step-title { display: flex; align-items: center; font-weight: 700; font-size: 1.05rem; margin-bottom: .3rem; color: var(--text-main, #1E293B); }
    .asis-step-help { color: var(--text-muted, #6c757d); font-size: .87rem; margin-bottom: 1.1rem; margin-left: 42px; }

    .asis-stepper { display: flex; align-items: center; gap: .75rem; margin-left: 42px; }
    .asis-stepper button {
        width: 42px; height: 42px; border-radius: 10px; border: 1px solid rgba(128,128,128,.3);
        background: transparent; font-size: 1.2rem; font-weight: 700; color: var(--text-main, #495057);
    }
    .asis-stepper button:hover { background: #E52320; color: #fff; border-color: #E52320; }
    .asis-stepper .asis-count { font-size: 1.6rem; font-weight: 800; min-width: 46px; text-align: center; color: var(--text-main, #1E293B); }

    .asis-turno-opciones { display: flex; gap: .75rem; margin-left: 42px; flex-wrap: wrap; }
    .asis-turno-card {
        border: 2px solid rgba(128,128,128,.25); border-radius: 10px; padding: .9rem 1.1rem; cursor: pointer;
        min-width: 150px; transition: all .15s ease; background: transparent;
    }
    .asis-turno-card:hover { border-color: #f3a5a3; }
    .asis-turno-card.active { border-color: #E52320; background: rgba(229,35,32,.1); }
    .asis-turno-card .num { font-weight: 800; font-size: 1.3rem; color: #E52320; }
    .asis-turno-card .prev { font-size: .74rem; color: var(--text-muted, #868e96); margin-top: .2rem; line-height: 1.3; }

    .asis-dias { display: flex; gap: .5rem; margin-left: 42px; flex-wrap: wrap; }
    .asis-dia-pill {
        width: 54px; height: 54px; border-radius: 10px; border: 2px solid rgba(128,128,128,.25);
        display: flex; align-items: center; justify-content: center; flex-direction: column;
        cursor: pointer; font-weight: 700; font-size: .78rem; color: var(--text-main, #495057); transition: all .15s ease;
    }
    .asis-dia-pill.active { border-color: #E52320; background: #E52320; color: #fff; }

    .asis-resumen {
        background: rgba(229,35,32,.06); border: 1px dashed rgba(229,35,32,.35); border-radius: 10px;
        padding: 1rem 1.2rem; margin-top: .5rem; font-size: .92rem; color: var(--text-main, #1E293B);
    }
    .asis-resumen strong { color: #E52320; }
</style>

@if ($yaConfigurado)
    <div class="col-12">
        <div class="asis-hero">
            <h3><i class="fas fa-circle-check mr-1"></i> Tu taller ya está configurado</h3>
            <p>Tienes {{ $bahias }} bahía(s), {{ $turnos }} turno(s) y {{ $horarios }} horario(s) registrados. Si quieres agregar o renombrar algo, hazlo desde las pantallas de Configuración — este asistente es solo para el arranque inicial.</p>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ tenant_url('tenant.configuracion.bahia.index') }}" class="asis-card card d-block text-decoration-none">
            <div class="card-body text-center">
                <i class="fas fa-warehouse fa-2x text-danger mb-2"></i>
                <h6 class="font-weight-bold mb-0">Bahías</h6>
                <small class="text-muted">Editar / agregar</small>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ tenant_url('tenant.configuracion.turno.index') }}" class="asis-card card d-block text-decoration-none">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-danger mb-2"></i>
                <h6 class="font-weight-bold mb-0">Turnos</h6>
                <small class="text-muted">Editar / agregar</small>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ tenant_url('tenant.configuracion.horario.index') }}" class="asis-card card d-block text-decoration-none">
            <div class="card-body text-center">
                <i class="fas fa-calendar-days fa-2x text-danger mb-2"></i>
                <h6 class="font-weight-bold mb-0">Horarios</h6>
                <small class="text-muted">Editar / agregar</small>
            </div>
        </a>
    </div>
@else
    <div class="col-12">
        <div class="asis-hero">
            <h3><i class="fas fa-wand-magic-sparkles mr-1"></i> Configura tu taller en 30 segundos</h3>
            <p>Responde 3 preguntas simples y armamos tus bahías, turnos y horarios automáticamente — con nombres y horas por defecto que puedes renombrar cuando quieras desde Configuración. Así ya puedes empezar a recibir reservas hoy mismo.</p>
        </div>
    </div>

    <div class="col-12">
        <form id="asistente_form">
            @csrf

            <div class="asis-card card">
                <div class="card-body">
                    <div class="asis-step-title"><span class="asis-step-num">1</span> ¿Cuántas bahías de trabajo tienes?</div>
                    <div class="asis-step-help">Cada bahía es un espacio físico donde se atiende una moto a la vez.</div>
                    <div class="asis-stepper">
                        <button type="button" id="bahiasMenos"><i class="fas fa-minus"></i></button>
                        <div class="asis-count" id="bahiasCount">2</div>
                        <button type="button" id="bahiasMas"><i class="fas fa-plus"></i></button>
                        <span class="text-muted small ml-2" id="bahiasPreview"></span>
                    </div>
                </div>
            </div>

            <div class="asis-card card">
                <div class="card-body">
                    <div class="asis-step-title"><span class="asis-step-num">2</span> ¿Cuántos turnos manejas al día?</div>
                    <div class="asis-step-help">Elige el que más se parezca a tu horario de atención.</div>
                    <div class="asis-turno-opciones">
                        <div class="asis-turno-card" data-turnos="1">
                            <div class="num">1</div>
                            <div class="prev">Turno Único<br>08:00 - 18:00</div>
                        </div>
                        <div class="asis-turno-card active" data-turnos="2">
                            <div class="num">2</div>
                            <div class="prev">Mañana 08-13<br>Tarde 14-19</div>
                        </div>
                        <div class="asis-turno-card" data-turnos="3">
                            <div class="num">3</div>
                            <div class="prev">Mañana / Tarde<br>/ Noche</div>
                        </div>
                        <div class="asis-turno-card" data-turnos="4">
                            <div class="num">4</div>
                            <div class="prev">4 bloques de<br>3 horas</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="asis-card card">
                <div class="card-body">
                    <div class="asis-step-title"><span class="asis-step-num">3</span> ¿Qué días trabajas?</div>
                    <div class="asis-step-help">Toca los días para activarlos o desactivarlos.</div>
                    <div class="asis-dias">
                        @foreach (['LUNES' => 'Lun', 'MARTES' => 'Mar', 'MIERCOLES' => 'Mié', 'JUEVES' => 'Jue', 'VIERNES' => 'Vie', 'SABADO' => 'Sáb', 'DOMINGO' => 'Dom'] as $valor => $label)
                            <div class="asis-dia-pill {{ $valor !== 'DOMINGO' ? 'active' : '' }}" data-dia="{{ $valor }}">
                                {{ $label }}
                            </div>
                        @endforeach
                    </div>

                    <div class="asis-resumen" id="resumenBox">
                        Se crearán <strong id="resumenBahias">2</strong> bahías,
                        <strong id="resumenTurnos">2</strong> turnos y
                        <strong id="resumenHorarios">12</strong> horarios (turnos × días trabajados).
                    </div>
                </div>
            </div>

            <div class="text-right mb-4">
                <a href="{{ tenant_url('tenant.home') }}" class="btn btn-outline-secondary mr-2">Lo haré manualmente</a>
                <button type="submit" id="generarBtn" class="btn btn-danger btn-lg px-4">
                    <i class="fas fa-wand-magic-sparkles mr-1"></i> Generar mi configuración
                </button>
            </div>
        </form>
    </div>
@endif

@endsection

@section('script')
<script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        var numBahias = 2;
        var numTurnos = 2;

        function actualizarPreviewBahias() {
            $('#bahiasCount').text(numBahias);
            var nombres = [];
            for (var i = 1; i <= Math.min(numBahias, 3); i++) nombres.push('Bahía ' + i);
            var extra = numBahias > 3 ? '…' : '';
            $('#bahiasPreview').text('(' + nombres.join(', ') + (extra ? ', ' + extra : '') + ')');
        }

        function diasActivos() {
            return $('.asis-dia-pill.active').length;
        }

        function actualizarResumen() {
            $('#resumenBahias').text(numBahias);
            $('#resumenTurnos').text(numTurnos);
            $('#resumenHorarios').text(numTurnos * diasActivos());
        }

        $('#bahiasMas').click(function () {
            if (numBahias < 20) numBahias++;
            actualizarPreviewBahias();
            actualizarResumen();
        });
        $('#bahiasMenos').click(function () {
            if (numBahias > 1) numBahias--;
            actualizarPreviewBahias();
            actualizarResumen();
        });

        $('.asis-turno-card').click(function () {
            $('.asis-turno-card').removeClass('active');
            $(this).addClass('active');
            numTurnos = parseInt($(this).data('turnos'));
            actualizarResumen();
        });

        $('.asis-dia-pill').click(function () {
            $(this).toggleClass('active');
            actualizarResumen();
        });

        actualizarPreviewBahias();
        actualizarResumen();

        $('#asistente_form').submit(function (e) {
            e.preventDefault();

            var dias = $('.asis-dia-pill.active').map(function () { return $(this).data('dia'); }).get();

            if (dias.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Selecciona al menos un día de trabajo', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                return;
            }

            var $btn = $('#generarBtn');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Generando...');

            $.ajax({
                url: "{{ tenant_url('tenant.configuracion.asistente.generar') }}",
                type: 'POST',
                data: { num_bahias: numBahias, num_turnos: numTurnos, dias: dias },
                dataType: 'json',
                success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Tu taller ya está listo!',
                        text: data.success,
                        confirmButtonText: 'Ir al panel',
                        confirmButtonColor: '#E52320',
                    }).then(function () {
                        window.location.href = "{{ tenant_url('tenant.home') }}";
                    });
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo generar la configuración.';
                    Swal.fire({ icon: 'error', title: msg });
                    $btn.prop('disabled', false).html('<i class="fas fa-wand-magic-sparkles mr-1"></i> Generar mi configuración');
                }
            });
        });
    });
</script>
@endsection
