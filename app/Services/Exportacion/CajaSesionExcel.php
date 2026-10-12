<?php

namespace App\Services\Exportacion;

use App\Models\Tenant\CajaSesion;
use Illuminate\Support\Facades\DB;

/**
 * Excel del detalle de un turno de caja (el mismo que se ve en Historial de
 * Cajas > Ver): hoja "Resumen del turno" con los datos del arqueo y el cuadro
 * por metodo de pago, y una hoja por cada tipo de movimiento (ventas,
 * compras, gastos). Recibe los mismos datos que arma detalle() de cada
 * vertical, para que el archivo siempre coincida con lo que muestra la
 * pantalla; las anulaciones/estados se leen aparte porque ese detalle no
 * las trae en todos los verticales.
 */
class CajaSesionExcel
{
    public static function descargar(CajaSesion $sesion, array $datos)
    {
        $caja = $sesion->caja->CAJ_Nombre ?? 'Caja';
        $abierta = $sesion->CS_Estado === 'abierta';

        $contexto = [
            'Caja: ' . $caja,
            'Turno N° ' . $sesion->CS_Id,
            'Estado: ' . ($abierta ? 'Abierto' : 'Cerrado'),
        ];

        return ExcelExporter::descargar(ExcelExporter::nombreArchivo('caja_turno_' . $sesion->CS_Id), [
            self::hojaResumen($sesion, $datos, $contexto, $abierta),
            self::hojaVentas($datos['ventas'] ?? [], $contexto),
            self::hojaCompras($datos['compras'] ?? [], $contexto),
            self::hojaGastos($datos['gastos'] ?? [], $contexto),
        ]);
    }

    private static function hojaResumen(CajaSesion $sesion, array $datos, array $contexto, bool $abierta): array
    {
        $diferencia = $sesion->CS_Diferencia;
        $colorDif = $diferencia === null ? null : ((float) $diferencia == 0.0 ? '15803D' : ((float) $diferencia < 0 ? 'B91C1C' : 'B45309'));

        $info = [
            ['Caja', $sesion->caja->CAJ_Nombre ?? '—'],
            ['Turno N°', (string) $sesion->CS_Id],
            ['Estado', $abierta ? 'Abierto (en curso)' : 'Cerrado'],
            ['Apertura', ($sesion->CS_FechaApertura?->format('d/m/Y H:i') ?? '—') . '   por   ' . ($sesion->usuarioApertura->name ?? '—')],
            ['Cierre', $abierta ? '—' : (($sesion->CS_FechaCierre?->format('d/m/Y H:i') ?? '—') . '   por   ' . ($sesion->usuarioCierre->name ?? '—'))],
        ];

        if (! empty($sesion->CS_TipoCierre) && ! $abierta) {
            $info[] = ['Tipo de cierre', ucfirst((string) $sesion->CS_TipoCierre)];
        }

        $info[] = ['Monto de apertura', (float) $sesion->CS_MontoApertura, ExcelExporter::MONEDA];
        $info[] = [$abierta ? 'Monto esperado (a este momento)' : 'Monto esperado', (float) ($datos['montoEsperado'] ?? 0), ExcelExporter::MONEDA];
        $info[] = ['Monto real contado', $sesion->CS_MontoReal !== null ? (float) $sesion->CS_MontoReal : '—', $sesion->CS_MontoReal !== null ? ExcelExporter::MONEDA : ExcelExporter::TEXTO];
        $info[] = ['Diferencia', $diferencia !== null ? (float) $diferencia : '—', $diferencia !== null ? ExcelExporter::MONEDA : ExcelExporter::TEXTO, $colorDif];
        $info[] = ['Observación', trim((string) $sesion->CS_Observacion) !== '' ? $sesion->CS_Observacion : '—'];

        // Cuadro por metodo de pago: una columna por metodo + total.
        $resumen = $datos['resumenPorMetodo'] ?? ['columnas' => []];
        $metodos = $resumen['columnas'] ?? [];
        $tieneAbonos = isset($metodos[0]) && array_key_exists('abonos', $metodos[0]);

        $columnas = [['titulo' => 'Movimiento', 'ancho' => 36]];
        foreach ($metodos as $m) {
            $columnas[] = ['titulo' => $m['nombre'], 'tipo' => ExcelExporter::MONEDA, 'ancho' => 16];
        }
        $columnas[] = ['titulo' => 'TOTAL', 'tipo' => ExcelExporter::MONEDA, 'ancho' => 16];

        $linea = function (string $titulo, string $campo, string $campoTotal) use ($metodos, $resumen) {
            $fila = [$titulo];
            foreach ($metodos as $m) {
                $fila[] = (float) ($m[$campo] ?? 0);
            }
            $fila[] = (float) ($resumen[$campoTotal] ?? 0);

            return $fila;
        };

        $filas = [$linea('Ingreso por ventas', 'ventas', 'total_ventas')];
        if ($tieneAbonos) {
            $filas[] = $linea('Ingreso por abonos (cuentas por cobrar)', 'abonos', 'total_abonos');
        }
        $filas[] = $linea('Egreso por compras', 'compras', 'total_compras');
        $filas[] = $linea('Egreso por gastos (afectan caja)', 'gastos', 'total_gastos');
        $filas[] = $linea('NETO', 'neto', 'total_neto');

        return [
            'nombre' => 'Resumen del turno',
            'titulo' => 'Arqueo de Caja - Resumen del Turno',
            'filtros' => $contexto,
            'info' => $info,
            'columnas' => $columnas,
            'filas' => $filas,
            'filas_resaltadas' => [count($filas) - 1],
            'sin_autofiltro' => true,
            'mensaje_vacio' => 'No hay movimientos por método de pago en este turno.',
        ];
    }

    private static function hojaVentas(array $ventas, array $contexto): array
    {
        $ids = collect($ventas)->pluck('VEN_Id')->all();

        // Estado de cada venta: el detalle del turno no siempre lo trae.
        $estados = $ids
            ? DB::table('venta as v')
                ->leftJoin('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
                ->whereIn('v.VEN_Id', $ids)
                ->select('v.VEN_Id', 'v.VEN_Status', DB::raw('MAX(dov.DOV_Anulado) as anulado'))
                ->groupBy('v.VEN_Id', 'v.VEN_Status')
                ->get()
                ->keyBy('VEN_Id')
            : collect();

        $filas = [];
        foreach ($ventas as $v) {
            $e = $estados[$v['VEN_Id']] ?? null;
            $anulada = $e && ((int) $e->VEN_Status !== 1 || (int) $e->anulado === 1);

            $filas[] = [
                (string) $v['VEN_Id'],
                substr((string) $v['created_at'], 0, 10),
                substr((string) $v['created_at'], 11, 5),
                $v['CLI_Nombre'],
                $v['MEP_Pago'],
                $anulada ? 'Anulada' : 'Vigente',
                (float) $v['total'],
            ];
        }

        return [
            'nombre' => 'Ventas',
            'titulo' => 'Ventas del Turno',
            'filtros' => $contexto,
            'columnas' => [
                ['titulo' => 'N° venta', 'ancho' => 11, 'centrar' => true],
                ['titulo' => 'Fecha', 'tipo' => ExcelExporter::FECHA, 'ancho' => 12],
                ['titulo' => 'Hora', 'ancho' => 8, 'centrar' => true],
                ['titulo' => 'Cliente', 'ancho' => 36],
                ['titulo' => 'Método de pago', 'ancho' => 18],
                ['titulo' => 'Estado', 'ancho' => 12, 'centrar' => true],
                ['titulo' => 'Total', 'tipo' => ExcelExporter::MONEDA, 'ancho' => 15, 'total' => true],
            ],
            'filas' => $filas,
            'total_excluye' => [5, 'Anulada'],
            'total_etiqueta' => 'TOTAL (sin anuladas)',
            'mensaje_vacio' => 'No hubo ventas en este turno.',
        ];
    }

    private static function hojaCompras(array $compras, array $contexto): array
    {
        $ids = collect($compras)->pluck('COM_Id')->all();

        $estados = $ids
            ? DB::table('compra')->whereIn('COM_Id', $ids)->pluck('COM_Status', 'COM_Id')
            : collect();

        $filas = [];
        foreach ($compras as $c) {
            $filas[] = [
                (string) $c['COM_Id'],
                substr((string) $c['created_at'], 0, 10),
                substr((string) $c['created_at'], 11, 5),
                $c['PROV_RazonSocial'],
                $c['MEP_Pago'],
                (int) ($estados[$c['COM_Id']] ?? 1) === 1 ? 'Activa' : 'Inactiva',
                (float) $c['total'],
            ];
        }

        return [
            'nombre' => 'Compras',
            'titulo' => 'Compras del Turno',
            'filtros' => $contexto,
            'columnas' => [
                ['titulo' => 'N° compra', 'ancho' => 11, 'centrar' => true],
                ['titulo' => 'Fecha', 'tipo' => ExcelExporter::FECHA, 'ancho' => 12],
                ['titulo' => 'Hora', 'ancho' => 8, 'centrar' => true],
                ['titulo' => 'Proveedor', 'ancho' => 36],
                ['titulo' => 'Método de pago', 'ancho' => 18],
                ['titulo' => 'Estado', 'ancho' => 12, 'centrar' => true],
                ['titulo' => 'Total', 'tipo' => ExcelExporter::MONEDA, 'ancho' => 15, 'total' => true],
            ],
            'filas' => $filas,
            'total_excluye' => [5, 'Inactiva'],
            'total_etiqueta' => 'TOTAL (sin inactivas)',
            'mensaje_vacio' => 'No hubo compras en este turno.',
        ];
    }

    private static function hojaGastos(array $gastos, array $contexto): array
    {
        $filas = [];
        foreach ($gastos as $g) {
            $filas[] = [
                (string) $g['GAS_Id'],
                $g['GAS_Fecha'],
                $g['GAS_Descripcion'],
                $g['MEP_Pago'],
                $g['GAS_Afecta'] === 'SI' ? 'SI' : 'NO',
                (float) $g['GAS_Monto'],
            ];
        }

        return [
            'nombre' => 'Gastos',
            'titulo' => 'Gastos del Turno',
            'filtros' => $contexto,
            'columnas' => [
                ['titulo' => 'N° gasto', 'ancho' => 11, 'centrar' => true],
                ['titulo' => 'Fecha', 'tipo' => ExcelExporter::FECHA, 'ancho' => 12],
                ['titulo' => 'Descripción', 'ancho' => 44],
                ['titulo' => 'Método de pago', 'ancho' => 18],
                ['titulo' => '¿Afecta la caja?', 'ancho' => 16, 'centrar' => true],
                ['titulo' => 'Monto', 'tipo' => ExcelExporter::MONEDA, 'ancho' => 15, 'total' => true],
            ],
            'filas' => $filas,
            'total_excluye' => [4, 'NO'],
            'total_etiqueta' => 'TOTAL (afectan caja)',
            'mensaje_vacio' => 'No hubo gastos en este turno.',
        ];
    }
}
