<?php

return [

    /*
    |--------------------------------------------------------------------------
    | COBROS
    |--------------------------------------------------------------------------
    | Política del comando `cobros:procesar` (programado a diario).
    */
    'cobros' => [
        // Días de atraso (desde la fecha de cobro del ciclo) antes de
        // suspender automáticamente al cliente/tenant por falta de pago.
        'dias_gracia_suspension' => 5,

        // Días de prueba gratis desde el registro (autoregistro o carga
        // manual por staff) antes de que arranque el primer ciclo de cobro.
        'dias_prueba_gratis' => 7,
    ],

    'plans' => [

        'start' => [

            /* PRECIO DE REFERENCIA (S/ / mes) — usado para estimar MRR en el
             * dashboard central. Plus/Empresarial pueden variar por acuerdo
             * comercial; este valor es solo el piso publicado en la web. */
            'price' => 59,

            /* LIMITES */
            'max_users' => 3,
            'max_images' => 4,
            'storage_limit_mb' => 500,

            /* FEATURES */
            'custom_domain_enabled' => false,
            'custom_branding' => false,

            /* DATA INICIAL */
            'data' => [

                'branding' => [
                    'logo' => null,
                    'primary_color' => '#0B63CE',
                ],

                // mantenimientos incluye el flujo de reservas (agenda de servicios)
                'modules' => [
                    'mantenimientos' => true,
                    'productos' => false,
                    'inventario' => false,
                    'compras' => false,
                    'ventas' => false,
                    'reports' => false,
                    'analytics' => false,
                    'api_access' => false,
                ],

                'limits' => [
                    'branches' => 1,
                    'cash_registers' => 1,
                ],

            ],
        ],

        'basic' => [

            'price' => 99,

            'max_users' => 5,
            'max_images' => 10,
            'storage_limit_mb' => 1500,

            'custom_domain_enabled' => false,
            'custom_branding' => false,

            'data' => [

                'branding' => [
                    'logo' => null,
                    'primary_color' => '#0B63CE',
                ],

                'modules' => [
                    'mantenimientos' => true,
                    'productos' => false,
                    'inventario' => false,
                    'compras' => false,
                    'ventas' => false,
                    'reports' => true,
                    'analytics' => false,
                    'api_access' => false,
                ],

                'limits' => [
                    'branches' => 1,
                    'cash_registers' => 2,
                ],

            ],
        ],

        'plus' => [

            'price' => 199,

            'max_users' => 15,
            'max_images' => 50,
            'storage_limit_mb' => 5000,

            'custom_domain_enabled' => true,
            'custom_branding' => true,

            'data' => [

                'branding' => [
                    'logo' => null,
                    'primary_color' => '#0B63CE',
                ],

                'modules' => [
                    'mantenimientos' => true,
                    'productos' => true,
                    'inventario' => true,
                    'compras' => true,
                    'ventas' => true,
                    'reports' => true,
                    'analytics' => true,
                    'api_access' => false,
                ],

                'limits' => [
                    'branches' => 5,
                    'cash_registers' => 10,
                ],

            ],
        ],

        'empresarial' => [

            'price' => 300,

            'max_users' => 50,
            'max_images' => 200,
            'storage_limit_mb' => 20000,

            'custom_domain_enabled' => true,
            'custom_branding' => true,

            'data' => [

                'branding' => [
                    'logo' => null,
                    'primary_color' => '#0B63CE',
                ],

                'modules' => [
                    'mantenimientos' => true,
                    'productos' => true,
                    'inventario' => true,
                    'compras' => true,
                    'ventas' => true,
                    'reports' => true,
                    'analytics' => true,
                    'api_access' => true,
                ],

                'limits' => [
                    'branches' => 20,
                    'cash_registers' => 50,
                ],

                // Plan Empresarial: base para personalizaciones específicas por cliente.
                // Sobrescribir/añadir claves aquí a nivel de tenant (tenant.data) según
                // lo que se acuerde con cada cliente, sin tocar este config global.
                'customizable' => true,

            ],
        ],

    ],

];
