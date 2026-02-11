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
            return route($name, ['tenant' => tenant()->id] + $params);
        }

        // fallback a central login
        return route('central.login');
    }
}
