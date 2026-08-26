<?php

namespace App\Services\Facturacion;

use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\GuiaRemision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Envia una guia de remision a la API GRE (plataforma nueva de SUNAT: REST
 * + OAuth2, no el SOAP clasico de factura/boleta). Es un servicio aparte de
 * SunatService porque esta atada a la tabla guia_remision, no a
 * documento_venta, y porque el flujo es asincrono por ticket.
 */
class GuiaRemisionSunatService
{
    const ENDPOINT_ENVIO = '/api/guia-remision.php';
    const ENDPOINT_CONSULTA = '/api/consulta-ticket-guia.php';

    protected ?EmpresaFacturacion $empresa;

    public function __construct()
    {
        $this->empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
    }

    /**
     * Envia la guia y guarda el resultado. Nunca lanza: siempre devuelve un
     * arreglo con 'success', para que el job pueda reintentar sin tumbarse.
     */
    public function enviarGuia(int $guiaId): array
    {
        try {
            $this->verificarConfiguracion();

            $guia = GuiaRemision::findOrFail($guiaId);
            $venta = $this->obtenerVentaConCliente($guia->VEN_Id);
            $sede = $this->obtenerSede($venta->ALM_Id);
            $items = $this->obtenerItems($guia->VEN_Id);

            $payload = $this->armarPayload($guia, $venta, $sede, $items);

            $this->marcarEnvio($guiaId);

            $respuesta = Http::timeout(120)->post(
                rtrim(config('facturacion.api_url'), '/') . self::ENDPOINT_ENVIO,
                $payload
            );

            $data = $respuesta->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'La API facturadora respondio algo que no es JSON (HTTP ' . $respuesta->status() . ').'
                );
            }

            $this->guardarArchivos($data);
            $this->guardarResultado($guiaId, $data);

            return $data;
        } catch (\Throwable $e) {
            Log::error('Error enviando la guia de remision ' . $guiaId . ' a SUNAT', [
                'tenant' => tenant('id'),
                'error'  => $e->getMessage(),
            ]);

            $this->guardarFallo($guiaId, $e->getMessage());

            return [
                'success'     => false,
                'estado'      => 'ERROR',
                'descripcion' => $e->getMessage(),
            ];
        }
    }

    /**
     * Simula una respuesta ACEPTADA de SUNAT, sin llamar a la API GRE real.
     *
     * Solo para probar el flujo (registro + documento impreso) mientras no
     * hay client_id/client_secret configurados o mientras se evita gastar
     * un envio real. Bloqueado por completo si la empresa esta en modo
     * produccion: no existe forma de que esto se cuele como un envio real.
     */
    public function simularAceptado(int $guiaId): array
    {
        if (!$this->empresa || $this->empresa->esProduccion()) {
            throw new RuntimeException(
                'La simulacion solo esta disponible en ambiente BETA, para no confundirla nunca con un envio real.'
            );
        }

        $guia = GuiaRemision::findOrFail($guiaId);
        $nombre = $guia->GRM_Nombre;

        $xmlSimulado = '<?xml version="1.0" encoding="UTF-8"?>' .
            "\n<!-- XML SIMULADO: no fue firmado ni enviado a SUNAT, solo para pruebas. -->" .
            "\n<DespatchAdvice><cbc:ID>{$nombre}</cbc:ID></DespatchAdvice>";

        $data = [
            'success'     => true,
            'estado'      => 'ACEPTADO',
            'ticket'      => 'SIMULADO-' . now()->timestamp,
            'codigo'      => '0',
            'descripcion' => 'Aceptada (SIMULADO - no se envio a SUNAT)',
            'xml_name'    => $nombre . '.xml',
            'xml_base64'  => base64_encode($xmlSimulado),
        ];

        $this->guardarArchivos($data);
        $this->guardarResultado($guiaId, $data);

        return $data;
    }

    /**
     * Consulta un ticket cuando el envio quedo en PENDIENTE (no se resolvio
     * dentro de los reintentos cortos del propio guia-remision.php).
     */
    public function consultarTicket(int $guiaId): array
    {
        try {
            $this->verificarConfiguracion();

            $guia = GuiaRemision::findOrFail($guiaId);

            if (!$guia->GRM_Ticket) {
                throw new RuntimeException('Esta guia no tiene un ticket pendiente de consulta.');
            }

            $payload = [
                'empresa' => $this->bloqueEmpresa($this->obtenerSede(
                    DB::table('venta')->where('VEN_Id', $guia->VEN_Id)->value('ALM_Id')
                )),
                'ticket' => $guia->GRM_Ticket,
            ];

            $respuesta = Http::timeout(60)->post(
                rtrim(config('facturacion.api_url'), '/') . self::ENDPOINT_CONSULTA,
                $payload
            );

            $data = $respuesta->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'La API facturadora respondio algo que no es JSON (HTTP ' . $respuesta->status() . ').'
                );
            }

            if (($data['estado'] ?? null) !== 'PENDIENTE') {
                $this->guardarArchivos($data);
                $this->guardarResultado($guiaId, $data);
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('Error consultando el ticket de la guia de remision ' . $guiaId, [
                'tenant' => tenant('id'),
                'error'  => $e->getMessage(),
            ]);

            return [
                'success'     => false,
                'estado'      => 'ERROR',
                'descripcion' => $e->getMessage(),
            ];
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

        if (blank($this->empresa->gre_client_id) || blank($this->empresa->gre_client_secret)) {
            throw new RuntimeException(
                'Falta configurar el client_id/client_secret de la API GRE (guia de remision) en los datos de facturacion de la empresa.'
            );
        }

        if (blank(config('facturacion.api_url'))) {
            throw new RuntimeException('Falta definir FACTURADOR_API en el archivo .env.');
        }
    }

    private function obtenerVentaConCliente(int $ventaId)
    {
        $venta = DB::table('venta as v')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->where('v.VEN_Id', $ventaId)
            ->select('v.*', 'c.CLI_TipoDocumento', 'c.CLI_NumDocumento', 'c.CLI_Nombre', 'c.CLI_Direccion')
            ->first();

        if (!$venta) {
            throw new RuntimeException("La venta $ventaId no existe.");
        }

        return $venta;
    }

    private function obtenerItems(int $ventaId): array
    {
        $detalle = DB::table('detalle_venta as dv')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->where('dv.VEN_Id', $ventaId)
            ->get();

        if ($detalle->isEmpty()) {
            throw new RuntimeException("La venta $ventaId no tiene productos.");
        }

        return $detalle->map(fn ($item) => [
            'codigo'      => $item->PRO_Id,
            'descripcion' => $item->PRO_Nombre,
            'unidad'      => 'NIU',
            'cantidad'    => (float) $item->DEV_Cantidad,
        ])->all();
    }

    private function obtenerSede($almacenId): Almacen
    {
        $sede = Almacen::find($almacenId);

        if (!$sede) {
            throw new RuntimeException('La venta no tiene una sede asociada.');
        }

        $problemas = $sede->problemasDeConfiguracion();

        if ($problemas) {
            throw new RuntimeException(implode(' ', $problemas));
        }

        return $sede;
    }

    private function bloqueEmpresa(Almacen $sede): array
    {
        $empresa = $this->empresa;

        return [
            'modo'             => $empresa->modoApi(),
            'ruc'              => $empresa->ruc,
            'razon_social'     => $empresa->razon_social,
            'nombre_comercial' => $empresa->nombre_comercial,
            'usuario_sol'      => $empresa->sol_usuario,
            'clave_sol'        => $empresa->sol_password,
            'client_id'        => $empresa->gre_client_id,
            'client_secret'    => $empresa->gre_client_secret,
            'ubigeo'           => $sede->ALM_Ubigeo,
            'departamento'     => $sede->ALM_Departamento,
            'provincia'        => $sede->ALM_Provincia,
            'distrito'         => $sede->ALM_Distrito,
            'direccion'        => $sede->ALM_Direccion,
            'cod_local'        => $sede->ALM_CodigoSunat ?: '0000',
        ];
    }

    private function armarPayload(GuiaRemision $guia, $venta, Almacen $sede, array $items): array
    {
        $empresa = $this->empresa;

        $payload = [
            'serie'               => $guia->GRM_Serie,
            'correlativo'         => (string) $guia->GRM_Numero,
            'fecha_emision'       => $guia->created_at->toDateString(),
            'fecha_traslado'      => $guia->GRM_FechaTraslado->toDateString(),
            'motivo_traslado'     => $guia->GRM_MotivoTraslado,
            'des_motivo_traslado' => $guia->GRM_DesMotivo,
            'peso_total'          => (float) $guia->GRM_PesoTotal,
            'und_peso'            => $guia->GRM_UndPeso,
            'modo_transporte'     => $guia->GRM_ModoTransporte,

            'empresa' => $this->bloqueEmpresa($sede),

            'destinatario' => [
                'tipo_doc' => SunatService::tipoDocumentoSunat($venta->CLI_TipoDocumento),
                'numero'   => $venta->CLI_NumDocumento,
                'nombre'   => $venta->CLI_Nombre,
            ],

            'punto_partida' => [
                'ubigeo'    => $guia->GRM_UbigeoPartida,
                'direccion' => $guia->GRM_DireccionPartida,
            ],
            'punto_llegada' => [
                'ubigeo'    => $guia->GRM_UbigeoLlegada,
                'direccion' => $guia->GRM_DireccionLlegada,
            ],

            'detalle' => $items,

            'certificado'           => base64_encode(file_get_contents($empresa->rutaCertificado())),
            'clave_certificado'     => $empresa->certificado_password,
            'extension_certificado' => $empresa->extensionCertificado(),
        ];

        if ($guia->GRM_ModoTransporte === GuiaRemision::MODO_PUBLICO) {
            $payload['transportista'] = [
                'tipo_doc'     => $guia->GRM_TransportistaTipoDoc,
                'numero'       => $guia->GRM_TransportistaNumero,
                'razon_social' => $guia->GRM_TransportistaRazonSocial,
            ];
        } else {
            $payload['vehiculo'] = ['placa' => $guia->GRM_VehiculoPlaca];
            $payload['conductor'] = [
                'tipo_doc'   => $guia->GRM_ConductorTipoDoc,
                'numero'     => $guia->GRM_ConductorNumero,
                'nombres'    => $guia->GRM_ConductorNombres,
                'apellidos'  => $guia->GRM_ConductorApellidos,
                'licencia'   => $guia->GRM_ConductorLicencia,
            ];
        }

        return $payload;
    }

    private function guardarArchivos(array $data): void
    {
        $base = 'tenant/' . tenant('tipo_negocio') . '/' . tenant('id') . '/sunat/';

        if (!empty($data['xml_base64']) && !empty($data['xml_name'])) {
            Storage::put($base . 'xml/' . $data['xml_name'], base64_decode($data['xml_base64']));
        }

        if (!empty($data['cdr_base64']) && !empty($data['cdr_name'])) {
            Storage::put($base . 'cdr/' . $data['cdr_name'], base64_decode($data['cdr_base64']));
        }
    }

    private function marcarEnvio(int $guiaId): void
    {
        DB::table('guia_remision')
            ->where('GRM_Id', $guiaId)
            ->update([
                'GRM_FechaEnvioSunat' => now(),
                'GRM_IntentosSunat'   => DB::raw('GRM_IntentosSunat + 1'),
                'updated_at'          => now(),
            ]);
    }

    private function guardarResultado(int $guiaId, array $data): void
    {
        $estadoRespuesta = (string) ($data['estado'] ?? '');

        if ($estadoRespuesta === 'PENDIENTE') {
            // Todavia sin resolver: se guarda el ticket para consultarlo despues.
            DB::table('guia_remision')
                ->where('GRM_Id', $guiaId)
                ->update([
                    'GRM_Estado'      => 'PENDIENTE',
                    'GRM_EstadoSunat' => 'PENDIENTE',
                    'GRM_Ticket'      => $data['ticket'] ?? null,
                    'updated_at'      => now(),
                ]);

            return;
        }

        // La API responde HTTP 200 tambien cuando SUNAT rechaza: el estado
        // real esta en 'success', no en el codigo HTTP.
        if (!($data['success'] ?? false)) {
            $estado = 'RECHAZADO';
        } else {
            $estado = 'ACEPTADO';
        }

        $respuesta = $data;
        unset($respuesta['xml_base64'], $respuesta['cdr_base64'], $respuesta['certificado']);

        DB::table('guia_remision')
            ->where('GRM_Id', $guiaId)
            ->update([
                'GRM_Estado'              => $estado,
                'GRM_EstadoSunat'         => $data['estado'] ?? null,
                'GRM_Ticket'              => $data['ticket'] ?? null,
                'GRM_CodigoSunat'         => $data['codigo'] ?? null,
                'GRM_DescripcionSunat'    => $data['descripcion'] ?? null,
                'GRM_ResponseSunat'       => json_encode($respuesta),
                'GRM_FechaRespuestaSunat' => now(),
                'updated_at'              => now(),
            ]);
    }

    private function guardarFallo(int $guiaId, string $mensaje): void
    {
        try {
            DB::table('guia_remision')
                ->where('GRM_Id', $guiaId)
                ->update([
                    'GRM_Estado'              => 'ERROR',
                    'GRM_DescripcionSunat'    => $mensaje,
                    'GRM_FechaRespuestaSunat' => now(),
                    'updated_at'              => now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('No se pudo registrar el fallo del envio de la guia', ['error' => $e->getMessage()]);
        }
    }
}
