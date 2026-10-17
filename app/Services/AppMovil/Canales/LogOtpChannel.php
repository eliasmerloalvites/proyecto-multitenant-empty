<?php

namespace App\Services\AppMovil\Canales;

use App\Services\AppMovil\Contracts\OtpChannel;
use Illuminate\Support\Facades\Log;

/**
 * Canal de DESARROLLO: no envia nada, escribe el codigo en el log para poder
 * probar la app sin un proveedor de WhatsApp/SMS. Solo registra el codigo en
 * entornos local y testing; en produccion deja una advertencia sin el codigo,
 * de modo que sin un canal real nadie puede iniciar sesion (y el codigo nunca
 * queda en los logs de produccion).
 */
class LogOtpChannel implements OtpChannel
{
    public function enviar(string $celular, string $codigo, string $taller): void
    {
        if (app()->environment(['local', 'testing'])) {
            Log::info("[APP MOVIL] OTP para {$celular} ({$taller}): {$codigo}");

            return;
        }

        Log::warning('[APP MOVIL] Se pidio un codigo de verificacion pero no hay un canal de envio configurado (APP_MOVIL_OTP_CANAL).');
    }
}
