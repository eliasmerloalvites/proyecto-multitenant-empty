@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')

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

        small.text-muted{
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ===== Modal "Ver Detalle" — rediseño ===== */

        #modalVerDetalle .modal-header{
            background: linear-gradient(135deg, #1E293B, #334155);
        }

        #modalVerDetalle .modal-body{
            background: #F8FAFC;
        }

        .product-image-container{
            position: relative;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 14px;
            cursor: zoom-in;
            transition: box-shadow .2s ease;
        }

        .product-image-container:hover{
            box-shadow: 0 10px 28px rgba(15,23,42,.10);
        }

        .product-image{
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 14px;
            background: #F1F5F9;
            transition: filter .15s ease;
        }

        .product-image-container:hover .product-image{
            filter: brightness(0.9);
        }

        .product-image-container .zoom-hint{
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15,23,42,.75);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            opacity: 0;
            transition: opacity .15s ease;
            pointer-events: none;
        }

        .product-image-container:hover .zoom-hint{
            opacity: 1;
        }

        .galeria-thumb{
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 10px;
            cursor: zoom-in;
            transition: transform .15s ease, border-color .15s ease;
        }

        .galeria-thumb:hover{
            transform: scale(1.08);
            border-color: #6366F1 !important;
        }

        .pd-badge{
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .2px;
        }

        .pd-eyebrow{
            display: inline-block;
            background: #EEF2FF;
            color: #4338CA;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .pd-chip{
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 7px 14px;
            font-size: 12.5px;
            color: #64748B;
        }

        .pd-chip strong{
            color: #0F172A;
            font-weight: 700;
        }

        .pd-price-panel{
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            padding: 20px 10px;
        }

        .pd-price-panel .pd-label{
            color: #94A3B8;
            text-transform: uppercase;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .6px;
            display: block;
        }

        .pd-price-panel .pd-value{
            font-size: 24px;
            font-weight: 800;
            margin-top: 4px;
        }

        .pd-price-panel .pd-divider{
            width: 1px;
            background: #E2E8F0;
        }

        #lightboxImagenProducto{
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10,10,15,.92);
            z-index: 2000;
            text-align: center;
        }

        #lightboxImg{
            max-width: 90%;
            max-height: 84vh;
            margin-top: 5vh;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0,0,0,.6);
        }

        #lightboxImagenProducto .lightbox-btn{
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,.12);
            border: none;
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            transition: background .15s ease;
        }

        #lightboxImagenProducto .lightbox-btn:hover{
            background: rgba(255,255,255,.25);
        }

        #lightboxCerrar{
            position: absolute;
            top: 20px;
            right: 24px;
            background: none;
            border: none;
            color: #fff;
            font-size: 30px;
            cursor: pointer;
        }
    </style>
    @can('tenant.inventario.producto.create')
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CREAR PRODUCTO</h5>
                    <p class="card-text"></p>
                    <form method="POST" id="product_form" action="{{ tenant_url('tenant.inventario.producto.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="text" id="producto_id_edit" hidden>
                        <input type="hidden" id="_method" name="_method" value="" style="display: none;">
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Categoria:</label>
                                <select class="form-control select2 select2-info" id="CAT_Id" name="CAT_Id"
                                    data-dropdown-css-class="select2-info" style="width: 100%; ">
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
                                <label class="control-label" style=" text-align: left; display: block;">Nombre:</label>
                                <input type="text" id="PRO_Nombre" name="PRO_Nombre" class="form-control input_user "
                                    placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Descripción:</label>
                                <input type="text" id="PRO_Descripcion" name="PRO_Descripcion"
                                    class="form-control input_user " placeholder="Descripción" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Precio de
                                    Compra:</label>
                                <input type="number" id="PRO_PrecioCompra" name="PRO_PrecioCompra"
                                    class="form-control input_user " placeholder="Precio de Compra" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Precio de Venta:</label>
                                <input type="number" id="PRO_PrecioVenta" name="PRO_PrecioVenta"
                                    class="form-control input_user " placeholder="Precio de Venta" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Marca:</label>
                                <input type="text" id="PRO_Marca" name="PRO_Marca" class="form-control input_user "
                                    placeholder="Marca" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label class="control-label" style=" text-align: left; display: block;">Código Interno:</label>
                                <input type="text" id="PRO_CodigoInterno" name="PRO_CodigoInterno"
                                    class="form-control input_user " placeholder="Ej. INT-0001">
                            </div>
                            <div class="col-6">
                                <label class="control-label" style=" text-align: left; display: block;">Código de Fabricación:</label>
                                <input type="text" id="PRO_CodigoFabricacion" name="PRO_CodigoFabricacion"
                                    class="form-control input_user " placeholder="Ej. SKU del proveedor">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="control-label" style=" text-align: left; display: block;">Stock Mínimo:</label>
                                <input type="number" id="PRO_StockMinimo" name="PRO_StockMinimo" min="0" step="1"
                                    class="form-control input_user " placeholder="Stock Mínimo" value="0">
                                <small class="form-text text-muted">Se avisará cuando el stock llegue a este nivel o menos.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="PRO_MostrarCatalogo" name="PRO_MostrarCatalogo" value="1" checked>
                                    <label class="custom-control-label" for="PRO_MostrarCatalogo">Mostrar en el catálogo web</label>
                                </div>
                                <small class="form-text text-muted">Si lo desactivas, el producto sigue disponible para vender pero no aparece en el catálogo público de la página web.</small>
                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: left;">
                            <label>Añadir Imagen (principal)</label>
                            <div class="custom-file center">
                                <input type="file" class="custom-file-input" accept="image/*" name="file" id="fileImagen">
                                <label class="custom-file-label" id="idFileImagen">Añadir Imagen</label>
                            </div>
                        </div>
                        <p></p>

                        {{-- Galería adicional: solo se ve al editar un producto ya guardado
                             (necesita PRO_Id), igual que las fotos de mantenimiento no
                             existen todavía en su formulario de creación. --}}
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" id="galeriaProductoWrap" style="display:none; text-align:left;">
                            <label>Galería adicional (hasta 4 fotos más, 5 en total)</label>
                            <div id="galeriaProductoGrid" class="d-flex flex-wrap" style="gap:8px; margin-bottom:8px;"></div>
                            <div class="custom-file center">
                                <input type="file" class="custom-file-input" accept="image/*" id="fileGaleriaProducto">
                                <label class="custom-file-label" id="idFileGaleriaProducto">Agregar foto a la galería</label>
                            </div>
                        </div>
                        <p></p>

                        <div class="form-group text-right">
                            <button id="productosave" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                            <button id="updateBtn" class="btn btn-info" style="display: none;"><i class="fas fa-save"></i>
                                Actualizar</button>
                            <button type="reset" id="btncancelar" class="btn btn-danger"> <i class="fas fa-ban"></i> Cancelar
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endcan
    @can('tenant.inventario.producto.index')
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
                        <h5 class="card-title mb-0">LISTA DE PRODUCTOS</h5>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <select class="form-control form-control-sm" id="filtroEstadoProducto" style="width:auto;">
                                <option value="ACT" selected>Activos</option>
                                <option value="INA">Inactivos</option>
                                <option value="TODOS">Todos</option>
                            </select>
                            @can('tenant.inventario.producto.create')
                            <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalImportarProducto">
                                <i class="fa fa-file-excel mr-1"></i> Importar
                            </button>
                            @endcan
                        </div>
                    </div>
                    <p class="card-text">
                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table" id="lista_productos">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Cód. Interno</th>
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
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- HEADER -->
                <div class="modal-header bg-dark text-gray-600 border-0">

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
                <div class="modal-body p-4">

                    <div class="row g-4">

                        <!-- IMAGEN -->
                        <div class="col-lg-4">

                            <div class="product-image-container" id="ver_ImagenPrincipalWrap">

                                <img id="ver_Imagen" class="product-image">
                                <span class="zoom-hint"><i class="fas fa-search-plus mr-1"></i>Click para ampliar</span>

                            </div>

                            <div id="ver_Galeria" class="d-flex flex-wrap justify-content-center mt-2" style="gap:6px;"></div>

                            <div class="mt-3 d-flex flex-wrap justify-content-center" style="gap:6px;">

                                <span class="badge pd-badge bg-success" id="ver_BadgeEstado">
                                    Producto Activo
                                </span>

                                <span class="badge pd-badge" id="ver_BadgeCatalogo">
                                </span>

                            </div>

                        </div>

                        <!-- INFORMACION -->
                        <div class="col-lg-8">

                            <span class="pd-eyebrow" id="ver_CAT_Nombre"></span>

                            <h3 class="fw-bold text-dark mt-2 mb-1" id="ver_PRO_Nombre"></h3>

                            <div class="text-muted mb-3" style="font-size:13px;">
                                ID #<span id="ver_PRO_Id"></span>
                                <span id="ver_PRO_Marca_Wrap">· <span id="ver_PRO_Marca"></span></span>
                            </div>

                            <p class="text-secondary mb-3" id="ver_PRO_Descripcion" style="font-size:14.5px;"></p>

                            <div class="d-flex flex-wrap mb-4" style="gap:8px;" id="ver_CodigosWrap">
                                <span class="pd-chip"><i class="fas fa-barcode mr-1"></i>Interno: <strong id="ver_PRO_CodigoInterno"></strong></span>
                                <span class="pd-chip"><i class="fas fa-industry mr-1"></i>Fabricación: <strong id="ver_PRO_CodigoFabricacion"></strong></span>
                            </div>

                            <!-- PRECIOS -->
                            <div class="pd-price-panel">
                                <div class="row no-gutters text-center align-items-center">

                                    <div class="col-4">
                                        <span class="pd-label">Compra</span>
                                        <div class="pd-value text-danger">S/ <span id="ver_PRO_PrecioCompra"></span></div>
                                    </div>

                                    <div class="col-auto pd-divider" style="height:36px;"></div>

                                    <div class="col-4">
                                        <span class="pd-label">Venta</span>
                                        <div class="pd-value text-success">S/ <span id="ver_PRO_PrecioVenta"></span></div>
                                    </div>

                                    <div class="col-auto pd-divider" style="height:36px;"></div>

                                    <div class="col-3">
                                        <span class="pd-label">Margen</span>
                                        <div class="pd-value text-primary"><span id="ver_Margen"></span>%</div>
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

    {{-- Zoom de imagen: overlay propio (no modal de Bootstrap) para poder
         abrirlo encima de "Ver Detalle" sin pelear con el stacking de modales. --}}
    <div id="lightboxImagenProducto">
        <button type="button" id="lightboxCerrar"><i class="fas fa-times"></i></button>
        <button type="button" id="lightboxPrev" class="lightbox-btn" style="left:20px;"><i class="fas fa-chevron-left"></i></button>
        <button type="button" id="lightboxNext" class="lightbox-btn" style="right:20px;"><i class="fas fa-chevron-right"></i></button>
        <img id="lightboxImg" src="">
        <div id="lightboxContador" class="text-white mt-2" style="font-size:13px;"></div>
    </div>

    <!-- IMPORTAR PRODUCTOS -->
    <div class="modal fade" id="modalImportarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">

                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <i class="fa fa-file-excel text-success me-2"></i>
                        Importar Productos
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="text-muted">
                        Sube un Excel con tus productos, su stock inicial y su stock mínimo. Los productos nuevos se crean; si el nombre
                        ya existe, no se duplica y solo se le agrega el stock. Las categorías que no existan se crean solas.
                    </p>

                    <a href="{{ tenant_url('tenant.inventario.producto.importar.plantilla') }}" class="btn btn-outline-primary btn-sm mb-3">
                        <i class="fa fa-download mr-1"></i> Descargar plantilla
                    </a>

                    <form id="form_importar_producto" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="control-label">Sede / Almacén donde ingresa el stock:</label>
                            <select class="form-control" id="importar_ALM_Id" name="ALM_Id" required>
                                <option value="">Seleccionar ...</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{ $alm->ALM_Id }}">{{ $alm->ALM_NombreAlmacen }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Archivo Excel (.xlsx):</label>
                            <input type="file" class="form-control" id="importar_archivo" name="archivo" accept=".xlsx,.xls,.csv" required>
                        </div>

                    </form>

                    <div id="importar_resultado" style="display:none; max-height: 260px; overflow-y: auto;" class="mt-3"></div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success px-4" id="btnImportarProducto">
                        <i class="fa fa-upload mr-1"></i> Importar
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
                ajax: {
                    url: "{{ tenant_url('tenant.inventario.producto.index') }}",
                    data: function (d) {
                        d.estado = $('#filtroEstadoProducto').val();
                    }
                },
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
                        data: 'PRO_CodigoInterno',
                        name: 'PRO_CodigoInterno',
                        className: 'text-start',
                        defaultContent: '-'
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
                                +data.action1 + ' ' +
                            @endcan
                            ''
                            @can('tenant.inventario.producto.destroy')
                                +data.action2
                            @endcan ;
                        }
                    }
                ],
            });

            $('#filtroEstadoProducto').on('change', function() {
                table.draw();
            });

            $('#productosave').click(function(e) {
                e.preventDefault();
                nombre = $("#PRO_Nombre").val();
                descripcion = $("#PRO_Descripcion").val();
                precioCompra = $("#PRO_PrecioCompra").val();
                precioVenta = $("#PRO_PrecioVenta").val();
                marca = $("#PRO_Marca").val();
                catId = $("#CAT_Id").val();

                if (nombre == '' || descripcion == '' || precioCompra == '' || precioVenta == '' || marca ==
                    '' || catId == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                let formData = new FormData($('#product_form')[0]);
                $.ajax({
                    url: "{{ tenant_url('tenant.inventario.producto.store') }}",
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
                $.get('{{ tenant_url('tenant.inventario.producto.edit', ['producto' => ':producto']) }}'
                    .replace(':producto', Producto_id_edit),
                    function(result) {
                        console.log(result);
                        $('#producto_id_edit').val(result.data.PRO_Id);
                        $('#PRO_Nombre').val(result.data.PRO_Nombre);
                        $('#PRO_Descripcion').val(result.data.PRO_Descripcion);
                        $('#PRO_PrecioCompra').val(result.data.PRO_PrecioCompra);
                        $('#PRO_PrecioVenta').val(result.data.PRO_PrecioVenta);
                        $('#PRO_Marca').val(result.data.PRO_Marca);
                        $('#PRO_CodigoInterno').val(result.data.PRO_CodigoInterno);
                        $('#PRO_CodigoFabricacion').val(result.data.PRO_CodigoFabricacion);
                        $('#PRO_StockMinimo').val(result.data.PRO_StockMinimo);
                        $('#PRO_MostrarCatalogo').prop('checked', !!Number(result.data.PRO_MostrarCatalogo));
                        $('#CAT_Id').val(result.data.CAT_Id);
                        $('#CAT_Id').change();


                        // Mostrar botón Actualizar y ocultar botón Guardar
                        $('#_method').val('PUT').show();
                        $("#productosave").hide();
                        $("#updateBtn").show();

                        // Galería adicional: solo tiene sentido con el producto ya
                        // guardado (necesita PRO_Id para subir/borrar fotos).
                        $('#galeriaProductoWrap').show();
                        renderGaleriaProducto(result.galeria || []);
                    })
            });

            $('body').on('click', '.eyeProducto', function() {
                var Producto_id_ver = $(this).data('id');
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.inventario.producto.show', ['producto' => ':producto']) }}'
                    .replace(':producto', Producto_id_ver),
                    function(data) {
                        console.log(data)
                        $('#ver_PRO_Id').text(data.data.PRO_Id);
                        $('#ver_CAT_Nombre').text(data.data.CAT_Nombre);
                        $('#ver_PRO_Nombre').text(data.data.PRO_Nombre);
                        $('#ver_PRO_Descripcion').text(data.data.PRO_Descripcion);
                        $('#ver_PRO_Marca').text(data.data.PRO_Marca);
                        $('#ver_PRO_CodigoInterno').text(data.data.PRO_CodigoInterno || '-');
                        $('#ver_PRO_CodigoFabricacion').text(data.data.PRO_CodigoFabricacion || '-');
                        $('#ver_PRO_PrecioCompra').text(Number(data.data.PRO_PrecioCompra).toFixed(2));
                        $('#ver_PRO_PrecioVenta').text(Number(data.data.PRO_PrecioVenta).toFixed(2));
                        $('#ver_Imagen').attr('src', data.imagen);
                        $('#ver_PRO_Marca_Wrap').toggle(!!data.data.PRO_Marca);

                        var compra = Number(data.data.PRO_PrecioCompra) || 0;
                        var venta = Number(data.data.PRO_PrecioVenta) || 0;
                        var margen = compra > 0 ? (((venta - compra) / compra) * 100) : 0;
                        $('#ver_Margen').text(margen.toFixed(0));

                        var activo = Number(data.data.PRO_Status) === 1;
                        $('#ver_BadgeEstado')
                            .attr('class', 'badge pd-badge ' + (activo ? 'bg-success' : 'bg-secondary'))
                            .text(activo ? 'Producto Activo' : 'Producto Inactivo');

                        var enCatalogo = data.data.PRO_MostrarCatalogo === undefined || Number(data.data.PRO_MostrarCatalogo) === 1;
                        $('#ver_BadgeCatalogo')
                            .attr('class', 'badge pd-badge ' + (enCatalogo ? 'bg-info' : 'bg-light text-muted border'))
                            .html('<i class="fas ' + (enCatalogo ? 'fa-eye' : 'fa-eye-slash') + ' mr-1"></i>' + (enCatalogo ? 'En catálogo web' : 'Oculto del catálogo'));

                        // Imágenes disponibles para el zoom: principal + galería (usando el
                        // original de cada una, no la miniatura, para ver el detalle real).
                        lightboxImagenes = [data.imagen];
                        var galeriaHtml = '';
                        (data.galeria || []).forEach(function(img, idx) {
                            lightboxImagenes.push(img.PROI_url);
                            galeriaHtml += '<img src="' + img.PROI_Thumb + '" class="rounded border galeria-thumb" data-lightbox-index="' + (idx + 1) + '">';
                        });
                        $('#ver_Galeria').html(galeriaHtml);
                    })
            });

            // ================= ZOOM DE IMAGEN (lightbox) =================

            var lightboxImagenes = [];
            var lightboxIndex = 0;

            function renderLightbox() {
                $('#lightboxImg').attr('src', lightboxImagenes[lightboxIndex]);
                $('#lightboxContador').text((lightboxIndex + 1) + ' / ' + lightboxImagenes.length);
                $('#lightboxPrev, #lightboxNext').toggle(lightboxImagenes.length > 1);
            }

            function abrirLightbox(index) {
                if (!lightboxImagenes.length) {
                    return;
                }
                lightboxIndex = index;
                renderLightbox();
                $('#lightboxImagenProducto').fadeIn(150);
            }

            $('body').on('click', '#ver_ImagenPrincipalWrap', function() {
                abrirLightbox(0);
            });

            $('body').on('click', '.galeria-thumb', function() {
                abrirLightbox($(this).data('lightbox-index'));
            });

            $('#lightboxCerrar, #lightboxImagenProducto').on('click', function(e) {
                if (e.target.id === 'lightboxImagenProducto' || e.target.id === 'lightboxCerrar' || $(e.target).closest('#lightboxCerrar').length) {
                    $('#lightboxImagenProducto').fadeOut(150);
                }
            });

            $('#lightboxPrev').on('click', function(e) {
                e.stopPropagation();
                lightboxIndex = (lightboxIndex - 1 + lightboxImagenes.length) % lightboxImagenes.length;
                renderLightbox();
            });

            $('#lightboxNext').on('click', function(e) {
                e.stopPropagation();
                lightboxIndex = (lightboxIndex + 1) % lightboxImagenes.length;
                renderLightbox();
            });

            $(document).on('keydown', function(e) {
                if (!$('#lightboxImagenProducto').is(':visible')) {
                    return;
                }
                if (e.key === 'Escape') $('#lightboxImagenProducto').fadeOut(150);
                if (e.key === 'ArrowLeft') $('#lightboxPrev').click();
                if (e.key === 'ArrowRight') $('#lightboxNext').click();
            });

            // ================= GALERÍA ADICIONAL DE PRODUCTO (hasta 4 fotos, 5 en total) =================

            function renderGaleriaProducto(items) {
                var html = '';
                items.forEach(function(img) {
                    html += '<div class="position-relative" data-item="' + img.PROI_Item + '">' +
                        '<img src="' + img.PROI_Thumb + '" class="rounded border" style="width:70px;height:70px;object-fit:cover;">' +
                        '<a href="javascript:void(0)" class="eliminarImagenGaleriaProducto" data-item="' + img.PROI_Item + '" ' +
                        'style="position:absolute;top:-6px;right:-6px;background:#dc3545;color:#fff;border-radius:50%;width:20px;height:20px;line-height:18px;text-align:center;font-size:11px;">' +
                        '<i class="fas fa-times"></i></a>' +
                        '</div>';
                });
                $('#galeriaProductoGrid').html(html);
                $('#idFileGaleriaProducto').text(items.length >= 4 ? 'Máximo alcanzado (4/4)' : 'Agregar foto a la galería');
                $('#fileGaleriaProducto').prop('disabled', items.length >= 4);
            }

            $('#fileGaleriaProducto').on('change', function() {
                var file = this.files[0];
                var productoId = $('#producto_id_edit').val();
                if (!file || !productoId) {
                    return;
                }

                var formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    url: '{{ tenant_url('tenant.inventario.producto.galeria.store', ['producto' => ':producto']) }}'
                        .replace(':producto', productoId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        $('#fileGaleriaProducto').val('');
                        renderGaleriaProducto(result.msg.data || []);
                        Toast.fire({
                            type: 'success',
                            title: result.msg.mensaje
                        });
                    },
                    error: function(xhr) {
                        $('#fileGaleriaProducto').val('');
                        var msg = xhr.responseJSON && xhr.responseJSON.msg ? xhr.responseJSON.msg : 'No se pudo subir la foto.';
                        Toast.fire({
                            type: 'error',
                            title: msg
                        });
                    }
                });
            });

            $('body').on('click', '.eliminarImagenGaleriaProducto', function() {
                var item = $(this).data('item');
                var productoId = $('#producto_id_edit').val();

                $.ajax({
                    type: 'DELETE',
                    url: '{{ tenant_url('tenant.inventario.producto.galeria.destroy', ['producto' => ':producto', 'item' => ':item']) }}'
                        .replace(':producto', productoId).replace(':item', item),
                    success: function(result) {
                        renderGaleriaProducto(result.data || []);
                        Toast.fire({
                            type: 'success',
                            title: result.message
                        });
                    },
                    error: function() {
                        Toast.fire({
                            type: 'error',
                            title: 'No se pudo eliminar la foto.'
                        });
                    }
                });
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();
                Producto_id_update = $('#producto_id_edit').val();
                let formData = new FormData($('#product_form')[0]);
                $.ajax({
                    data: $('#product_form').serialize(),
                    url: '{{ tenant_url('tenant.inventario.producto.update', ['producto' => ':producto']) }}'
                        .replace(
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
                $('#galeriaProductoWrap').hide();
                $('#galeriaProductoGrid').empty();
                $('#fileGaleriaProducto').val('').prop('disabled', false);
                $('#PRO_MostrarCatalogo').prop('checked', true);
            }

            $('body').on('click', '.deleteProducto', function() {

                var Producto_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ tenant_url('tenant.inventario.producto.destroy', ['producto' => ':producto']) }}'
                            .replace(
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
                } else {
                    Toast.fire({
                        title: 'Acción cancelada',
                        text: 'El producto no ha sido eliminado.',
                        icon: 'info'
                    });
                }
            });

            $('body').on('click', '.activarProducto', function() {
                var Producto_id_activar = $(this).data("id");

                $.ajax({
                    type: "PUT",
                    url: '{{ tenant_url('tenant.inventario.producto.activar', ['producto' => ':producto']) }}'
                        .replace(':producto', Producto_id_activar),
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        table.draw();
                        Toast.fire({
                            type: 'success',
                            title: String(data.success),
                            icon: 'success'
                        });
                    },
                    error: function(data) {
                        Toast.fire({
                            type: 'error',
                            title: 'No se pudo activar el producto.',
                            icon: 'error'
                        });
                    }
                });
            });

            // IMPORTAR PRODUCTOS
            $('#btnImportarProducto').on('click', function() {
                if (!document.getElementById('form_importar_producto').reportValidity()) {
                    return;
                }

                var formData = new FormData(document.getElementById('form_importar_producto'));
                var $btn = $(this);
                var $resultado = $('#importar_resultado');

                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Importando...');
                $resultado.hide().html('');

                $.ajax({
                    url: "{{ tenant_url('tenant.inventario.producto.importar') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var r = data.resumen;
                        var html = '<div class="alert alert-success mb-2">' +
                            '<b>' + r.creados + '</b> productos creados, ' +
                            '<b>' + r.con_stock_agregado + '</b> ya existían (se les agregó stock), ' +
                            '<b>' + r.errores + '</b> filas con error.' +
                            '</div>';

                        if (r.categorias_creadas.length) {
                            html += '<div class="alert alert-info mb-2"><b>Categorías nuevas creadas:</b> ' +
                                r.categorias_creadas.join(', ') + '</div>';
                        }

                        var errores = data.detalle.filter(function(d) { return d.estado === 'error'; });
                        if (errores.length) {
                            html += '<div class="alert alert-warning mb-0"><b>Filas con error:</b><ul class="mb-0">';
                            errores.forEach(function(e) {
                                html += '<li>Fila ' + e.fila + ': ' + e.detalle + '</li>';
                            });
                            html += '</ul></div>';
                        }

                        $resultado.html(html).show();
                        table.ajax.reload(null, false);

                        Toast.fire({
                            type: 'success',
                            title: 'Importación procesada',
                            icon: 'success'
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        var msg = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error :
                            'No se pudo importar el archivo.';
                        $resultado.html('<div class="alert alert-danger mb-0">' + msg + '</div>').show();
                        Toast.fire({
                            type: 'error',
                            title: 'Fallo la importación',
                            icon: 'error'
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fa fa-upload mr-1"></i> Importar');
                    }
                });
            });
        })
    </script>
@endsection
