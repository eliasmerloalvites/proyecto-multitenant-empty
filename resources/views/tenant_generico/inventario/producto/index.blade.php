@extends('tenant_generico.layout.appAdminLte')

@section('titulo', 'Productos')

@section('contenido')

    @can('tenant.inventario.producto.create')
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">CREAR PRODUCTO</h5>
                <p class="card-text"></p>
                <form method="POST" id="product_form" action="{{route('tenant.inventario.producto.store',tenant('id'))}}" enctype="multipart/form-data">
                    @csrf
                    <input type="text" id="producto_id_edit" hidden>
                    <input type="hidden" id="_method" name="_method" value="" style="display: none;">
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label" style=" text-align: left; display: block;">Categoria:</label>
                            <select class="form-control select2 select2-info" id="CAT_Id" name="CAT_Id"
                                data-dropdown-css-class="select2-info"  style="width: 100%; ">
                                <option value="">Seleccionar ...</option>
                                @foreach ($categorias as $itemCategoria)
                                    <option value="{{ $itemCategoria->CAT_Id }}">{{ $itemCategoria->CAT_Nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Nombre:</label>
                            <input type="text" id="PRO_Nombre" name="PRO_Nombre"
                                class="form-control input_user "
                                placeholder="Nombre" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Descripción:</label>
                            <input type="text" id="PRO_Descripcion" name="PRO_Descripcion"
                                class="form-control input_user "
                                placeholder="Descripción" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Precio de Compra:</label>
                            <input type="number" id="PRO_PrecioCompra" name="PRO_PrecioCompra"
                                class="form-control input_user "
                                placeholder="Precio de Compra" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Precio de Venta:</label>
                            <input type="number" id="PRO_PrecioVenta" name="PRO_PrecioVenta"
                                class="form-control input_user "
                                placeholder="Precio de Venta" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="control-label"  style=" text-align: left; display: block;">Marca:</label>
                            <input type="text" id="PRO_Marca" name="PRO_Marca"
                                class="form-control input_user "
                                placeholder="Marca" required>
                        </div>
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
                        <button id="productosave" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
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
    @can('tenant.inventario.producto.index')
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">LISTA DE PRODUCTOS</h5>
                <p class="card-text">
                <div class="table-responsive" style="background:#FFF;" >
                    <table class="table" id="lista_productos">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Categoria</th>
                                <th scope="col">P. Venta</th>
                                <th scope="col">P. Compra</th>
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
                    <h5 class="modal-title" id="modalLabel">Detalles del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p >Id Producto: </p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_PRO_Id"></p>
                        </div>                        
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Categoria:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_CAT_Nombre"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Nombre Producto:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_PRO_Nombre"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Descripcion:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_PRO_Descripcion"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Marca:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_PRO_Marca"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Precio Compra:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_PRO_PrecioCompra"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="text-align: left;" >
                            <p>Precio Venta:</p>
                        </div>
                        <div class="col-8" style="text-align: left;" >
                            <p id="ver_PRO_PrecioVenta"></p>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            
            $('.select2').select2();

            $('.select2bs4').select2({
              theme: 'bootstrap4'
            })

            $("#fileImagen").change(function() {
                $nombre = document.getElementById('fileImagen').files[0].name;
                document.querySelector('#idFileImagen').innerText = $nombre;
            });

            var table = $('#lista_productos').DataTable({
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
                ajax: "{{ route('tenant.inventario.producto.index', tenant('id')) }}",
                columns: [{
                        data: 'PRO_Id',
                        name: 'PRO_Id',
                        className: 'text-start'
                    },
                    {
                        data: 'PRO_Nombre',
                        name: 'PRO_Nombre',
                        className: 'text-start'
                    },
                    {
                        data: 'CAT_Nombre',
                        name: 'CAT_Nombre',
                        className: 'text-start'
                    },
                    {
                        data: 'PRO_PrecioVenta',
                        name: 'PRO_PrecioVenta',
                        className: 'text-start'
                    }, 
                    {
                        data: 'PRO_PrecioCompra',
                        name: 'PRO_PrecioCompra',
                        className: 'text-start'
                    },
                    {
                        data: null,
                        name: '',
                        'render': function(data, type, row) {
                            return @can('tenant.inventario.producto.show')
                                    data.action3 + ' ' +
                                @endcan
                            '' 
                            @can('tenant.inventario.producto.edit')
                                + data.action1 + ' ' +
                            @endcan
                            ''
                            @can('tenant.inventario.producto.destroy')
                                +data.action2
                            @endcan ;
                        }
                    }
                ],
            });

            $('#productosave').click(function(e) {
                e.preventDefault();
                nombre = $("#PRO_Nombre").val();
                descripcion = $("#PRO_Descripcion").val();
                precioCompra = $("#PRO_PrecioCompra").val();
                precioVenta = $("#PRO_PrecioVenta").val();
                marca = $("#PRO_Marca").val();
                catId = $("#CAT_Id").val();

                if (nombre == ''||descripcion == ''||precioCompra == ''||precioVenta == ''||marca == ''||catId == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                let formData = new FormData($('#product_form')[0]);
                $.ajax({
                    url: "{{ route('tenant.inventario.producto.store', tenant('id')) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
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
                            title: 'producto fallo al Registrarse.'
                        })
                    }
                });
            });

            $('body').on('click', '.editProducto', function() {
                var Producto_id_edit = $(this).data('id');
                $.get('{{ route('tenant.inventario.producto.edit', ['producto' => ':producto', 'tenant' => tenant('id')]) }}'.replace(':producto', Producto_id_edit),
                    function(result) {
                        console.log(result);
                        $('#producto_id_edit').val(result.data.PRO_Id);
                        $('#PRO_Nombre').val(result.data.PRO_Nombre);
                        $('#PRO_Descripcion').val(result.data.PRO_Descripcion);
                        $('#PRO_PrecioCompra').val(result.data.PRO_PrecioCompra);
                        $('#PRO_PrecioVenta').val(result.data.PRO_PrecioVenta);
                        $('#PRO_Marca').val(result.data.PRO_Marca);
                        $('#CAT_Id').val(result.data.CAT_Id);
                        $('#CAT_Id').change();


                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $('#_method').val('PUT').show();
                        $("#productosave").hide();
                        $("#updateBtn").show();
                    })
            });

            $('body').on('click', '.eyeProducto', function() {
                var Producto_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ route('tenant.inventario.producto.show', ['producto' => ':producto', 'tenant' => tenant('id')]) }}'.replace(':producto', Producto_id_ver),
                    function(data) {
                        console.log(data)
                        $('#ver_PRO_Id').text(data.data.PRO_Id);
                        $('#ver_CAT_Nombre').text(data.data.CAT_Nombre);
                        $('#ver_PRO_Descripcion').text(data.data.PRO_Descripcion);
                        $('#ver_PRO_Marca').text(data.data.PRO_Marca);
                        $('#ver_PRO_PrecioCompra').text(data.data.PRO_PrecioCompra);
                        $('#ver_PRO_PrecioVenta').text(data.data.PRO_PrecioVenta);
                        $('#ver_Imagen').attr('src', data.imagen);
                    })
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Producto_id_update = $('#producto_id_edit').val();
                let formData = new FormData($('#product_form')[0]);
                $.ajax({
                    data: $('#product_form').serialize(),
                    url: '{{ route('tenant.inventario.producto.update', ['producto' => ':producto', 'tenant' => tenant('id')]) }}'.replace(
                        ':producto', Producto_id_update),
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
                            title: 'Producto fallo al actualizarse.'
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
                $('#product_form').trigger("reset");
                $("#CAT_Id").val('');
                $('#CAT_Id').change();
                $('#fileImagen').val("")
                document.querySelector('#idFileImagen').innerText = "Añadir Imagen";
                $('#_method').val('').hide();
                $("#producto_id_edit").val('');
                $("#productosave").show(); // Mostrar botón Guardar
                $("#updateBtn").hide();
            }

            $('body').on('click', '.deleteProducto', function() {

                var Producto_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ route('tenant.inventario.producto.destroy', ['producto' => ':producto', 'tenant' => tenant('id')]) }}'.replace(
                            ':producto', Producto_id_delete),
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
                                title: 'Producto fallo al Eliminarlo.',
                                icon: 'info'
                            })
                        }
                    });
                }else{
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'El producto no ha sido eliminado.',
                        icon: 'info'
                    });
                 }
            });
        })
    </script>
@endsection
