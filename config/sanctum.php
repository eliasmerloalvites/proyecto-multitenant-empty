<?php

/*
|--------------------------------------------------------------------------
| Laravel Sanctum - solo tokens de API (app movil de clientes)
|--------------------------------------------------------------------------
|
| Sanctum se usa unicamente para tokens opacos de la app movil. Se desactiva
| todo lo que no hace falta:
|  - 'guard' vacio: no se acepta la sesion web como forma de autenticarse en
|    la API (solo el header Authorization: Bearer).
|  - 'stateful' vacio y 'routes' false: sin SPA con cookies ni ruta
|    /sanctum/csrf-cookie.
|
| Los tokens viven en la base de datos del TENANT (tabla personal_access_tokens
| de cada taller), asi que un token emitido por un taller no existe en otro.
|
*/

return [

    'stateful' => [],

    'guard' => [],

    'routes' => false,

    // Minutos de vigencia de un token (30 dias). Ademas, cada token se emite
    // con su propio vencimiento (ver config/app_movil.php).
    'expiration' => 60 * 24 * 30,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'ktm_'),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
