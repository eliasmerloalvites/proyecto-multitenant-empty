<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table='metodo_pago';
    protected $primaryKey='MEP_Id';
    public $timestamps=false;
    protected $fillable=[
        'MEP_Pago',
        'MEP_Status'
    ];
    protected $guarded=[];
}
