<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\EsquemaComision;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EsquemaComisionController extends Controller
{
    /**
     * "Sube/baja el esquema" de un vendedor: inserta una versión nueva y
     * desactiva la anterior, en vez de editarla. Los clientes que el
     * vendedor ya tenía referidos NO se ven afectados — su fila en
     * cliente_vendedor ya tiene copiado (congelado) el % y los meses del
     * esquema con el que fueron referidos, y nunca se vuelve a leer contra
     * esta tabla.
     */
    public function store(Request $request, Vendedor $vendedor)
    {
        $validated = $request->validate([
            'porcentaje' => 'required|numeric|min:0|max:100',
            'meses_duracion' => 'required|integer|min:1|max:60',
        ]);

        DB::transaction(function () use ($vendedor, $validated) {
            $vendedor->esquemasComision()->where('activo', true)->update(['activo' => false]);

            EsquemaComision::create([
                'vendedor_id' => $vendedor->id,
                'porcentaje' => $validated['porcentaje'],
                'meses_duracion' => $validated['meses_duracion'],
                'activo' => true,
            ]);
        });

        \App\Models\AuditLog::registrar(
            'vendedor.esquema_actualizado',
            'Cambió el esquema de comisión de "' . ($vendedor->user->name ?? 'Vendedor #' . $vendedor->id) . '" a ' . $validated['porcentaje'] . '% / ' . $validated['meses_duracion'] . ' meses',
            ['vendedor_id' => $vendedor->id, 'porcentaje' => $validated['porcentaje'], 'meses_duracion' => $validated['meses_duracion']]
        );

        return response()->json(['success' => 'Esquema de comisión actualizado. Aplica solo a clientes nuevos que traiga de ahora en adelante.']);
    }
}
