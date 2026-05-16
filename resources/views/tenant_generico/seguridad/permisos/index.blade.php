@extends('tenant_generico.layout.appAdminLte')

@section('titulo','Permisos')

@section('contenido')
        @can('tenant.seguridad.permiso.create')
        <div class="col-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR PERMISO</h5>
                    <p class="card-text"></p>
                    <form  id="PermisoForm" name="PermisoForm" action="{{--route('tenant.seguridad.permiso.store')--}}" >
                        @csrf
                
                            <div class="form-group row">
                                <div class="col-12">
                                    <input type="text" id="permiso_id_edit" hidden>
                                    <label class="control-label">Grupo:</label>
                                    <input type="text" id="group_name" name="group_name" class="form-control input_user @error('group_name') is-invalid @enderror"  placeholder="Grupo" required>
                                </div>
                                <div class="col-12">
                                    <input type="text" id="permiso_id_edit" hidden>
                                    <label class="control-label">Nombre:</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control input_user @error('nombre') is-invalid @enderror"  placeholder="Nombre" required>
                                </div>
                                <div class="col-12">
                                    <label class="control-label">Ruta:</label>
                                    <input type="text" id="name" name="name" class="form-control input_user @error('name') is-invalid @enderror"  placeholder="Ruta" required>
                                </div>
                                <div class="col-12">
                                    <label class="control-label">Descripción:</label>
                                    <input type="text" id="description" name="description" class="form-control input_user @error('description') is-invalid @enderror"  placeholder="description" >
                                </div>
                                
                            </div>
                        
                            <div class="form-group text-right">
                                <button id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                                <button id="updateBtn" class="btn btn-default" style="display: none;"><i
                                        class="fas fa-save"></i> Actualizar</button>
                                <button type="reset" id="btncancelar" class="btn btn-danger"> <i
                                    class="fas fa-ban"></i> Cancelar </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan
        @can('tenant.seguridad.permiso.index')
        <div class="col-7">
            <div class="card">
                <div class="card-body">
                  <h5 class="card-title">LISTA DE PERMISOS</h5>               
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="table-permisos">
                            <thead >
                            <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Grupo</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Link</th>
                            <th scope="col">Opciones</th>
                            </tr>
                            </thead>
                            {{-- <tbody>
                            @if(count($roles)<=0)
                                <tr>
                                    <td colspan="3"><b>No hay registro</b></td>
                                </tr>
                            @else
                                @foreach ($roles as $role)
                                <tr>
                                </tr>
                                @endforeach
                            @endif
                            </tbody> --}}
                        
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        

    <!-- Modal Ver detalles-->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p class="col text-start">id de permiso: </p>
                    <p id="ver_id" class="col text-start"></p>
                </div>
                <div class="row">
                    <p class="col text-start">Grupo:</p>
                    <p id="ver_group" class="col text-start"></p>
                </div> 
                <div class="row">
                    <p class="col text-start">Nombre de permiso:</p>
                    <p id="ver_nombre" class="col text-start"></p>
                </div>               
                <div class="row">
                    <p class="col text-start">Ruta de permiso:</p>
                    <p id="ver_ruta" class="col text-start"></p>
                </div>
                <div class="row">
                    <p class="col text-start">Descripción de permiso: </p>
                    <p id="ver_description" class="col text-start"></p>
                </div>
                <div class="row">
                    <p class="col text-start">Fecha de registro de permiso: </p>
                    <p id="ver_fecha_registro" class="col text-start"></p>
                </div>
                <div class="row">
                    <p class="col text-start">Fecha de actualización de permiso: </p>
                    <p id="ver_fecha_update" class="col text-start"></p>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>
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
            timer: 3000
        });
        var table = $('#table-permisos').DataTable({
            responsive: true, // Habilitar la opción responsive
            autoWidth: false,
            searchDelay : 2000,
            processing: true,
            serverSide: true,
            "language": {
            "lengthMenu": "Mostrar _MENU_ ",
            "zeroRecords": "Nada encontrado - disculpa",
            "info": "Mostrando la página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate":{
                "next" : "Siguiente",
                "previous" : "Anterior"
            }
            },

            order: [
                [0, "asc"]
            ],
            ajax: "{{ route('tenant.seguridad.permiso.index', tenant('id')) }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'group_name',
                    name: 'group_name'
                },
                {
                    data: 'nombre',
                    name: 'nombre'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: null,
                    name: 'name',
                    'render': function(data, type, row) {
                        return @can('tenant.seguridad.permiso.show') data.action3 +' '+ @endcan ''
                            @can('tenant.seguridad.permiso.edit') + data.action1 +' '+ @endcan ''
                            @can('tenant.seguridad.permiso.destroy') +data.action2 @endcan;
                    }
                }
            ]
        });

        $('#saveBtn').click(function(e) {
            e.preventDefault();
            
            const form = document.getElementById('PermisoForm');

            if (form.checkValidity()) {
                $.ajax({
                    data: $('#PermisoForm').serialize(),
                    url: "{{ route('tenant.seguridad.permiso.store', tenant('id')) }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        $('#PermisoForm').trigger("reset");
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Permiso fallo al Registrarse.'
                        })
                    }
                });
            } else {
                form.reportValidity();
            }
        });

        $('body').on('click', '.deletePermiso', function() {

            var Permiso_id_delete = $(this).data("id");
            $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
            if ($confirm == true) {
                $.ajax({
                    type: "DELETE",
                    
                    url: '{{ route('tenant.seguridad.permiso.destroy', ['permiso' => ':permiso', tenant('id')]) }}'.replace(':permiso', Permiso_id_delete).replace(':tenant', '{{ tenant() }}'),
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
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
                            title: 'Permiso fallo al Eliminarlo.'
                        })
                    }
                });
            }
        });

        $('body').on('click', '.editPermiso', function() {
            var Permiso_id_edit = $(this).data('id');
            $.get('{{ route('tenant.seguridad.permiso.edit', ['permiso' => ':permiso', tenant('id')]) }}'.replace(':permiso', Permiso_id_edit),
                function(data) {
                    console.log(data);
                    $('#permiso_id_edit').val(data.data.id);
                    $('#group_name').val(data.data.group_name);
                    $('#nombre').val(data.data.nombre);
                    $('#name').val(data.data.name);
                    $('#description').val(data.data.description);

                    //desactivar campo name
                    $("#name").prop("disabled", true);
                    //desactivar boton guardar
                    $("#saveBtn").hide();
                    $("#updateBtn").show();
                })
        });

        $('#btncancelar').click(function(e) {
            cancelarUpdate();
            $("#name").prop("disabled", false);
        });


        function cancelarUpdate(){
            $("#saveBtn").show();
            $("#updateBtn").hide();

        }

        $('#updateBtn').click(function(e) {
            e.preventDefault();
            Permiso_id_update = $('#permiso_id_edit').val();
            $.ajax({
                data: $('#PermisoForm').serialize(),
                url: '{{ route('tenant.seguridad.permiso.update', ['permiso' => ':permiso', tenant('id')]) }}'.replace(':permiso', Permiso_id_update).replace(':tenant', '{{ tenant() }}'),
                type: "PUT",
                dataType: 'json',
                success: function(data) {
                    console.log('Success:', data);
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    });
                    cancelarUpdate();
                    $('#PermisoForm').trigger("reset");
                    table.draw();
                },
                error: function(data) {
                    console.log('Error:', data);
                    if(data.responseText){
                        Toast.fire({
                            type: 'error',
                            title: data.responseText
                        })
                    }else{
                        Toast.fire({
                            type: 'error',
                            title: 'Permiso fallo al Registrarse.'
                        })
                    }
                }
            });
        });

        $('body').on('click', '.eyePermiso', function() {
            var Permiso_id_ver = $(this).data('id');
            $('#modalVerDetalle').modal('show');
            $.get('{{ route('tenant.seguridad.permiso.show', ['permiso' => ':permiso', 'tenant' => ':tenant']) }}'.replace(':permiso', Permiso_id_ver).replace(':tenant', '{{ tenant() }}'),
                function(data) {
                    console.log(data);
                    $('#ver_id').text(data.data.id);
                    $('#ver_group').text(data.data.group_name);
                    $('#ver_nombre').text(data.data.nombre);
                    $('#ver_ruta').text(data.data.name);
                    $('#ver_description').text(data.data.description);
                    $('#ver_fecha_registro').text(moment(data.data.created_at).format('YYYY-MM-DD HH:mm:ss'));
                    $('#ver_fecha_update').text(moment(data.data.updated_at).format('YYYY-MM-DD HH:mm:ss'));

                })
           
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@endsection