<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoGarantiaHero  extends Model
{
    protected $table = 'mantenimiento_garantia_hero';
    protected $primaryKey = 'MGH_Id';
    public $timestamps = false;
    protected $fillable = [
        'MGH_Placa',
        'MGH_Propietario',
        'MGH_celular',
        'MGH_Unidad',
        'MGH_KMEntrada',

        'MGH_DetalleIngreso',
        'MGH_DetalleObservacion',

        
    	'MGH_Det1',
    	'MGH_Det1Informacion',
    	'MGH_Det2',
    	'MGH_Det3',
    	'MGH_Det4',
    	'MGH_Det5',
    	'MGH_Det6',
    	'MGH_Det7',
    	'MGH_Det7Admision',
    	'MGH_Det7Escape',
    	'MGH_Det8',
    	'MGH_Det8Medida',
    	'MGH_Det9',
    	'MGH_Det10',
    	'MGH_Det11',
    	'MGH_Det11Vida',
    	'MGH_Det11Carga',
    	'MGH_Det11Arranque',

        'MGH_DetalleRealizado',
        'MGH_CorrecionObservacion',

        'MGH_ProximoCambioAceite',
        'MGH_ProximoServicio',

        'MGH_FechaCreacion',
        'MGH_FechaEdicion',
        'MGH_FechaInicio',
        'MGH_FechaTermino',

        'MGH_UsuarioCreacion',
        'MGH_UsuarioEditado',

        'MGH_Estado',

        'PER_Id',
        'PLAN_Id',
        'RES_Id',
        'MGH_AvisoMecanico',

        'statevalidate',
        'notificar',

        'observacion',
        'respuesta',
    ];

    protected $casts = [

       'MGH_FechaCreacion' => 'datetime',
        'MGH_FechaEdicion' => 'datetime',
        'MGH_FechaInicio' => 'datetime',
        'MGH_FechaTermino' => 'datetime',

        'statevalidate' => 'boolean',
        'MGH_AvisoMecanico' => 'boolean',
        'notificar' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function detallesReemplazo()
    {
        return $this->hasMany(
            MghDetalleReemplazo::class,
            'MGH_Id',
            'MGH_Id'
        );
    }

    public function imagenes()
    {
        return $this->hasMany(
            MghImagen::class,
            'MGH_Id',
            'MGH_Id'
        );
    }
}
