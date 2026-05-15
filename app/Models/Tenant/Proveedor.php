<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table ='proveedor';
    protected $primaryKey='PROV_Id';
    public $timestamps= false;
    protected $fillable=[
        'PROV_TipoDocumento',
        'PROV_NumDocumento',
        'PROV_RazonSocial',
        'PROV_Direccion',
        'PROV_Descripcion',
        'PROV_Celular',
        'PROV_Correo',
        'PROV_Status'
    ];
    protected $guarded=[];
}
