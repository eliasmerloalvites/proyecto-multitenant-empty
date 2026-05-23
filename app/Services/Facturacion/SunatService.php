<?php

namespace App\Services\Facturacion;

use App\Models\Tenant\EmpresaFacturacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SunatService
{
    protected $empresa;

    public function __construct()
    {
        $this->empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
    }


    public function enviarVenta($ventaId)
    {
        try {

            /* ========================= | OBTENER VENTA ==========================*/

            $venta = DB::table('documento_venta as dov')
                ->join('venta as v', 'v.VEN_Id', '=', 'dov.VEN_Id')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->where('v.VEN_Id', $ventaId)
                ->first();

            /* ========================= | DETALLE ==========================*/

            $detalle = DB::table('detalle_venta as dv')
                ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
                ->where('dv.VEN_Id', $ventaId)
                ->get();

            /* ========================= | ARMAR ITEMS ==========================*/
            $mtoOperGravadas = 0;
            $igvTotal = 0;
            $totalVenta = 0;
            $items = [];

            foreach ($detalle as $item) {

                $valorVenta = $item->DEV_PrecioUnitario / 1.18;
                $igv = $item->DEV_PrecioUnitario - $valorVenta;

                $valorVentaItem = round($valorVenta * $item->DEV_Cantidad,2);
                $igvItem = round($igv * $item->DEV_Cantidad, 2);
                $totalItem = round($item->DEV_PrecioUnitario * $item->DEV_Cantidad,2);

                $mtoOperGravadas += $valorVentaItem;
                $igvTotal += $igvItem;
                $totalVenta += $totalItem;

                $items[] = [
                    'codigo' => $item->PRO_Id,
                    'unidad' => 'NIU',
                    'cantidad' => $item->DEV_Cantidad,
                    'descripcion' => $item->PRO_Nombre,
                    'valor_unitario' => round($valorVenta, 2),
                    'precio_unitario' => round($item->DEV_PrecioUnitario, 2),
                    'base_igv' => round( $valorVenta * $item->DEV_Cantidad, 2),
                    'porcentaje_igv' => 18,
                    'igv' => round($igv * $item->DEV_Cantidad, 2),
                    'tipo_afectacion' => '10',
                    'total_impuestos' => round($igv * $item->DEV_Cantidad, 2),
                    'valor_venta' => round($valorVenta * $item->DEV_Cantidad, 2),
                ];
            }

            /* ========================= | CERTIFICADO ==========================*/
            $nombreCertificado = $this->empresa->certificado;
            $extensionCertificado = strtolower(pathinfo($nombreCertificado, PATHINFO_EXTENSION ));
            $rutaCertificado = storage_path('app/tenant/' .tenant('tipo_negocio') . '/' .
                    tenant('id') . '/sunat/certificado/' . $this->empresa->certificado);

            $certificadoBase64 = base64_encode(file_get_contents($rutaCertificado));

            /* ========================= | PAYLOAD ==========================*/
            // dd($venta);
            $payload = [
                'serie' => str_replace('P', 'B', $venta->DOV_Serie),
                'correlativo' => $venta->DOV_Numero,
                'fecha_emision' => $venta->VEN_FechaEnvio,
                'moneda' => 'PEN',
                'mto_oper_gravadas' => round($mtoOperGravadas, 2),
                'mto_oper_exoneradas' => 0,
                'igv' => round($igvTotal, 2),
                'total_impuestos' => round($igvTotal, 2),
                'valor_venta' => round($mtoOperGravadas, 2),
                'subtotal' => round($totalVenta, 2),
                'total' => round($totalVenta, 2),
                'icbper' => 0,

                /* EMPRESA */
                'empresa' => [
                    'modo' => 'BETA',
                    'ruc' => $this->empresa->ruc,
                    'razon_social' =>$this->empresa->razon_social,
                    'nombre_comercial' =>$this->empresa->nombre_comercial,
                    'usuario_sol' => $this->empresa->sol_usuario,
                    'clave_sol' =>$this->empresa->sol_password,
                    'ubigeo' => $this->empresa->ubigeo,
                    'departamento' => $this->empresa->departamento,
                    'provincia' => $this->empresa->provincia,
                    'distrito' => $this->empresa->distrito,
                    'direccion' => $this->empresa->direccion,
                ],

                /* CLIENTE */

                'cliente' => [
                    'tipo_doc' =>  $this->obtenerTipoDocumentoSunat($venta->CLI_TipoDocumento),
                    'numero' =>$venta->CLI_NumDocumento,
                    'nombre' => $venta->CLI_Nombre,
                    'direccion' => $venta->CLI_Direccion,
                ],

                /* ITEMS */
                'detalle' => $items,

                /* CERTIFICADO */
                'certificado' => $certificadoBase64,
                'clave_certificado' =>$this->empresa->certificado_password,
                'extension_certificado' => $extensionCertificado
            ];

            /* ========================= | API FACTURADOR ==========================*/
            $response = Http::post(
                env('FACTURADOR_API') .
                '/api/boleta.php',
                $payload
            );

            $data = $response->json();
            /* ========================= | GUARDAR XML ==========================*/

            if (!empty($data['xml_base64'])) {

                $rutaXml =
                    'tenant/' .
                    tenant('tipo_negocio') . '/' .
                    tenant('id') .
                    '/sunat/xml/' .
                    $data['xml_name'];

                Storage::put(
                    $rutaXml,
                    base64_decode(
                        $data['xml_base64']
                    )
                );
            }

            /* ========================= | GUARDAR CDR ==========================*/

            if (!empty($data['cdr_base64'])) {

                $rutaCdr =
                    'tenant/' .
                    tenant('tipo_negocio') . '/' .
                    tenant('id') .
                    '/sunat/cdr/' .
                    $data['cdr_name'];

                Storage::put(
                    $rutaCdr,
                    base64_decode(
                        $data['cdr_base64']
                    )
                );
            }

            /* =========================
            | ACTUALIZAR DOCUMENTO
            ==========================*/
            if (($data['success'] ?? false) == true) {
                if (($data['codigo'] ?? null) == '0') {
                    $estadoSistema = 'ACEPTADO';
                } else {
                    $estadoSistema = 'OBSERVADO';
                }
            } else {
                $estadoSistema = 'ERROR';
            }

            $responseSunat = $data;

            unset($responseSunat['xml_base64']);
            unset($responseSunat['cdr_base64']);


            DB::table('documento_venta')
                ->where('VEN_Id', $ventaId)
                ->update([
                    /* ESTADO INTERNO */
                    'DOV_Estado' => $estadoSistema,
                    /* RESPUESTA SUNAT */
                    'DOV_EstadoSunat' => $data['estado'] ?? null,
                    'DOV_CodigoSunat' => $data['codigo'] ?? null,
                    'DOV_DescripcionSunat' => $data['descripcion'] ?? null,
                    'DOV_ResponseSunat' => json_encode($responseSunat),
                    /* HASH */
                    'DOV_Hash' => $data['hash'] ?? null,
                    /* CONTROL SUNAT */
                    'DOV_IntentosSunat' => DB::raw('DOV_IntentosSunat + 1'),
                    /* FECHAS */
                    'DOV_FechaRespuestaSunat' => now(),
                    /* RESUMEN */
                    'DOV_StateToRes' => 3,
                    /* UPDATED */
                    'updated_at' => now(),
                ]);
            return $data;

        } catch (\Throwable $e) {
            Log::error($e);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }


    private function obtenerTipoDocumentoSunat($tipoDocumento)
    {
        $tipoDocumento = strtoupper(trim($tipoDocumento));

        return match ($tipoDocumento) {
            'DNI' => '1',
            'RUC' => '6',
            'CE', 'CARNET DE EXTRANJERIA' => '4',
            'PASAPORTE' => '7',
            default => '0', // Sin documento / otros
        };
    }

}
