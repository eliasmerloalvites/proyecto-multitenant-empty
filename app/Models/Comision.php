<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comision extends Model
{
    protected $table = 'comisiones';

    protected $fillable = [
        'pago_id',
        'client_id',
        'vendedor_id',
        'periodo',
        'monto_pago',
        'porcentaje_aplicado',
        'monto_comision',
    ];

    protected $casts = [
        'monto_pago' => 'decimal:2',
        'porcentaje_aplicado' => 'decimal:2',
        'monto_comision' => 'decimal:2',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    /**
     * Reporte agrupado por vendedor+periodo: total de comisión, cuántos
     * clientes pagaron ese mes, y si ese vendedor+periodo ya fue marcado
     * como liquidado (comisiones_liquidaciones). Usado tanto por el reporte
     * global del Admin como por "Mis comisiones" del propio vendedor
     * (pasando $vendedorId para acotarlo a lo suyo).
     */
    public static function reporteQuery(?int $vendedorId = null)
    {
        $query = DB::table('comisiones as c')
            ->join('vendedores as v', 'v.id', '=', 'c.vendedor_id')
            ->join('users as u', 'u.id', '=', 'v.user_id')
            ->leftJoin('comisiones_liquidaciones as l', function ($join) {
                $join->on('l.vendedor_id', '=', 'c.vendedor_id')
                    ->on('l.periodo', '=', 'c.periodo');
            })
            ->select(
                'c.vendedor_id',
                'c.periodo',
                'u.name as vendedor_nombre',
                DB::raw('SUM(c.monto_comision) as total_comision'),
                DB::raw('COUNT(*) as clientes_pagaron'),
                DB::raw('MAX(l.id) as liquidacion_id'),
                DB::raw('MAX(l.liquidado_en) as liquidado_en')
            )
            ->groupBy('c.vendedor_id', 'c.periodo', 'u.name')
            ->orderByDesc('c.periodo');

        if ($vendedorId !== null) {
            $query->where('c.vendedor_id', $vendedorId);
        }

        return $query;
    }
}
