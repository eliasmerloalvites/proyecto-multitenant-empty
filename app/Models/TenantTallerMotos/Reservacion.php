<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Reservacion extends Model
{
    protected $table='reservacion';

    protected $primaryKey='RES_Id';

    public $timestamps=false;
    protected $fillable =[
    	'TUR_Id',
    	'ALM_Id',
    	'BAH_Id',
    	'RES_Placa',
    	'RES_Moto',
    	'RES_Cliente',
    	'RES_Celular',
    	'RES_Detalle',
    	'RES_Adicional',
    	'RES_FechaProgramada',
    	'RES_State',
    	'RES_Estado',
    	'RES_Notificado',
    	'RES_NotificadoEn',
    ];

    protected $guarded =[

    ];

    protected $casts = [
        'RES_Notificado' => 'boolean',
        'RES_NotificadoEn' => 'datetime',
    ];

    /**
     * Nombre del índice único (ver migración add_slot_unique_constraint_to_reservacion_table)
     * que impide, a nivel de base de datos, dos reservas "vivas" para la
     * misma bahía+turno+fecha.
     */
    public const SLOT_UNIQUE_INDEX = 'RES_UQ_SLOT';

    /**
     * Pre-chequeo (informativo, no es la garantía real) de si un slot ya
     * está ocupado por otra reserva pendiente o aprobada. Sirve para dar
     * un mensaje inmediato y claro en el caso normal (sin carrera). La
     * protección definitiva contra condiciones de carrera la da el índice
     * único de BD; ver esConflictoDeSlot().
     */
    public static function slotEstaOcupado($BAH_Id, $TUR_Id, $RES_FechaProgramada, $excluirId = null): bool
    {
        $query = self::where('BAH_Id', $BAH_Id)
            ->where('TUR_Id', $TUR_Id)
            ->where('RES_FechaProgramada', $RES_FechaProgramada)
            ->where('RES_Estado', 'ACT')
            ->where('RES_State', '!=', 'RECHAZADO');

        if ($excluirId) {
            $query->where('RES_Id', '!=', $excluirId);
        }

        return $query->exists();
    }

    /**
     * True si la QueryException recibida es justo la violación del índice
     * único anti doble-reserva (y no cualquier otro error de BD).
     */
    public static function esConflictoDeSlot(QueryException $e): bool
    {
        return (int) ($e->errorInfo[1] ?? 0) === 1062
            && str_contains($e->getMessage(), self::SLOT_UNIQUE_INDEX);
    }
}
