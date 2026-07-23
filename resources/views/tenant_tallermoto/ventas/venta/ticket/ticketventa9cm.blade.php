<style>
    *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#fff;
    font-family:'Segoe UI', sans-serif;
    color:#000;
}

.ticket{
    width:9cm;
    margin:auto;
    padding:12px;
}

.logo-container{
    text-align:center;
}

.logo{
    width:90px;
    margin:auto;
    margin-bottom:8px;
}

.empresa{
    font-size:24px;
    font-weight:800;
}

.ruc{
    font-size:15px;
    font-weight:700;
    margin-top:4px;
}

.direccion{
    font-size:13px;
    margin-top:2px;
}

.documento{
    margin-top:14px;
    border:2px dashed #000;
    padding:10px;
    text-align:center;
    font-weight:800;
    font-size:18px;
    border-radius:10px;
}

.fecha{
    margin-top:10px;
    display:flex;
    justify-content:space-between;
    font-size:13px;
}

.linea{
    border-top:2px dashed #000;
    margin:12px 0;
}

.cliente{
    font-size:13px;
}

.row-ticket{
    display:flex;
    justify-content:space-between;
    gap:10px;
    margin-bottom:6px;
}

.productos{
    width:100%;
    border-collapse:collapse;
}

.productos thead{
    border-bottom:2px solid #000;
}

.productos th{
    padding:8px 2px;
    font-size:12px;
}

.productos td{
    padding:8px 2px;
    font-size:13px;
    vertical-align:top;
    text-align:center;
}

.producto-col{
    width:52%;
    text-align:left !important;
}

.producto-nombre{
    font-weight:700;
}

.producto-extra{
    font-size:11px;
    color:#666;
}

.totales{
    font-size:14px;
}

.total-box{
    margin-top:12px;
    background:#000;
    color:#fff;
    padding:12px;
    border-radius:12px;
    display:flex;
    justify-content:space-between;
    font-size:22px;
    font-weight:800;
}

.qr-section{
    text-align:center;
    margin-top:18px;
}

.qr{
    width:110px;
}

.footer{
    text-align:center;
    margin-top:18px;
    font-size:12px;
    line-height:1.6;
}

.gracias{
    margin-top:10px;
    font-size:20px;
    font-weight:800;
}

@media print{

    body{
        margin:0;
    }

    .ticket{
        width:9cm;
    }

}
</style>
<body onload="imprimir();">

    <div class="ticket">

        <!-- LOGO -->
        <div class="logo-container">
            @if($generaimagen)
                <img src="{{ public_path('/images/logo.png') }}" class="logo">
            @else
                <img src="{{ asset_root('/images/logo.png') }}" class="logo">
            @endif

            <div class="empresa">
                {{ $datosalmacen->razon_social }}
            </div>

            <div class="ruc">
                RUC: {{ $datosalmacen->ruc }}
            </div>

            <div class="direccion">
                {{ $datosalmacen->ALM_Direccion }}
            </div>

            <div class="direccion">
                LA LIBERTAD - PACASMAYO
            </div>

            <div class="direccion">
                CEL: {{ $datosalmacen->ALM_Celular }}
            </div>

        </div>

        <!-- DOCUMENTO -->
        <div class="documento">

            <?php
            if ($ventae->tipoDoc == 'BOL') {
                echo 'BOLETA ELECTRÓNICA';
            }
            
            if ($ventae->tipoDoc == 'FAC') {
                echo 'FACTURA ELECTRÓNICA';
            }
            ?>

            <br>

            {{ $UbiDoc }} - {{ $NumDoc }}

        </div>

        <!-- FECHA -->
        <div class="fecha">

            <div>
                FECHA:
                {{ $ventae->fechaVenta }}
            </div>

            <div>
                HORA:
                {{ $ventae->fechaVentaT }}
            </div>

        </div>

        <div class="linea"></div>

        <!-- CLIENTE -->
        <div class="cliente">

            <div class="row-ticket">
                <span>CLIENTE</span>
                <span>{{ $ventae->cliente }}</span>
            </div>

            <div class="row-ticket">
                <span>DOCUMENTO</span>
                <span>{{ $ventae->clienteNumero }}</span>
            </div>

            <div class="row-ticket">
                <span>PAGO</span>
                <span>
                    {{ $ventae->tipopago == '1' ? 'CONTADO' : 'CRÉDITO' }}
                </span>
            </div>

        </div>

        <div class="linea"></div>

        <!-- PRODUCTOS -->
        <table class="productos">

            <thead>

                <tr>
                    <th class="producto-col">
                        PRODUCTO
                    </th>

                    <th>
                        CANT
                    </th>

                    <th>
                        P/U
                    </th>

                    <th>
                        TOT
                    </th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($detallese as $de) { ?>

                <tr>

                    <td class="producto-col">

                        <div class="producto-nombre">
                            {{ $de->articulo }}
                        </div>

                        <div class="producto-extra">
                            {{ $de->categoria }}
                        </div>

                    </td>

                    <td>
                        {{ $de->cantidad }}
                    </td>

                    <td>
                        {{ number_format($de->precio_venta, 2) }}
                    </td>

                    <td>
                        {{ number_format($de->subtotal, 2) }}
                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

        <div class="linea"></div>

        <!-- TOTALES -->
        <div class="totales">

            <div class="row-ticket">
                <span>SUBTOTAL</span>
                <span>S/ {{ number_format($Subtotal, 2) }}</span>
            </div>

            <div class="row-ticket">
                <span>IGV</span>
                <span>S/ {{ number_format($igv, 2) }}</span>
            </div>

            <div class="row-ticket">
                <span>DESCUENTO</span>
                <span>S/ {{ number_format($ventae->total_descuento, 2) }}</span>
            </div>

        </div>

        <!-- TOTAL -->
        <div class="total-box">

            <span>TOTAL</span>

            <span>
                S/
                {{ number_format($ventae->total_venta - $ventae->total_descuento, 2) }}
            </span>

        </div>

        <!-- QR -->
        <div class="qr-section">

            {!! QrCode::size(120)->generate(
                'RUC: '.$datosalmacen->ruc.
                ' | DOC: '.$UbiDoc.'-'.$NumDoc.
                ' | TOTAL: S/'.$ventae->total_venta
            ) !!}

        </div>

        <!-- FOOTER -->
        <div class="footer">

            Representación impresa de comprobante electrónico

            <br><br>

            Consulte en:
            <br>

            www.sunat.gob.pe

            <br><br>

            CAJERO:
            {{ $ventae->EMP_Codigo }}

            <br><br>

            <div class="gracias">
                ¡GRACIAS POR SU COMPRA!
            </div>

        </div>

    </div>

</body>

<script>

    

    window.onload = function () {

        if(!window.location.href.includes('captura=1')){

            window.print();

        }

    }

</script>
