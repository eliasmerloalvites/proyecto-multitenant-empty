<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoPreventivoCarburada  extends Model
{
    protected $table = 'mantenimiento_preventivo_carburada';
    protected $primaryKey = 'MPC_Id';
    public $timestamps = false;
    protected $fillable = [
        'MPC_Placa',
        'MPC_Propietario',
        'MPC_celular',
        'MPC_Unidad',
        'MPC_KMEntrada',

        'MPC_DetalleIngreso',
        'MPC_DetalleObservacion',

        
    	'MPC_Det1',
    	'MPC_Det1Informacion',
    	'MPC_Det2',
    	'MPC_Det3',
    	'MPC_Det4',
    	'MPC_Det5',
    	'MPC_Det6',
    	'MPC_Det7',
    	'MPC_Det7Admision',
    	'MPC_Det7Escape',
    	'MPC_Det8',
    	'MPC_Det8Medida',
    	'MPC_Det9',
    	'MPC_Det10',
    	'MPC_Det11',
    	'MPC_Det11Vida',
    	'MPC_Det11Carga',
    	'MPC_Det11Arranque',

        'MPC_DetalleRealizado',
        'MPC_CorrecionObservacion',

        'MPC_ProximoCambioAceite',
        'MPC_ProximoServicio',

        'MPC_FechaCreacion',
        'MPC_FechaEdicion',

        'MPC_UsuarioCreacion',
        'MPC_UsuarioEditado',

        'MPC_Estado',

        'PER_Id',

        'statevalidate',
        'notificar',

        'observacion',
        'respuesta',
    ];

    protected $casts = [

       'MPC_FechaCreacion' => 'datetime',
        'MPC_FechaEdicion' => 'datetime',

        'statevalidate' => 'boolean',
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
            MgiDetalleReemplazo::class,
            'MPC_Id',
            'MPC_Id'
        );
    }

    public function imagenes()
    {
        return $this->hasMany(
            MgiImagen::class,
            'MPC_Id',
            'MPC_Id'
        );
    }
}
