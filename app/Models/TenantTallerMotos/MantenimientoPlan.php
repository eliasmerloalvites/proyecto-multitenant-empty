<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoPlan extends Model
{
    protected $table = 'mantenimiento_plan';
    protected $primaryKey = 'PLAN_Id';

    protected $fillable = [
        'PLAN_Tipo',
        'PLAN_Nombre',
        'PLAN_Items',
        'PLAN_Activo',
    ];

    protected $casts = [
        'PLAN_Items' => 'array',
        'PLAN_Activo' => 'boolean',
    ];
}
