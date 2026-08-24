<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Revisa recordatorios/vencidos/suspensiones de cobro todos los días.
// Requiere que el cron del servidor tenga corriendo `schedule:run` cada minuto
// (crontab: * * * * * php artisan schedule:run >> /dev/null 2>&1).
Schedule::command('cobros:procesar')
    ->dailyAt('08:00')
    ->timezone('America/Lima')
    ->withoutOverlapping();

// Apertura/cierre automático de cajas con CAJ_ProgramacionActiva activada.
// Corre cada minuto para poder disparar en la hora exacta configurada por
// cada caja (CAJ_HoraApertura / CAJ_HoraCierre).
Schedule::command('caja:programacion')
    ->everyMinute()
    ->timezone('America/Lima')
    ->withoutOverlapping();

// Detecta tenants que quedaron por encima de su límite (usuarios, sedes,
// cajas) tras un downgrade de plan. El enforcement normal solo bloquea la
// creación de registros nuevos, no audita a los que ya existían — esto
// cubre ese hueco. Semanal porque es una auditoría, no algo urgente.
Schedule::command('plan:auditar-limites')
    ->weeklyOn(1, '07:00')
    ->timezone('America/Lima')
    ->withoutOverlapping();
