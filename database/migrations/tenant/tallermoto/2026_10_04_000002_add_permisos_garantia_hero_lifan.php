<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Registra los permisos de Garantia Hero / Garantia Lifan en tenants que ya
 * existian (los seeders solo corren al crear un tenant nuevo). Es idempotente.
 * Los permisos existen en todos los tenants tallermoto, pero el modulo solo se
 * ve y responde si el tenant lo tiene activado (tenant_has_module()).
 */
return new class extends Migration
{
    private const GUARD = 'tenant';

    private array $modulos = [
        'garantiahero' => 'Hero',
        'garantialifan' => 'Lifan',
    ];

    private array $acciones = [
        'index' => ['Ver Lista', 'Listar'],
        'create' => ['Crear', 'Crear'],
        'edit' => ['Editar', 'Editar'],
        'show' => ['Ver', 'Ver'],
        'destroy' => ['Eliminar', 'Eliminar'],
        'aprobar' => ['Aprobar', 'Aprobar'],
        'notificar' => ['Notificar', 'Notificar'],
    ];

    /** Roles que ya tienen Preventivo Carburada, y que acciones. */
    private array $rolesYAcciones = [
        'Admin' => ['index', 'create', 'edit', 'show', 'destroy', 'aprobar', 'notificar'],
        'Gerente' => ['index', 'create', 'edit', 'show', 'destroy', 'aprobar', 'notificar'],
        'Mantenimiento' => ['index', 'create', 'edit', 'show', 'aprobar', 'notificar'],
        'Mecanico' => ['index', 'create', 'edit', 'show'],
    ];

    public function up(): void
    {
        foreach ($this->modulos as $slug => $etiqueta) {
            foreach ($this->acciones as $accion => [$nombre, $descripcion]) {
                Permission::firstOrCreate(
                    ['name' => "tenant.mantenimientos.{$slug}.{$accion}", 'guard_name' => self::GUARD],
                    [
                        'group_name' => "Mtto Garantía {$etiqueta}",
                        'nombre' => "{$nombre} Mtto Garantía {$etiqueta}",
                        'description' => "{$descripcion} Mtto Garantía {$etiqueta}",
                        'estado' => true,
                    ]
                );
            }
        }

        foreach ($this->rolesYAcciones as $rolNombre => $acciones) {
            $rol = Role::where('name', $rolNombre)->where('guard_name', self::GUARD)->first();

            if (! $rol) {
                continue;
            }

            foreach (array_keys($this->modulos) as $slug) {
                foreach ($acciones as $accion) {
                    $rol->givePermissionTo("tenant.mantenimientos.{$slug}.{$accion}");
                }
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $nombres = [];
        foreach (array_keys($this->modulos) as $slug) {
            foreach (array_keys($this->acciones) as $accion) {
                $nombres[] = "tenant.mantenimientos.{$slug}.{$accion}";
            }
        }

        Permission::whereIn('name', $nombres)->where('guard_name', self::GUARD)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
