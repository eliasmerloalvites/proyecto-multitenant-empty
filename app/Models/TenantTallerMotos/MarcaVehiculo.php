<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MarcaVehiculo extends Model
{
    protected $table = 'marca_vehiculo';
    protected $primaryKey = 'MAR_Id';

    protected $fillable = ['MAR_Nombre', 'MAR_Estado'];

    protected $casts = ['MAR_Estado' => 'boolean'];
}
