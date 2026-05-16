@extends('tenant_generico.layout.appAdminLte')
@section('titulo', 'Categorias')
@section('contenido')

    @can('tenant.inventario.categoria.create')
    <div class="col-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">CREAR CATEGORIAS</h5>
                <p class="card-text"></p>
                <form method="POST" id="cat_form" action="{{ route('tenant.inventario.categoria.store', tenant('id')) }}">
                    @csrf
                    <input type="hidden" id="_method" name="_method" value="" style="display: none;">
                    <input type="text" id="categoria_id_edit" hidden>
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Clase:</label>
                            <select class="form-control select2 select2-info" id="CLA_Id" name="CLA_Id"
                                data-dropdown-css-class="select2-info" style="width: 100%;">
                                <option value="">Seleccionar Clase</option>
                                @foreach ($clases as $itemClase)
                                    <option value="{{ $itemClase->CLA_Id }}"> {{ $itemClase->CLA_Nombre }}
                                    </option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label"  style=" text-align: left; display: block;">Nombre:</label>
                        <input type="text" id="CAT_Nombre" name="CAT_Nombre" class="form-control "
                            placeholder="Nombre" required>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: left;">
                        <label>Añadir Imagen </label>
                        <div class="custom-file center">
                            <input type="file" class="custom-file-input"
                                accept="image/*"
                                name="file" id="fileImagen">
                            <label class="custom-file-label" id="idFileImagen">Añadir Imagen</label>
                        </div>
                    </div>
                    <p></p>
                    
                    <div class="form-group text-right">
                        <button id="saveCategoria" class="btn btn-primary"><i class="fas fa-save"></i>Guardar</button>
                        <button id="updateBtn" class="btn btn-info" style="display: none;"><i
                                class="fas fa-save"></i>Actualizar</button>
                        <button type="reset" id="btncancelar" class="btn btn-danger"> <i
                                class="fas fa-ban"></i>Cancelar </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    @endcan
    
    @can('tenant.inventario.categoria.index')
    <div class="col-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">LISTA DE CATEGORIAS</h5>
                <p class="card-text">

                <div class="table-responsive" style="background:#FFF;" >
                    <table class="table" id="lista_categorias">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Clase</th>
                                <th scope="col">Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
    @endcan

    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Categoria</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p class="col">Id Categoria: </p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_CAT_Id" class="col"></p>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Clase:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_CLA_Nombre"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Nombre Categoria:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_CAT_Nombre"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Imagen: </p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <img id="ver_Imagen"  style="width: 120px; height: 120px; margin-right: 10px;" />
                        </div>
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

            $('.select2').select2()

            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            $("#fileImagen").change(function() {
                $nombre = document.getElementById('fileImagen').files[0].name;
                document.querySelector('#idFileImagen').innerText = $nombre;
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            var table = $('#lista_categorias').DataTable({
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
                ajax: "{{ route('tenant.inventario.categoria.index', tenant('id')) }}",
                columns: [{
                        data: 'CAT_Id',
                        name: 'CAT_Id',
                        className: 'text-start'
                    },
                    {
                        data: 'CAT_Nombre',
                        name: 'CAT_Nombre',
                        className: 'text-start'
                    },
                    {
                        data: 'CLA_Nombre',
                        name: 'CLA_Nombre',
                        className: 'text-start'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return @can('tenant.inventario.categoria.show')
                                    data.action3 + ' ' +
                                @endcan
                            '' 
                            @can('tenant.inventario.categoria.edit')
                                   + data.action1 + ' ' +
                                @endcan
                            ''
                            @can('tenant.inventario.categoria.destroy')
                                +data.action2
                            @endcan ;
                        }
                    }
                ],
            });

            $('#saveCategoria').click(function(e) {
                e.preventDefault();
                nombreCategoria = $("#CAT_Nombre").val();
                nombreClase = $("#CLA_Nombre").val();

                if (nombreCategoria == '' || nombreClase == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                let formData = new FormData($('#cat_form')[0]);

                $.ajax({
                    url: "{{ route('tenant.inventario.categoria.store', tenant('id')) }}",
                    type: "POST",
                    data: formData,
                    processData: false, // Evitar que jQuery procese el FormData
                    contentType: false, // Evitar que jQuery establezca el tipo de contenido
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        cancelarUpdate()
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Categoria fallo al Registrarse.'
                        })
                    }
                });
            });

            $('body').on('click', '.editCategoria', function() {
                var Categoria_id_edit = $(this).data('id');
                $.get('{{ route('tenant.inventario.categoria.edit', ['categoria' => ':categoria', 'tenant' => tenant('id')]) }}'.replace(':categoria', Categoria_id_edit),
                    function(result) {
                        console.log(result);
                        $('#categoria_id_edit').val(result.data.CAT_Id);
                        $('#CAT_Nombre').val(result.data.CAT_Nombre);
                        $('#CLA_Id').val(result.data.CLA_Id);
                        $('#CLA_Id').change();
                        

                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $('#_method').val('PUT').show();
                        $("#saveCategoria").hide();
                        $("#updateBtn").show();
                    })
            });

            $('body').on('click', '.eyeCategoria', function() {
                var Categoria_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ route('tenant.inventario.categoria.show', ['tenant' => tenant('id'), 'categoria' => ':categoria']) }}'.replace(':categoria',Categoria_id_ver),
                    function(data) {
                        $('#ver_CAT_Id').text(data.data.CAT_Id);
                        $('#ver_CLA_Nombre').text(data.data.CLA_Nombre);
                        $('#ver_CAT_Nombre').text(data.data.CAT_Nombre);
                        $('#ver_Imagen').attr('src', data.imagen);
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Categoria_id_update = $('#categoria_id_edit').val();
                let formData = new FormData($('#cat_form')[0]);
                $.ajax({
                    url: '{{ route('tenant.inventario.categoria.update', ['tenant' => tenant('id'), 'categoria' => ':categoria']) }}'.replace(':categoria',
                        Categoria_id_update),
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
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
                            title: 'Categoria fallo al actualizarse.'
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
                $('#cat_form').trigger("reset");
                $('#CLA_Id').val('');
                $('#CLA_Id').change();                
                $('#fileImagen').val("")
                document.querySelector('#idFileImagen').innerText = "Añadir Imagen";
                $("#categoria_id_edit").val('');
                $('#_method').val('').hide();
                $("#saveCategoria").show(); // Mostrar botón Guardar
                $("#updateBtn").hide();
            }

            $('body').on('click', '.deleteCategoria', function() {

                var Categoria_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ route('tenant.inventario.categoria.destroy', ['tenant' => tenant('id'), 'categoria' => ':categoria']) }}'.replace(
                            ':categoria', Categoria_id_delete),
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
                                title: 'Categoria fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'La categoria no ha sido eliminada.',
                        icon: 'info'
                    });
                }
            });
        })
    </script>
@endsection
