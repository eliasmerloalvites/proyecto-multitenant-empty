<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 🔑 GUARD CENTRAL (CLAVE)
        $guard = 'tenant';
        
        // 🔹 LIMPIAR CACHE DE SPATIE
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | PERMISOS - USUARIOS
        |--------------------------------------------------------------------------
        */
        $permissions = [

            // Usuarios
            ['seguridad.users.index',   'Usuarios', 'Ver Lista Usuarios',   'Listar Usuarios de la Aplicación'],
            ['seguridad.users.create',  'Usuarios', 'Crear Usuarios',       'Crear Usuarios de la Aplicación'],
            ['seguridad.users.edit',    'Usuarios', 'Editar Usuarios',      'Editar Usuarios de la Aplicación'],
            ['seguridad.users.show',    'Usuarios', 'Ver Detalle Usuario',  'Ver Detalle del Usuario'],
            ['seguridad.users.destroy', 'Usuarios', 'Eliminar Usuarios',    'Eliminar Usuarios de la Aplicación'],

            // Permisos
            ['seguridad.permiso.index',   'Permisos', 'Ver Lista Permisos',  'Listar Permisos'],
            ['seguridad.permiso.create',  'Permisos', 'Crear Permisos',      'Crear Permisos'],
            ['seguridad.permiso.edit',    'Permisos', 'Editar Permisos',     'Editar Permisos'],
            ['seguridad.permiso.show',    'Permisos', 'Ver Detalle Permiso', 'Ver Detalle del Permiso'],
            ['seguridad.permiso.destroy', 'Permisos', 'Eliminar Permisos',   'Eliminar Permisos'],

            // Roles
            ['seguridad.roles.index',   'Roles', 'Ver Lista Roles',  'Listar Roles'],
            ['seguridad.roles.create',  'Roles', 'Crear Roles',      'Crear Roles'],
            ['seguridad.roles.edit',    'Roles', 'Editar Roles',     'Editar Roles'],
            ['seguridad.roles.show',    'Roles', 'Ver Detalle Rol',  'Ver Detalle del Rol'],
            ['seguridad.roles.destroy', 'Roles', 'Eliminar Roles',   'Eliminar Roles'],

            // Clientes
            ['admin.clients.index',   'Clientes', 'Ver Lista Clientes', 'Listar Clientes'],
            ['admin.clients.create',  'Clientes', 'Crear Clientes',     'Crear Clientes'],
            ['admin.clients.edit',    'Clientes', 'Editar Clientes',    'Editar Clientes'],
            ['admin.clients.show',    'Clientes', 'Ver Cliente',        'Ver Cliente'],
            ['admin.clients.destroy', 'Clientes', 'Eliminar Clientes',  'Eliminar Clientes'],
        ];

        foreach ($permissions as [$name, $group, $nombre, $description]) {
            Permission::firstOrCreate(
                [
                    'name'       => $name,
                    'guard_name' => $guard, // 🔑 CLAVE
                ],
                [
                    'group_name' => $group,
                    'nombre'     => $nombre,
                    'description'=> $description,
                    'estado'     => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */
        $adminRole = Role::firstOrCreate([
            'name'       => 'Admin',
            'guard_name' => $guard, // 🔑 CLAVE
        ]);

        $gerenteRole = Role::firstOrCreate([
            'name'       => 'Gerente',
            'guard_name' => $guard,
        ]);

        /*
        |--------------------------------------------------------------------------
        | ASIGNAR PERMISOS
        |--------------------------------------------------------------------------
        */
        $adminRole->givePermissionTo([
            'seguridad.users.index',
            'seguridad.users.create',
            'seguridad.users.edit',
            'seguridad.users.show',
            'seguridad.users.destroy',

            'seguridad.permiso.index',
            'seguridad.permiso.create',
            'seguridad.permiso.edit',
            'seguridad.permiso.show',
            'seguridad.permiso.destroy',

            'seguridad.roles.index',
            'seguridad.roles.create',
            'seguridad.roles.edit',
            'seguridad.roles.show',
            'seguridad.roles.destroy',

            'admin.clients.index',
            'admin.clients.create',
            'admin.clients.edit',
            'admin.clients.show',
            'admin.clients.destroy',
        ]);

        $gerenteRole->givePermissionTo([
            'seguridad.users.index',
            'seguridad.users.create',
            'seguridad.users.edit',
            'seguridad.users.show',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ASIGNAR ROLES A USUARIOS
        |--------------------------------------------------------------------------
        */
        if ($user = User::find(1)) {
            $user->assignRole('Admin');
        }

    }
}
