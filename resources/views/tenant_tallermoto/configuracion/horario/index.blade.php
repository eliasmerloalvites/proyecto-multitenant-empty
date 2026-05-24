@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Horario')
@section('contenido')

    @can('tenant.configuracion.horario.create')
        <div class="col-12 col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR HORARIO</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="horario_form" action="{{ tenant_url('tenant.configuracion.horario.store') }}">
                        @csrf
                        <input type="text" id="horario_id_edit" hidden>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Sede:</label>
                            <select class="form-control select2 select2-info" id="ALM_Id" name="ALM_Id"
                                data-dropdown-css-class="select2-info" style="width: 100%;">
                                <option value="">Seleccionar Sede</option>
                                @foreach ($sedes as $item)
                                    <option value="{{ $item->ALM_Id }}"> {{ $item->ALM_NombreAlmacen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Día:</label>
                            <select class="form-control select2 select2-info" id="HOR_Dia" name="HOR_Dia"
                                data-dropdown-css-class="select2-info" style="width: 100%;">
                                <option value="">Seleccionar Día</option>
                                <option value="Lunes">Lunes</option>
                                <option value="Martes">Martes</option>
                                <option value="Miércoles">Miércoles</option>
                                <option value="Jueves">Jueves</option>  
                                <option value="Viernes">Viernes</option>
                                <option value="Sábado">Sábado</option>
                                <option value="Domingo">Domingo</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Turno:</label>
                            <select class="form-control select2 select2-info" id="TUR_Id" name="TUR_Id"
                                data-dropdown-css-class="select2-info" style="width: 100%;">
                                <option value="">Seleccionar Turno</option>
                                @foreach ($turnos as $item)
                                    <option value="{{ $item->id }}"> {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Horario:</label>
                                <input type="text" id="HOR_Detalle" name="HOR_Detalle" class="form-control "
                                    placeholder="Horario" required>
                            </div>
                        </div>
                        <p></p>
                        <div class="form-group text-right">
                            <button type="button" id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                            <button type="button" id="updateBtn" class="btn btn-info" style="display: none;"><i class="fas fa-save"></i>
                                Actualizar</button>
                            <button type="reset" id="btncancelar" class="btn btn-danger"> <i class="fas fa-ban"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('tenant.configuracion.horario.index')
        <div class="col-12 col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE HORARIO</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="tabla_horario">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Sede</th>
                                    <th scope="col">Dia</th>
                                    <th scope="col">Turno</th>
                                    <th scope="col">Horario</th>
                                    <th scope="col">Opciones</th>
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

            const resetForm = () => {
                $('#horario_form').trigger('reset');
                $('#ALM_Id').val('');
                $('#ALM_Id').change();
                $('#TUR_Id').val('');
                $('#TUR_Id').change();
                $('#HOR_Dia').val('');
                $('#HOR_Dia').change();
                $('#horario_id_edit').val('');
                $('#saveBtn').show();
                $('#updateBtn').hide();
            };

            const reloadTable = () => {
                table.ajax.reload(null, false);
            };

            const handleAjaxError = (message, error = null) => {
                console.error(error);
                showToast('error', message);
            };

            // DATATABLE

            const table = $('#tabla_horario').DataTable({

                responsive: true,
                autoWidth: false,
                searchDelay: 800,
                processing: true,
                serverSide: true,

                order: [
                    [0, "asc"]
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

                ajax: "{{ tenant_url('tenant.configuracion.horario.index') }}",

                columns: [{
                        data: 'HOR_Id',
                        name: 'HOR_Id'
                    },
                    {
                        data: 'ALM_NombreAlmacen',
                        name: 'ALM_NombreAlmacen'
                    },
                    {
                        data: 'HOR_Dia',
                        name: 'HOR_Dia'
                    },
                    {
                        data: 'TUR_Nombre',
                        name: 'TUR_Nombre'
                    },
                    {
                        data: 'HOR_Detalle',
                        name: 'HOR_Detalle'
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

            // GUARDAR

            $('#saveBtn').on('click', function(e) {

                e.preventDefault();

                const dia = $('#HOR_Dia').val();
                const horario = $('#HOR_Detalle').val().trim();
                const sede = $('#ALM_Id').val();
                const turno = $('#TUR_Id').val();

                if (!dia || !horario || !sede || !turno) {
                    showToast('warning', 'Complete todos los campos');
                    return;
                }

                $.ajax({

                    url: "{{ tenant_url('tenant.configuracion.horario.store') }}",
                    type: "POST",
                    data: $('#horario_form').serialize(),
                    dataType: 'json',

                    success: function(data) {
                        showToast('success', data.message);
                        resetForm();
                        reloadTable();
                    },

                    error: function(error) {
                        handleAjaxError('Ya existe un horario registrado con ese nombre.', error);
                    }

                });

            });

            // EDITAR

            $('body').on('click', '.editHorario', function() {

                const horarioId = $(this).data('id');

                $.get(
                    '{{ tenant_url('tenant.configuracion.horario.edit', ['horario' => ':horario']) }}'
                    .replace(':horario', horarioId),

                    function(response) {

                        $('#horario_id_edit').val(response.data.HOR_Id);
                        $('#ALM_Id').val(response.data.ALM_Id);
                        $('#ALM_Id').change();
                        $('#TUR_Id').val(response.data.TUR_Id);
                        $('#TUR_Id').change();
                        $('#HOR_Dia').val(response.data.HOR_Dia);
                        $('#HOR_Dia').change();
                        $('#HOR_Detalle').val(response.data.HOR_Detalle);

                        $('#saveBtn').hide();
                        $('#updateBtn').show();

                    }

                );

            });

            // ACTUALIZAR

            $('#updateBtn').on('click', function(e) {

                e.preventDefault();

                const horarioId = $('#horario_id_edit').val();

                $.ajax({

                    url: '{{ tenant_url('tenant.configuracion.horario.update', ['horario' => ':horario']) }}'
                        .replace(':horario', horarioId),
                    type: "PUT",
                    data: $('#horario_form').serialize(),
                    dataType: 'json',

                    success: function(data) {
                        showToast('success', data.message);
                        resetForm();
                        reloadTable();
                    },

                    error: function(error) {
                        handleAjaxError('El horario falló al actualizarse.', error);
                    }

                });

            });

            // CANCELAR

            $('#btncancelar').on('click', function() {
                resetForm();
                showToast('info', 'Formulario reiniciado correctamente');
            });

            // ELIMINAR

            $('body').on('click', '.deleteHorario', function() {

                const horarioId = $(this).data('id');

                Swal.fire({

                    title: '¿Eliminar horario?',
                    text: 'El horario será desactivado.',
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

                        url: '{{ tenant_url('tenant.configuracion.horario.destroy', ['horario' => ':horario']) }}'
                            .replace(':horario', horarioId),
                        type: "DELETE",

                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },

                        error: function(error) {
                            handleAjaxError('El horario falló al eliminarse.', error);
                        }

                    });

                });

            });

            // ACTIVAR

            $('body').on('click', '.activarHorario', function() {

                const horarioId = $(this).data('id');

                Swal.fire({

                    title: '¿Activar horario?',
                    text: 'El horario volverá a estar disponible.',
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

                        url: '{{ tenant_url('tenant.configuracion.horario.activar', ['horario' => ':horario']) }}'
                            .replace(':horario', horarioId),
                        type: "PUT",

                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },

                        error: function(error) {
                            handleAjaxError('El horario falló al activarse.', error);
                        }

                    });

                });

            });

        });
    </script>
@endsection
