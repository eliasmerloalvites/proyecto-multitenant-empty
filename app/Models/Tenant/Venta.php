<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';
    protected $primaryKey='VEN_Id';
    public $timestamps=true;
    protected $fillable=[
        'VEN_TipoPago',
        'VEN_Vuelto',
        'VEN_Pagado',
        'MEP_Id',
        'USU_Id',
        'CLI_Id',
        'ALM_Id',
        'CAJ_Id',
        'CS_Id',
        'COT_Id',
        'VEN_Status',
        'VEN_FechaEnvio',
    ];

    protected $guarded =[

    ];

    /**
     * Solo tiene sentido para ventas de tallermoto (generico no usa esta
     * relacion desde su propio codigo, pero 'venta.COT_Id' existe en ambos
     * verticales y la tabla 'cotizacion' se llama igual en los dos, asi que
     * esto sigue resolviendo sin romper nada si algo la invoca desde un
     * tenant generico).
     */
    public function cotizacion()
    {
        return $this->belongsTo(\App\Models\TenantTallerMotos\Cotizacion::class, 'COT_Id', 'COT_Id');
    }
}
