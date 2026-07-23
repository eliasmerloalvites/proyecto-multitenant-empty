@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Reservaciones')
@section('contenido')

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px" >
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">RESERVACIONES</h4>
                        <small class="text-muted">Gestión y seguimiento de reservaciones</small>
                    </div>
                    <!-- NUEVO -->
                    <div>
                        <a href="{{ tenant_url('tenant.reservaciones.administracion.create') }}"
                        class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i>
                            Nuevo Registro
                        </a>
                    </div>
                </div>

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

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="tabla_reservaciones">
                        <thead class="bg-light">
                            <tr>
                                <th>N°</th>
                                <th>Sede</th>
                                <th>Fecha Programada</th>
                                <th>Moto</th>
                                <th>Cliente</th>
                                <th>Celular</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        
                    </table>
                </div>
                    
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            var metodo = "AGREGAR";
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            var table;
            $(document).ready(function() {

                $('.select2').select2()

                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $("#ReservacionForm").on('keyup', function(e) {
                    if (e.key === 'Enter' || e.keyCode === 13) {
                        if (metodo == "EDITAR") {
                            Editar();
                        } else {
                            Agregar();
                        }
                    }
                });

                table = $('#tabla_reservaciones').DataTable({
                    searchDelay : 2000,
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
                    ajax: "{{ route('tenant.reservaciones.administracion.index') }}",
                    columns: [{
                            data: 'RES_Id',
                            name: 'RES_Id'
                        },
                        {
                            data: 'sede',
                            name: 'a.ALM_NombreAlmacen'
                        },
                        {
                            data: 'Fecha',
                            name: 'Fecha',
                        },
                        {
                            data: 'RES_Moto',
                            name: 'RES_Moto',
                        },
                        {
                            data: 'RES_Cliente',
                            name: 'RES_Cliente',
                        },
                        {
                            data: 'RES_Celular',
                            name: 'RES_Celular',
                        },
                        {
                            data: 'RES_Detalle',
                            name: 'RES_Detalle',
                        },
                        {
                            data: 'RES_State',
                            name: 'RES_State',
                        }
                    ]
                });

                $('body').on('click', '.editReservacion', function() {
                    var Reservacion_id = $(this).data('id');
                    $.get("{{ route('tenant.reservaciones.administracion.index') }}" + '/' + Reservacion_id + '/edit',
                        function(data) {
                            $('#idRES_Nombre').attr('disabled', false);
                            $('#idRES_Direccion').attr('disabled', false);
                            $('#idRES_Descripcion').attr('disabled', false);
                            $('#idRES_Id').val(data.RES_Id);
                            $('#idRES_Nombre').val(data.RES_Nombre);
                            $('#idRES_Direccion').val(data.RES_Direccion);
                            $('#idRES_Descripcion').val(data.RES_Descripcion);
                            metodo = "EDITAR"
                            CambioEditar();
                        })
                });
                $('#saveBtn').click(function(e) {
                    e.preventDefault();

                    Agregar();
                });
                $('#updateBtn').click(function(e) {
                    e.preventDefault();
                    Editar();
                });

                $('body').on('click', '.deleteReservacion', function() {

                    var Reservacion_id = $(this).data("id");
                    $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                    if ($confirm == true) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('tenant.reservaciones.administracion.index') }}" + '/' + Reservacion_id,
                            success: function(data) {
                                table.draw();
                                Toast.fire({
                                    type: 'success',
                                    title: String(data.success)
                                });

                            },
                            error: function(data) {
                                console.log('Error:', data);
                                Toast.fire({
                                    type: 'error',
                                    title: 'Reservacion fallo al Eliminarlo.'
                                })
                            }
                        });
                    }
                });

                $('body').on('click', '.activarReservacion', function() {

                    var Reservacion_id = $(this).data("id");
                    $confirm = confirm("¿Estás seguro de que quieres activarlo?");
                    if ($confirm == true) {
                        $.ajax({
                            type: "PUT",
                            url: "{{ route('tenant.reservaciones.administracion.index') }}" + '/' + Reservacion_id+'/activar',
                            success: function(data) {
                                table.draw();
                                Toast.fire({
                                    type: 'success',
                                    title: String(data.success)
                                });

                            },
                            error: function(data) {
                                console.log('Error:', data);
                                Toast.fire({
                                    type: 'error',
                                    title: 'Reservacion fallo al Eliminarlo.'
                                })
                            }
                        });
                    }
                });
            });

            function CambioEditar() {
                document.getElementById('updateBtn').style.display = 'block';
                document.getElementById('saveBtn').style.display = 'none';
            }

            function CambioCrear() {
                document.getElementById('updateBtn').style.display = 'none';
                document.getElementById('saveBtn').style.display = 'block';
            }

            function Limpiar() {
                $('#idRES_Nombre').val("");
                $('#idRES_Direccion').val("");
                $('#idRES_Descripcion').val("");
            }

            function Habilitar() {
                $('#idRES_Nombre').attr('disabled', false);
                $('#idRES_Direccion').attr('disabled', false);
                $('#idRES_Descripcion').attr('disabled', false);
                $('#btnCancelar').attr('disabled', false);
                $('#btnAgregaMedida').attr('disabled', false);
            }

            function Deshabilitar() {
                Limpiar();
                CambioCrear();
                $('#idRES_Nombre').attr('disabled', true);
                $('#idRES_Direccion').attr('disabled', true);
                $('#idRES_Descripcion').attr('disabled', true);
                $('#btnCancelar').attr('disabled', true);
                $('#btnAgregaMedida').attr('disabled', true);
            }

            function Agregar() {
                $.ajax({
                        data: $('#ReservacionForm').serialize(),
                        url: "{{ route('tenant.reservaciones.administracion.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            console.log('Success:', data);
                            Toast.fire({
                                type: 'success',
                                title: data.success
                            })
                            $('#ReservacionForm').trigger("reset");
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Reservacion fallo al Registrarse.'
                            })
                        }
                    });
            }
            function Editar() {
                Reservacion_id = $('#idRES_Id').val();
                    $.ajax({
                        data: $('#ReservacionForm').serialize(),
                        url: "{{ route('tenant.reservaciones.administracion.index') }}" + '/' + Reservacion_id,
                        type: "PUT",
                        dataType: 'json',
                        success: function(data) {
                            console.log('Success:', data);
                            Toast.fire({
                                type: 'success',
                                title: data.success
                            });
                            CambioCrear();
                            Deshabilitar();
                           // $('#ReservacionForm').trigger("reset");
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Reservacion fallo al Registrarse.'
                            })
                            $('#saveBtn').html('Save Changes');
                        }
                    });
            }
        </script>

    @endpush
@endsection
