<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $table = 'clase';

    protected $primaryKey = 'CLA_Id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'CLA_Nombre'
    ];
}
