@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')
@section('titulo', 'Metodo de Pago')
@section('contenido')

    @can('tenant.ventas.metodopago.create') 
        <div class="col-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR MÉTODO DE PAGO</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="metodoPago_form" action="{{ tenant_url('tenant.ventas.metodopago.store') }}">
                        @csrf
                        <input type="text" id="metodoPago_id_edit" hidden>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label"  style=" text-align: left; display: block;">Metodo de Pago:</label>
                                <input type="text" id="MEP_Pago" name="MEP_Pago" class="form-control "
                                    placeholder="Metodo de Pago" required>
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

    @can('tenant.ventas.metodopago.index')
        <div class="col-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE METODOS DE PAGO</h5>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;" >
                        <table class="table" id="tabla_metodoPago">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Metodo de Pago</th>
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

            var table = $('#tabla_metodoPago').DataTable({
                responsive: true, // Habilitar la opción responsive
                autoWidth: false,
                searchDelay: 2000,
                processing: true,
                serverSide: true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "Nada encontrado - disculpa",
                    "info": "Mostrando la página _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },

                order: [
                    [0, "asc"]
                ],
                ajax: "{{ tenant_url('tenant.ventas.metodopago.index') }}",
                columns: [{
                        data: 'MEP_Id',
                        name: 'MEP_Id'
                    },
                    {
                        data: 'MEP_Pago',
                        name: 'MEP_Pago'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return data.action1 + ' '  +data.action2
                        }
                    }
                ]
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                nombre = $("#MEP_Pago").val();

                if (nombre == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                $.ajax({
                    data: $('#metodoPago_form').serialize(),
                    url: "{{ tenant_url('tenant.ventas.metodopago.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        cancelarUpdate();
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Metodo de Pago fallo al Registrarse.'
                        })
                    }
                });
            });

            $('body').on('click', '.editMetodoPago', function() {
                var MetodoPago_id_edit = $(this).data('identificador');
                $.get('{{ tenant_url('tenant.ventas.metodopago.edit',['metodopago' => ':metodopago']) }}'.replace(':metodopago', MetodoPago_id_edit),
                    function(result) {
                        console.log(result);
                        $('#metodoPago_id_edit').val(result.data.MEP_Id);
                        $('#MEP_Pago').val(result.data.MEP_Pago);


                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $("#saveBtn").hide();
                        $("#updateBtn").show();
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                MetodoPago_id_update = $('#metodoPago_id_edit').val();
                $.ajax({
                    data: $('#metodoPago_form').serialize(),
                    url: '{{ tenant_url('tenant.ventas.metodopago.update', ['metodopago' => ':metodopago']) }}'.replace(
                        ':metodopago', MetodoPago_id_update),
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        });
                        cancelarUpdate();
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Metodo de Pago fallo al actualizarse.'
                        })
                    }
                });
            });

            $('#btncancelar').click(function(e) {
                cancelarUpdate();
                Swal.fire({
                    icon: 'info',
                    title: 'Registro cancelado',
                    text: 'El formulario se ha reiniciado correctamente.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            });

            function cancelarUpdate() {
                $('#metodoPago_form').trigger("reset");
                $("#metodoPago_id_edit").val('');
                $("#saveBtn").show(); // Mostrar botón Guardar
                $("#updateBtn").hide();
            }

            $('body').on('click', '.deleteMetodoPago', function() {

                var MetodoPago_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ tenant_url('tenant.ventas.metodopago.destroy',  ['metodopago' => ':metodopago']) }}'.replace(
                            ':metodopago', MetodoPago_id_delete),
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                            console.log('success:', data);
                            Toast.fire({
                                type: 'success',
                                title: String(data.success),
                                icon: 'info'
                            });

                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Metodo de Pago fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                }else{
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'El Metodo de Pago no ha sido eliminada.',
                        icon: 'info'
                    });
                 }
            });
        });
    </script>
@endsection