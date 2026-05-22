<?php

if (! function_exists('asset_root')) {
    function asset_root(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (! function_exists('tenant_url')) {

    function tenant_url(string $name, array $params = [])
    {
        if (function_exists('tenant') && tenant()) {

            $tenantName = str_replace(tenant()->tipo_negocio.'_', '', tenant()->id);

            return route($name, [
                'tenant' => $tenantName
            ] + $params);
        }

        return route('central.login');
    }
}
