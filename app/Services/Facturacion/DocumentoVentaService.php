<?php

namespace App\Services\Facturacion;

use App\Models\Tenant\Almacen;
use App\Models\Tenant\DocumentoVenta;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Crea el documento_venta de un comprobante electronico.
 *
 * SUNAT exige que cada serie lleve su propia numeracion correlativa: la boleta
 * B001 y la factura F001 no comparten contador, y un correlativo repetido se
 * rechaza por duplicado. Por eso el numero se calcula por (tipo, serie) y no a
 * partir del correlativo de la proforma.
 */
class DocumentoVentaService
{
    /** Comprobante que llega del punto de venta => tipo interno. */
    const TIPOS = [
        'BOLETA'  => EmpresaFacturacion::TIPO_BOLETA,
        'FACTURA' => EmpresaFacturacion::TIPO_FACTURA,
    ];

    public function esComprobanteElectronico(?string $comprobante): bool
    {
        return isset(self::TIPOS[strtoupper((string) $comprobante)]);
    }

    /**
     * Tipo interno (BOL / FAC) del comprobante elegido en el punto de venta.
     */
    public function tipoDe(string $comprobante): string
    {
        return self::TIPOS[strtoupper($comprobante)]
            ?? throw new RuntimeException("Comprobante no soportado: $comprobante");
    }

    /**
     * Regla unica sobre el cliente de un comprobante electronico.
     *
     * Se aplica al registrar la venta, para no consumir un correlativo en un
     * documento que SUNAT nunca va a aceptar, y otra vez antes de enviar, por
     * si el cliente cambio entre ambos momentos.
     *
     * @param object $cliente fila con CLI_TipoDocumento, CLI_NumDocumento y CLI_Nombre
     */
    public static function validarCliente(string $tipo, $cliente): void
    {
        if ($tipo !== EmpresaFacturacion::TIPO_FACTURA) {
            return;
        }

        $tipoDoc = strtoupper(trim((string) ($cliente->CLI_TipoDocumento ?? '')));
        $numero  = trim((string) ($cliente->CLI_NumDocumento ?? ''));
        $nombre  = $cliente->CLI_Nombre ?? 'sin nombre';

        if ($tipoDoc !== 'RUC') {
            throw new RuntimeException(
                'Para emitir una factura el cliente debe tener RUC; "' . $nombre .
                '" tiene ' . ($tipoDoc ?: 'sin documento') . '. Elige otro cliente o emite una boleta.'
            );
        }

        if (strlen($numero) !== 11) {
            throw new RuntimeException(
                'El RUC de "' . $nombre . '" debe tener 11 digitos y tiene ' . strlen($numero) . '.'
            );
        }
    }

    /**
     * Crea el documento de venta para una boleta o factura.
     *
     * Debe llamarse dentro de una transaccion: toma un bloqueo sobre las filas
     * de la serie para que dos cajas no puedan reservar el mismo correlativo.
     */
    public function crear(int $ventaId, string $comprobante, EmpresaFacturacion $empresa): DocumentoVenta
    {
        $tipo = $this->tipoDe($comprobante);

        $cliente = DB::table('venta as v')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->where('v.VEN_Id', $ventaId)
            ->select('c.CLI_TipoDocumento', 'c.CLI_NumDocumento', 'c.CLI_Nombre')
            ->first();

        self::validarCliente($tipo, $cliente);

        // La serie sale de la sede donde se hizo la venta: para SUNAT cada
        // establecimiento anexo lleva su propia numeracion.
        $sede = Almacen::find(
            DB::table('venta')->where('VEN_Id', $ventaId)->value('ALM_Id')
        );

        if (!$sede) {
            throw new RuntimeException("La venta $ventaId no tiene una sede asociada.");
        }

        $serie = $sede->seriePara($tipo);

        if (!$serie) {
            throw new RuntimeException(
                'La sede "' . $sede->ALM_NombreAlmacen . '" no tiene configurada la serie de ' .
                ($tipo === EmpresaFacturacion::TIPO_FACTURA ? 'Factura' : 'Boleta') . '.'
            );
        }

        $numero = $this->siguienteCorrelativo($tipo, $serie);

        $documento = new DocumentoVenta;
        $documento->DOV_Tipo         = $tipo;
        $documento->DOV_TipoOriginal = $tipo;
        $documento->DOV_Serie        = $serie;
        $documento->DOV_Numero       = $numero;
        $documento->DOV_Nombre       = $serie . '-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT);
        $documento->VEN_Id           = $ventaId;
        $documento->DOV_Estado       = 'PENDIENTE';
        $documento->DOV_StateToRes   = 0;
        $documento->DOV_Pdf          = $this->codigoPublicoUnico();
        $documento->DOV_Vista        = 1;
        $documento->save();

        return $documento;
    }


    /**
     * Catalogo 09 de SUNAT: motivos validos para una nota de credito.
     * Se valida aqui tambien (ademas de en la API) para fallar rapido con un
     * mensaje claro en vez de gastar el viaje a SUNAT.
     */
    const MOTIVOS_NOTA_CREDITO = [
        '01' => 'Anulacion de la operacion',
        '02' => 'Anulacion por error en el RUC',
        '03' => 'Correccion por error en la descripcion',
        '04' => 'Descuento global',
        '05' => 'Descuento por item',
        '06' => 'Devolucion total',
        '07' => 'Devolucion por item',
        '08' => 'Bonificacion',
        '09' => 'Disminucion en el valor',
    ];

    /**
     * Crea el documento_venta de una nota de credito que afecta una boleta o
     * factura ya aceptada por SUNAT.
     *
     * A diferencia de crear(), no recibe el VEN_Id de una venta existente:
     * la nota es su propio "documento" y necesita su propia venta+detalle
     * (los items que se acreditan), que el llamador ya debe haber creado.
     *
     * Debe llamarse dentro de una transaccion: toma un bloqueo sobre las
     * filas de la serie para que dos notas no reserven el mismo correlativo.
     */
    public function crearNotaCredito(
        int $ventaNotaId,
        DocumentoVenta $documentoAfectado,
        Almacen $sede,
        string $codMotivo,
        string $desMotivo
    ): DocumentoVenta {
        if (!isset(self::MOTIVOS_NOTA_CREDITO[$codMotivo])) {
            throw new RuntimeException(
                "Motivo de nota de credito no valido: '$codMotivo'. Debe ser uno del 01 al 09."
            );
        }

        if (!in_array($documentoAfectado->DOV_Tipo, [EmpresaFacturacion::TIPO_BOLETA, EmpresaFacturacion::TIPO_FACTURA], true)) {
            throw new RuntimeException('Solo se pueden emitir notas de credito sobre una boleta o una factura.');
        }

        if (!in_array($documentoAfectado->DOV_Estado, ['ACEPTADO', 'OBSERVADO'], true)) {
            throw new RuntimeException(
                'El comprobante ' . $documentoAfectado->DOV_Nombre . ' aun no fue aceptado por SUNAT; ' .
                'no se le puede emitir una nota de credito.'
            );
        }

        $serie = $sede->serieNotaCreditoPara($documentoAfectado->DOV_Tipo);

        if (!$serie) {
            $origenLabel = $documentoAfectado->DOV_Tipo === EmpresaFacturacion::TIPO_FACTURA ? 'factura' : 'boleta';
            throw new RuntimeException(
                'La sede "' . $sede->ALM_NombreAlmacen . '" no tiene configurada la serie de nota de credito para ' . $origenLabel . '.'
            );
        }

        $tipo   = EmpresaFacturacion::TIPO_NOTA_CREDITO;
        $numero = $this->siguienteCorrelativo($tipo, $serie);

        $tipoDocAfectado = $documentoAfectado->DOV_Tipo === EmpresaFacturacion::TIPO_FACTURA ? '01' : '03';
        $numDocAfectado  = $documentoAfectado->DOV_Serie . '-' . $documentoAfectado->DOV_Numero;

        $documento = new DocumentoVenta;
        $documento->DOV_Tipo             = $tipo;
        $documento->DOV_TipoOriginal     = $tipo;
        $documento->DOV_CodMotivo        = $codMotivo;
        $documento->DOV_DesMotivo        = $desMotivo;
        $documento->DOV_TipoDocAfectado  = $tipoDocAfectado;
        $documento->DOV_NumDocAfectado   = $numDocAfectado;
        $documento->DOV_DocAfectadoId    = $documentoAfectado->DOV_Id;
        $documento->DOV_Serie            = $serie;
        $documento->DOV_Numero           = $numero;
        $documento->DOV_Nombre           = $serie . '-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT);
        $documento->VEN_Id               = $ventaNotaId;
        $documento->DOV_Estado           = 'PENDIENTE';
        $documento->DOV_StateToRes       = 0;
        $documento->DOV_Pdf              = $this->codigoPublicoUnico();
        $documento->DOV_Vista            = 1;
        $documento->save();

        return $documento;
    }

    /**
     * Siguiente numero libre de la serie, bloqueando las filas existentes para
     * que dos ventas simultaneas no obtengan el mismo.
     */
    private function siguienteCorrelativo(string $tipo, string $serie): int
    {
        $ultimo = DB::table('documento_venta')
            ->where('DOV_Tipo', $tipo)
            ->where('DOV_Serie', $serie)
            ->lockForUpdate()
            ->max('DOV_Numero');

        return ((int) $ultimo) + 1;
    }

    /**
     * Codigo aleatorio con el que se expone el comprobante hacia afuera,
     * para no publicar el id incremental.
     */
    private function codigoPublicoUnico(): string
    {
        $alfabeto = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        do {
            $codigo = '';
            for ($i = 0; $i < 20; $i++) {
                $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
        } while (DocumentoVenta::where('DOV_Pdf', $codigo)->exists());

        return $codigo;
    }
}
