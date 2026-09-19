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
}
