<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table='detalle_compra';
    protected $primaryKey='DCOM_Item';
    public $timestamps=false;
    protected $fillable=[
        'COM_Id',
        'ALM_Id',
        'PRO_Id',
        'DCOM_Cantidad',
        'DCOM_PrecioCompra',
        'DCOM_PrecioVenta'
    ];
    protected $guarded=[];
    public function compra(){
        return $this->belongsTo(Compra::class,'COM_Id','COM_Id');
    }
    public function almacen(){
        return $this->belongsTo(Almacen::class,'ALM_Id','ALM_Id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class,'PRO_Id','PRO_Id');
    }

}
