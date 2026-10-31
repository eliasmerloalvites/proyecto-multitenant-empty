<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Models\Tenant\AjusteDetalle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Ajuste de inventario propio de taller de motos. Hereda toda la logica
 * compartida de App\Http\Controllers\Tenant\AjusteController y solo
 * sobreescribe productos() para excluir los productos marcados como
 * Servicio (PRO_TipoProducto, columna exclusiva de tallermoto): un servicio
 * no tiene stock que ajustar. El binding en AppServiceProvider hace que las
 * rutas de Ajuste de Inventario resuelvan a ESTA clase cuando el tenant es
 * 'tallermoto'.
 */
class AjusteController extends \App\Http\Controllers\Tenant\AjusteController
{
    public function productos(Request $request)
    {
        $request->validate(['ALM_Id' => 'required|integer|exists:almacen,ALM_Id']);

        $columnas = ['p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion'];
        $agrupar = ['p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion'];

        $productos = DB::table('producto as p')
            ->leftJoin('lote as lt', function ($join) use ($request) {
                $join->on('lt.PRO_Id', '=', 'p.PRO_Id')
                    ->where('lt.ALM_Id', '=', $request->ALM_Id);
            })
            ->select(array_merge($columnas, [DB::raw('COALESCE(SUM(lt.LOT_CantidadReal), 0) as stock')]))
            ->where('p.PRO_Status', 1)
            ->where('p.PRO_TipoProducto', 'PRODUCTO')
            ->when($request->filled('search'), function ($q) use ($request) {
                $busqueda = '%' . $request->search . '%';
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('p.PRO_Nombre', 'like', $busqueda)
                        ->orWhere('p.PRO_CodigoInterno', 'like', $busqueda)
                        ->orWhere('p.PRO_CodigoFabricacion', 'like', $busqueda);
                });
            })
            ->groupBy($agrupar)
            ->orderBy('p.PRO_Nombre')
            ->limit(30)
            ->get();

        return response()->json($productos);
    }

    /**
     * Plantilla Excel del conteo fisico: todos los productos activos con el
     * stock que tiene el sistema en el almacen; el usuario solo llena la
     * columna "Stock contado" (las filas que deje vacias no se tocan).
     */
    public function plantillaMasiva(Request $request)
    {
        $request->validate(['ALM_Id' => 'required|integer|exists:almacen,ALM_Id']);

        $almacen = DB::table('almacen')->where('ALM_Id', $request->ALM_Id)->value('ALM_NombreAlmacen');
        $productos = $this->productosConStock((int) $request->ALM_Id);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Conteo fisico');
        $sheet->fromArray(['Id', 'Codigo Interno', 'Codigo Fabricacion', 'Producto', 'Stock en sistema', 'Stock contado'], null, 'A1');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');

        $fila = 2;
        foreach ($productos as $pr) {
            $sheet->fromArray([$pr->PRO_Id, $pr->PRO_CodigoInterno, $pr->PRO_CodigoFabricacion, $pr->PRO_Nombre, (float) $pr->stock, null], null, "A$fila");
            $fila++;
        }
        foreach (['A' => 8, 'B' => 18, 'C' => 20, 'D' => 46, 'E' => 16, 'F' => 16] as $col => $ancho) {
            $sheet->getColumnDimension($col)->setWidth($ancho);
        }
        $sheet->freezePane('A2');

        $nota = $spreadsheet->createSheet();
        $nota->setTitle('Instrucciones');
        $nota->fromArray([
            ['Almacen: ' . $almacen],
            ['1. Escribe en "Stock contado" lo que realmente hay de cada producto.'],
            ['2. Deja vacio el que no vas a contar: no se modifica.'],
            ['3. No cambies la columna Id (con ella se identifica el producto).'],
            ['4. Al subirlo veras primero que se sumara o restara, antes de aplicar.'],
        ], null, 'A1');
        $nota->getStyle('A1')->getFont()->setBold(true);
        $nota->getColumnDimension('A')->setWidth(80);
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'conteo_fisico_' . \Illuminate\Support\Str::slug($almacen) . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Muestra que se sumaria o restaria, sin tocar el stock. */
    public function previewMasivo(Request $request)
    {
        $request->validate([
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'archivo' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $r = $this->calcularConteo((int) $request->ALM_Id, $request->file('archivo')->getRealPath());

        return response()->json([
            'success' => true,
            'resumen' => [
                'incrementos' => collect($r['items'])->where('tipo', AjusteDetalle::TIPO_INCREMENTO)->count(),
                'decrementos' => collect($r['items'])->where('tipo', AjusteDetalle::TIPO_DECREMENTO)->count(),
                'sin_cambio' => $r['sin_cambio'],
                'errores' => count($r['errores']),
            ],
            'items' => $r['items'],
            'errores' => $r['errores'],
        ]);
    }

    /** Aplica el conteo como UN solo ajuste (motivo: conteo fisico). */
    public function storeMasivo(Request $request)
    {
        $request->validate([
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'archivo' => 'required|file|mimes:xlsx,xls,csv',
            'AJU_Observacion' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Se recalcula contra el stock de este momento (puede haberse
            // vendido algo entre la vista previa y confirmar).
            $r = $this->calcularConteo((int) $request->ALM_Id, $request->file('archivo')->getRealPath());

            if (! empty($r['errores'])) {
                throw new Exception('El archivo tiene ' . count($r['errores']) . ' fila(s) con error. Corrigelas y vuelve a subirlo.');
            }
            if (empty($r['items'])) {
                throw new Exception('No hay diferencias para ajustar: el conteo coincide con el sistema.');
            }

            $observacion = trim('Conteo fisico masivo. ' . (string) $request->AJU_Observacion);
            $ajuste = $this->crearAjuste(
                $request->ALM_Id,
                'CONTEO_FISICO',
                $observacion,
                array_column($r['items'], 'PRO_Id'),
                array_column($r['items'], 'tipo'),
                array_column($r['items'], 'cantidad')
            );

            DB::commit();

            return response()->json(['success' => 'Conteo aplicado: ' . count($r['items']) . ' producto(s) ajustados.', 'AJU_Id' => $ajuste->AJU_Id]);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function productosConStock(int $almacenId)
    {
        return DB::table('producto as p')
            ->leftJoin('lote as lt', function ($join) use ($almacenId) {
                $join->on('lt.PRO_Id', '=', 'p.PRO_Id')->where('lt.ALM_Id', '=', $almacenId);
            })
            ->select('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion', DB::raw('COALESCE(SUM(lt.LOT_CantidadReal), 0) as stock'))
            ->where('p.PRO_Status', 1)
            ->where('p.PRO_TipoProducto', 'PRODUCTO')
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion')
            ->orderBy('p.PRO_Nombre')
            ->get();
    }

    /**
     * Lee el Excel y compara lo contado con el stock actual. Devuelve las
     * diferencias (items), las filas con error y cuantas no cambian.
     */
    private function calcularConteo(int $almacenId, string $ruta): array
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($ruta);
        $reader->setReadDataOnly(true);
        $filas = $reader->load($ruta)->getSheet(0)->toArray(null, true, true, false);
        array_shift($filas);

        $productos = $this->productosConStock($almacenId);
        $porId = $productos->keyBy('PRO_Id');
        $porInterno = $productos->filter(fn ($x) => filled($x->PRO_CodigoInterno))->keyBy(fn ($x) => mb_strtolower(trim($x->PRO_CodigoInterno)));
        $porFabricacion = $productos->filter(fn ($x) => filled($x->PRO_CodigoFabricacion))->keyBy(fn ($x) => mb_strtolower(trim($x->PRO_CodigoFabricacion)));

        $items = [];
        $errores = [];
        $vistos = [];
        $sinCambio = 0;
        $numero = 1;

        foreach ($filas as $fila) {
            $numero++;
            [$id, $interno, $fabricacion, $nombre, , $contado] = array_pad($fila, 6, null);

            if ($contado === null || trim((string) $contado) === '') {
                continue; // no contado: no se toca
            }

            $producto = null;
            if (filled($id) && is_numeric($id)) {
                $producto = $porId[(int) $id] ?? null;
            }
            if (! $producto && filled($interno)) {
                $producto = $porInterno[mb_strtolower(trim((string) $interno))] ?? null;
            }
            if (! $producto && filled($fabricacion)) {
                $producto = $porFabricacion[mb_strtolower(trim((string) $fabricacion))] ?? null;
            }
            $etiqueta = trim((string) ($nombre ?: $interno ?: $id));

            if (! $producto) {
                $errores[] = ['fila' => $numero, 'detalle' => "\"$etiqueta\": no se encontro un producto activo con ese Id o codigo."];
                continue;
            }
            if (isset($vistos[$producto->PRO_Id])) {
                $errores[] = ['fila' => $numero, 'detalle' => "\"{$producto->PRO_Nombre}\": esta repetido en el archivo."];
                continue;
            }
            $vistos[$producto->PRO_Id] = true;

            $texto = str_replace(',', '.', trim((string) $contado));
            if (! is_numeric($texto) || (float) $texto < 0) {
                $errores[] = ['fila' => $numero, 'detalle' => "\"{$producto->PRO_Nombre}\": el stock contado \"$contado\" no es un numero valido."];
                continue;
            }

            $diferencia = round((float) $texto - (float) $producto->stock, 2);
            if ($diferencia == 0) {
                $sinCambio++;
                continue;
            }

            $items[] = [
                'fila' => $numero,
                'PRO_Id' => $producto->PRO_Id,
                'producto' => $producto->PRO_Nombre,
                'codigo_interno' => $producto->PRO_CodigoInterno,
                'stock_sistema' => (float) $producto->stock,
                'contado' => round((float) $texto, 2),
                'tipo' => $diferencia > 0 ? AjusteDetalle::TIPO_INCREMENTO : AjusteDetalle::TIPO_DECREMENTO,
                'cantidad' => abs($diferencia),
            ];
        }

        return ['items' => $items, 'errores' => $errores, 'sin_cambio' => $sinCambio];
    }

    protected function validarProductosAjustables(array $proIds): void
    {
        if (empty($proIds)) {
            return;
        }

        $servicios = DB::table('producto')
            ->whereIn('PRO_Id', $proIds)
            ->where('PRO_TipoProducto', 'SERVICIO')
            ->pluck('PRO_Nombre');

        if ($servicios->isNotEmpty()) {
            throw new Exception(
                'No se puede ajustar stock de un Servicio: ' . $servicios->implode(', ') . '.'
            );
        }
    }
}
