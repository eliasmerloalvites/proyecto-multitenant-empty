@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Cajas')
@section('contenido')

    @can('tenant.ventas.caja.create')
        <div class="col-12 col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR CAJA</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="caja_form" action="{{ tenant_url('tenant.ventas.caja.store') }}">
                        @csrf
                        <input type="text" id="caja_id_edit" hidden>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Nombre de la caja:</label>
                                <input type="text" id="CAJ_Nombre" name="CAJ_Nombre" class="form-control"
                                    placeholder="Ej. Caja 1" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Sede / Local:</label>
                                <select id="ALM_Id" name="ALM_Id" class="form-control">
                                    <option value="">— Sin asignar —</option>
                                    @foreach ($almacenes as $almacen)
                                        <option value="{{ $almacen->ALM_Id }}">{{ $almacen->ALM_NombreAlmacen }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Monto de apertura por defecto (S/):</label>
                                <input type="number" step="0.01" min="0" id="CAJ_MontoApertura" name="CAJ_MontoApertura"
                                    class="form-control" placeholder="0.00" value="0">
                            </div>
                        </div>
                        <div class="form-group row" id="estadoCajaRow" style="display:none;">
                            <div class="col-12">
                                <label class="control-label" style="text-align: left; display: block;">Estado:</label>
                                <select id="CAJ_Status" name="CAJ_Status" class="form-control">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="CAJ_ProgramacionActiva" name="CAJ_ProgramacionActiva" value="1">
                                    <label class="custom-control-label" for="CAJ_ProgramacionActiva">Apertura/cierre automático por horario</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="programacionRow" style="display:none;">
                            <div class="col-6">
                                <label class="control-label" style="text-align: left; display: block;">Hora de apertura:</label>
                                <input type="time" id="CAJ_HoraApertura" name="CAJ_HoraApertura" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="control-label" style="text-align: left; display: block;">Hora de cierre:</label>
                                <input type="time" id="CAJ_HoraCierre" name="CAJ_HoraCierre" class="form-control">
                            </div>
                        </div>

                        <p></p>
                        <div class="form-group text-right">
                            <button id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                            <button id="updateBtn" class="btn btn-info" style="display: none;"><i
                                    class="fas fa-save"></i> Actualizar</button>
                            <button type="reset" id="btncancelar" class="btn btn-danger"> <i
                                    class="fas fa-ban"></i> Cancelar </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('tenant.ventas.caja.index')
        <div class="col-12 col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">LISTA DE CAJAS</h5>
                        <a href="{{ tenant_url('tenant.ventas.caja.historial') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-clock-rotate-left mr-1"></i> Historial de arqueos
                        </a>
                    </div>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="tabla_caja">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Caja</th>
                                    <th scope="col">Sede</th>
                                    <th scope="col">Monto Apertura</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Turno</th>
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                icon: 'info'
            });

            $('#CAJ_ProgramacionActiva').change(function() {
                $('#programacionRow').toggle(this.checked);
            });

            var table = $('#tabla_caja').DataTable({
                responsive: true,
                autoWidth: false,
                searchDelay: 800,
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
                ajax: "{{ tenant_url('tenant.ventas.caja.index') }}",
                columns: [{
                        data: 'CAJ_Id',
                        name: 'CAJ_Id'
                    },
                    {
                        data: 'CAJ_Nombre',
                        name: 'CAJ_Nombre'
                    },
                    {
                        data: 'sede',
                        name: 'sede'
                    },
                    {
                        data: 'CAJ_MontoApertura',
                        name: 'CAJ_MontoApertura',
                        'render': function(data) {
                            return 'S/ ' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'estado',
                        name: 'estado'
                    },
                    {
                        data: 'turno_action',
                        name: 'turno_action'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return data.action1 + ' ' + data.action2
                        }
                    }
                ]
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                nombre = $("#CAJ_Nombre").val();

                if (nombre == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                $.ajax({
                    data: $('#caja_form').serialize(),
                    url: "{{ tenant_url('tenant.ventas.caja.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        cancelarUpdate();
                        table.draw();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : 'La caja falló al registrarse.';
                        Toast.fire({
                            type: 'error',
                            title: msg
                        })
                    }
                });
            });

            $('body').on('click', '.editCaja', function() {
                var caja_id_edit = $(this).data('identificador');
                $.get('{{ tenant_url('tenant.ventas.caja.edit', ['caja' => ':caja']) }}'.replace(':caja', caja_id_edit),
                    function(result) {
                        $('#caja_id_edit').val(result.data.CAJ_Id);
                        $('#CAJ_Nombre').val(result.data.CAJ_Nombre);
                        $('#ALM_Id').val(result.data.ALM_Id);
                        $('#CAJ_MontoApertura').val(result.data.CAJ_MontoApertura);
                        $('#CAJ_Status').val(result.data.CAJ_Status ? '1' : '0');
                        $('#estadoCajaRow').show();
                        $('#CAJ_ProgramacionActiva').prop('checked', !!result.data.CAJ_ProgramacionActiva);
                        $('#CAJ_HoraApertura').val(result.data.CAJ_HoraApertura ? result.data.CAJ_HoraApertura.substring(0,5) : '');
                        $('#CAJ_HoraCierre').val(result.data.CAJ_HoraCierre ? result.data.CAJ_HoraCierre.substring(0,5) : '');
                        $('#programacionRow').toggle(!!result.data.CAJ_ProgramacionActiva);

                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                caja_id_update = $('#caja_id_edit').val();
                $.ajax({
                    data: $('#caja_form').serialize(),
                    url: '{{ tenant_url('tenant.ventas.caja.update', ['caja' => ':caja']) }}'.replace(
                        ':caja', caja_id_update),
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        });
                        cancelarUpdate();
                        table.draw();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : 'La caja falló al actualizarse.';
                        Toast.fire({
                            type: 'error',
                            title: msg
                        })
                    }
                });
            });

            $('#btncancelar').click(function(e) {
                cancelarUpdate();
                Toast.fire({
                    icon: 'info',
                    title: 'Registro cancelado'
                });
            });

            function cancelarUpdate() {
                $('#caja_form').trigger("reset");
                $("#caja_id_edit").val('');
                $('#estadoCajaRow').hide();
                $('#programacionRow').hide();
                $("#saveBtn").show();
                $("#updateBtn").hide();
            }

            $('body').on('click', '.deleteCaja', function() {
                var caja_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarla?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",
                        url: '{{ tenant_url('tenant.ventas.caja.destroy', ['caja' => ':caja']) }}'.replace(
                            ':caja', caja_id_delete),
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                            Toast.fire({
                                type: 'success',
                                title: String(data.success),
                                icon: 'info'
                            });
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.error
                                ? xhr.responseJSON.error
                                : 'La caja falló al eliminarse.';
                            Toast.fire({
                                type: 'error',
                                title: msg,
                                icon: 'info'
                            })
                        }
                    });
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'La caja no ha sido eliminada.',
                        icon: 'info'
                    });
                }
            });

            // APERTURAR
            $('body').on('click', '.aperturarCaja', function() {
                var cajaId = $(this).data('id');
                var nombre = $(this).data('nombre');
                var montoDefault = $(this).data('monto');

                Swal.fire({
                    title: 'Aperturar "' + nombre + '"',
                    input: 'number',
                    inputLabel: 'Monto de apertura (S/)',
                    inputValue: montoDefault,
                    inputAttributes: { step: '0.01', min: '0' },
                    showCancelButton: true,
                    confirmButtonText: 'Aperturar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ tenant_url('tenant.caja-sesion.abrir') }}",
                        type: 'POST',
                        data: { caja_id: cajaId, monto_apertura: result.value },
                        dataType: 'json',
                        success: function(data) {
                            Toast.fire({ type: 'success', title: data.success });
                            table.draw();
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo aperturar la caja.';
                            Toast.fire({ type: 'error', title: msg });
                        }
                    });
                });
            });

            // CERRAR
            $('body').on('click', '.cerrarCaja', function() {
                var cajaId = $(this).data('id');
                var nombre = $(this).data('nombre');

                Swal.fire({
                    title: 'Cerrar "' + nombre + '"',
                    input: 'number',
                    inputLabel: 'Monto real contado en caja (S/)',
                    inputAttributes: { step: '0.01', min: '0' },
                    showCancelButton: true,
                    confirmButtonText: 'Cerrar caja',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ tenant_url('tenant.caja-sesion.cerrar') }}",
                        type: 'POST',
                        data: { caja_id: cajaId, monto_real: result.value },
                        dataType: 'json',
                        success: function(data) {
                            var dif = parseFloat(data.sesion.CS_Diferencia);
                            var msg = 'Caja cerrada. Diferencia: S/ ' + dif.toFixed(2);
                            Toast.fire({ type: dif === 0 ? 'success' : 'warning', title: msg });
                            table.draw();
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo cerrar la caja.';
                            Toast.fire({ type: 'error', title: msg });
                        }
                    });
                });
            });
        });
    </script>
@endsection
