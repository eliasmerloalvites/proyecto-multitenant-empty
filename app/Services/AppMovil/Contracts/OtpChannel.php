<?php

namespace App\Services\AppMovil\Contracts;

/**
 * Canal por el que se entrega el codigo de verificacion al celular del
 * cliente (WhatsApp, SMS, ...). Cambiar de proveedor es implementar esta
 * interfaz y apuntar config('app_movil.otp.canal') a la nueva clase, sin
 * tocar el resto del flujo de autenticacion.
 */
interface OtpChannel
{
    /**
     * @param  string  $celular  Celular normalizado, solo digitos con codigo de pais.
     * @param  string  $codigo   Codigo en texto plano (solo se usa aqui; en la base se guarda su hash).
     * @param  string  $taller   Nombre del taller, para el texto del mensaje.
     */
    public function enviar(string $celular, string $codigo, string $taller): void;
}
