<?php

namespace App\Models\TenantTallerMotos;

use App\Models\Tenant\Producto;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    protected $table = 'producto_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'PRO_Id',
        'PROI_Item',
        'PROI_url',
        'PROI_Thumb',
        'PROI_Nombre',
        'PROI_Peso',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Id', 'PRO_Id');
    }
}
