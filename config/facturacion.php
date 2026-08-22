<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API facturadora
    |--------------------------------------------------------------------------
    |
    | URL base del servicio que firma y envia los comprobantes a SUNAT.
    | Se lee desde config() y no desde env() directamente, porque env() devuelve
    | null cuando la configuracion esta cacheada (php artisan config:cache).
    |
    */

    'api_url' => env('FACTURADOR_API'),

];
