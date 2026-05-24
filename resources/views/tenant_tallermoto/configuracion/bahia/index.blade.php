@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Bahia')
@section('contenido')

    @can('tenant.configuracion.bahia.create')
        <div class="col-12 col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR BAHIA</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="bahia_form" action="{{ tenant_url('tenant.configuracion.bahia.store') }}">
                        @csrf
                        <input type="text" id="bahia_id_edit" hidden>
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
                            <label class="control-label"  style=" text-align: left; display: block;">Responsable:</label>
                            <select class="form-control select2 select2-info" id="USU_Id" name="USU_Id"
                                data-dropdown-css-class="select2-info" style="width: 100%;">
                                <option value="">Seleccionar Responsable</option>
                                @foreach ($users as $item)
                                    <option value="{{ $item->id }}"> {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Bahia:</label>
                                <input type="text" id="BAH_Nombre" name="BAH_Nombre" class="form-control "
                                    placeholder="Bahia" required>
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

    @can('tenant.configuracion.bahia.index')
        <div class="col-12 col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE BAHIA</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="tabla_bahia">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Bahia</th>
                                    <th scope="col">Sede</th>
                                    <th scope="col">Responsable</th>
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
                $('#bahia_form').trigger('reset');
                $('#ALM_Id').val('');
                $('#ALM_Id').change();
                $('#USU_Id').val('');
                $('#USU_Id').change();
                $('#bahia_id_edit').val('');
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

            const table = $('#tabla_bahia').DataTable({

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

                ajax: "{{ tenant_url('tenant.configuracion.bahia.index') }}",

                columns: [{
                        data: 'BAH_Id',
                        name: 'BAH_Id'
                    },
                    {
                        data: 'ALM_NombreAlmacen',
                        name: 'ALM_NombreAlmacen'
                    },
                    {
                        data: 'BAH_Nombre',
                        name: 'BAH_Nombre'
                    },
                    {
                        data: 'USU_Nombre',
                        name: 'USU_Nombre'
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

                const bahia = $('#BAH_Nombre').val().trim();
                const sede = $('#ALM_Id').val();
                const responsable = $('#USU_Id').val();

                if (!bahia || !sede || !responsable) {
                    showToast('warning', 'Complete todos los campos');
                    return;
                }

                $.ajax({

                    url: "{{ tenant_url('tenant.configuracion.bahia.store') }}",
                    type: "POST",
                    data: $('#bahia_form').serialize(),
                    dataType: 'json',

                    success: function(data) {
                        showToast('success', data.message);
                        resetForm();
                        reloadTable();
                    },

                    error: function(error) {
                        handleAjaxError('Ya existe un bahia registrado con ese nombre.', error);
                    }

                });

            });

            // EDITAR

            $('body').on('click', '.editBahia', function() {

                const bahiaId = $(this).data('id');

                $.get(
                    '{{ tenant_url('tenant.configuracion.bahia.edit', ['bahia' => ':bahia']) }}'
                    .replace(':bahia', bahiaId),

                    function(response) {

                        $('#bahia_id_edit').val(response.data.BAH_Id);
                        $('#ALM_Id').val(response.data.ALM_Id);
                        $('#ALM_Id').change();
                        $('#USU_Id').val(response.data.USU_Id);
                        $('#USU_Id').change();
                        $('#BAH_Nombre').val(response.data.BAH_Nombre);

                        $('#saveBtn').hide();
                        $('#updateBtn').show();

                    }

                );

            });

            // ACTUALIZAR

            $('#updateBtn').on('click', function(e) {

                e.preventDefault();

                const bahiaId = $('#bahia_id_edit').val();

                $.ajax({

                    url: '{{ tenant_url('tenant.configuracion.bahia.update', ['bahia' => ':bahia']) }}'
                        .replace(':bahia', bahiaId),
                    type: "PUT",
                    data: $('#bahia_form').serialize(),
                    dataType: 'json',

                    success: function(data) {
                        showToast('success', data.message);
                        resetForm();
                        reloadTable();
                    },

                    error: function(error) {
                        handleAjaxError('El bahia falló al actualizarse.', error);
                    }

                });

            });

            // CANCELAR

            $('#btncancelar').on('click', function() {
                resetForm();
                showToast('info', 'Formulario reiniciado correctamente');
            });

            // ELIMINAR

            $('body').on('click', '.deleteBahia', function() {

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

            $('body').on('click', '.activarBahia', function() {

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
