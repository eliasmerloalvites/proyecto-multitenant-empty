<?php

namespace App\Console\Commands;

use App\Http\Controllers\Tenant\ComprobanteSunatController;
use App\Models\Tenant;
use App\Models\Tenant\EmpresaFacturacion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Reintenta automaticamente las boletas/facturas que quedaron en
 * PENDIENTE o ERROR (nunca llegaron a SUNAT, o el envio fallo), a la hora
 * que cada tenant configuro en Configuracion > Empresa
 * (EmpresaFacturacion::sunat_reintento_hora, 21:00 por defecto). Corre
 * cada minuto (ver routes/console.php) para poder disparar en la hora
 * exacta de cada tenant, igual que caja:programacion con
 * CAJ_HoraApertura/CAJ_HoraCierre.
 *
 * Reusa ComprobanteSunatController::reenviar() en vez de reimplementar la
 * logica de envio: esa es la misma accion que ya usa el usuario cuando
 * reenvia un comprobante a mano, incluida la verificacion previa contra
 * SUNAT para no duplicar un comprobante que si llego pero cuya respuesta
 * se perdio localmente.
 */
class SunatReintentarPendientesCommand extends Command
{
    protected $signature = 'sunat:reintentar-pendientes';

    protected $description = 'Reintenta boletas/facturas en PENDIENTE o ERROR, a la hora configurada por cada tenant.';

    /** Tope por tenant en cada corrida, para no saturar la cola de golpe si se acumularon muchas. */
    const LIMITE_POR_TENANT = 100;

    public function handle(): int
    {
        $horaActual = Carbon::now('America/Lima')->format('H:i');
        $tenantsProcesados = 0;
        $totalReintentados = 0;
        $totalConError = 0;

        foreach (Tenant::where('status', 'activo')->get() as $tenant) {
            $tenant->run(function () use ($tenant, $horaActual, &$tenantsProcesados, &$totalReintentados, &$totalConError) {
                $empresa = EmpresaFacturacion::delTenantActual();

                if (! $empresa || substr($empresa->sunat_reintento_hora ?? '', 0, 5) !== $horaActual) {
                    return;
                }

                // Sin esto configurado no hay nada que reintentar (mismo
                // chequeo que ya bloquea el envio normal al vender).
                if (! $empresa->puedeFacturar()) {
                    return;
                }

                // Solo PENDIENTE (nunca se envio) o ERROR (fallo el envio).
                // RECHAZADO se excluye a proposito: es una respuesta firme de
                // SUNAT, no una falla tecnica -- reintentarlo solo tiene
                // sentido despues de que el usuario corrija el dato que lo
                // causo, no todas las noches sin intervencion.
                $pendientes = DB::table('documento_venta')
                    ->whereIn('DOV_Tipo', [EmpresaFacturacion::TIPO_BOLETA, EmpresaFacturacion::TIPO_FACTURA])
                    ->where('DOV_Anulado', 0)
                    ->whereIn('DOV_Estado', ['PENDIENTE', 'ERROR'])
                    ->orderBy('DOV_Id')
                    ->limit(self::LIMITE_POR_TENANT)
                    ->pluck('VEN_Id');

                if ($pendientes->isEmpty()) {
                    return;
                }

                $tenantsProcesados++;
                $controller = app(ComprobanteSunatController::class);

                foreach ($pendientes as $ventaId) {
                    try {
                        $respuesta = $controller->reenviar($ventaId);
                        $data = json_decode($respuesta->getContent(), true);

                        if (! empty($data['success'])) {
                            $totalReintentados++;
                        } else {
                            $totalConError++;
                        }
                    } catch (\Throwable $e) {
                        report($e);
                        $totalConError++;
                    }
                }

                $this->info("Tenant {$tenant->id}: {$pendientes->count()} comprobante(s) reintentado(s).");
            });
        }

        $this->info("Listo. Tenants con pendientes a esta hora: {$tenantsProcesados}. Reintentados OK: {$totalReintentados}. Con error: {$totalConError}.");

        return self::SUCCESS;
    }
}
