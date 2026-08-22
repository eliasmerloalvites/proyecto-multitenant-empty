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
     * Los 4 planes (start/basic/plus/empresarial) de un tipo_negocio dado,
     * leídos desde la tabla `planes` (editable desde el panel central) y
     * devueltos con la misma forma que antes tenía config('saas.plans'),
     * para que todo el código que ya consumía ese array (ClientController,
     * dashboards, etc.) no tuviera que cambiar de estructura al migrar de
     * config a BD. Cada vertical (tallermoto, generico, ...) tiene su
     * propio precio/límites/módulos por plan, por eso siempre requiere
     * tipo_negocio explícito — no existe un plan "global".
     *
     * Memoizado por request y por tipo_negocio: esto se llama potencialmente
     * muchas veces por página (una vez por @if(tenant_has_module(...)) en
     * el sidebar).
     */
    function saas_plans_config(string $tipoNegocio): array
    {
        static $plans = [];

        if (! isset($plans[$tipoNegocio])) {
            $plans[$tipoNegocio] = Plan::paraNegocio($tipoNegocio)->get()->keyBy('key')->map->toConfigArray()->toArray();
        }

        return $plans[$tipoNegocio];
    }
}

if (! function_exists('tenant_storage_usado_mb')) {

    /**
     * Suma en MB de todos los archivos que el tenant actual tiene en su
     * carpeta del disco 'public' (storage/app/public/{tipo_negocio}/{id}/...
     * — mismo árbol que usan tanto los uploads vía Storage::disk('public')
     * como los que se mueven directo con $file->move(public_path('storage/...')),
     * porque 'storage/' en public_path() es el symlink a storage/app/public).
     * Se usa para hacer cumplir storage_limit_mb del plan antes de aceptar
     * una subida nueva.
     */
    function tenant_storage_usado_mb(): float
    {
        if (! tenant()) {
            return 0.0;
        }

        $carpeta = tenant('tipo_negocio') . '/' . tenant('id');
        $disco = \Illuminate\Support\Facades\Storage::disk('public');

        if (! $disco->exists($carpeta)) {
            return 0.0;
        }

        $bytes = collect($disco->allFiles($carpeta))->sum(fn ($archivo) => $disco->size($archivo));

        return round($bytes / 1024 / 1024, 2);
    }
}

if (! function_exists('tenant_caja_activa_id')) {

    /**
     * Caja con la que el usuario está operando en esta sesión. Solo cuentan
     * las cajas que tienen un turno ABIERTO ahora mismo (caja_sesion) — una
     * caja activa (CAJ_Status=1) pero sin aperturar no sirve para vender
     * todavía, primero hay que aperturarla. Reglas:
     * - Sin cajas con turno abierto → null (nada que elegir; el layout
     *   ofrece aperturar en vez de seleccionar).
     * - Una sola caja con turno abierto → esa, siempre.
     * - Varias con turno abierto → la que el usuario eligió (sesión); null
     *   si todavía no eligió, lo que dispara el modal de selección.
     */
    function tenant_caja_activa_id(): ?int
    {
        $abiertas = tenant_cajas_con_turno_abierto();

        if ($abiertas->isEmpty()) {
            return null;
        }

        if ($abiertas->count() === 1) {
            return $abiertas->first()->CAJ_Id;
        }

        $sesionId = session('caja_activa_id');

        if ($sesionId && $abiertas->contains('CAJ_Id', (int) $sesionId)) {
            return (int) $sesionId;
        }

        return null;
    }
}

if (! function_exists('tenant_cajas_con_turno_abierto')) {

    /**
     * Cajas (CAJ_Status=1) que tienen un turno abierto ahora mismo, con la
     * sesión abierta precargada (->sesionAbierta) para no repetir consultas.
     */
    function tenant_cajas_con_turno_abierto(): \Illuminate\Support\Collection
    {
        if (! tenant()) {
            return collect();
        }

        return \App\Models\Tenant\Caja::where('CAJ_Status', 1)
            ->whereHas('sesionAbierta')
            ->with('sesionAbierta')
            ->get();
    }
}

if (! function_exists('tenant_caja_sesion_activa_id')) {

    /**
     * CS_Id (turno de caja abierto) contra el que se deben registrar
     * ventas/compras/gastos ahora mismo. Null si no hay caja operando (el
     * tenant no usa cajas, o hay varias abiertas y aún no se eligió una).
     */
    function tenant_caja_sesion_activa_id(): ?int
    {
        $cajaId = tenant_caja_activa_id();

        if (! $cajaId) {
            return null;
        }

        $caja = tenant_cajas_con_turno_abierto()->firstWhere('CAJ_Id', $cajaId);

        return $caja?->sesionAbierta?->CS_Id;
    }
}

if (! function_exists('tenant_requiere_apertura_caja')) {

    /**
     * true cuando el tenant SÍ usa cajas (tiene al menos una activa) pero
     * ninguna está aperturada ahora mismo — momento en el que Ventas,
     * Compras y Gastos deben bloquear la pantalla de creación y pedir
     * aperturar antes de dejar seguir. false si el tenant no usa cajas
     * (comportamiento de siempre, sin bloqueo) o si ya hay una operando.
     */
    function tenant_requiere_apertura_caja(): bool
    {
        return \App\Models\Tenant\Caja::where('CAJ_Status', 1)->exists() && ! tenant_caja_sesion_activa_id();
    }
}

if (! function_exists('tenant_caja_activa_almacen_id')) {

    /**
     * ALM_Id de la sede/almacén ligado a la caja con la que se está
     * operando ahora mismo (Caja.ALM_Id). Null si no hay caja activa o la
     * caja no tiene almacén asignado — en ese caso el caller debe caer a
     * su propio fallback (ej. el almacén principal).
     */
    function tenant_caja_activa_almacen_id(): ?int
    {
        $cajaId = tenant_caja_activa_id();

        if (! $cajaId) {
            return null;
        }

        return \App\Models\Tenant\Caja::where('CAJ_Id', $cajaId)->value('ALM_Id');
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

        // El vertical Genérico es un POS: productos/inventario/compras/ventas
        // son su funcionalidad base, no un add-on premium como en Tallermoto
        // (donde el negocio es mantenimientos/reservas y ventas es un extra
        // de planes altos). Por eso, para Genérico estos módulos están
        // disponibles desde el plan Start, sin pasar por el gate de plan.
        if (tenant('tipo_negocio') === 'generico' && in_array($module, ['productos', 'inventario', 'compras', 'ventas'], true)) {
            return true;
        }

        $modules = tenant('modules');

        if (is_array($modules) && array_key_exists($module, $modules)) {
            return (bool) $modules[$module];
        }

        $plan = $tenant->plan ?? 'start';

        return (bool) (saas_plans_config(tenant('tipo_negocio'))[$plan]['data']['modules'][$module] ?? false);
    }
}

if (! function_exists('tenant_problemas_facturacion')) {

    /**
     * Qué le falta al tenant actual para poder emitir boletas y facturas
     * electrónicas. Arreglo vacío = está listo.
     *
     * @return string[]
     */
    function tenant_problemas_facturacion(): array
    {
        $empresa = \App\Models\Tenant\EmpresaFacturacion::delTenantActual();

        if (! $empresa) {
            return ['Aun no se han registrado los datos de facturacion de la empresa.'];
        }

        $problemas = $empresa->problemasDeConfiguracion();

        // El domicilio, el codigo de local y las series viven en la sede: cada
        // una es un establecimiento anexo ante SUNAT. Se valida la sede con la
        // que se esta operando ahora mismo.
        $sedeId = tenant_caja_activa_almacen_id();

        $sede = $sedeId
            ? \App\Models\Tenant\Almacen::find($sedeId)
            : \App\Models\Tenant\Almacen::principal()->first();

        if (! $sede) {
            $problemas[] = 'No hay una sede configurada desde la cual emitir.';
        } else {
            $problemas = array_merge($problemas, $sede->problemasDeConfiguracion());
        }

        return $problemas;
    }
}

if (! function_exists('tenant_puede_facturar')) {

    /**
     * true cuando el tenant puede emitir comprobantes electrónicos. Se usa
     * para ocultar las opciones de Boleta y Factura en el punto de venta.
     */
    function tenant_puede_facturar(): bool
    {
        return tenant_problemas_facturacion() === [];
    }
}

if (! function_exists('tenant_facturacion_en_pruebas')) {

    /**
     * true cuando el tenant emite contra el ambiente de pruebas de SUNAT.
     *
     * Lo que se emite ahi no tiene validez tributaria, asi que el punto de
     * venta no debe ofrecer el comprobante como si fuera imprimible.
     */
    function tenant_facturacion_en_pruebas(): bool
    {
        return \App\Models\Tenant\EmpresaFacturacion::delTenantActual()?->esBeta() ?? true;
    }
}

if (! function_exists('tenant_tiene_certificado')) {

    /**
     * true cuando el tenant ya cargó su certificado digital y el archivo
     * existe. Se usa para mostrar la columna de SUNAT en la lista de ventas:
     * sin certificado no hay comprobantes electrónicos que revisar.
     */
    function tenant_tiene_certificado(): bool
    {
        return (bool) \App\Models\Tenant\EmpresaFacturacion::delTenantActual()?->rutaCertificado();
    }
}
