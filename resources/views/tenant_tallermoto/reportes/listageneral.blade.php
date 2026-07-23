@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Mantenimiento General Carburadas')
@section('contenido')

    @can('tenant.configuracion.bahia.index')
    <div class="col-12">

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">REPORTES DE LISTA GENERAL</h4>
                </div>
                <!-- NUEVO -->
                <div>
                </div>
            </div>
            <!-- FILTROS -->
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio">
                        </div>
                        <!-- FECHA FIN -->
                        <div class="col-md-3">
                            <label>Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin">
                        </div>

                        <!-- ESTADO -->
                        <div class="col-md-3">
                            <label>Estado</label>
                            <select class="form-control" id="estado">
                                <option value="">Todos</option>
                                <option value="PENDIENTE">PENDIENTE</option>
                                <option value="APROBADO">APROBADO</option>
                            </select>
                        </div>

                        <!-- BOTON -->
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary btn-block" id="btnFiltrar">
                                <i class="fas fa-search mr-1"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLA -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped" id="tabla_listageneral">
                    <thead class="bg-light">
                        <tr>
                            <th>N°</th>
                            <th>Reporte</th>
                            <th>Placa</th>
                            <th>Propietario</th>
                            <th>celularnotificar</th>
                            <th>Unidad</th>
                            <th>KM Entrada</th>
                            <th>Técnico</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Observacion</th>
                            <th>Solucion</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="ajaxModelObservar" aria-hidden="true">
        <div class="modal-dialog modal-md">                
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="formDataObservacion" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" name="id"  id="idMtto">
                        <input type="hidden" name="Url"  id="urlMtto">
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                            <label>
                                Descripcion
                            </label>
                            <textarea class="form-control" id="observacion" name="observacion" required placeholder="Describe detalladamente la observacion"
                                style="background: #FFFFFF;font-size: 16px; margin-bottom: 5px;" rows="6" onKeyUp="this.value=this.value.toUpperCase();" title="Descripción de Observacion" type="textarea"></textarea>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px;">
                            <div class="row" >
                                <div class="btn-group pull-left col-lg-6 col-md-6 col-sm-12 col-xs-5"
                                    style=" padding-top: 5px; padding-bottom: 5px">
                                    <button class="btn btn-sm" style="background:#afafaf; color: #fff; border-radius: 10px;height:35px;margin:10px" type="button" data-dismiss="modal">
                                        <b> Cerrar</b>
                                    </button>
                                </div>
                                <div class="btn-group pull-right col-lg-6 col-md-6 col-sm-12 col-xs-5"
                                    style=" padding-top: 5px; padding-bottom: 5px">
                                    <button class="btn btn-sm" id="btnGuardarObservacion"
                                        style="background:#28a745; color: #fff; border-radius: 10px;height:35px;margin:10px" type="button" onclick="AgregarObservacion()">
                                        Guardar Observacion
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModelResponder" aria-hidden="true">
        <div class="modal-dialog modal-md">                
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeadingRespuesta"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="formDataRespuesta" name="userForm" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" name="id"  id="idMttoRpta">
                        <input type="hidden" name="Url"  id="urlMttoRpta">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " align="left">
                            <label  style="color: #df6666; font-size: 14px;" >OBSERVACIÓN</label><br/>
                            <label style="color: #444444; font-size: 14px;" id="TextObservacion" ></label>
                        </div>
                        
                        <div id="contentRespuestaDetalle" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " align="left">                            
                                <label  style="color: #3fa7fc; font-size: 14px;" >RESPUESTA</label><br/>
                                <label style="color: #444444; font-size: 14px;" id="TextRespuesta" ></label>
                            </div>
                        </div>
                        <div id="contentRespuesta" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 "  align="left">                            
                                <label  style="color: #3fa7fc; font-size: 14px;" >INGRESE RESPUESTA</label><br/>
                                <textarea class="form-control" id="respuesta" name="respuesta" required placeholder="Describe detalladamente la acción que tomaste"
                                    style="background: #FFFFFF;font-size: 16px; margin-bottom: 5px;" rows="6" onKeyUp="this.value=this.value.toUpperCase();" title="Descripción" type="textarea"></textarea>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px;">
                                <div class="row" >
                                    <div class="btn-group pull-left col-lg-6 col-md-6 col-sm-12 col-xs-5"
                                        style=" padding-top: 5px; padding-bottom: 5px">
                                        <button class="btn btn-sm" style="background:#afafaf; color: #fff; border-radius: 10px;height:35px;margin:10px" type="button" data-dismiss="modal">
                                            <b> Cerrar</b>
                                        </button>
                                    </div>
                                    {{-- <div class="btn-group pull-right col-lg-6 col-md-6 col-sm-12 col-xs-5"
                                        style=" padding-top: 5px; padding-bottom: 5px">
                                        <button class="btn btn-sm" style="background:#f4ae17; color: #fff; border-radius: 10px;height:35px;margin:10px" type="button" onclick="AgregarCordinacion()">
                                            Coordinado
                                        </button>
                                    </div> --}}
                                    <div class="btn-group pull-right col-lg-6 col-md-6 col-sm-12 col-xs-5"
                                        style=" padding-top: 5px; padding-bottom: 5px">
                                        <button class="btn btn-sm" style="background:#28a745; color: #fff; border-radius: 10px;height:35px;margin:10px" type="button" onclick="AgregarRespuesta()">
                                            Solucionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endcan

@endsection
@section('script')
    <script>
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

            let table;

            const reloadTable = () => {
                table.ajax.reload(null, false);
            };

            const handleAjaxError = (message, error = null) => {
                console.error(error);
                showToast('error', message);
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

            

            // DATATABLE

            table = $('#tabla_listageneral').DataTable({
                responsive: true,
                autoWidth: false,
                searchDelay : 800,
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                dom: 'Blfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'pdfHtml5'
                ],
                 ajax: {
                    url: "{{ tenant_url('tenant.reportes.listageneral') }}",
                    data: function(d) {
                        d.fecha_inicio = $('#fecha_inicio').val();
                        d.fecha_fin    = $('#fecha_fin').val();
                        d.estado       = $('#estado').val();
                    }
                },
                columns: [
                    {
                            data: 'Id',
                            name: 'Id'
                        },
                        {
                            data: 'Tipo',
                            name: 'Tipo'
                        },
                        {
                            data: 'Placa',
                            name: 'Placa'
                        },
                        {
                            data: 'Propietario',
                            name: 'Propietario'
                        },
                        {
                            data: 'celularnotificar',
                            name: 'celularnotificar'
                        },
                        {
                            data: 'Unidad',
                            name: 'Unidad'
                        },
                        {
                            data: 'KMEntrada',
                            name: 'KMEntrada'
                        },
                        {
                            data: 'personal',
                            name: 'personal'
                        },
                        {
                            data: 'FechaCreacion',
                            name: 'FechaCreacion'
                        },
                        {
                            data: 'estado',
                            name: 'estado'
                        },
                        {
                            data: 'observaciones',
                            name: 'observaciones'
                        },
                        {
                            data: 'respuestas',
                            name: 'respuestas'
                        },
                        {
                            data: null,
                            name: 'name',
                            'render': function(data, type, row) {
                                return @can('tenant.mantenimientos.generalcarburada.destroy') data.action3 +' '+ @endcan ''
                                @can('tenant.mantenimientos.generalcarburada.edit') +data.action4 +' '+ @endcan ''
                                @can('tenant.mantenimientos.generalcarburada.edit') +data.action1 +' '+ @endcan ''
                                @can('tenant.mantenimientos.generalcarburada.edit') +data.action2 @endcan;
                            }
                        }
                ]

            });


            $('#btnFiltrar').on('click', function () {
                table.ajax.reload();
            });

            $('body').on('click', '.editar', function() {
                    var id = $(this).data('id');
                    var url = $(this).data('url');
			        document.location.href=url +'/' + id +'/edit'
                });


                $('body').on('click', '.modalObservar', function() {
                    var _id = $(this).data("id");
                    var url = $(this).data("url");
                    var data = $(this).data("info");
                    $('#idMtto').val(_id);
                    $('#urlMtto').val(url);
                    if(data == null || data == ''){
                        $('#observacion').val('');
                        $('#btnGuardarObservacion').html("Guardar Observacion");
                        $('#modelHeading').html("Agregar Observacion");
                    }else{
                        $('#observacion').val(data);
                        $('#btnGuardarObservacion').html("Actualizar Observacion");
                        $('#modelHeading').html("Actualizar Observacion");
                    }
                    $('#ajaxModelObservar').modal('show');

                });

                $('body').on('click', '.modalResponder', function() {
                    var _id = $(this).data("id");
                    var url = $(this).data("url");
                    var dataInformacion = $(this).data("infoobservacion");
                    var data = $(this).data("info");
                    $admintotal = '<?php echo $rolAdmin; ?>';
                    $('#idMttoRpta').val(_id);
                    $('#urlMttoRpta').val(url);
                    if($admintotal){                        
                        document.getElementById('contentRespuestaDetalle').style.display = 'none';
                        document.getElementById('contentRespuesta').style.display = 'block';
                    }else{
                        document.getElementById('contentRespuestaDetalle').style.display = 'block';
                        document.getElementById('contentRespuesta').style.display = 'none';
                    }                    
                    
                    if(data == null || data == ''){
                        $('#TextObservacion').html(dataInformacion);
                        $('#respuesta').val('');
                        // $('#btnGuardarRespuesta').html("Guardar Respuesta");
                        $('#modelHeadingRespuesta').html("Agregar Respuesta");
                    }else{
                        $('#TextObservacion').html(dataInformacion);
                        $('#TextRespuesta').html(data);
                        $('#respuesta').val(data);
                        // $('#btnGuardarRespuesta').html("Actualizar Respuesta");
                        $('#modelHeadingRespuesta').html("Actualizar Respuesta");
                    }
                    $('#ajaxModelResponder').modal('show');

                });
            // ELIMINAR

            $('body').on('click', '.deleteMantenimientoGeneralCarburadas', function() {
                const generalCarburadasId = $(this).data('id');
                Swal.fire({
                    title: '¿Eliminar Actividad Variada?',
                    text: 'La actividad variada será eliminada.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) {
                        showToast('info', 'Acción cancelada');
                        return;
                    }

                    $.ajax({
                        url: '{{ tenant_url('tenant.mantenimientos.generalcarburada.destroy', ['generalcarburada' => ':generalcarburada']) }}'
                            .replace(':generalcarburada', generalCarburadasId),
                        type: "DELETE",
                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },
                        error: function(error) {
                            handleAjaxError('El mantenimiento falló al eliminarse.', error);
                        }

                    });

                });

            });

            // ACTIVAR

            $('body').on('click', '.activar', function() {
                var _id = $(this).data("id");
                var url = $(this).data("url");
                Swal.fire({
                    title: '¿Activar Mantenimiento?',
                    text: 'El mantenimiento volverá a estar disponible.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, activar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }
                    $.ajax({
                        data:'&notificar=1',
                        url: url + '/' + _id + '/actualizarestado',
                        type: "PUT",

                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },

                        error: function(error) {
                            handleAjaxError('El mantenimiento falló al activarse.', error);
                        }

                    });

                });

            });

            //APROBAR
             $('body').on('click', '.aprobar', function() {
                var _id = $(this).data("id");
                var url = $(this).data("url");
                Swal.fire({
                    title: 'Aprobaras este servicio',
                    text: "Selecciona una opción:",
                    icon: 'info',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Aceptar',
                    denyButtonText: 'Aceptar y notificar',
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false, // 👈 Desactiva el estilo por defecto
                    customClass: {
                        confirmButton: 'btn btn-success',   // verde
                        denyButton: 'btn btn-primary mx-2',      // azul
                        cancelButton: 'btn btn-danger'      // rojo
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                            $.ajax({
                                data:'&estado=APROBADO&notificar=2',
                                url: url + '/' + _id + '/actualizarestado',
                                type: "PUT",
                                
                                success: function(data) {
                                    showToast('success', data.message);
                                    reloadTable();
                                },
                                error: function(error) {
                                    handleAjaxError('El mantenimiento falló al aprobarse y notificarse.', error);
                                }
                            });
                            console.log('Aceptado');
                        } else if (result.isDenied) {
                            $.ajax({
                                data:'&estado=APROBADO',
                                type: "PUT",
                                url: url + '/' + _id + '/actualizarestado',
                                success: function(data) {
                                    showToast('success', data.message);
                                    reloadTable();

                                },
                                error: function(error) {
                                    console.log('Error:', error);
                                    handleAjaxError('El mantenimiento falló al aprobarse.', error);

                                }
                            });
                            console.log('Aceptado y notificado');
                        } else {
                            console.log('Cancelado');
                        }

                });

            });
            
            
        });

        function AgregarObservacion() {
            var _id = $('#idMtto').val()
            var url = $('#urlMtto').val()
            var observacion = $('#observacion').val()

            $.ajax({
                data:'&observacion=' + observacion,
                url: url + '/' + _id + '/actualizarestado',
                type: "PUT",
                dataType: 'json',
                success: function(data) {
                    $('#ajaxModelObservar').modal('hide');
                    $('#formDataObservacion').trigger("reset");
                    showToast('success', data.message);
                    reloadTable();
                },
                error: function(error) {
                    console.log('Error:', error);
                    handleAjaxError('El mantenimiento falló al observarse.', error);
                    $('#saveBtn').html('Save Changes');
                }
            });
        }

        function AgregarRespuesta() {
            var _id = $('#idMttoRpta').val()
            var url = $('#urlMttoRpta').val()
            var respuesta = $('#respuesta').val()

            $.ajax({
                data:'&respuesta=' + respuesta,
                url: url + '/' + _id + '/actualizarestado',
                type: "PUT",
                dataType: 'json',
                success: function(data) {
                    $('#ajaxModelResponder').modal('hide');
                    $('#formDataRespuesta').trigger("reset");
                    showToast('success', data.message);
                    reloadTable();
                },
                error: function(data) {
                    console.log('Error:', data);
                    handleAjaxError('El mantenimiento falló al responder.', error);
                    $('#saveBtn').html('Save Changes');
                }
            });
        }

    </script>
@endsection
