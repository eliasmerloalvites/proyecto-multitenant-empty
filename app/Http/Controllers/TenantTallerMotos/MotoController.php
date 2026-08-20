<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

/**
 * En un taller de motos se atiende por PLACA, no por cliente — la misma
 * moto puede cambiar de dueño con el tiempo. Esta vista junta los 5 tipos
 * de mantenimiento (general/preventivo × inyectada/carburada + actividad
 * variada) y los agrupa por placa, mostrando el último mantenimiento de
 * cada una. El detalle por placa trae el historial completo de esa moto
 * específica, sin importar quién figure como propietario en cada registro.
 */
class MotoController extends Controller
{
    /**
     * Cada tupla: [tabla, prefijo de columnas, etiqueta legible, nombre de
     * ruta .edit para "ver detalle completo" de un registro puntual].
     */
    private const TIPOS = [
        ['mantenimiento_general_inyectada', 'MGI', 'General Inyectada', 'tenant.mantenimientos.generalinyectada.edit', 'generalinyectada'],
        ['mantenimiento_general_carburada', 'MGC', 'General Carburada', 'tenant.mantenimientos.generalcarburada.edit', 'generalcarburada'],
        ['mantenimiento_preventivo_inyectada', 'MPI', 'Preventivo Inyectada', 'tenant.mantenimientos.preventivoinyectada.edit', 'preventivoinyectada'],
        ['mantenimiento_preventivo_carburada', 'MPC', 'Preventivo Carburada', 'tenant.mantenimientos.preventivocarburada.edit', 'preventivocarburada'],
        ['mantenimiento_actividad_variadas', 'MAV', 'Actividad Variada', 'tenant.actividades.mantenimientoactividadvariada.edit', 'mantenimientoactividadvariada'],
    ];

    /**
     * UNION ALL de los 5 tipos con columnas normalizadas, para poder
     * agrupar/rankear por placa sin importar en qué tabla vive cada
     * registro.
     */
    private function queryUnificada()
    {
        $selects = [];

        foreach (self::TIPOS as [$tabla, $prefijo, $etiqueta]) {
            $selects[] = "SELECT
                {$prefijo}_Placa AS placa,
                {$prefijo}_Propietario AS propietario,
                {$prefijo}_celular AS celular,
                {$prefijo}_Unidad AS unidad,
                {$prefijo}_FechaCreacion AS fecha,
                {$prefijo}_Estado AS estado,
                {$prefijo}_ProximoServicio AS proximo_servicio,
                {$prefijo}_Id AS registro_id,
                '{$etiqueta}' AS tipo,
                '{$tabla}' AS tabla
            FROM {$tabla}
            WHERE {$prefijo}_Placa IS NOT NULL AND {$prefijo}_Placa != ''";
        }

        return implode(' UNION ALL ', $selects);
    }

    /**
     * Listado: una fila por placa única, con los datos de su mantenimiento
     * más reciente y el total de veces que se le ha atendido.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sql = "SELECT placa, propietario, celular, unidad, fecha, estado, proximo_servicio, tipo, total_visitas
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY placa ORDER BY fecha DESC) AS rn,
                        COUNT(*) OVER (PARTITION BY placa) AS total_visitas
                    FROM ({$this->queryUnificada()}) AS combinado
                ) AS rankeado
                WHERE rn = 1
                ORDER BY fecha DESC";

            $data = collect(DB::select($sql));

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('placa', fn ($row) => strtoupper($row->placa))
                ->addColumn('fecha', fn ($row) => $row->fecha ? \Carbon\Carbon::parse($row->fecha)->format('d/m/Y') : '—')
                ->addColumn('visitas', fn ($row) => $row->total_visitas . ' ' . ($row->total_visitas == 1 ? 'visita' : 'visitas'))
                ->addColumn('estado', function ($row) {
                    $estilos = [
                        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#D97706', 'bg' => 'rgba(245,158,11,.12)'],
                        'APROBADO' => ['label' => 'Aprobado', 'color' => '#16A34A', 'bg' => 'rgba(34,197,94,.12)'],
                        'OBSERVADO' => ['label' => 'Observado', 'color' => '#DC2626', 'bg' => 'rgba(239,68,68,.12)'],
                    ];
                    $e = $estilos[$row->estado] ?? ['label' => $row->estado, 'color' => '#6B7280', 'bg' => 'rgba(107,114,128,.12)'];
                    return '<span style="color:' . $e['color'] . ';background:' . $e['bg'] . ';padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">' . $e['label'] . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . tenant_url('tenant.motos.detalle', ['placa' => $row->placa]) . '" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye mr-1"></i>Ver historial</a>';
                })
                ->rawColumns(['estado', 'action'])
                ->make(true);
        }

        return view('tenant_tallermoto.motos.index');
    }

    /**
     * Detalle de una placa específica: datos básicos del último
     * mantenimiento + el historial completo (los 5 tipos combinados,
     * ordenados del más reciente al más antiguo).
     */
    public function detalle(string $placa)
    {
        $placa = strtoupper($placa);

        $sql = "SELECT placa, propietario, celular, unidad, fecha, estado, proximo_servicio, registro_id, tipo, tabla
            FROM ({$this->queryUnificada()}) AS combinado
            WHERE UPPER(placa) = ?
            ORDER BY fecha DESC";

        $historial = collect(DB::select($sql, [$placa]));

        if ($historial->isEmpty()) {
            abort(404, 'No se encontró historial para esa placa.');
        }

        $ultimo = $historial->first();

        // Mapa tipo => [nombre de ruta, nombre del param] para armar los
        // links "ver detalle completo" de cada registro del historial.
        $rutasPorTipo = collect(self::TIPOS)->mapWithKeys(fn ($t) => [$t[2] => ['ruta' => $t[3], 'param' => $t[4]]]);

        return view('tenant_tallermoto.motos.detalle', [
            'placa' => $placa,
            'ultimo' => $ultimo,
            'historial' => $historial,
            'rutasPorTipo' => $rutasPorTipo,
        ]);
    }
}
