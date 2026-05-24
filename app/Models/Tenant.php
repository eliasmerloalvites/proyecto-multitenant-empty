<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'tipo_negocio',
        'plan',
        'status',
        'max_users',
        'max_images',
        'storage_limit_mb',
        'custom_domain_enabled',
        'custom_branding',
        'data',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'tipo_negocio',
            'plan',
            'status',
            'max_users',
            'max_images',
            'storage_limit_mb',
            'custom_domain_enabled',
            'custom_branding',
            'data',
        ];
    }

}