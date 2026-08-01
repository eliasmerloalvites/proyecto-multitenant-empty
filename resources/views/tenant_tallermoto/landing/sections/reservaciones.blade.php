<!-- Oscurecer fondo
    <div class="absolute inset-0 z-10 "></div>-->

<!-- Gradiente lateral -->
<main class="relative  overflow-hidden">
    {{-- <div class="absolute inset-0 z-20
        bg-gradient-to-l
        from-slate-700 v ">
    </div> --}}

    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">

        <section id="reservas-main" class="py-20 relative overflow-hidden">
            <div
                class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-600/5 rounded-full blur-[120px] pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-12 border-b border-white/5 pb-8">
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-950/50 border border-emerald-500/30 rounded-full text-[10px] font-bold uppercase tracking-widest text-emerald-400">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                            Sistema de Bahías Activo
                        </div>
                        <h2 class="text-3xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-tighter">
                            Centro de <span class="text-brand-500">Agendamiento</span>
                        </h2>
                        <p class="text-xs text-gray-500 max-w-md">
                            Selecciona un bloque horario disponible. Nuestro sistema asigna automáticamente la bahía técnica mejor equipada para tu tipo de moto.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <!-- SELECTOR DE SEDE / LOCAL -->
                        <div class="flex items-center gap-2 bg-slate-900/50 p-2 rounded-2xl border border-white/5">
                            <div class="w-8 h-8 bg-brand-600/20 text-brand-400 rounded-xl flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] text-gray-500 font-bold uppercase">Sede / Local</span>
                                <select id="select_sede" onchange="cambiarSede(this.value)" class="bg-transparent text-xs text-white font-bold outline-none cursor-pointer pr-2">
                                    @foreach($locales as $local)
                                        <option value="{{ $local->ALM_Id }}" class="bg-slate-900 text-white" {{ $idlocal == $local->ALM_Id ? 'selected' : '' }}>
                                            {{ $local->ALM_NombreAlmacen }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Semana Actual -->
                        <div class="flex items-center gap-4 bg-slate-900/50 p-2 rounded-2xl border border-white/5">
                            <div class="text-right">
                                <span class="block text-[10px] text-gray-500 font-bold uppercase">Semana Actual</span>
                                <span class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono">
                                    {{ \Carbon\Carbon::parse($fechaInicial)->format('d M') }} - {{ \Carbon\Carbon::parse($fechaFinal)->format('d M, Y') }}
                                </span>
                            </div>
                            <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} shadow-lg shadow-brand-600/20">
                                <i data-lucide="calendar" class="w-5 h-5"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <div class="lg:col-span-8 space-y-6">

                        <!-- Selector de Días de la Semana -->
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                            @foreach ($semana as $sem)
                                @php
                                    // Compara si este item de la semana es el que está seleccionado actualmente
                                    $esSeleccionado = $sem['fecha'] === $fechaSeleccionada;

                                    if ($esSeleccionado) {
                                        $dia = $sem; // Asignamos el día activo
                                    }
                                @endphp

                                <a href="{{ request()->fullUrlWithQuery(['fecha' => $sem['fecha']]) }}"
                                    class="{{ $esSeleccionado
                                        ? 'bg-brand-600 border border-brand-400/50 shadow-[0_0_20px_rgba(37,99,235,0.2)]'
                                        : 'bg-slate-900/40 border border-white/5 hover:border-white/20 group' }} p-3 rounded-2xl text-center transition-all block">

                                    {{-- Nombre del día --}}
                                    <span
                                        class="block text-[10px] font-bold uppercase {{ $esSeleccionado ? 'text-brand-100' : 'text-gray-500 group-hover:text-gray-300' }}">
                                        {{ Str::substr(ucfirst($sem['dia']), 0, 3) }}
                                    </span>

                                    {{-- Número del día --}}
                                    <span
                                        class="text-lg font-black italic {{ $esSeleccionado ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                                        {{ \Carbon\Carbon::parse($sem['fecha'])->format('d') }}
                                    </span>
                                </a>
                            @endforeach
                        </div>

                        <!-- Lista de Turnos y Bahías -->
                        <div class="space-y-4">
                            @foreach ($turnos as $turno)
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2 text-center md:text-left">
                                        <span
                                            class="block text-xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono tracking-tighter italic">
                                            {{ $turno->TUR_Descripcion }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-bold uppercase">
                                            {{ count($bahias) }} Bahías
                                        </span>
                                    </div>

                                    <div class="md:col-span-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach ($bahias as $bahia)
                                            @php
                                                // Usamos $dia que fue asignado arriba según el día seleccionado
                                                $nombreDiaSemana = ucfirst($dia['diaNormalizado'] ?? '');

                                                $habilitados =
                                                    isset($horarioprogramado[$nombreDiaSemana]) &&
                                                    in_array($turno->TUR_Id, $horarioprogramado[$nombreDiaSemana]);
                                            @endphp

                                            @if ($habilitados)
                                                @php
                                                    $fechaClave = $dia['fecha'];
                                                    $state = 'estado-disponible';
                                                    $statename = 'DISPONIBLE';
                                                    $cliente = '';

                                                    if (isset($reservas[$fechaClave][$turno->TUR_Id][$bahia->BAH_Id])) {
                                                        $resData =
                                                            $reservas[$fechaClave][$turno->TUR_Id][$bahia->BAH_Id];
                                                        $estadoReserva = $resData[0];
                                                        $cliente = $resData[2] ?? '';

                                                        if ($estadoReserva == 'APROBADO') {
                                                            $statename = 'OCUPADO';
                                                        } else {
                                                            $statename = 'PENDIENTE';
                                                        }
                                                    }
                                                @endphp

                                                @if ($statename == 'OCUPADO' || $statename == 'PENDIENTE')
                                                    <!-- Tarjeta Ocupada / Pendiente -->
                                                    <div class="relative group cursor-not-allowed">
                                                        <div
                                                            class="absolute inset-0 bg-red-500/5 rounded-2xl border border-red-500/20">
                                                        </div>
                                                        <div class="relative p-4 flex items-center justify-between">
                                                            <div class="flex items-center gap-3">
                                                                <div
                                                                    class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center text-red-500/50 border border-red-500/10">
                                                                    <i data-lucide="bike" class="w-5 h-5"></i>
                                                                </div>
                                                                <div>
                                                                    <h4
                                                                        class="text-xs font-black text-gray-400 uppercase">
                                                                        {{ $bahia->BAH_Nombre }}</h4>
                                                                    <p
                                                                        class="text-[10px] text-red-500/70 font-bold tracking-widest">
                                                                        {{ $statename }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <span
                                                                    class="block text-[9px] text-gray-600 font-bold">CLIENTE</span>
                                                                <span
                                                                    class="text-[10px] text-gray-500 font-mono">{{ $cliente }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Tarjeta Disponible -->
                                                    <button onclick="seleccionarBahia(this)"
                                                        data-fecha="{{ $dia['fecha'] }}"
                                                        data-fecha-formateada="{{ \Carbon\Carbon::parse($dia['fecha'])->locale('es')->isoFormat('dddd, DD [de] MMMM') }}"
                                                        data-turno="{{ $turno->TUR_Descripcion }}"
                                                        data-turnoId="{{ $turno->TUR_Id }}"
                                                        data-bahia="{{ $bahia->BAH_Nombre }}"
                                                        data-bahiaId="{{ $bahia->BAH_Id }}"
                                                        class="relative group overflow-hidden transition-all hover:-translate-y-1">
                                                        <div
                                                            class="absolute inset-0 bg-slate-900/40 group-hover:bg-brand-600/10 rounded-2xl border border-white/5 group-hover:border-brand-500/50 transition-all">
                                                        </div>
                                                        <div class="relative p-4 flex items-center justify-between">
                                                            <div class="flex items-center gap-3">
                                                                <div
                                                                    class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                                                    <i data-lucide="plus" class="w-5 h-5"></i>
                                                                </div>
                                                                <div class="text-left">
                                                                    <h4
                                                                        class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase">
                                                                        {{ $bahia->BAH_Nombre }}
                                                                    </h4>
                                                                    <p
                                                                        class="text-[10px] text-emerald-400 font-bold tracking-widest animate-pulse">
                                                                        {{ $statename }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <i data-lucide="chevron-right"
                                                                class="w-4 h-4 text-gray-700 group-hover:text-brand-500 transition-colors"></i>
                                                        </div>
                                                    </button>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div
                            class="glass-panel sticky top-24 rounded-3xl p-6 space-y-6 border border-white/10 shadow-2xl">
                            <h3
                                class="text-sm font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-widest border-b border-white/5 pb-4 flex items-center gap-2">
                                <i data-lucide="shopping-bag" class="w-4 h-4 text-brand-500"></i> Resumen de Reserva
                            </h3>

                            <!-- Inputs ocultos para enviar en tu formulario -->
                            <input type="hidden" id="input_fecha" name="fecha">
                            <input type="hidden" id="input_turno" name="turno">
                            <input type="hidden" id="input_bahia" name="bahia">

                            <div class="space-y-4">
                                <div
                                    class="flex justify-between items-center bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Fecha
                                        Seleccionada</span>
                                    <span id="resumen_fecha"
                                        class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono">
                                        Seleccione una bahía
                                    </span>
                                </div>

                                <div
                                    class="flex justify-between items-center bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Bloque
                                        Horario</span>
                                    <span id="resumen_turno"
                                        class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono font-black italic">
                                        --:--
                                    </span>
                                </div>

                                <div
                                    class="flex justify-between items-center bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Punto de
                                        Trabajo</span>
                                    <span id="resumen_bahia" class="text-xs text-brand-400 font-mono font-black">
                                        --
                                    </span>
                                </div>
                            </div>

                            <div class="bg-brand-600/5 border border-brand-500/20 rounded-2xl p-4">
                                <div class="flex gap-3">
                                    <i data-lucide="info" class="w-4 h-4 text-brand-400 shrink-0"></i>
                                    <p class="text-[10px] text-gray-400 leading-relaxed">
                                        Su reserva garantiza la prioridad en la bahía. Por favor, llegue 10 minutos
                                        antes para el registro de ingreso.
                                    </p>
                                </div>
                            </div>

                            <button type="button" id="btn_confirmar" disabled onclick="abrirModalRegistro()"
                                class="w-full bg-gradient-to-r from-brand-500 to-brand-400 hover:from-brand-400 hover:to-brand-500 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-brand-500/20 hover:shadow-xl hover:shadow-brand-500/40 flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                Confirmar Cita
                                <i data-lucide="arrow-right"
                                    class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                            </button>

                            <p class="text-center text-[9px] text-gray-600 font-bold uppercase tracking-tighter">
                                Sistema Asegurado • KAEL Technologies 2026
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>


    </div>
</main>

<!-- Modal de Registro de Cita -->
<div id="modalRegistroCita"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto">
    <div
        class="relative w-full max-w-lg bg-slate-900 border border-white/10 rounded-3xl shadow-2xl overflow-hidden p-6 space-y-6 animate-in fade-in zoom-in duration-200">

        <!-- Cabecera del Modal -->
        <div class="flex items-center justify-between border-b border-white/5 pb-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-brand-500/10 rounded-xl flex items-center justify-center text-brand-400 border border-brand-500/20">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider">Datos del Cliente</h3>
                    <p class="text-[11px] text-gray-400">Complete los datos para confirmar la reserva</p>
                </div>
            </div>
            <button onclick="cerrarModalRegistro()"
                class="text-gray-400 hover:text-white p-2 rounded-xl hover:bg-white/5 transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Banner con el Resumen Seleccionado -->
        <div class="bg-slate-950/60 p-3 rounded-2xl border border-white/5 grid grid-cols-3 gap-2 text-center">
            <div>
                <span class="block text-[9px] text-gray-500 font-bold uppercase">Fecha</span>
                <span id="modal_summary_fecha" class="text-xs text-brand-400 font-mono font-bold">--</span>
            </div>
            <div>
                <span class="block text-[9px] text-gray-500 font-bold uppercase">Horario</span>
                <span id="modal_summary_turno" class="text-xs text-white font-mono font-bold italic">--</span>
            </div>
            <div>
                <span class="block text-[9px] text-gray-500 font-bold uppercase">Bahía</span>
                <span id="modal_summary_bahia" class="text-xs text-emerald-400 font-mono font-bold">--</span>
            </div>
        </div>

        <!-- Formulario -->
        <form id="formRegistroCita" action="{{ route('web.reservar.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Campos ocultos transferidos de la selección previa -->
            <input type="hidden" id="modal_input_fecha" name="RES_FechaProgramada">
            <input type="hidden" id="modal_input_turnoId" name="TUR_Id">
            <input type="hidden" id="modal_input_bahiaId" name="BAH_Id">
            <input type="hidden" id="modal_input_almacenId" name="ALM_Id" value="{{ $idlocal }}" >

            <!-- Fila 1: Placa y Teléfono -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 flex justify-between items-center">
                        <span>Placa de la Moto <span class="text-red-500">*</span></span>
                        <span id="placa_status" class="text-[9px] font-semibold text-brand-400 hidden">
                            <!-- Mensaje de estado dinámico -->
                        </span>
                    </label>
                    <div class="relative flex items-center">
                        <i data-lucide="hash"
                            class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2 z-10"></i>

                        <input type="text" id="input_placa" name="RES_Placa" required placeholder="ABC-123"
                            autocomplete="off"
                            class="w-full bg-slate-950/50 border border-white/10 rounded-xl py-2.5 pl-9 pr-12 text-xs text-white font-mono tracking-widest uppercase focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">

                        <!-- Botón de Búsqueda -->
                        <button type="button" onclick="buscarPlaca()" title="Buscar en sistema"
                            class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-brand-600/30 hover:bg-brand-500 text-brand-300 hover:text-white p-1.5 rounded-lg border border-brand-500/30 transition-all flex items-center justify-center">
                            <i data-lucide="search" id="icon_buscar_placa" class="w-3.5 h-3.5"></i>
                            <i data-lucide="loader-2" id="icon_loading_placa"
                                class="w-3.5 h-3.5 animate-spin hidden"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                        Celular / WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="phone"
                            class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="tel" name="RES_Celular" required placeholder="987654321"
                            class="w-full bg-slate-950/50 border border-white/10 rounded-xl py-2.5 pl-9 pr-3 text-xs text-white font-mono focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Fila 2: Nombre del Cliente -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                    Nombre del Cliente <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="user" class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="RES_Cliente" required placeholder="Ej. Juan Pérez"
                        class="w-full bg-slate-950/50 border border-white/10 rounded-xl py-2.5 pl-9 pr-3 text-xs text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                </div>
            </div>

            <!-- Fila 3: Descripción de la Moto -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                    Descripción / Modelo de la Moto <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="bike" class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="RES_Moto" required placeholder="Ej. Yamaha MT-03 2023 Negra"
                        class="w-full bg-slate-950/50 border border-white/10 rounded-xl py-2.5 pl-9 pr-3 text-xs text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                </div>
            </div>

            <!-- Fila 4: Trabajo a realizar -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                    Trabajo / Servicio Requerido <span class="text-red-500">*</span>
                </label>
                <textarea name="RES_Detalle" rows="3" required
                    placeholder="Detalle las fallas o el mantenimiento que necesita..."
                    class="w-full bg-slate-950/50 border border-white/10 rounded-xl p-3 text-xs text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all resize-none"></textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="cerrarModalRegistro()"
                    class="w-1/3 bg-white/5 hover:bg-white/10 text-gray-400 py-3 rounded-2xl text-xs font-bold uppercase transition-all">
                    Cancelar
                </button>
                <button type="submit" id="btn_guardar_reserva"
                    class="w-2/3 bg-gradient-to-r from-brand-500 to-brand-400 hover:from-brand-400 hover:to-brand-500 text-white py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-brand-500/20 hover:shadow-xl hover:shadow-brand-500/40 flex items-center justify-center gap-2 group">
                    Guardar Reserva
                    <i data-lucide="check" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Opcional: Buscar automáticamente al presionar 'Enter' en el input de placa
    document.getElementById('input_placa')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarPlaca();
        }
    });

    function cambiarSede(almacenId) {
        const url = new URL(window.location.href);
        url.searchParams.set('almacen', almacenId);
        window.location.href = url.toString();
    }

    // Variable global para guardar la selección actual
    let seleccionActual = null;

    function seleccionarBahia(elemento) {
        // Desmarcar selecciones previas
        document.querySelectorAll('.btn-bahia .indicador-bg').forEach(bg => {
            bg.classList.remove('bg-brand-600/20', 'border-brand-500');
            bg.classList.add('bg-slate-900/40', 'border-white/5');
        });

        // Marcar selección actual
        const bgActual = elemento.querySelector('.indicador-bg');
        if (bgActual) {
            bgActual.classList.remove('bg-slate-900/40', 'border-white/5');
            bgActual.classList.add('bg-brand-600/20', 'border-brand-500');
        }

        // Extraer datos
        seleccionActual = {
            fecha: elemento.getAttribute('data-fecha'),
            fechaFormateada: elemento.getAttribute('data-fecha-formateada'),
            turno: elemento.getAttribute('data-turno'),
            turnoId: elemento.getAttribute('data-turnoId'),
            bahia: elemento.getAttribute('data-bahia'),
            bahiaId: elemento.getAttribute('data-bahiaId')
        };

        // Actualizar resumen lateral
        document.getElementById('resumen_fecha').textContent = seleccionActual.fechaFormateada;
        document.getElementById('resumen_turno').textContent = seleccionActual.turno;
        document.getElementById('resumen_bahia').textContent = seleccionActual.bahia;

        // Habilitar botón de confirmación
        document.getElementById('btn_confirmar').removeAttribute('disabled');
    }

    function abrirModalRegistro() {
        if (!seleccionActual) return;

        // Poblar resumen dentro del modal
        document.getElementById('modal_summary_fecha').textContent = seleccionActual.fechaFormateada;
        document.getElementById('modal_summary_turno').textContent = seleccionActual.turno;
        document.getElementById('modal_summary_bahia').textContent = seleccionActual.bahia;

        // Inyectar datos en los inputs ocultos del formulario modal
        document.getElementById('modal_input_fecha').value = seleccionActual.fecha;
        document.getElementById('modal_input_turnoId').value = seleccionActual.turnoId;
        document.getElementById('modal_input_bahiaId').value = seleccionActual.bahiaId;

        // Mostrar modal
        const modal = document.getElementById('modalRegistroCita');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Re-renderizar iconos de Lucide si es necesario
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function cerrarModalRegistro() {
        const modal = document.getElementById('modalRegistroCita');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function buscarPlaca() {
        const placaInput = document.getElementById('input_placa');
        const placa = placaInput.value.trim();
        const statusTag = document.getElementById('placa_status');
        const iconSearch = document.getElementById('icon_buscar_placa');
        const iconLoading = document.getElementById('icon_loading_placa');

        if (!placa) {
            mostrarEstadoPlaca('Ingrese una placa para buscar', 'text-amber-400');
            return;
        }

        // Mostrar loader en el botón
        iconSearch.classList.add('hidden');
        iconLoading.classList.remove('hidden');
        mostrarEstadoPlaca('Buscando...', 'text-gray-400');

        try {
            // Reemplaza esta URL con la ruta de tu API en Laravel
            const response = await fetch(`/api/clientes/buscar-por-placa?placa=${encodeURIComponent(placa)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok && data.encontrado) {
                // Autopoblar campos si el vehículo/cliente existe
                document.getElementById('input_cliente').value = data.cliente || '';
                document.getElementById('input_celular').value = data.celular || '';
                document.getElementById('input_moto').value = data.moto_descripcion || '';

                mostrarEstadoPlaca('✓ Cliente encontrado', 'text-emerald-400');
            } else {
                mostrarEstadoPlaca('Placa nueva (no registrada)', 'text-amber-400');
            }

        } catch (error) {
            console.error('Error al consultar placa:', error);
            mostrarEstadoPlaca('Error al consultar', 'text-red-400');
        } finally {
            // Restaurar icono del botón
            iconSearch.classList.remove('hidden');
            iconLoading.classList.add('hidden');
        }
    }

    function mostrarEstadoPlaca(mensaje, claseColor) {
        const statusTag = document.getElementById('placa_status');
        statusTag.textContent = mensaje;
        statusTag.className = `text-[9px] font-semibold hidden ${claseColor}`;
        statusTag.classList.remove('hidden');
    }

    // 2. Interceptar el envío del formulario para procesar vía AJAX
    document.getElementById('formRegistroCita').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btnSubmit = document.getElementById('btn_guardar_reserva');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Guardando...';

        const formData = new FormData(this);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                cerrarModalRegistro();
                
                // Mostrar alerta de éxito visual
                Swal.fire({
                    icon: 'success',
                    title: '¡Reserva Confirmada!',
                    text: data.message || 'Tu cita ha sido agendada con éxito.',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Entendido'
                }).then(() => {
                    // Recargar para actualizar los bloques a "OCUPADO" / "PENDIENTE"
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Atención',
                    text: data.message || 'No se pudo registrar la reserva. Revisa los datos.',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de servidor',
                text: 'Ocurrió un problema al procesar la reserva.',
                background: '#0f172a',
                color: '#fff',
                confirmButtonColor: '#ef4444'
            });
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<span>Confirmar Reserva</span><i data-lucide="check-circle" class="w-4 h-4"></i>';
            if (window.lucide) lucide.createIcons();
        }
    });
</script>
