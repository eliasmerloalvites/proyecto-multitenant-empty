<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Personal extends Model
{
    use HasFactory;

    protected $table='personal';
    protected $primaryKey='PER_Id';
    public $timestamps=false;

    protected $fillable =[
    	'PER_Nombre',
    	'PER_Apellido',
    	'PER_TipoDocumento',
    	'PER_NumeroDocumento',
    	'PER_FechaNacimiento',
    	'PER_Edad',
        'PER_Sexo',
        'PER_EstadoCivil',
        'PER_NumeroHijos',
        'PER_Procedencia',
        'PER_Direccion',
        'PER_Referencia',
        'PER_Correo',
        'PER_Celular',
        'PER_Parenteso',
        'PER_PNombre',
        'PER_PCelular',
        'PER_PDireccion',
        'PER_Parenteso2',
        'PER_PNombre2',
        'PER_PCelular2',
        'PER_PDireccion2',
        'PUE_Id',
        'PER_Carrera',
        'PER_GradoAcademico',
        'PER_EstadoLaboral',
        'ARE_Id',
        'PER_TPolo',
        'PER_TPantalon',
        'PER_TZapatos',
        'PER_Titular',
        'PER_Banco',
        'PER_NumeroCuenta',
        'PER_CCI',
        'PER_CV',
        'PER_Documento'
    ];

    protected $guarded =[

    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'PER_Id', 'PER_Id');
    }
}
