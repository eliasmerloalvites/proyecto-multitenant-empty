<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Configuracion de la app movil del taller: si los clientes ven el precio de
 * los repuestos en el detalle de su orden (por defecto SI), y el permiso para
 * cambiarlo (Admin y Gerente).
 */
return new class extends Migration
{
    private const GUARD = 'tenant';

    public function up(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->boolean('app_movil_mostrar_precios')->default(true);
        });

        Permission::firstOrCreate(
            ['name' => 'tenant.appmovil.config', 'guard_name' => self::GUARD],
            [
                'group_name' => 'App movil',
                'nombre' => 'Configurar la app movil',
                'description' => 'Elegir que informacion ven los clientes en la app (por ejemplo, el precio de los repuestos)',
                'estado' => true,
            ]
        );

        foreach (['Admin', 'Gerente'] as $rolNombre) {
            Role::where('name', $rolNombre)->where('guard_name', self::GUARD)->first()?->givePermissionTo('tenant.appmovil.config');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->dropColumn('app_movil_mostrar_precios');
        });

        Permission::where('name', 'tenant.appmovil.config')->where('guard_name', self::GUARD)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
