<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoActividadVariada extends Model
{
    protected $table = 'mantenimiento_actividad_variadas';
    protected $primaryKey = 'MAV_Id';
    public $timestamps = false;
    protected $fillable = [
        'MAV_Placa',
        'MAV_Propietario',
        'MAV_celular',
        'MAV_Unidad',
        'MAV_KMEntrada',

        'MAV_DetalleIngreso',
        'MAV_DetalleObservacion',
        'MAV_DetalleRealizado',
        'MAV_CorrecionObservacion',

        'MAV_ProximoCambioAceite',
        'MAV_ProximoServicio',

        'MAV_FechaCreacion',
        'MAV_FechaEdicion',

        'MAV_UsuarioCreacion',
        'MAV_UsuarioEditado',

        'MAV_Estado',

        'USU_Id',

        'statevalidate',
        'notificar',

        'observacion',
        'respuesta',
    ];

    protected $casts = [

        'statevalidate' => 'boolean',
        'notificar' => 'boolean',

        'MAV_FechaCreacion' => 'datetime',
        'MAV_FechaEdicion' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function imagenes()
    {
        return $this->hasMany(
            MavImagen::class,
            'MAV_Id',
            'MAV_Id'
        );
    }

    public function detalleReemplazo()
    {
        return $this->hasMany(
            MavDetalleReemplazo::class,
            'MAV_Id',
            'MAV_Id'
        );
    }
}
