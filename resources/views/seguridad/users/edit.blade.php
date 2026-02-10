@extends('central.layout.appAdminLte')

@section('titulo','Editar Usuario')

@section('contenido')

<div class="container ">
    <form action="{{route('usuario.update',$usuario->id)}}" method="post">
        @method('put')
        @csrf
        <div class="row row-cols-1 row-cols-md-2 g-4">
        
            <div class="col-5">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">EDITAR USUARIO</h5>
                    <p class="card-text"></p>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="control-label">Nombre:</label>
                                    <input type="text" name="name" class="form-control input_user @error('name') is-invalid @enderror" value="{{$usuario->name}}"  placeholder="Nombre" required>
                                    @error('name') 
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="control-label">Email:</label>
                                    <input type="email" name="email" class="form-control input_user "  placeholder="email" value="{{$usuario->email}}" readonly required>
                                    
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="col-7">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">LISTA DE ROLES</h5>
                    <p class="card-text"></p>
                        <div class="form-group">
                            <label for="roles">Roles:</label><br>
                            @if(count($roles)<=0)
                                <p>No hay registro</p>
                            @else
                                @foreach($roles as $role)
                                <div class="form-check">
                                    <center>
                                        <input class="form-check-input" type="checkbox" value="{{$role->id}}" id="role{{$role->id}}" name="roles[]"
                                        {{ $usuario->hasRole($role->name) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role{{$role->id}}">
                                            {{$role->name}} ({{$role->descripcion}})
                                        </label>
                                    </center>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
       <center> <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Guardar</button>
        <a href="{{route('usuario.cancelar')}}" class="btn btn-danger" ><i class="fas fa-ban"></i>Regresar</a></center>
    </form>
    
    
</div>
@endsection

@section('script')
<script>
    //para cerrar el mensaje
    setTimeout(function () {
        //selecciono el id mensaje y lo remuevo en 2000 segundos
        document.querySelector('#mensaje').remove();
        
    }, 2000);
</script>
@endsection