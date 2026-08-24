<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';
    protected $primaryKey='LOT_Id';
    public $timestamps=true;
    protected $fillable=[
        'ALM_Id',
        'PRO_Id',
        'LOT_TipoIngreso',
        'LOT_IdIngreso',
        'LOT_CantidadReal',
        'LOT_CantidadIngreso',
        'LOT_PrecioCompra',
        'LOT_PrecioVenta',
    ];
    
    protected $guarded =[

    ];

    /**
     * Devuelve cantidad al lote de origen (nota de credito o anulacion con
     * devolucion fisica de mercaderia). Si el lote ya no existe (producto
     * eliminado, etc.) no revienta el flujo que lo llama: solo no hace nada,
     * para que quede constando y se revise a mano.
     */
    public static function devolver($lotId, float $cantidad): void
    {
        $lote = self::find($lotId);

        if ($lote) {
            $lote->LOT_CantidadReal = $lote->LOT_CantidadReal + $cantidad;
            $lote->save();
        }
    }
}
