<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddPermisosSeguimiento extends Migration
{
    private const PERMISOS = [
        ['admin.seguimiento.index', 'Seguimiento', 'Ver Seguimiento', 'Ver el seguimiento comercial de clientes y pruebas'],
        ['admin.seguimiento.gestionar', 'Seguimiento', 'Gestionar Seguimiento', 'Enviar mensajes, cambiar etapas y registrar notas de seguimiento'],
    ];

    /**
     * Crea los permisos y se los da al rol Admin. Va como migracion (y no solo
     * en el seeder) para que en produccion basten las migraciones, sin volver
     * a correr el seeder de roles.
     */
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISOS as [$name, $group, $nombre, $description]) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'central'],
                ['group_name' => $group, 'nombre' => $nombre, 'description' => $description, 'estado' => true]
            );
        }

        $admin = Role::where('name', 'Admin')->where('guard_name', 'central')->first();
        $admin?->givePermissionTo(array_column(self::PERMISOS, 0));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::whereIn('name', array_column(self::PERMISOS, 0))->where('guard_name', 'central')->delete();
    }
}
