@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Guía de remisión')
@section('contenido')

    <style>
        .gr-resumen dt { color: var(--text-muted, #94A3B8); font-weight: 500; font-size: .85rem; }
        .gr-resumen dd { margin-bottom: .6rem; }

        /* El panel visible depende solo de qué radio está :checked - CSS puro,
           no depende de que ningún JS se ejecute correctamente. */
        .gr-transporte-modo { display: none; }
        #modoPrivado:checked ~ .gr-transporte-modo[data-modo="02"],
        #modoPublico:checked ~ .gr-transporte-modo[data-modo="01"] {
            display: block;
        }

        /* Botones de modo = labels de radios ocultos; el estilo "activo"
           tambien es CSS puro via :checked, no una clase manejada por JS. */
        .modo-radio { position: absolute; opacity: 0; width: 0; height: 0; }
        .modo-label {
            cursor: pointer;
            margin-bottom: 0;
            text-align: center;
        }
        #modoPrivado:checked ~ .modo-labels label[for="modoPrivado"],
        #modoPublico:checked ~ .modo-labels label[for="modoPublico"] {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #fff !important;
        }
    </style>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1">Emitir guía de remisión</h5>
                    <p class="text-muted mb-4">
                        Traslado de los productos de la venta a <strong>{{ $venta->CLI_Nombre }}</strong>
                        (motivo SUNAT: Venta)
                    </p>

                    @if (!empty($problemasSede))
                        <div class="alert alert-warning">
                            @foreach ($problemasSede as $problema)
                                <div>{{ $problema }}</div>
                            @endforeach
                            <a href="{{ tenant_url('tenant.configuracion.sede.index') }}" class="alert-link">
                                Configurar en Sedes
                            </a>
                        </div>
                    @endif

                    {{-- method="POST" es respaldo: la misma URL responde a GET (mostrar
                         el formulario) y a POST (crear la guia). Sin esto, si el submit
                         de abajo no llega a interceptar a tiempo, el navegador cae a un
                         GET nativo y recarga el propio formulario en blanco - se ve como
                         "no pasa nada". --}}
                    <form id="formGuiaRemision" method="POST" action="{{ url()->current() }}" onsubmit="return enviarGuiaRemision(event)">
                        @csrf

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Fecha de traslado</label>
                                <input type="date" class="form-control" id="fecha_traslado" name="fecha_traslado"
                                    value="{{ now()->addDay()->toDateString() }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Peso total</label>
                                <input type="number" step="0.001" min="0.001" class="form-control" id="peso_total"
                                    name="peso_total" placeholder="0.000" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Unidad</label>
                                <select class="form-control" id="und_peso" name="und_peso">
                                    <option value="KGM">KGM</option>
                                    <option value="TNE">TNE</option>
                                </select>
                            </div>
                        </div>

                        <label class="d-block mb-2">Punto de partida</label>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Departamento</label>
                                <select class="form-control ubigeo-departamento" data-destino="partida">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Provincia</label>
                                <select class="form-control ubigeo-provincia" data-destino="partida" disabled>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Distrito</label>
                                <select class="form-control ubigeo-distrito" data-destino="partida" disabled>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Ubigeo</label>
                                <input type="text" class="form-control" id="ubigeo_partida" name="ubigeo_partida"
                                    value="{{ $venta->ALM_Ubigeo }}" maxlength="6" readonly required>
                            </div>
                            <div class="form-group col-md-9">
                                <label>Dirección</label>
                                <input type="text" class="form-control" id="direccion_partida" name="direccion_partida"
                                    value="{{ $venta->ALM_Direccion }}" required>
                            </div>
                        </div>

                        <label class="d-block mb-2">Punto de llegada</label>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Departamento</label>
                                <select class="form-control ubigeo-departamento" data-destino="llegada">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Provincia</label>
                                <select class="form-control ubigeo-provincia" data-destino="llegada" disabled>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Distrito</label>
                                <select class="form-control ubigeo-distrito" data-destino="llegada" disabled>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Ubigeo</label>
                                <input type="text" class="form-control" id="ubigeo_llegada" name="ubigeo_llegada"
                                    placeholder="Se completa solo" maxlength="6" readonly required>
                            </div>
                            <div class="form-group col-md-9">
                                <label>Dirección</label>
                                <input type="text" class="form-control" id="direccion_llegada" name="direccion_llegada"
                                    value="{{ $venta->CLI_Direccion }}" required>
                            </div>
                        </div>

                        <label class="d-block mb-2">Transporte</label>

                        {{-- Radios nativos ocultos: el valor real del campo se rige por
                             cual esta :checked, con CSS puro (sin depender de JS para
                             funcionar). Deben ser hermanos directos de .modo-labels y de
                             los paneles .gr-transporte-modo para que el selector ~ aplique. --}}
                        <input type="radio" class="modo-radio" id="modoPrivado" name="modo_transporte" value="02" checked>
                        <input type="radio" class="modo-radio" id="modoPublico" name="modo_transporte" value="01">

                        <div class="btn-group d-flex mb-3 modo-labels">
                            <label for="modoPrivado" class="btn btn-outline-primary flex-fill modo-label">
                                Privado (vehículo propio)
                            </label>
                            <label for="modoPublico" class="btn btn-outline-primary flex-fill modo-label">
                                Público (transportista contratado)
                            </label>
                        </div>

                        <div class="gr-transporte-modo" data-modo="02">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Placa del vehículo</label>
                                    <input type="text" class="form-control" name="vehiculo_placa" placeholder="ABC-123">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Doc. conductor</label>
                                    <select class="form-control" name="conductor_tipo_doc">
                                        <option value="1">DNI</option>
                                        <option value="4">C.E.</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>N° documento</label>
                                    <input type="text" class="form-control" name="conductor_numero">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Nombres</label>
                                    <input type="text" class="form-control" name="conductor_nombres">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Apellidos</label>
                                    <input type="text" class="form-control" name="conductor_apellidos">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Licencia de conducir <span class="text-muted">(opcional)</span></label>
                                <input type="text" class="form-control" name="conductor_licencia">
                            </div>
                        </div>

                        <div class="gr-transporte-modo" data-modo="01">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label>Tipo doc.</label>
                                    <select class="form-control" name="transportista_tipo_doc">
                                        <option value="6">RUC</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>N° documento</label>
                                    <input type="text" class="form-control" name="transportista_numero">
                                </div>
                                <div class="form-group col-md-7">
                                    <label>Razón social</label>
                                    <input type="text" class="form-control" name="transportista_razon_social">
                                </div>
                            </div>
                        </div>

                        <label class="d-block mb-2 mt-3">Productos a trasladar</label>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $it)
                                        <tr>
                                            <td>{{ $it->PRO_Nombre }}</td>
                                            <td class="text-center">{{ rtrim(rtrim(number_format($it->DEV_Cantidad, 2), '0'), '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end" style="gap:.5rem">
                            <a href="{{ tenant_url('tenant.ventas.venta.index') }}" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="btnEmitir"
                                {{ !empty($problemasSede) ? 'disabled' : '' }}>
                                <i class="fa fa-truck"></i> Emitir guía de remisión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body gr-resumen">
                    <h6 class="mb-3">Venta</h6>
                    <dl class="row mb-0">
                        <dt class="col-5">Cliente</dt>
                        <dd class="col-7">{{ $venta->CLI_Nombre }}</dd>

                        <dt class="col-5">Documento cliente</dt>
                        <dd class="col-7">{{ $venta->CLI_NumDocumento }}</dd>

                        <dt class="col-5">Sede de partida</dt>
                        <dd class="col-7">{{ $venta->ALM_NombreAlmacen }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <script>
        // El cambio de modo (privado/publico) ya no depende de JS: los radios
        // ocultos + CSS (:checked ~ ...) resuelven tanto el resaltado del
        // boton como que panel de campos se muestra.
        //
        // El envio del formulario esta enganchado por onsubmit="" directo en
        // el <form> (no por un listener registrado via jQuery/$(document).ready),
        // a proposito: asi no depende de que jQuery ni ningun otro script de
        // la pagina termine de cargar/ejecutarse sin errores antes que este.
        // Usa fetch() nativo por la misma razon.
        function enviarGuiaRemision(event) {
            event.preventDefault();

            var form = event.target;
            var btn = document.getElementById('btnEmitir');
            var textoOriginal = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Emitiendo...';

            function avisar(icono, titulo, texto) {
                if (window.Swal) {
                    return Swal.fire({ icon: icono, title: titulo, text: texto || '' });
                }
                alert(titulo + (texto ? ': ' + texto : ''));
                return Promise.resolve();
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    var r = result.data;

                    if (!result.ok || !r.success) {
                        avisar('error', 'No se pudo emitir', r.error || 'Revisa los datos del formulario.');
                        btn.disabled = false;
                        btn.innerHTML = textoOriginal;
                        return;
                    }

                    avisar('success', 'Guía de remisión creada', r.guia + ' se está enviando a SUNAT.')
                        .then(function () {
                            window.location.href = '{{ tenant_url("tenant.ventas.guiaremision.index") }}';
                        });
                })
                .catch(function (err) {
                    avisar('error', 'No se pudo emitir', 'Error de conexión: ' + err.message);
                    btn.disabled = false;
                    btn.innerHTML = textoOriginal;
                });

            return false;
        }

        // Selects en cascada Departamento -> Provincia -> Distrito, para no
        // pedirle a nadie que se sepa de memoria el codigo de ubigeo INEI de
        // 6 digitos. El dataset es estatico (no cambia entre tenants) y se
        // carga una sola vez desde /data/ubigeo-peru.json.
        (function () {
            var UBIGEO_URL = '{{ asset_root('data/ubigeo-peru.json') }}';
            var datosUbigeo = null;

            var partidaPreseleccion = {
                departamento: @json($venta->ALM_Departamento),
                provincia: @json($venta->ALM_Provincia),
                distrito: @json($venta->ALM_Distrito)
            };

            function llenarSelect(select, valores) {
                select.innerHTML = '';
                select.appendChild(new Option('Seleccione...', ''));
                valores.forEach(function (v) {
                    select.appendChild(new Option(v, v));
                });
                select.disabled = valores.length === 0;
            }

            // Los datos de Sede son texto libre (escrito a mano en el
            // formulario de Configuracion > Sedes), asi que la coincidencia
            // con el dataset se hace sin importar mayusculas/minusculas.
            function buscarClaveInsensible(obj, valor) {
                if (!valor) return null;
                var buscado = valor.trim().toLowerCase();
                var claves = Object.keys(obj);
                for (var i = 0; i < claves.length; i++) {
                    if (claves[i].toLowerCase() === buscado) return claves[i];
                }
                return null;
            }

            function activarCascada(destino, preseleccion) {
                var selDep = document.querySelector('.ubigeo-departamento[data-destino="' + destino + '"]');
                var selProv = document.querySelector('.ubigeo-provincia[data-destino="' + destino + '"]');
                var selDist = document.querySelector('.ubigeo-distrito[data-destino="' + destino + '"]');
                var inputUbigeo = document.getElementById('ubigeo_' + destino);

                selDep.addEventListener('change', function () {
                    var provincias = datosUbigeo[selDep.value] ? Object.keys(datosUbigeo[selDep.value]) : [];
                    llenarSelect(selProv, provincias);
                    llenarSelect(selDist, []);
                    inputUbigeo.value = '';
                });

                selProv.addEventListener('change', function () {
                    var dep = datosUbigeo[selDep.value];
                    var distritos = dep && dep[selProv.value] ? Object.keys(dep[selProv.value]) : [];
                    llenarSelect(selDist, distritos);
                    inputUbigeo.value = '';
                });

                selDist.addEventListener('change', function () {
                    var dep = datosUbigeo[selDep.value];
                    var prov = dep ? dep[selProv.value] : null;
                    inputUbigeo.value = (prov && prov[selDist.value]) ? prov[selDist.value] : '';
                });

                if (!preseleccion) {
                    return;
                }

                var depKey = buscarClaveInsensible(datosUbigeo, preseleccion.departamento);
                if (!depKey) {
                    return;
                }
                selDep.value = depKey;
                selDep.dispatchEvent(new Event('change'));

                var provKey = buscarClaveInsensible(datosUbigeo[depKey], preseleccion.provincia);
                if (!provKey) {
                    return;
                }
                selProv.value = provKey;
                selProv.dispatchEvent(new Event('change'));

                var distKey = buscarClaveInsensible(datosUbigeo[depKey][provKey], preseleccion.distrito);
                if (!distKey) {
                    return;
                }
                selDist.value = distKey;
                selDist.dispatchEvent(new Event('change'));
            }

            fetch(UBIGEO_URL)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    datosUbigeo = data;
                    var departamentos = Object.keys(data);

                    document.querySelectorAll('.ubigeo-departamento').forEach(function (select) {
                        llenarSelect(select, departamentos);
                        select.disabled = false;
                    });

                    activarCascada('partida', partidaPreseleccion);
                    activarCascada('llegada', null);
                })
                .catch(function (err) {
                    console.error('No se pudo cargar el catalogo de ubigeo (/data/ubigeo-peru.json):', err);
                });
        })();
    </script>
@endsection
