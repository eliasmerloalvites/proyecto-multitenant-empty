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
            
            // Almacen || Sede
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

            // Turno
            ['tenant.configuracion.turno.index',   'Turnos', 'Ver Lista Turnos', 'Listar Turnos'],
            ['tenant.configuracion.turno.create',  'Turnos', 'Crear Turnos',     'Crear Turnos'],
            ['tenant.configuracion.turno.edit',    'Turnos', 'Editar Turnos',    'Editar Turnos'],
            ['tenant.configuracion.turno.show',    'Turnos', 'Ver Turno',        'Ver Turno'],
            ['tenant.configuracion.turno.destroy', 'Turnos', 'Eliminar Turnos',  'Eliminar Turnos'],

            // Bahia
            ['tenant.configuracion.bahia.index',   'Bahias', 'Ver Lista Bahias', 'Listar Bahias'],
            ['tenant.configuracion.bahia.create',  'Bahias', 'Crear Bahias',     'Crear Bahias'],
            ['tenant.configuracion.bahia.edit',    'Bahias', 'Editar Bahias',    'Editar Bahias'],
            ['tenant.configuracion.bahia.show',    'Bahias', 'Ver Bahia',        'Ver Bahia'],
            ['tenant.configuracion.bahia.destroy', 'Bahias', 'Eliminar Bahias',  'Eliminar Bahias'],

            
            // Horario
            ['tenant.configuracion.horario.index',   'Horarios', 'Ver Lista Horarios', 'Listar Horarios'],
            ['tenant.configuracion.horario.create',  'Horarios', 'Crear Horarios',     'Crear Horarios'],
            ['tenant.configuracion.horario.edit',    'Horarios', 'Editar Horarios',    'Editar Horarios'],
            ['tenant.configuracion.horario.show',    'Horarios', 'Ver Horario',        'Ver Horario'],
            ['tenant.configuracion.horario.destroy', 'Horarios', 'Eliminar Horarios',  'Eliminar Horarios'],

            
            
            // Mtto Preventivo Carburada
            ['tenant.actividades.mantenimientoactividadvariada.index',   'Actividad Variadas', 'Ver Lista Actividad Variadas', 'Listar Actividad Variadas'],
            ['tenant.actividades.mantenimientoactividadvariada.create',  'Actividad Variadas', 'Crear Actividad Variadas',     'Crear Actividad Variadas'],
            ['tenant.actividades.mantenimientoactividadvariada.edit',    'Actividad Variadas', 'Editar Actividad Variadas',    'Editar Actividad Variadas'],
            ['tenant.actividades.mantenimientoactividadvariada.show',    'Actividad Variadas', 'Ver Actividad Variada',        'Ver Actividad Variada'],
            ['tenant.actividades.mantenimientoactividadvariada.destroy', 'Actividad Variadas', 'Eliminar Actividad Variadas',  'Eliminar Actividad Variadas'],
            ['tenant.actividades.mantenimientoactividadvariada.aprobar', 'Actividad Variadas', 'Aprobar Actividad Variadas',  'Aprobar Actividad Variadas'],
            ['tenant.actividades.mantenimientoactividadvariada.notificar', 'Actividad Variadas', 'Notificar Actividad Variadas',  'Notificar Actividad Variadas'],

            // Mantenimiento Preventivo Carburada
            ['tenant.mantenimientos.preventivocarburada.index',   'Mtto Preventivo Carburadas', 'Ver Lista Mtto Preventivo Carburadas', 'Listar Mtto Preventivo Carburadas'],
            ['tenant.mantenimientos.preventivocarburada.create',  'Mtto Preventivo Carburadas', 'Crear Mtto Preventivo Carburadas',     'Crear Mtto Preventivo Carburadas'],
            ['tenant.mantenimientos.preventivocarburada.edit',    'Mtto Preventivo Carburadas', 'Editar Mtto Preventivo Carburadas',    'Editar Mtto Preventivo Carburadas'],
            ['tenant.mantenimientos.preventivocarburada.show',    'Mtto Preventivo Carburadas', 'Ver Mtto Preventivo Carburada',        'Ver Mtto Preventivo Carburada'],
            ['tenant.mantenimientos.preventivocarburada.destroy', 'Mtto Preventivo Carburadas', 'Eliminar Mtto Preventivo Carburadas',  'Eliminar Mtto Preventivo Carburadas'],
            ['tenant.mantenimientos.preventivocarburada.aprobar', 'Mtto Preventivo Carburadas', 'Aprobar Mtto Preventivo Carburadas',  'Aprobar Mtto Preventivo Carburadas'],
            ['tenant.mantenimientos.preventivocarburada.notificar', 'Mtto Preventivo Carburadas', 'Notificar Mtto Preventivo Carburadas',  'Notificar Mtto Preventivo Carburadas'],

            // Mantenimiento Preventivo Inyectada
            ['tenant.mantenimientos.preventivoinyectada.index',   'Mtto Preventivo Inyectadas', 'Ver Lista Mtto Preventivo Inyectadas', 'Listar Mtto Preventivo Inyectadas'],
            ['tenant.mantenimientos.preventivoinyectada.create',  'Mtto Preventivo Inyectadas', 'Crear Mtto Preventivo Inyectadas',     'Crear Mtto Preventivo Inyectadas'],
            ['tenant.mantenimientos.preventivoinyectada.edit',    'Mtto Preventivo Inyectadas', 'Editar Mtto Preventivo Inyectadas',    'Editar Mtto Preventivo Inyectadas'],
            ['tenant.mantenimientos.preventivoinyectada.show',    'Mtto Preventivo Inyectadas', 'Ver Mtto Preventivo Inyectada',        'Ver Mtto Preventivo Inyectada'],
            ['tenant.mantenimientos.preventivoinyectada.destroy', 'Mtto Preventivo Inyectadas', 'Eliminar Mtto Preventivo Inyectadas',  'Eliminar Mtto Preventivo Inyectadas'],
            ['tenant.mantenimientos.preventivoinyectada.aprobar', 'Mtto Preventivo Inyectadas', 'Aprobar Mtto Preventivo Inyectadas',  'Aprobar Mtto Preventivo Inyectadas'],
            ['tenant.mantenimientos.preventivoinyectada.notificar', 'Mtto Preventivo Inyectadas', 'Notificar Mtto Preventivo Inyectadas',  'Notificar Mtto Preventivo Inyectadas'],
            
            // Mantenimiento General Carburada
            ['tenant.mantenimientos.generalcarburada.index',   'Mtto General Carburadas', 'Ver Lista Mtto General Carburadas', 'Listar Mtto General Carburadas'],
            ['tenant.mantenimientos.generalcarburada.create',  'Mtto General Carburadas', 'Crear Mtto General Carburadas',     'Crear Mtto General Carburadas'],
            ['tenant.mantenimientos.generalcarburada.edit',    'Mtto General Carburadas', 'Editar Mtto General Carburadas',    'Editar Mtto General Carburadas'],
            ['tenant.mantenimientos.generalcarburada.show',    'Mtto General Carburadas', 'Ver Mtto General Carburada',        'Ver Mtto General Carburada'],
            ['tenant.mantenimientos.generalcarburada.destroy', 'Mtto General Carburadas', 'Eliminar Mtto General Carburadas',  'Eliminar Mtto General Carburadas'],
            ['tenant.mantenimientos.generalcarburada.aprobar', 'Mtto General Carburadas', 'Aprobar Mtto General Carburadas',  'Aprobar Mtto General Carburadas'],
            ['tenant.mantenimientos.generalcarburada.notificar', 'Mtto General Carburadas', 'Notificar Mtto General Carburadas',  'Notificar Mtto General Carburadas'],

            // Mantenimiento General Inyectada
            ['tenant.mantenimientos.generalinyectada.index',   'Mtto General Inyectadas', 'Ver Lista Mtto General Inyectadas', 'Listar Mtto General Inyectadas'],
            ['tenant.mantenimientos.generalinyectada.create',  'Mtto General Inyectadas', 'Crear Mtto General Inyectadas',     'Crear Mtto General Inyectadas'],
            ['tenant.mantenimientos.generalinyectada.edit',    'Mtto General Inyectadas', 'Editar Mtto General Inyectadas',    'Editar Mtto General Inyectadas'],
            ['tenant.mantenimientos.generalinyectada.show',    'Mtto General Inyectadas', 'Ver Mtto General Inyectada',        'Ver Mtto General Inyectada'],
            ['tenant.mantenimientos.generalinyectada.destroy', 'Mtto General Inyectadas', 'Eliminar Mtto General Inyectadas',  'Eliminar Mtto General Inyectadas'],
            ['tenant.mantenimientos.generalinyectada.aprobar', 'Mtto General Inyectadas', 'Aprobar Mtto General Inyectadas',  'Aprobar Mtto General Inyectadas'],
            ['tenant.mantenimientos.generalinyectada.notificar', 'Mtto General Inyectadas', 'Notificar Mtto General Inyectadas',  'Notificar Mtto General Inyectadas'],
            
            // Reportes
            ['tenant.reportes.listageneral',   'Reportes', 'Ver Lista de General', 'Lista General de Mantenimientos'],

            // Reservaciones
            ['tenant.reservaciones.administracion.index',   'Mtto General Inyectadas', 'Ver Lista Mtto General Inyectadas', 'Listar Mtto General Inyectadas'],
            ['tenant.reservaciones.administracion.create',  'Mtto General Inyectadas', 'Crear Mtto General Inyectadas',     'Crear Mtto General Inyectadas'],
            ['tenant.reservaciones.administracion.edit',    'Mtto General Inyectadas', 'Editar Mtto General Inyectadas',    'Editar Mtto General Inyectadas'],
            ['tenant.reservaciones.administracion.show',    'Mtto General Inyectadas', 'Ver Mtto General Inyectada',        'Ver Mtto General Inyectada'],
            ['tenant.reservaciones.administracion.destroy', 'Mtto General Inyectadas', 'Eliminar Mtto General Inyectadas',  'Eliminar Mtto General Inyectadas'],
            ['tenant.reservaciones.administracion.aprobar', 'Mtto General Inyectadas', 'Aprobar Mtto General Inyectadas',  'Aprobar Mtto General Inyectadas'],
            ['tenant.reservaciones.administracion.notificar', 'Mtto General Inyectadas', 'Notificar Mtto General Inyectadas',  'Notificar Mtto General Inyectadas'],
            
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

            'tenant.configuracion.turno.index',
            'tenant.configuracion.turno.create',
            'tenant.configuracion.turno.edit',
            'tenant.configuracion.turno.show',
            'tenant.configuracion.turno.destroy',

            'tenant.configuracion.bahia.index',
            'tenant.configuracion.bahia.create',
            'tenant.configuracion.bahia.edit',
            'tenant.configuracion.bahia.show',
            'tenant.configuracion.bahia.destroy',
            
            'tenant.configuracion.horario.index',
            'tenant.configuracion.horario.create',
            'tenant.configuracion.horario.edit',
            'tenant.configuracion.horario.show',
            'tenant.configuracion.horario.destroy',

            'tenant.actividades.mantenimientoactividadvariada.index',
            'tenant.actividades.mantenimientoactividadvariada.create',
            'tenant.actividades.mantenimientoactividadvariada.edit',
            'tenant.actividades.mantenimientoactividadvariada.show',
            'tenant.actividades.mantenimientoactividadvariada.destroy',
            'tenant.actividades.mantenimientoactividadvariada.aprobar',
            'tenant.actividades.mantenimientoactividadvariada.notificar',

            'tenant.mantenimientos.preventivocarburada.index',
            'tenant.mantenimientos.preventivocarburada.create',
            'tenant.mantenimientos.preventivocarburada.edit',
            'tenant.mantenimientos.preventivocarburada.show',
            'tenant.mantenimientos.preventivocarburada.destroy',
            'tenant.mantenimientos.preventivocarburada.aprobar',
            'tenant.mantenimientos.preventivocarburada.notificar',

            'tenant.mantenimientos.preventivoinyectada.index',
            'tenant.mantenimientos.preventivoinyectada.create',
            'tenant.mantenimientos.preventivoinyectada.edit',
            'tenant.mantenimientos.preventivoinyectada.show',
            'tenant.mantenimientos.preventivoinyectada.destroy',
            'tenant.mantenimientos.preventivoinyectada.aprobar',
            'tenant.mantenimientos.preventivoinyectada.notificar',

            'tenant.mantenimientos.generalcarburada.index',
            'tenant.mantenimientos.generalcarburada.create',
            'tenant.mantenimientos.generalcarburada.edit',
            'tenant.mantenimientos.generalcarburada.show',
            'tenant.mantenimientos.generalcarburada.destroy',
            'tenant.mantenimientos.generalcarburada.aprobar',
            'tenant.mantenimientos.generalcarburada.notificar',

            'tenant.mantenimientos.generalinyectada.index',
            'tenant.mantenimientos.generalinyectada.create',
            'tenant.mantenimientos.generalinyectada.edit',
            'tenant.mantenimientos.generalinyectada.show',
            'tenant.mantenimientos.generalinyectada.destroy',
            'tenant.mantenimientos.generalinyectada.aprobar',
            'tenant.mantenimientos.generalinyectada.notificar',

            'tenant.reportes.listageneral',

            'tenant.reservaciones.administracion.index',
            'tenant.reservaciones.administracion.create',
            'tenant.reservaciones.administracion.edit',
            'tenant.reservaciones.administracion.show',
            'tenant.reservaciones.administracion.destroy'
            
        ]);

        $gerenteRole->givePermissionTo([
            'tenant.seguridad.users.index',
            'tenant.seguridad.users.create',
            'tenant.seguridad.users.edit',
            'tenant.seguridad.users.show',
            
            /* 'tenant.inventario.clase.index',
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
            'tenant.inventario.producto.destroy', */

            'tenant.configuracion.sede.index',
            'tenant.configuracion.sede.create',
            'tenant.configuracion.sede.edit',
            'tenant.configuracion.sede.show',
            'tenant.configuracion.sede.destroy',

            /* 'tenant.compras.tipogasto.index',
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
            'tenant.ventas.venta.destroy', */

            'tenant.configuracion.turno.index',
            'tenant.configuracion.turno.create',
            'tenant.configuracion.turno.edit',
            'tenant.configuracion.turno.show',
            'tenant.configuracion.turno.destroy',            

            'tenant.configuracion.bahia.index',
            'tenant.configuracion.bahia.create',
            'tenant.configuracion.bahia.edit',
            'tenant.configuracion.bahia.show',
            'tenant.configuracion.bahia.destroy',
            
            'tenant.configuracion.horario.index',
            'tenant.configuracion.horario.create',
            'tenant.configuracion.horario.edit',
            'tenant.configuracion.horario.show',
            'tenant.configuracion.horario.destroy',


            'tenant.actividades.mantenimientoactividadvariada.index',
            'tenant.actividades.mantenimientoactividadvariada.create',
            'tenant.actividades.mantenimientoactividadvariada.edit',
            'tenant.actividades.mantenimientoactividadvariada.show',
            'tenant.actividades.mantenimientoactividadvariada.destroy',
            'tenant.actividades.mantenimientoactividadvariada.aprobar',
            'tenant.actividades.mantenimientoactividadvariada.notificar',
            
            'tenant.mantenimientos.preventivocarburada.index',
            'tenant.mantenimientos.preventivocarburada.create',
            'tenant.mantenimientos.preventivocarburada.edit',
            'tenant.mantenimientos.preventivocarburada.show',
            'tenant.mantenimientos.preventivocarburada.destroy',
            'tenant.mantenimientos.preventivocarburada.aprobar',
            'tenant.mantenimientos.preventivocarburada.notificar',
            
            'tenant.mantenimientos.preventivoinyectada.index',
            'tenant.mantenimientos.preventivoinyectada.create',
            'tenant.mantenimientos.preventivoinyectada.edit',
            'tenant.mantenimientos.preventivoinyectada.show',
            'tenant.mantenimientos.preventivoinyectada.destroy',
            'tenant.mantenimientos.preventivoinyectada.aprobar',
            'tenant.mantenimientos.preventivoinyectada.notificar',
            
            'tenant.mantenimientos.generalcarburada.index',
            'tenant.mantenimientos.generalcarburada.create',
            'tenant.mantenimientos.generalcarburada.edit',
            'tenant.mantenimientos.generalcarburada.show',
            'tenant.mantenimientos.generalcarburada.destroy',
            'tenant.mantenimientos.generalcarburada.aprobar',
            'tenant.mantenimientos.generalcarburada.notificar',
            
            'tenant.mantenimientos.generalinyectada.index',
            'tenant.mantenimientos.generalinyectada.create',
            'tenant.mantenimientos.generalinyectada.edit',
            'tenant.mantenimientos.generalinyectada.show',
            'tenant.mantenimientos.generalinyectada.destroy',
            'tenant.mantenimientos.generalinyectada.aprobar',
            'tenant.mantenimientos.generalinyectada.notificar',

            'tenant.reportes.listageneral',
            
            'tenant.reservaciones.administracion.index',
            'tenant.reservaciones.administracion.create',
            'tenant.reservaciones.administracion.edit',
            'tenant.reservaciones.administracion.show',
            'tenant.reservaciones.administracion.destroy'
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
