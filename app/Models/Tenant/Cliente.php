<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey='CLI_Id';
    public $timestamps=true;
    protected $fillable=[
        'CLI_TipoDocumento',
        'CLI_NumDocumento',
        'CLI_Nombre',
        'CLI_Direccion',
        'CLI_Celular',
        'CLI_Correo',
        'CLI_Status',
    ];
    
    protected $guarded =[

    ];
}
