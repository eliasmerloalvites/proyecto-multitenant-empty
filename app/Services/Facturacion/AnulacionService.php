<?php

namespace App\Services\Facturacion;

use App\Models\Tenant\Almacen;
use App\Models\Tenant\DocumentoVenta;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Anula un comprobante que SUNAT ya acepto.
 *
 * A diferencia de una nota de credito, una baja no es un documento
 * tributario nuevo: es un aviso sobre un documento existente, y SUNAT lo
 * procesa en diferido (responde con un ticket que hay que consultar
 * despues). Ademas usa DOS mecanismos distintos segun el tipo:
 *
 *   - Boleta: resumen diario de baja (RC), un detalle con estado=3 (Anular).
 *   - Factura / nota de credito: comunicacion de baja (RA).
 *
 * El resultado de ambos vive en las columnas DOV_*Baja de documento_venta,
 * no en una venta nueva.
 */
class AnulacionService
{
    /** Tipo interno de documento => endpoint que lo anula. */
    const ENDPOINTS = [
        EmpresaFacturacion::TIPO_BOLETA       => '/api/resumen-boleta.php',
        EmpresaFacturacion::TIPO_FACTURA      => '/api/comunicacion-baja.php',
        EmpresaFacturacion::TIPO_NOTA_CREDITO => '/api/comunicacion-baja.php',
    ];

    /** Tipo interno => codigo SUNAT, tal como lo espera comunicacion-baja.php. */
    const TIPOS_SUNAT = [
        EmpresaFacturacion::TIPO_FACTURA      => '01',
        EmpresaFacturacion::TIPO_NOTA_CREDITO => '07',
    ];

    protected $empresa;

    public function __construct()
    {
        $this->empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
    }

    /**
     * Envia la solicitud de baja a SUNAT y guarda el ticket.
     *
     * Devuelve siempre un arreglo con 'success'; nunca lanza.
     */
    public function solicitarBaja(int $ventaId, string $motivo): array
    {
        try {
            $documento = $this->documento($ventaId);
            $this->verificarPuedeAnularse($documento);
            $this->verificarConfiguracion();

            $sede = $this->obtenerSede($documento);

            $payload = $documento->DOV_Tipo === EmpresaFacturacion::TIPO_BOLETA
                ? $this->armarResumenBoleta($documento, $sede, $motivo)
                : $this->armarComunicacionBaja($documento, $sede, $motivo);

            $respuesta = Http::timeout(120)->post(
                rtrim(config('facturacion.api_url'), '/') . self::ENDPOINTS[$documento->DOV_Tipo],
                $payload
            );

            $data = $respuesta->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'La API facturadora respondio algo que no es JSON (HTTP ' . $respuesta->status() . ').'
                );
            }

            if (empty($data['success']) || empty($data['ticket'])) {
                $documento->update([
                    'DOV_MotivoBaja'         => $motivo,
                    'DOV_EstadoBaja'         => 'ERROR',
                    'DOV_DescripcionBaja'    => $data['descripcion'] ?? 'SUNAT no devolvio un ticket.',
                    'DOV_FechaSolicitudBaja' => now(),
                ]);

                return ['success' => false, 'descripcion' => $documento->DOV_DescripcionBaja];
            }

            $documento->update([
                'DOV_TicketBaja'          => $data['ticket'],
                'DOV_MotivoBaja'          => $motivo,
                'DOV_EstadoBaja'          => 'PENDIENTE',
                'DOV_DescripcionBaja'     => 'Enviado a SUNAT; el resultado se sabe en unos minutos.',
                'DOV_FechaSolicitudBaja'  => now(),
                'DOV_FechaRespuestaBaja'  => null,
            ]);

            return ['success' => true, 'estado' => 'PENDIENTE', 'ticket' => $data['ticket']];
        } catch (\Throwable $e) {
            Log::error('Error solicitando la baja de la venta ' . $ventaId, [
                'tenant' => tenant('id'),
                'error'  => $e->getMessage(),
            ]);

            return ['success' => false, 'descripcion' => $e->getMessage()];
        }
    }

    /**
     * Pregunta a SUNAT el resultado de una baja ya solicitada.
     */
    public function consultarBaja(int $ventaId): array
    {
        try {
            $documento = $this->documento($ventaId);

            if (!$documento->DOV_TicketBaja) {
                throw new RuntimeException('Este comprobante no tiene una solicitud de baja en curso.');
            }

            $this->verificarConfiguracion();
            $sede = $this->obtenerSede($documento);

            $respuesta = Http::timeout(60)->post(
                rtrim(config('facturacion.api_url'), '/') . '/api/consulta-ticket.php',
                [
                    'ticket'      => $documento->DOV_TicketBaja,
                    'empresa'     => $this->datosEmpresa($sede),
                    'certificado' => base64_encode(file_get_contents($this->empresa->rutaCertificado())),
                    'clave_certificado'     => $this->empresa->certificado_password,
                    'extension_certificado' => $this->empresa->extensionCertificado(),
                ]
            );

            $data = $respuesta->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'La API facturadora respondio algo que no es JSON (HTTP ' . $respuesta->status() . ').'
                );
            }

            if (empty($data['success'])) {
                // SUNAT aun no tiene el resultado, o la consulta fallo; no se
                // pisa el estado PENDIENTE para poder reintentar despues.
                return [
                    'success'     => false,
                    'estado'      => $documento->DOV_EstadoBaja,
                    'descripcion' => $data['descripcion'] ?? 'SUNAT todavia no tiene el resultado.',
                ];
            }

            $aceptado = ($data['estado'] ?? null) === 'ACEPTADO';

            $documento->update([
                'DOV_EstadoBaja'         => $aceptado ? 'ACEPTADO' : 'RECHAZADO',
                'DOV_DescripcionBaja'    => $data['descripcion'] ?? null,
                'DOV_FechaRespuestaBaja' => now(),
                'DOV_Anulado'            => $aceptado,
            ]);

            return [
                'success'     => true,
                'estado'      => $documento->DOV_EstadoBaja,
                'descripcion' => $documento->DOV_DescripcionBaja,
            ];
        } catch (\Throwable $e) {
            Log::error('Error consultando la baja de la venta ' . $ventaId, [
                'tenant' => tenant('id'),
                'error'  => $e->getMessage(),
            ]);

            return ['success' => false, 'descripcion' => $e->getMessage()];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDACIONES
    |--------------------------------------------------------------------------
    */

    private function verificarPuedeAnularse(DocumentoVenta $documento): void
    {
        if (!isset(self::ENDPOINTS[$documento->DOV_Tipo])) {
            throw new RuntimeException('Este tipo de documento no se puede anular desde aqui.');
        }

        if ($documento->DOV_Anulado) {
            throw new RuntimeException('Este comprobante ya esta anulado.');
        }

        if ($documento->DOV_EstadoBaja === 'PENDIENTE') {
            throw new RuntimeException(
                'Ya hay una solicitud de baja en curso para este comprobante; consulta su resultado antes de pedir otra.'
            );
        }

        if (!in_array($documento->DOV_Estado, ['ACEPTADO', 'OBSERVADO'], true)) {
            throw new RuntimeException(
                'Solo se puede anular un comprobante que SUNAT ya acepto.'
            );
        }
    }

    private function verificarConfiguracion(): void
    {
        if (!$this->empresa) {
            throw new RuntimeException('Esta empresa no tiene datos de facturacion configurados.');
        }

        $problemas = $this->empresa->problemasDeConfiguracion();

        if ($problemas) {
            throw new RuntimeException(implode(' ', $problemas));
        }

        if (blank(config('facturacion.api_url'))) {
            throw new RuntimeException('Falta definir FACTURADOR_API en el archivo .env.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    private function documento(int $ventaId): DocumentoVenta
    {
        $documento = DocumentoVenta::where('VEN_Id', $ventaId)->first();

        if (!$documento) {
            throw new RuntimeException("La venta $ventaId no tiene un documento asociado.");
        }

        return $documento;
    }

    private function obtenerSede(DocumentoVenta $documento): Almacen
    {
        $almacenId = DB::table('venta')->where('VEN_Id', $documento->VEN_Id)->value('ALM_Id');
        $sede = $almacenId ? Almacen::find($almacenId) : null;

        if (!$sede) {
            throw new RuntimeException('El comprobante no tiene una sede asociada.');
        }

        return $sede;
    }

    private function datosEmpresa(Almacen $sede): array
    {
        $empresa = $this->empresa;

        return [
            'modo'             => $empresa->modoApi(),
            'ruc'              => $empresa->ruc,
            'razon_social'     => $empresa->razon_social,
            'nombre_comercial' => $empresa->nombre_comercial,
            'usuario_sol'      => $empresa->sol_usuario,
            'clave_sol'        => $empresa->sol_password,
            'ubigeo'           => $sede->ALM_Ubigeo,
            'departamento'     => $sede->ALM_Departamento,
            'provincia'        => $sede->ALM_Provincia,
            'distrito'         => $sede->ALM_Distrito,
            'direccion'        => $sede->ALM_Direccion,
            'cod_local'        => $sede->ALM_CodigoSunat ?: '0000',
        ];
    }

    /**
     * Payload del resumen diario que anula una boleta (RC, estado 3).
     */
    private function armarResumenBoleta(DocumentoVenta $documento, Almacen $sede, string $motivo): array
    {
        $venta = DB::table('venta as v')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->where('v.VEN_Id', $documento->VEN_Id)
            ->select('c.CLI_TipoDocumento', 'c.CLI_NumDocumento', 'v.VEN_FechaEnvio')
            ->first();

        $detalle = DB::table('detalle_venta as dv')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->where('dv.VEN_Id', $documento->VEN_Id)
            ->get();

        $items = app(SunatService::class)->mapearItems($detalle);

        $gravadas = $igv = $icbper = 0.0;
        foreach ($items as $item) {
            $gravadas += $item['valor_venta'];
            $igv      += $item['igv'];
        }

        return [
            'correlativo'               => (string) $this->siguienteCorrelativoBaja('RC'),
            'fecha_emision_documentos'  => \Illuminate\Support\Carbon::parse($venta->VEN_FechaEnvio)->toDateString(),
            'moneda'                    => $this->empresa->moneda ?: 'PEN',
            'boletas'                   => [[
                'serie'       => $documento->DOV_Serie,
                'correlativo' => (string) $documento->DOV_Numero,
                'cliente'     => [
                    'tipo_doc' => SunatService::tipoDocumentoSunat($venta->CLI_TipoDocumento),
                    'numero'   => $venta->CLI_NumDocumento,
                ],
                'mto_oper_gravadas' => round($gravadas, 2),
                'igv'               => round($igv, 2),
                'icbper'            => round($icbper, 2),
                'total'             => round($gravadas + $igv + $icbper, 2),
            ]],
            'empresa'                   => $this->datosEmpresa($sede),
            'certificado'               => base64_encode(file_get_contents($this->empresa->rutaCertificado())),
            'clave_certificado'         => $this->empresa->certificado_password,
            'extension_certificado'     => $this->empresa->extensionCertificado(),
        ];
    }

    /**
     * Payload de la comunicacion de baja (RA) para factura o nota de credito.
     */
    private function armarComunicacionBaja(DocumentoVenta $documento, Almacen $sede, string $motivo): array
    {
        $fechaEmision = DB::table('venta')->where('VEN_Id', $documento->VEN_Id)->value('VEN_FechaEnvio');

        return [
            'correlativo'               => (string) $this->siguienteCorrelativoBaja('RA'),
            'fecha_emision_documentos'  => \Illuminate\Support\Carbon::parse($fechaEmision)->toDateString(),
            'documentos'                => [[
                'tipo_doc'    => self::TIPOS_SUNAT[$documento->DOV_Tipo],
                'serie'       => $documento->DOV_Serie,
                'correlativo' => (string) $documento->DOV_Numero,
                'motivo'      => $motivo,
            ]],
            'empresa'                   => $this->datosEmpresa($sede),
            'certificado'               => base64_encode(file_get_contents($this->empresa->rutaCertificado())),
            'clave_certificado'         => $this->empresa->certificado_password,
            'extension_certificado'     => $this->empresa->extensionCertificado(),
        ];
    }

    /**
     * Numero de la comunicacion/resumen del dia (no del comprobante que se
     * anula). SUNAT exige que sea unico por RUC, tipo y dia.
     *
     * No es un contador atomico perfecto (cuenta filas en vez de llevar una
     * secuencia dedicada), pero la baja es una operacion poco frecuente y en
     * la practica no hay dos solicitudes simultaneas del mismo tipo.
     */
    private function siguienteCorrelativoBaja(string $categoria): int
    {
        $tipos = $categoria === 'RC'
            ? [EmpresaFacturacion::TIPO_BOLETA]
            : [EmpresaFacturacion::TIPO_FACTURA, EmpresaFacturacion::TIPO_NOTA_CREDITO];

        $cantidad = DB::table('documento_venta')
            ->whereIn('DOV_Tipo', $tipos)
            ->whereDate('DOV_FechaSolicitudBaja', now()->toDateString())
            ->lockForUpdate()
            ->count();

        return $cantidad + 1;
    }
}
