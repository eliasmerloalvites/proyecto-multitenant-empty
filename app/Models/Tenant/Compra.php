<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table='compra';
    protected $primaryKey='COM_Id';
    public $timestamps=true;
    protected $fillable=[
        'COM_TipoDocumento',
        'COM_NumDocumento',
        'COM_TipoPago',
        'MEP_Id',
        'PROV_Id',
        'USU_Id',
        'COM_Status'
    ];
    protected $guarded=[];

    public function metodo_pago(){
        return $this->belongsTo(MetodoPago::class,'MEP_Id','MEP_Id');
    }

    public function proveedor(){
        return $this->belongsTo(Proveedor::class,'PROV_Id','PROV_Id');
    }

    public function users(){
        return $this->belongsTo(User::class,'USU_Id','id');
    }

    public function detalle_compra(){
        return $this->hasMany(DetalleCompra::class,'COM_Id','COM_Id');
    }


}
