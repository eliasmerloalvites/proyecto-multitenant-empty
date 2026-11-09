<?php

namespace App\Services\Seguimiento;

use App\Models\Client;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mide cuanto uso el sistema un cliente: entra a la base de SU negocio y
 * cuenta lo que registro DESPUES de crearse la cuenta (los datos de ejemplo
 * que trae el sistema al nacer no cuentan como uso).
 */
class UsoClienteService
{
    public const SIN_USO = 'sin_uso';
    public const POCO_USO = 'poco_uso';
    public const ACTIVO = 'activo';

    /** Minutos tras el alta durante los que se asume que todo es dato inicial. */
    private const MARGEN_ALTA_MIN = 30;

    /** Tablas de negocio comunes a ambos rubros: etiqueta => tabla. */
    private const TABLAS = [
        'ventas'   => 'venta',
        'compras'  => 'compra',
        'productos' => 'producto',
        'clientes' => 'cliente',
        'reservas' => 'reservacion',
        'usuarios' => 'users',
    ];

    /**
     * @return array{nivel:?string, datos:array<string,int>, ultima_actividad:?Carbon, resumen:string}
     */
    public function medir(Client $client): array
    {
        $tenant = Tenant::find($client->tenant_id);

        if (! $tenant) {
            return $this->vacio('No se encontró el negocio de este cliente.');
        }

        $desde = Carbon::parse($client->created_at)->addMinutes(self::MARGEN_ALTA_MIN);

        try {
            $datos = $tenant->run(fn () => $this->contar($desde));
        } catch (\Throwable $e) {
            return $this->vacio('No se pudo leer la base del cliente.');
        }

        $ultima = $datos['_ultima'] ?? null;
        unset($datos['_ultima']);

        $total = array_sum($datos);
        $operaciones = ($datos['ventas'] ?? 0) + ($datos['mantenimientos'] ?? 0) + ($datos['reservas'] ?? 0);

        $nivel = match (true) {
            $total >= 10 || $operaciones >= 3 => self::ACTIVO,
            $total >= 1 => self::POCO_USO,
            default => self::SIN_USO,
        };

        return [
            'nivel' => $nivel,
            'datos' => $datos,
            'ultima_actividad' => $ultima,
            'resumen' => $this->resumen($datos),
        ];
    }

    /** Se ejecuta DENTRO del tenant (conexion ya apuntando a su base). */
    private function contar(Carbon $desde): array
    {
        $tablas = self::TABLAS;

        // Mantenimientos: una tabla por tipo de servicio (solo taller).
        $mantenimientos = [];
        if (class_exists(\App\Services\TenantTallerMotos\GestionProcesoService::class)) {
            try {
                foreach (\App\Services\TenantTallerMotos\GestionProcesoService::tipos() as $meta) {
                    $mantenimientos[] = $meta['tabla'];
                }
            } catch (\Throwable $e) {
                // el rubro generico no tiene mantenimientos
            }
        }

        $datos = [];
        $ultima = null;

        $registrar = function (string $etiqueta, array $nombresTabla) use ($desde, &$datos, &$ultima) {
            $cuenta = 0;

            foreach ($nombresTabla as $tabla) {
                if (! Schema::hasTable($tabla) || ! Schema::hasColumn($tabla, 'created_at')) {
                    continue;
                }

                $q = DB::table($tabla)->where('created_at', '>', $desde);
                $cuenta += (clone $q)->count();
                $max = (clone $q)->max('created_at');

                if ($max) {
                    $max = Carbon::parse($max);
                    $ultima = ($ultima === null || $max->gt($ultima)) ? $max : $ultima;
                }
            }

            if ($cuenta > 0 || ! empty(array_filter($nombresTabla, fn ($t) => Schema::hasTable($t)))) {
                $datos[$etiqueta] = $cuenta;
            }
        };

        foreach ($tablas as $etiqueta => $tabla) {
            $registrar($etiqueta, [$tabla]);
        }

        if ($mantenimientos) {
            $registrar('mantenimientos', $mantenimientos);
        }

        // Ultimo ingreso: las sesiones guardan last_activity (se purgan con el
        // tiempo, por eso solo suma como pista de actividad reciente).
        if (Schema::hasTable('sessions')) {
            $ts = DB::table('sessions')->whereNotNull('user_id')->max('last_activity');
            if ($ts) {
                $s = Carbon::createFromTimestamp((int) $ts);
                $ultima = ($ultima === null || $s->gt($ultima)) ? $s : $ultima;
            }
        }

        $datos['_ultima'] = $ultima;

        return $datos;
    }

    private function resumen(array $datos): string
    {
        $nombres = [
            'ventas' => 'ventas', 'compras' => 'compras', 'productos' => 'productos',
            'clientes' => 'clientes', 'reservas' => 'reservas', 'usuarios' => 'usuarios',
            'mantenimientos' => 'mantenimientos',
        ];

        $partes = [];
        foreach ($datos as $clave => $n) {
            if ($n > 0) {
                $partes[] = $n . ' ' . ($nombres[$clave] ?? $clave);
            }
        }

        return $partes ? implode(', ', $partes) : 'No registró nada todavía';
    }

    private function vacio(string $motivo): array
    {
        return ['nivel' => null, 'datos' => [], 'ultima_actividad' => null, 'resumen' => $motivo];
    }
}
