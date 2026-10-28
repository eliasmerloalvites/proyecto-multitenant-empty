<?php

namespace App\Services\TenantTallerMotos;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Marca del vehiculo y casilla "Por garantia" en reservas y mantenimientos
 * (ver la migracion create_marca_vehiculo_y_garantia). La marca es siempre
 * opcional: puede que la moto sea de una marca que no esta en la lista.
 */
class MarcaVehiculoService
{
    /** Marcas activas para los selectores: [{MAR_Id, MAR_Nombre}]. */
    public static function activas(): Collection
    {
        if (! Schema::hasTable('marca_vehiculo')) {
            return collect();
        }

        return DB::table('marca_vehiculo')
            ->where('MAR_Estado', 1)
            ->orderBy('MAR_Nombre')
            ->get(['MAR_Id', 'MAR_Nombre']);
    }

    /**
     * Id de marca valido o null. Acepta una marca activa, o la que el
     * registro ya tenia aunque luego se haya desactivado (para no perderla
     * al volver a guardar).
     */
    public static function marcaValida($marcaId, $actual = null): ?int
    {
        $marcaId = (int) $marcaId;
        if ($marcaId <= 0 || ! Schema::hasTable('marca_vehiculo')) {
            return null;
        }
        if ($actual !== null && (int) $actual === $marcaId) {
            return $marcaId;
        }

        return DB::table('marca_vehiculo')->where('MAR_Id', $marcaId)->where('MAR_Estado', 1)->exists() ? $marcaId : null;
    }

    /**
     * Aplica al mantenimiento la marca y "Por garantia" que vienen del
     * formulario. Solo toca lo que el formulario envio (los formularios
     * mandan siempre ambos campos; otros flujos que no los traen no los pisan).
     */
    public static function aplicarAMantenimiento($mantenimiento, string $prefijo, Request $request): void
    {
        if (! Schema::hasColumn($mantenimiento->getTable(), 'MAR_Id')) {
            return;
        }

        if ($request->has('MAR_Id')) {
            $mantenimiento->MAR_Id = self::marcaValida($request->input('MAR_Id'), $mantenimiento->MAR_Id);
        }

        $campo = "{$prefijo}_EsGarantia";
        if ($request->has($campo)) {
            $mantenimiento->{$campo} = $request->boolean($campo) ? 1 : 0;
        }
    }

    /** Nombre de una marca (o null). */
    public static function nombre($marcaId): ?string
    {
        return $marcaId && Schema::hasTable('marca_vehiculo')
            ? DB::table('marca_vehiculo')->where('MAR_Id', $marcaId)->value('MAR_Nombre')
            : null;
    }
}
