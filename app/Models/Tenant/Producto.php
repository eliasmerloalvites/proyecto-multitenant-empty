<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table ='producto';
    protected $primaryKey='PRO_Id';
    public $timestamps=false;
    protected $fillable=[
        'PRO_Nombre',
        'PRO_Descripcion',
        'PRO_PrecioCompra',
        'PRO_PrecioVenta',
        'PRO_Marca',
        'PRO_TipoProducto',
        'PRO_Imagen',
        'PRO_StockMinimo',
        'PRO_CodigoInterno',
        'PRO_CodigoFabricacion',
        'CAT_Id',
        'PRO_Status',
        'PRO_MostrarCatalogo'
    ];
    protected $guarded=[];

    public function categoria(){
        return $this->belongsTo(Categoria::class,'CAT_Id','CAT_Id');
    }
}
