@extends('tenant_generico.layout.appAdminLte')
@section('titulo', 'Crear Venta')
@section('contenido')
    <style type="text/css">
        :root {
            --kael-primary: #6C3BFF;
            --kael-secondary: #8B5CF6;
            --kael-accent: #A6CE39;
            --kael-dark: #131722;
            --kael-light: #F4F7FB;
            --kael-border: #E5E7EB;
            --kael-text: #1F2937;
        }

        body {
            background: linear-gradient(135deg, #f4f7fb 0%, #eef2ff 100%);
            font-family: 'Inter', sans-serif;
        }

        #contenedor1 {
            background: rgba(255, 255, 255, .75);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            border: 1px solid rgba(255, 255, 255, .4);
        }

        .table-responsive {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
        }

        #detallesVenta {
            background: #fff;
        }

        #detallesVenta thead {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2;
        }

        #detallesVenta th {
            padding: 18px;
            font-size: 14px;
            color: #6B7280;
            border: none;
        }

        #detallesVenta td {
            border: none !important;
            padding: 4px;
            border-bottom: 1px solid #F3F4F6 !important;
        }

        #detallesVenta tr {
            transition: .2s ease;
        }

        #detallesVenta tr:hover {
            background: #F9FAFB;
            transform: scale(1.01);
        }

        #total {
            font-size: 28px;
            font-weight: 800;
            color: var(--kael-primary);
        }

        .sale-row {
            transition: .2s ease;
        }

        .sale-row:hover {
            background: #F9FAFB;
        }

        .sale-detail {
            padding: 8px !important;
        }

        .sale-product-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 3px;

            text-transform: capitalize;
        }

        .sale-product-info {
            display: flex;
            align-items: center;
            gap: 8px;

            font-size: 11px;
            color: #9CA3AF;
        }

        .sale-qty {
            font-weight: 700;
            color: #111827;
        }

        .sale-unit-price {
            color: #6B7280;
        }

        .sale-discount {
            color: #EF4444;
            font-weight: 600;
        }

        .sale-total {
            vertical-align: top !important;
            text-align: right;
            padding: 14px !important;
        }

        .sale-total-price {
            font-size: 16px;
            font-weight: 800;
            color: #6C3BFF;
        }

        .btnGenerar {
            background: linear-gradient(135deg, #A6CE39, #8EB82E);
            border: none;
            border-radius: 20px;
            font-size: 18px;
            font-weight: 800;

            box-shadow:
                0 12px 24px rgba(166, 206, 57, .25);

            transition: .25s ease;
        }

        .btnGenerar:hover {
            transform: translateY(-3px);
        }

        .btn-select {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            min-height: 58px;
            font-weight: 600;
            transition: .2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .03);
        }

        .btn-select:hover {
            border-color: var(--kael-primary);
            transform: translateY(-2px);
        }

        #detallesCalculadora {
            border: none !important;
        }

        #detallesCalculadora td {
            border: none !important;
            padding: 6px !important;
        }

        .btn-calculadora {
            border: none;
            border-radius: 18px;
            background: #fff;
            color: #111827;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .06);
            font-size: 28px !important;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn-calculadora:hover {
            transform: translateY(-2px);
            background: #6C3BFF;
            color: #fff;
        }

        #detalles {
            width: 100% !important;
            table-layout: fixed !important;
        }

        #detalles td {
            width: 33.33% !important;
            max-width: 33.33% !important;
            padding: 0px !important;
            vertical-align: top;
        }

        .btntipo1 {
            width: 100% !important;
            height: 140px !important;

            border-radius: 24px !important;
            background: #fff !important;

            border: none !important;

            box-shadow: 0 10px 20px rgba(0, 0, 0, .06);

            transition: .25s ease;

            padding: 14px !important;

            display: flex !important;
            align-items: center !important;
            gap: 14px;

            overflow: hidden;

            text-align: left !important;
        }

        .btntipo1:hover {
            transform: translateY(-4px);

            box-shadow:
                0 20px 40px rgba(108, 59, 255, .12);
        }

        .btntipo1 img {
            width: 70px !important;
            height: 70px !important;
            min-width: 70px !important;
            object-fit: contain;
        }

        .btntipo1 span,
        .btntipo1 small {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-image-container {
            width: 78px;
            height: 78px;

            min-width: 78px;

            border-radius: 18px;
            background: #F9FAFB;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image-container img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .product-content {
            flex: 1;
            min-width: 0;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-title {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.25;

            color: #111827;

            margin-bottom: 6px;

            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;

            overflow: hidden;

            text-transform: capitalize;
        }

        .product-description {
            font-size: 12px;
            color: #9CA3AF;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;

            margin-bottom: 8px;
        }

        .product-price {
            color: #6C3BFF;
            font-size: 22px;
            font-weight: 800;

            line-height: 1;
        }

        .product-td {
            width: 33.33%;
            padding: 10px !important;
            vertical-align: top;
        }

        .table::-webkit-scrollbar,
        .table-responsive::-webkit-scrollbar {
            width: 8px;
        }

        .table::-webkit-scrollbar-thumb,
        .table-responsive::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 10px;
        }

        .kael-products-container {
            background: linear-gradient(135deg, rgba(255, 255, 255, .85), rgba(255, 255, 255, .65));
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 5px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }

        .kael-products-grid {
            border-collapse: separate !important;
            border-spacing: 12px !important;
        }

        .dropdown-menu {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, .12);
        }

        .dropdown-item {
            padding: 14px 18px;
        }

        .dropdown-item:hover {
            background: #F3F4F6;
        }

        input.form-control {
            border-radius: 14px !important;
            border: 1px solid #E5E7EB !important;
            box-shadow: none !important;
        }

        @media(max-width:768px) {

            #contenedor1 {
                padding: 14px;
            }

            .btntipo1 {
                min-height: 120px !important;
            }

            .btn-calculadora {
                font-size: 22px !important;
            }

            #total {
                font-size: 22px;
            }
        }
    </style>

    <div class="col-lg-5  col-md-12 col-sm-12 col-xs-12 ">

        <form action="{{ route('tenant.ventas.venta.store', tenant('id')) }}" method="POST" id="venta_form">
            @csrf
            <input class="form-control" hidden id="VentaTipo" name="VentaTipo" value="LIBRE" />
            <input class="form-control" hidden name="USU_Id" value="{{ Auth::user()->id }}" />
            <div class="col-lg-12  col-md-12 col-sm-12 col-xs-12 " controls id="contenedor1">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 1px">
                    <div class="table-responsive" style="height: calc(60vh - 90px);  overflow-y:scroll;">
                        <table id="detallesVenta" class="table table-sm table-condensed " style="width: 100%">
                            <thead>
                                <th style="text-align: left;  width: 80%;">Detalle</th>
                                <th style="text-align: right;  width: 20%;">Importe</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background-color: #DDD;">

                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="text-align: center;font-weight: bold;">
                            TOTAL</div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="text-align: right;"><label id="total"
                                name="total">S/ 0.00</label><input name="total_venta" hidden id="total_venta"></div>
                        <!--<th hidden="true"><input hidden name="totalVenta" id="totalVenta"></th>-->
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px">
                        <div class="input-group">
                            <span type="button" id="btnPago" class="btn btn-primary btn-sm " hidden
                                onclick="PagoCalculadora()"
                                style="border-bottom-left-radius:10px; border-top-left-radius: 10px; border-bottom-right-radius:10px; border-top-right-radius: 10px;"><i
                                    class="fa fa-plus"></i></span>
                            <span class="form-control"
                                style="background: #EDEDED; border-bottom-left-radius:10px; border-top-left-radius: 10px;">PAGO</span>
                            <input onclick="PagoCalculadora()" type="text" class="form-control input-sm" readonly="true"
                                value="0" name="VEN_Pagado" id="idPagado">
                            <span class="form-control" style="background: #EDEDED; ">VUELTO</span>
                            <input type="text" class="form-control input-sm" readonly="true" value="0"
                                name="VEN_Vuelto" id="idVuelto"
                                style="border-bottom-right-radius:10px; border-top-right-radius: 10px;">

                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-5  col-md-5 col-sm-12 col-xs-12 ">
                        <div class="table-responsive col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <table id="detallesInformacion"
                                class="table table-sm table-bordered table-condensed table-hover" hidden>
                                <thead style="background: #dddddd;">
                                    <th style="text-align: left; font-size: 12px; width: 80%;font-weight: bold;">
                                        <label>PRODUCTO</label>
                                    </th>
                                    <th style="text-align: left; font-size: 12px; width: 20%;font-weight: bold;">
                                        <label>STOCK</label>
                                    </th>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>

                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="input-group" hidden>
                                <span class="form-control"
                                    style="background: #EDEDED; border-bottom-left-radius:10px; border-top-left-radius: 10px;">TIPO
                                    PAGO</span>
                                <select class="form-control " id="VEN_TipoPago" name="VEN_TipoPago">
                                    <option value="1" selected>CONTADO</option>
                                    <!--<option value="2">CREDITO</option>-->
                                </select>

                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px">
                            <div class="dropdown" style="width: 100%">
                                <input type="hidden" id="hiddenSelectedIdMetodoPago" name="selectedIdMetodoPago"
                                    value="1">
                                <button type="button" class="btn-select"><b>Metodo Pago: </b><span id="selectedOption">
                                        EFECTIVO</span></button>
                                <div class="dropdown-menu">
                                    @foreach ($metodo_pago as $mep)
                                        <div class="dropdown-item" data-id="{{ $mep->MEP_Id }}">
                                            {{ $mep->MEP_Pago }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="dropdown" style="width: 100%" hidden>
                                <input type="hidden" id="hiddenSelectedIdTipoComprobante" name="selectedIdTipoComprobante"
                                    value="NOTA VENTA">
                                <button type="button" class="btn-select"><b>Comprobante: </b><span
                                        id="selectedOption">NOTA VENTA</span></button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-item" data-id="NOTA VENTA">NOTA VENTA</div>
                                    <div class="dropdown-item" data-id="BOLETA">BOLETA</div>
                                    <div class="dropdown-item" data-id="FACTURA">FACTURA</div>
                                </div>
                            </div>
                            <input type="hidden" id="hiddenSelectedIdCliente" name="selectedIdCliente" value="1">
                            <button type="button" class="btn-select" onclick="showCliente()"><b>Cliente:
                                </b><span id="selectedCliente"> SIN NOMBRE </span></button>
                            <button type="button" target="_blank" class="btn btn-success" onclick="aceptar();">GENERAR
                                PAGO</button>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px">
                            <input hidden name="_token" value="{{ csrf_token() }}">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" hidden>
                                <button title="Vaciar Carrito" onclick="vaciar()" target="_blank" type="button"
                                    class="btn btn-danger" style="border-radius: 10px;">VACIAR </button>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-7  col-md-7 col-sm-12 col-xs-12 ">

                        <div class="table kael-products-container" style="height: calc(30vh - 10px)">
                            <table id="detallesCalculadora"
                                class="table table-sm table-bordered table-condensed table-hover ; padding:0px;margin:0px">
                                <thead hidden style="background: #ade9ff;">
                                </thead>

                                <tr style="padding:0px;margin:0px">
                                    <td
                                        style=" text-align: center; padding:0px;margin:0px; font-size: 30px; width: 15%;  font-weight: bold;  ">
                                        <button class="btn-calculadora"
                                            style="width: 100%; padding:0px;margin:0px; height:50px;" title="1"
                                            type="button" id="idn1" onclick="Editar('1')">1</button>
                                    </td>
                                    <td
                                        style=" text-align: center; padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora"
                                            style="width: 100%; padding:0px;margin:0px; height:50px; " title="2"
                                            type="button" id="idn2" onclick="Editar('2')">2</button>
                                    </td>
                                    <td
                                        style=" text-align: center; padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora"
                                            style="width: 100%; padding:0px;margin:0px; height:50px; " title="3"
                                            type="button" id="idn3" onclick="Editar('3')">3</button>
                                    </td>
                                    <td
                                        style=" text-align: center; padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora"
                                            style="width: 100%; padding:0px;margin:0px; height:50px; " title="Cantidad"
                                            type="button" id="idn4" onclick="Metodo('CANTIDAD')">Cant</button>
                                    </td>


                                </tr>
                                <tr>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="4"
                                            type="button" id="idn5" onclick="Editar('4')">4</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="5"
                                            type="button" id="idn6" onclick="Editar('5')">5</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="6"
                                            type="button" id="idn7" onclick="Editar('6')">6</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; "
                                            title="Descuento" type="button" id="idn8"
                                            onclick="Metodo('DESCUENTO')">Desc.</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="7"
                                            type="button" id="idn9" onclick="Editar('7')">7</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="8"
                                            type="button" id="idn10" onclick="Editar('8')">8</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="9"
                                            type="button" id="idn11" onclick="Editar('9')">9</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; "
                                            title="Precio Venta" type="button" onclick="Metodo('PRECIO')"
                                            id="idn12">Prec.</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; "
                                            title="Eliminar Producto" type="button" id="idn13"
                                            onclick="eliminar()"><i class="fa fa-trash"
                                                style="color: #FF0000"></i></button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="0"
                                            type="button" id="idn14" onclick="Editar('0')">0</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; " title="."
                                            type="button" id="idn15" onclick="Editar('.')">.</button>
                                    </td>
                                    <td
                                        style=" text-align: center;padding:0px;margin:0px;  font-size: 30px; width: 15%;  font-weight: bold; ">
                                        <button class="btn-calculadora" style="width: 100%; height:50px; "
                                            title="Eliminar" type="button" id="idn4" onclick="elimi('.')"><i
                                                class="fa fa-times"></i></button>
                                    </td>

                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>


    <div class="col-lg-7  col-md-12 col-sm-12 col-xs-12 ">
        <div class="row">
            <div class="col-lg-12  col-md-12 col-sm-12 col-xs-12 ">
                <div class="row">
                    {{-- <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 1px">
                        <select class="form-control" id="idClase" style="width: 100%;" onchange="FiltrarCategoria()" >
                            @foreach ($clase as $t => $val)                            
                                <option value="{{$val->CLA_Id}}" >{{$val->CLA_Nombre}}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="margin-top: 1px; margin-left:10px">
                        <select id="idCategoria-icon" class="form-control" style="width: 100%;"
                            onchange="Tabla_Producto2();">
                            @foreach ($categoria as $t => $val)
                                <option value="{{ $val->CAT_Id }}"
                                    data-img-src="/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/categoria/{{ $val->CAT_Imagen }}">
                                    {{ $val->CAT_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


            </div>
            {{-- <div class="col-lg-3  col-md-3 col-sm-3 col-xs-12 ">
                <div class="table" style="height: calc(28vh - 10px);   overflow-y:scroll">
                    <table id="detallesClase" class="table table-sm table-bordered table-condensed table-hover ; padding:0px;margin:0px">
                        <thead hidden style="background: #ade9ff;">
                        </thead>
                        @foreach ($clase as $t => $val)
                        <tr>
                            <td style=" text-align: center;  font-size: 30px;"><button class="btn4" id="btnidc{{$t+1}}" onclick="Tabla_Categoria({{$t+1}});" style="text-align:center;" value="{{$val->CLA_Id}}" type="button">{{$val->CLA_Nombre}}</button></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div> --}}
        </div>
        <div class="col-lg-12  col-md-12 col-sm-12 col-xs-12 ">
            {{-- <div class="table-responsive" style="background:#FFF;">
                <table id="detallesCategoria" class="table  table-bordered table-condensed table-hover">
                    <thead hidden style="background: #ade9ff;">
                    </thead>
                    <tr>
                        @foreach ($categoria as $t => $val)
                        <td style=" text-align: center;"><button class="btn2" id="btnidt{{$t+1}}" onclick="Tabla_Producto({{$t+1}});" style="text-align:center;" value="{{$val->CAT_Id}}" type="button">{{$val->CAT_Nombre}}</button></td>
                        @endforeach
                    </tr>
                </table>
            </div> --}}
        </div>
        <div class="col-lg-12  col-md-12 col-sm-12 col-xs-12 ">
            <div class="table kael-products-container"
                style="height: calc(90vh - 90px); background: linear-gradient(135deg, #f4f7fb 0%, #eef2ff 100%);  overflow-y:scroll">
                <table id="detalles" class="table kael-products-grid">
                    <thead hidden>
                        <th style="text-align: center; width: 33%;">1</th>
                        <th style="text-align: center; width: 33%;">2</th>
                        <th style="text-align: center; width: 34%;">3</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalSelectCliente" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11" style="margin-top: 10px">
                            <label class="control-label" style=" text-align: left; display: block;">Seleccionar
                                Cliente:</label>
                            <select class="form-control select2" onchange="onSelectCliente()" id="idCliente"
                                style="width: 100%">
                                @foreach ($clientes as $cli)
                                    <option value="{{ $cli->CLI_Id }}">({{ $cli->CLI_NumDocumento }})
                                        {{ $cli->CLI_Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-top: 38px">
                            <span title="Nuevo Cliente" type="button" id="btnNuevoCliea" class="btn btn-success"
                                onclick="mostrarformulario()"
                                style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"><i
                                    class="fa fa-plus"></i> <i class="fa fa-user"></i></span>
                        </div>
                    </div>

                    <div class="card mt-2" id="idfrmCliente" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title">CREAR CLIENTE</h5>
                            <p class="card-text"></p>
                            <form method="POST" id="cliente_form"
                                action="{{ route('tenant.ventas.cliente.store', tenant('id')) }}">
                                @csrf
                                <input type="text" id="cliente_id_edit" hidden>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                Cliente
                                            </span>
                                        </div>
                                        <select class="form-control" onChange="Limitar()" id="idCLI_TipoDocumento"
                                            name="CLI_TipoDocumento"
                                            style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;">
                                            <option value="DNI">
                                                DNI
                                            </option>
                                            <option value="RUC">
                                                RUC
                                            </option>
                                            <option value="CE">
                                                Carnet Extrangeria
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                Nº Doc
                                            </span>
                                        </div>
                                        <input class="form-control input-sm" maxlength="8" id="idCLI_NumDocumento"
                                            name="CLI_NumDocumento" required placeholder="Ingresa Numero de Documento"
                                            type="text">
                                        <span class="input-group-append btn btn-primary btn-sm" id="Buscar_Cliente"
                                            style="display: block; border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                            onclick="buscarCliente()"><i class="fas fa-search"></i></span>
                                        <span class="input-group-append btn btn-primary btn-sm hide" id="cargando"
                                            style="display: none; border-bottom-right-radius:10px; border-top-right-radius: 10px;"><img
                                                width="15px" src="{{ asset_root('images/gif/cargando1.gif') }}"></span>


                                    </div>
                                </div>

                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                Razon social
                                            </span>
                                        </div>
                                        <input class="form-control input-sm" id="idCLI_Nombre" name="CLI_Nombre"
                                            onkeyup="this.value=this.value.toUpperCase();" required
                                            placeholder="ingresa nombre"
                                            style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                            type="text">
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                Direccion
                                            </span>
                                        </div>
                                        <input class="form-control input-sm" id="idCLI_Direccion" name="CLI_Direccion"
                                            onkeyup="this.value=this.value.toUpperCase();" placeholder="ingresa direccion"
                                            style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                            type="text">
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                Telefono
                                            </span>
                                        </div>
                                        <input class="form-control input-sm" maxlength="9" name="CLI_Telefono"
                                            onkeyup="this.value=this.value.toUpperCase();" placeholder="ingresa numero"
                                            style=" border-bottom-right-radius:10px; border-top-right-radius: 10px;"
                                            type="text">
                                    </div>
                                </div>
                                <p></p>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button id="saveBtn" class="btn btn-primary"><i class="fas fa-save"></i>Guardar</button>
                            <button id="updateBtn" class="btn btn-info" style="display: none;"><i
                                    class="fas fa-save"></i>Actualizar</button>
                            <button type="reset" id="btncancelar" class="btn btn-danger"> <i
                                    class="fas fa-ban"></i>Cancelar </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {

                $('body').addClass('sidebar-collapse');

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'info'
                });

                $('.select2').select2()

                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })

                $('#btnNuevo').click(function() {
                    habilitar();
                });

                $('#idCategoria-icon').select2({
                    minimumResultsForSearch: Infinity,
                    templateResult: formatState,
                    templateSelection: formatState
                });

                document.querySelector(".btn-select").addEventListener("click", function() {
                    // Alternar la visibilidad del menú
                    this.parentNode.classList.toggle("open");
                });

                document.querySelectorAll(".dropdown-menu div").forEach(option => {
                    option.addEventListener("click", function() {
                        const selectedText = this.textContent.trim();
                        const selectedId = this.getAttribute("data-id");
                        document.getElementById("selectedOption").textContent = selectedText;
                        document.querySelector("#hiddenSelectedIdMetodoPago").value = selectedId;
                        this.closest(".dropdown").classList.remove("open");
                    });
                });

                // Cerrar el menú si se hace clic fuera
                document.addEventListener("click", function(event) {
                    const dropdown = document.querySelector(".dropdown");
                    if (!dropdown.contains(event.target)) {
                        dropdown.classList.remove("open");
                    }
                });


                Tabla_Producto2()

                function formatState(state) {
                    if (!state.id) {
                        return state.text;
                    }
                    const img = $(state.element).data('img-src');
                    return $(
                        `<span><img src="${img}" style="width: 30px; height: 30px; margin-right: 10px;" />${state.text}</span>`
                    );
                }

                window.addEventListener("keydown", function(e) {
                    if (e.keyCode == 13) {
                        toggleFullScreen();
                    }
                }, false);

                $('#saveBtn').click(function(e) {
                    e.preventDefault();
                    const form = document.getElementById('cliente_form');
                    if (form.checkValidity()) {
                        $.ajax({
                            data: $('#cliente_form').serialize(),
                            url: "{{ route('tenant.ventas.cliente.store', tenant('id')) }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                console.log('Success:', data);
                                document.getElementById("selectedCliente").textContent = data[0]
                                    .CLI_Nombre;
                                document.querySelector("#hiddenSelectedIdCliente").value = data[0]
                                    .CLI_Id;
                                Toast.fire({
                                    type: 'success',
                                    title: data.success
                                })
                                vaciarCampos();
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                Toast.fire({
                                    type: 'error',
                                    title: 'cliente fallo al Registrarse.'
                                })
                            }
                        });
                    } else {
                        form.reportValidity();
                    }

                });


            });


            function toggleFullScreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            }


            function Tabla_Categoria(id) {
                var idcla = $('#btnidc' + id).val();

                $("#detallesCategoria tbody").html('');
                $("#detalles tbody").html('');
                $con = 0;
                $fin = 0;
                $ind = 1;
                var fila = "<tr>";
                <?php foreach ($categoria as $t => $val): ?>
                $cla = "<?php echo $val->CLA_Id; ?>";
                $idtipo = "<?php echo $val->CAT_Id; ?>";
                $cate = "<?php echo $val->CAT_Nombre; ?>";
                if (idcla == $cla) {
                    fila += '<td style=" text-align: center;"><button class="btn2" id="btnidt' + $ind +
                        '" onclick="Tabla_Producto(' + $ind + ');" value="' + $idtipo +
                        '" style="text-align:center;" value="1" type="button">' + $cate + '</button></td>';

                    $ind = $ind + 1;
                }
                <?php endforeach ?>
                fila += '</tr>';
                $("#detallesCategoria").append(fila);


            }


            //metodo para agregar pedidos de manera temporal
            $POS = 0
            $MET = 0
            $NUME = 0
            $DECI = 0
            $ACTD = "NO"
            $ACTCLI = "NO"
            $CALCULADORA = "NO"
            var ListPedido = [];
            var subtotal = 0; //importe
            var total = 0;
            var cont = 0;

            function agregar(ind) {
                datosArticulo = document.getElementById('btnidp' + ind + '').value.split('_');

                idProducto = datosArticulo[0];
                producto = datosArticulo[1];
                stock = datosArticulo[2];
                pVenta = datosArticulo[3];
                cantidad = 1;
                $editar = 0
                console.log("ListPedidoAntes", ListPedido);
                for (var i = 0; i < ListPedido.length; i++) {
                    if (ListPedido[i][0] == idProducto) {
                        var can = ListPedido[i][3]
                        var sub = ListPedido[i][4]
                        subtotal = parseFloat(cantidad * pVenta);
                        total = parseFloat(total + subtotal);
                        $("#total").html("S/ " + total);
                        $("#idCPC_MontoTotal").val(total);
                        $("#idCPC_MontoFaltante").val(total);
                        ListPedido[i][3] = parseFloat(can) + parseFloat(cantidad)
                        ListPedido[i][4] = parseFloat(ListPedido[i][3] * ListPedido[i][2]).toFixed(1);
                        ListPedido[i][6] = stock;

                        $editar = 1
                    }

                }
                console.log("ListPedidoValidato", ListPedido);


                if (idProducto != "" && cantidad > 0 && pVenta > 0) {

                    if ($editar == 0) {
                        subtotal = parseFloat(cantidad * pVenta);
                        /*total = parseFloat(total+subtotal);
                        $("#total").html("S/ "+total.toFixed(1));
                        $("#idCPC_MontoTotal").val(total.toFixed(1));
                        $("#idCPC_MontoFaltante").val(total.toFixed(1));*/

                        var fila1 = [idProducto, producto, pVenta, cantidad, subtotal.toFixed(1), "0", stock];
                        ListPedido.push(fila1);
                    }
                    console.log("ListPedidoDespues", ListPedido);
                    actualizarTabla()
                    

                    //alert($POS)
                    total1 = 0;
                    for (var i = 0; i < ListPedido.length; i++) {
                        total1 = (parseFloat(total1) + parseFloat(ListPedido[i][4])).toFixed(1);
                        //alert(ListPedido[i][5]);

                    }
                    $("#total").html("S/. " + total1);
                    $("#total").val(total1);
                    limpiar_numeros();
                    ValidarVuelto();


                } else {
                    alert("Error, Revise los datos del Producto");
                }
            }

            function actualizarTabla(){
                $("#detallesVenta tbody").html('');
                for (var i = 0; i < ListPedido.length; i++) {
                    console.log("entro al for "+i+"", ListPedido[i]);
                    var fila = `
                        <tr id="fila${i}" class="sale-row" onclick="posicionamiento(${i});">
                            <td class="sale-detail">
                                <input readonly hidden name="PRO_Id[]" value="${ListPedido[i][0]}">
                                <div class="sale-product-name"> ${capitalizeWords(ListPedido[i][1])}</div>
                                <div class="sale-product-info">
                                    <input readonly type="number" hidden onkeyup="Valida2(${i});"
                                        name="DEV_Cantidad[]" id="cantidad${i}" value="${ListPedido[i][3]}">
                                    <span id="cantidadLabel${i}" class="sale-qty">
                                        ${ListPedido[i][3]}
                                    </span>
                                    <span class="sale-unit-price">
                                        Unit x S/ 
                                        <span id="precioUnitLabel${i}">
                                            ${ListPedido[i][2]}
                                        </span>
                                    </span>
                                    <span id="descuentoLabel${i}" 
                                        class="sale-discount">
                                    </span>
                                    <input readonly hidden type="number" name="DEV_PrecioUnitario[]"
                                        id="precioUnit${i}" value="${ListPedido[i][2]}">
                                    <input readonly hidden name="DEV_Descuento[]" onkeyup="Valida2(${i});"
                                        id="descuento${i}" value="${ListPedido[i][5]}">
                                </div>
                            </td>
                            <td class="sale-total">
                                <input disabled hidden name="subTot${i}"
                                    id="subTot${i}" value="${ListPedido[i][4]}">
                                <span id="subTotLabel${i}" class="sale-total-price">
                                    S/ ${ListPedido[i][4]}
                                </span>
                            </td>
                        </tr>
                        `;
                    console.log("fila", fila);
                    $("#detallesVenta tbody").append(fila);

                    $POS = i;
                }

                $("#detallesVenta").on("click", "tr", function() {
                    // Remueve la clase 'selected' de todas las filas
                    $("#detallesVenta tr").removeClass("selected");
                    // Añade la clase 'selected' a la fila clickeada
                    $(this).addClass("selected");
                });



                $("#detallesInformacion tbody").html('');
                var filainfo = '<tr  ><td><label style="font-size: 11px;"  hidden ">' + ListPedido[$POS][1] +
                    '</label></td><td><label style="font-size: 11px;"  hidden>' + stock + '</label></td></tr>'
                $("#detallesInformacion").append(filainfo);
            }

            function FiltrarCategoria() {
                $valor = $('#idClase').val();

            }

            function Tabla_Producto2() {
                var idTipo = $('#idCategoria-icon').val();
                $("#detalles tbody").html('');
                $con = 0;
                $fin = 0;
                $ind = 1;

                <?php foreach ($lotesuni as $lot): ?>
                $categoriaid = '<?php echo $lot->CATEGORIA; ?>';
                $Producto = '<?php echo $lot->PRO_Nombre; ?>';
                $Descripcion = '<?php echo $lot->PRO_Descripcion; ?>';
                $Imagen = '<?php echo $lot->PRO_Imagen; ?>';
                $idProducto = '<?php echo $lot->PRO_Id; ?>';
                $stock = '<?php echo $lot->PRO_Cantidad; ?>';
                $preventa = '<?php echo $lot->PRO_PrecioBaseVenta; ?>';

                if (idTipo == $categoriaid) {
                    $con = $con + 1;
                    if ($con == 1) {
                        var fila = "<tr>";
                        if ($stock > 0) {
                            fila += `
                                <td class="product-td">
                                    <button class="btntipo1" 
                                            id="btnidp${$ind}" 
                                            onclick="agregar(${$ind});" 
                                            value="${$idProducto}_${$Producto}_${$stock}_${$preventa}" 
                                            type="button">
                                        <div class="product-image-container">
                                            <img 
                                                src="/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/producto/${$Imagen}" 
                                                alt="Imagen">
                                        </div>

                                        <div class="product-content">
                                            <div class="product-title">${capitalizeWords($Producto)}</div>
                                            <div class="product-description">${$Descripcion.toLowerCase()}</div>
                                            <div class="product-price">S/. ${$preventa}</div>
                                        </div>
                                    </button>
                                </td>
                                `;
                        } else {
                            fila += '<td style=" text-align: center;"><button class="btntipo3" disabled id="btnidp' + $ind +
                                '" onclick="agregar(' + $ind + ');" value="' + $idProducto + '_' + $stock + '_' + $preventa +
                                '" style="text-align:center;" value="1" type="button">' + $Producto + '</button></td>';
                        }

                    } else if ($con == 2) {
                        if ($stock > 0) {
                            fila += `
                                <td class="product-td">
                                    <button class="btntipo1" 
                                            id="btnidp${$ind}" 
                                            onclick="agregar(${$ind});" 
                                            value="${$idProducto}_${$Producto}_${$stock}_${$preventa}" 
                                            type="button">
                                        <div class="product-image-container">
                                            <img 
                                                src="/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/producto/${$Imagen}" 
                                                alt="Imagen">
                                        </div>
                                        <div class="product-content">
                                            <div class="product-title">${capitalizeWords($Producto)}</div>
                                            <div class="product-description">${$Descripcion.toLowerCase()}</div>
                                            <div class="product-price">S/. ${$preventa}</div>
                                        </div>
                                    </button>
                                </td>
                                `;
                        } else {
                            fila += '<td style=" text-align: center;"><button class="btntipo3" disabled id="btnidp' + $ind +
                                '" onclick="agregar(' + $ind + ');" value="' + $idProducto + '_' + $stock + '_' + $preventa +
                                '" style="text-align:center;" value="1" type="button">' + $Producto + '</button></td>';
                        }

                    } else if ($con == 3) {
                        if ($stock > 0) {
                            fila += `
                                <td class="product-td">
                                    <button class="btntipo1" 
                                            id="btnidp${$ind}" 
                                            onclick="agregar(${$ind});" 
                                            value="${$idProducto}_${$Producto}_${$stock}_${$preventa}" 
                                            type="button">
                                        <div class="product-image-container">
                                            <img 
                                                src="/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/producto/${$Imagen}" 
                                                alt="Imagen">
                                        </div>
                                        <div class="product-content">
                                            <div class="product-title">${capitalizeWords($Producto)}</div>
                                            <div class="product-description">${$Descripcion.toLowerCase()}</div>
                                            <div class="product-price">S/. ${$preventa}</div>
                                        </div>
                                    </button>
                                </td>
                                `;
                        } else {
                            fila += '<td style=" text-align: center;"><button class="btntipo3" disabled id="btnidp' + $ind +
                                '" onclick="agregar(' + $ind + ');" value="' + $idProducto + '_' + $stock + '_' + $preventa +
                                '" style="text-align:center;" value="1" type="button">' + $Producto + '</button></td>';
                        }

                        fila += '</tr>';
                        $("#detalles").append(fila);
                        $con = 0;
                        $fin = $fin + 1;

                    }

                    $ind = $ind + 1;

                }
                <?php endforeach ?>

                if ($con == 1 || $con == 2) {
                    fila += '</tr>';
                    $("#detalles").append(fila);
                    $con = 0;
                    $fin = $fin + 1;
                }

            }

            function capitalizeWords(texto) {

                if (!texto) return '';

                const excepciones = [
                    'ml',
                    'kg',
                    'gr',
                    'lt',
                    'l',
                    'cm',
                    'mm',
                    'hz',
                    'gb',
                    'tb',
                    'mb',
                    'usb',
                    'hdmi',
                    'tv',
                    'led',
                    'lcd',
                    'smart',
                    'wifi',
                    'bluetooth',
                    'cpu',
                    'gpu',
                    'ssd',
                    'hdd',
                    'rgb',
                    'ios',
                    'android',
                    'pc',
                    'ps4',
                    'ps5',
                    'xbox'
                ];

                return texto
                    .toLowerCase()
                    .split(' ')
                    .map(palabra => {

                        const limpia = palabra.replace(/[^a-z0-9]/gi, '');

                        if (excepciones.includes(limpia)) {

                            switch (limpia) {

                                case 'usb':
                                case 'hdmi':
                                case 'tv':
                                case 'led':
                                case 'lcd':
                                case 'wifi':
                                case 'cpu':
                                case 'gpu':
                                case 'ssd':
                                case 'hdd':
                                case 'rgb':
                                case 'ios':
                                case 'pc':
                                    return limpia.toUpperCase();

                                case 'ps4':
                                case 'ps5':
                                case 'xbox':
                                    return limpia.toUpperCase();

                                case 'android':
                                case 'smart':
                                case 'bluetooth':
                                    return limpia.charAt(0).toUpperCase() + limpia.slice(1);

                                default:
                                    return limpia;
                            }
                        }

                        return palabra.charAt(0).toUpperCase() + palabra.slice(1);

                    })
                    .join(' ');
            }

            function aceptar() {
                $('#VentaTipo').val('LIBRE');
                var formulario = document.getElementById("venta_form");
                $.ajax({
                    data: $('#venta_form').serialize(),
                    url: "{{ route('tenant.ventas.venta.store', tenant('id')) }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Swal.fire({
                            icon: "success",
                            title: "Venta Generada",
                            text: "Se realizo el pago correctamente!",
                            confirmButtonText: "Aceptar",
                            footer: '<a title="TICKET" target="_blank"  href="/gestion/venta/' + data[0]
                                .ventagenerado.VEN_Id +
                                '/ticket" class="btn btn-danger btn-sm" style="margin-left: 5px;"><i class="fa fa fa-print"></i> ¿Desea imprimir documento?</a>',
                            allowOutsideClick: false, // Deshabilita clics fuera del alert
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            } else if (result.isDenied) {
                                // Swal.fire("Changes are not saved", "", "info");
                            }
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Venta fallo al Registrarse.'
                        })
                    }
                });
            }

            function PagoCalculadora() {
                $CALCULADORA = "SI"
                $NUME = 0
                $DECI = 0
                $ACTD = "NO"
            }


            function mostrarformulario() {
                if ($ACTCLI == "NO") {
                    $("#idfrmCliente").show();
                    $ACTCLI = "SI"
                } else {
                    $("#idfrmCliente").hide();
                    $ACTCLI = "NO"
                }
            }

            function buscarCliente() {
                if ($('#idCLI_TipoDocumento').val() == 'DNI') {
                    var numdni = $('#idCLI_NumDocumento').val();
                    if (numdni != '') {
                        ocultar()
                        var url = '/consultardni/' + numdni + '?';
                        $.ajax({
                            type: 'GET',
                            url: url,
                            success: function(dat) {
                                if (dat.success[1] == false) {
                                    Swal
                                        .fire({
                                            title: "DNI Inválido",
                                            icon: 'error',
                                            confirmButtonColor: "#26BA9A",
                                            width: '350px',
                                            confirmButtonText: "Ok"
                                        })
                                        .then(resultado => {
                                            if (resultado.value) {
                                                $("#idCLI_Nombre").val("");
                                            } else {}
                                        });
                                } else {
                                    $('#idCLI_Nombre').val(dat.success[0].apellido + ' ' + dat.success[0].nombre);
                                }
                            }

                        });
                    } else {
                        //mostrar()
                        alert('Escriba el DNI.!');
                        $('#idCLI_NumDocumento').focus();
                    }
                } else if ($('#idCLI_TipoDocumento').val() == 'RUC') {
                    var numdni = $('#idCLI_NumDocumento').val();
                    if (numdni != '') {
                        ocultar()
                        var url = '/consultarruc/' + numdni + '?';
                        $.ajax({
                            type: 'GET',
                            url: url,
                            success: function(dat) {
                                console.log(dat)
                                if (dat.success[0] == "") {
                                    $('#idCLI_Nombre').val(dat.success[1]);
                                    $('#idCLI_Direccion').val(dat.success[2] + ' ' + dat.success[3]);
                                } else {
                                    $('#idCLI_Nombre').val(dat.success[0].apellido + ' ' + dat.success[0].nombre);
                                }

                            }

                        });
                    } else {
                        alert('Escriba el RUC.!');
                        $('#idCLI_NumDocumento').focus();
                    }
                }
            }

            function onSelectCliente() {
                const selectedValue = $('#idCliente').val(); // Obtiene el ID del cliente
                const selectedLabel = $('#idCliente option:selected').text();

                document.getElementById("selectedCliente").textContent = selectedLabel;
                document.querySelector("#hiddenSelectedIdCliente").value = selectedValue;
                $('#modalSelectCliente').modal('hide');
            }

            function vaciarCampos() {
                $('#cliente_form').trigger("reset");
                $("#cliente_id_edit").val('');
                $('#modalSelectCliente').modal('hide');
                $("#saveBtn").show();
            }

            function showCliente() {
                $('#modalSelectCliente').modal('show');
            }

            function Limitar() {
                var cod = document.getElementById("idCLI_TipoDocumento").value;
                if (cod == 'DNI') {
                    $("#idCLI_NumDocumento").val("");
                    $("#idCLI_NumDocumento").attr('maxlength', '8');
                } else {
                    $("#idCLI_NumDocumento").val("");
                    $("#idCLI_NumDocumento").attr('maxlength', '11');
                }
            }

            function ocultar() {
                document.getElementById('Buscar_Cliente').style.display = 'none';
                document.getElementById('cargando').style.display = 'block';
                setInterval('mostrar()', 1000);
            }

            function mostrar() {
                $valorcito = $('#idCLI_Nombre').val();
                $valor = $valorcito.length;
                if ($valor > 0) {
                    document.getElementById('Buscar_Cliente').style.display = 'block';
                    document.getElementById('cargando').style.display = 'none';
                }
            }

            function posicionamiento(index) {
                $POS = index;
                $("#detallesInformacion tbody").html('');
                var filainfo = '<tr  ><td><label style="font-size: 11px;"  hidden ">' + ListPedido[$POS][1] +
                    '</label></td><td><label style="font-size: 11px;"  hidden>' + ListPedido[$POS][6] + '</label></td></tr>'
                $("#detallesInformacion").append(filainfo);


                limpiar_numeros();
            }

            function Metodo(text) {
                $MET = text;
                limpiar_numeros();
            }

            function limpiar_numeros() {
                $NUME = 0
                $DECI = 0
                $ACTD = "NO"
                $CALCULADORA = "NO"
            }

            function elimi() {
                if ($DECI > 0) {
                    $DECI = parseInt($DECI / 10)
                    Editar("ELIMINAR")
                } else {
                    $NUME = parseInt($NUME / 10)
                    $ACTD = "NO"
                    Editar("ELIMINAR")
                }


            }

            function Editar(num) {


                if (num == "." || $ACTD == "SI") {
                    if ($ACTD == "SI") {
                        if (num == "ELIMINAR") {
                            num = $NUME + "." + $DECI
                        } else {
                            $DECI = parseFloat($DECI * 10) + parseFloat(num)
                            num = $NUME + "." + $DECI
                        }


                    } else {
                        num = $NUME + ".0"
                    }


                    $ACTD = "SI"
                } else if (num == "ELIMINAR") {
                    num = $NUME
                } else {
                    $NUME = parseFloat($NUME * 10) + parseFloat(num)
                    num = $NUME
                }


                if ($CALCULADORA == "NO") {
                    //alert(num)
                    if ($MET == "CANTIDAD") {
                        $('#cantidad' + $POS).val(num);
                        document.querySelector('#cantidadLabel' + $POS).innerText = num;
                    } else if ($MET == "PRECIO") {
                        $('#precioUnit' + $POS).val(num);
                        document.querySelector('#precioUnitLabel' + $POS).innerText = num;
                    } else if ($MET == "DESCUENTO") {
                        $('#descuento' + $POS).val(num);
                        document.querySelector('#descuentoLabel' + $POS).innerText = " - Descuento S/ " + num;
                    }

                    var cantid = $('#cantidad' + $POS).val();
                    var pvent = $('#precioUnit' + $POS).val();
                    var descuento = $('#descuento' + $POS).val();


                    if (cantid.length >= 0 && pvent >= 0.00 && descuento >= 0.00 && descuento.length > 0 && cantid.length > 0 &&
                        pvent.length > 0) {

                        ListPedido[$POS][2] = pvent;
                        ListPedido[$POS][3] = cantid;
                        ListPedido[$POS][5] = descuento;

                        subtot = (parseFloat(ListPedido[$POS][3] * ListPedido[$POS][2]) - parseFloat(ListPedido[$POS][5]))
                            .toFixed(1);

                        //alert(descuento);
                        ListPedido[$POS][4] = subtot;
                        $('#subTot' + $POS).val(subtot);
                        document.querySelector('#subTotLabel' + $POS).innerText = subtot;



                    } else {
                        imp = (0).toFixed(1);
                    }

                    total1 = 0;
                    for (var i = 0; i < ListPedido.length; i++) {
                        total1 = (parseFloat(total1) + parseFloat(ListPedido[i][4])).toFixed(1);
                        $("#total").html("S/. " + total1);
                        $("#total").val(total1);

                    }
                    ValidarVuelto();
                } else {
                    $totalpagar = $("#total").val();
                    $("#idPagado").val(num);
                    $vuelto = (num - $totalpagar).toFixed(1)
                    $("#idVuelto").val($vuelto);

                }
            }

            function ValidarVuelto() {
                $totalpagar = $("#total").val();
                $pagado = $("#idPagado").val();
                if ($pagado.length > 0 && $pagado > 0) {
                    $vuelto = ($pagado - $totalpagar).toFixed(1)
                    $("#idVuelto").val($vuelto);
                }

            }

            function habilitar() {
                $('#familia').attr('disabled', false);
                $('#btnCancelar').attr('disabled', false);
                $('#btnAgregaImagen').attr('disabled', false);
            }

            function Deshabilitar() {
                $('#familia').attr('disabled', true);
                $('#btnCancelar').attr('disabled', true);
                $('#btnAgregaImagen').attr('disabled', true);
            }

            function vaciar() {
                for (var i = ListPedido.length - 1; i >= 0; i--) {
                    total = 0;
                    $("#total").html("S/. " + total);
                    $("#total").val(total);
                    $('#fila' + i).remove();
                    ListPedido.splice(i, 1);
                }
                $POS = 0
            }

            function eliminar() {
                $('#fila' + $POS).remove();
                ListPedido.splice($POS, 1);
                $POS = $POS - 1
                total1 = 0;
                for (var i = 0; i < ListPedido.length; i++) {
                    total1 = (parseFloat(total1) + parseFloat(ListPedido[i][4])).toFixed(1);
                }
                $("#total").html("S/. " + total1);
                $("#total").val(total1);
                $pagado = $("#idPagado").val();
                $vuelto = ($pagado - total1).toFixed(1)
                $("#idVuelto").val($vuelto);

            }
        </script>
    @endpush
@endsection
