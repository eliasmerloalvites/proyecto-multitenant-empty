<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrdenPublica;
use App\Services\AppMovil\OrdenesMotoService;
use App\Services\AppMovil\VinculacionMotoService;
use App\Support\ApiRespuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Historial y estado de las ordenes de servicio. Toda orden se resuelve con
 * OrdenesMotoService::localizar(); una referencia ajena responde 404 igual que
 * una inexistente.
 */
class OrdenController extends Controller
{
    public function __construct(private OrdenesMotoService $ordenes, private VinculacionMotoService $vinculacion)
    {
    }

    public function index(Request $request)
    {
        $datos = $request->validate([
            'estado' => 'nullable|in:activas,todas',
            'moto' => 'nullable|integer|min:1',
            'limite' => 'nullable|integer|min:1|max:50',
            'cursor' => 'nullable|string|max:20',
        ]);

        $cuenta = $request->user('cliente');
        $this->vinculacion->sincronizarSiHaceFalta($cuenta);

        $limite = (int) ($datos['limite'] ?? 20);
        $offset = ctype_digit($datos['cursor'] ?? '') ? (int) $datos['cursor'] : 0;

        $todas = $this->ordenes->listar($cuenta, $datos['estado'] ?? 'todas', $datos['moto'] ?? null);
        $pagina = $todas->slice($offset, $limite)->values();
        $siguiente = $offset + $limite < $todas->count() ? (string) ($offset + $limite) : null;

        return ApiRespuesta::ok($pagina->map(fn ($o) => OrdenPublica::resumen($o))->all(), 200, [
            'siguiente_cursor' => $siguiente,
            'total' => $todas->count(),
        ]);
    }

    public function show(Request $request, string $ref)
    {
        $cuenta = $request->user('cliente');
        $orden = $this->ordenes->localizar($cuenta, $ref);

        if (! $orden) {
            return $this->noEncontrada();
        }

        return ApiRespuesta::ok(OrdenPublica::detalle($orden, $this->ordenes, $this->ordenes->puedeDescargarPdf($orden, $cuenta)));
    }

    public function pdf(Request $request, string $ref)
    {
        $cuenta = $request->user('cliente');
        $orden = $this->ordenes->localizar($cuenta, $ref);

        if (! $orden || ! $this->ordenes->puedeDescargarPdf($orden, $cuenta)) {
            return $this->noEncontrada();
        }

        return $this->ordenes->pdf($orden['registro']->prefijo, (int) $orden['registro']->id);
    }

    public function foto(Request $request, string $ref, int $item)
    {
        $cuenta = $request->user('cliente');
        $orden = $this->ordenes->localizar($cuenta, $ref);

        $ruta = $orden
            ? $this->ordenes->rutaFoto($orden['registro']->prefijo, (int) $orden['registro']->id, $item, $request->boolean('miniatura'))
            : null;

        $disco = Storage::disk('public');

        if (! $ruta || ! $disco->exists($ruta)) {
            return $this->noEncontrada();
        }

        return response()->file($disco->path($ruta), ['Cache-Control' => 'private, max-age=3600']);
    }

    private function noEncontrada()
    {
        return ApiRespuesta::error('NO_ENCONTRADO', 'No encontramos esa orden.', 404);
    }
}
