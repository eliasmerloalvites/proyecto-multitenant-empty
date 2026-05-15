<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table= 'almacen';
    protected $primaryKey='ALM_Id';
    public $timestamps=false;
    protected $fillable=[
        'ALM_Nombre',
        'ALM_NombreAlmacen',
        'ALM_Direccion',
        'ALM_Ruc',
        'ALM_Celular',
        'ALM_Status'
    ];
    protected $guarded=[];

}
