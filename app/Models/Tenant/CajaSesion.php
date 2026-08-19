<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CajaSesion extends Model
{
    protected $table = 'caja_sesion';
    protected $primaryKey = 'CS_Id';

    protected $fillable = [
        'CAJ_Id',
        'USU_Id_Apertura',
        'USU_Id_Cierre',
        'CS_MontoApertura',
        'CS_MontoEsperado',
        'CS_MontoReal',
        'CS_Diferencia',
        'CS_FechaApertura',
        'CS_FechaCierre',
        'CS_Estado',
        'CS_TipoCierre',
        'CS_Observacion',
    ];

    protected $casts = [
        'CS_MontoApertura' => 'decimal:2',
        'CS_MontoEsperado' => 'decimal:2',
        'CS_MontoReal' => 'decimal:2',
        'CS_Diferencia' => 'decimal:2',
        'CS_FechaApertura' => 'datetime',
        'CS_FechaCierre' => 'datetime',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'CAJ_Id', 'CAJ_Id');
    }

    public function usuarioApertura()
    {
        return $this->belongsTo(User::class, 'USU_Id_Apertura', 'id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'USU_Id_Cierre', 'id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'CS_Id', 'CS_Id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'CS_Id', 'CS_Id');
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'CS_Id', 'CS_Id');
    }
}
