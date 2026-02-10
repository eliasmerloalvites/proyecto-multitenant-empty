@extends('central.layout.appAdminLte')

@section('titulo','Usuario')


@section('contenido')

<div class="container ">
    {{-- Mensaje de alerta --}}
    <div id="mensaje">
        @if (session('datos'))
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                {{session('datos')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i ></i></span>
                </button>
            </div>
        @endif
    </div>
    <div class="row ">
        <div class="col-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">CREAR USUARIO</h5>
              <p class="card-text"></p>
                <form id="UsuarioForm" name="UsuarioForm"  >
                @csrf
        
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label">Nombre:</label>
                            <input type="text" name="name" id="name" class="form-control input_user @error('name') is-invalid @enderror"  placeholder="Nombre" required>
                            @error('name') 
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="control-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control input_user @error('email') is-invalid @enderror"  placeholder="email" required>
                            @error('email') 
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="control-label">Contraseña:</label>
                            <input type="password" name="password" id="password" class="form-control input_pass @error('password') is-invalid @enderror"  placeholder="contraseña" required>
                            @error('password') 
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="control-label">Confirmar contraseña:</label>
                            <input type="password" name="confipassword" id="confipassword" class="form-control input_pass @error('confipassword') is-invalid @enderror" placeholder="contraseña" required>
                            @error('confipassword') 
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    @can('seguridad.users.create')
                    <button  id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i>Guardar</button>
                    @endcan

                    <button type="reset" class="btn btn-danger"> <i class="fas fa-ban"></i>Cancelar </button>
                    
                </form>
            </div>
          </div>
        </div>
        <div class="col-7">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">LISTA DE USUARIOS</h5>
              <p class="card-text">
                
                <table class="table table-striped nowrap" id="table-users" name="table-users">
                <thead style="background-color:#FF5F67;color: #fff;">
                    <tr>
                      <th scope="col">N°</th>
                      <th scope="col">Nombre</th>
                      <th scope="col">Opciones</th>
                    </tr>
                  </thead>
                <tbody>

                </tbody>
                
            </table>
            </div>
          </div>
        </div>
        
      </div>
    
    <!-- Modal Ver detalles-->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p class="col">id de usuario: </p>
                    <p id="ver_id" class="col"></p>
                </div>
                <div class="row">
                    <p class="col">Nombre de usuario:</p>
                    <p id="ver_nombre" class="col"></p>
                </div>
                <div class="row">
                    <p class="col">Email de usuario:</p>
                    <p id="ver_email" class="col"></p>
                </div>
                <div class="row">
                    <p class="col">Fecha de registro de usuario: </p>
                    <p id="ver_fecha_registro" class="col"></p>
                </div>
                <div class="row">
                    <p class="col">Fecha de actualización de usuario: </p>
                    <p id="ver_fecha_update" class="col"></p>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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
        var table = $('#table-users').DataTable({
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
            ajax: "{{ route('usuario.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: null,
                    name: 'name',
                    'render': function(data, type, row) {
                        return @can('seguridad.users.show') data.action3 +' '+ @endcan ''
                            @can('seguridad.users.edit') + data.action1 +' '+ @endcan ''
                            @can('seguridad.users.destroy') +data.action2 @endcan;
                    }
                }
            ]
        });


        $('#saveBtn').click(function(e) {
            e.preventDefault();
            name = $("#name").val();
            email = $("#email").val();
            contra = $("#password").val();
            confirmacontra = $("#confipassword").val();
            if (name == '' || email == ''  || contra=='' || confirmacontra=='') {
                Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                return false;
            }
            $.ajax({
                data: $('#UsuarioForm').serialize(),
                url: "{{ route('usuario.store') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    console.log('Success:', data);
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    })
                    $('#UsuarioForm').trigger("reset");
                    table.draw();
                },
                error: function(data) {
                    console.log('Error:', data);
                    Toast.fire({
                        type: 'error',
                        title: 'Usuario fallo al Registrarse.'
                    })
                }
            });
        });
        $('body').on('click', '.editUsuario', function() {
            var Usuario_id = $(this).data('id');
            window.location.href = "/usuario/"+Usuario_id+"/edit";
        });

        
        $('body').on('click', '.deleteUsuario', function() {

            var Usuario_id_delete = $(this).data("id");
            $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
            if ($confirm == true) {
                $.ajax({
                    type: "DELETE",
                    
                    url: '{{ route('usuario.destroy', ['usuario' => ':usuario']) }}'.replace(':usuario', Usuario_id_delete),
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
                            title: 'Usuario fallo al Eliminarlo.'
                        })
                    }
                });
            }
        });

        $('body').on('click', '.eyeUsuario', function() {
            var Usuario_id_ver = $(this).data('id');
            $('#modalVerDetalle').modal('show');
            $.get('{{ route('usuario.show', ['usuario' => ':usuario']) }}'.replace(':usuario', Usuario_id_ver),
                function(data) {
                    console.log(data);
                    $('#ver_id').text(data.data.id);
                    $('#ver_nombre').text(data.data.name);
                    $('#ver_email').text(data.data.email);
                    $('#ver_fecha_registro').text(moment(data.data.created_at).format('YYYY-MM-DD HH:mm:ss'));
                    $('#ver_fecha_update').text(moment(data.data.updated_at).format('YYYY-MM-DD HH:mm:ss'));

                })
           
        });
        
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@endsection
