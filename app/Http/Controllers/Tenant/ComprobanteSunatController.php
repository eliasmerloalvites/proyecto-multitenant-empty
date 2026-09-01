<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarVentaSunatJob;
use App\Models\Tenant\EmpresaFacturacion;
use App\Services\Facturacion\SunatService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Acciones de SUNAT sobre un comprobante ya emitido: descargar su XML o su
 * CDR, consultar su estado en SUNAT y reintentar el envio.
 */
class ComprobanteSunatController extends Controller
{
    /** Tipo interno => codigo de SUNAT. */
    const TIPOS_SUNAT = [
        EmpresaFacturacion::TIPO_BOLETA  => '03',
        EmpresaFacturacion::TIPO_FACTURA => '01',
    ];

    /**
     * Estados en los que SUNAT ya recibio y acepto el comprobante.
     *
     * OBSERVADO cuenta: SUNAT lo acepto y solo dejo observaciones, asi que
     * volver a enviarlo lo duplicaria igual que si estuviera ACEPTADO.
     */
    const ESTADOS_EN_SUNAT = ['ACEPTADO', 'OBSERVADO'];

    /**
     * Codigo con el que SUNAT indica que el comprobante no esta registrado.
     * Se deja documentado, pero ya no se usa para decidir si se reenvia: un
     * codigo distinto a este NO prueba que SUNAT lo tenga (ver ESTADOS_CONFIRMADOS).
     */
    const CODIGO_NO_REGISTRADO = '0004';

    /**
     * Unicos estados de la consulta que prueban que SUNAT ya tiene el
     * comprobante registrado. Cualquier otra respuesta (codigo ambiguo,
     * sin 'estado', error del servicio de consulta, etc.) NO es prueba de
     * nada y no debe bloquear ni falsear el reenvio real del comprobante.
     */
    const ESTADOS_CONFIRMADOS = ['ACEPTADO', 'OBSERVADO', 'RECHAZADO'];

    /**
     * Descarga el XML firmado que se envio a SUNAT.
     */
    public function xml($ventaId)
    {
        return $this->descargar($ventaId, 'xml', 'xml_name', 'application/xml');
    }

    /**
     * Descarga el CDR (la constancia que devuelve SUNAT), comprimido en zip.
     */
    public function cdr($ventaId)
    {
        return $this->descargar($ventaId, 'cdr', 'cdr_name', 'application/zip');
    }

    /**
     * Consulta en SUNAT el estado real del comprobante.
     */
    public function consultar($ventaId)
    {
        try {
            $documento = $this->documento($ventaId);
            $empresa   = $this->empresa();

            $tipoSunat = self::TIPOS_SUNAT[$documento->DOV_Tipo] ?? null;

            if (!$tipoSunat) {
                throw new RuntimeException('Este documento no es un comprobante electronico.');
            }

            if (!$empresa->esProduccion()) {
                throw new RuntimeException(
                    'SUNAT solo permite consultar comprobantes en produccion. ' .
                    'Mientras el ambiente sea de pruebas, el estado que ves es el que devolvio el envio.'
                );
            }

            $respuesta = Http::timeout(60)->post(
                rtrim(config('facturacion.api_url'), '/') . '/api/consulta-comprobante.php',
                [
                    'tipo_doc'    => $tipoSunat,
                    'serie'       => $documento->DOV_Serie,
                    'correlativo' => $documento->DOV_Numero,
                    'empresa'     => [
                        'modo'        => $empresa->modoApi(),
                        'ruc'         => $empresa->ruc,
                        'usuario_sol' => $empresa->sol_usuario,
                        'clave_sol'   => $empresa->sol_password,
                    ],
                ]
            );

            $data = $respuesta->json();

            if (!is_array($data)) {
                throw new RuntimeException('La API facturadora no respondio JSON.');
            }

            return response()->json([
                'success'     => $data['success'] ?? false,
                'codigo'      => $data['codigo'] ?? null,
                'descripcion' => $data['descripcion'] ?? 'Sin respuesta de SUNAT.',
                'estado'      => $data['estado'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success'     => false,
                'descripcion' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reintenta el envio de un comprobante que no llego a SUNAT.
     */
    public function reenviar($ventaId)
    {
        try {
            $documento = $this->documento($ventaId);

            if (in_array($documento->DOV_Estado, self::ESTADOS_EN_SUNAT, true)) {
                throw new RuntimeException(
                    'SUNAT ya recibio este comprobante (' . $documento->DOV_Estado .
                    '); reenviarlo lo duplicaria.'
                );
            }

            if (!isset(self::TIPOS_SUNAT[$documento->DOV_Tipo])) {
                throw new RuntimeException('Este documento no se envia a SUNAT.');
            }

            // Antes de reenviar se le pregunta a SUNAT si ya lo tiene. El caso
            // que esto evita: el envio llego pero la respuesta se perdio, el
            // documento quedo en ERROR y reenviarlo lo duplicaria.
            $yaRegistrado = $this->yaEstaEnSunat($documento);

            if ($yaRegistrado !== null) {
                $this->marcarComoRegistrado($ventaId, $yaRegistrado);

                return response()->json([
                    'success'     => false,
                    'ya_en_sunat' => true,
                    'descripcion' => 'SUNAT ya tiene este comprobante, no se reenvio. Respuesta de SUNAT: ' .
                        $yaRegistrado['descripcion'],
                ], 409);
            }

            EnviarVentaSunatJob::dispatch($ventaId, tenant('id'), tenant('tipo_negocio'));

            // Con la cola en modo sincrono el envio ya termino y el estado
            // esta actualizado; con worker, queda en camino.
            $actualizado = $this->documento($ventaId);

            return response()->json([
                'success'     => true,
                'estado'      => $actualizado->DOV_Estado,
                'descripcion' => $actualizado->DOV_DescripcionSunat ?: 'Envio encolado.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success'     => false,
                'descripcion' => $e->getMessage(),
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNOS
    |--------------------------------------------------------------------------
    */

    /**
     * Pregunta a SUNAT si el comprobante ya esta registrado.
     *
     * Devuelve null cuando se puede reenviar sin riesgo, o los datos de la
     * respuesta de SUNAT cuando el comprobante ya existe alla.
     *
     * En pruebas SUNAT no ofrece el servicio de consulta, asi que no hay nada
     * que verificar y se permite el reenvio: alli un duplicado no tiene efecto.
     */
    private function yaEstaEnSunat($documento): ?array
    {
        $empresa = $this->empresa();

        if (!$empresa->esProduccion()) {
            return null;
        }

        try {
            $respuesta = Http::timeout(60)->post(
                rtrim(config('facturacion.api_url'), '/') . '/api/consulta-comprobante.php',
                [
                    'tipo_doc'    => self::TIPOS_SUNAT[$documento->DOV_Tipo],
                    'serie'       => $documento->DOV_Serie,
                    'correlativo' => $documento->DOV_Numero,
                    'empresa'     => [
                        'modo'        => $empresa->modoApi(),
                        'ruc'         => $empresa->ruc,
                        'usuario_sol' => $empresa->sol_usuario,
                        'clave_sol'   => $empresa->sol_password,
                    ],
                ]
            );

            $data = $respuesta->json();
        } catch (\Throwable $e) {
            // Si no se puede consultar, se deja pasar el reenvio: bloquearlo
            // dejaria al usuario sin salida cuando SUNAT esta caido.
            return null;
        }

        if (!is_array($data) || empty($data['success'])) {
            return null;
        }

        $estado = $data['estado'] ?? null;

        // Solo un 'estado' reconocido prueba que SUNAT ya tiene el
        // comprobante. Cualquier otra respuesta (codigo ambiguo, sin
        // 'estado', consulta caida, etc.) se deja pasar para que el
        // reenvio real se intente en vez de darlo por aceptado a ciegas.
        if (!in_array($estado, self::ESTADOS_CONFIRMADOS, true)) {
            return null;
        }

        return [
            'codigo'      => (string) ($data['codigo'] ?? ''),
            'descripcion' => $data['descripcion'] ?? 'sin detalle',
            'estado'      => $estado,
        ];
    }

    /**
     * Alinea el documento local con lo que SUNAT dice tener, para que la lista
     * deje de ofrecer el reenvio.
     */
    private function marcarComoRegistrado($ventaId, array $sunat): void
    {
        // $sunat['estado'] siempre viene de ESTADOS_CONFIRMADOS (ver
        // yaEstaEnSunat), nunca vacio: no hay fallback a 'ACEPTADO' a ciegas.
        DB::table('documento_venta')
            ->where('VEN_Id', $ventaId)
            ->update([
                'DOV_Estado'              => $sunat['estado'],
                'DOV_CodigoSunat'         => $sunat['codigo'],
                'DOV_DescripcionSunat'    => $sunat['descripcion'],
                'DOV_FechaRespuestaSunat' => now(),
                'updated_at'              => now(),
            ]);
    }

    private function documento($ventaId)
    {
        $documento = DB::table('documento_venta')->where('VEN_Id', $ventaId)->first();

        if (!$documento) {
            throw new RuntimeException("La venta $ventaId no tiene documento asociado.");
        }

        return $documento;
    }

    private function empresa(): EmpresaFacturacion
    {
        $empresa = EmpresaFacturacion::delTenantActual();

        if (!$empresa) {
            throw new RuntimeException('Esta empresa no tiene datos de facturacion.');
        }

        return $empresa;
    }

    /**
     * Entrega el XML o el CDR guardados al enviar el comprobante.
     */
    private function descargar($ventaId, string $carpeta, string $clave, string $mime)
    {
        try {
            $documento = $this->documento($ventaId);

            $respuesta = json_decode($documento->DOV_ResponseSunat ?? '', true);
            $nombre = $respuesta[$clave] ?? null;

            if (!$nombre) {
                throw new RuntimeException(
                    'Este comprobante todavia no tiene ' . strtoupper($carpeta) .
                    '; probablemente aun no se envio a SUNAT.'
                );
            }

            $ruta = 'tenant/' . tenant('tipo_negocio') . '/' . tenant('id') . '/sunat/' . $carpeta . '/' . $nombre;

            if (!Storage::exists($ruta)) {
                throw new RuntimeException(
                    'El archivo ' . $nombre . ' no esta en el servidor. Reenvia el comprobante para regenerarlo.'
                );
            }

            return response(Storage::get($ruta), 200, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'attachment; filename="' . $nombre . '"',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success'     => false,
                'descripcion' => $e->getMessage(),
            ], 422);
        }
    }
}
