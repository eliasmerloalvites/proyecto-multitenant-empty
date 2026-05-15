<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'movimiento';
    protected $primaryKey='idmovimiento';
    public $timestamps=true;
    protected $fillable=[
    	'tipo',
    	'idcv'
    ];
    
    protected $guarded =[

    ];
}
