<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class BahiaCuentaItem extends Model
{
    protected $table = 'bahia_cuenta_item';
    protected $primaryKey = 'BCI_Id';

    protected $fillable = [
        'BCT_Id',
        'PRO_Id',
        'BCI_Cantidad',
        'BCI_PrecioUnitario',
        'USU_Id_Agrega',
    ];

    protected $casts = [
        'BCI_Cantidad' => 'float',
        'BCI_PrecioUnitario' => 'float',
    ];

    public function subtotal(): float
    {
        return $this->BCI_Cantidad * $this->BCI_PrecioUnitario;
    }

    public function producto()
    {
        return $this->belongsTo(\App\Models\Tenant\Producto::class, 'PRO_Id', 'PRO_Id');
    }
}
