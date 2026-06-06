@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Turno')
@section('contenido')

    @can('tenant.configuracion.turno.create')
        <div class="col-12 col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR TURNO</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="turno_form" action="{{ tenant_url('tenant.configuracion.turno.store') }}">
                        @csrf
                        <input type="text" id="turno_id_edit" hidden>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Turno:</label>
                                <input type="text" id="TUR_Nombre" name="TUR_Nombre" class="form-control "
                                    placeholder="Turno" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Descripción:</label>
                                <input type="text" id="TUR_Descripcion" name="TUR_Descripcion" class="form-control "
                                    placeholder="Descripción" required>
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

    @can('tenant.configuracion.turno.index')
        <div class="col-12 col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE TURNO</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="tabla_turno">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Turno</th>
                                    <th scope="col">Descripcion</th>
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
                $('#turno_form').trigger('reset');
                $('#turno_id_edit').val('');
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

            const table = $('#tabla_turno').DataTable({
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

                ajax: "{{ tenant_url('tenant.configuracion.turno.index') }}",

                columns: [{
                        data: 'TUR_Id',
                        name: 'TUR_Id'
                    },
                    {
                        data: 'TUR_Nombre',
                        name: 'TUR_Nombre'
                    },
                    {
                        data: 'TUR_Descripcion',
                        name: 'TUR_Descripcion'
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

                const turno = $('#TUR_Nombre').val().trim();
                const descripcion = $('#TUR_Descripcion').val().trim();

                if (!turno || !descripcion) {
                    showToast('warning', 'Complete todos los campos');
                    return;
                }

                $.ajax({

                    url: "{{ tenant_url('tenant.configuracion.turno.store') }}",
                    type: "POST",
                    data: $('#turno_form').serialize(),
                    dataType: 'json',

                    success: function(data) {
                        showToast('success', data.message);
                        resetForm();
                        reloadTable();
                    },

                    error: function(error) {
                        handleAjaxError('Ya existe un turno registrado con ese nombre.', error);
                    }

                });

            });

            // EDITAR

            $('body').on('click', '.editTurno', function() {

                const turnoId = $(this).data('id');

                $.get(
                    '{{ tenant_url('tenant.configuracion.turno.edit', ['turno' => ':turno']) }}'
                    .replace(':turno', turnoId),

                    function(response) {

                        $('#turno_id_edit').val(response.data.TUR_Id);
                        $('#TUR_Nombre').val(response.data.TUR_Nombre);
                        $('#TUR_Descripcion').val(response.data.TUR_Descripcion);

                        $('#saveBtn').hide();
                        $('#updateBtn').show();

                    }

                );

            });

            // ACTUALIZAR

            $('#updateBtn').on('click', function(e) {

                e.preventDefault();

                const turnoId = $('#turno_id_edit').val();

                $.ajax({

                    url: '{{ tenant_url('tenant.configuracion.turno.update', ['turno' => ':turno']) }}'
                        .replace(':turno', turnoId),
                    type: "PUT",
                    data: $('#turno_form').serialize(),
                    dataType: 'json',

                    success: function(data) {
                        showToast('success', data.message);
                        resetForm();
                        reloadTable();
                    },

                    error: function(error) {
                        handleAjaxError('El turno falló al actualizarse.', error);
                    }

                });

            });

            // CANCELAR

            $('#btncancelar').on('click', function() {
                resetForm();
                showToast('info', 'Formulario reiniciado correctamente');
            });

            // ELIMINAR

            $('body').on('click', '.deleteTurno', function() {

                const turnoId = $(this).data('id');

                Swal.fire({

                    title: '¿Eliminar turno?',
                    text: 'El turno será desactivado.',
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

                        url: '{{ tenant_url('tenant.configuracion.turno.destroy', ['turno' => ':turno']) }}'
                            .replace(':turno', turnoId),
                        type: "DELETE",

                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },

                        error: function(error) {
                            handleAjaxError('El turno falló al eliminarse.', error);
                        }

                    });

                });

            });

            // ACTIVAR

            $('body').on('click', '.activarTurno', function() {

                const turnoId = $(this).data('id');

                Swal.fire({

                    title: '¿Activar turno?',
                    text: 'El turno volverá a estar disponible.',
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

                        url: '{{ tenant_url('tenant.configuracion.turno.activar', ['turno' => ':turno']) }}'
                            .replace(':turno', turnoId),
                        type: "PUT",

                        success: function(data) {
                            showToast('success', data.message);
                            reloadTable();
                        },

                        error: function(error) {
                            handleAjaxError('El turno falló al activarse.', error);
                        }

                    });

                });

            });

        });
    </script>
@endsection
