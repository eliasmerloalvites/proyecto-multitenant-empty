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

        $hojas = [
            self::hojaResumen($sesion, $datos, $contexto, $abierta),
            self::hojaVentas($datos['ventas'] ?? [], $contexto),
        ];

        // Abonos y adelantos solo existen en el vertical de talleres.
        if (array_key_exists('abonos', $datos)) {
            $hojas[] = self::hojaAbonos($datos['abonos'], $contexto);
        }
        if (array_key_exists('adelantos', $datos)) {
            $hojas[] = self::hojaAdelantos($datos['adelantos'], $contexto);
        }

        $hojas[] = self::hojaCompras($datos['compras'] ?? [], $contexto);
        if (array_key_exists('pagosProveedor', $datos)) {
            $hojas[] = self::hojaPagosProveedor($datos['pagosProveedor'], $contexto);
        }
        $hojas[] = self::hojaGastos($datos['gastos'] ?? [], $contexto);

        return ExcelExporter::descargar(ExcelExporter::nombreArchivo('caja_turno_' . $sesion->CS_Id), $hojas);
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
        $tieneAdelantos = isset($metodos[0]) && array_key_exists('adelantos', $metodos[0]);
        $tienePagosProveedor = isset($metodos[0]) && array_key_exists('pagos_proveedor', $metodos[0]);

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
        if ($tieneAdelantos) {
            $filas[] = $linea('Ingreso por adelantos (ventas por bahía)', 'adelantos', 'total_adelantos');
        }
        $filas[] = $linea('Egreso por compras', 'compras', 'total_compras');
        if ($tienePagosProveedor) {
            $filas[] = $linea('Egreso por pagos a proveedores (cuentas por pagar)', 'pagos_proveedor', 'total_pagos_proveedor');
        }
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

    private static function hojaAbonos(array $abonos, array $contexto): array
    {
        $filas = [];
        foreach ($abonos as $a) {
            $a = (array) $a;
            $filas[] = [
                (string) $a['CPA_Id'],
                substr((string) $a['created_at'], 0, 10),
                substr((string) $a['created_at'], 11, 5),
                $a['CLI_Nombre'] ?: 'Cliente Genérico',
                '#' . $a['VEN_Id'],
                $a['MEP_Pago'],
                $a['usuario'] ?? '—',
                (float) $a['CPA_Monto'],
            ];
        }

        return [
            'nombre' => 'Abonos',
            'titulo' => 'Abonos de Cuentas por Cobrar del Turno',
            'filtros' => $contexto,
            'columnas' => [
                ['titulo' => 'N° abono', 'ancho' => 11, 'centrar' => true],
                ['titulo' => 'Fecha', 'tipo' => ExcelExporter::FECHA, 'ancho' => 12],
                ['titulo' => 'Hora', 'ancho' => 8, 'centrar' => true],
                ['titulo' => 'Cliente', 'ancho' => 34],
                ['titulo' => 'Venta', 'ancho' => 10, 'centrar' => true],
                ['titulo' => 'Método de pago', 'ancho' => 18],
                ['titulo' => 'Usuario', 'ancho' => 22],
                ['titulo' => 'Monto', 'tipo' => ExcelExporter::MONEDA, 'ancho' => 15, 'total' => true],
            ],
            'filas' => $filas,
            'mensaje_vacio' => 'No hubo abonos en este turno.',
        ];
    }

    private static function hojaPagosProveedor(array $pagos, array $contexto): array
    {
        $filas = [];
        foreach ($pagos as $p) {
            $p = (array) $p;
            $documento = trim(($p['COM_TipoDocumento'] ?? '') . ' ' . ($p['COM_NumDocumento'] ?? ''));
            $filas[] = [
                (string) $p['CPPA_Id'],
                substr((string) $p['created_at'], 0, 10),
                substr((string) $p['created_at'], 11, 5),
                $p['PROV_RazonSocial'] ?: '—',
                $documento !== '' ? $documento : '#' . $p['COM_Id'],
                $p['MEP_Pago'],
                $p['usuario'] ?? '—',
                (float) $p['CPPA_Monto'],
            ];
        }

        return [
            'nombre' => 'Pagos a proveedores',
            'titulo' => 'Pagos de Cuentas por Pagar del Turno',
            'filtros' => $contexto,
            'columnas' => [
                ['titulo' => 'N° pago', 'ancho' => 10, 'centrar' => true],
                ['titulo' => 'Fecha', 'tipo' => ExcelExporter::FECHA, 'ancho' => 12],
                ['titulo' => 'Hora', 'ancho' => 8, 'centrar' => true],
                ['titulo' => 'Proveedor', 'ancho' => 34],
                ['titulo' => 'Compra', 'ancho' => 18],
                ['titulo' => 'Método de pago', 'ancho' => 18],
                ['titulo' => 'Usuario', 'ancho' => 22],
                ['titulo' => 'Monto', 'tipo' => ExcelExporter::MONEDA, 'ancho' => 15, 'total' => true],
            ],
            'filas' => $filas,
            'mensaje_vacio' => 'No hubo pagos a proveedores en este turno.',
        ];
    }

    private static function hojaAdelantos(array $adelantos, array $contexto): array
    {
        $filas = [];
        foreach ($adelantos as $a) {
            $a = (array) $a;
            $filas[] = [
                (string) $a['BCA_Id'],
                substr((string) $a['created_at'], 0, 10),
                substr((string) $a['created_at'], 11, 5),
                $a['RES_Cliente'] ?? '—',
                $a['RES_Placa'] ?? '—',
                $a['MEP_Pago'],
                $a['VEN_Id'] ? 'Cobrada (venta #' . $a['VEN_Id'] . ')' : 'Abierta',
                (float) $a['BCA_Monto'],
            ];
        }

        return [
            'nombre' => 'Adelantos',
            'titulo' => 'Adelantos de Ventas por Bahía del Turno',
            'filtros' => $contexto,
            'columnas' => [
                ['titulo' => 'N° adelanto', 'ancho' => 12, 'centrar' => true],
                ['titulo' => 'Fecha', 'tipo' => ExcelExporter::FECHA, 'ancho' => 12],
                ['titulo' => 'Hora', 'ancho' => 8, 'centrar' => true],
                ['titulo' => 'Cliente', 'ancho' => 30],
                ['titulo' => 'Placa', 'ancho' => 12, 'centrar' => true],
                ['titulo' => 'Método de pago', 'ancho' => 18],
                ['titulo' => 'Cuenta', 'ancho' => 22],
                ['titulo' => 'Monto', 'tipo' => ExcelExporter::MONEDA, 'ancho' => 15, 'total' => true],
            ],
            'filas' => $filas,
            'mensaje_vacio' => 'No hubo adelantos en este turno.',
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
