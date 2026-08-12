<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacen';
    protected $primaryKey = 'ALM_Id';
    
    // Cambiado a true ya que la migración incluye $table->timestamps()
    public $timestamps = true;

    protected $fillable = [
        'EMP_Id',
        'ALM_NombreAlmacen',
        'ALM_CodigoSunat',
        'ALM_EsPrincipal',
        'ALM_Direccion',
        'ALM_Departamento',
        'ALM_Provincia',
        'ALM_Distrito',
        'ALM_Ubigeo',
        'ALM_Referencia',
        'ALM_Latitud',
        'ALM_Longitud',
        'ALM_Celular',
        'ALM_Encargado',
        'ALM_Telefono',
        'ALM_Email',
        'ALM_SerieFactura',
        'ALM_SerieBoleta',
        'ALM_SerieNotaCredito',
        'ALM_SerieNotaDebito',
        'ALM_SerieGuiaRemision',
        'ALM_SerieNotaVenta',
        'ALM_PermitirVentaSinStock',
        'ALM_Status',
    ];

    /**
     * Conversión automática de tipos de datos.
     */
    protected $casts = [
        'ALM_EsPrincipal'           => 'boolean',
        'ALM_PermitirVentaSinStock' => 'boolean',
        'ALM_Status'                => 'boolean',
    ];

    /* =========================================================================
     | RELACIONES
     | ========================================================================= */

    /**
     * Relación con la Empresa / Tenant a la que pertenece el almacén.
     */
    public function empresa()
    {
        return $this->belongsTo(EmpresaFacturacion::class, 'EMP_Id', 'id');
    }

    /* =========================================================================
     | SCOPES (Filtros comunes)
     | ========================================================================= */

    /**
     * Scope para obtener solo almacenes activos.
     * Ejemplo: Almacen::activos()->get();
     */
    public function scopeActivos($query)
    {
        return $query->where('ALM_Status', true);
    }

    /**
     * Scope para obtener la sede principal.
     * Ejemplo: Almacen::principal()->first();
     */
    public function scopePrincipal($query)
    {
        return $query->where('ALM_EsPrincipal', true);
    }
}