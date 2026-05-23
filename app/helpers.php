<?php

use App\Models\Tenant;

if (! function_exists('asset_root')) {
    function asset_root(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (! function_exists('tenant_url')) {

    function tenant_url(string $name, array $params = [])
    {
        if (tenant()) {
            return route($name, $params);
        }

        return route('central.login');
    }
}
