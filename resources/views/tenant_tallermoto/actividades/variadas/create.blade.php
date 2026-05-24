@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Creacion Actividad Variada')
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
            <form id="MantenimientoActividadVariadasForm" name="MantenimientoActividadVariadasForm" onsubmit="return false;">
                <div class="d-flex justify-content-between align-items-center ">
                    <div>
                        <h4>CREAR MANTENIMIENTO DE ACTIVIDAD VARIADA</h4>
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
                                        <input type="text" class="form-control" id="MAV_Placa" name="MAV_Placa"
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
                                        <input type="text" class="form-control" id="MAV_Propietario"
                                            name="MAV_Propietario" maxlength="50" placeholder="Ingrese Propietario" required
                                            onkeyup="this.value=this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>CELULAR</label>
                                        <input type="number" class="form-control" id="MAV_celular" name="MAV_celular"
                                            maxlength="12" placeholder="999999999">
                                    </div>
                                </div>

                                <!-- ROW -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>UNIDAD</label>
                                        <input type="text" class="form-control" id="MAV_Unidad" name="MAV_Unidad"
                                            maxlength="40" placeholder="Ingrese Unidad"
                                            onkeyup="this.value=this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>KM</label>
                                        <input type="text" class="form-control" id="MAV_KMEntrada" name="MAV_KMEntrada"
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
                                    <textarea class="form-control" rows="4" name="MAV_DetalleIngreso" id="MAV_DetalleIngreso"
                                        onkeyup="handleText(this); this.value=this.value.toUpperCase();"></textarea>
                                </div>
                                <div class="form-group mb-0">
                                    <label>OBSERVACIONES</label>
                                    <textarea class="form-control" rows="4" name="MAV_DetalleObservacion" id="MAV_DetalleObservacion"
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
                                <div class="form-group">
                                    <label>DETALLE DE LO REALIZADO</label>
                                    <textarea class="form-control" rows="5" name="MAV_DetalleRealizado" id="MAV_DetalleRealizado"
                                        onkeyup="handleText(this); this.value=this.value.toUpperCase();"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>CORRECCIÓN DE OBSERVACIONES</label>
                                    <textarea class="form-control" rows="5" name="MAV_CorrecionObservacion" id="MAV_CorrecionObservacion"
                                        onkeyup="handleText(this); this.value=this.value.toUpperCase();"></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>PRÓXIMO CAMBIO DE ACEITE</label>
                                        <input type="text" class="form-control" id="MAV_ProximoCambioAceite"
                                            name="MAV_ProximoCambioAceite" maxlength="50" placeholder="#### KM" required
                                            onkeyup="this.value=this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>PRÓXIMO SERVICIO</label>
                                        <input type="text" class="form-control" id="MAV_ProximoServicio"
                                            name="MAV_ProximoServicio" maxlength="50" placeholder="#### KM" required
                                            onkeyup="this.value=this.value.toUpperCase();">
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
                                            <input class="form-control" id="MAVD_Descripcion_Aux"
                                                name="MAVD_Descripcion_Aux" type="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>PRECIO</label>
                                            <input class="form-control" id="MAVD_Precio_Aux" name="MAVD_Precio_Aux"
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

            // GUARDAR
            $('#saveBtn').on('click', function(e) {
                e.preventDefault();
                AgregarMAV()
            });

        });

        function agregardetalle() {

            var descripcion = $('#MAVD_Descripcion_Aux').val();
            var precio = $('#MAVD_Precio_Aux').val();

            if (descripcion != "" && precio != "") {
                var fila1 = [descripcion, precio];
                ListDetVenta.push(fila1);
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

        function cargarTabla(){
            $("#detalles tbody").html('');
                var totalCosto = 0
                for (var i = ListDetVenta.length - 1; i >= 0; i--) {
                    var col0 = '<tr  onClick="MostrarValores1(' + ListDetVenta[i][0] + ');" id="fila' + i + '">'
                    var col1 = '<td style="text-align: left;">' + (i + 1) + '</td>'
                    var col2 = '<td style="text-align: left;"><input id="MAVD_Descripcion' + i + '" type="hidden" name="MAVD_Descripcion[]" value="' + ListDetVenta[i][0] +
                        '">' + ListDetVenta[i][0] + '</td>'
                    var col3 = '<td style="text-align: left;"><input id="MAV_Precio' + i + '" type="hidden" name="MAV_Precio[]" value="' + ListDetVenta[i][1] +
                        '">' + ListDetVenta[i][1] + '</td>'
                    var col4 =
                        '<td style="width:80px; height : 24px; text-align: center;"><button  type="button"  class="btn" onclick="eliminar(' +
                        i +
                        ');" style="border-radius: 10px; height : 24px; color:red;padding:0px"><i class="fa fa-trash"></button></td></tr>';
                    var fila = col0 + col1 + col2 + col3 + col4 ;
                    $("#detalles").append(fila);
                    totalCosto = parseFloat(totalCosto) + parseFloat(ListDetVenta[i][1])
                }

                $("#totalCosto").html(totalCosto);
        }

        function BuscarCliente() {
            ocultar()
            var url = "/busquedaplaca/responsable/" + $('#MAV_Placa').val() + '?';
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    if (data.success.validacion == "true") {
                        $('#MAV_Propietario').val(data.success.mtto[0].Propietario);
                        $('#MAV_celular').val(data.success.mtto[0].celular);
                        $('#MAV_Unidad').val(data.success.mtto[0].Unidad);
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
            $('#MAVD_Descripcion_Aux').val('');
            $('#MAVD_Precio_Aux').val('');

        }

        function eliminar(index) {
            $('#fila' + index).remove();
            ListDetVenta.splice(index, 1);
            cargarTabla()
        }


        function AgregarMAV() {
            $.ajax({
                data: $('#MantenimientoActividadVariadasForm').serialize(),
                url: "{{ route('tenant.actividades.mantenimientoactividadvariada.store') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    console.log('Success:', data);
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    })
                    document.location.href =
                        "{{ route('tenant.actividades.mantenimientoactividadvariada.index') }}/" + data.id +
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
            document.location.href = "{{ route('tenant.actividades.mantenimientoactividadvariada.index') }}/"
        }
    </script>
@endsection
