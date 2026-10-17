<?php

namespace App\Services\AppMovil\Canales;

use App\Services\AppMovil\Contracts\OtpChannel;
use App\Services\AppMovil\NormalizadorCelular;
use Illuminate\Support\Facades\Log;

/**
 * Canal de DESARROLLO: no envia nada, escribe el codigo en el log para poder
 * probar la app sin un proveedor de WhatsApp/SMS. Solo registra el codigo en
 * entornos local y testing; en produccion deja una advertencia sin el codigo,
 * de modo que sin un canal real nadie puede iniciar sesion (y el codigo nunca
 * queda en los logs de produccion).
 *
 * Excepcion para validar la app contra produccion: los celulares listados en
 * APP_MOVIL_OTP_CELULARES_PRUEBA si registran su codigo (y solo ellos).
 */
class LogOtpChannel implements OtpChannel
{
    public function enviar(string $celular, string $codigo, string $taller): void
    {
        if (app()->environment(['local', 'testing'])) {
            Log::info("[APP MOVIL] OTP para {$celular} ({$taller}): {$codigo}");

            return;
        }

        if ($this->esCelularDePrueba($celular)) {
            Log::warning("[APP MOVIL][PRUEBA] OTP para {$celular} ({$taller}): {$codigo}");

            return;
        }

        Log::warning('[APP MOVIL] Se pidio un codigo de verificacion pero no hay un canal de envio configurado (APP_MOVIL_OTP_CANAL).');
    }

    private function esCelularDePrueba(string $celular): bool
    {
        foreach ((array) config('app_movil.otp.celulares_prueba', []) as $prueba) {
            if (NormalizadorCelular::normalizar($prueba) === $celular) {
                return true;
            }
        }

        return false;
    }
}
