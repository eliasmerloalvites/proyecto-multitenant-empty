<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoGarantiaLifan  extends Model
{
    protected $table = 'mantenimiento_garantia_lifan';
    protected $primaryKey = 'MGL_Id';
    public $timestamps = false;
    protected $fillable = [
        'MGL_Placa',
        'MGL_Propietario',
        'MGL_celular',
        'MGL_Unidad',
        'MGL_KMEntrada',

        'MGL_DetalleIngreso',
        'MGL_DetalleObservacion',

        
    	'MGL_Det1',
    	'MGL_Det1Informacion',
    	'MGL_Det2',
    	'MGL_Det3',
    	'MGL_Det4',
    	'MGL_Det5',
    	'MGL_Det6',
    	'MGL_Det7',
    	'MGL_Det7Admision',
    	'MGL_Det7Escape',
    	'MGL_Det8',
    	'MGL_Det8Medida',
    	'MGL_Det9',
    	'MGL_Det10',
    	'MGL_Det11',
    	'MGL_Det11Vida',
    	'MGL_Det11Carga',
    	'MGL_Det11Arranque',

        'MGL_DetalleRealizado',
        'MGL_CorrecionObservacion',

        'MGL_ProximoCambioAceite',
        'MGL_ProximoServicio',

        'MGL_FechaCreacion',
        'MGL_FechaEdicion',
        'MGL_FechaInicio',
        'MGL_FechaTermino',

        'MGL_UsuarioCreacion',
        'MGL_UsuarioEditado',

        'MGL_Estado',

        'PER_Id',
        'PLAN_Id',
        'RES_Id',
        'MGL_AvisoMecanico',

        'statevalidate',
        'notificar',

        'observacion',
        'respuesta',
    ];

    protected $casts = [

       'MGL_FechaCreacion' => 'datetime',
        'MGL_FechaEdicion' => 'datetime',
        'MGL_FechaInicio' => 'datetime',
        'MGL_FechaTermino' => 'datetime',

        'statevalidate' => 'boolean',
        'MGL_AvisoMecanico' => 'boolean',
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
            MglDetalleReemplazo::class,
            'MGL_Id',
            'MGL_Id'
        );
    }

    public function imagenes()
    {
        return $this->hasMany(
            MglImagen::class,
            'MGL_Id',
            'MGL_Id'
        );
    }
}
