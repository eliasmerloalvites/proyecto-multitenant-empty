<?php

namespace App\Services\Facturacion;

use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SunatService
{
    /** Tipo interno de documento => endpoint de la API facturadora. */
    const ENDPOINTS = [
        EmpresaFacturacion::TIPO_BOLETA       => '/api/boleta.php',
        EmpresaFacturacion::TIPO_FACTURA      => '/api/factura.php',
        EmpresaFacturacion::TIPO_NOTA_CREDITO => '/api/nota-credito.php',
    ];

    protected $empresa;

    public function __construct()
    {
        $this->empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
    }

    /**
     * Envia a SUNAT el comprobante de una venta y guarda el resultado.
     *
     * Devuelve siempre un arreglo con 'success'; nunca lanza, para que el job
     * pueda registrar el fallo y reintentar sin tumbar la venta.
     */
    public function enviarVenta($ventaId)
    {
        try {
            $this->verificarConfiguracion();

            $venta = $this->obtenerVenta($ventaId);
            $tipo  = $venta->DOV_Tipo;

            if (!isset(self::ENDPOINTS[$tipo])) {
                throw new RuntimeException(
                    "El documento de la venta $ventaId es de tipo '$tipo' y no se envia a SUNAT."
                );
            }

            $this->verificarCliente($tipo, $venta);

            $sede = $this->obtenerSede($venta->ALM_Id);

            $payload = $this->armarPayload($venta, $sede, $this->obtenerItems($ventaId));

            $this->marcarEnvio($ventaId);

            $respuesta = Http::timeout(120)->post(
                rtrim(config('facturacion.api_url'), '/') . self::ENDPOINTS[$tipo],
                $payload
            );

            $data = $respuesta->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'La API facturadora respondio algo que no es JSON (HTTP ' . $respuesta->status() . ').'
                );
            }

            $this->guardarArchivos($data);
            $this->guardarResultado($ventaId, $data);

            return $data;
        } catch (\Throwable $e) {
            Log::error('Error enviando la venta ' . $ventaId . ' a SUNAT', [
                'tenant' => tenant('id'),
                'error'  => $e->getMessage(),
            ]);

            $this->guardarFallo($ventaId, $e->getMessage());

            return [
                'success'     => false,
                'estado'      => 'ERROR',
                'descripcion' => $e->getMessage(),
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDACIONES PREVIAS
    |--------------------------------------------------------------------------
    */

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

    /**
     * SUNAT rechaza una factura cuyo cliente no tenga RUC. Se detecta aqui para
     * no gastar el viaje y poder decirle al usuario que corrija el cliente.
     */
    private function verificarCliente(string $tipo, $venta): void
    {
        DocumentoVentaService::validarCliente($tipo, $venta);
    }

    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    private function obtenerVenta($ventaId)
    {
        $venta = DB::table('documento_venta as dov')
            ->join('venta as v', 'v.VEN_Id', '=', 'dov.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->where('v.VEN_Id', $ventaId)
            ->first();

        if (!$venta) {
            throw new RuntimeException("La venta $ventaId no tiene un documento de venta asociado.");
        }

        return $venta;
    }

    private function obtenerItems($ventaId): array
    {
        $detalle = DB::table('detalle_venta as dv')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->where('dv.VEN_Id', $ventaId)
            ->get();

        if ($detalle->isEmpty()) {
            throw new RuntimeException("La venta $ventaId no tiene productos.");
        }

        return $this->mapearItems($detalle);
    }

    /**
     * Traduce las lineas del detalle de venta al formato de la API.
     *
     * @param iterable $detalle filas con DEV_Cantidad, DEV_PrecioUnitario,
     *                          DEV_Descuento, PRO_Id y PRO_Nombre
     */
    public function mapearItems(iterable $detalle): array
    {
        $items = [];
        $porcentajeIgv = 18;

        foreach ($detalle as $item) {
            $cantidad = (float) $item->DEV_Cantidad;
            $precio   = round((float) $item->DEV_PrecioUnitario, 2);

            // El importe que cobra la caja es la verdad: de ahi se saca todo lo
            // demas. El IGV se calcula como la diferencia contra el valor de
            // venta, no como un porcentaje del valor de venta; si no, la resta
            // de los redondeos deja al XML un centimo por debajo del ticket.
            // DEV_PrecioUnitario ya viene neto (con el descuento del POS
            // aplicado), asi que valor_venta/igv salen correctos sin mas.
            $totalLinea = round($precio * $cantidad, 2);
            $valorVenta = round($totalLinea / (1 + $porcentajeIgv / 100), 2);
            $igv        = round($totalLinea - $valorVenta, 2);

            $fila = [
                'codigo'          => $item->PRO_Id,
                'descripcion'     => $item->PRO_Nombre,
                'unidad'          => 'NIU',
                'cantidad'        => $cantidad,
                'valor_unitario'  => $cantidad > 0 ? round($valorVenta / $cantidad, 10) : 0,
                'precio_unitario' => $precio,
                'valor_venta'     => $valorVenta,
                'base_igv'        => $valorVenta,
                'igv'             => $igv,
                'total_impuestos' => $igv,
                'tipo_afectacion' => '10',
                'porcentaje_igv'  => $porcentajeIgv,
            ];

            // Si el POS aplico un descuento en esta linea (DEV_Descuento, con
            // IGV incluido), se declara como AllowanceCharge de item: monto y
            // monto_base van sin IGV (son valor de venta, no precio de
            // venta). valor_venta de la linea ya quedo neto arriba; esto solo
            // deja evidencia de por que es menor a su precio de lista.
            $descuentoConIgv = round((float) ($item->DEV_Descuento ?? 0), 2);

            if ($descuentoConIgv > 0) {
                $totalLineaBruto = round($totalLinea + $descuentoConIgv, 2);
                $valorVentaBruto = round($totalLineaBruto / (1 + $porcentajeIgv / 100), 2);
                $descuentoValorVenta = round($valorVentaBruto - $valorVenta, 2);

                if ($descuentoValorVenta > 0) {
                    $fila['descuentos'] = [[
                        // Catalogo 53 SUNAT: '00' = descuento por item que SI
                        // afecta la base imponible del IGV (asi lo usa el
                        // ejemplo oficial de Greenter, InvoiceDiscountStore).
                        // '02' es para el descuento GLOBAL a nivel de
                        // documento, no de linea; usarlo aqui es lo que
                        // SUNAT rechazo como formato invalido.
                        'cod_tipo'   => '00',
                        'monto'      => $descuentoValorVenta,
                        'monto_base' => $valorVentaBruto,
                        // SUNAT valida el formato de este factor con pocos
                        // decimales; el ejemplo oficial usa 2 (ej. 0.30).
                        'factor'     => $valorVentaBruto > 0 ? round($descuentoValorVenta / $valorVentaBruto, 2) : 0,
                    ]];
                }
            }

            $items[] = $fila;
        }

        return $items;
    }

    /**
     * Sede desde la que se emitio la venta, ya validada.
     */
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

    private function armarPayload($venta, Almacen $sede, array $items): array
    {
        $empresa = $this->empresa;

        $payload = [
            'serie'         => $venta->DOV_Serie,
            'correlativo'   => (string) $venta->DOV_Numero,
            'fecha_emision' => $venta->VEN_FechaEnvio,
            'moneda'        => $empresa->moneda ?: 'PEN',

            // Identidad y credenciales son de la empresa; el domicilio y el
            // codigo de local son de la sede que emitio.
            'empresa' => [
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
            ],

            'cliente' => [
                'tipo_doc'  => self::tipoDocumentoSunat($venta->CLI_TipoDocumento),
                'numero'    => $venta->CLI_NumDocumento,
                'nombre'    => $venta->CLI_Nombre,
                'direccion' => $venta->CLI_Direccion,
            ],

            'detalle' => $items,

            'certificado'           => base64_encode(file_get_contents($empresa->rutaCertificado())),
            'clave_certificado'     => $empresa->certificado_password,
            'extension_certificado' => $empresa->extensionCertificado(),
        ];

        // La nota de credito necesita ademas el motivo y el documento que
        // afecta; esos datos viven en el propio documento_venta de la nota.
        if ($venta->DOV_Tipo === EmpresaFacturacion::TIPO_NOTA_CREDITO) {
            $payload['cod_motivo'] = $venta->DOV_CodMotivo;
            $payload['des_motivo'] = $venta->DOV_DesMotivo;
            $payload['documento_afectado'] = [
                'tipo_doc' => $venta->DOV_TipoDocAfectado,
                'numero'   => $venta->DOV_NumDocAfectado,
            ];
        }

        return $payload;
    }

    /*
    |--------------------------------------------------------------------------
    | RESULTADO
    |--------------------------------------------------------------------------
    */

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

    private function marcarEnvio($ventaId): void
    {
        DB::table('documento_venta')
            ->where('VEN_Id', $ventaId)
            ->update([
                'DOV_FechaEnvioSunat' => now(),
                'DOV_IntentosSunat'   => DB::raw('DOV_IntentosSunat + 1'),
                'updated_at'          => now(),
            ]);
    }

    private function guardarResultado($ventaId, array $data): void
    {
        // La API responde HTTP 200 tambien cuando SUNAT rechaza: el estado real
        // esta en 'success', no en el codigo HTTP.
        if (!($data['success'] ?? false)) {
            $estado = 'RECHAZADO';
        } elseif (!empty($data['notas'])) {
            $estado = 'OBSERVADO';
        } else {
            $estado = 'ACEPTADO';
        }

        $respuesta = $data;
        unset($respuesta['xml_base64'], $respuesta['cdr_base64'], $respuesta['certificado']);

        DB::table('documento_venta')
            ->where('VEN_Id', $ventaId)
            ->update([
                'DOV_Estado'              => $estado,
                'DOV_EstadoSunat'         => $data['estado'] ?? null,
                'DOV_CodigoSunat'         => $data['codigo'] ?? null,
                'DOV_DescripcionSunat'    => $data['descripcion'] ?? null,
                'DOV_ResponseSunat'       => json_encode($respuesta),
                'DOV_FechaRespuestaSunat' => now(),
                'DOV_StateToRes'          => 3,
                'updated_at'              => now(),
            ]);
    }

    private function guardarFallo($ventaId, string $mensaje): void
    {
        try {
            DB::table('documento_venta')
                ->where('VEN_Id', $ventaId)
                ->update([
                    'DOV_Estado'              => 'ERROR',
                    'DOV_DescripcionSunat'    => $mensaje,
                    'DOV_FechaRespuestaSunat' => now(),
                    'updated_at'              => now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('No se pudo registrar el fallo del envio', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Convierte DNI/RUC/CE/PASAPORTE (como se guardan en cliente) al codigo
     * de SUNAT. Publico y estatico porque tambien lo usa AnulacionService.
     */
    public static function tipoDocumentoSunat($tipoDocumento): string
    {
        return match (strtoupper(trim((string) $tipoDocumento))) {
            'DNI' => '1',
            'RUC' => '6',
            'CE', 'CARNET DE EXTRANJERIA' => '4',
            'PASAPORTE' => '7',
            default => '0',
        };
    }
}
