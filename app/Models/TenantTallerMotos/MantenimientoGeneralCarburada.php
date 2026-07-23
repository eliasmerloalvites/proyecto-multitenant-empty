<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoGeneralCarburada  extends Model
{
    protected $table = 'mantenimiento_general_carburada';
    protected $primaryKey = 'MGC_Id';
    public $timestamps = false;
    protected $fillable = [
        'MGC_Placa',
        'MGC_Propietario',
        'MGC_celular',
        'MGC_Unidad',
        'MGC_KMEntrada',

        'MGC_DetalleIngreso',
        'MGC_DetalleObservacion',

        'MGC_Det1',
        'MGC_Det1Informacion',

        'MGC_Det2',
        'MGC_Det3',
        'MGC_Det4',
        'MGC_Det5',
        'MGC_Det6',
        'MGC_Det7',

        'MGC_Det8',
        'MGC_Det8Admision',
        'MGC_Det8Escape',

        'MGC_Det9',
        'MGC_Det9Medida',

        'MGC_Det10',
        'MGC_Det11',
        'MGC_Det12',
        'MGC_Det13',
        'MGC_Det14',
        'MGC_Det15',

        'MGC_Det16',
        'MGC_Det17',

        'MGC_Det18',
        'MGC_Det18Humedad',

        'MGC_Det19',
        'MGC_Det19Ventilador',

        'MGC_Det20',

        'MGC_Det21',
        'MGC_Det21Vida',
        'MGC_Det21Carga',
        'MGC_Det21Arranque',

        'MGC_DetalleRealizado',
        'MGC_CorrecionObservacion',

        'MGC_ProximoCambioAceite',
        'MGC_ProximoServicio',

        'MGC_FechaCreacion',
        'MGC_FechaEdicion',

        'MGC_UsuarioCreacion',
        'MGC_UsuarioEditado',

        'MGC_Estado',

        'PER_Id',

        'statevalidate',
        'notificar',

        'observacion',
        'respuesta',
    ];

    protected $casts = [

       'MGC_FechaCreacion' => 'datetime',
        'MGC_FechaEdicion' => 'datetime',

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
            'MGC_Id',
            'MGC_Id'
        );
    }

    public function imagenes()
    {
        return $this->hasMany(
            MgiImagen::class,
            'MGC_Id',
            'MGC_Id'
        );
    }
}
