<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AppMovil\CatalogoMovilService;
use App\Support\ApiRespuesta;
use Illuminate\Http\Request;

/** Tienda de la app: catalogo de productos del taller, por sede. */
class CatalogoController extends Controller
{
    public function __construct(private CatalogoMovilService $catalogo)
    {
    }

    public function categorias()
    {
        if ($r = $this->sinTienda()) {
            return $r;
        }

        return ApiRespuesta::ok([
            'categorias' => $this->catalogo->categorias()->map(fn ($c) => ['id' => (int) $c->id, 'nombre' => $c->nombre, 'productos' => (int) $c->productos])->values(),
            'sedes' => $this->catalogo->sedes()->map(fn ($s) => ['id' => (int) $s->id, 'nombre' => $s->nombre, 'direccion' => $s->direccion])->values(),
            'sede_por_defecto' => $this->catalogo->sedePorDefecto(),
        ]);
    }

    public function productos(Request $request)
    {
        if ($r = $this->sinTienda()) {
            return $r;
        }

        $d = $request->validate([
            'sede' => 'nullable|integer|min:1',
            'categoria' => 'nullable|integer|min:1',
            'buscar' => 'nullable|string|max:60',
            'limite' => 'nullable|integer|min:1|max:50',
            'cursor' => 'nullable|string|max:10',
        ]);

        $sede = $this->catalogo->sedeValida($d['sede'] ?? null);
        if (! $sede) {
            return ApiRespuesta::error('SEDE_INVALIDA', 'Esa sede no está disponible.', 422);
        }

        $limite = (int) ($d['limite'] ?? 20);
        $offset = ctype_digit($d['cursor'] ?? '') ? (int) $d['cursor'] : 0;
        $r = $this->catalogo->productos($sede, $d['categoria'] ?? null, $d['buscar'] ?? null, $offset, $limite);

        return ApiRespuesta::ok($r['filas']->values()->all(), 200, [
            'siguiente_cursor' => $r['siguiente'] !== null ? (string) $r['siguiente'] : null,
            'total' => $r['total'],
            'sede' => $sede,
        ]);
    }

    public function show(Request $request, int $producto)
    {
        if ($r = $this->sinTienda()) {
            return $r;
        }

        $sede = $this->catalogo->sedeValida($request->integer('sede') ?: null);
        $detalle = $sede ? $this->catalogo->detalle($sede, $producto) : null;

        return $detalle
            ? ApiRespuesta::ok($detalle)
            : ApiRespuesta::error('NO_ENCONTRADO', 'Ese producto no está en el catálogo.', 404);
    }

    private function sinTienda()
    {
        return tenant_has_module('productos')
            ? null
            : ApiRespuesta::error('TIENDA_NO_DISPONIBLE', 'Este taller no tiene tienda en la app.', 404);
    }
}
