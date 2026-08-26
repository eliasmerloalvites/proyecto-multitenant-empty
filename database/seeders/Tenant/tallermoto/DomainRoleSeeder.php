<?php

namespace Database\Seeders\Tenant\tallermoto;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DomainRoleSeeder extends Seeder
{
    private const GUARD = 'tenant';

    /**
     * Roles operativos por dominio de negocio. Cada uno recibe solo
     * index/create/edit/show (+ aprobar/notificar donde aplique) de su
     * propio módulo — eliminar queda reservado para Admin/Gerente.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'Compras' => [
                'tenant.compras.tipogasto.index',
                'tenant.compras.tipogasto.create',
                'tenant.compras.tipogasto.edit',
                'tenant.compras.tipogasto.show',

                'tenant.compras.proveedor.index',
                'tenant.compras.proveedor.create',
                'tenant.compras.proveedor.edit',
                'tenant.compras.proveedor.show',

                'tenant.compras.gasto.index',
                'tenant.compras.gasto.create',
                'tenant.compras.gasto.edit',
                'tenant.compras.gasto.show',

                'tenant.compras.compra.index',
                'tenant.compras.compra.create',
                'tenant.compras.compra.edit',
                'tenant.compras.compra.show',
            ],

            'Ventas' => [
                'tenant.ventas.metodopago.index',
                'tenant.ventas.metodopago.create',
                'tenant.ventas.metodopago.edit',
                'tenant.ventas.metodopago.show',

                'tenant.ventas.caja.index',
                'tenant.ventas.caja.create',
                'tenant.ventas.caja.edit',
                'tenant.ventas.caja.show',

                'tenant.ventas.cliente.index',
                'tenant.ventas.cliente.create',
                'tenant.ventas.cliente.edit',
                'tenant.ventas.cliente.show',

                'tenant.ventas.venta.index',
                'tenant.ventas.venta.create',
                'tenant.ventas.venta.edit',
                'tenant.ventas.venta.show',

                'tenant.ventas.guiaremision.index',
                'tenant.ventas.guiaremision.create',
                'tenant.ventas.guiaremision.show',
            ],

            'Mantenimiento' => [
                'tenant.actividades.mantenimientoactividadvariada.index',
                'tenant.actividades.mantenimientoactividadvariada.create',
                'tenant.actividades.mantenimientoactividadvariada.edit',
                'tenant.actividades.mantenimientoactividadvariada.show',
                'tenant.actividades.mantenimientoactividadvariada.aprobar',
                'tenant.actividades.mantenimientoactividadvariada.notificar',

                'tenant.mantenimientos.preventivocarburada.index',
                'tenant.mantenimientos.preventivocarburada.create',
                'tenant.mantenimientos.preventivocarburada.edit',
                'tenant.mantenimientos.preventivocarburada.show',
                'tenant.mantenimientos.preventivocarburada.aprobar',
                'tenant.mantenimientos.preventivocarburada.notificar',

                'tenant.mantenimientos.preventivoinyectada.index',
                'tenant.mantenimientos.preventivoinyectada.create',
                'tenant.mantenimientos.preventivoinyectada.edit',
                'tenant.mantenimientos.preventivoinyectada.show',
                'tenant.mantenimientos.preventivoinyectada.aprobar',
                'tenant.mantenimientos.preventivoinyectada.notificar',

                'tenant.mantenimientos.generalcarburada.index',
                'tenant.mantenimientos.generalcarburada.create',
                'tenant.mantenimientos.generalcarburada.edit',
                'tenant.mantenimientos.generalcarburada.show',
                'tenant.mantenimientos.generalcarburada.aprobar',
                'tenant.mantenimientos.generalcarburada.notificar',

                'tenant.mantenimientos.generalinyectada.index',
                'tenant.mantenimientos.generalinyectada.create',
                'tenant.mantenimientos.generalinyectada.edit',
                'tenant.mantenimientos.generalinyectada.show',
                'tenant.mantenimientos.generalinyectada.aprobar',
                'tenant.mantenimientos.generalinyectada.notificar',
            ],

            'Reservas' => [
                'tenant.reservaciones.administracion.index',
                'tenant.reservaciones.administracion.create',
                'tenant.reservaciones.administracion.edit',
                'tenant.reservaciones.administracion.show',
                'tenant.reservaciones.administracion.aprobar',
                'tenant.reservaciones.administracion.notificar',
            ],

            'Inventario' => [
                'tenant.inventario.clase.index',
                'tenant.inventario.clase.create',
                'tenant.inventario.clase.edit',
                'tenant.inventario.clase.show',

                'tenant.inventario.categoria.index',
                'tenant.inventario.categoria.create',
                'tenant.inventario.categoria.edit',
                'tenant.inventario.categoria.show',

                'tenant.inventario.producto.index',
                'tenant.inventario.producto.create',
                'tenant.inventario.producto.edit',
                'tenant.inventario.producto.show',
            ],

            'Reportes' => [
                'tenant.reportes.listageneral',
                'tenant.reportes.rendimientomecanicos',
            ],

            // Técnico que ejecuta y llena los reportes de mantenimiento en
            // taller. Puede registrar/editar su propio trabajo y consultar
            // repuestos y el reporte general, pero no aprobar, notificar al
            // cliente ni eliminar registros — eso queda para Admin/Gerente.
            'Mecanico' => [
                'tenant.actividades.mantenimientoactividadvariada.index',
                'tenant.actividades.mantenimientoactividadvariada.create',
                'tenant.actividades.mantenimientoactividadvariada.edit',
                'tenant.actividades.mantenimientoactividadvariada.show',

                'tenant.mantenimientos.preventivocarburada.index',
                'tenant.mantenimientos.preventivocarburada.create',
                'tenant.mantenimientos.preventivocarburada.edit',
                'tenant.mantenimientos.preventivocarburada.show',

                'tenant.mantenimientos.preventivoinyectada.index',
                'tenant.mantenimientos.preventivoinyectada.create',
                'tenant.mantenimientos.preventivoinyectada.edit',
                'tenant.mantenimientos.preventivoinyectada.show',

                'tenant.mantenimientos.generalcarburada.index',
                'tenant.mantenimientos.generalcarburada.create',
                'tenant.mantenimientos.generalcarburada.edit',
                'tenant.mantenimientos.generalcarburada.show',

                'tenant.mantenimientos.generalinyectada.index',
                'tenant.mantenimientos.generalinyectada.create',
                'tenant.mantenimientos.generalinyectada.edit',
                'tenant.mantenimientos.generalinyectada.show',

                'tenant.inventario.clase.index',
                'tenant.inventario.clase.show',

                'tenant.inventario.categoria.index',
                'tenant.inventario.categoria.show',

                'tenant.inventario.producto.index',
                'tenant.inventario.producto.show',

                'tenant.reportes.listageneral',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => self::GUARD,
            ]);

            $role->syncPermissions($permissions);
        }
    }
}
