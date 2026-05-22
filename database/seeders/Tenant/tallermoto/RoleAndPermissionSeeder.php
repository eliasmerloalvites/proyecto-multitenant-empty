<?php

namespace Database\Seeders\Tenant\tallermoto;

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
            ['tenant.seguridad.users.index',   'Usuarios', 'Ver Lista Usuarios',   'Listar Usuarios de la Aplicación'],
            ['tenant.seguridad.users.create',  'Usuarios', 'Crear Usuarios',       'Crear Usuarios de la Aplicación'],
            ['tenant.seguridad.users.edit',    'Usuarios', 'Editar Usuarios',      'Editar Usuarios de la Aplicación'],
            ['tenant.seguridad.users.show',    'Usuarios', 'Ver Detalle Usuario',  'Ver Detalle del Usuario'],
            ['tenant.seguridad.users.destroy', 'Usuarios', 'Eliminar Usuarios',    'Eliminar Usuarios de la Aplicación'],

            // Permisos
            ['tenant.seguridad.permiso.index',   'Permisos', 'Ver Lista Permisos',  'Listar Permisos'],
            ['tenant.seguridad.permiso.create',  'Permisos', 'Crear Permisos',      'Crear Permisos'],
            ['tenant.seguridad.permiso.edit',    'Permisos', 'Editar Permisos',     'Editar Permisos'],
            ['tenant.seguridad.permiso.show',    'Permisos', 'Ver Detalle Permiso', 'Ver Detalle del Permiso'],
            ['tenant.seguridad.permiso.destroy', 'Permisos', 'Eliminar Permisos',   'Eliminar Permisos'],

            // Roles
            ['tenant.seguridad.roles.index',   'Roles', 'Ver Lista Roles',  'Listar Roles'],
            ['tenant.seguridad.roles.create',  'Roles', 'Crear Roles',      'Crear Roles'],
            ['tenant.seguridad.roles.edit',    'Roles', 'Editar Roles',     'Editar Roles'],
            ['tenant.seguridad.roles.show',    'Roles', 'Ver Detalle Rol',  'Ver Detalle del Rol'],
            ['tenant.seguridad.roles.destroy', 'Roles', 'Eliminar Roles',   'Eliminar Roles'],

            // Clases
            ['tenant.inventario.clase.index',   'Clases', 'Ver Lista Clases', 'Listar Clases'],
            ['tenant.inventario.clase.create',  'Clases', 'Crear Clases',     'Crear Clases'],
            ['tenant.inventario.clase.edit',    'Clases', 'Editar Clases',    'Editar Clases'],
            ['tenant.inventario.clase.show',    'Clases', 'Ver Clase',        'Ver Clase'],
            ['tenant.inventario.clase.destroy', 'Clases', 'Eliminar Clases',  'Eliminar Clases'],
            
            // Categoria
            ['tenant.inventario.categoria.index',   'Categorias', 'Ver Lista Categorias', 'Listar Categorias'],
            ['tenant.inventario.categoria.create',  'Categorias', 'Crear Categorias',     'Crear Categorias'],
            ['tenant.inventario.categoria.edit',    'Categorias', 'Editar Categorias',    'Editar Categorias'],
            ['tenant.inventario.categoria.show',    'Categorias', 'Ver Categoria',        'Ver Categoria'],
            ['tenant.inventario.categoria.destroy', 'Categorias', 'Eliminar Categorias',  'Eliminar Categorias'],

            // Producto
            ['tenant.inventario.producto.index',   'Productos', 'Ver Lista Productos', 'Listar Productos'],
            ['tenant.inventario.producto.create',  'Productos', 'Crear Productos',     'Crear Productos'],
            ['tenant.inventario.producto.edit',    'Productos', 'Editar Productos',    'Editar Productos'],
            ['tenant.inventario.producto.show',    'Productos', 'Ver Producto',        'Ver Producto'],
            ['tenant.inventario.producto.destroy', 'Productos', 'Eliminar Productos',  'Eliminar Productos'],
            
            // Almacen
            ['tenant.configuracion.sede.index',   'Sedes', 'Ver Lista Sedes', 'Listar Sedes'],
            ['tenant.configuracion.sede.create',  'Sedes', 'Crear Sedes',     'Crear Sedes'],
            ['tenant.configuracion.sede.edit',    'Sedes', 'Editar Sedes',    'Editar Sedes'],
            ['tenant.configuracion.sede.show',    'Sedes', 'Ver Sede',        'Ver Sede'],
            ['tenant.configuracion.sede.destroy', 'Sedes', 'Eliminar Sedes',  'Eliminar Sedes'],
            
            // Tipo Gasto
            ['tenant.compras.tipogasto.index',   'TipoGastos', 'Ver Lista TipoGastos', 'Listar TipoGastos'],
            ['tenant.compras.tipogasto.create',  'TipoGastos', 'Crear TipoGastos',     'Crear TipoGastos'],
            ['tenant.compras.tipogasto.edit',    'TipoGastos', 'Editar TipoGastos',    'Editar TipoGastos'],
            ['tenant.compras.tipogasto.show',    'TipoGastos', 'Ver TipoGasto',        'Ver TipoGasto'],
            ['tenant.compras.tipogasto.destroy', 'TipoGastos', 'Eliminar TipoGastos',  'Eliminar TipoGastos'],   

            //Metodo Pago
            ['tenant.ventas.metodopago.index',   'MetodoPagos', 'Ver Lista MetodoPagos', 'Listar MetodoPagos'],
            ['tenant.ventas.metodopago.create',  'MetodoPagos', 'Crear MetodoPagos',     'Crear MetodoPagos'],
            ['tenant.ventas.metodopago.edit',    'MetodoPagos', 'Editar MetodoPagos',    'Editar MetodoPagos'],
            ['tenant.ventas.metodopago.show',    'MetodoPagos', 'Ver MetodoPago',        'Ver MetodoPago'],
            ['tenant.ventas.metodopago.destroy', 'MetodoPagos', 'Eliminar MetodoPagos',  'Eliminar MetodoPagos'],

            //Proveedor
            ['tenant.compras.proveedor.index',   'Proveedores', 'Ver Lista Proveedores', 'Listar Proveedores'],
            ['tenant.compras.proveedor.create',  'Proveedores', 'Crear Proveedores',     'Crear Proveedores'],
            ['tenant.compras.proveedor.edit',    'Proveedores', 'Editar Proveedores',    'Editar Proveedores'],
            ['tenant.compras.proveedor.show',    'Proveedores', 'Ver Proveedor',        'Ver Proveedor'],
            ['tenant.compras.proveedor.destroy', 'Proveedores', 'Eliminar Proveedores',  'Eliminar Proveedores'],

            //Gastos
            ['tenant.compras.gasto.index',   'Gastos', 'Ver Lista Gastos', 'Listar Gastos'],
            ['tenant.compras.gasto.create',  'Gastos', 'Crear Gastos',     'Crear Gastos'],
            ['tenant.compras.gasto.edit',    'Gastos', 'Editar Gastos',    'Editar Gastos'],
            ['tenant.compras.gasto.show',    'Gastos', 'Ver Gasto',        'Ver Gasto'],
            ['tenant.compras.gasto.destroy', 'Gastos', 'Eliminar Gastos',  'Eliminar Gastos'],

            //Clientes
            ['tenant.ventas.cliente.index',   'Clientes', 'Ver Lista Clientes', 'Listar Clientes'],
            ['tenant.ventas.cliente.create',  'Clientes', 'Crear Clientes',     'Crear Clientes'],
            ['tenant.ventas.cliente.edit',    'Clientes', 'Editar Clientes',    'Editar Clientes'],
            ['tenant.ventas.cliente.show',    'Clientes', 'Ver Cliente',        'Ver Cliente'],
            ['tenant.ventas.cliente.destroy', 'Clientes', 'Eliminar Clientes',  'Eliminar Clientes'],

            //Compras
            ['tenant.compras.compra.index',   'Compras', 'Ver Lista Compras', 'Listar Compras'],
            ['tenant.compras.compra.create',  'Compras', 'Crear Compras',     'Crear Compras'],
            ['tenant.compras.compra.edit',    'Compras', 'Editar Compras',    'Editar Compras'],
            ['tenant.compras.compra.show',    'Compras', 'Ver Compra',        'Ver Compra'],
            ['tenant.compras.compra.destroy', 'Compras', 'Eliminar Compras',  'Eliminar Compras'],

            //Ventas
            ['tenant.ventas.venta.index',   'Ventas', 'Ver Lista Ventas', 'Listar Ventas'],
            ['tenant.ventas.venta.create',  'Ventas', 'Crear Ventas',     'Crear Ventas'],
            ['tenant.ventas.venta.edit',    'Ventas', 'Editar Ventas',    'Editar Ventas'],
            ['tenant.ventas.venta.show',    'Ventas', 'Ver Venta',        'Ver Venta'],
            ['tenant.ventas.venta.destroy', 'Ventas', 'Eliminar Ventas',  'Eliminar Ventas'],
            
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
            'tenant.seguridad.users.index',
            'tenant.seguridad.users.create',
            'tenant.seguridad.users.edit',
            'tenant.seguridad.users.show',
            'tenant.seguridad.users.destroy',

            'tenant.seguridad.permiso.index',
            'tenant.seguridad.permiso.create',
            'tenant.seguridad.permiso.edit',
            'tenant.seguridad.permiso.show',
            'tenant.seguridad.permiso.destroy',

            'tenant.seguridad.roles.index',
            'tenant.seguridad.roles.create',
            'tenant.seguridad.roles.edit',
            'tenant.seguridad.roles.show',
            'tenant.seguridad.roles.destroy',

            'tenant.inventario.clase.index',
            'tenant.inventario.clase.create',
            'tenant.inventario.clase.edit',
            'tenant.inventario.clase.show',
            'tenant.inventario.clase.destroy',

            'tenant.inventario.categoria.index',
            'tenant.inventario.categoria.create',
            'tenant.inventario.categoria.edit',
            'tenant.inventario.categoria.show',
            'tenant.inventario.categoria.destroy',

            'tenant.inventario.producto.index',
            'tenant.inventario.producto.create',
            'tenant.inventario.producto.edit',
            'tenant.inventario.producto.show',
            'tenant.inventario.producto.destroy',

            'tenant.configuracion.sede.index',
            'tenant.configuracion.sede.create',
            'tenant.configuracion.sede.edit',
            'tenant.configuracion.sede.show',
            'tenant.configuracion.sede.destroy',

            'tenant.compras.tipogasto.index',
            'tenant.compras.tipogasto.create',
            'tenant.compras.tipogasto.edit',
            'tenant.compras.tipogasto.show',
            'tenant.compras.tipogasto.destroy',

            'tenant.compras.proveedor.index',
            'tenant.compras.proveedor.create',
            'tenant.compras.proveedor.edit',
            'tenant.compras.proveedor.show',
            'tenant.compras.proveedor.destroy',

            'tenant.compras.gasto.index',
            'tenant.compras.gasto.create',
            'tenant.compras.gasto.edit',
            'tenant.compras.gasto.show',
            'tenant.compras.gasto.destroy',

            'tenant.ventas.metodopago.index',
            'tenant.ventas.metodopago.create',  
            'tenant.ventas.metodopago.edit',
            'tenant.ventas.metodopago.show',
            'tenant.ventas.metodopago.destroy',

            'tenant.ventas.cliente.index',
            'tenant.ventas.cliente.create',  
            'tenant.ventas.cliente.edit',
            'tenant.ventas.cliente.show',
            'tenant.ventas.cliente.destroy',

            'tenant.compras.compra.index',
            'tenant.compras.compra.create',
            'tenant.compras.compra.edit',
            'tenant.compras.compra.show',
            'tenant.compras.compra.destroy',

            'tenant.ventas.venta.index',
            'tenant.ventas.venta.create',  
            'tenant.ventas.venta.edit',
            'tenant.ventas.venta.show',
            'tenant.ventas.venta.destroy',

        ]);

        $gerenteRole->givePermissionTo([
            'tenant.seguridad.users.index',
            'tenant.seguridad.users.create',
            'tenant.seguridad.users.edit',
            'tenant.seguridad.users.show',
            
            'tenant.inventario.clase.index',
            'tenant.inventario.clase.create',
            'tenant.inventario.clase.edit',
            'tenant.inventario.clase.show',
            'tenant.inventario.clase.destroy',

            'tenant.inventario.categoria.index',
            'tenant.inventario.categoria.create',
            'tenant.inventario.categoria.edit',
            'tenant.inventario.categoria.show',

            'tenant.inventario.producto.index',
            'tenant.inventario.producto.create',
            'tenant.inventario.producto.edit',
            'tenant.inventario.producto.show',
            'tenant.inventario.producto.destroy',

            'tenant.configuracion.sede.index',
            'tenant.configuracion.sede.create',
            'tenant.configuracion.sede.edit',
            'tenant.configuracion.sede.show',
            'tenant.configuracion.sede.destroy',

            'tenant.compras.tipogasto.index',
            'tenant.compras.tipogasto.create',
            'tenant.compras.tipogasto.edit',
            'tenant.compras.tipogasto.show',
            'tenant.compras.tipogasto.destroy',

            'tenant.ventas.metodopago.index',
            'tenant.ventas.metodopago.create',  
            'tenant.ventas.metodopago.edit',
            'tenant.ventas.metodopago.show',
            'tenant.ventas.metodopago.destroy',

            'tenant.compras.proveedor.index',
            'tenant.compras.proveedor.create',
            'tenant.compras.proveedor.edit',
            'tenant.compras.proveedor.show',
            'tenant.compras.proveedor.destroy',

            'tenant.compras.gasto.index',
            'tenant.compras.gasto.create',
            'tenant.compras.gasto.edit',
            'tenant.compras.gasto.show',
            'tenant.compras.gasto.destroy',

            'tenant.ventas.cliente.index',
            'tenant.ventas.cliente.create',  
            'tenant.ventas.cliente.edit',
            'tenant.ventas.cliente.show',
            'tenant.ventas.cliente.destroy',

            'tenant.compras.compra.index',
            'tenant.compras.compra.create',
            'tenant.compras.compra.edit',
            'tenant.compras.compra.show',
            'tenant.compras.compra.destroy',

            'tenant.ventas.venta.index',
            'tenant.ventas.venta.create',  
            'tenant.ventas.venta.edit',
            'tenant.ventas.venta.show',
            'tenant.ventas.venta.destroy',

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
