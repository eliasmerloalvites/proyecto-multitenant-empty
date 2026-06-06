@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Reservaciones')
@section('contenido')

    <style>
        .scheduler-topbar {
            padding: 0px 10px;
            margin-bottom: 10px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .scheduler-title-group {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .scheduler-logo {
            width: 50px;
            height: 50px;

            border-radius: 12px;

            background: #eef4ff;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #2563eb;
            font-size: 24px;
        }

        .scheduler-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .scheduler-subtitle {
            font-size: 14px;
            color: #475569;
            margin-top: 2px;
        }

        .scheduler-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .scheduler-btn {
            border: none;
            background: white;
            height: 42px;
            min-width: 42px;
            border-radius: 12px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);

            font-weight: 600;
            color: #334155;

            transition: .2s;
        }

        .scheduler-btn:hover {
            transform: translateY(-2px);
        }

        .scheduler-date {
            display: flex;
            align-items: center;
            gap: 10px;

            height: 42px;

            padding: 0 18px;

            background: white;

            border-radius: 12px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);

            color: #334155;
            font-weight: 600;
        }

        .scheduler-new {
            background: #2563eb;
            color: white;
            border: none;

            height: 42px;

            padding: 0 20px;

            border-radius: 12px;

            font-weight: 600;

            transition: .2s;
        }

        .scheduler-new:hover {
            background: #1d4ed8;
        }

        .kpi-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            transition: .3s;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
            height: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .12);
        }

        .kpi-card .card-body {
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .kpi-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
            background: red;
            color: white;
        }

        .kpi-info h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .kpi-info h6 {
            margin: 0;
            font-size: 14px;
            color: #64748b;
        }

        .kpi-info small {
            color: #94a3b8;
        }

        .kpi-icon.kpi-blue {
            background: #3b82f6;
        }

        .kpi-icon.kpi-green {
            background: #22c55e;
        }

        .kpi-icon.kpi-orange {
            background: #f59e0b;
        }

        .kpi-icon.kpi-purple {
            background: #8b5cf6;
        }

        .scheduler-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, .08);
            padding: 20px;
        }

        .scheduler-table {
            min-width: 1400px;
        }

        .scheduler-table thead th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .turno-cell {
            width: 140px;
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
        }

        .turno-hora {
            font-size: 18px;
            font-weight: 700;
        }

        .turno-detalle {
            font-size: 12px;
            color: #64748b;
        }

        .bahias-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: flex-start;
        }

        .bahia-box {
            width: 150px;
            min-height: 80px;
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 0;
            border-left: 4px solid transparent;
        }

        .estado-disponible {
            background: #ecfdf5;
            border-left-color: #22c55e;
        }

        .estado-pendiente {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }

        .estado-ocupado {
            background: #fee2e2;
            border-left-color: #ef4444;
        }

        .estado-mantenimiento {
            background: #f3f4f6;
            border-left-color: #94a3b8;
        }

        .bahia-card {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bahia-img img {
            width: 45px;
            height: auto;
        }

        .bahia-data {
            flex: 1;
        }

        .bahia-cliente-estado-disponible {
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #22c55e;
        }

        .bahia-cliente-estado-pendiente {
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #f59e0b;
        }

        .bahia-cliente-estado-ocupado {
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #ef4444;
        }

        .bahia-cliente-estado-mantenimiento {
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .bahia-estado {
            margin-bottom: 4px;
        }

        .estado-badge-estado-disponible {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            color: white;
            background: #22c55e;
        }

        .estado-badge-estado-pendiente {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            color: white;
            background: #f59e0b;
        }

        .estado-badge-estado-ocupado {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            color: white;
            background: #ef4444;
        }

        .estado-badge-estado-mantenimiento {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            color: white;
            background: #94a3b8;
        }


        .bahia-codigo {
            font-size: 10px;
            font-weight: 600;
        }

        .ocupacion-badge {
            font-size: 11px;
            background: #e2e8f0;
            padding: 3px 8px;
            border-radius: 20px;
            margin-top: 5px;
            display: inline-block;
        }

        .day-date {
            font-size: 18px;
            font-weight: 700;
        }

        .day-name {
            font-size: 13px;
            color: #64748b;
        }






        .reservation-modal {
            border: none;
            border-radius: 24px;
            overflow: hidden;
        }

        .reservation-header {
            border: none;
            padding: 10px 14px;
        }

        .reservation-header-icon {
            width: 36px;
            height: 36px;
            border-radius: 14px;
            background: #eef4ff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 18px;
            color: #2563eb;

            margin-right: 15px;
        }

        .reservation-title {
            font-size: 22px;
            line-height: 1.2;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }

        .reservation-subtitle {
            color: #64748b;
            font-size: 13px;
            margin-top: 5px;
        }

        .reservation-close {
            font-size: 35px;
            color: #64748b;
            opacity: 1;
        }

        .reservation-info {
            background: #f8fbff;
            border-left: 5px solid #2563eb;
            border-radius: 10px;
            padding: 10px 10px;
            margin-bottom: 10px;
        }

        .reservation-section-title {
            font-size: 14px;
            color: #1e3a8a;
        }

        .reservation-section-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 5px;
        }

        .reservation-detail {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .detail-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eef4ff;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #2563eb;
            font-size: 18px;
        }

        .reservation-detail small {
            display: block;
            color: #64748b;
        }

        .reservation-detail strong {
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
        }

        .reservation-form-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 10px;
        }

        .reservation-input {
            height: 36px;
            font-size: 14px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
        }

        textarea.reservation-input {
            height: auto;
        }

        .reservation-footer {
            border: none;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
        }

        .reservation-btn-danger {
            background: #fff5f5;
            color: #ef4444;
            border: 1px solid #fecaca;
            border-radius: 14px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
        }

        .reservation-btn-success {
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 14px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
        }

        .reservation-btn-success:hover {
            background: #16a34a;
            color: white;
        }
    </style>


    <div class="col-md-12 scheduler-topbar">

        <div class="scheduler-title-group">

            <div class="scheduler-logo">
                <i class="fas fa-calendar-alt"></i>
            </div>

            <div>
                <h3 class="scheduler-title">
                    Reservaciones Semanales
                </h3>

                <div class="scheduler-subtitle">
                    {{ $localFirst->ALM_NombreAlmacen }}
                </div>
            </div>

        </div>

        <div class="scheduler-actions">

            <button class="scheduler-btn">
                Hoy
            </button>

            <div class="scheduler-date">
                <i class="fas fa-calendar-alt"></i>

                {{ \Carbon\Carbon::parse($fechaInicial)->format('d M') }}
                -
                {{ \Carbon\Carbon::parse($fechaFinal)->format('d M, Y') }}
            </div>

        </div>

    </div>

    <div class="col-md-3 mb-3">
        <div class="card kpi-card">
            <div class="card-body">

                <div class="kpi-icon kpi-blue">
                    <i class="fas fa-calendar"></i>
                </div>

                <div class="kpi-info">
                    <h2>{{ count($reservas ?? []) }}</h2>
                    <h6>Reservaciones</h6>
                    <small>Esta semana</small>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card kpi-card">
            <div class="card-body">

                <div class="kpi-icon kpi-green">
                    <i class="fa fa-motorcycle"></i>
                </div>

                <div class="kpi-info">
                    <h2>{{ count($bahias) }}</h2>
                    <h6>Bahías</h6>
                    <small>Configuradas</small>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card kpi-card">
            <div class="card-body">

                <div class="kpi-icon kpi-orange">
                    <i class="fas fa-clock"></i>
                </div>

                <div class="kpi-info">
                    <h2>{{ count($turnos) }}</h2>
                    <h6>Turnos</h6>
                    <small>Programados</small>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card kpi-card">
            <div class="card-body">

                <div class="kpi-icon kpi-purple">
                    <i class="fas fa-calendar-check"></i>
                </div>

                <div class="kpi-info">
                    <h2>{{ count($semana) }}</h2>
                    <h6>Días</h6>
                    <small>Mostrados</small>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-12 scheduler-container">
        <div class="table-responsive">
            @if (count($horarioprogramado) > 0)
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>Turno / Día</th>
                            @foreach ($semana as $sem)
                                <th>
                                    <div class="day-header">
                                        <div class="day-date">
                                            {{ ucfirst($sem['mesdia']) }}
                                        </div>

                                        <div class="day-name">
                                            {{ ucfirst($sem['dia']) }}
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($turnos as $turno)
                            <tr>
                                <td class="turno-cell">
                                    <div class="turno-hora">
                                        {{ $turno->TUR_Descripcion }}
                                    </div>

                                    <div class="ocupacion-badge">
                                        {{ count($bahias) }} Bahías
                                    </div>
                                </td>
                                @foreach ($semana as $sem)
                                    <td>
                                        <div class="bahias-container">
                                            @foreach ($bahias as $bahia)
                                                @php
                                                    $habilitados =
                                                        isset($horarioprogramado[ucfirst($sem['diaNormalizado'])]) &&
                                                        in_array(
                                                            $turno->TUR_Id,
                                                            $horarioprogramado[ucfirst($sem['diaNormalizado'])],
                                                        );
                                                @endphp

                                                @if ($habilitados)
                                                    @php
                                                        $state = 'estado-disponible';
                                                        $idreservacion = '';
                                                        $statename = 'Disponible';
                                                        $getClass = 'getDataReservacion';
                                                        $modal = '#revervacionModal';
                                                        $cliente = '';
                                                        if (
                                                            isset($reservas[ucfirst($sem['fecha'])][$turno->TUR_Id]) &&
                                                            isset(
                                                                $reservas[ucfirst($sem['fecha'])][$turno->TUR_Id][
                                                                    $bahia->BAH_Id
                                                                ],
                                                            )
                                                        ) {
                                                            $state =
                                                                $reservas[ucfirst($sem['fecha'])][$turno->TUR_Id][
                                                                    $bahia->BAH_Id
                                                                ][0];
                                                            $idreservacion =
                                                                $reservas[ucfirst($sem['fecha'])][$turno->TUR_Id][
                                                                    $bahia->BAH_Id
                                                                ][1];
                                                            $cliente =
                                                                $reservas[ucfirst($sem['fecha'])][$turno->TUR_Id][
                                                                    $bahia->BAH_Id
                                                                ][2];

                                                            if ($state == 'APROBADO') {
                                                                $state = 'estado-ocupado';
                                                                $statename = 'Ocupado';
                                                                $getClass = 'getDataReservacionPendiente';
                                                                $modal = '#revervacionEditModal';
                                                            } else {
                                                                $state = 'estado-pendiente';
                                                                $statename = 'Pendiente';
                                                                $getClass = 'getDataReservacionPendiente';
                                                                $modal = '#revervacionEditModal';
                                                            }
                                                        }

                                                    @endphp
                                                    <div class="bahia-box {{ $state }} {{ $getClass }}"
                                                        data-idreservacion="{{ $idreservacion }}"
                                                        data-bahiaid="{{ $bahia->BAH_Id }}"
                                                        data-turno="{{ $turno->TUR_Id }}"
                                                        data-mecanico="{{ $bahia->Nombre }}"
                                                        data-bahianombre="{{ $bahia->BAH_Nombre }}"
                                                        data-fechaprogramada="{{ $sem['fecha'] }}"
                                                        data-fechaprogramadadata="{{ $sem['fecha'] }} | Turno {{ $turno->TUR_Descripcion }}"
                                                        data-toggle="modal" data-target="{{ $modal }}">
                                                        <div class="bahia-card">
                                                            <div class="bahia-img">
                                                                <img
                                                                    src="{{ asset_root(
                                                                        'images/tipo_vehiculo/' .
                                                                            match ($statename) {
                                                                                'Ocupado' => '2',
                                                                                'Pendiente' => '1',
                                                                                default => '0',
                                                                            } .
                                                                            '.png',
                                                                    ) }}">
                                                            </div>

                                                            <div class="bahia-data">

                                                                @if ($cliente)
                                                                    <div class="bahia-cliente-{{ $state }}">
                                                                        {{ strtoupper($cliente) }}
                                                                    </div>
                                                                @else
                                                                    <div class="bahia-cliente-{{ $state }}">

                                                                    </div>
                                                                @endif

                                                                <div class="bahia-estado">
                                                                    @if ($statename == 'Ocupado' || $statename == 'Pendiente')
                                                                        <span class="estado-badge-{{ $state }}">
                                                                            {{ $statename }}
                                                                        </span>
                                                                    @else
                                                                        <span class="estado-badge-{{ $state }}">
                                                                            Disponible
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                <div class="bahia-codigo">
                                                                    <i class="fa fa-user"></i>
                                                                    {{ $bahia->BAH_Nombre }}
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="bahia-box estado-mantenimiento">
                                                        <div class="bahia-card">
                                                            <div class="bahia-img">
                                                                <img src="{{ asset_root('images/tipo_vehiculo/3.png') }}">
                                                            </div>

                                                            <div class="bahia-data">
                                                                <div class="bahia-cliente">

                                                                </div>

                                                                <div class="bahia-estado">
                                                                    <span class="estado-badge-estado-mantenimiento">
                                                                        Inactivo
                                                                    </span>
                                                                </div>

                                                                <div class="bahia-codigo">
                                                                    <i class="fa fa-user"></i>
                                                                    {{ $bahia->BAH_Nombre }}
                                                                </div>

                                                            </div>

                                                        </div>


                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <tr>
                    <td colspan="8" class="text-center">No hay turnos disponibles</td>
                </tr>

            @endif
        </div>
    </div>

    {{-- Modal para Reservar --}}
    <div class="modal fade" id="revervacionModal" tabindex="-1" role="dialog" aria-labelledby="revervacionModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-md" role="document">

            <form id="reservacion_form" method="PUT">

                @csrf

                <div class="modal-content reservation-modal">

                    {{-- HEADER --}}
                    <div class="modal-header reservation-header">

                        <div class="d-flex align-items-center">

                            <div class="reservation-header-icon">
                                <i class="fas fa-tools"></i>
                            </div>

                            <div>

                                <h3 class="reservation-title">
                                    Reserva tu mantenimiento
                                </h3>

                                <div class="reservation-subtitle">
                                    Completa los datos para crear tu reservación
                                </div>

                            </div>

                        </div>

                        <button type="button" class="close reservation-close" data-dismiss="modal">

                            <span>&times;</span>

                        </button>

                    </div>

                    {{-- BODY --}}
                    <div class="modal-body">

                        {{-- INFORMACIÓN RESERVA --}}
                        <div class="reservation-info">

                            <div class="reservation-section-title">

                                <i class="fas fa-calendar-check"></i>

                                Información de la reserva

                            </div>

                            <div class="row mt-2">

                                <div class="col-md-6">

                                    <div class="reservation-detail">

                                        <div class="detail-icon">
                                            <i class="fas fa-warehouse"></i>
                                        </div>

                                        <div>

                                            <small>Bahía</small>

                                            <strong id="ver_BAH_Nombre"></strong>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="reservation-detail">

                                        <div class="detail-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>

                                        <div>

                                            <small>Fecha y turno</small>

                                            <strong id="ver_FechaProgramada"></strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- CAMPOS HIDDEN --}}
                        <input type="hidden" id="bahia_id" name="BAH_Id">
                        <input type="hidden" id="turno_Id" name="TUR_Id">
                        <input type="hidden" id="fechaprogramad_id" name="RES_FechaProgramada">
                        <input type="hidden" id="local_id" name="ALM_Id" value="{{ $localFirst->ALM_Id }}">

                        {{-- DATOS --}}
                        <div class="reservation-form-card">

                            <div class="reservation-section-title">

                                <i class="fas fa-user"></i>

                                Datos solicitados

                            </div>

                            <div class="reservation-section-subtitle">
                                Por favor complete todos los campos obligatorios
                            </div>

                            <hr>

                            {{-- MOTO --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size: 13px;">
                                    Motocicleta *
                                </label>

                                <input type="text" name="RES_Moto" onkeyup="this.value=this.value.toUpperCase();"
                                    class="form-control reservation-input" placeholder="Ej. Yamaha FZ 2.0">

                            </div>

                            {{-- CLIENTE --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size: 13px;">
                                    Nombre solicitante *
                                </label>

                                <input type="text" name="RES_Cliente" onkeyup="this.value=this.value.toUpperCase();"
                                    class="form-control reservation-input" placeholder="Ingrese su nombre completo">

                            </div>

                            {{-- CELULAR --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size: 13px;">
                                    Celular *
                                </label>

                                <input type="text" name="RES_Celular" class="form-control reservation-input"
                                    placeholder="Ej. 999888777">

                            </div>

                            {{-- DETALLE --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size: 13px;">
                                    Detalle del servicio *
                                </label>

                                <textarea name="RES_Detalle" rows="4" onkeyup="this.value=this.value.toUpperCase();"
                                    class="form-control reservation-input" placeholder="Describa el servicio que necesita"></textarea>

                            </div>

                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer reservation-footer">

                        <button type="button" id="btnInahibilitarReservacion" class="btn reservation-btn-danger">

                            <i class="fas fa-ban"></i>

                            Inhabilitar Reservación

                        </button>

                        <button type="button" id="btnGuardarReservacion" class="btn reservation-btn-success">

                            <i class="fas fa-save"></i>

                            Guardar Reservación

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- Modal para Editar Reservar --}}
    <div class="modal fade" id="revervacionEditModal" tabindex="-1" role="dialog"
        aria-labelledby="revervacionEditModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-md" role="document">

            <form id="reservacion_edit_form" action="{{ route('tenant.reservaciones.administracion.store') }}"
                method="POST">

                @csrf

                <div class="modal-content reservation-modal">

                    {{-- HEADER --}}
                    <div class="modal-header reservation-header">

                        <div class="d-flex align-items-center">

                            <div class="reservation-header-icon">
                                <i class="fas fa-edit"></i>
                            </div>

                            <div>

                                <h3 class="reservation-title">
                                    Actualizar reservación
                                </h3>

                                <div class="reservation-subtitle">
                                    Administra el estado y los datos de la reserva
                                </div>

                            </div>

                        </div>

                        <button type="button" class="close reservation-close" data-dismiss="modal">

                            <span>&times;</span>

                        </button>

                    </div>

                    {{-- BODY --}}
                    <div class="modal-body">

                        {{-- INFORMACIÓN RESERVA --}}
                        <div class="reservation-info">

                            <div class="reservation-section-title">

                                <i class="fas fa-calendar-check"></i>

                                Información de la reserva

                            </div>

                            <div class="row mt-2">

                                <div class="col-md-6">

                                    <div class="reservation-detail">

                                        <div class="detail-icon">
                                            <i class="fas fa-warehouse"></i>
                                        </div>

                                        <div>

                                            <small>Bahía</small>

                                            <strong id="edit_ver_BAH_Nombre"></strong>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="reservation-detail">

                                        <div class="detail-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>

                                        <div>

                                            <small>Fecha y turno</small>

                                            <strong id="edit_ver_FechaProgramada"></strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- HIDDEN INPUTS --}}
                        <input type="hidden" id="edit_reservacion_id" name="RES_Id">
                        <input type="hidden" id="edit_bahia_id" name="BAH_Id">
                        <input type="hidden" id="edit_turno_Id" name="TUR_Id">
                        <input type="hidden" id="edit_fechaprogramad_id" name="RES_FechaProgramada">

                        <input type="hidden" id="edit_local_id" name="ALM_Id" value="{{ $localFirst->ALM_Id }}">

                        {{-- FORMULARIO --}}
                        <div class="reservation-form-card">

                            <div class="reservation-section-title">

                                <i class="fas fa-user-edit"></i>

                                Datos de la reservación

                            </div>

                            <div class="reservation-section-subtitle">
                                Actualice la información del cliente o del servicio
                            </div>

                            <hr>

                            {{-- MOTO --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size:13px;">
                                    Motocicleta *
                                </label>

                                <input type="text" id="idRES_Moto" name="RES_Moto"
                                    onkeyup="this.value=this.value.toUpperCase();" class="form-control reservation-input">

                            </div>

                            {{-- CLIENTE --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size:13px;">
                                    Nombre solicitante *
                                </label>

                                <input type="text" id="idRES_Cliente" name="RES_Cliente"
                                    onkeyup="this.value=this.value.toUpperCase();" class="form-control reservation-input">

                            </div>

                            {{-- CELULAR --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size:13px;">
                                    Celular *
                                </label>

                                <input type="text" id="idRES_Celular" name="RES_Celular"
                                    class="form-control reservation-input">

                            </div>

                            {{-- DETALLE --}}
                            <div class="form-group" style="margin-bottom:2px">

                                <label style="font-size:13px;">
                                    Detalle del servicio *
                                </label>

                                <textarea id="idRES_Detalle" name="RES_Detalle" rows="4" onkeyup="this.value=this.value.toUpperCase();"
                                    class="form-control reservation-input"></textarea>

                            </div>

                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer reservation-footer">

                        <button type="button" id="btnRechazarReservacion" class="btn reservation-btn-danger">

                            <i class="fas fa-times-circle"></i>

                            Rechazar Reservación

                        </button>

                        <button type="button" id="btnAprobarReservacion" class="btn reservation-btn-success">
                            <i class="fas fa-check-circle"></i>
                            Aprobar Reservación
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            var table;
            $(document).ready(function() {
                $id = "{{ $localFirst->ALM_Id }}";
                $('#idCabReservacion').addClass('menu-open');
                $('#idReservacion').addClass('active');
                $('#idCabReservacionSemanal' + $id).addClass('active');

                $('.select2').select2()

                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                $('body').on('click', '.getDataReservacion', function() {
                    var Marcacion_id_ver = $(this).data('id');


                    var Bahiaid_id_ver = $(this).data('bahiaid');
                    var TurnoId_id_ver = $(this).data('turno');
                    var BahiaNombre_id_ver = $(this).data('bahianombre');
                    var Mecanico_id_ver = $(this).data('mecanico');
                    var FechaProgramada_id_ver = $(this).data('fechaprogramadadata');
                    var FechaProgramada = $(this).data('fechaprogramada');



                    $('#bahia_id').val(Bahiaid_id_ver);
                    $('#turno_Id').val(TurnoId_id_ver);
                    $('#fechaprogramad_id').val(FechaProgramada);
                    $('#ver_BAH_Nombre').text(BahiaNombre_id_ver);
                    // $('#ver_Mecanico').text(Mecanico_id_ver);
                    $('#ver_FechaProgramada').text(FechaProgramada_id_ver);
                });

                $('body').on('click', '.getDataReservacionPendiente', function() {
                    var Reservacion_id_ver = $(this).data('idreservacion');
                    var Bahiaid_id_ver = $(this).data('bahiaid');
                    var TurnoId_id_ver = $(this).data('turno');
                    var BahiaNombre_id_ver = $(this).data('bahianombre');
                    var Mecanico_id_ver = $(this).data('mecanico');
                    var FechaProgramada_id_ver = $(this).data('fechaprogramadadata');
                    var FechaProgramada = $(this).data('fechaprogramada');
                    $.get("{{ route('tenant.reservaciones.administracion.index') }}" + '/' +
                        Reservacion_id_ver + '/edit',
                        function(data) {
                            $('#idRES_Moto').val(data.RES_Moto);
                            $('#idRES_Cliente').val(data.RES_Cliente);
                            $('#idRES_Celular').val(data.RES_Celular);
                            document.getElementById('idRES_Detalle').value = data.RES_Detalle;
                        })

                    $('#edit_reservacion_id').val(Reservacion_id_ver);
                    $('#edit_bahia_id').val(Bahiaid_id_ver);
                    $('#edit_turno_Id').val(TurnoId_id_ver);
                    $('#edit_fechaprogramad_id').val(FechaProgramada);
                    $('#edit_ver_BAH_Nombre').text(BahiaNombre_id_ver);
                    // $('#edit_ver_Mecanico').text(Mecanico_id_ver);
                    $('#edit_ver_FechaProgramada').text(FechaProgramada_id_ver);
                });

                $('body').on('click', '.getDataReservacionOcupado', function() {
                    var Marcacion_id_ver = $(this).data('id');


                    var Bahiaid_id_ver = $(this).data('bahiaid');
                    var TurnoId_id_ver = $(this).data('turno');
                    var BahiaNombre_id_ver = $(this).data('bahianombre');
                    var Mecanico_id_ver = $(this).data('mecanico');
                    var FechaProgramada_id_ver = $(this).data('fechaprogramadadata');
                    var FechaProgramada = $(this).data('fechaprogramada');



                    $('#bahia_id').val(Bahiaid_id_ver);
                    $('#turno_Id').val(TurnoId_id_ver);
                    $('#fechaprogramad_id').val(FechaProgramada);
                    $('#ver_BAH_Nombre').text(BahiaNombre_id_ver);
                    $('#ver_Mecanico').text(Mecanico_id_ver);
                    $('#ver_FechaProgramada').text(FechaProgramada_id_ver);
                });

                $('#btnInahibilitarReservacion').on('click', function(e) {
                    e.preventDefault();

                    $('#reservacion_form input[name="RES_Moto"]').val('SIN MOTOCICLETA');
                    $('#reservacion_form input[name="RES_Cliente"]').val('INHABILITADO');
                    $('#reservacion_form input[name="RES_Celular"]').val('-');
                    $('#reservacion_form textarea[name="RES_Detalle"]').val('RESERVACIÓN INHABILITADA');

                    $.ajax({
                        data: $('#reservacion_form').serialize(),
                        url: "{{ route('tenant.reservaciones.administracion.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            console.log(data)
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                toast: true,
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false
                            });

                            Swal.fire({
                                title: '¡Reservación Inhabilitada!',
                                text: 'La bahía ha sido inhabilitada correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-success',
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                } else {
                                    location.reload();
                                }
                            });


                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al registrar la reservación',
                                toast: true,
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    });

                });

                $('#btnGuardarReservacion').on('click', function(e) {
                    e.preventDefault();
                    const form = document.getElementById('reservacion_form');
                    if (form.checkValidity()) {
                        $.ajax({
                            data: $('#reservacion_form').serialize(),
                            url: "{{ route('tenant.reservaciones.administracion.store') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                console.log(data)
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000,
                                    showConfirmButton: false
                                });

                                Swal.fire({
                                    title: '¡Reservación Guardada!',
                                    text: 'Nos estaremos comunicando contigo para confirmar tu cita.',
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'btn btn-success',
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    } else {
                                        location.reload();
                                    }
                                });


                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al registrar la reservación',
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        form.reportValidity();
                    }
                });


                $('#btnAprobarReservacion').on('click', function(e) {
                    e.preventDefault();
                    const form = document.getElementById('reservacion_edit_form');
                    if (form.checkValidity()) {
                        // Serializar el formulario
                        let formData = $('#reservacion_edit_form').serialize();

                        // Agregar parámetros adicionales
                        formData += '&RES_State=APROBADO';
                        $.ajax({
                            data: formData,
                            url: "{{ route('tenant.reservaciones.administracion.index') }}/" + $(
                                '#edit_reservacion_id').val(),
                            type: "PUT",
                            dataType: 'json',
                            success: function(data) {
                                console.log(data)
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000,
                                    showConfirmButton: false
                                });

                                Swal.fire({
                                    title: '¡Reservación Aprobada!',
                                    text: '',
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'btn btn-success',
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    } else {
                                        location.reload();
                                    }
                                });


                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al registrar la reservación',
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        form.reportValidity();
                    }
                });

                $('#btnRechazarReservacion').on('click', function(e) {
                    e.preventDefault();
                    const form = document.getElementById('reservacion_edit_form');
                    if (form.checkValidity()) {
                        // Serializar el formulario
                        let formData = $('#reservacion_edit_form').serialize();

                        // Agregar parámetros adicionales
                        formData += '&RES_State=RECHAZADO';
                        $.ajax({
                            data: formData,
                            url: "{{ route('tenant.reservaciones.administracion.index') }}/" + $(
                                '#edit_reservacion_id').val(),
                            type: "PUT",
                            dataType: 'json',
                            success: function(data) {
                                console.log(data)
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000,
                                    showConfirmButton: false
                                });

                                Swal.fire({
                                    title: '¡Reservación Rechazada!',
                                    text: 'Se procederá a habilitar la bahía.',
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'btn btn-success',
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    } else {
                                        location.reload();
                                    }
                                });


                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al registrar la reservación',
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        form.reportValidity();
                    }
                });


            });
        </script>
    @endpush
@endsection
