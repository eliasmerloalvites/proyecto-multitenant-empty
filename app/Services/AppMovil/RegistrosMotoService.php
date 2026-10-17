<?php

namespace App\Services\AppMovil;

use App\Models\TenantTallerMotos\CuentaMovil;
use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Models\TenantTallerMotos\Moto;
use App\Services\TenantTallerMotos\GestionProcesoService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Lee, por placa o por celular, los registros que el taller ya tiene (los 5
 * tipos de mantenimiento y las reservas). No escribe nada.
 *
 * REGLA DE VISIBILIDAD (que registros de una moto puede ver una cuenta):
 *  - vinculo COMPLETO (el taller respalda que es el dueno): todos los
 *    registros de la placa.
 *  - vinculo REGISTROS_PROPIOS (se dedujo por coincidencia de celular): solo
 *    los registros hechos con el celular de la cuenta. Asi, si una moto
 *    cambia de dueno, el nuevo dueno no ve el historial del anterior ni el
 *    anterior el del nuevo, aunque la placa sea la misma.
 */
class RegistrosMotoService
{
    /**
     * Todos los registros de mantenimiento de una placa normalizada, del mas
     * reciente al mas antiguo.
     *
     * @return Collection<int, object>
     */
    public function registrosDeLaPlaca(string $placaNormalizada): Collection
    {
        $registros = collect();

        foreach (GestionProcesoService::TIPOS as $meta) {
            $p = $meta['prefijo'];

            $filas = DB::table($meta['tabla'])
                ->whereRaw(NormalizadorPlaca::expresionSql("{$p}_Placa") . ' = ?', [$placaNormalizada])
                ->select(
                    DB::raw("{$p}_Id as id"),
                    DB::raw("{$p}_Celular as celular"),
                    DB::raw("{$p}_Propietario as propietario"),
                    DB::raw("{$p}_Unidad as unidad"),
                    DB::raw("{$p}_KMEntrada as km"),
                    DB::raw("{$p}_ProximoServicio as proximo_servicio"),
                    DB::raw("{$p}_ProximoCambioAceite as proximo_cambio_aceite"),
                    DB::raw("{$p}_FechaCreacion as fecha"),
                    DB::raw("{$p}_FechaInicio as fecha_inicio"),
                    DB::raw("{$p}_FechaTermino as fecha_termino"),
                    DB::raw("{$p}_Estado as estado"),
                    DB::raw("RES_Id as res_id")
                )
                ->get();

            foreach ($filas as $fila) {
                $fila->tabla = $meta['tabla'];
                $fila->prefijo = $p;
                $fila->tipo = $meta['etiqueta'];
                $registros->push($fila);
            }
        }

        return $registros->sortByDesc('fecha')->values();
    }

    /** Registros de la lista cuyo celular (normalizado) es el indicado. */
    public function conCelular(Collection $registros, string $celularNormalizado): Collection
    {
        return $registros
            ->filter(fn ($r) => NormalizadorCelular::normalizar($r->celular) === $celularNormalizado)
            ->values();
    }

    /** Registros de la moto que esta cuenta puede ver segun el alcance de su vinculo. */
    public function visiblesPara(CuentaMovilMoto $vinculo, CuentaMovil $cuenta, Moto $moto): Collection
    {
        $todos = $this->registrosDeLaPlaca($moto->MOT_Placa);

        return $vinculo->CMM_Alcance === CuentaMovilMoto::ALCANCE_COMPLETO
            ? $todos
            : $this->conCelular($todos, $cuenta->CMV_Celular);
    }

    /**
     * Placas (normalizadas y validas) que aparecen en el taller con este
     * celular, en mantenimientos o reservas. Devuelve
     * [placa => ['descripcion' => ..., 'fecha' => ...]] con los datos del
     * registro mas reciente de cada placa.
     */
    public function placasPorCelular(string $celularNormalizado): array
    {
        // Prefiltro en SQL por los ultimos 9 digitos (el celular en la base viene
        // con espacios, guiones o "+51"); la comparacion exacta se hace en PHP.
        $nacional = substr($celularNormalizado, -9);
        $sinFormato = fn (string $col) => "REPLACE(REPLACE(REPLACE(REPLACE({$col}, ' ', ''), '-', ''), '+', ''), '.', '')";

        $origenes = [];

        foreach (GestionProcesoService::TIPOS as $meta) {
            $p = $meta['prefijo'];
            $origenes[] = ['tabla' => $meta['tabla'], 'placa' => "{$p}_Placa", 'celular' => "{$p}_celular", 'unidad' => "{$p}_Unidad", 'fecha' => "{$p}_FechaCreacion"];
        }

        $origenes[] = ['tabla' => 'reservacion', 'placa' => 'RES_Placa', 'celular' => 'RES_Celular', 'unidad' => 'RES_Moto', 'fecha' => 'RES_FechaProgramada'];

        $resultado = [];

        foreach ($origenes as $o) {
            $filas = DB::table($o['tabla'])
                ->whereRaw($sinFormato($o['celular']) . ' LIKE ?', ['%' . $nacional])
                ->select(
                    DB::raw("{$o['placa']} as placa"),
                    DB::raw("{$o['celular']} as celular"),
                    DB::raw("{$o['unidad']} as unidad"),
                    DB::raw("{$o['fecha']} as fecha")
                )
                ->get();

            foreach ($filas as $f) {
                $placa = NormalizadorPlaca::normalizar($f->placa);

                if (! $placa || NormalizadorCelular::normalizar($f->celular) !== $celularNormalizado) {
                    continue;
                }

                $actual = $resultado[$placa] ?? null;

                if (! $actual || (string) $f->fecha > (string) $actual['fecha']) {
                    $resultado[$placa] = ['descripcion' => trim((string) $f->unidad) ?: null, 'fecha' => (string) $f->fecha];
                }
            }
        }

        return $resultado;
    }
}
