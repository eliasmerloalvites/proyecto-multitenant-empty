<?php

namespace App\Http\Resources\Api\V1;

use App\Services\AppMovil\OrdenesMotoService;
use Illuminate\Support\Carbon;

/** Lista blanca de lo que el cliente ve de una orden. Nunca celular ni propietario. */
class OrdenPublica
{
    public static function resumen(array $o): array
    {
        $r = $o['registro'];

        return [
            'ref' => OrdenesMotoService::ref($r->prefijo, $r->id),
            'numero' => OrdenesMotoService::numeroOrden($r->prefijo, $r->id),
            'tipo' => $r->tipo,
            'moto_id' => $o['moto']->MOT_Id,
            'placa' => $o['moto']->MOT_Placa,
            'estado' => $o['estado'],
            'estado_texto' => OrdenesMotoService::etiquetaEstado($o['estado']),
            'fecha_ingreso' => ($r->fecha_inicio ?: $r->fecha) ? Carbon::parse($r->fecha_inicio ?: $r->fecha)->toIso8601String() : null,
            'fecha_termino' => $r->fecha_termino ? Carbon::parse($r->fecha_termino)->toIso8601String() : null,
            'fecha_entrega' => $o['entrega'],
            'km' => $r->km,
        ];
    }

    public static function detalle(array $o, OrdenesMotoService $svc, bool $pdf): array
    {
        $r = $o['registro'];
        $ref = OrdenesMotoService::ref($r->prefijo, $r->id);
        $t = $svc->textos($r->prefijo, $r->id);

        return self::resumen($o) + [
            'unidad' => $r->unidad,
            'proximo_servicio' => $r->proximo_servicio,
            'proximo_cambio_aceite' => $r->proximo_cambio_aceite,
            'solicitado' => $t->solicitado ?? null,
            'realizado' => $t->realizado ?? null,
            'recomendacion' => $t->recomendacion ?? null,
            'recomendacion_prioridad' => $t->prioridad ?? null,
            'repuestos' => $svc->repuestos($r->prefijo, $r->id),
            'fotos' => $svc->fotos($r->prefijo, $r->id)->map(fn ($f) => [
                'item' => (int) $f->item,
                'url' => url("/api/v1/ordenes/{$ref}/fotos/{$f->item}"),
                'miniatura' => url("/api/v1/ordenes/{$ref}/fotos/{$f->item}?miniatura=1"),
            ])->values()->all(),
            'pdf_disponible' => $pdf,
        ];
    }
}
