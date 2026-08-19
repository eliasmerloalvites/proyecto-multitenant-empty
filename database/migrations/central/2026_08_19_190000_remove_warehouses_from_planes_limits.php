<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveWarehousesFromPlanesLimits extends Migration
{
    /**
     * 'Locales/Sedes' (branches) y 'Almacenes' (warehouses) apuntaban al
     * mismo recurso real (la tabla `almacen`, ver SedeController y
     * AlmacenController) — nunca hubo forma de diferenciarlos en el
     * sistema. Se deja un único límite ('branches') y se limpia la clave
     * 'warehouses' que quedó huérfana en los planes ya guardados.
     */
    public function up(): void
    {
        foreach (DB::table('planes')->get() as $plan) {
            $limits = json_decode($plan->limits, true) ?? [];

            if (array_key_exists('warehouses', $limits)) {
                unset($limits['warehouses']);

                DB::table('planes')->where('id', $plan->id)->update([
                    'limits' => json_encode($limits),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No reversible de forma significativa: el valor de 'warehouses' ya se perdió.
    }
}
