<?php

namespace App\Models\TenantTallerMotos;

use App\Models\Tenant\Producto;
use Illuminate\Database\Eloquent\Model;

class CotizacionItem extends Model
{
    protected $table = 'cotizacion_item';
    protected $primaryKey = 'COI_Id';

    protected $fillable = [
        'COT_Id',
        'PRO_Id',
        'COI_Nombre',
        'COI_Cantidad',
        'COI_PrecioUnitario',
        'COI_Descuento',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Id', 'PRO_Id');
    }

    public function esLibre(): bool
    {
        return $this->PRO_Id === null;
    }

    public function nombre(): string
    {
        return $this->esLibre() ? (string) $this->COI_Nombre : (string) ($this->producto->PRO_Nombre ?? 'Producto #' . $this->PRO_Id);
    }

    public function subtotal(): float
    {
        return ($this->COI_Cantidad * $this->COI_PrecioUnitario) - $this->COI_Descuento;
    }
}
