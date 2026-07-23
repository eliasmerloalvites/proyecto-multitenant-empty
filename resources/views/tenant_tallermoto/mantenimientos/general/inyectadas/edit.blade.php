@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Actualizar Mantenimiento General Inyectadas')
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




        .image-gallery{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
            gap:20px;
            margin-top:20px;
        }

        .image-card{
            position:relative;
            border-radius:14px;
            overflow:hidden;
            background:#fff;
            box-shadow:0 3px 10px rgba(0,0,0,.08);
            transition:.3s;
        }

        .image-card:hover{

            transform:translateY(-5px);

            box-shadow:0 8px 20px rgba(0,0,0,.15);
        }
        .image-card:hover img{
            transform:scale(1.05);
        }

        .image-card img{

            width:100%;
            height:180px;

            object-fit:cover;

            cursor:pointer;
        }

        .image-overlay{

            position:absolute;

            inset:0;

            background:rgba(0,0,0,.45);

            opacity:0;

            transition:.25s;

            display:flex;

            align-items:center;
            justify-content:center;
            gap:10px;
        }

        .image-card:hover .image-overlay{

            opacity:1;
        }

        .image-btn{

            width:42px;
            height:42px;

            border:none;

            border-radius:50%;

            color:white;

            display:flex;

            align-items:center;
            justify-content:center;

            cursor:pointer;

            transition:.2s;
        }

        .image-btn:hover{
            transform:scale(1.1);
        }

        .btn-view{
            background:#17a2b8;
        }

        .btn-delete{
            background:#dc3545;
        }

        .image-footer{

            padding:10px;

            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .image-size{

            font-size:12px;
            color:#6c757d;
            font-weight:600;
        }

        .upload-container{
            margin-bottom:25px;
        }

        .upload-box{

            width:100%;

            min-height:180px;

            border:2px dashed #ced4da;

            border-radius:18px;

            background:#fafafa;

            cursor:pointer;

            transition:.25s;

            display:flex;

            align-items:center;
            justify-content:center;

            text-align:center;

            padding:30px;
        }

        .upload-box:hover{

            border-color:#28a745;

            background:#f8fff9;
        }

        .upload-content i{

            font-size:48px;

            color:#28a745;

            margin-bottom:15px;
        }

        .upload-content h5{

            font-weight:700;

            margin-bottom:10px;
        }

        .upload-content p{

            color:#6c757d;

            margin-bottom:20px;
        }

        .upload-btn{

            display:inline-block;

            background:#28a745;

            color:white;

            padding:10px 20px;

            border-radius:10px;

            font-weight:600;

            transition:.25s;
        }

        .upload-box:hover .upload-btn{

            background:#218838;
        }

    </style>

    <div class="col-12">
        <div class="form-scroll-container">
            <form id="MantenimientoGeneralInyectadasForm" name="MantenimientoGeneralInyectadasForm" onsubmit="return false;">
                <div class="d-flex justify-content-between align-items-center ">
                    <div>
                        <h4>ACTUALIZAR MANTENIMIENTO GENERAL INYECTADAS</h4>
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
                                        <input type="text" class="form-control" id="MGI_Placa" name="MGI_Placa"
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
                                        <input type="text" class="form-control" id="MGI_Propietario"
                                            name="MGI_Propietario" maxlength="50" placeholder="Ingrese Propietario" required
                                            onkeyup="this.value=this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>CELULAR</label>
                                        <input type="number" class="form-control" id="MGI_celular" name="MGI_celular"
                                            maxlength="12" placeholder="999999999">
                                    </div>
                                </div>

                                <!-- ROW -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>UNIDAD</label>
                                        <input type="text" class="form-control" id="MGI_Unidad" name="MGI_Unidad"
                                            maxlength="40" placeholder="Ingrese Unidad"
                                            onkeyup="this.value=this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>KM</label>
                                        <input type="text" class="form-control" id="MGI_KMEntrada" name="MGI_KMEntrada"
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
                                    <textarea class="form-control" rows="4" name="MGI_DetalleIngreso" id="MGI_DetalleIngreso"
                                        onkeyup="handleText(this); this.value=this.value.toUpperCase();"></textarea>
                                </div>
                                <div class="form-group mb-0">
                                    <label>OBSERVACIONES</label>
                                    <textarea class="form-control" rows="4" name="MGI_DetalleObservacion" id="MGI_DetalleObservacion"
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
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det1==="SI"? 'checked' : ''}} name="MGI_Det1" id="customSwitch1">
                                    <label class="custom-control-label" for="customSwitch1"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12" style="text-align: center">
                                <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                    id="MGI_Det1Informacion" name="MGI_Det1Informacion" placeholder="Ingrese informacion" value="{{$datos->MGI_Det1Informacion}}"
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
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det2==="SI"? 'checked' : ''}} name="MGI_Det2" id="customSwitch2">
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
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det3==="SI"? 'checked' : ''}} name="MGI_Det3" id="customSwitch3">
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
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det4==="SI"? 'checked' : ''}} name="MGI_Det4" id="customSwitch4">
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
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det5==="SI"? 'checked' : ''}} name="MGI_Det5" id="customSwitch5">
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
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det6==="SI"? 'checked' : ''}} name="MGI_Det6" id="customSwitch6">
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
                                LIMPIEZA DEL CUERPO DE ACELERACION
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det7==="SI"? 'checked' : ''}} name="MGI_Det7" id="customSwitch7">
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
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LAVADO DE INYECTOR EN ULTRASONIDO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det8==="SI"? 'checked' : ''}} name="MGI_Det8" id="customSwitch8">
                                    <label class="custom-control-label" for="customSwitch8"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12"style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                9.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-7 col-8"style="text-align: left">
                                AJUSTE DE VALVULAS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det9==="SI"? 'checked' : ''}}  name="MGI_Det9" id="customSwitch9">
                                    <label class="custom-control-label" for="customSwitch9"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det9Admision" name="MGI_Det9Admision" placeholder="Admision: #.## mm" value="{{$datos->MGI_Det9Admision}}"
                                        maxlength="10" required="">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det9Escape" name="MGI_Det9Escape" placeholder="Escape #.## mm" value="{{$datos->MGI_Det9Escape}}"
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                10.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                REVISION Y CALIBRACION DE BUJIA
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det10==="SI"? 'checked' : ''}}  name="MGI_Det10" id="customSwitch10">
                                    <label class="custom-control-label" for="customSwitch10"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det10Medida" name="MGI_Det10Medida" placeholder="Medida: #.## mm" value="{{$datos->MGI_Det10Medida}}"
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                11.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                MEDICION DE COMPRESION DEL MOTOR
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det11==="SI"? 'checked' : ''}}  name="MGI_Det11" id="customSwitch11">
                                    <label class="custom-control-label" for="customSwitch11"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det11Medida" name="MGI_Det11Medida" placeholder="Medida: #.## mm" value="{{$datos->MGI_Det11Medida}}"
                                        maxlength="11" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                12.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                AJUSTE DE LA BRIDA DEL TUBO DE ESCAPE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det12==="SI"? 'checked' : ''}}  name="MGI_Det12" id="customSwitch12">
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
                                LAVADO Y AJUSTE DEL SISTEMA DE ARRASTRE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det13==="SI"? 'checked' : ''}}  name="MGI_Det13" id="customSwitch13">
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
                                MANTENIMIENTO DE FRENO DELANTERO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det14==="SI"? 'checked' : ''}}  name="MGI_Det14" id="customSwitch14">
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
                                MANTENIMIENTO DE FRENO POSTERIOR
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det15==="SI"? 'checked' : ''}}  name="MGI_Det15" id="customSwitch15">
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
                                AJUSTE DE PERNOS DE CHASIS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det16==="SI"? 'checked' : ''}}  name="MGI_Det16" id="customSwitch16">
                                    <label class="custom-control-label" for="customSwitch16"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                17.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LIMPIEZA DE CONECTORES ELECTRICOS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det17==="SI"? 'checked' : ''}}  name="MGI_Det17" id="customSwitch17">
                                    <label class="custom-control-label" for="customSwitch17"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                18.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                PRESION DE NEUMATICO DELANTERO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <input type="number" class="form-control form-control-sm"
                                    id="MGI_Det18" name="MGI_Det18" placeholder="## PSI" value="{{$datos->MGI_Det18}}"
                                    required="">
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                19.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                PRESION DE NEUMATICO POSTERIOR
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <input type="number" class="form-control form-control-sm"
                                    id="MGI_Det19" name="MGI_Det19" placeholder="## PSI" value="{{$datos->MGI_Det19}}"
                                    required="">
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                20.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                REVISION Y TEST DE LIQUIDO DE FRENOS
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det20==="SI"? 'checked' : ''}}  name="MGI_Det20" id="customSwitch20">
                                    <label class="custom-control-label" for="customSwitch20"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det20Humedad" name="MGI_Det20Humedad" placeholder="% HUMEDAD " value="{{$datos->MGI_Det20Humedad}}"
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                21.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                REVISION Y TEST DE LIQUIDO REFRIGERANTE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det21==="SI"? 'checked' : ''}}  name="MGI_Det21" id="customSwitch21">
                                    <label class="custom-control-label" for="customSwitch21"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                22.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                REVISION DEL SISTEMA DE ENFRIAMIENTO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det22==="SI"? 'checked' : ''}}  name="MGI_Det22" id="customSwitch22">
                                    <label class="custom-control-label" for="customSwitch22"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det22Ventilador" name="MGI_Det22Ventilador" placeholder="Ventilador: ## " value="{{$datos->MGI_Det22Ventilador}}"
                                        maxlength="10" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                23.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LUBRICACION DEL SISTEMA DE ARRASTRE
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det23==="SI"? 'checked' : ''}}  name="MGI_Det23" id="customSwitch23">
                                    <label class="custom-control-label" for="customSwitch23"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                24.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                TEST DE BATERIA
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det24==="SI"? 'checked' : ''}}  name="MGI_Det24" id="customSwitch24">
                                    <label class="custom-control-label" for="customSwitch24"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 row "style="text-align: left">
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det24Vida" name="MGI_Det24Vida" placeholder="%. VIDA UTIL: #% " value="{{$datos->MGI_Det24Vida}}"
                                        maxlength="20" required="">
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det24Carga" name="MGI_Det24Carga" placeholder="V. Carga: ## " value="{{$datos->MGI_Det24Carga}}"
                                        maxlength="20" required="">
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-12 "style="text-align: left">
                                    <input type="text" class="form-control form-control-sm" onKeyUp="this.value=this.value.toUpperCase();"
                                        id="MGI_Det24Arranque" name="MGI_Det24Arranque" placeholder="V. ARRANQUE: ## DVC " value="{{$datos->MGI_Det24Arranque}}"
                                        maxlength="20" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                25.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                LIMPIEZA DEL SENSOR DE OXIGENO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det25==="SI"? 'checked' : ''}}  name="MGI_Det25" id="customSwitch25">
                                    <label class="custom-control-label" for="customSwitch25"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                26.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                ESCANEO
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det26==="SI"? 'checked' : ''}}  name="MGI_Det26" id="customSwitch26">
                                    <label class="custom-control-label" for="customSwitch26"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-2" style="text-align: left">
                                27.0
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-8"style="text-align: left">
                                VERIFICACION DEL SISTEMA DE LUCES
                            </div>
                            <div class="form-group col-lg-1 col-md-1 col-sm-3 col-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" {{$datos->MGI_Det27==="SI"? 'checked' : ''}}  name="MGI_Det27" id="customSwitch27">
                                    <label class="custom-control-label" for="customSwitch27"></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 "style="text-align: left">
                            </div>
                        </div>




                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" align="left">
                            <label for="Descripcion de averia">DETALLE DE LO REALIZADO</label><br>
                            <textarea onKeyUp="handleText(this);" name="MGI_DetalleRealizado" id="MGI_DetalleRealizado" class="form-control"
                                rows="6" onKeyUp="this.value=this.value.toUpperCase();">{{$datos->MGI_DetalleRealizado}}</textarea>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" align="left">
                            <label for="Descripcion de averia">CORRECION DE OBSERVACIONES</label><br>
                            <textarea onKeyUp="handleText(this);" name="MGI_CorrecionObservacion" id="MGI_CorrecionObservacion" class="form-control"
                                rows="6" onKeyUp="this.value=this.value.toUpperCase();">{{$datos->MGI_CorrecionObservacion}}</textarea>
                        </div>


                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12 row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="name" class=" control-label">PROXIMO CAMBIO DE ACEITE</label>
                                <input type="text" class="form-control" id="MGI_ProximoCambioAceite" name="MGI_ProximoCambioAceite"
                                placeholder="#### KM" value="{{$datos->MGI_ProximoCambioAceite}}" maxlength="50" required="">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="name" class=" control-label">PROXIMO SERVICIO</label>
                                <input type="text" class="form-control" id="MGI_ProximoServicio" name="MGI_ProximoServicio"
                                placeholder="#### KM" value="{{$datos->MGI_ProximoServicio}}" maxlength="50" required="">
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
                                            <input class="form-control" id="MGID_Descripcion_Aux"
                                                name="MGID_Descripcion_Aux" type="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>PRECIO</label>
                                            <input class="form-control" id="MGID_Precio_Aux" name="MGID_Precio_Aux"
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

                <div class="row mt-3">
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card card-outline card-danger">
                            <div class="card-header">
                                <h3 class="card-title-custom">
                                    <i class="fas fa-camera-retro mr-1"></i>
                                    Galería de Evidencias
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 fileforImagen"> </div>
                                <div class="image-gallery imagenesadjuntas">
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
                                @if($admin && $userAprobador)
                                    @if($datos->MGI_Estado == "APROBADO")
                                        <button type="button" id="activarBtn" class="btn btn-info">Activar</button>
                                    @endif                    
                                        <button type="button" id="updateBtn" class="btn btn-success">Aprobar</button>
                                        <button type="button" id="updateNotificarBtn" class="btn btn-info">Aprobar y notificar</button>
                                    @elseif($datos->MGI_Estado != "APROBADO")
                                        <button type="button" id="updateBtn" class="btn btn-success">Actualizar</button>                    
                                @endif

                                {{-- <button type="button" id="updateBtn" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i>
                                    Guardar Reporte
                                </button> --}}
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
        
        var ListDet = [];
        var images = []
        var isImgProcessing = false;
        var table;
        var id_mpi = <?php echo $id; ?>;
        var contador = 1;
        const MAX_IMAGES = {{ tenant()->max_images }};

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
            Toast.fire({ icon, title });
        };

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2()
            $('.select2bs4').select2({ theme: 'bootstrap4' })

            const reloadTable = () => {
                table.ajax.reload(null, false);
            };

            const handleAjaxError = (message, error = null) => {
                console.error(error);
                showToast('error', message);
            };
            
            cargarData();
            cargarFileForImagen();

            $('body').on('click', '.eliminarImagen', function() {
   
                var item = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('tenant.mantenimientos.generalinyectada.index') }}" + '/crop/'+id_mpi+'/'+ item,
                        success: function(data) {
                            console.log(data+"data");
                            images = []
                            for(var i = 0; i< data.data.length; i++){
                                var obj = {
                                    id: data.data[i].MGII_Item,
                                    src: data.data[i].MGII_url,
                                    name: data.data[i].MGII_Nombre,
                                    size: data.data[i].MGII_Peso
                                }
                                images.push(obj);
                            }

                            cargarImagenes()

                            Toast.fire({
                                type: 'success',
                                title: String(data.message)
                            });

                        },
                        error: function(data) {
                            console.log(data+"data");
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: String(data.message)
                            })
                        }
                    });
                }
            });


            $('body').on('click', '.cargarImagenCrop', function() {
                if (!isImgProcessing) {
                    isImgProcessing = true;
                    var id = $(this).data("id");
                    var cont = $(this).data("cont");
                    AñadirImagen(id)
                }

            });

            // GUARDAR
            $('#updateBtn').on('click', function(e) {
                e.preventDefault();
                ActualizarMGI()
            });

            $('#updateNotificarBtn').click(function(e) {
                e.preventDefault();
                ActualizarNotificar();
            });

            $('#activarBtn').click(function(e) {
                e.preventDefault();
                ActivarMGI();
            });

        });

        function cargarFileForImagen(){
            var container = document.querySelector('.fileforImagen');
            container.innerHTML = '';

            var wrapper = document.createElement('div');
            wrapper.classList.add('upload-container');

            var label = document.createElement('label');
            label.setAttribute('for', 'file');
            label.classList.add('upload-box');
            label.innerHTML = `
                <div class="upload-content">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h5>Subir Imágenes</h5>
                    <p>Arrastra imágenes aquí o haz click</p>
                    <span class="upload-btn">
                        Seleccionar Imagen
                    </span>
                </div>
            `;

            /* INPUT REAL OCULTO */
            var inputFile = document.createElement('input');
            inputFile.setAttribute('type', 'file');
            inputFile.setAttribute('name', 'file');
            inputFile.setAttribute('id', 'file');
            inputFile.setAttribute('hidden', true);
            inputFile.setAttribute('accept', 'image/*');
            inputFile.setAttribute('data-id', id_mpi);
            inputFile.setAttribute('data-cont', contador);
            inputFile.classList.add('cargarImagenCrop');
            
            /* APPEND */
            wrapper.appendChild(label);
            wrapper.appendChild(inputFile);

            container.appendChild(wrapper);

        }

        function cargarImagenes(){
            if(images.length >= MAX_IMAGES){
                $('.fileforImagen').hide();
            }else{
                $('.fileforImagen').show();
            }

            const container = document.querySelector('.imagenesadjuntas');
            container.innerHTML = '';
            images.forEach(function(image){
                const card = document.createElement('div');
                card.classList.add('image-card');
                card.innerHTML = `
                    <img src="${image.src}" onclick="verImagen('${image.src}')">
                    <div class="image-overlay">
                        <button class="image-btn btn-view" onclick="verImagen('${image.src}')">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button class="image-btn btn-delete eliminarImagen"
                                data-id="${image.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="image-footer">
                        <span class="image-size">${image.size}</span>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function verImagen(src){
            Swal.fire({
                imageUrl: src,
                imageWidth: '100%',
                imageAlt: 'Imagen',
                showConfirmButton:false,
                background:'#000',
                width:'900px'
            });
        }

        function createAttachmentLi(image) {
            var li = createElement('li');
            li.style.marginTop = '20px';
            li.style.marginBottom = '20px';
            var spanIcon = createElement('span', ['mailbox-attachment-icon', 'has-img']);
            var img = createElement('img');
            img.style.height = '250px'
            var divInfo = createElement('div', ['mailbox-attachment-info']);
            var aName = createElement('a', ['mailbox-attachment-name']);
            var spanSize = createElement('span', ['mailbox-attachment-size', 'clearfix', 'mt-1', 'ml-4']);
            // var aDownload = createElement('a', ['btn', 'btn-info', 'float-right']);
            var aDelete = createElement('a', ['btn', 'btn-danger', 'float-right','eliminarImagen']);
            aDelete.setAttribute('data-id', image.id);

            img.src = image.src;
            img.alt = "Attachment";
            aName.innerHTML = '<i class="fas fa-camera"></i> ' ;
            spanSize.innerHTML = '<span>' + image.size + '</span>';
            // aDownload.innerHTML = '<i class="fas fa-cloud-download-alt" style="position: absolute; color: white;"></i>';
            aDelete.innerHTML = '<i class="fas fa-times-circle" style="position: absolute; color: white;"></i>';

            spanIcon.appendChild(img);
            appendChildren(divInfo, [aName, spanSize]);
            spanSize.appendChild(aDelete);
            // spanSize.appendChild(aDownload);
            appendChildren(li, [spanIcon, divInfo]);

            return li;
        }

        function createNewUl() {
            var ul = document.createElement('ul');
            ul.classList.add('mailbox-attachments', 'd-flex', 'align-items-stretch', 'clearfix');
            ul.style.marginTop = '20px';
            ul.style.marginBottom = '20px';
            return ul;
        }

        function createElement(tagName, classNames) {
            var element = document.createElement(tagName);
            if (classNames && Array.isArray(classNames)) {
                element.classList.add(...classNames);
            }
            return element;
        }

        function appendChildren(parent, children) {
            if (!Array.isArray(children)) {
                children = [children];
            }
            children.forEach(function(child) {
                parent.appendChild(child);
            });
        }


        function cargarData(){
            $('#MGI_Placa').val(`<?php echo $datos->MGI_Placa; ?>`);
            $('#MGI_Propietario').val(`<?php echo $datos->MGI_Propietario; ?>`);
            $('#MGI_celular').val(`<?php echo $datos->MGI_celular; ?>`);
            $('#MGI_Unidad').val(`<?php echo $datos->MGI_Unidad; ?>`);
            $('#MGI_KMEntrada').val(`<?php echo $datos->MGI_KMEntrada; ?>`);
            $('#USU_Id').val(`<?php echo $datos->PER_Id; ?>`).change();
            $('#MGI_DetalleIngreso').val(`<?php echo $datos->MGI_DetalleIngreso; ?>`);
            $('#MGI_DetalleObservacion').val(`<?php echo $datos->MGI_DetalleObservacion; ?>`);
            $('#MGI_DetalleRealizado').val(`<?php echo $datos->MGI_DetalleRealizado; ?>`);
            $('#MGI_CorrecionObservacion').val(`<?php echo $datos->MGI_CorrecionObservacion; ?>`);
            $('#MGI_ProximoCambioAceite').val(`<?php echo $datos->MGI_ProximoCambioAceite; ?>`);
            $('#MGI_ProximoServicio').val(`<?php echo $datos->MGI_ProximoServicio; ?>`);

            <?php foreach ($detalle as $dev): ?>
                var descripcion = `<?php echo $dev->MGID_Descripcion; ?>`;
                var precio = `<?php echo $dev->MGI_Precio; ?>`;

                var fila1 = [descripcion, precio];
                ListDet.push(fila1);
            <?php endforeach ?>

            <?php foreach ($imagenes as $img): ?>
                var obj = {
                    id: `<?php echo $img->MGII_Item ?>`,
                    src: `<?php echo $img->MGII_url ?>`,
                    name: `<?php echo $img->MGII_Nombre ?>`,
                    size: `<?php echo $img->MGII_Peso ?>`
                }
                images.push(obj);
            <?php endforeach ?>
            cargarTabla()
            cargarImagenes()
            limpiardetalle();
        }

        function agregardetalle() {

            var descripcion = $('#MGID_Descripcion_Aux').val();
            var precio = $('#MGID_Precio_Aux').val();

            if (descripcion != "" && precio != "") {
                var fila1 = [descripcion, precio];
                ListDet.push(fila1);
                cargarTabla()
                limpiardetalle();

            }else{
                if(descripcion == ""){
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
                if(precio == ""){
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

        
        function AñadirImagen($id) {

            if (images.length >= MAX_IMAGES) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite alcanzado',
                    text: 'Tu plan permite máximo ' + MAX_IMAGES + ' imágenes.'
                });

                return;
            }

            const Toast2 = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            var cropToolInstance;

            cropToolInstance = $('#file').ijaboCropTool({
                preview: '.image-previewer',
                setRatio: 1,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'heic'],
                buttonsText: ['ACEPTAR', 'SALIR'],
                buttonsColor: ['#30bf7d', '#ee5155', -15],
                processUrl: "{{ route('tenant.mantenimientos.generalinyectada.index') }}"+"/"+$id+'/crop',
                withCSRF: ['_token', '{{ csrf_token() }}'],
                onSuccess: function(message, element, status ) {
                    console.log("message", message);
                    console.log("element", element);
                    console.log("status", status);
                    Toast2.fire({
                        type: 'success',
                        title: message.mensaje
                    })
                    images = []
                    for(var i = 0; i< message.data.length; i++){
                        var obj = {
                            id: message.data[i].MGII_Item,
                            src: message.data[i].MGII_url,
                            name: message.data[i].MGII_Nombre,
                            size: message.data[i].MGII_Peso
                        }
                        images.push(obj);
                    }

                    cargarImagenes()
                    isImgProcessing = false;

                },
                onError: function(message, element, status) {
                    isImgProcessing = false;
                    console.log(message);
                    alert(message);
                }
            });

            contador++
            cargarFileForImagen()

        }


        function cargarTabla(){
            $("#detalles tbody").html('');
                var totalCosto = 0
                for (var i = ListDet.length - 1; i >= 0; i--) {
                    var col0 = '<tr  onClick="MostrarValores1(' + ListDet[i][0] + ');" id="fila' + i + '">'
                    var col1 = '<td style="text-align: left;">' + (i + 1) + '</td>'
                    var col2 = '<td style="text-align: left;"><input id="MGID_Descripcion' + i + '" type="hidden" name="MGID_Descripcion[]" value="' + ListDet[i][0] +
                        '">' + ListDet[i][0] + '</td>'
                    var col3 = '<td style="text-align: left;"><input id="MGI_Precio' + i + '" type="hidden" name="MGI_Precio[]" value="' + ListDet[i][1] +
                        '">' + ListDet[i][1] + '</td>'
                    var col4 =
                        '<td style="width:80px; height : 24px; text-align: center;"><button  type="button"  class="btn" onclick="eliminar(' +
                        i +
                        ');" style="border-radius: 10px; height : 24px; color:red;padding:0px"><i class="fa fa-trash"></button></td></tr>';
                    var fila = col0 + col1 + col2 + col3 + col4 ;
                    $("#detalles").append(fila);
                    totalCosto = parseFloat(totalCosto) + parseFloat(ListDet[i][1])
                }

                $("#totalCosto").html(totalCosto);
        }

        function BuscarCliente() {
            ocultar()
            var url = "/busquedaplaca/responsable/" + $('#MGI_Placa').val() + '?';
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    if (data.success.validacion == "true") {
                        $('#MGI_Propietario').val(data.success.mtto[0].Propietario);
                        $('#MGI_celular').val(data.success.mtto[0].celular);
                        $('#MGI_Unidad').val(data.success.mtto[0].Unidad);
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
            $('#MGID_Descripcion_Aux').val('');
            $('#MGID_Precio_Aux').val('');

        }

        function eliminar(index) {
            $('#fila' + index).remove();
            ListDet.splice(index, 1);
            cargarTabla()
        }


        function ActualizarMGI() {
            var new_id_mpi = id_mpi
            $.ajax({
                data: $('#MantenimientoGeneralInyectadasForm').serialize(),
                url: "{{ route('tenant.mantenimientos.generalinyectada.index') }}/" +new_id_mpi,
                type: "PUT",
                dataType: 'json',
                success: function (data) {
                    console.log('Success:', data);
                    Toast.fire({
                        type: 'success',
                        title: data.message
                    });
                    document.location.href="{{ route('tenant.mantenimientos.generalinyectada.index') }}";
                },
                error: function (data) {
                    console.log('Error:', data);
                    Toast.fire({
                        type: 'error',
                        title: 'Mantenimiento Preventivo fallo al Registrarse.'
                    })
                }
            });

        }

        function ActualizarNotificar() {
            var new_id_mpi = id_mpi
            $.ajax({
                data: $('#MantenimientoGeneralInyectadasForm').serialize() + '&notificar=1',
                url: "{{ route('tenant.mantenimientos.generalinyectada.index') }}/" +new_id_mpi,
                type: "PUT",
                dataType: 'json',
                success: function (data) {
                    console.log('Success:', data);
                    Toast.fire({
                        type: 'success',
                        title: data.message
                    });
                    document.location.href="{{ route('tenant.mantenimientos.generalinyectada.index') }}";
                },
                error: function (data) {
                    console.log('Error:', data);
                    Toast.fire({
                        type: 'error',
                        title: 'Mantenimiento Preventivo fallo al Registrarse.'
                    })
                }
            });

        }

        function ActivarMGI() {
            var new_id_mpi = id_mpi
            $.ajax({
                url: "{{ route('tenant.mantenimientos.generalinyectada.index') }}/" +new_id_mpi + "/activar",
                type: "PUT",
                dataType: 'json',
                success: function (data) {
                    console.log('Success:', data);
                    Toast.fire({
                        type: 'success',
                        title: data.message
                    });
                    document.location.href="{{ route('tenant.mantenimientos.generalinyectada.index') }}";
                },
                error: function (data) {
                    console.log('Error:', data);
                    Toast.fire({
                        type: 'error',
                        title: 'Mantenimiento Preventivo fallo al Activarse.'
                    })
                }
            });

        }

       
        function Regresar() {
            document.location.href = "{{ route('tenant.mantenimientos.generalinyectada.index') }}"
        }
    </script>
@endsection
