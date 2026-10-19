<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Services\TenantTallerMotos\FirmasMantenimientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Firmas digitales de un mantenimiento: {tipo} es el slug de su ruta
 * (generalcarburada, garantiahero, mantenimientoactividadvariada, ...).
 * Reglas en FirmasMantenimientoService.
 */
class FirmaMantenimientoController extends Controller
{
    public function __construct(private FirmasMantenimientoService $servicio)
    {
    }

    public function estado(string $tipo, int $id)
    {
        $meta = $this->meta($tipo);

        try {
            return response()->json($this->servicio->estado($meta, $id, Auth::user()));
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function firmar(Request $request, string $tipo, int $id)
    {
        $meta = $this->meta($tipo);
        $request->validate([
            'rol' => 'required|in:' . implode(',', FirmasMantenimientoService::ROLES),
            'imagen' => 'required|string|max:600000',
            'nombre' => 'nullable|string|max:150',
            'documento' => 'nullable|string|max:20',
        ]);

        try {
            $this->servicio->firmar($meta, $id, $request->rol, $request->imagen, $request->nombre, $request->documento, Auth::user(), $request->ip());
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $estado = $this->servicio->estado($meta, $id, Auth::user());
        $mensaje = $estado['cerrado']
            ? 'Firma del cliente registrada. El mantenimiento quedó cerrado.'
            : 'Firma registrada.';

        return response()->json(['success' => $mensaje, 'estado' => $estado]);
    }

    /** Imagen de una firma (disco privado: solo personal con sesion). */
    public function imagen(string $tipo, int $id, string $rol)
    {
        $meta = $this->meta($tipo);
        $ruta = DB::table('mantenimiento_firma')
            ->where('FIR_Tabla', $meta['tabla'])->where('FIR_RegistroId', $id)->where('FIR_Rol', strtoupper($rol))
            ->value('FIR_Imagen');
        $png = $ruta ? FirmasMantenimientoService::contenido($ruta) : null;
        abort_unless($png, 404);

        return response($png, 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'private, max-age=600']);
    }

    public function reabrir(Request $request, string $tipo, int $id)
    {
        $meta = $this->meta($tipo);
        $request->validate(['motivo' => 'required|string|min:5|max:255']);

        try {
            $this->servicio->reabrir($meta, $id, $request->motivo, Auth::user());
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => 'Mantenimiento reabierto: las firmas se borraron y deberán firmar de nuevo.',
            'estado' => $this->servicio->estado($meta, $id, Auth::user()),
        ]);
    }

    private function meta(string $tipo): array
    {
        abort_unless(FirmasMantenimientoService::disponible(), 404);
        $meta = FirmasMantenimientoService::tipoPorSlug($tipo);
        abort_unless($meta, 404);

        return $meta;
    }
}
