<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table='categoria';
    protected $primaryKey='CAT_Id';
    public $timestamps=false;
    protected $fillable=[
        'CAT_Nombre',
        'CAT_Imagen',
        'CLA_Id'
    ]  ;
    protected $guarded=[];
}
