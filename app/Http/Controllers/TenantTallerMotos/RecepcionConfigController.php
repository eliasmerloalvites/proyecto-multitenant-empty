<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\RecepcionCategoria;
use App\Models\TenantTallerMotos\RecepcionItem;
use Illuminate\Http\Request;

/**
 * Configuracion del "Estado de Recepcion de la Motocicleta": categorias e
 * items 100% editables por el tenant (agregar, renombrar, reordenar,
 * activar/desactivar) sin tocar codigo. Nunca se borra nada de verdad
 * (solo *_Activo = 0) para no perder el historico de recepciones ya
 * guardadas que referencian un item/categoria vieja — ver las migraciones
 * de recepcion_item/recepcion_respuesta para el detalle.
 */
class RecepcionConfigController extends Controller
{
    public function index()
    {
        $categorias = RecepcionCategoria::with(['items' => fn ($q) => $q->orderBy('RIT_Orden')])
            ->orderBy('RCT_Grupo')
            ->orderBy('RCT_Orden')
            ->get();

        return view('tenant_tallermoto.configuracion.recepcion.index', [
            'categorias' => $categorias,
            'tiposCampo' => RecepcionItem::TIPOS_VALIDOS,
        ]);
    }

    public function storeCategoria(Request $request)
    {
        $validated = $this->validarCategoria($request);

        $categoria = RecepcionCategoria::create($validated);

        return response()->json(['success' => true, 'categoria' => $categoria]);
    }

    public function updateCategoria(Request $request, RecepcionCategoria $categoria)
    {
        $validated = $this->validarCategoria($request);

        $categoria->update($validated);

        return response()->json(['success' => true, 'categoria' => $categoria]);
    }

    public function toggleCategoria(RecepcionCategoria $categoria)
    {
        $categoria->update(['RCT_Activo' => !$categoria->RCT_Activo]);

        return response()->json(['success' => true, 'activo' => $categoria->RCT_Activo]);
    }

    public function storeItem(Request $request)
    {
        $validated = $this->validarItem($request);

        $item = RecepcionItem::create($validated);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function updateItem(Request $request, RecepcionItem $item)
    {
        $validated = $this->validarItem($request, $item);

        $item->update($validated);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function toggleItem(RecepcionItem $item)
    {
        $item->update(['RIT_Activo' => !$item->RIT_Activo]);

        return response()->json(['success' => true, 'activo' => $item->RIT_Activo]);
    }

    private function validarCategoria(Request $request): array
    {
        $validated = $request->validate([
            'RCT_Nombre' => 'required|string|max:100',
            'RCT_Grupo' => 'required|string|in:' . RecepcionCategoria::GRUPO_INVENTARIO . ',' . RecepcionCategoria::GRUPO_INSPECCION,
            'RCT_Orden' => 'nullable|integer|min:0',
        ]);

        $validated['RCT_Orden'] = $validated['RCT_Orden'] ?? 0;

        return $validated;
    }

    private function validarItem(Request $request, ?RecepcionItem $item = null): array
    {
        $validated = $request->validate([
            'RCT_Id' => 'required|integer|exists:recepcion_categoria,RCT_Id',
            'RIT_Codigo' => 'required|string|max:60|unique:recepcion_item,RIT_Codigo,' . ($item->RIT_Id ?? 'NULL') . ',RIT_Id',
            'RIT_Etiqueta' => 'required|string|max:150',
            'RIT_TipoCampo' => 'required|string|in:' . implode(',', RecepcionItem::TIPOS_VALIDOS),
            'RIT_Opciones' => 'nullable|array',
            'RIT_Opciones.*' => 'string|max:60',
            'RIT_Orden' => 'nullable|integer|min:0',
        ]);

        // RIT_Opciones solo tiene sentido para SELECT; en el resto el
        // frontend ya sabe que vocabulario usar (SI/NO, BUENO/REGULAR/MALO,
        // OK/REEMPLAZAR) sin necesidad de guardarlo por item.
        $validated['RIT_Opciones'] = $validated['RIT_TipoCampo'] === RecepcionItem::TIPO_SELECT
            ? ($validated['RIT_Opciones'] ?? [])
            : null;
        $validated['RIT_Orden'] = $validated['RIT_Orden'] ?? 0;

        return $validated;
    }
}
