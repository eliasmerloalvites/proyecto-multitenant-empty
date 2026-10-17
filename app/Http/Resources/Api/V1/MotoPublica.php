<?php

namespace App\Http\Resources\Api\V1;

use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Models\TenantTallerMotos\Moto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Lo que la app ve de una moto de su garaje. Se calcula SOLO con los
 * registros que el vinculo de la cuenta permite ver (ver RegistrosMotoService)
 * y nunca incluye datos de personas (nombre o celular de quien figura en los
 * registros del taller), ni ids internos de mantenimientos o de personal.
 */
class MotoPublica
{
    /** @param Collection<int, object> $registrosVisibles Del mas reciente al mas antiguo. */
    public static function datos(Moto $moto, CuentaMovilMoto $vinculo, Collection $registrosVisibles): array
    {
        $ultimo = $registrosVisibles->first();

        return [
            'id' => $moto->MOT_Id,
            'placa' => $moto->MOT_Placa,
            'descripcion' => $moto->MOT_Descripcion ?: ($ultimo ? trim((string) $ultimo->unidad) ?: null : null),
            'vinculo' => [
                'metodo' => $vinculo->CMM_Metodo,
                'alcance' => $vinculo->CMM_Alcance,
            ],
            'total_servicios' => $registrosVisibles->count(),
            'ultimo_servicio' => $ultimo ? [
                'fecha' => self::fecha($ultimo->fecha),
                'tipo' => $ultimo->tipo,
                'km' => self::texto($ultimo->km),
            ] : null,
            'proximo_servicio' => self::texto($registrosVisibles->first(fn ($r) => self::texto($r->proximo_servicio) !== null)?->proximo_servicio),
            'proximo_cambio_aceite' => self::texto($registrosVisibles->first(fn ($r) => self::texto($r->proximo_cambio_aceite) !== null)?->proximo_cambio_aceite),
        ];
    }

    /** Solicitud pendiente: solo la placa y la fecha, sin datos de registros. */
    public static function pendiente(Moto $moto, CuentaMovilMoto $vinculo): array
    {
        return [
            'id' => $moto->MOT_Id,
            'placa' => $moto->MOT_Placa,
            'estado' => $vinculo->CMM_Estado,
            'solicitada_el' => optional($vinculo->created_at)->toIso8601String(),
        ];
    }

    private static function fecha($valor): ?string
    {
        return $valor ? Carbon::parse($valor)->toIso8601String() : null;
    }

    private static function texto($valor): ?string
    {
        $t = trim((string) $valor);

        return $t === '' ? null : $t;
    }
}
