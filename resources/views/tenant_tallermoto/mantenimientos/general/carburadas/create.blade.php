@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Creacion Mantenimiento General Carburada')
@section('contenido')
    <style>
        .card-title-custom {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
        }

        .form-control,
        .input-group-text {
            border-radius: 8px;
        }

        .form-control {
            height: 38px;
        }

        .form-scroll-container {
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 5px;
        }

        textarea.form-control {
            height: auto;
            resize: none;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #4B5563;
        }

        .section-card {
            border-radius: 12px;
            overflow: hidden;
        }

        .section-card .card-header {
            padding: 10px 15px;
        }

        .table thead th {
            font-size: 12px;
            font-weight: 700;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
            font-size: 13px;
        }

        .btn-action {
            min-width: 140px;
        }
    </style>

    <div class="col-12">
        <div class="form-scroll-container">
            <form id="MantenimientoGeneralCarburadasForm" name="MantenimientoGeneralCarburadasForm" onsubmit="return false;">
                <div class="d-flex justify-content-between align-items-center ">
                    <div>
                        <h4>CREAR MANTENIMIENTO GENERAL CARBURADAS</h4>
                    </div>
                </div>
                <div class="row">
                    <!-- DATOS -->
                    <div class="col-lg-5">
                        <div class="card card-outline card-danger shadow-sm section-card h-100">
                            <div class="card-header">
                                <h3 class="card-title-custom">
                                    <i class="fas fa-motorcycle mr-1"></i>
                                    Datos de la Unidad
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- PLACA -->
                                <div class="form-group">
                                    <label>PLACA</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="MGC_Placa" name="MGC_Placa"
                                            placeholder="78C5-59" maxlength="18" required
                                            onkeyup="this.value=this.value.toUpperCase();">

                                        <div class="input-group-append">

                                            <button type="button" class="btn btn-info" id="Buscar_Placa"
                                                onclick="BuscarCliente()">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button type="button" class="btn btn-default" id="cargando"
                                                style="display:none;">
                                                <img width="15px" src="{{ asset_root('images/gif/cargando1.gif') }}">
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- ROW -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>PROPIETARIO</label>
                                        <input type="text" class="form-control" id="MGC_Propietario"
                                            name="MGC_Propietario" maxlength="50" placeholder="Ingrese Propietario" required
                                            onkeyup="this.value=this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>CELULAR</label>
                                        <input type="number" class="form-control" id="MGC_celular" name="MGC_celular"
                                            maxlength="12" placeholder="999999999">
                                    </div>
                                </div>

                                <!-- ROW -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>UNIDAD</label>
                                        <input type="text" class="form-control" id="MGC_Unidad" name="MGC_Unidad"
                                            maxlength="40" placeholder="Ingrese Unidad"
                                            onkeyup="this.value=this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>KM</label>
                                        <input type="text" class="form-control" id="MGC_KMEntrada" name="MGC_KMEntrada"
                                            maxlength="20" placeholder="KM" required>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>RESPONSABLE</label>
                                        <select class="form-control select2 select2-danger" id="USU_Id" name="USU_Id"
                                            style="width:100%;">
                                            @if ($admin)
                                                <option value="">Seleccionar</option>
                                            @endif
                                            @foreach ($personal as $per)
                                                <option value="{{ $per->id }}">{{ $per->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DETALLE -->
                    <div class="col-lg-7">
                        <div class="card card-outline card-warning shadow-sm section-card h-100">
                            <div class="card-header">
                                <h3 class="card-title-custom">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    Detalle del Mantenimiento
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>INGRESO DE UNIDAD</label>
                                    <textarea class="form-control" rows="4" name="MGC_DetalleIngreso" id="MGC_DetalleIngreso"
                                        onkeyup="handleText(this); this.value=this.value.toUpperCase();"></textarea>
                                </div>
                                <div class="form-group mb-0">
                                    <label>OBSERVACIONES</label>
                                    <textarea class="form-control" rows="4" name="MGC_DetalleObservacion" id="MGC_DetalleObservacion"
                                        onkeyup="handleText(this); this.value=this.value.toUpperCase();"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPCION -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-outline card-info shadow-sm section-card">
                            <div class="card-header">
                                <h3 class="card-title-custom">
                                    <i class="fas fa-wrench mr-1"></i>
                                    Descripción del Servicio
                                </h3>
                            </div>
                            <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                1.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                CAMBIO DE ACEITE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="MGC_Det1" id="customSwitch1">
                                    <label class="custom-control-label" for="customSwitch1"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12" style="text-align: center">
                                <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                    id="MGC_Det1Informacion" name="MGC_Det1Informacion" placeholder="Ingrese informacion" value=""
                                    maxlength="50" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                2.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                CAMBIO DE FILTRO DE ACEITE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="MGC_Det2" id="customSwitch2">
                                    <label class="custom-control-label" for="customSwitch2"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12"style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                3.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LIMPIEZA DE CHASIS CON AIRE COMP.
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det3" id="customSwitch3">
                                    <label class="custom-control-label" for="customSwitch3"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12"style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                4.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LIMPIEZA DE CABLES ELEC CON AIRE COMP.
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det4" id="customSwitch4">
                                    <label class="custom-control-label" for="customSwitch4"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12"style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                5.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                DESENGRASE INTERNO DE LA UNIDAD
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det5" id="customSwitch5">
                                    <label class="custom-control-label" for="customSwitch5"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12"style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                6.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                MANTENIMIENTO DE FILTRO DE AIRE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="MGC_Det6" id="customSwitch6">
                                    <label class="custom-control-label" for="customSwitch6"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12"style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                7.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                MANTENIMIENTO DE CARBURADOR
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="MGC_Det7" id="customSwitch7">
                                    <label class="custom-control-label" for="customSwitch7"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12"style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                8.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-7 col-8"style="text-align: left">
                                AJUSTE DE VALVULAS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det8" id="customSwitch8">
                                    <label class="custom-control-label" for="customSwitch8"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det8Admision" name="MGC_Det8Admision" placeholder="Admision: #.## mm" value=""
                                        maxlength="10" required="">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det8Escape" name="MGC_Det8Escape" placeholder="Escape #.## mm" value=""
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                9.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                REVISION Y CALIBRACION DE BUJIA
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det9" id="customSwitch9">
                                    <label class="custom-control-label" for="customSwitch9"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det9Medida" name="MGC_Det9Medida" placeholder="Medida: #.## mm" value=""
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                10.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                AJUSTE DE LA BRIDA DEL TUBO DE ESCAPE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det10" id="customSwitch10">
                                    <label class="custom-control-label" for="customSwitch10"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                11.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LAVADO Y AJUSTE DEL SISTEMA DE ARRASTRE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det11" id="customSwitch11">
                                    <label class="custom-control-label" for="customSwitch11"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                12.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                MANTENIMIENTO DE FRENO DELANTERO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det12" id="customSwitch12">
                                    <label class="custom-control-label" for="customSwitch12"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                13.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                MANTENIMIENTO DE FRENO POSTERIOR
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det13" id="customSwitch13">
                                    <label class="custom-control-label" for="customSwitch13"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                14.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                AJUSTE DE PERNOS DE CHASIS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det14" id="customSwitch14">
                                    <label class="custom-control-label" for="customSwitch14"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                15.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LIMPIEZA DE CONECTORES ELECTRICOS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det15" id="customSwitch15">
                                    <label class="custom-control-label" for="customSwitch15"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                16.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                PRESION DE NEUMATICO DELANTERO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <input type="number" class="form-control form-control-sm"
                                    id="MGC_Det16" name="MGC_Det16" placeholder="## PSI" value=""
                                    required="">
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                17.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                PRESION DE NEUMATICO POSTERIOR
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <input type="number" class="form-control form-control-sm"
                                    id="MGC_Det17" name="MGC_Det17" placeholder="## PSI" value=""
                                    required="">
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                18.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                REVISION Y TEST DE LIQUIDO DE FRENOS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det18" id="customSwitch18">
                                    <label class="custom-control-label" for="customSwitch18"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det18Humedad" name="MGC_Det18Humedad" placeholder="% HUMEDAD " value=""
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                19.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                REVISION DEL SISTEMA DE ENFRIAMIENTO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det19" id="customSwitch19">
                                    <label class="custom-control-label" for="customSwitch19"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det19Ventilador" name="MGC_Det19Ventilador" placeholder="Ventilador: ## " value=""
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                20.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LUBRICACION DEL SISTEMA DE ARRASTRE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det20" id="customSwitch20">
                                    <label class="custom-control-label" for="customSwitch20"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                21.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                TEST DE BATERIA
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  name="MGC_Det21" id="customSwitch21">
                                    <label class="custom-control-label" for="customSwitch21"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det21Vida" name="MGC_Det21Vida" placeholder="%. VIDA UTIL: #% " value=""
                                        maxlength="20" required="">
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det21Carga" name="MGC_Det21Carga" placeholder="V. Carga: ## " value=""
                                        maxlength="20" required="">
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGC_Det21Arranque" name="MGC_Det21Arranque" placeholder="V. ARRANQUE: ## DVC " value=""
                                        maxlength="20" required="">
                                </div>
                            </div>
                        </div>


                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" align="left">
                            <label for="Descripcion de averia">DETALLE DE LO REALIZADO</label><br>
                            <textarea onKeyUp="handleText(this);" name="MGC_DetalleRealizado" id="MGC_DetalleRealizado" class="form-control"
                                rows="6" onKeyUp="this.value=this.value.toUpperCase();"></textarea>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" align="left">
                            <label for="Descripcion de averia">CORRECION DE OBSERVACIONES</label><br>
                            <textarea onKeyUp="handleText(this);" name="MGC_CorrecionObservacion" id="MGC_CorrecionObservacion" class="form-control"
                                rows="6" onKeyUp="this.value=this.value.toUpperCase();"></textarea>
                        </div>


                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12 row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="name" class=" control-label">PROXIMO CAMBIO DE ACEITE</label>
                                <input type="text" class="form-control" onKeyUp="this.value=this.value.toUpperCase();"
                                    id="MGC_ProximoCambioAceite" name="MGC_ProximoCambioAceite" placeholder="#### KM" value=""
                                    maxlength="50" required="">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="name" class=" control-label">PROXIMO SERVICIO</label>
                                <input type="text" class="form-control" onKeyUp="this.value=this.value.toUpperCase();"
                                    id="MGC_ProximoServicio" name="MGC_ProximoServicio" placeholder="#### KM" value=""
                                    maxlength="50" required="">
                            </div>
                        </div>

                    </div>
                        </div>
                    </div>
                </div>


                <!-- REPUESTOS -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-outline card-success shadow-sm section-card">
                            <div class="card-header">
                                <h3 class="card-title-custom">
                                    <i class="fas fa-cogs mr-1"></i>
                                    Repuestos a Reemplazar
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- AGREGAR -->
                                <div class="row align-items-end">
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            <label>DESCRIPCIÓN</label>
                                            <input class="form-control" id="MGCD_Descripcion_Aux"
                                                name="MGCD_Descripcion_Aux" type="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>PRECIO</label>
                                            <input class="form-control" id="MGCD_Precio_Aux" name="MGCD_Precio_Aux"
                                                type="number">
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group d-flex align-items-end h-100">
                                            <button class="btn btn-success btn-block" onclick="agregardetalle()"
                                                type="button" style="height:38px; margin-top:3px;">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- TABLA -->
                                <div class="table-responsive mt-3" style="max-height: 400px; overflow-y:auto;">
                                    <table class="table table-bordered table-hover table-sm" id="detalles">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="50">N°</th>
                                                <th>Descripción</th>
                                                <th width="150">Precio</th>
                                                <th width="90" class="text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <th colspan="2" class="text-right">
                                                    TOTAL:
                                                </th>
                                                <th>
                                                    <label id="totalCosto" class="mb-0 text-success">0</label>
                                                </th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- BOTONES -->
                <div class="row mt-3 mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-right">
                                <button type="button" onclick="Regresar()" class="btn btn-default">
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Regresar
                                </button>
                                <button type="button" id="saveBtn" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i>
                                    Guardar Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection
@section('script')
    <script>
        var ListDetVenta = [];
        // TOAST
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // HELPERS
        const showToast = (icon, title) => {
            Toast.fire({
                icon,
                title
            });
        };

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2()
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            const reloadTable = () => {
                table.ajax.reload(null, false);
            };

            const handleAjaxError = (message, error = null) => {
                console.error(error);
                showToast('error', message);
            };

            // GUARDAR
            $('#saveBtn').on('click', function(e) {
                e.preventDefault();
                AgregarMGC()
            });

        });

        function agregardetalle() {

            var descripcion = $('#MGCD_Descripcion_Aux').val();
            var precio = $('#MGCD_Precio_Aux').val();

            if (descripcion != "" && precio != "") {
                var fila1 = [descripcion, precio];
                ListDetVenta.push(fila1);
                cargarTabla()
                limpiardetalle();

            } else {
                if (descripcion == "") {
                    Swal.fire({
                            title: "Debe esbribir una descripcion",
                            icon: 'error',
                            confirmButtonColor: "#26BA9A",
                            confirmButtonText: "Ok"
                        })
                        .then(resultado => {
                            if (resultado.value) {} else {}
                        });

                }
                if (precio == "") {
                    Swal.fire({
                            title: "Debe Añadir el precio",
                            icon: 'error',
                            confirmButtonColor: "#26BA9A",
                            confirmButtonText: "Ok"
                        })
                        .then(resultado => {
                            if (resultado.value) {} else {}
                        });

                }

            }
        }

        function handleText(textarea) {
            textarea.value = textarea.value.toUpperCase(); // Convertir a mayúsculas
            const maxCharsPerLine = 100;
            let text = textarea.value;
            let lines = text.split('\n');

            for (let i = 0; i < lines.length; i++) {
                if (lines[i].length > maxCharsPerLine) {
                    let currentLine = lines[i];
                    let newLine = '';

                    while (currentLine.length > maxCharsPerLine) {
                        newLine += currentLine.substring(0, maxCharsPerLine) + '\n';
                        currentLine = currentLine.substring(maxCharsPerLine);
                    }
                    lines[i] = newLine + currentLine;
                }
            }

            textarea.value = lines.join('\n');
        }

        function cargarTabla() {
            $("#detalles tbody").html('');
            var totalCosto = 0
            for (var i = ListDetVenta.length - 1; i >= 0; i--) {
                var col0 = '<tr  onClick="MostrarValores1(' + ListDetVenta[i][0] + ');" id="fila' + i + '">'
                var col1 = '<td style="text-align: left;">' + (i + 1) + '</td>'
                var col2 = '<td style="text-align: left;"><input id="MGCD_Descripcion' + i +
                    '" type="hidden" name="MGCD_Descripcion[]" value="' + ListDetVenta[i][0] +
                    '">' + ListDetVenta[i][0] + '</td>'
                var col3 = '<td style="text-align: left;"><input id="MGC_Precio' + i +
                    '" type="hidden" name="MGC_Precio[]" value="' + ListDetVenta[i][1] +
                    '">' + ListDetVenta[i][1] + '</td>'
                var col4 =
                    '<td style="width:80px; height : 24px; text-align: center;"><button  type="button"  class="btn" onclick="eliminar(' +
                    i +
                    ');" style="border-radius: 10px; height : 24px; color:red;padding:0px"><i class="fa fa-trash"></button></td></tr>';
                var fila = col0 + col1 + col2 + col3 + col4;
                $("#detalles").append(fila);
                totalCosto = parseFloat(totalCosto) + parseFloat(ListDetVenta[i][1])
            }

            $("#totalCosto").html(totalCosto);
        }

        function BuscarCliente() {
            ocultar()
            var url = "/busquedaplaca/responsable/" + $('#MGC_Placa').val() + '?';
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    if (data.success.validacion == "true") {
                        $('#MGC_Propietario').val(data.success.mtto[0].Propietario);
                        $('#MGC_celular').val(data.success.mtto[0].celular);
                        $('#MGC_Unidad').val(data.success.mtto[0].Unidad);
                    }
                    mostrar()
                },
                error: function(data) {
                    console.log('Error:', data);
                    mostrar()
                }
            });
        }

        function ocultar() {
            document.getElementById('Buscar_Placa').style.display = 'none';
            document.getElementById('cargando').style.display = 'block';

            setInterval('mostrar()', 1000);
        }

        function mostrar() {
            document.getElementById('Buscar_Placa').style.display = 'block';
            document.getElementById('cargando').style.display = 'none';
        }

        function limpiardetalle() {
            $('#MGCD_Descripcion_Aux').val('');
            $('#MGCD_Precio_Aux').val('');

        }

        function eliminar(index) {
            $('#fila' + index).remove();
            ListDetVenta.splice(index, 1);
            cargarTabla()
        }


        function AgregarMGC() {
            $.ajax({
                data: $('#MantenimientoGeneralCarburadasForm').serialize(),
                url: "{{ route('tenant.mantenimientos.generalcarburada.store') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    console.log('Success:', data);
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    })
                    document.location.href =
                        "{{ route('tenant.mantenimientos.generalcarburada.index') }}/" + data.id +
                        '/edit';
                },
                error: function(data) {
                    console.log('Error:', data);
                    Toast.fire({
                        type: 'error',
                        title: 'Actividades variadas fallo al Registrarse.'
                    })
                }
            });
        }

        function Regresar() {
            document.location.href = "{{ route('tenant.mantenimientos.generalcarburada.index') }}"
        }
    </script>
@endsection
