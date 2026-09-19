<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Reportes graficos y estadisticos de ventas, exclusivo del vertical
 * 'generico'. Calcula utilidad real (precio de venta vs costo), productos
 * mas vendidos, clientes que mas compran, utilidad por categoria y un
 * detalle linea-por-linea de ventas, con exportacion a CSV (abre nativo en
 * Excel). No reemplaza ni modifica el dashboard existente
 * (HomeController::index()) ni ninguna tabla: solo lee.
 */
class ReporteController extends Controller
{
    /**
     * Pagina "Resumen General": KPIs + serie de tiempo + metodos de pago.
     */
    public function index(Request $request)
    {
        return view('tenant_generico.reportes.index', $this->datosIniciales($request));
    }

    public function utilidad(Request $request)
    {
        return view('tenant_generico.reportes.utilidad', $this->datosIniciales($request));
    }

    public function ranking(Request $request)
    {
        return view('tenant_generico.reportes.ranking', $this->datosIniciales($request));
    }

    public function categorias(Request $request)
    {
        return view('tenant_generico.reportes.categorias', $this->datosIniciales($request));
    }

    /**
     * Pagina "Detalle de Ventas": el listado linea-por-linea. No reusa
     * datosIniciales() porque no necesita el reporte agregado completo,
     * solo el detalle (que puede tener muchas filas).
     */
    public function detalle(Request $request)
    {
        [$fechaDesde, $fechaHasta] = $this->resolverRangoFechas($request);

        $lineas = $this->obtenerLineas($request, $fechaDesde, $fechaHasta);

        return view('tenant_generico.reportes.detalle', [
            'lineasDetalle' => $this->formatearLineasDetalle($lineas),
            'almacenes' => $this->almacenesActivos(),
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'almacenId' => $request->get('almacen_id', ''),
        ]);
    }

    /**
     * Endpoint AJAX compartido por Resumen General, Utilidad por Producto,
     * Productos y Clientes, y Utilidad por Categoria: todas piden los
     * mismos filtros y reciben el reporte completo, cada pagina solo pinta
     * la parte que le interesa.
     */
    public function datos(Request $request)
    {
        [$fechaDesde, $fechaHasta] = $this->resolverRangoFechas($request);

        return response()->json($this->construirReporte($request, $fechaDesde, $fechaHasta));
    }

    /**
     * Endpoint AJAX exclusivo de la pagina "Detalle de Ventas" (separado de
     * datos() para no cargar el listado linea-por-linea, que puede ser
     * largo, en las demas paginas que no lo necesitan).
     */
    public function datosDetalle(Request $request)
    {
        [$fechaDesde, $fechaHasta] = $this->resolverRangoFechas($request);

        $lineas = $this->obtenerLineas($request, $fechaDesde, $fechaHasta);

        return response()->json([
            'lineas' => $this->formatearLineasDetalle($lineas),
        ]);
    }

    /**
     * Pagina "Movimientos de Producto": todo el flujo de stock de un
     * producto (o de todos) — creacion, compras, anulaciones de compra,
     * ventas y ajustes manuales — en una sola linea de tiempo, con stock
     * acumulado cuando se filtra un producto puntual.
     */
    public function movimientos(Request $request)
    {
        [$fechaDesde, $fechaHasta] = $this->resolverRangoFechas($request);

        $productoId = $request->get('producto_id');
        $productoNombre = '';

        if ($productoId) {
            $producto = DB::table('producto')->where('PRO_Id', $productoId)->first(['PRO_Id', 'PRO_Nombre']);
            $productoNombre = $producto->PRO_Nombre ?? '';
        }

        return view('tenant_generico.reportes.movimientos', [
            'almacenes' => $this->almacenesActivos(),
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'almacenId' => $request->get('almacen_id', ''),
            'productoId' => $productoId ?: '',
            'productoNombre' => $productoNombre,
            'tipo' => $request->get('tipo', ''),
        ]);
    }

    /**
     * Endpoint AJAX de "Movimientos de Producto".
     */
    public function datosMovimientos(Request $request)
    {
        [$fechaDesde, $fechaHasta] = $this->resolverRangoFechas($request);

        return response()->json($this->construirReporteMovimientos($request, $fechaDesde, $fechaHasta));
    }

    /**
     * Buscador de productos para el select2 AJAX del filtro de "Movimientos
     * de Producto". Se hace uno propio (en vez de reusar
     * ProductoBuscadorController::compra(), pensado para Compras) porque
     * aca no hace falta el precio de compra/venta empacado en el "id", y
     * porque un producto ya inactivo (PRO_Status = 0) igual puede tener
     * historial de movimientos que se quiera consultar, a diferencia del
     * buscador de Compras que solo debe ofrecer productos activos.
     */
    public function buscarProductosMovimientos(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $page = max(1, (int) $request->get('page', 1));
        $porPagina = 20;

        $query = DB::table('producto as p')
            ->leftJoin('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Id', 'p.PRO_Nombre', DB::raw("COALESCE(c.CAT_Nombre, 'Sin categoría') as CAT_Nombre"));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('p.PRO_Nombre', 'like', '%' . $search . '%')
                    ->orWhere('c.CAT_Nombre', 'like', '%' . $search . '%');
            });
        }

        $total = (clone $query)->count();

        $productos = $query->orderBy('p.PRO_Nombre')->forPage($page, $porPagina)->get();

        return response()->json([
            'results' => $productos->map(function ($p) {
                return ['id' => $p->PRO_Id, 'text' => $p->PRO_Nombre . ' (' . $p->CAT_Nombre . ')'];
            }),
            'pagination' => [
                'more' => ($page * $porPagina) < $total,
            ],
        ]);
    }

    /**
     * Exportacion a "Excel": el proyecto no tiene ninguna libreria de Excel
     * instalada (ni maatwebsite/excel ni phpoffice/phpspreadsheet en
     * composer.json), asi que se genera un CSV con BOM UTF-8 (para que las
     * tildes y "S/" se vean bien) y separador ";" (el que Excel en
     * español espera por defecto al abrir un .csv con doble clic) —
     * Excel lo abre nativo, sin instalar nada nuevo. Reutiliza las mismas
     * funciones de agregacion que las paginas, para que el archivo
     * exportado siempre cuadre con lo que se ve en pantalla.
     */
    public function exportar(Request $request, string $tipo)
    {
        [$fechaDesde, $fechaHasta] = $this->resolverRangoFechas($request);

        $lineas = $this->obtenerLineas($request, $fechaDesde, $fechaHasta);
        $sufijoFechas = $fechaDesde . '_a_' . $fechaHasta;

        switch ($tipo) {
            case 'utilidad':
                $encabezados = ['Producto', 'Categoría', 'Unidades', 'Precio Venta Prom.', 'Precio Compra Prom.', 'Vendido', 'Costo', 'Utilidad', 'Margen %'];
                $filas = array_map(function ($p) {
                    return [$p['nombre'], $p['categoria'], $p['unidades'], $p['precioVentaProm'], $p['precioCompraProm'], $p['importe'], $p['costo'], $p['utilidad'], $p['margen']];
                }, $this->calcularTodosProductos($lineas));
                $nombreArchivo = "utilidad-por-producto_{$sufijoFechas}.csv";
                break;

            case 'productos':
                $encabezados = ['Producto', 'Categoría', 'Unidades', 'Vendido', 'Costo', 'Utilidad', 'Margen %'];
                $productos = collect($this->calcularTodosProductos($lineas))->sortByDesc('unidades')->values()->all();
                $filas = array_map(function ($p) {
                    return [$p['nombre'], $p['categoria'], $p['unidades'], $p['importe'], $p['costo'], $p['utilidad'], $p['margen']];
                }, $productos);
                $nombreArchivo = "productos-mas-vendidos_{$sufijoFechas}.csv";
                break;

            case 'clientes':
                $encabezados = ['Cliente', 'Documento', 'N° Compras', 'Monto Total', 'Última Compra'];
                $filas = array_map(function ($c) {
                    return [$c['nombre'], $c['documento'], $c['numCompras'], $c['montoTotal'], $c['ultimaCompra'] ? Carbon::parse($c['ultimaCompra'])->format('d/m/Y H:i') : ''];
                }, $this->calcularTopClientes($lineas, null));
                $nombreArchivo = "clientes-que-mas-compran_{$sufijoFechas}.csv";
                break;

            case 'categorias':
                $encabezados = ['Categoría', 'Vendido', 'Costo', 'Utilidad', 'Margen %'];
                $filas = array_map(function ($c) {
                    return [$c['nombre'], $c['importe'], $c['costo'], $c['utilidad'], $c['margen']];
                }, $this->calcularUtilidadPorCategoria($lineas));
                $nombreArchivo = "utilidad-por-categoria_{$sufijoFechas}.csv";
                break;

            case 'detalle':
                $encabezados = ['Fecha', 'Venta', 'Cliente', 'Documento', 'Producto', 'Categoría', 'Cantidad', 'Precio Venta', 'Precio Compra', 'Importe', 'Costo', 'Utilidad', 'Margen %', 'Método de Pago'];
                $filas = array_map(function ($l) {
                    return [$l['fecha'], $l['ventaId'], $l['cliente'], $l['documento'], $l['producto'], $l['categoria'], $l['cantidad'], $l['precioVenta'], $l['precioCompra'], $l['importe'], $l['costo'], $l['utilidad'], $l['margen'], $l['metodoPago']];
                }, $this->formatearLineasDetalle($lineas));
                $nombreArchivo = "detalle-de-ventas_{$sufijoFechas}.csv";
                break;

            case 'movimientos':
                $reporteMovimientos = $this->construirReporteMovimientos($request, $fechaDesde, $fechaHasta);
                $conStock = !is_null($reporteMovimientos['producto']);
                $encabezados = ['Fecha', 'Tipo', 'Documento', 'Producto', 'Categoría', 'Almacén', 'Entrada', 'Salida', 'Costo Unit.', 'Precio Unit.', 'Referencia'];
                if ($conStock) {
                    $encabezados[] = 'Stock';
                }
                $filas = array_map(function ($m) use ($conStock) {
                    $fila = [$m['fecha'], $m['tipo'], $m['documento'], $m['producto'], $m['categoria'], $m['almacen'], $m['entrada'], $m['salida'], $m['costoUnitario'], $m['precioUnitario'], $m['referencia']];
                    if ($conStock) {
                        $fila[] = $m['stockDespues'];
                    }
                    return $fila;
                }, $reporteMovimientos['movimientos']);
                $nombreArchivo = "movimientos-producto_{$sufijoFechas}.csv";
                break;

            default:
                abort(404);
        }

        return $this->responderCsv($nombreArchivo, $encabezados, $filas);
    }

    /**
     * Arma la respuesta de descarga CSV: BOM UTF-8 + separador ";" para
     * que Excel en español lo abra correctamente con doble clic.
     */
    private function responderCsv(string $nombreArchivo, array $encabezados, array $filas)
    {
        return response()->streamDownload(function () use ($encabezados, $filas) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, $encabezados, ';');

            foreach ($filas as $fila) {
                fputcsv($salida, $fila, ';');
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Datos comunes que necesita la carga inicial (sin AJAX) de Resumen
     * General, Utilidad por Producto, Productos y Clientes, y Utilidad por
     * Categoria: mismo reporte completo, cada vista pinta su parte.
     */
    private function datosIniciales(Request $request): array
    {
        [$fechaDesde, $fechaHasta] = $this->resolverRangoFechas($request);

        $reporte = $this->construirReporte($request, $fechaDesde, $fechaHasta);

        return array_merge($reporte, [
            'almacenes' => $this->almacenesActivos(),
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'almacenId' => $request->get('almacen_id', ''),
        ]);
    }

    private function resolverRangoFechas(Request $request): array
    {
        $hoy = Carbon::now('America/Lima');
        $fechaDesde = $request->get('fecha_desde') ?: $hoy->copy()->startOfMonth()->toDateString();
        $fechaHasta = $request->get('fecha_hasta') ?: $hoy->toDateString();

        return [$fechaDesde, $fechaHasta];
    }

    private function almacenesActivos()
    {
        return DB::table('almacen')
            ->where('ALM_Status', 1)
            ->orderBy('ALM_NombreAlmacen')
            ->get(['ALM_Id', 'ALM_NombreAlmacen']);
    }

    /**
     * Trae en una sola consulta todas las lineas de venta del rango (con
     * sus joins a venta/cliente/producto/categoria/lote/metodo de pago),
     * ya con importe/costo/utilidad calculados por linea. Es la base que
     * reusan tanto construirReporte() (agrega en PHP) como el detalle y la
     * exportacion (no agregan, solo formatean).
     */
    private function obtenerLineas(Request $request, string $fechaDesde, string $fechaHasta)
    {
        $almacenId = $request->get('almacen_id');

        // Costo unitario real: el del lote del que salio el stock
        // (historico, no el costo actual del producto). Excepcion: el
        // lote sintetico que crea VentaController::ReducirStock() cuando
        // se vende sin stock queda con LOT_PrecioCompra = 0, asi que ahi
        // se usa como respaldo el costo de referencia actual del producto
        // (producto.PRO_PrecioCompra) para no reportar 100% de utilidad
        // en esas lineas.
        $costoUnitarioExpr = 'COALESCE(NULLIF(lo.LOT_PrecioCompra, 0), p.PRO_PrecioCompra)';
        $importeLineaExpr = '(dv.DEV_Cantidad * dv.DEV_PrecioUnitario) - dv.DEV_Descuento';
        $costoLineaExpr = "(dv.DEV_Cantidad * {$costoUnitarioExpr})";

        return DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('lote as lo', 'lo.LOT_Id', '=', 'dv.LOT_Id')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
            ->leftJoin('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->where('v.VEN_Status', 1)
            ->whereDate('v.created_at', '>=', $fechaDesde)
            ->whereDate('v.created_at', '<=', $fechaHasta)
            ->when($almacenId, function ($query) use ($almacenId) {
                $query->where('v.ALM_Id', $almacenId);
            })
            ->select(
                'v.VEN_Id',
                'v.created_at',
                'v.CLI_Id',
                'c.CLI_Nombre',
                'c.CLI_NumDocumento',
                'mp.MEP_Pago',
                'p.PRO_Id',
                'p.PRO_Nombre',
                'cat.CAT_Nombre',
                'dv.DEV_Cantidad',
                DB::raw('dv.DEV_PrecioUnitario as precio_unitario'),
                DB::raw("{$costoUnitarioExpr} as costo_unitario"),
                DB::raw("{$importeLineaExpr} as importe"),
                DB::raw("{$costoLineaExpr} as costo")
            )
            ->orderByDesc('v.created_at')
            ->get()
            ->map(function ($linea) {
                $linea->precio_unitario = round((float) $linea->precio_unitario, 2);
                $linea->costo_unitario = round((float) $linea->costo_unitario, 2);
                $linea->importe = round((float) $linea->importe, 2);
                $linea->costo = round((float) $linea->costo, 2);
                $linea->utilidad = round($linea->importe - $linea->costo, 2);
                $linea->CAT_Nombre = $linea->CAT_Nombre ?: 'Sin categoría';

                return $linea;
            });
    }

    /**
     * Hace TODAS las agregaciones (KPIs, serie de tiempo, top productos,
     * todos los productos, top clientes, utilidad por categoria, metodos
     * de pago) en PHP sobre la coleccion de lineas ya traida por
     * obtenerLineas() — un unico viaje a la base de datos, mismo estilo
     * que CuentaCobrarController::obtenerCuentasFiltradas().
     */
    private function construirReporte(Request $request, string $fechaDesde, string $fechaHasta): array
    {
        $lineas = $this->obtenerLineas($request, $fechaDesde, $fechaHasta);

        return [
            'resumen' => $this->calcularResumen($lineas),
            'serieTiempo' => $this->calcularSerieTiempo($lineas, $fechaDesde, $fechaHasta),
            'topProductos' => $this->calcularTopProductos($lineas, 10),
            'todosProductos' => $this->calcularTodosProductos($lineas),
            'topClientes' => $this->calcularTopClientes($lineas, 10),
            'utilidadPorCategoria' => $this->calcularUtilidadPorCategoria($lineas),
            'metodosPago' => $this->calcularMetodosPago($lineas),
        ];
    }

    private function calcularResumen($lineas): array
    {
        $numVentas = $lineas->pluck('VEN_Id')->unique()->count();
        $totalVendido = round((float) $lineas->sum('importe'), 2);
        $totalCosto = round((float) $lineas->sum('costo'), 2);
        $utilidad = round($totalVendido - $totalCosto, 2);

        return [
            'numVentas' => $numVentas,
            'totalVendido' => $totalVendido,
            'totalCosto' => $totalCosto,
            'utilidad' => $utilidad,
            'margen' => $totalVendido > 0 ? round(($utilidad / $totalVendido) * 100, 1) : 0,
            'ticketPromedio' => $numVentas > 0 ? round($totalVendido / $numVentas, 2) : 0,
        ];
    }

    /**
     * Serie de ventas/utilidad en el tiempo. Se agrupa primero por dia (a
     * partir de las lineas ya traidas, sin volver a consultar la BD), y
     * luego, si el rango es amplio, se vuelven a agrupar esos baldes
     * diarios en semanas o meses para que el grafico no quede ilegible
     * con demasiados puntos.
     */
    private function calcularSerieTiempo($lineas, string $fechaDesde, string $fechaHasta): array
    {
        $porDia = $lineas->groupBy(function ($linea) {
            return Carbon::parse($linea->created_at)->toDateString();
        });

        $diffDias = Carbon::parse($fechaDesde)->diffInDays(Carbon::parse($fechaHasta)) + 1;

        if ($diffDias <= 31) {
            $granularidad = 'dia';
            $formatoClave = fn (Carbon $fecha) => $fecha->toDateString();
            $formatoEtiqueta = fn (Carbon $fecha) => $fecha->format('d/m');
        } elseif ($diffDias <= 180) {
            $granularidad = 'semana';
            $formatoClave = fn (Carbon $fecha) => $fecha->startOfWeek()->toDateString();
            $formatoEtiqueta = fn (Carbon $fecha) => 'Sem. ' . $fecha->startOfWeek()->format('d/m');
        } else {
            $granularidad = 'mes';
            $formatoClave = fn (Carbon $fecha) => $fecha->format('Y-m');
            $formatoEtiqueta = fn (Carbon $fecha) => ucfirst($fecha->translatedFormat('M Y'));
        }

        $baldes = [];

        foreach ($porDia as $fechaStr => $lineasDelDia) {
            $fecha = Carbon::parse($fechaStr);
            $clave = $formatoClave($fecha->copy());

            if (!isset($baldes[$clave])) {
                $baldes[$clave] = [
                    'etiqueta' => $formatoEtiqueta($fecha->copy()),
                    'orden' => $clave,
                    'ventas' => 0,
                    'utilidad' => 0,
                ];
            }

            $baldes[$clave]['ventas'] += (float) $lineasDelDia->sum('importe');
            $baldes[$clave]['utilidad'] += (float) $lineasDelDia->sum('utilidad');
        }

        ksort($baldes);

        return [
            'granularidad' => $granularidad,
            'puntos' => array_map(function ($balde) {
                return [
                    'etiqueta' => $balde['etiqueta'],
                    'ventas' => round($balde['ventas'], 2),
                    'utilidad' => round($balde['utilidad'], 2),
                ];
            }, array_values($baldes)),
        ];
    }

    /**
     * Top productos por unidades vendidas (para el grafico/tabla resumida
     * de "Productos y Clientes"). $limit = null trae todos.
     */
    private function calcularTopProductos($lineas, ?int $limit = 10): array
    {
        $porProducto = collect($this->calcularTodosProductos($lineas))->sortByDesc('unidades')->values();

        return ($limit ? $porProducto->take($limit) : $porProducto)->values()->all();
    }

    /**
     * TODOS los productos vendidos en el rango, con precio de venta y
     * precio de compra PROMEDIO PONDERADO por unidad (no el neto de
     * descuento, para que refleje el precio de lista real que se cobró) —
     * es la tabla completa de la pagina "Utilidad por Producto" y la base
     * de calcularTopProductos().
     */
    private function calcularTodosProductos($lineas): array
    {
        return $lineas->groupBy('PRO_Id')->map(function ($lineasProducto) {
            $primera = $lineasProducto->first();
            $unidades = (float) $lineasProducto->sum('DEV_Cantidad');
            $importe = (float) $lineasProducto->sum('importe');
            $costo = (float) $lineasProducto->sum('costo');
            $utilidad = $importe - $costo;
            $sumaPrecioVenta = (float) $lineasProducto->sum(fn ($l) => $l->DEV_Cantidad * $l->precio_unitario);
            $sumaPrecioCompra = (float) $lineasProducto->sum(fn ($l) => $l->DEV_Cantidad * $l->costo_unitario);

            return [
                'nombre' => $primera->PRO_Nombre,
                'categoria' => $primera->CAT_Nombre,
                'unidades' => round($unidades, 2),
                'precioVentaProm' => $unidades > 0 ? round($sumaPrecioVenta / $unidades, 2) : 0,
                'precioCompraProm' => $unidades > 0 ? round($sumaPrecioCompra / $unidades, 2) : 0,
                'importe' => round($importe, 2),
                'costo' => round($costo, 2),
                'utilidad' => round($utilidad, 2),
                'margen' => $importe > 0 ? round(($utilidad / $importe) * 100, 1) : 0,
            ];
        })->sortByDesc('utilidad')->values()->all();
    }

    /**
     * Top clientes por monto comprado. $limit = null trae todos (usado por
     * la exportacion).
     */
    private function calcularTopClientes($lineas, ?int $limit = 10): array
    {
        $porCliente = $lineas->groupBy('CLI_Id')->map(function ($lineasCliente) {
            $primera = $lineasCliente->first();

            return [
                'nombre' => $primera->CLI_Nombre,
                'documento' => $primera->CLI_NumDocumento,
                'numCompras' => $lineasCliente->pluck('VEN_Id')->unique()->count(),
                'montoTotal' => round((float) $lineasCliente->sum('importe'), 2),
                'ultimaCompra' => $lineasCliente->max('created_at'),
            ];
        })->sortByDesc('montoTotal')->values();

        return ($limit ? $porCliente->take($limit) : $porCliente)->values()->all();
    }

    private function calcularUtilidadPorCategoria($lineas): array
    {
        return $lineas->groupBy('CAT_Nombre')->map(function ($lineasCategoria, $nombre) {
            $importe = (float) $lineasCategoria->sum('importe');
            $costo = (float) $lineasCategoria->sum('costo');
            $utilidad = $importe - $costo;

            return [
                'nombre' => $nombre,
                'importe' => round($importe, 2),
                'costo' => round($costo, 2),
                'utilidad' => round($utilidad, 2),
                'margen' => $importe > 0 ? round(($utilidad / $importe) * 100, 1) : 0,
            ];
        })->sortByDesc('utilidad')->values()->all();
    }

    private function calcularMetodosPago($lineas): array
    {
        return $lineas->groupBy('MEP_Pago')->map(function ($lineasMetodo, $nombre) {
            return [
                'nombre' => $nombre,
                'importe' => round((float) $lineasMetodo->sum('importe'), 2),
            ];
        })->sortByDesc('importe')->values()->all();
    }

    /**
     * Formatea las lineas crudas de obtenerLineas() para la pagina/export
     * de "Detalle de Ventas" (una fila por linea de venta, ya ordenadas de
     * mas reciente a mas antigua por obtenerLineas()).
     */
    private function formatearLineasDetalle($lineas): array
    {
        return $lineas->map(function ($l) {
            $margen = $l->importe > 0 ? round(($l->utilidad / $l->importe) * 100, 1) : 0;

            return [
                'fecha' => Carbon::parse($l->created_at)->format('d/m/Y H:i'),
                'ventaId' => $l->VEN_Id,
                'cliente' => $l->CLI_Nombre,
                'documento' => $l->CLI_NumDocumento,
                'producto' => $l->PRO_Nombre,
                'categoria' => $l->CAT_Nombre,
                'cantidad' => round((float) $l->DEV_Cantidad, 2),
                'precioVenta' => $l->precio_unitario,
                'precioCompra' => $l->costo_unitario,
                'importe' => $l->importe,
                'costo' => $l->costo,
                'utilidad' => $l->utilidad,
                'margen' => $margen,
                'metodoPago' => $l->MEP_Pago,
            ];
        })->values()->all();
    }

    /**
     * Arma el reporte completo de "Movimientos de Producto": trae las 4
     * fuentes de movimiento con obtenerMovimientos(), calcula KPIs, serie de
     * tiempo, desglose por tipo, ranking de productos (solo cuando se ven
     * "todos") y, cuando se filtra un producto puntual, su ficha
     * (creacion, precios, stock actual) y el stock acumulado fila por fila
     * (estilo Kardex).
     */
    private function construirReporteMovimientos(Request $request, string $fechaDesde, string $fechaHasta): array
    {
        $productoId = $request->get('producto_id');
        $almacenId = $request->get('almacen_id');

        $movimientos = $this->obtenerMovimientos($request, $fechaDesde, $fechaHasta);
        $producto = $productoId ? $this->infoProductoMovimiento($productoId, $almacenId, $fechaDesde) : null;

        $movimientosAsc = $movimientos->sortBy('fecha')->values();

        if ($producto) {
            // Estilo Kardex: orden cronologico ascendente con el stock
            // acumulado despues de cada movimiento, arrancando desde el
            // stock real que tenia el producto justo antes de fecha_desde
            // (mismo criterio que InventarioAjusteController::historial()).
            $stock = $producto['stockPrevio'];
            $movimientosAsc = $movimientosAsc->map(function ($m) use (&$stock) {
                $stock = round($stock + $m->entrada - $m->salida, 2);
                $m->stockDespues = $stock;

                return $m;
            });
            $filasTabla = $movimientosAsc->values();
        } else {
            // Con "todos los productos" el stock acumulado no tiene sentido
            // (serian varios productos mezclados), asi que se muestra el
            // movimiento mas reciente primero, igual que "Detalle de Ventas".
            $filasTabla = $movimientosAsc->sortByDesc('fecha')->values();
        }

        return [
            'resumen' => $this->calcularResumenMovimientos($movimientos),
            'serieTiempo' => $this->calcularSerieMovimientos($movimientos, $fechaDesde, $fechaHasta),
            'porTipo' => $this->calcularPorTipoMovimiento($movimientos),
            'topProductos' => $productoId ? [] : $this->calcularTopProductosMovimiento($movimientos, 10),
            'producto' => $producto,
            'movimientos' => $this->formatearMovimientos($filasTabla),
        ];
    }

    /**
     * Une en una sola coleccion las 4 fuentes de movimiento de stock:
     *
     * - Compras reales Y sus anulaciones: ambas quedan en 'lote' con
     *   LOT_TipoIngreso = 'COMPRA' y el mismo LOT_IdIngreso = COM_Id (ver
     *   CompraAnulacionController); se distinguen solo por el signo de
     *   LOT_CantidadIngreso. Se usa la fecha PROPIA de la fila de lote (no
     *   la de la compra), para que una anulacion posterior muestre su
     *   propia fecha en vez de la de la compra original.
     * - Ventas: normal una venta NO crea fila en 'lote' (solo descuenta el
     *   lote existente via FIFO), asi que salen de detalle_venta/venta, no
     *   de lote.
     * - Ajustes manuales de stock (AJUSTE_INGRESO/AJUSTE_SALIDA), igual
     *   formato que InventarioAjusteController::historial().
     *
     * El lote sintetico 'VENTA_SIN_STOCK' (creado por
     * VentaController::ReducirStock() al vender sin stock) queda excluido a
     * proposito de las 3 consultas sobre 'lote' (ninguna pide ese tipo), y
     * la venta que lo origino ya aparece igual como movimiento de tipo
     * "Venta" a traves de detalle_venta.
     */
    private function obtenerMovimientos(Request $request, string $fechaDesde, string $fechaHasta)
    {
        $almacenId = $request->get('almacen_id');
        $productoId = $request->get('producto_id');
        $tipo = $request->get('tipo');

        $compras = DB::table('lote as lo')
            ->join('compra as co', 'co.COM_Id', '=', 'lo.LOT_IdIngreso')
            ->join('producto as p', 'p.PRO_Id', '=', 'lo.PRO_Id')
            ->leftJoin('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->leftJoin('proveedor as prov', 'prov.PROV_Id', '=', 'co.PROV_Id')
            ->leftJoin('almacen as alm', 'alm.ALM_Id', '=', 'lo.ALM_Id')
            ->where('lo.LOT_TipoIngreso', 'COMPRA')
            ->whereDate('lo.created_at', '>=', $fechaDesde)
            ->whereDate('lo.created_at', '<=', $fechaHasta)
            ->when($almacenId, function ($q) use ($almacenId) {
                $q->where('lo.ALM_Id', $almacenId);
            })
            ->when($productoId, function ($q) use ($productoId) {
                $q->where('lo.PRO_Id', $productoId);
            })
            ->select(
                'lo.LOT_Id',
                DB::raw('lo.created_at as fecha'),
                'lo.PRO_Id',
                'p.PRO_Nombre',
                DB::raw("COALESCE(cat.CAT_Nombre, 'Sin categoría') as categoria"),
                DB::raw("COALESCE(alm.ALM_NombreAlmacen, '-') as almacen"),
                'lo.LOT_CantidadIngreso',
                'lo.LOT_PrecioCompra',
                'co.COM_Id',
                'co.COM_NumDocumento',
                DB::raw("COALESCE(prov.PROV_RazonSocial, '-') as referencia")
            )
            ->get()
            ->map(function ($r) {
                $cantidad = (float) $r->LOT_CantidadIngreso;
                $esAnulacion = $cantidad < 0;

                return (object) [
                    'fecha' => $r->fecha,
                    'tipo' => $esAnulacion ? 'Anulación de Compra' : 'Compra',
                    'tipoClave' => $esAnulacion ? 'anulacion_compra' : 'compra',
                    'documento' => 'Compra #' . $r->COM_Id . ($r->COM_NumDocumento ? ' - ' . $r->COM_NumDocumento : ''),
                    'PRO_Id' => $r->PRO_Id,
                    'producto' => $r->PRO_Nombre,
                    'categoria' => $r->categoria,
                    'almacen' => $r->almacen,
                    'entrada' => $cantidad > 0 ? $cantidad : 0,
                    'salida' => $cantidad < 0 ? abs($cantidad) : 0,
                    'costoUnitario' => round((float) $r->LOT_PrecioCompra, 2),
                    'precioUnitario' => null,
                    'referencia' => $r->referencia,
                ];
            });

        $ventas = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->leftJoin('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->leftJoin('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->leftJoin('almacen as alm', 'alm.ALM_Id', '=', 'v.ALM_Id')
            ->leftJoin('lote as lo', 'lo.LOT_Id', '=', 'dv.LOT_Id')
            ->where('v.VEN_Status', 1)
            ->whereDate('v.created_at', '>=', $fechaDesde)
            ->whereDate('v.created_at', '<=', $fechaHasta)
            ->when($almacenId, function ($q) use ($almacenId) {
                $q->where('v.ALM_Id', $almacenId);
            })
            ->when($productoId, function ($q) use ($productoId) {
                $q->where('dv.PRO_Id', $productoId);
            })
            ->select(
                'v.VEN_Id',
                DB::raw('v.created_at as fecha'),
                'dv.PRO_Id',
                'p.PRO_Nombre',
                DB::raw("COALESCE(cat.CAT_Nombre, 'Sin categoría') as categoria"),
                DB::raw("COALESCE(alm.ALM_NombreAlmacen, '-') as almacen"),
                'dv.DEV_Cantidad',
                'dv.DEV_PrecioUnitario',
                DB::raw('COALESCE(NULLIF(lo.LOT_PrecioCompra, 0), p.PRO_PrecioCompra) as costo_unitario'),
                DB::raw("COALESCE(c.CLI_Nombre, 'Sin cliente') as referencia")
            )
            ->get()
            ->map(function ($r) {
                return (object) [
                    'fecha' => $r->fecha,
                    'tipo' => 'Venta',
                    'tipoClave' => 'venta',
                    'documento' => 'Venta #' . $r->VEN_Id,
                    'PRO_Id' => $r->PRO_Id,
                    'producto' => $r->PRO_Nombre,
                    'categoria' => $r->categoria,
                    'almacen' => $r->almacen,
                    'entrada' => 0,
                    'salida' => (float) $r->DEV_Cantidad,
                    'costoUnitario' => round((float) $r->costo_unitario, 2),
                    'precioUnitario' => round((float) $r->DEV_PrecioUnitario, 2),
                    'referencia' => $r->referencia,
                ];
            });

        $ajustes = DB::table('lote as lo')
            ->join('producto as p', 'p.PRO_Id', '=', 'lo.PRO_Id')
            ->leftJoin('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->leftJoin('almacen as alm', 'alm.ALM_Id', '=', 'lo.ALM_Id')
            ->whereIn('lo.LOT_TipoIngreso', ['AJUSTE_INGRESO', 'AJUSTE_SALIDA'])
            ->whereDate('lo.created_at', '>=', $fechaDesde)
            ->whereDate('lo.created_at', '<=', $fechaHasta)
            ->when($almacenId, function ($q) use ($almacenId) {
                $q->where('lo.ALM_Id', $almacenId);
            })
            ->when($productoId, function ($q) use ($productoId) {
                $q->where('lo.PRO_Id', $productoId);
            })
            ->select(
                'lo.LOT_Id',
                DB::raw('lo.created_at as fecha'),
                'lo.PRO_Id',
                'p.PRO_Nombre',
                DB::raw("COALESCE(cat.CAT_Nombre, 'Sin categoría') as categoria"),
                DB::raw("COALESCE(alm.ALM_NombreAlmacen, '-') as almacen"),
                'lo.LOT_TipoIngreso',
                'lo.LOT_CantidadReal',
                'lo.LOT_PrecioCompra',
                'lo.LOT_Motivo'
            )
            ->get()
            ->map(function ($r) {
                $esIngreso = $r->LOT_TipoIngreso === 'AJUSTE_INGRESO';
                $cantidad = (float) $r->LOT_CantidadReal;

                return (object) [
                    'fecha' => $r->fecha,
                    'tipo' => $esIngreso ? 'Ajuste de Ingreso' : 'Ajuste de Salida',
                    'tipoClave' => $esIngreso ? 'ajuste_ingreso' : 'ajuste_salida',
                    'documento' => $r->LOT_Motivo ?: ('Ajuste #' . $r->LOT_Id),
                    'PRO_Id' => $r->PRO_Id,
                    'producto' => $r->PRO_Nombre,
                    'categoria' => $r->categoria,
                    'almacen' => $r->almacen,
                    'entrada' => $cantidad > 0 ? $cantidad : 0,
                    'salida' => $cantidad < 0 ? abs($cantidad) : 0,
                    'costoUnitario' => round((float) $r->LOT_PrecioCompra, 2),
                    'precioUnitario' => null,
                    'referencia' => '-',
                ];
            });

        $movimientos = $compras->concat($ventas)->concat($ajustes);

        if ($tipo) {
            $movimientos = $movimientos->filter(function ($m) use ($tipo) {
                return $m->tipoClave === $tipo;
            });
        }

        return $movimientos->values();
    }

    /**
     * Ficha del producto seleccionado (nombre, categoria, precios de
     * referencia actuales, fecha de creacion) + su stock, para el
     * encabezado de "Movimientos de Producto" cuando se filtra uno solo.
     * 'stockPrevio' es el stock REAL (suma de TODO 'lote', cualquier tipo,
     * respetando el filtro de almacen si hay uno) justo antes de
     * fecha_desde — misma tecnica que
     * InventarioAjusteController::historial() — y es la base desde la que
     * arranca el stock acumulado fila por fila.
     */
    private function infoProductoMovimiento($productoId, $almacenId, string $fechaDesde): ?array
    {
        $producto = DB::table('producto as p')
            ->leftJoin('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->where('p.PRO_Id', $productoId)
            ->select(
                'p.PRO_Id',
                'p.PRO_Nombre',
                'p.PRO_PrecioCompra',
                'p.PRO_PrecioVenta',
                'p.created_at',
                DB::raw("COALESCE(cat.CAT_Nombre, 'Sin categoría') as categoria")
            )
            ->first();

        if (!$producto) {
            return null;
        }

        $stockQuery = DB::table('lote')->where('PRO_Id', $productoId);

        if ($almacenId) {
            $stockQuery->where('ALM_Id', $almacenId);
        }

        $stockActual = (float) (clone $stockQuery)->sum('LOT_CantidadReal');
        $stockPrevio = (float) (clone $stockQuery)
            ->where('created_at', '<', $fechaDesde . ' 00:00:00')
            ->sum('LOT_CantidadReal');

        return [
            'id' => $producto->PRO_Id,
            'nombre' => $producto->PRO_Nombre,
            'categoria' => $producto->categoria,
            'precioCompra' => round((float) $producto->PRO_PrecioCompra, 2),
            'precioVenta' => round((float) $producto->PRO_PrecioVenta, 2),
            'creadoEl' => Carbon::parse($producto->created_at)->format('d/m/Y'),
            'stockActual' => round($stockActual, 2),
            'stockPrevio' => round($stockPrevio, 2),
        ];
    }

    private function calcularResumenMovimientos($movimientos): array
    {
        $totalEntradas = round((float) $movimientos->sum('entrada'), 2);
        $totalSalidas = round((float) $movimientos->sum('salida'), 2);
        $valorEntradas = round((float) $movimientos->sum(function ($m) {
            return $m->entrada * $m->costoUnitario;
        }), 2);
        $valorSalidas = round((float) $movimientos->sum(function ($m) {
            return $m->salida * ($m->precioUnitario ?? $m->costoUnitario);
        }), 2);

        return [
            'numMovimientos' => $movimientos->count(),
            'totalEntradas' => $totalEntradas,
            'totalSalidas' => $totalSalidas,
            'movimientoNeto' => round($totalEntradas - $totalSalidas, 2),
            'valorEntradas' => $valorEntradas,
            'valorSalidas' => $valorSalidas,
        ];
    }

    /**
     * Serie de entradas/salidas en el tiempo, con la misma logica de
     * granularidad automatica (dia/semana/mes segun el ancho del rango) que
     * calcularSerieTiempo(), pero sobre entrada/salida en vez de
     * ventas/utilidad.
     */
    private function calcularSerieMovimientos($movimientos, string $fechaDesde, string $fechaHasta): array
    {
        $porDia = $movimientos->groupBy(function ($m) {
            return Carbon::parse($m->fecha)->toDateString();
        });

        $diffDias = Carbon::parse($fechaDesde)->diffInDays(Carbon::parse($fechaHasta)) + 1;

        if ($diffDias <= 31) {
            $granularidad = 'dia';
            $formatoClave = fn (Carbon $fecha) => $fecha->toDateString();
            $formatoEtiqueta = fn (Carbon $fecha) => $fecha->format('d/m');
        } elseif ($diffDias <= 180) {
            $granularidad = 'semana';
            $formatoClave = fn (Carbon $fecha) => $fecha->startOfWeek()->toDateString();
            $formatoEtiqueta = fn (Carbon $fecha) => 'Sem. ' . $fecha->startOfWeek()->format('d/m');
        } else {
            $granularidad = 'mes';
            $formatoClave = fn (Carbon $fecha) => $fecha->format('Y-m');
            $formatoEtiqueta = fn (Carbon $fecha) => ucfirst($fecha->translatedFormat('M Y'));
        }

        $baldes = [];

        foreach ($porDia as $fechaStr => $lineasDelDia) {
            $fecha = Carbon::parse($fechaStr);
            $clave = $formatoClave($fecha->copy());

            if (!isset($baldes[$clave])) {
                $baldes[$clave] = [
                    'etiqueta' => $formatoEtiqueta($fecha->copy()),
                    'orden' => $clave,
                    'entradas' => 0,
                    'salidas' => 0,
                ];
            }

            $baldes[$clave]['entradas'] += (float) $lineasDelDia->sum('entrada');
            $baldes[$clave]['salidas'] += (float) $lineasDelDia->sum('salida');
        }

        ksort($baldes);

        return [
            'granularidad' => $granularidad,
            'puntos' => array_map(function ($balde) {
                return [
                    'etiqueta' => $balde['etiqueta'],
                    'entradas' => round($balde['entradas'], 2),
                    'salidas' => round($balde['salidas'], 2),
                ];
            }, array_values($baldes)),
        ];
    }

    /**
     * Desglose por tipo de movimiento (Compra, Anulación de Compra, Venta,
     * Ajuste de Ingreso, Ajuste de Salida), para el grafico de dona y la
     * tabla-resumen de la pagina.
     */
    private function calcularPorTipoMovimiento($movimientos): array
    {
        return $movimientos->groupBy('tipo')->map(function ($grupo, $nombre) {
            return [
                'nombre' => $nombre,
                'unidades' => round((float) ($grupo->sum('entrada') + $grupo->sum('salida')), 2),
                'movimientos' => $grupo->count(),
            ];
        })->sortByDesc('unidades')->values()->all();
    }

    /**
     * Ranking de los productos con mas movimiento (entradas + salidas) en
     * el rango, solo tiene sentido cuando se ven "todos los productos" (con
     * uno solo seleccionado ya se ve su detalle completo en la tabla).
     */
    private function calcularTopProductosMovimiento($movimientos, int $limit = 10): array
    {
        return $movimientos->groupBy('PRO_Id')->map(function ($grupo) {
            $primera = $grupo->first();

            return [
                'nombre' => $primera->producto,
                'categoria' => $primera->categoria,
                'entradas' => round((float) $grupo->sum('entrada'), 2),
                'salidas' => round((float) $grupo->sum('salida'), 2),
                'movimientos' => $grupo->count(),
            ];
        })->sortByDesc(function ($p) {
            return $p['entradas'] + $p['salidas'];
        })->take($limit)->values()->all();
    }

    /**
     * Formatea las filas ya unidas/ordenadas de obtenerMovimientos() (o su
     * version con 'stockDespues' ya calculado) para JSON/DataTable/CSV.
     */
    private function formatearMovimientos($movimientos): array
    {
        return $movimientos->map(function ($m) {
            return [
                'fecha' => Carbon::parse($m->fecha)->format('d/m/Y H:i'),
                'tipo' => $m->tipo,
                'tipoClave' => $m->tipoClave,
                'documento' => $m->documento,
                'producto' => $m->producto,
                'categoria' => $m->categoria,
                'almacen' => $m->almacen,
                'entrada' => round((float) $m->entrada, 2),
                'salida' => round((float) $m->salida, 2),
                'costoUnitario' => $m->costoUnitario,
                'precioUnitario' => $m->precioUnitario,
                'referencia' => $m->referencia,
                'stockDespues' => $m->stockDespues ?? null,
            ];
        })->values()->all();
    }
}
