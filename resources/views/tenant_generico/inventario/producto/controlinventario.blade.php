@extends('tenant_'.tenant('tipo_negocio').'.layout.appAdminLte')

@section('titulo', 'Productos')

@section('contenido')
<style>
        .modal-content{
            border-radius: 18px;
        }

        .card{
            border-radius: 18px;
        }

        .modal-header{
            padding: 18px 24px;
        }

        .modal-body{
            padding: 24px;
        }

        .product-image-container{
            background: white;
            border-radius: 18px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }

        .product-image{
            width: 100%;
            max-width: 240px;
            height: 240px;
            object-fit: cover;
            background: #f8f9fa;
        }

        .price-card{
            transition: .2s ease;
        }

        .price-card:hover{
            transform: translateY(-2px);
        }

        small.text-muted{
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .badge{
            border-radius: 10px;
            font-size: 12px;
        }
    table.dataTable{
        width:100% !important;
    }
    .kardex-table-container{
        max-height: 500px;
        overflow-y: auto;
        overflow-x: auto;
    }
    #tablaKardex{
        min-width: 1200px;
        border-collapse: separate;
        border-spacing: 0;
    }
    #tablaKardex thead th{
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        white-space: nowrap;
        box-shadow: 0 2px 2px rgba(0,0,0,.05);
    }
    #tablaKardex thead th:first-child{
        z-index: 11;
    }

    #tablaKardex td{
        vertical-align:middle;
    }
    /* PRIMERA COLUMNA */
    #tablaKardex th:first-child,
    #tablaKardex td:first-child{
        width: 170px;
        min-width: 170px;
        max-width: 170px;
        position: sticky;
        left: 0;
        background: white;
        z-index: 5;
    }
    /* ESQUINA SUPERIOR IZQUIERDA */
    #tablaKardex thead th:first-child{
        z-index: 20;
    }

    /* CELDAS */
    #tablaKardex td,
    #tablaKardex th{
        white-space: nowrap;
    }

    .badge-kardex{
        padding:6px 10px;
        border-radius:20px;
        font-size:12px;
    }

    .kardex-entrada{
        background:#eafaf1;
    }

    .kardex-salida{
        background:#fdeeee;
    }

    .modal-xl{
        max-width: 95%;
    }

    @media(max-width:768px){
        .kardex-table-container{
            height: 50vh;
        }

        #tablaKardex{
            min-width: 900px;
        }
    }

</style>

    <div class="col-12">
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
                                <th scope="col">Stock</th>
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
    

    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- HEADER -->
                <div class="modal-header bg-dark text-white border-0">

                    <div>
                        <h5 class="modal-title mb-0">
                            <i class="fas fa-box-open me-2"></i>
                            Detalles del Producto
                        </h5>

                        <small class="opacity-75">
                            Información completa del producto
                        </small>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close">
                    </button>

                </div>

                <!-- BODY -->
                <div class="modal-body bg-light">

                    <div class="card border-0 shadow-sm rounded-4">

                        <div class="card-body">

                            <div class="row g-4">

                                <!-- IMAGEN -->
                                <div class="col-lg-4 text-center">

                                    <div class="product-image-container">

                                        <img id="ver_Imagen" class="img-fluid rounded-4 border shadow-sm product-image">

                                    </div>

                                    <div class="mt-3">

                                        <span class="badge bg-success px-3 py-2">
                                            Producto Activo
                                        </span>

                                    </div>

                                </div>

                                <!-- INFORMACION -->
                                <div class="col-lg-8">

                                    <!-- ID -->
                                    <div class="mb-4">

                                        <small class="text-muted d-block">
                                            ID PRODUCTO
                                        </small>

                                        <h4 class="fw-bold text-primary mb-0" id="ver_PRO_Id">
                                        </h4>

                                    </div>

                                    <!-- CATEGORIA -->
                                    <div class="mb-3">

                                        <small class="text-muted d-block">
                                            Categoría
                                        </small>

                                        <div class="fw-semibold fs-6" id="ver_CAT_Nombre">
                                        </div>

                                    </div>

                                    <!-- NOMBRE -->
                                    <div class="mb-3">

                                        <small class="text-muted d-block">
                                            Nombre Producto
                                        </small>

                                        <div class="fw-bold fs-4 text-dark" id="ver_PRO_Nombre">
                                        </div>

                                    </div>

                                    <!-- DESCRIPCION -->
                                    <div class="mb-4">

                                        <small class="text-muted d-block">
                                            Descripción
                                        </small>

                                        <div class="text-secondary" id="ver_PRO_Descripcion">
                                        </div>

                                    </div>

                                    <!-- MARCA -->
                                    <div class="mb-4">

                                        <small class="text-muted d-block">
                                            Marca
                                        </small>

                                        <div class="fw-semibold" id="ver_PRO_Marca">
                                        </div>

                                    </div>

                                    <!-- PRECIOS -->
                                    <div class="row">

                                        <!-- PRECIO COMPRA -->
                                        <div class="col-md-6">

                                            <div class="price-card bg-white border rounded-4 p-3 shadow-sm">

                                                <small class="text-muted d-block">
                                                    Precio Compra
                                                </small>

                                                <h4 class="mb-0 text-danger fw-bold">
                                                    S/
                                                    <span id="ver_PRO_PrecioCompra"></span>
                                                </h4>

                                            </div>

                                        </div>

                                        <!-- PRECIO VENTA -->
                                        <div class="col-md-6">

                                            <div class="price-card bg-white border rounded-4 p-3 shadow-sm">

                                                <small class="text-muted d-block">
                                                    Precio Venta
                                                </small>

                                                <h4 class="mb-0 text-success fw-bold">
                                                    S/
                                                    <span id="ver_PRO_PrecioVenta"></span>
                                                </h4>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white border-0">

                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal">

                        <i class="fas fa-times me-1"></i>
                        Cerrar

                    </button>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerLotes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detalles del Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h5 class="mb-1 font-weight-bold" id="lote_producto"></h5>
                            <small class="text-muted">Gestión de lotes del producto</small>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="card shadow-sm border-0 bg-light d-inline-block px-3 py-2">
                                <small class="text-muted d-block">
                                    Stock Total
                                </small>
                                <strong class="h5 mb-0" id="lote_stock_total" >
                                    203.00
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tablaLotes" class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Lote</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Stock Ingreso</th>
                                    <th>Stock Actual</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody id="tbody_lotes"></tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KARDEX -->
    <div class="modal fade" id="modalKardex" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow">
                <!-- HEADER -->
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">Kardex del Producto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- BODY -->
                <div class="modal-body">
                    <!-- RESUMEN -->
                    <div class="row mb-4 align-items-center">
                        <!-- PRODUCTO -->
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1" id="kardex_producto"></h3>
                            <p class="text-muted mb-0">Historial de movimientos del producto</p>
                        </div>

                        <!-- STOCK -->
                        <div class="col-md-6 text-right">
                            <div class="d-inline-block bg-light shadow-sm rounded px-4 py-3">
                                <small class="text-muted d-block">Stock Actual</small>
                                <h3 class="font-weight-bold mb-0" id="kardex_stock_total"></h3>
                            </div>
                        </div>
                    </div>

                    <!-- FILTROS -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <div class="row">
                                <!-- FECHA INICIO -->
                                <div class="col-md-3">
                                    <label>Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio_kardex">
                                </div>
                                <!-- FECHA FIN -->
                                <div class="col-md-3">
                                    <label>Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin_kardex">
                                </div>

                                <!-- TIPO -->
                                <div class="col-md-3">
                                    <label>Tipo Movimiento</label>
                                    <select class="form-control" id="tipo_movimiento_kardex">
                                        <option value="">Todos</option>
                                        <option value="COMPRA">Compra</option>
                                        <option value="VENTA">Venta</option>
                                        <option value="AJUSTE">Ajuste</option>
                                    </select>
                                </div>

                                <!-- BOTON -->
                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary btn-block" id="btnFiltrarKardex">
                                        <i class="fas fa-search mr-1"></i>
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="kardex-table-container">
                                <table id="tablaKardex" class="table table-sm table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Documento</th>
                                            <th>Lote</th>
                                            <th class="text-center">Stock Inicial</th>
                                            <th class="text-center">Entrada</th>
                                            <th class="text-center">Salida</th>
                                            <th class="text-center">Stock Final</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_kardex">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">

                        Cerrar

                    </button>

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
                ajax: "{{ tenant_url('tenant.inventario.controlinventario.index') }}",
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
                        data: 'cantidad_total',
                        name: 'cantidad_total',
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
                                + data.lotes + ' ' +
                            @endcan
                            ''
                            @can('tenant.inventario.producto.destroy')
                                +data.kardex
                            @endcan ;
                        }
                    }
                ],
            });

            $('body').on('click', '.eyeProducto', function() {
                var Producto_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.inventario.producto.show', ['producto' => ':producto']) }}'.replace(':producto', Producto_id_ver),
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

            $('body').on('click', '.lotesProducto', function() {
                var Producto_id_ver = $(this).data('id');
                $('#modalVerLotes').modal('show');
                $.get('{{ tenant_url('tenant.inventario.controlinventario.lotes', ['producto' => ':producto']) }}'.replace(':producto', Producto_id_ver),
                    function(data) {
                        console.log(data)
                        $('#lote_producto').text(data.producto.PRO_Nombre);
                        $('#lote_stock_total').text(data.producto.cantidad_total);
                        // LIMPIAR TABLA
                        $('#tbody_lotes').html('');

                        // RECORRER LOTES
                        data.lotes.forEach(function (lote) {

                            let estado = '';
                            let badge = '';

                            // EJEMPLO SIMPLE ESTADO
                            if (lote.LOT_CantidadReal <= 0) {
                                badge = '<span class="badge badge-danger">Agotado</span>';
                            } else {
                                badge = '<span class="badge badge-success">Disponible</span>';
                            }

                            let fila = `
                                <tr>
                                    <td>${lote.LOT_Id}</td>
                                    <td>${lote.created_at}</td>
                                    <td>${lote.LOT_CantidadIngreso}</td>
                                    <td>${lote.LOT_CantidadReal}</td>
                                    <td>${badge}</td>
                                </tr>
                            `;

                            $('#tbody_lotes').append(fila);

                            

                        });

                        if ($.fn.DataTable.isDataTable('#tablaLotes')) {
                            $('#tablaLotes').DataTable().destroy();
                        }

                        $('#tablaLotes').DataTable({
                            responsive: true,
                            autoWidth: false,
                            destroy: true,
                            pageLength: 5,
                            lengthChange: false,
                            order: [[1, 'desc']],
                            scrollX: false,
                            responsive: true,
                            autoWidth: false
                        });

                    })
            });

            

            $('body').on('click', '.kardexProducto', function () {
                var Producto_id_ver = $(this).data('id');
                $('#modalKardex').modal('show');
                $('#btnFiltrarKardex').data('producto', Producto_id_ver);
                cargarKardex(Producto_id_ver);
            });

            $('#btnFiltrarKardex').click(function () {
                let producto = $(this).data('producto');
                let fecha_inicio = $('#fecha_inicio_kardex').val();
                let fecha_fin = $('#fecha_fin_kardex').val();
                let tipo = $('#tipo_movimiento_kardex').val();
                cargarKardex(producto, fecha_inicio, fecha_fin, tipo);
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

                        url: '{{ tenant_url('tenant.inventario.producto.destroy', ['producto' => ':producto']) }}'.replace(
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

        function cargarKardex(producto, fecha_inicio = '', fecha_fin = '', tipo = '') {

            $.get(
                '{{ tenant_url('tenant.inventario.controlinventario.kardex', ['producto' => ':producto']) }}'
                .replace(':producto', producto),

                {
                    fecha_inicio: fecha_inicio,
                    fecha_fin: fecha_fin,
                    tipo: tipo
                },

                function (data) {

                    $('#tbody_kardex').html('');

                    $('#kardex_producto').text(data.producto.PRO_Nombre);

                    $('#kardex_stock_total').text(data.producto.cantidad_total);

                    data.kardex.forEach(function (kardex) {

                        let badgeTipo = '';

                        if(kardex.tipo == 'Entrada'){

                            badgeTipo = `
                                <span class="badge badge-success badge-kardex">
                                    Compra
                                </span>
                            `;

                        }else{

                            badgeTipo = `
                                <span class="badge badge-danger badge-kardex">
                                    Venta
                                </span>
                            `;
                        }

                        let fila = `
                            <tr>
                                <td>${kardex.fecha}</td>
                                <td>${badgeTipo}</td>
                                <td>${kardex.documento}</td>
                                <td>LT_${kardex.lote_id}</td>
                                <td class="text-center">${kardex.stock_inicial}</td>
                                <td class="text-center">${kardex.entrada}</td>
                                <td class="text-center">${kardex.salida}</td>
                                <td class="text-center">${kardex.stock_final}</td>
                            </tr>
                        `;

                        $('#tbody_kardex').append(fila);

                    });

                }
            );

        }

    </script>
@endsection
