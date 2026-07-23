<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MantenimientoPreventivoInyectada  extends Model
{
    protected $table = 'mantenimiento_preventivo_inyectada';
    protected $primaryKey = 'MPI_Id';
    public $timestamps = false;
    protected $fillable = [
        'MPI_Placa',
        'MPI_Propietario',
        'MPI_celular',
        'MPI_Unidad',
        'MPI_KMEntrada',

        'MPI_DetalleIngreso',
        'MPI_DetalleObservacion',

        
    	'MPI_Det1',
    	'MPI_Det1Informacion',
    	'MPI_Det2',
    	'MPI_Det3',
    	'MPI_Det4',
    	'MPI_Det5',
    	'MPI_Det6',
    	'MPI_Det7',
    	'MPI_Det7Admision',
    	'MPI_Det7Escape',
    	'MPI_Det8',
    	'MPI_Det8Medida',
    	'MPI_Det9',
    	'MPI_Det10',
    	'MPI_Det11',
    	'MPI_Det12',
    	'MPI_Det13',
    	'MPI_Det14',
    	'MPI_Det15',
    	'MPI_Det16',
    	'MPI_Det17',
    	'MPI_Det17Ventilador',
    	'MPI_Det18',
    	'MPI_Det19',
    	'MPI_Det19Vida',
    	'MPI_Det19Carga',
    	'MPI_Det19Arranque',
    	'MPI_Det20',

        'MPI_DetalleRealizado',
        'MPI_CorrecionObservacion',

        'MPI_ProximoCambioAceite',
        'MPI_ProximoServicio',

        'MPI_FechaCreacion',
        'MPI_FechaEdicion',

        'MPI_UsuarioCreacion',
        'MPI_UsuarioEditado',

        'MPI_Estado',

        'PER_Id',

        'statevalidate',
        'notificar',

        'observacion',
        'respuesta',
    ];

    protected $casts = [

       'MPI_FechaCreacion' => 'datetime',
        'MPI_FechaEdicion' => 'datetime',

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
            'MPI_Id',
            'MPI_Id'
        );
    }

    public function imagenes()
    {
        return $this->hasMany(
            MgiImagen::class,
            'MPI_Id',
            'MPI_Id'
        );
    }
}
