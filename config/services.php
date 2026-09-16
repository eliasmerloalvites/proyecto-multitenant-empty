<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Pasarela de pago (cobros:procesar genera un link de pago por Culqi
    // para cada cliente en vez de solo avisar por correo). Llaves de prueba
    // llevan el prefijo pk_test_/sk_test_, las de producción pk_live_/sk_live_.
    'culqi' => [
        'public_key' => env('CULQI_PUBLIC_KEY'),
        'secret_key' => env('CULQI_SECRET_KEY'),
    ],

    // Chromium del sistema para Browsershot (generacion de PDF/imagen de
    // tickets y cotizaciones). Vive en config/ en vez de leerse con env()
    // directo en el controlador porque config:cache deja de leer el .env
    // en tiempo de ejecucion.
    'puppeteer' => [
        'executable_path' => env('PUPPETEER_EXECUTABLE_PATH'),
    ],

];
