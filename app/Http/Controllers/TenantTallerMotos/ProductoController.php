<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Models\Tenant\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Productos propio de taller de motos. Hereda de
 * App\Http\Controllers\Tenant\ProductoController (base compartida, que ya
 * trae los guard-flags tenantTieneCodigosProducto()/tenantTieneGaleriaProducto()/
 * tenantTieneCatalogoWeb() para las columnas/tablas exclusivas de este
 * vertical). El binding en AppServiceProvider hace que las rutas de
 * Inventario > Producto resuelvan a ESTA clase cuando el tenant es
 * 'tallermoto', asi que a partir de ahora se puede sobreescribir aqui
 * cualquier metodo sin afectar a generico.
 *
 * store()/update() estan sobreescritos aqui (no en la base compartida)
 * porque PRO_TipoProducto (PRODUCTO/SERVICIO) solo existe en la tabla
 * producto de tallermoto -- ver migracion
 * add_tipo_producto_to_producto_table. Un Servicio (mano de obra,
 * diagnostico, lavado, etc.) nunca tiene lotes/stock: se vende igual que un
 * producto pero VentaController::ReducirStock()/getProductos() lo tratan
 * distinto (ver ahi).
 */
class ProductoController extends \App\Http\Controllers\Tenant\ProductoController
{
    private function tipoProducto(Request $request): string
    {
        return $request->input('PRO_TipoProducto') === 'SERVICIO' ? 'SERVICIO' : 'PRODUCTO';
    }

    protected function esProductoServicio($productoExistente): bool
    {
        return ($productoExistente->PRO_TipoProducto ?? null) === 'SERVICIO';
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $query = Producto::where('PRO_Nombre', '=', $request->get('PRO_Nombre'))->get();
            if ($query->count() != 0) {
                return response()->json(['error' => 'Producto ya registrado'], 401);
            }

            $producto = new Producto();
            $producto->PRO_Nombre = $request->PRO_Nombre;
            $producto->PRO_Descripcion = $request->PRO_Descripcion;
            $producto->PRO_PrecioCompra = $request->PRO_PrecioCompra;
            $producto->PRO_PrecioVenta = $request->PRO_PrecioVenta;
            $producto->PRO_Marca = $request->PRO_Marca;
            $producto->PRO_TipoProducto = $this->tipoProducto($request);
            // Un servicio no tiene stock que reponer, asi que no tiene
            // sentido pedirle un minimo de alerta.
            $producto->PRO_StockMinimo = $producto->PRO_TipoProducto === 'SERVICIO' ? 0 : ($request->PRO_StockMinimo ?? 0);
            $producto->PRO_CodigoInterno = $request->PRO_CodigoInterno ?: null;
            $producto->PRO_CodigoFabricacion = $request->PRO_CodigoFabricacion ?: null;
            $producto->PRO_MostrarCatalogo = $request->boolean('PRO_MostrarCatalogo');
            $producto->PRO_Status = $request->PRO_Status ?? 1;
            $producto->CAT_Id = $request->CAT_Id;
            $producto->save();

            $ubicacionNegocio = tenant() ? tenant('tipo_negocio') : '';
            $id = tenant() ? tenant('id') : null;
            $path = public_path('storage/' . $ubicacionNegocio . '/' . $id . '/archivos/producto/');

            $file = $request->file('file');
            if ($file) {
                $limiteStorage = (float) tenant('storage_limit_mb');
                if ($limiteStorage > 0 && tenant_storage_usado_mb() + ($file->getSize() / 1024 / 1024) > $limiteStorage) {
                    throw new Exception('Tu plan alcanzó el límite de almacenamiento (' . $limiteStorage . ' MB). Actualiza tu plan para subir más archivos.');
                }

                if (! file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $extension = $file->getClientOriginalExtension();
                $fileName = $producto->PRO_Id . '.' . $extension;
                $file->move($path, $fileName);

                DB::table('producto')->where('PRO_Id', $producto->PRO_Id)->update(['PRO_Imagen' => $fileName]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => 'Producto Registrado Exitosamente!', compact('producto')]);
    }

    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $producto = Producto::find($id);
            $producto->PRO_Nombre = $request->PRO_Nombre;
            $producto->PRO_Descripcion = $request->PRO_Descripcion;
            $producto->PRO_PrecioCompra = $request->PRO_PrecioCompra;
            $producto->PRO_PrecioVenta = $request->PRO_PrecioVenta;
            $producto->PRO_Marca = $request->PRO_Marca;
            $producto->PRO_TipoProducto = $this->tipoProducto($request);
            $producto->PRO_StockMinimo = $producto->PRO_TipoProducto === 'SERVICIO' ? 0 : ($request->PRO_StockMinimo ?? 0);
            $producto->PRO_CodigoInterno = $request->PRO_CodigoInterno ?: null;
            $producto->PRO_CodigoFabricacion = $request->PRO_CodigoFabricacion ?: null;
            $producto->PRO_MostrarCatalogo = $request->boolean('PRO_MostrarCatalogo');
            $producto->PRO_Status = $request->PRO_Status ?? 1;
            $producto->CAT_Id = $request->CAT_Id;
            $producto->update();

            $ubicacionNegocio = tenant() ? tenant('tipo_negocio') : '';
            $tenantId = tenant() ? tenant('id') : null;
            $path = public_path('storage/' . $ubicacionNegocio . '/' . $tenantId . '/archivos/producto/');

            $file = $request->file('file');
            if ($file) {
                $limiteStorage = (float) tenant('storage_limit_mb');
                if ($limiteStorage > 0 && tenant_storage_usado_mb() + ($file->getSize() / 1024 / 1024) > $limiteStorage) {
                    throw new Exception('Tu plan alcanzó el límite de almacenamiento (' . $limiteStorage . ' MB). Actualiza tu plan para subir más archivos.');
                }

                if (! file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $extension = $file->getClientOriginalExtension();
                $fileName = $producto->PRO_Id . '.' . $extension;
                $file->move($path, $fileName);

                DB::table('producto')->where('PRO_Id', $producto->PRO_Id)->update(['PRO_Imagen' => $fileName]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => 'Producto Editado Exitosamente.', compact('producto')]);
    }

    /**
     * Plantilla Excel para la carga masiva de productos, con las columnas
     * propias de este vertical (codigos, tipo de producto, catalogo web)
     * que la plantilla de generico no tiene.
     */
    public function plantillaImportacion()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Productos');

        $encabezados = [
            'Nombre', 'Categoria', 'Marca', 'Descripcion', 'Precio Compra', 'Precio Venta', 'Stock Inicial', 'Stock Minimo',
            'Codigo Interno', 'Codigo Fabricacion', 'Tipo (PRODUCTO o SERVICIO)', 'Mostrar en Catalogo Web (SI/NO)',
        ];
        $ejemplo = [
            'ACEITE 20W50 1L', 'LUBRICANTES', 'LIQUI MOLY', 'Aceite mineral para motor', 25.00, 35.00, 10, 3,
            'INT-0001', 'LM-20W50-1L', 'PRODUCTO', 'SI',
        ];

        $ultimaColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($encabezados));

        $sheet->fromArray($encabezados, null, 'A1');
        $sheet->getStyle("A1:{$ultimaColumna}1")->getFont()->setBold(true);
        foreach (range('A', $ultimaColumna) as $col) {
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        // Fila de ejemplo, para que quede claro el formato esperado.
        $sheet->fromArray($ejemplo, null, 'A2');

        // Hoja aparte con las categorias que ya existen en el sistema, para
        // que el usuario las escriba tal cual y no cree una nueva por error
        // de tipeo (ej. "Lubricantes" vs "Lubricante").
        $categorias = DB::table('categoria')->orderBy('CAT_Nombre')->pluck('CAT_Nombre')->filter()->values();

        $hojaCategorias = $spreadsheet->createSheet();
        $hojaCategorias->setTitle('Categorias existentes');
        $hojaCategorias->fromArray(['Categorias ya registradas en el sistema'], null, 'A1');
        $hojaCategorias->getStyle('A1')->getFont()->setBold(true);
        $hojaCategorias->getColumnDimension('A')->setWidth(35);
        if ($categorias->isNotEmpty()) {
            $hojaCategorias->fromArray($categorias->map(fn ($c) => [$c])->toArray(), null, 'A2');
        }

        // Desplegable en la columna Categoria (filas 2 a 500) que sugiere las
        // existentes, pero sin bloquear que se escriba una categoria nueva.
        if ($categorias->isNotEmpty()) {
            $ultimaFila = 1 + $categorias->count();
            $rango = "'Categorias existentes'!\$A\$2:\$A\$$ultimaFila";
            for ($fila = 2; $fila <= 500; $fila++) {
                $validacion = $sheet->getCell("B$fila")->getDataValidation();
                $validacion->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validacion->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validacion->setAllowBlank(true);
                $validacion->setShowDropDown(true);
                $validacion->setShowInputMessage(true);
                $validacion->setShowErrorMessage(false);
                $validacion->setPromptTitle('Categoria');
                $validacion->setPrompt('Elige una existente o escribe una nueva.');
                $validacion->setFormula1($rango);
            }
        }

        // Desplegable en la columna Tipo (solo PRODUCTO/SERVICIO, lista fija
        // asi que no hace falta una hoja de apoyo como la de categorias).
        $colTipo = array_search('Tipo (PRODUCTO o SERVICIO)', $encabezados, true);
        $letraTipo = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colTipo + 1);
        for ($fila = 2; $fila <= 500; $fila++) {
            $validacion = $sheet->getCell("{$letraTipo}{$fila}")->getDataValidation();
            $validacion->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validacion->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validacion->setAllowBlank(true);
            $validacion->setShowDropDown(true);
            $validacion->setShowInputMessage(true);
            $validacion->setShowErrorMessage(true);
            $validacion->setPromptTitle('Tipo');
            $validacion->setPrompt('Vacio = PRODUCTO.');
            $validacion->setErrorTitle('Valor invalido');
            $validacion->setError('Solo PRODUCTO o SERVICIO.');
            $validacion->setFormula1('"PRODUCTO,SERVICIO"');
        }

        // Desplegable en la columna Mostrar en Catalogo Web (SI/NO).
        $colCatalogo = array_search('Mostrar en Catalogo Web (SI/NO)', $encabezados, true);
        $letraCatalogo = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCatalogo + 1);
        for ($fila = 2; $fila <= 500; $fila++) {
            $validacion = $sheet->getCell("{$letraCatalogo}{$fila}")->getDataValidation();
            $validacion->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validacion->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validacion->setAllowBlank(true);
            $validacion->setShowDropDown(true);
            $validacion->setShowInputMessage(true);
            $validacion->setShowErrorMessage(true);
            $validacion->setPromptTitle('Mostrar en Catalogo Web');
            $validacion->setPrompt('Vacio = SI.');
            $validacion->setErrorTitle('Valor invalido');
            $validacion->setError('Solo SI o NO.');
            $validacion->setFormula1('"SI,NO"');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'plantilla_productos.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Lee y valida una fila del Excel de importacion (sin tocar la BD salvo
     * lecturas). Devuelve ['error' => string] si la fila es invalida, o los
     * datos ya limpios/tipados listos para usar tanto en la previsualizacion
     * como en el procesamiento real -- asi ambas rutas quedan siempre de
     * acuerdo en que es una fila valida.
     */
    private function parsearFilaImportacion(array $fila): array
    {
        [$nombre, $categoriaNombre, $marca, $descripcion, $precioCompra, $precioVenta, $stockInicial, $stockMinimo, $codigoInterno, $codigoFabricacion, $tipoProductoTexto, $mostrarCatalogoTexto] = array_pad($fila, 12, null);

        $nombre = trim((string) $nombre);
        $categoriaNombre = trim((string) $categoriaNombre);

        // Fila totalmente vacia (ej. al final del archivo): se ignora en silencio.
        if ($nombre === '' && $categoriaNombre === '') {
            return ['vacia' => true];
        }

        if ($nombre === '') {
            return ['error' => 'Falta el nombre del producto.'];
        }

        if ($categoriaNombre === '') {
            return ['error' => "\"$nombre\": falta la categoria."];
        }

        if (!is_numeric($precioVenta) || (float) $precioVenta < 0) {
            return ['error' => "\"$nombre\": el precio de venta no es un numero valido."];
        }

        if (!is_numeric($stockInicial) || (float) $stockInicial < 0) {
            return ['error' => "\"$nombre\": el stock inicial no es un numero valido."];
        }

        $stockMinimoTexto = trim((string) $stockMinimo);
        if ($stockMinimoTexto !== '' && (!is_numeric($stockMinimoTexto) || (float) $stockMinimoTexto < 0)) {
            return ['error' => "\"$nombre\": el stock minimo no es un numero valido."];
        }
        $stockMinimo = $stockMinimoTexto === '' ? 0 : (float) $stockMinimoTexto;

        $codigoInterno = trim((string) $codigoInterno) ?: null;
        $codigoFabricacion = trim((string) $codigoFabricacion) ?: null;

        // Tipo: vacio = PRODUCTO (para no romper plantillas viejas, de antes
        // de que existiera esta columna).
        $tipoProductoTexto = mb_strtoupper(trim((string) $tipoProductoTexto));
        if ($tipoProductoTexto !== '' && !in_array($tipoProductoTexto, ['PRODUCTO', 'SERVICIO'], true)) {
            return ['error' => "\"$nombre\": el Tipo debe ser PRODUCTO o SERVICIO."];
        }
        $tipoProducto = $tipoProductoTexto === 'SERVICIO' ? 'SERVICIO' : 'PRODUCTO';

        if ($tipoProducto === 'SERVICIO' && $stockInicial > 0) {
            return ['error' => "\"$nombre\": es un Servicio, no se le puede cargar stock inicial."];
        }

        // Mostrar en Catalogo Web: vacio = SI (mismo default que el checkbox
        // del formulario normal de creacion).
        $mostrarCatalogoTexto = mb_strtoupper(trim((string) $mostrarCatalogoTexto));
        if ($mostrarCatalogoTexto !== '' && !in_array($mostrarCatalogoTexto, ['SI', 'NO'], true)) {
            return ['error' => "\"$nombre\": \"Mostrar en Catalogo Web\" debe ser SI o NO."];
        }
        $mostrarCatalogo = $mostrarCatalogoTexto !== 'NO';

        return [
            'nombre' => $nombre,
            'categoriaNombre' => $categoriaNombre,
            'marca' => $marca,
            'descripcion' => $descripcion,
            'precioCompra' => is_numeric($precioCompra) ? (float) $precioCompra : 0,
            'precioVenta' => (float) $precioVenta,
            'stockInicial' => (float) $stockInicial,
            'stockMinimo' => $stockMinimo,
            'codigoInterno' => $codigoInterno,
            'codigoFabricacion' => $codigoFabricacion,
            'tipoProducto' => $tipoProducto,
            'mostrarCatalogo' => $mostrarCatalogo,
        ];
    }

    /**
     * Un producto de la fila se considera "el mismo" que uno ya existente
     * solo si coinciden Nombre Y Codigo Interno (ambos vacios cuenta como
     * coincidencia, para no romper productos viejos sin codigo). Antes solo
     * se comparaba el nombre, lo que hacia que dos productos distintos con
     * el mismo nombre pero codigo distinto terminaran mezclados en un solo
     * stock.
     */
    private function buscarProductoDuplicado(string $nombre, ?string $codigoInterno)
    {
        $candidatos = DB::table('producto')
            ->whereRaw('LOWER(PRO_Nombre) = ?', [mb_strtolower($nombre)])
            ->get();

        if ($candidatos->isEmpty()) {
            return null;
        }

        $codigoNormalizado = $codigoInterno !== null ? mb_strtolower(trim($codigoInterno)) : '';

        foreach ($candidatos as $candidato) {
            $codigoExistente = $candidato->PRO_CodigoInterno !== null ? mb_strtolower(trim($candidato->PRO_CodigoInterno)) : '';
            if ($codigoExistente === $codigoNormalizado) {
                return $candidato;
            }
        }

        return null;
    }

    /**
     * Previsualizacion de la carga masiva: lee el Excel y clasifica cada
     * fila en nueva/repetida/error SIN escribir nada en la BD, para que el
     * usuario vea que productos van a chocar con uno ya existente (mismo
     * Nombre + Codigo Interno) antes de decidir si a esos se les agrega
     * stock o no.
     */
    public function importarPreview(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($request->file('archivo')->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('archivo')->getRealPath());
        $filas = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        array_shift($filas);

        $nuevos = [];
        $repetidos = [];
        $errores = [];
        $numeroFila = 1;

        foreach ($filas as $fila) {
            $numeroFila++;

            $datos = $this->parsearFilaImportacion($fila);

            if (!empty($datos['vacia'])) {
                continue;
            }

            if (isset($datos['error'])) {
                $errores[] = ['fila' => $numeroFila, 'detalle' => $datos['error']];
                continue;
            }

            $existente = $this->buscarProductoDuplicado($datos['nombre'], $datos['codigoInterno']);

            if ($existente) {
                if ($this->esProductoServicio($existente) && $datos['stockInicial'] > 0) {
                    $errores[] = ['fila' => $numeroFila, 'detalle' => "\"{$datos['nombre']}\": es un Servicio, no se le puede agregar stock."];
                    continue;
                }

                $stockActual = DB::table('lote')->where('PRO_Id', $existente->PRO_Id)->sum('LOT_CantidadReal');

                $repetidos[] = [
                    'fila' => $numeroFila,
                    'nombre' => $datos['nombre'],
                    'codigo_interno' => $datos['codigoInterno'],
                    'categoria' => $datos['categoriaNombre'],
                    'stock_actual' => (float) $stockActual,
                    'stock_a_agregar' => $datos['stockInicial'],
                ];
            } else {
                $nuevos[] = [
                    'fila' => $numeroFila,
                    'nombre' => $datos['nombre'],
                    'codigo_interno' => $datos['codigoInterno'],
                    'categoria' => $datos['categoriaNombre'],
                    'stock_inicial' => $datos['stockInicial'],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'resumen' => [
                'nuevos' => count($nuevos),
                'repetidos' => count($repetidos),
                'errores' => count($errores),
            ],
            'nuevos' => $nuevos,
            'repetidos' => $repetidos,
            'errores' => $errores,
        ]);
    }

    /**
     * Carga masiva de productos desde un Excel, con las columnas propias de
     * este vertical (codigos, tipo de producto, catalogo web).
     *
     * omitir_stock_filas: numeros de fila (los que ya vio el usuario en
     * importarPreview como "repetidos") a los que el usuario decidio NO
     * agregarles el stock de esta carga -- se dejan sin tocar en vez de
     * sumarles un lote nuevo.
     */
    public function importar(Request $request)
    {
        $request->validate([
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'archivo' => 'required|file|mimes:xlsx,xls,csv',
            'omitir_stock_filas' => 'nullable|array',
            'omitir_stock_filas.*' => 'integer',
        ]);

        $filasAOmitirStock = array_map('intval', $request->get('omitir_stock_filas', []));

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($request->file('archivo')->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('archivo')->getRealPath());
        $filas = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        // La primera fila son encabezados; se ignora.
        array_shift($filas);

        $categoriasCache = collect(
            DB::table('categoria')->get(['CAT_Id', 'CAT_Nombre'])
        )->mapWithKeys(fn ($c) => [mb_strtolower(trim($c->CAT_Nombre)) => $c->CAT_Id]);

        $claseGeneral = null;
        $categoriasCreadas = [];
        $resultados = [];
        $creados = 0;
        $conStockAgregado = 0;
        $repetidosSinCambios = 0;
        $errores = 0;
        $numeroFila = 1; // fila 1 = encabezados

        DB::beginTransaction();
        try {
            foreach ($filas as $fila) {
                $numeroFila++;

                $datos = $this->parsearFilaImportacion($fila);

                if (!empty($datos['vacia'])) {
                    continue;
                }

                if (isset($datos['error'])) {
                    $resultados[] = ['fila' => $numeroFila, 'estado' => 'error', 'detalle' => $datos['error']];
                    $errores++;
                    continue;
                }

                $nombre = $datos['nombre'];
                $categoriaNombre = $datos['categoriaNombre'];
                $precioCompra = $datos['precioCompra'];
                $precioVenta = $datos['precioVenta'];
                $stockInicial = $datos['stockInicial'];

                $claveCategoria = mb_strtolower($categoriaNombre);
                if (!$categoriasCache->has($claveCategoria)) {
                    if (!$claseGeneral) {
                        $claseGeneral = DB::table('clase')->orderBy('CLA_Id')->first()
                            ?? (object) ['CLA_Id' => DB::table('clase')->insertGetId(['CLA_Nombre' => 'General'])];
                    }

                    $nuevaCatId = DB::table('categoria')->insertGetId([
                        'CAT_Nombre' => $categoriaNombre,
                        'CLA_Id' => $claseGeneral->CLA_Id,
                    ]);
                    $categoriasCache[$claveCategoria] = $nuevaCatId;
                    $categoriasCreadas[] = $categoriaNombre;
                }
                $catId = $categoriasCache[$claveCategoria];

                $productoExistente = $this->buscarProductoDuplicado($nombre, $datos['codigoInterno']);

                if ($productoExistente && $this->esProductoServicio($productoExistente) && $stockInicial > 0) {
                    $resultados[] = ['fila' => $numeroFila, 'estado' => 'error', 'detalle' => "\"$nombre\": es un Servicio, no se le puede agregar stock."];
                    $errores++;
                    continue;
                }

                $omitirStockDeEstaFila = in_array($numeroFila, $filasAOmitirStock, true);

                if ($productoExistente) {
                    $proId = $productoExistente->PRO_Id;

                    if ($omitirStockDeEstaFila) {
                        $estado = 'repetido_sin_cambios';
                        $repetidosSinCambios++;
                    } else {
                        $estado = 'stock_agregado';
                        $conStockAgregado++;
                    }
                } else {
                    $datosProducto = [
                        'PRO_Nombre' => $nombre,
                        'PRO_Descripcion' => $datos['descripcion'],
                        'PRO_PrecioCompra' => $precioCompra,
                        'PRO_PrecioVenta' => $precioVenta,
                        'PRO_Marca' => $datos['marca'],
                        'PRO_StockMinimo' => $datos['stockMinimo'],
                        'PRO_Status' => 1,
                        'CAT_Id' => $catId,
                        'PRO_CodigoInterno' => $datos['codigoInterno'],
                        'PRO_CodigoFabricacion' => $datos['codigoFabricacion'],
                        'PRO_TipoProducto' => $datos['tipoProducto'],
                        'PRO_MostrarCatalogo' => $datos['mostrarCatalogo'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $proId = DB::table('producto')->insertGetId($datosProducto);
                    $estado = 'creado';
                    $creados++;
                }

                if ($stockInicial > 0 && !$omitirStockDeEstaFila) {
                    DB::table('lote')->insert([
                        'ALM_Id' => $request->ALM_Id,
                        'PRO_Id' => $proId,
                        'LOT_TipoIngreso' => 'CARGA_MASIVA',
                        'LOT_IdIngreso' => 0,
                        'LOT_CantidadReal' => $stockInicial,
                        'LOT_CantidadIngreso' => $stockInicial,
                        'LOT_PrecioCompra' => $precioCompra,
                        'LOT_PrecioVenta' => $precioVenta,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $resultados[] = ['fila' => $numeroFila, 'estado' => $estado, 'detalle' => $nombre];
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => 'No se pudo procesar el archivo: ' . $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'resumen' => [
                'creados' => $creados,
                'con_stock_agregado' => $conStockAgregado,
                'repetidos_sin_cambios' => $repetidosSinCambios,
                'errores' => $errores,
                'categorias_creadas' => array_values(array_unique($categoriasCreadas)),
            ],
            'detalle' => $resultados,
        ]);
    }
}
