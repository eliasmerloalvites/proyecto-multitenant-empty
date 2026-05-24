<?php

return [

    'plans' => [

        'start' => [

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

                'modules' => [
                    'agenda' => true,
                    'reports' => false,
                    'inventory' => true,
                    'sales' => true,
                ],

                'limits' => [
                    'branches' => 1,
                    'warehouses' => 1,
                    'cash_registers' => 1,
                ],

            ],
        ],

        'basic' => [

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
                    'agenda' => true,
                    'reports' => true,
                    'inventory' => true,
                    'sales' => true,
                ],

                'limits' => [
                    'branches' => 1,
                    'warehouses' => 2,
                    'cash_registers' => 2,
                ],

            ],
        ],

        'plus' => [

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
                    'agenda' => true,
                    'reports' => true,
                    'inventory' => true,
                    'sales' => true,
                    'analytics' => true,
                ],

                'limits' => [
                    'branches' => 5,
                    'warehouses' => 10,
                    'cash_registers' => 10,
                ],

            ],
        ],

    ],

];