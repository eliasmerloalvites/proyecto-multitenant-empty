<?php

namespace App\Http\Controllers\Api\V1\Central;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TallerPublico;
use App\Models\Tenant;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Support\ApiRespuesta;

/**
 * Directorio publico de talleres (solo en el dominio central). La app
 * escanea un QR con el codigo publico del taller y aqui se le devuelve a que
 * direccion (base_url) debe conectarse y su marca basica.
 *
 * El codigo solo IDENTIFICA al taller: no da acceso a ningun dato privado.
 * Es el unico lugar por el que la app se entera de una direccion de taller, y
 * solo devuelve talleres activos con la app movil contratada y un dominio
 * registrado, asi que la app nunca se conecta a un host elegido libremente.
 */
class DirectorioController extends Controller
{
    public function show(string $codigo)
    {
        $codigo = strtoupper(trim($codigo));

        // Mismo error para "no existe", "suspendido" y "sin app contratada":
        // no se revela el estado comercial de un taller.
        $noEncontrado = fn () => ApiRespuesta::error('TALLER_NO_ENCONTRADO', 'No encontramos ese taller.', 404);

        if (! preg_match('/^[A-Z0-9]{' . (int) config('app_movil.directorio.longitud_codigo') . '}$/', $codigo)) {
            return $noEncontrado();
        }

        $tenant = Tenant::where('data->codigo_app', $codigo)->first();

        if (! $tenant || $tenant->status !== 'activo' || $tenant->tipo_negocio !== 'tallermoto') {
            return $noEncontrado();
        }

        if (! (($tenant->modules ?? [])['app_movil'] ?? false)) {
            return $noEncontrado();
        }

        $dominios = $tenant->domains()->orderBy('id')->pluck('domain');
        $sufijo = config('app_movil.sufijo_dominio');
        $dominio = ($sufijo ? $dominios->first(fn ($d) => str_ends_with($d, $sufijo)) : null) ?? $dominios->first();

        if (! $dominio) {
            return $noEncontrado();
        }

        $base = config('app_movil.esquema') . '://' . $dominio . (config('app_movil.puerto') ? ':' . config('app_movil.puerto') : '');

        $taller = $tenant->run(function () use ($base) {
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            $sedes = Almacen::where('ALM_Status', 1)->orderBy('ALM_EsPrincipal', 'desc')->orderBy('ALM_NombreAlmacen')->get();

            return TallerPublico::datos($empresa, $sedes, $base);
        });

        return ApiRespuesta::ok([
            'codigo' => $codigo,
            'base_url' => $base,
            'taller' => $taller,
        ]);
    }
}
