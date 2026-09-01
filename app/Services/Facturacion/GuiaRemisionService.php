<?php

namespace App\Services\Facturacion;

use App\Models\Tenant\Almacen;
use App\Models\Tenant\DocumentoVenta;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\GuiaRemision;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Arma y numera una guia de remision a partir de una venta ya facturada.
 * Mismo espiritu que DocumentoVentaService::crearNotaCredito(): reserva su
 * propia serie/correlativo (con lockForUpdate) tomando la serie configurada
 * en la sede de la venta.
 */
class GuiaRemisionService
{
    /**
     * @param int $ventaId
     * @param array $datos Campos de traslado ya validados por el controlador:
     *   fecha_traslado, peso_total, und_peso, ubigeo_partida, direccion_partida,
     *   ubigeo_llegada, direccion_llegada, modo_transporte, y segun el modo:
     *   transportista_tipo_doc/numero/razon_social  o
     *   vehiculo_placa + conductor_tipo_doc/numero/nombres/apellidos/licencia.
     */
    public function crear(int $ventaId, array $datos): GuiaRemision
    {
        $venta = DB::table('venta')->where('VEN_Id', $ventaId)->first();

        if (!$venta) {
            throw new RuntimeException("La venta $ventaId no existe.");
        }

        $documento = DocumentoVenta::where('VEN_Id', $ventaId)->first();

        if (!$documento || !in_array($documento->DOV_Estado, ['ACEPTADO', 'OBSERVADO'], true)) {
            throw new RuntimeException(
                'La venta debe tener un comprobante (boleta/factura) aceptado por SUNAT antes de emitir su guia de remision.'
            );
        }

        $sede = Almacen::find($venta->ALM_Id);

        if (!$sede) {
            throw new RuntimeException("La venta $ventaId no tiene una sede asociada.");
        }

        $serie = $sede->seriePara(EmpresaFacturacion::TIPO_GUIA_REMISION);

        if (!$serie) {
            throw new RuntimeException(
                'La sede "' . $sede->ALM_NombreAlmacen . '" no tiene configurada la serie de guia de remision.'
            );
        }

        $numero = $this->siguienteCorrelativo($serie, $sede->correlativoInicialPara(EmpresaFacturacion::TIPO_GUIA_REMISION));

        $guia = new GuiaRemision();
        $guia->GRM_Serie = $serie;
        $guia->GRM_Numero = $numero;
        $guia->VEN_Id = $ventaId;

        $guia->GRM_MotivoTraslado = GuiaRemision::MOTIVO_VENTA;
        $guia->GRM_DesMotivo = $datos['des_motivo'] ?? 'VENTA';
        $guia->GRM_FechaTraslado = $datos['fecha_traslado'];
        $guia->GRM_PesoTotal = $datos['peso_total'];
        $guia->GRM_UndPeso = $datos['und_peso'] ?? 'KGM';

        $guia->GRM_UbigeoPartida = $datos['ubigeo_partida'];
        $guia->GRM_DireccionPartida = $datos['direccion_partida'];
        $guia->GRM_UbigeoLlegada = $datos['ubigeo_llegada'];
        $guia->GRM_DireccionLlegada = $datos['direccion_llegada'];

        $guia->GRM_ModoTransporte = $datos['modo_transporte'];

        if ($datos['modo_transporte'] === GuiaRemision::MODO_PUBLICO) {
            $guia->GRM_TransportistaTipoDoc = $datos['transportista_tipo_doc'];
            $guia->GRM_TransportistaNumero = $datos['transportista_numero'];
            $guia->GRM_TransportistaRazonSocial = $datos['transportista_razon_social'];
        } else {
            $guia->GRM_VehiculoPlaca = $datos['vehiculo_placa'];
            $guia->GRM_ConductorTipoDoc = $datos['conductor_tipo_doc'];
            $guia->GRM_ConductorNumero = $datos['conductor_numero'];
            $guia->GRM_ConductorNombres = $datos['conductor_nombres'];
            $guia->GRM_ConductorApellidos = $datos['conductor_apellidos'];
            $guia->GRM_ConductorLicencia = $datos['conductor_licencia'] ?? null;
        }

        $guia->GRM_Estado = 'PENDIENTE';
        $guia->save();

        return $guia;
    }

    private function siguienteCorrelativo(string $serie, int $correlativoInicial = 0): int
    {
        $ultimo = GuiaRemision::where('GRM_Serie', $serie)
            ->lockForUpdate()
            ->max('GRM_Numero');

        $base = max((int) $ultimo, $correlativoInicial - 1);

        return $base + 1;
    }
}
