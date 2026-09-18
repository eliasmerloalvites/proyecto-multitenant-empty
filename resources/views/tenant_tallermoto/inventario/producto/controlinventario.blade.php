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

        small.text-muted{
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .badge{
            border-radius: 10px;
            font-size: 12px;
        }

        /* ===== Modal "Ver Detalle" — mismo rediseño que Inventario > Productos =====
           Usa las variables de tema del panel (--bg-card/--bg-main/--text-main/
           --text-muted/--primary/--accent, definidas en csskael/kael-dark.css y
           kael-light.css) en vez de colores fijos, para que el contraste sea
           correcto sin importar el tema activo. */

        #modalVerDetalle .modal-content{
            background: var(--bg-card);
        }

        #modalVerDetalle .modal-header{
            background: linear-gradient(135deg, var(--primary), var(--accent));
        }

        #modalVerDetalle .modal-body{
            background: var(--bg-main);
        }

        #modalVerDetalle .modal-footer{
            background: var(--bg-card) !important;
            border-top: 1px solid rgba(148,163,184,.15);
        }

        .pd-title{
            color: var(--text-main);
        }

        .pd-subtitle, .pd-desc{
            color: var(--text-muted);
        }

        .product-image-container{
            position: relative;
            background: var(--bg-card);
            border: 1px solid rgba(148,163,184,.25);
            border-radius: 20px;
            padding: 14px;
            cursor: zoom-in;
            transition: box-shadow .2s ease;
        }

        .product-image-container:hover{
            box-shadow: 0 10px 28px rgba(0,0,0,.18);
        }

        .product-image{
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 14px;
            background: rgba(148,163,184,.12);
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
            background: rgba(15,23,42,.8);
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
            border: 1px solid rgba(148,163,184,.3) !important;
            transition: transform .15s ease, border-color .15s ease;
        }

        .galeria-thumb:hover{
            transform: scale(1.08);
            border-color: var(--primary) !important;
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
            background: rgba(59,130,246,.15);
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .pd-chip{
            background: var(--bg-main);
            border: 1px solid rgba(148,163,184,.25);
            border-radius: 10px;
            padding: 7px 14px;
            font-size: 12.5px;
            color: var(--text-muted);
        }

        .pd-chip strong{
            color: var(--text-main);
            font-weight: 700;
        }

        .pd-price-panel{
            background: var(--bg-main);
            border: 1px solid rgba(148,163,184,.25);
            border-radius: 18px;
            padding: 20px 10px;
        }

        .pd-price-panel .pd-label{
            color: var(--text-muted);
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
            background: rgba(148,163,184,.25);
        }

        /* Panel de stock: propio de Control de Inventario, no existe en la
           ficha de Producto (esta pantalla es justo sobre eso: cuanto hay). */
        .pd-stock-panel{
            background: var(--bg-main);
            border: 1px solid rgba(148,163,184,.25);
            border-radius: 18px;
            padding: 16px;
            margin-top: 16px;
        }

        .pd-stock-panel .pd-label{
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .6px;
            display: block;
        }

        .pd-stock-panel .pd-stock-value{
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
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
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0,0,0,.6);
            cursor: zoom-in;
            transition: transform .12s ease-out;
            will-change: transform;
            touch-action: none;
            user-select: none;
        }

        #lightboxZoomControls{
            position: absolute;
            bottom: 22px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            background: rgba(255,255,255,.08);
            padding: 6px;
            border-radius: 30px;
        }

        #lightboxZoomControls .lightbox-btn{
            position: static;
            transform: none;
            width: 38px;
            height: 38px;
            font-size: 15px;
        }

        #lightboxZoomPct{
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            align-self: center;
            min-width: 42px;
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
        @if ($stockBajoCount > 0)
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <div>
                    <strong>{{ $stockBajoCount }}</strong>
                    {{ $stockBajoCount == 1 ? 'producto está' : 'productos están' }}
                    en stock mínimo o por debajo. Revisa la lista para reponer.
                </div>
            </div>
        @endif
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
                                <th scope="col">Stock Mínimo</th>
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
                <div class="modal-header border-0">

                    <div>
                        <h5 class="modal-title mb-0 text-white">
                            <i class="fas fa-box-open me-2"></i>
                            Detalles del Producto
                        </h5>

                        <small class="text-white" style="opacity:.8;">
                            Información completa e inventario del producto
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

                            <h3 class="fw-bold pd-title mt-2 mb-1" id="ver_PRO_Nombre"></h3>

                            <div class="pd-subtitle mb-3" style="font-size:13px;">
                                ID #<span id="ver_PRO_Id"></span>
                                <span id="ver_PRO_Marca_Wrap">· <span id="ver_PRO_Marca"></span></span>
                            </div>

                            <p class="pd-desc mb-3" id="ver_PRO_Descripcion" style="font-size:14.5px;"></p>

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

                            <!-- STOCK (propio de Control de Inventario) -->
                            <div class="pd-stock-panel">
                                <div class="row no-gutters align-items-center">

                                    <div class="col-5 text-center">
                                        <span class="pd-label">Stock actual</span>
                                        <div class="pd-stock-value" id="ver_StockTotal"></div>
                                    </div>

                                    <div class="col-auto pd-divider" style="height:40px;"></div>

                                    <div class="col-4 text-center">
                                        <span class="pd-label">Stock mínimo</span>
                                        <div class="pd-stock-value" id="ver_StockMinimo"></div>
                                    </div>

                                    <div class="col text-center">
                                        <span class="badge pd-badge" id="ver_BadgeStock"></span>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0">

                    <button type="button" class="btn btn-outline-warning px-3" id="btnVerLotesDesdeDetalle">
                        <i class="fas fa-layer-group me-1"></i> Ver lotes
                    </button>

                    <button type="button" class="btn btn-outline-success px-3" id="btnVerKardexDesdeDetalle">
                        <i class="fas fa-chart-line me-1"></i> Ver kardex
                    </button>

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
        <div style="overflow:hidden; max-height:84vh; margin-top:5vh;">
            <img id="lightboxImg" src="">
        </div>
        <div id="lightboxContador" class="text-white mt-2" style="font-size:13px;"></div>
        <div id="lightboxZoomControls">
            <button type="button" id="lightboxZoomOut" class="lightbox-btn" title="Alejar"><i class="fas fa-search-minus"></i></button>
            <span id="lightboxZoomPct">100%</span>
            <button type="button" id="lightboxZoomIn" class="lightbox-btn" title="Acercar"><i class="fas fa-search-plus"></i></button>
            <button type="button" id="lightboxZoomReset" class="lightbox-btn" title="Restablecer"><i class="fas fa-compress"></i></button>
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
                        data: 'PRO_StockMinimo',
                        name: 'PRO_StockMinimo',
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

            var verDetalleProductoIdActivo = null;

            $('body').on('click', '.eyeProducto', function() {
                var Producto_id_ver = $(this).data('id');
                verDetalleProductoIdActivo = Producto_id_ver;
                $('#modalVerDetalle').modal('show');
                $.get('{{ tenant_url('tenant.inventario.producto.show', ['producto' => ':producto']) }}'.replace(':producto', Producto_id_ver),
                    function(data) {
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

                        // Stock: propio de esta pantalla (Control de Inventario).
                        var stockTotal = Number(data.stock_total) || 0;
                        var stockMinimo = Number(data.data.PRO_StockMinimo) || 0;
                        $('#ver_StockTotal').text(stockTotal.toFixed(2));
                        $('#ver_StockMinimo').text(stockMinimo.toFixed(2));

                        var badgeStock = 'bg-success';
                        var textoStock = 'Disponible';
                        if (stockTotal <= 0) {
                            badgeStock = 'bg-danger';
                            textoStock = 'Agotado';
                        } else if (stockTotal <= stockMinimo) {
                            badgeStock = 'bg-warning text-dark';
                            textoStock = 'Stock bajo';
                        }
                        $('#ver_BadgeStock').attr('class', 'badge pd-badge ' + badgeStock).text(textoStock);

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

            $('#btnVerLotesDesdeDetalle').on('click', function() {
                $('#modalVerDetalle').modal('hide');
                abrirLotesProducto(verDetalleProductoIdActivo);
            });

            $('#btnVerKardexDesdeDetalle').on('click', function() {
                $('#modalVerDetalle').modal('hide');
                $('#modalKardex').modal('show');
                $('#btnFiltrarKardex').data('producto', verDetalleProductoIdActivo);
                cargarKardex(verDetalleProductoIdActivo);
            });

            // ================= ZOOM DE IMAGEN (lightbox) =================

            var lightboxImagenes = [];
            var lightboxIndex = 0;
            var lightboxZoom = 1;
            var lightboxPanX = 0;
            var lightboxPanY = 0;
            var lightboxDragging = false;
            var lightboxDragStart = { x: 0, y: 0 };
            var ZOOM_MIN = 1;
            var ZOOM_MAX = 4;
            var ZOOM_STEP = 0.5;

            function aplicarZoomLightbox() {
                $('#lightboxImg').css('transform', 'translate(' + lightboxPanX + 'px,' + lightboxPanY + 'px) scale(' + lightboxZoom + ')');
                $('#lightboxImg').css('cursor', lightboxZoom > 1 ? 'grab' : 'zoom-in');
                $('#lightboxZoomPct').text(Math.round(lightboxZoom * 100) + '%');
                $('#lightboxZoomOut').prop('disabled', lightboxZoom <= ZOOM_MIN);
                $('#lightboxZoomIn').prop('disabled', lightboxZoom >= ZOOM_MAX);
            }

            function resetZoomLightbox() {
                lightboxZoom = 1;
                lightboxPanX = 0;
                lightboxPanY = 0;
                aplicarZoomLightbox();
            }

            function cambiarZoomLightbox(delta) {
                lightboxZoom = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, lightboxZoom + delta));
                if (lightboxZoom === ZOOM_MIN) {
                    lightboxPanX = 0;
                    lightboxPanY = 0;
                }
                aplicarZoomLightbox();
            }

            function renderLightbox() {
                $('#lightboxImg').attr('src', lightboxImagenes[lightboxIndex]);
                $('#lightboxContador').text((lightboxIndex + 1) + ' / ' + lightboxImagenes.length);
                $('#lightboxPrev, #lightboxNext').toggle(lightboxImagenes.length > 1);
                resetZoomLightbox();
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
                if (e.key === '+' || e.key === '=') cambiarZoomLightbox(ZOOM_STEP);
                if (e.key === '-' || e.key === '_') cambiarZoomLightbox(-ZOOM_STEP);
            });

            $('#lightboxZoomIn').on('click', function(e) {
                e.stopPropagation();
                cambiarZoomLightbox(ZOOM_STEP);
            });

            $('#lightboxZoomOut').on('click', function(e) {
                e.stopPropagation();
                cambiarZoomLightbox(-ZOOM_STEP);
            });

            $('#lightboxZoomReset').on('click', function(e) {
                e.stopPropagation();
                resetZoomLightbox();
            });

            $('#lightboxImg').on('wheel', function(e) {
                e.preventDefault();
                e.stopPropagation();
                cambiarZoomLightbox(e.originalEvent.deltaY < 0 ? ZOOM_STEP : -ZOOM_STEP);
            });

            $('#lightboxImg').on('dblclick', function(e) {
                e.stopPropagation();
                if (lightboxZoom > 1) {
                    resetZoomLightbox();
                } else {
                    lightboxZoom = 2;
                    aplicarZoomLightbox();
                }
            });

            $('#lightboxImg').on('mousedown', function(e) {
                if (lightboxZoom <= 1) {
                    return;
                }
                e.preventDefault();
                lightboxDragging = true;
                lightboxDragStart = { x: e.clientX - lightboxPanX, y: e.clientY - lightboxPanY };
                $(this).css('cursor', 'grabbing');
            });

            $(document).on('mousemove', function(e) {
                if (!lightboxDragging) {
                    return;
                }
                lightboxPanX = e.clientX - lightboxDragStart.x;
                lightboxPanY = e.clientY - lightboxDragStart.y;
                $('#lightboxImg').css('transform', 'translate(' + lightboxPanX + 'px,' + lightboxPanY + 'px) scale(' + lightboxZoom + ')');
            });

            $(document).on('mouseup', function() {
                if (lightboxDragging) {
                    lightboxDragging = false;
                    $('#lightboxImg').css('cursor', lightboxZoom > 1 ? 'grab' : 'zoom-in');
                }
            });

            $('body').on('click', '.lotesProducto', function() {
                abrirLotesProducto($(this).data('id'));
            });

            function abrirLotesProducto(Producto_id_ver) {
                $('#modalVerLotes').modal('show');
                $.get('{{ tenant_url('tenant.inventario.controlinventario.lotes', ['producto' => ':producto']) }}'.replace(':producto', Producto_id_ver),
                    function(data) {
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
            }

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
