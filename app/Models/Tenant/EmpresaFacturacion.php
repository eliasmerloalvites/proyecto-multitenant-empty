<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class EmpresaFacturacion extends Model
{
    protected $table = 'empresa_facturacion';

    protected $fillable = [
        'tenant_id',
        'ruc',
        'razon_social',
        'nombre_comercial',
        'ubigeo',
        'direccion',
        'departamento',
        'provincia',
        'distrito',
        'cod_local',
        'sol_usuario',
        'sol_password',
        'certificado',
        'certificado_password',
        'ambiente',
        'activo'
    ];
}
