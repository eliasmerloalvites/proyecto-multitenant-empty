<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

/** Item de un pedido de la app, con el nombre y precio del momento en que se pidio. */
class PedidoMovilItem extends Model
{
    protected $table = 'pedido_movil_item';
    protected $primaryKey = 'PDI_Id';

    protected $fillable = [
        'PED_Id',
        'PRO_Id',
        'PDI_Nombre',
        'PDI_Precio',
        'PDI_Cantidad',
        'PDI_Subtotal',
    ];

    protected $casts = [
        'PDI_Precio' => 'decimal:2',
        'PDI_Cantidad' => 'decimal:2',
        'PDI_Subtotal' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoMovil::class, 'PED_Id', 'PED_Id');
    }
}
