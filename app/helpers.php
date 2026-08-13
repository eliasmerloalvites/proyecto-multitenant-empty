<?php

use App\Models\Tenant;
use App\Models\Plan;

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

if (! function_exists('saas_plans_config')) {

    /**
     * Los 4 planes (start/basic/plus/empresarial), leídos desde la tabla
     * `planes` (editable desde el panel central) y devueltos con la misma
     * forma que antes tenía config('saas.plans'), para que todo el código
     * que ya consumía ese array (ClientController, dashboards, etc.) no
     * tuviera que cambiar de estructura al migrar de config a BD.
     *
     * Memoizado por request: esto se llama potencialmente muchas veces por
     * página (una vez por @if(tenant_has_module(...)) en el sidebar).
     */
    function saas_plans_config(): array
    {
        static $plans = null;

        if ($plans === null) {
            $plans = Plan::all()->keyBy('key')->map->toConfigArray()->toArray();
        }

        return $plans;
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
     * cae a los módulos configurados actualmente para su plan (tabla planes).
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

        return (bool) (saas_plans_config()[$plan]['data']['modules'][$module] ?? false);
    }
}
