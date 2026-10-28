<?php

namespace App\Services\Caja;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cuanto entro y salio por cada metodo de pago real: [MEP_Id => monto].
 * Es la misma cuenta que hace el arqueo de caja, pero se puede pedir para un
 * turno (porSesion) o para un rango de fechas (entreFechas, ej. el mes en
 * el dashboard o el periodo de un reporte), asi que caja, dashboard y
 * reportes nunca discrepan.
 *
 * Reglas (iguales a las de la caja):
 * - Venta con pago mixto: se reparte en sus metodos (venta_pago).
 * - Venta al credito: queda bajo el metodo "Credito" (no es dinero cobrado);
 *   lo cobrado entra despues como abono de cuenta por cobrar.
 * - Venta que cerro una cuenta de bahia: se resta lo que ya entro antes
 *   como adelanto (el adelanto cuenta en su propia fecha/turno).
 * - Compra con pago mixto: se reparte en sus metodos (compra_pago).
 * - Compra al credito: bajo "Credito"; lo pagado sale como pago a proveedor.
 * - Gastos: solo los que afectan caja (GAS_Afecta = 'SI').
 *
 * Las tablas propias de talleres (venta_pago, abonos, adelantos, ...) se
 * consultan solo si existen, asi que tambien sirve en negocios genericos.
 */
class FlujoPorMetodoService
{
    private ?string $sesionId = null;
    private ?Carbon $desde = null;
    private ?Carbon $hasta = null;
    private ?int $almacenId = null;

    public static function porSesion(string $csId): self
    {
        $s = new self();
        $s->sesionId = $csId;

        return $s;
    }

    /** Movimientos entre dos fechas; con $almacenId, solo los de las cajas de esa sede. */
    public static function entreFechas(Carbon $desde, Carbon $hasta, ?int $almacenId = null): self
    {
        $s = new self();
        $s->desde = $desde;
        $s->hasta = $hasta;
        $s->almacenId = $almacenId ?: null;

        return $s;
    }

    /** Filtro comun: el turno, o la fecha del movimiento (+ sede via su turno de caja). */
    private function filtrar(Builder $q, string $alias, string $colFecha = 'created_at', bool $soloFecha = false): Builder
    {
        if ($this->sesionId !== null) {
            return $q->where("$alias.CS_Id", $this->sesionId);
        }

        $soloFecha
            ? $q->whereBetween("$alias.$colFecha", [$this->desde->toDateString(), $this->hasta->toDateString()])
            : $q->whereBetween("$alias.$colFecha", [$this->desde, $this->hasta]);

        if ($this->almacenId) {
            $q->whereIn("$alias.CS_Id", function ($sub) {
                $sub->select('cs.CS_Id')->from('caja_sesion as cs')
                    ->join('caja as c', 'c.CAJ_Id', '=', 'cs.CAJ_Id')
                    ->where('c.ALM_Id', $this->almacenId);
            });
        }

        return $q;
    }

    /** Ventas activas por metodo real (mixtas repartidas, sin lo ya cobrado como adelanto). */
    public function ventas(): array
    {
        $revenuePorVenta = $this->filtrar(DB::table('venta as v'), 'v')
            ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->where('v.VEN_Status', 1)
            // DEV_PrecioUnitario ya es el precio final con descuento.
            ->select('v.VEN_Id', 'v.MEP_Id', DB::raw('SUM(dv.DEV_Cantidad * dv.DEV_PrecioUnitario) as revenue'))
            ->groupBy('v.VEN_Id', 'v.MEP_Id')
            ->get();

        if ($revenuePorVenta->isEmpty()) {
            return [];
        }

        $ids = $revenuePorVenta->pluck('VEN_Id');

        $pagosPorVenta = Schema::hasTable('venta_pago')
            ? DB::table('venta_pago')->whereIn('VEN_Id', $ids)->select('VEN_Id', 'MEP_Id', 'VNP_Monto')->get()->groupBy('VEN_Id')
            : collect();

        $adelantosPorVenta = Schema::hasTable('bahia_cuenta_adelanto')
            ? DB::table('bahia_cuenta_adelanto as a')
                ->join('bahia_cuenta as b', 'b.BCT_Id', '=', 'a.BCT_Id')
                ->whereIn('b.VEN_Id', $ids)
                ->select('b.VEN_Id', DB::raw('SUM(a.BCA_Monto) as total'))
                ->groupBy('b.VEN_Id')
                ->pluck('total', 'VEN_Id')
            : collect();

        $porMetodo = [];
        foreach ($revenuePorVenta as $venta) {
            $revenue = max(0, (float) $venta->revenue - (float) ($adelantosPorVenta[$venta->VEN_Id] ?? 0));
            $pagos = $pagosPorVenta->get($venta->VEN_Id);

            if (! $pagos || $pagos->isEmpty()) {
                $porMetodo[$venta->MEP_Id] = ($porMetodo[$venta->MEP_Id] ?? 0) + $revenue;
                continue;
            }

            $tendido = (float) $pagos->sum('VNP_Monto');
            foreach ($pagos as $pago) {
                $porcion = $tendido > 0 ? $revenue * ((float) $pago->VNP_Monto / $tendido) : 0;
                $porMetodo[$pago->MEP_Id] = ($porMetodo[$pago->MEP_Id] ?? 0) + $porcion;
            }
        }

        return $porMetodo;
    }

    /** Abonos de cuentas por cobrar recibidos (incluye el monto inicial de una venta al credito). */
    public function abonos(): array
    {
        return $this->sumaPorMetodo('cuenta_por_cobrar_abono', 'CPA_Monto');
    }

    /** Adelantos de pago sobre cuentas de bahia. */
    public function adelantos(): array
    {
        return $this->sumaPorMetodo('bahia_cuenta_adelanto', 'BCA_Monto');
    }

    /** Compras activas por metodo real (mixtas repartidas por compra_pago). */
    public function compras(): array
    {
        $totales = $this->filtrar(DB::table('compra as co'), 'co')
            ->join('detalle_compra as dc', 'dc.COM_Id', '=', 'co.COM_Id')
            ->where('co.COM_Status', 1)
            ->select('co.COM_Id', 'co.MEP_Id', DB::raw('SUM(dc.DCOM_Cantidad * dc.DCOM_PrecioCompra) as total'))
            ->groupBy('co.COM_Id', 'co.MEP_Id')
            ->get();

        $pagosPorCompra = Schema::hasTable('compra_pago') && $totales->isNotEmpty()
            ? DB::table('compra_pago')->whereIn('COM_Id', $totales->pluck('COM_Id'))->get()->groupBy('COM_Id')
            : collect();

        $porMetodo = [];
        foreach ($totales as $compra) {
            $pagos = $pagosPorCompra->get($compra->COM_Id);

            if (! $pagos || $pagos->isEmpty()) {
                $porMetodo[$compra->MEP_Id] = ($porMetodo[$compra->MEP_Id] ?? 0) + (float) $compra->total;
                continue;
            }

            $pagado = (float) $pagos->sum('CPG_Monto');
            foreach ($pagos as $pago) {
                $porcion = $pagado > 0 ? (float) $compra->total * ((float) $pago->CPG_Monto / $pagado) : 0;
                $porMetodo[$pago->MEP_Id] = ($porMetodo[$pago->MEP_Id] ?? 0) + $porcion;
            }
        }

        return $porMetodo;
    }

    /** Pagos a proveedores (abonos de cuentas por pagar, incluido el monto inicial). */
    public function pagosProveedor(): array
    {
        return $this->sumaPorMetodo('cuenta_por_pagar_abono', 'CPPA_Monto');
    }

    /** Gastos que afectan caja. */
    public function gastos(): array
    {
        return $this->filtrar(DB::table('gasto as g'), 'g', 'GAS_Fecha', true)
            ->where('g.GAS_Status', 1)
            ->where('g.GAS_Afecta', 'SI')
            ->select('g.MEP_Id', DB::raw('SUM(g.GAS_Monto) as total'))
            ->groupBy('g.MEP_Id')
            ->pluck('total', 'MEP_Id')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }

    /**
     * Cuadro completo por metodo (el mismo del detalle de un turno de caja):
     * una columna por metodo de pago + totales. $soloReales oculta las
     * etiquetas "Credito" y "Pago Mixto" (no son dinero que entro o salio).
     */
    public function resumen(bool $soloReales = false): array
    {
        $metodos = DB::table('metodo_pago')->orderBy('MEP_Id')->pluck('MEP_Pago', 'MEP_Id');
        $series = [
            'ventas' => $this->ventas(),
            'abonos' => $this->abonos(),
            'adelantos' => $this->adelantos(),
            'compras' => $this->compras(),
            'pagos_proveedor' => $this->pagosProveedor(),
            'gastos' => $this->gastos(),
        ];

        $columnas = [];
        $totales = array_fill_keys(array_keys($series), 0.0);

        foreach ($metodos as $mepId => $nombre) {
            if ($soloReales && in_array($nombre, ['Credito', 'Pago Mixto'], true)) {
                continue;
            }

            $col = ['id' => (int) $mepId, 'nombre' => $nombre];
            foreach ($series as $clave => $valores) {
                $col[$clave] = round((float) ($valores[$mepId] ?? 0), 2);
                $totales[$clave] += $col[$clave];
            }
            $col['ingresos'] = round($col['ventas'] + $col['abonos'] + $col['adelantos'], 2);
            $col['egresos'] = round($col['compras'] + $col['pagos_proveedor'] + $col['gastos'], 2);
            $col['neto'] = round($col['ingresos'] - $col['egresos'], 2);
            $columnas[] = $col;
        }

        $resultado = ['columnas' => $columnas];
        foreach ($totales as $clave => $total) {
            $resultado['total_' . $clave] = round($total, 2);
        }
        $resultado['total_ingresos'] = round($totales['ventas'] + $totales['abonos'] + $totales['adelantos'], 2);
        $resultado['total_egresos'] = round($totales['compras'] + $totales['pagos_proveedor'] + $totales['gastos'], 2);
        $resultado['total_neto'] = round($resultado['total_ingresos'] - $resultado['total_egresos'], 2);

        return $resultado;
    }

    private function sumaPorMetodo(string $tabla, string $columnaMonto): array
    {
        if (! Schema::hasTable($tabla)) {
            return [];
        }

        return $this->filtrar(DB::table("$tabla as t"), 't')
            ->select('t.MEP_Id', DB::raw("SUM(t.$columnaMonto) as total"))
            ->groupBy('t.MEP_Id')
            ->pluck('total', 'MEP_Id')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }
}
