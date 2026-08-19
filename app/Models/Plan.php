<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Plan extends Model
{
    // Este modelo se consulta también desde contexto tenant (tenant_has_module()
    // en app/helpers.php), donde la conexión por defecto ya está apuntando a la
    // BD del tenant. CentralConnection fuerza siempre la conexión central,
    // igual que hace el modelo Tenant, para que 'planes' se resuelva ahí.
    use CentralConnection;

    protected $table = 'planes';

    protected $fillable = [
        'key',
        'tipo_negocio',
        'nombre',
        'price',
        'max_users',
        'max_images',
        'storage_limit_mb',
        'custom_domain_enabled',
        'custom_branding',
        'customizable',
        'modules',
        'limits',
        'branding',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_users' => 'integer',
        'max_images' => 'integer',
        'storage_limit_mb' => 'integer',
        'custom_domain_enabled' => 'boolean',
        'custom_branding' => 'boolean',
        'customizable' => 'boolean',
        'modules' => 'array',
        'limits' => 'array',
        'branding' => 'array',
    ];

    /**
     * Módulos disponibles en el panel de un tenant, con su etiqueta legible.
     * Fuente única para no repetir esta lista en cada formulario/seeder.
     */
    public function scopeParaNegocio($query, string $tipoNegocio)
    {
        return $query->where('tipo_negocio', $tipoNegocio);
    }

    /**
     * Unión de todos los módulos que existen en cualquier vertical, usada
     * solo para validar el input del formulario (PlanController::update).
     */
    public const MODULOS = [
        'mantenimientos' => 'Mantenimientos (incluye Reservas)',
        'productos' => 'Productos',
        'inventario' => 'Inventario',
        'compras' => 'Compras',
        'ventas' => 'Ventas',
        'reports' => 'Reportes',
        'analytics' => 'Analíticas avanzadas',
        'api_access' => 'Acceso a API',
    ];

    /**
     * Módulos que sí tiene sentido activar/desactivar por plan en cada
     * vertical. 'productos'/'inventario'/'compras'/'ventas' no aparecen acá
     * para Genérico porque tenant_has_module() ya los fuerza a true siempre
     * en ese vertical (ver app/helpers.php) — ahí ES el negocio, no un
     * addon de planes altos como en Tallermoto. 'mantenimientos' tampoco
     * aplica a Genérico: es exclusivo del flujo de reservas de Tallermoto.
     */
    public const MODULOS_POR_NEGOCIO = [
        'tallermoto' => [
            'mantenimientos' => 'Mantenimientos (incluye Reservas)',
            'productos' => 'Productos',
            'inventario' => 'Inventario',
            'compras' => 'Compras',
            'ventas' => 'Ventas',
            'reports' => 'Reportes',
            'analytics' => 'Analíticas avanzadas',
            'api_access' => 'Acceso a API',
        ],
        'generico' => [
            'reports' => 'Reportes',
            'analytics' => 'Analíticas avanzadas',
            'api_access' => 'Acceso a API',
        ],
    ];

    public static function modulosPara(string $tipoNegocio): array
    {
        return self::MODULOS_POR_NEGOCIO[$tipoNegocio] ?? self::MODULOS;
    }

    /**
     * Representa el plan con la misma forma que antes tenía
     * config('saas.plans')[$key], para que el código que ya consumía ese
     * array (ClientController al crear/editar un cliente) siga funcionando
     * sin cambios estructurales.
     */
    public function toConfigArray(): array
    {
        return [
            'price' => (float) $this->price,
            'max_users' => $this->max_users,
            'max_images' => $this->max_images,
            'storage_limit_mb' => $this->storage_limit_mb,
            'custom_domain_enabled' => $this->custom_domain_enabled,
            'custom_branding' => $this->custom_branding,
            'data' => [
                'branding' => $this->branding ?? ['logo' => null, 'primary_color' => '#0B63CE'],
                'modules' => $this->modules ?? [],
                'limits' => $this->limits ?? [],
                'customizable' => $this->customizable,
            ],
        ];
    }
}
