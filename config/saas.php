<?php

return [

    'plans' => [
            'start' => [
                'max_users' => 3,
                'max_images' => 4,
                'storage_limit_mb' => 500,
                'custom_domain_enabled' => false,
                'custom_branding' => false,
            ],

            'basic' => [
                'max_users' => 5,
                'max_images' => 10,
                'storage_limit_mb' => 1500,
                'custom_domain_enabled' => false,
                'custom_branding' => false,
            ],

            'plus' => [
                'max_users' => 15,
                'max_images' => 50,
                'storage_limit_mb' => 5000,
                'custom_domain_enabled' => true,
                'custom_branding' => true,
            ],

            'empresarial' => [
                'max_users' => 999,
                'max_images' => 999,
                'storage_limit_mb' => 50000,
                'custom_domain_enabled' => true,
                'custom_branding' => true,
            ],
        ],

];