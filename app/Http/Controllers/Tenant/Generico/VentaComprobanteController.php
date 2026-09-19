<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\VentaController;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;

/**
 * Version corregida, exclusiva de generico, de VentaController::ticket()/
 * pdf()/ticketImagen()/ticketWhatsapp().
 *
 * Esos 4 metodos del controlador COMPARTIDO consultan una tabla que no
 * existe, 'cuentas_por_cobrar' (con 's' y columnas 'CPC_*'), un resto de
 * codigo de antes de que existiera el modulo real de Cuentas por Cobrar
 * ('cuenta_cobrar', columnas 'CXC_*'). Para cualquier venta al credito eso
 * revienta con "Base table or view not found" al pedir el ticket, el PDF o
 * la imagen para WhatsApp.
 *
 * Como el controlador compartido no se puede tocar (afecta tallermoto), se
 * duplica aqui la misma logica exacta, cambiando solo esa consulta rota por
 * una lectura correcta de 'cuenta_cobrar'. $datosdecuenta no se usa en
 * ninguna de las 2 vistas de ticket (ticket_A4.blade.php,
 * ticketventa9cm.blade.php son compartidas y se siguen usando tal cual), asi
 * que no hace falta que los nombres de columnas coincidan exactamente con
 * los viejos 'CPC_*': alcanza con que la variable no truene.
 */
class VentaComprobanteController extends Controller
{
    /**
     * Reemplazo de la consulta rota a 'cuentas_por_cobrar' por una lectura
     * real de 'cuenta_cobrar'. Devuelve 0 si la venta no es al credito o no
     * tiene (por algun motivo) una cuenta asociada, igual que el original.
     */
    private static function datosCuentaCobrar(string $idventa, $calificarventa)
    {
        if (!$calificarventa || $calificarventa->VEN_TipoPago != 2) {
            return 0;
        }

        return DB::table('cuenta_cobrar as cc')
            ->select(
                'cc.CXC_FrecuenciaDias as CPC_Frecuencia',
                DB::raw('date(cc.CXC_FechaEmision) AS FECHAEMISION'),
                'cc.CXC_MontoAbonado as CPC_MontoAbonado',
                'cc.CXC_MontoPendiente as CPC_MontoFaltante',
                'cc.CXC_FechaVencimiento as CPC_FechaVencimiento'
            )
            ->where('cc.VEN_Id', '=', $idventa)
            ->first();
    }

    /**
     * Copia de VentaController::rutaTicketPdf() (privado ahi, no se puede
     * reusar desde afuera). Es solo una convencion de nombre de archivo, sin
     * logica de negocio: no hay riesgo de desincronizarla de tallermoto.
     */
    private static function rutaTicketPdf(string $tipoNegocio, ?string $tenantId, string $codigo): string
    {
        $path = public_path('storage/' . $tipoNegocio . '/' . $tenantId . '/archivos/tickets/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path . $codigo . '.pdf';
    }

    public function ticket(string $idventa)
    {
        $ventae = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->select('dov.DOV_Id as codigoDoc', 'dov.DOV_Nombre as nombre', 'dov.DOV_Pdf as pdf', 'v.VEN_Id as codigoVenta', 'dov.DOV_Tipo as tipoDoc', 'dov.DOV_Numero as numDoc', 'dov.DOV_Serie as serDoc', 'c.CLI_Nombre as cliente', 'c.CLI_Celular as celular', 'c.CLI_NumDocumento as clienteNumero', 'c.CLI_TipoDocumento as tipoDocumento', 'c.CLI_Direccion as clienteDireccion', 'a.ALM_Id', 'u.id as EMP_Codigo', 'u.name as empleado', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), 'dov.DOV_Estado as estadoDocVenta', DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'), 'v.VEN_TipoPago as tipopago', 'a.ALM_Id as ubica')
            ->where('v.VEN_Id', '=', $idventa)
            ->groupBy('dov.DOV_Id', 'dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'u.name', 'c.CLI_Nombre', 'v.VEN_Id', 'dov.DOV_Numero', 'dov.DOV_Estado', 'v.created_at', 'v.VEN_TipoPago', 'c.CLI_Celular', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'c.CLI_Direccion', 'a.ALM_Id', 'u.id', 'dov.DOV_Serie')
            ->distinct()
            ->first();

        if (!$ventae) {
            abort(404, 'La venta indicada no existe.');
        }

        $datosalmacen = DB::table('almacen as al')
            ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
            ->where('emp.tenant_id', tenant('id'))
            ->where('al.ALM_Id', '=', $ventae->ALM_Id)
            ->first();

        $Subtotal = ($ventae->total_venta) / 1.18;
        $igv = round($ventae->total_venta - $Subtotal, 2);
        $Subtotal = round($Subtotal, 2);

        $codi = $ventae->fechaVenta . "| " . $datosalmacen->ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
        $UbiDoc = $ventae->serDoc;
        $numDocu = $ventae->numDoc;

        $detallese = DB::table('detalle_venta as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Nombre as articulo', 'c.CAT_Nombre as categoria', DB::raw('SUM(d.DEV_Cantidad ) as cantidad'), DB::raw('(d.DEV_PrecioUnitario ) as precio_venta'), DB::raw('(d.DEV_Descuento ) as descuento'), DB::raw('CAST(SUM(d.DEV_Cantidad*(d.DEV_PrecioUnitario)) as decimal(10,2)) as subtotal'))
            ->where('d.VEN_Id', '=', $idventa)
            ->groupBy('p.PRO_Nombre', 'c.CAT_Nombre', 'd.DEV_PrecioUnitario', 'd.DEV_Descuento')
            ->get();

        $calificarventa = DB::table('venta as v')
            ->select('v.VEN_TipoPago')
            ->where('v.VEN_Id', '=', $idventa)
            ->first();

        $datosdecuenta = self::datosCuentaCobrar($idventa, $calificarventa);

        $NumDoc = VentaController::IndiceNumeroDocumentoVenta($numDocu);
        $Total = round($ventae->total_venta - $ventae->total_descuento, 2);
        $x = str_replace(',', '.', $Total);
        $LetrasTotal = VentaController::numletras($x);

        $generaimagen = false;

        return view('tenant_' . tenant('tipo_negocio') . '/ventas/venta/ticket/ticketventa9cm', compact('ventae', 'detallese', 'Subtotal', 'igv', 'codi', 'UbiDoc', 'NumDoc', 'datosalmacen', 'calificarventa', 'datosdecuenta', 'LetrasTotal', 'generaimagen'));
    }

    public function pdf(string $idventa)
    {
        $ventae = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->select('dov.DOV_Id as codigoDoc', 'dov.DOV_Nombre as nombre', 'dov.DOV_Pdf as pdf', 'v.VEN_Id as codigoVenta', 'dov.DOV_Tipo as tipoDoc', 'dov.DOV_Numero as numDoc', 'dov.DOV_Serie as serDoc', 'c.CLI_Nombre as cliente', 'c.CLI_Celular as celular', 'c.CLI_NumDocumento as clienteNumero', 'c.CLI_TipoDocumento as tipoDocumento', 'c.CLI_Direccion as clienteDireccion', 'a.ALM_Id', 'u.id as EMP_Codigo', 'u.name as empleado', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), 'dov.DOV_Estado as estadoDocVenta', DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'), 'v.VEN_TipoPago as tipopago', 'a.ALM_Id as ubica')
            ->where('v.VEN_Id', '=', $idventa)
            ->groupBy('dov.DOV_Id', 'dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'u.name', 'c.CLI_Nombre', 'v.VEN_Id', 'dov.DOV_Numero', 'dov.DOV_Estado', 'v.created_at', 'v.VEN_TipoPago', 'c.CLI_Celular', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'c.CLI_Direccion', 'a.ALM_Id', 'u.id', 'dov.DOV_Serie')
            ->distinct()
            ->first();

        if (!$ventae) {
            abort(404, 'La venta indicada no existe.');
        }

        $datosalmacen = DB::table('almacen as al')
            ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
            ->where('emp.tenant_id', tenant('id'))
            ->where('al.ALM_Id', '=', $ventae->ALM_Id)
            ->first();

        $Subtotal = ($ventae->total_venta) / 1.18;
        $igv = round($ventae->total_venta - $Subtotal, 2);
        $Subtotal = round($Subtotal, 2);

        $codi = $ventae->fechaVenta . "| " . $datosalmacen->ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
        $UbiDoc = $ventae->serDoc;
        $numDocu = $ventae->numDoc;

        $detallese = DB::table('detalle_venta as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Nombre as articulo', 'c.CAT_Nombre as categoria', DB::raw('SUM(d.DEV_Cantidad ) as cantidad'), DB::raw('(d.DEV_PrecioUnitario ) as precio_venta'), DB::raw('(d.DEV_Descuento ) as descuento'), DB::raw('CAST(SUM(d.DEV_Cantidad*(d.DEV_PrecioUnitario)) as decimal(10,2)) as subtotal'))
            ->where('d.VEN_Id', '=', $idventa)
            ->groupBy('p.PRO_Nombre', 'c.CAT_Nombre', 'd.DEV_PrecioUnitario', 'd.DEV_Descuento')
            ->get();

        $calificarventa = DB::table('venta as v')
            ->select('v.VEN_TipoPago')
            ->where('v.VEN_Id', '=', $idventa)
            ->first();

        $datosdecuenta = self::datosCuentaCobrar($idventa, $calificarventa);

        $NumDoc = VentaController::IndiceNumeroDocumentoVenta($numDocu);
        $Total = round($ventae->total_venta - $ventae->total_descuento, 2);
        $x = str_replace(',', '.', $Total);
        $LetrasTotal = VentaController::numletras($x);

        $generaimagen = false;

        return view('tenant_' . tenant('tipo_negocio') . '/ventas/venta/ticket/ticket_A4', compact('ventae', 'detallese', 'Subtotal', 'igv', 'codi', 'UbiDoc', 'NumDoc', 'datosalmacen', 'calificarventa', 'datosdecuenta', 'LetrasTotal', 'generaimagen'));
    }

    /**
     * Genera (si hace falta) la imagen/PDF del ticket para WhatsApp y
     * devuelve el enlace corto compartible. Copia de
     * VentaController::ticketImagen() con la misma correccion de
     * cuentas_por_cobrar -> cuenta_cobrar.
     */
    public static function ticketImagen($idventa)
    {
        $ventae = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->select('dov.DOV_Id as codigoDoc', 'dov.DOV_Nombre as nombre', 'dov.DOV_Pdf as pdf', 'v.VEN_Id as codigoVenta', 'dov.DOV_Tipo as tipoDoc', 'dov.DOV_Numero as numDoc', 'dov.DOV_Serie as serDoc', 'c.CLI_Nombre as cliente', 'c.CLI_Celular as celular', 'c.CLI_NumDocumento as clienteNumero', 'c.CLI_TipoDocumento as tipoDocumento', 'c.CLI_Direccion as clienteDireccion', 'a.ALM_Id', 'u.id as EMP_Codigo', 'u.name as empleado', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), 'dov.DOV_Estado as estadoDocVenta', DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'), 'v.VEN_TipoPago as tipopago', 'a.ALM_Id as ubica')
            ->where('v.VEN_Id', '=', $idventa)
            ->groupBy('dov.DOV_Id', 'dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'u.name', 'c.CLI_Nombre', 'v.VEN_Id', 'dov.DOV_Numero', 'dov.DOV_Estado', 'v.created_at', 'v.VEN_TipoPago', 'c.CLI_Celular', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'c.CLI_Direccion', 'a.ALM_Id', 'u.id', 'dov.DOV_Serie')
            ->distinct()
            ->first();

        if (!$ventae) {
            abort(404, 'La venta indicada no existe.');
        }

        $datosalmacen = DB::table('almacen as al')
            ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
            ->where('emp.tenant_id', tenant('id'))
            ->where('al.ALM_Id', '=', $ventae->ALM_Id)
            ->first();

        $Subtotal = ($ventae->total_venta) / 1.18;
        $igv = round($ventae->total_venta - $Subtotal, 2);
        $Subtotal = round($Subtotal, 2);

        $codi = $ventae->fechaVenta . "| " . $datosalmacen->ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
        $UbiDoc = $ventae->serDoc;
        $numDocu = $ventae->numDoc;

        $detallese = DB::table('detalle_venta as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Nombre as articulo', 'c.CAT_Nombre as categoria', DB::raw('SUM(d.DEV_Cantidad ) as cantidad'), DB::raw('(d.DEV_PrecioUnitario ) as precio_venta'), DB::raw('(d.DEV_Descuento ) as descuento'), DB::raw('CAST(SUM(d.DEV_Cantidad*(d.DEV_PrecioUnitario)) as decimal(10,2)) as subtotal'))
            ->where('d.VEN_Id', '=', $idventa)
            ->groupBy('p.PRO_Nombre', 'c.CAT_Nombre', 'd.DEV_PrecioUnitario', 'd.DEV_Descuento')
            ->get();

        $calificarventa = DB::table('venta as v')
            ->select('v.VEN_TipoPago')
            ->where('v.VEN_Id', '=', $idventa)
            ->first();

        $datosdecuenta = self::datosCuentaCobrar($idventa, $calificarventa);

        $NumDoc = VentaController::IndiceNumeroDocumentoVenta($numDocu);
        $Total = round($ventae->total_venta - $ventae->total_descuento, 2);
        $x = str_replace(',', '.', $Total);
        $LetrasTotal = VentaController::numletras($x);

        $generaimagen = true;

        $html = view(
            'tenant_generico/ventas/venta/ticket/ticket_A4',
            compact(
                'ventae',
                'detallese',
                'Subtotal',
                'igv',
                'codi',
                'UbiDoc',
                'NumDoc',
                'datosalmacen',
                'calificarventa',
                'datosdecuenta',
                'LetrasTotal',
                'generaimagen'
            )
        )->render();

        $ubicacionNegocio = "";
        $id = null;
        if (tenant()) {
            $id = tenant('id');
            $ubicacionNegocio = tenant('tipo_negocio');
        }

        $rutaCompleta = self::rutaTicketPdf($ubicacionNegocio, $id, $ventae->pdf);

        if (!is_file($rutaCompleta)) {
            $browsershot = Browsershot::html($html)
                ->timeout(120)
                ->format('A4')
                ->showBackground()
                ->noSandbox()
                ->setNodeEnv(['HOME' => sys_get_temp_dir()]);

            if ($chromePath = env('PUPPETEER_EXECUTABLE_PATH')) {
                $browsershot->setChromePath($chromePath);
            }

            $browsershot->save($rutaCompleta);
        }

        return route('tenant.ventas.venta.compartir', $ventae->pdf);
    }

    public function ticketWhatsapp(string $idventa)
    {
        try {
            $url = self::ticketImagen($idventa);

            $cliente = DB::table('venta as v')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
                ->where('v.VEN_Id', $idventa)
                ->select('c.CLI_Nombre', 'c.CLI_Celular', 'dov.DOV_Serie', 'dov.DOV_Numero')
                ->first();

            return response()->json([
                'success'   => true,
                'url'       => $url,
                'documento' => $cliente ? $cliente->DOV_Serie . '-' . $cliente->DOV_Numero : '',
                'celular'   => $cliente->CLI_Celular ?? '',
                'cliente'   => $cliente->CLI_Nombre ?? '',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success'     => false,
                'descripcion' => 'No se pudo generar la imagen del ticket: ' . $e->getMessage(),
            ], 422);
        }
    }
}
