@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Actividades Variadas')
@section('contenido')

    @can('tenant.configuracion.bahia.index')
    <div class="col-12">

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">LISTA DE ACTIVIDADES VARIADAS</h4>
                    <small class="text-muted">Gestión y seguimiento de actividades</small>
                </div>
                <!-- NUEVO -->
                <div>
                    <a href="{{ tenant_url('tenant.actividades.mantenimientoactividadvariada.create') }}"
                       class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Registro
                    </a>
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
                <table class="table table-hover table-bordered table-striped" id="tabla_mantenimientoactividadvariada">
                    <thead class="bg-light">
                        <tr>
                            <th>N°</th>
                            <th>Placa</th>
                            <th>Propietario</th>
                            <th>Celular</th>
                            <th>Unidad</th>
                            <th>KM Entrada</th>
                            <th>Responsable</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th width="180">Opciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
@section('script')
    <script>
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



            const reloadTable = () => {
                table.ajax.reload(null, false);
            };

            const handleAjaxError = (message, error = null) => {
                console.error(error);
                showToast('error', message);
            };

            // DATATABLE

            const table = $('#tabla_mantenimientoactividadvariada').DataTable({
                responsive: true,
                autoWidth: false,
                searchDelay: 800,
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron registros",
                    info: "Mostrando página _PAGE_ de _PAGES_",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    search: "Buscar:",
                    paginate: {
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                 ajax: {
                    url: "{{ tenant_url('tenant.actividades.mantenimientoactividadvariada.index') }}",
                    data: function(d) {
                        d.fecha_inicio = $('#fecha_inicio').val();
                        d.fecha_fin    = $('#fecha_fin').val();
                        d.estado       = $('#estado').val();
                    }
                },
                columns: [
                    {
                        data: 'MAV_Id',
                        name: 'MAV_Id'
                    },
                    {
                        data: 'MAV_Placa',
                        name: 'MAV_Placa'
                    },
                    {
                        data: 'MAV_Propietario',
                        name: 'MAV_Propietario'
                    },
                    {
                        data: 'celular',
                        name: 'celular'
                    },
                    {
                        data: 'MAV_Unidad',
                        name: 'MAV_Unidad'
                    },
                    {
                        data: 'MAV_KMEntrada',
                        name: 'MAV_KMEntrada'
                    },
                    {
                        data: 'personal',
                        name: 'personal'
                    },
                    {
                        data: 'MAV_FechaCreacion',
                        name: 'MAV_FechaCreacion'
                    },
                    {
                        data: 'estado',
                        name: 'estado'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,

                        render: function(data) {
                            return `${data.action1} ${data.action2}`;
                        }
                    }
                ]

            });


            $('#btnFiltrar').on('click', function () {
                table.ajax.reload();
            });
            // ELIMINAR

            $('body').on('click', '.deleteActividades Variadas', function() {

                const bahiaId = $(this).data('id');

                Swal.fire({

                    title: '¿Eliminar bahia?',
                    text: 'El bahia será desactivado.',
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

                        url: '{{ tenant_url('tenant.configuracion.bahia.destroy', ['bahia' => ':bahia']) }}'
                            .replace(':bahia', bahiaId),
                        type: "DELETE",

                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },

                        error: function(error) {
                            handleAjaxError('El bahia falló al eliminarse.', error);
                        }

                    });

                });

            });

            // ACTIVAR

            $('body').on('click', '.activarActividades Variadas', function() {

                const bahiaId = $(this).data('id');

                Swal.fire({

                    title: '¿Activar bahia?',
                    text: 'El bahia volverá a estar disponible.',
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

                        url: '{{ tenant_url('tenant.configuracion.bahia.activar', ['bahia' => ':bahia']) }}'
                            .replace(':bahia', bahiaId),
                        type: "PUT",

                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },

                        error: function(error) {
                            handleAjaxError('El bahia falló al activarse.', error);
                        }

                    });

                });

            });

        });
    </script>
@endsection
