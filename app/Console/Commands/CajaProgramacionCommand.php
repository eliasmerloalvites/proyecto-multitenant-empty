<?php

namespace App\Console\Commands;

use App\Http\Controllers\Tenant\CajaSesionController;
use App\Models\Tenant;
use App\Models\Tenant\Caja;
use App\Models\Tenant\CajaSesion;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CajaProgramacionCommand extends Command
{
    protected $signature = 'caja:programacion';

    protected $description = 'Apertura y cierra automáticamente las cajas con CAJ_ProgramacionActiva según su CAJ_HoraApertura/CAJ_HoraCierre.';

    public function handle(): int
    {
        $aperturadas = 0;
        $cerradas = 0;

        foreach (Tenant::where('status', 'activo')->get() as $tenant) {
            $tenant->run(function () use (&$aperturadas, &$cerradas) {
                $ahora = Carbon::now('America/Lima');
                $horaActual = $ahora->format('H:i');

                $cajas = Caja::where('CAJ_Status', 1)
                    ->where('CAJ_ProgramacionActiva', true)
                    ->get();

                foreach ($cajas as $caja) {
                    // Apertura: coincide la hora, no tiene turno abierto ya.
                    if ($caja->CAJ_HoraApertura && substr($caja->CAJ_HoraApertura, 0, 5) === $horaActual) {
                        if (! $caja->sesionAbierta()->exists()) {
                            CajaSesion::create([
                                'CAJ_Id' => $caja->CAJ_Id,
                                'USU_Id_Apertura' => null,
                                'CS_MontoApertura' => $caja->CAJ_MontoApertura,
                                'CS_FechaApertura' => $ahora,
                                'CS_Estado' => 'abierta',
                            ]);
                            $aperturadas++;
                        }
                    }

                    // Cierre: coincide la hora, tiene un turno abierto para cerrar.
                    if ($caja->CAJ_HoraCierre && substr($caja->CAJ_HoraCierre, 0, 5) === $horaActual) {
                        $sesion = $caja->sesionAbierta()->first();

                        if ($sesion) {
                            $montoEsperado = CajaSesionController::calcularMontoEsperado($sesion);

                            $sesion->update([
                                'CS_MontoEsperado' => $montoEsperado,
                                // Cierre automático: nadie contó el efectivo físicamente,
                                // así que se asume que coincide con lo esperado. El
                                // usuario puede revisar y ajustar el turno después desde
                                // el historial si detecta una diferencia real.
                                'CS_MontoReal' => $montoEsperado,
                                'CS_Diferencia' => 0,
                                'CS_FechaCierre' => $ahora,
                                'CS_Estado' => 'cerrada',
                                'CS_TipoCierre' => 'automatico',
                                'CS_Observacion' => 'Cierre automático por programación horaria.',
                            ]);
                            $cerradas++;
                        }
                    }
                }
            });
        }

        $this->info("Cajas aperturadas automáticamente: {$aperturadas}. Cajas cerradas automáticamente: {$cerradas}.");

        return self::SUCCESS;
    }
}
