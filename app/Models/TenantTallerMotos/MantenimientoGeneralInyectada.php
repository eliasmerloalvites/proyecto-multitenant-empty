<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoGeneralInyectada  extends Model
{
    protected $table = 'mantenimiento_general_inyectada';
    protected $primaryKey = 'MGI_Id';
    public $timestamps = false;
    protected $fillable = [
        'MGI_Placa',
        'MGI_Propietario',
        'MGI_celular',
        'MGI_Unidad',
        'MGI_KMEntrada',

        'MGI_DetalleIngreso',
        'MGI_DetalleObservacion',

        'MGI_Det1',
        'MGI_Det1Informacion',

        'MGI_Det2',
        'MGI_Det3',
        'MGI_Det4',
        'MGI_Det5',
        'MGI_Det6',
        'MGI_Det7',
        'MGI_Det8',

        'MGI_Det9',
        'MGI_Det9Admision',
        'MGI_Det9Escape',

        'MGI_Det10',
        'MGI_Det10Medida',

        'MGI_Det11',
        'MGI_Det11Medida',

        'MGI_Det12',
        'MGI_Det13',
        'MGI_Det14',
        'MGI_Det15',
        'MGI_Det16',
        'MGI_Det17',

        'MGI_Det18',
        'MGI_Det19',

        'MGI_Det20',
        'MGI_Det20Humedad',

        'MGI_Det21',

        'MGI_Det22',
        'MGI_Det22Ventilador',

        'MGI_Det23',

        'MGI_Det24',
        'MGI_Det24Vida',
        'MGI_Det24Carga',
        'MGI_Det24Arranque',

        'MGI_Det25',
        'MGI_Det26',
        'MGI_Det27',

        'MGI_DetalleRealizado',
        'MGI_CorrecionObservacion',

        'MGI_ProximoCambioAceite',
        'MGI_ProximoServicio',

        'MGI_FechaCreacion',
        'MGI_FechaEdicion',

        'MGI_UsuarioCreacion',
        'MGI_UsuarioEditado',

        'MGI_Estado',

        'PER_Id',

        'statevalidate',
        'notificar',

        'observacion',
        'respuesta',
    ];

    protected $casts = [

       'MGI_FechaCreacion' => 'datetime',
        'MGI_FechaEdicion' => 'datetime',

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
            'MGI_Id',
            'MGI_Id'
        );
    }

    public function imagenes()
    {
        return $this->hasMany(
            MgiImagen::class,
            'MGI_Id',
            'MGI_Id'
        );
    }
}
