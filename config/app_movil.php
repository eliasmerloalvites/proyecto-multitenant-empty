<?php

/*
|--------------------------------------------------------------------------
| App movil de clientes (KaelTech Moto Mobile)
|--------------------------------------------------------------------------
*/

return [

    // Esquema de las URL que devuelve el directorio de talleres. En
    // produccion siempre https; en local se puede poner http en el .env.
    'esquema' => env('APP_MOVIL_ESQUEMA', 'https'),

    // Puerto a agregar a las direcciones de taller (solo desarrollo local, ej. 8000).
    'puerto' => env('APP_MOVIL_PUERTO'),

    // Si se define, el directorio prefiere el dominio del taller que termine en
    // este sufijo (solo desarrollo local, ej. .10.0.2.2.nip.io).
    'sufijo_dominio' => env('APP_MOVIL_SUFIJO_DOMINIO'),

    'directorio' => [
        // Largo del codigo publico del taller que va en el QR.
        'longitud_codigo' => 8,
    ],

    // Codigo de verificacion de un solo uso (OTP) enviado al celular.
    'otp' => [
        'longitud' => 6,
        'ttl_minutos' => 5,
        'max_intentos' => 5,
        'espera_reenvio_segundos' => 60,
        'max_envios_por_hora' => 3,

        // Canal de envio: 'log' (desarrollo: el codigo solo se escribe en el
        // log y unicamente fuera de produccion). Los canales reales (WhatsApp,
        // SMS) se agregan despues implementando OtpChannel.
        'canal' => env('APP_MOVIL_OTP_CANAL', 'log'),

        // SOLO PARA VALIDAR EN PRODUCCION mientras no haya un canal real:
        // celulares (separados por coma) cuyo codigo se escribe en el log del
        // servidor aunque el entorno sea produccion. Los demas celulares no
        // reciben nada. Dejar vacio (o borrar la variable) al terminar.
        'celulares_prueba' => array_values(array_filter(array_map('trim', explode(',', (string) env('APP_MOVIL_OTP_CELULARES_PRUEBA', ''))))),
    ],

    'sesion' => [
        'nombre_token' => 'app-movil',
        'dias_vigencia' => 30,
        'max_dispositivos' => 5,
    ],

];
