<?php

namespace App\Models\TenantTallerMotos;

use App\Models\Tenant\Almacen;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $table = 'traslado';
    protected $primaryKey = 'TRA_Id';
    protected $fillable = [
        'ALM_OrigenId',
        'ALM_DestinoId',
        'USU_Id',
        'TRA_Observacion',
    ];

    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'ALM_OrigenId', 'ALM_Id');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'ALM_DestinoId', 'ALM_Id');
    }

    public function detalle()
    {
        return $this->hasMany(TrasladoDetalle::class, 'TRA_Id', 'TRA_Id');
    }
}
