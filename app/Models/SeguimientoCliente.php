<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SeguimientoCliente extends Model
{
    use CentralConnection;

    protected $table = 'seguimiento_clientes';

    public const ETAPAS = [
        'nuevo'      => 'Nuevo',
        'contactado' => 'Contactado',
        'demo'       => 'En demo',
        'interesado' => 'Interesado',
        'convertido' => 'Convertido',
        'perdido'    => 'Perdido',
    ];

    protected $fillable = [
        'client_id', 'etapa', 'motivo_perdida', 'proxima_accion_at', 'proxima_accion_nota',
        'ultimo_contacto_at', 'no_contactar', 'uso_nivel', 'uso_datos', 'ultima_actividad_at',
        'uso_medido_at', 'responsable_id',
    ];

    protected $casts = [
        'proxima_accion_at'   => 'date',
        'ultimo_contacto_at'  => 'datetime',
        'ultima_actividad_at' => 'datetime',
        'uso_medido_at'       => 'datetime',
        'no_contactar'        => 'boolean',
        'uso_datos'           => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
