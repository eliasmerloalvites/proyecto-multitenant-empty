<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permisos del panel "App movil" (aprobar vinculos de motos y generar
 * invitaciones). Idempotente. Se asignan a Admin, Gerente y Recepcion, que
 * son quienes atienden al cliente en el mostrador.
 */
return new class extends Migration
{
    private const GUARD = 'tenant';

    private array $permisos = [
        'tenant.appmovil.index' => ['Ver App movil', 'Ver las solicitudes de vinculo e invitaciones de la app'],
        'tenant.appmovil.vinculos' => ['Aprobar vinculos de motos', 'Aprobar o rechazar solicitudes de vinculo de motos desde la app'],
        'tenant.appmovil.invitaciones' => ['Generar invitaciones de la app', 'Generar codigos de invitacion para que un cliente vincule su moto'],
    ];

    private array $roles = ['Admin', 'Gerente', 'Recepcion'];

    public function up(): void
    {
        foreach ($this->permisos as $nombre => [$titulo, $descripcion]) {
            Permission::firstOrCreate(
                ['name' => $nombre, 'guard_name' => self::GUARD],
                ['group_name' => 'App movil', 'nombre' => $titulo, 'description' => $descripcion, 'estado' => true]
            );
        }

        foreach ($this->roles as $rolNombre) {
            $rol = Role::where('name', $rolNombre)->where('guard_name', self::GUARD)->first();
            $rol?->givePermissionTo(array_keys($this->permisos));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::whereIn('name', array_keys($this->permisos))->where('guard_name', self::GUARD)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
