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

if (! function_exists('tenant_has_module')) {

    /**
     * Indica si el plan contratado por el tenant actual incluye un módulo.
     * "modules" se guarda como atributo propio del Tenant (columna virtual
     * "data" de stancl/tenancy: cualquier attr que no sea columna real de la
     * tabla tenants se serializa ahí y se restaura como atributo de nivel
     * superior al leer el modelo), por eso se lee con tenant('modules').
     * Si el tenant no tiene el flag seteado (planes antiguos sin resincronizar),
     * cae al mapa de config('saas.plans').
     */
    function tenant_has_module(string $module): bool
    {
        $tenant = tenant();

        if (! $tenant) {
            return false;
        }

        $modules = tenant('modules');

        if (is_array($modules) && array_key_exists($module, $modules)) {
            return (bool) $modules[$module];
        }

        $plan = $tenant->plan ?? 'start';

        return (bool) (config("saas.plans.{$plan}.data.modules.{$module}") ?? false);
    }
}
