<?php

namespace App\Services\AppMovil;

use App\Models\Tenant\EmpresaFacturacion;
use App\Models\TenantTallerMotos\CuentaMovil;
use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Models\TenantTallerMotos\Moto;
use App\Services\Mantenimiento\RepuestosBahiaSync;
use App\Services\TenantTallerMotos\GestionProcesoService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Ordenes de servicio (= registros de mantenimiento) que una cuenta puede ver.
 *
 * "Orden de servicio" y "mantenimiento" son la MISMA fila en este sistema: el
 * numero de orden ("MPC-000012") se calcula con el prefijo del tipo y el id, y
 * no se guarda. Por eso una orden se identifica con una referencia
 * "{prefijo}-{id}" (ej. "mpc-12").
 *
 * Todo pasa por localizar(): una referencia solo se resuelve si la moto
 * esta en el garaje VERIFICADO de la cuenta y el registro entra en lo que su
 * vinculo permite ver (ver RegistrosMotoService). Cualquier otro caso es
 * "no existe", sin distinguir entre ajeno e inexistente.
 */
class OrdenesMotoService
{
    public const EN_TALLER = 'EN_TALLER';
    public const TRABAJO_TERMINADO = 'TRABAJO_TERMINADO';
    public const ENTREGADA = 'ENTREGADA';
    public const SIN_CERRAR = 'SIN_CERRAR';

    private const ETIQUETAS_ESTADO = [
        self::EN_TALLER => 'En el taller',
        self::TRABAJO_TERMINADO => 'Trabajo terminado',
        self::ENTREGADA => 'Entregada',
        self::SIN_CERRAR => 'Sin cerrar',
    ];

    /** Un registro PENDIENTE sin cierre mas viejo que esto ya no se considera "en el taller". */
    private const DIAS_MAX_EN_TALLER = 30;

    private const CONTROLADORES_PDF = [
        'MGC' => \App\Http\Controllers\TenantTallerMotos\MantenimientoGeneralCarburadaController::class,
        'MGI' => \App\Http\Controllers\TenantTallerMotos\MantenimientoGeneralInyectadaController::class,
        'MPC' => \App\Http\Controllers\TenantTallerMotos\MantenimientoPreventivoCarburadaController::class,
        'MPI' => \App\Http\Controllers\TenantTallerMotos\MantenimientoPreventivoInyectadaController::class,
        'MAV' => \App\Http\Controllers\TenantTallerMotos\MantenimientoActividadVariadaController::class,
        'MGH' => \App\Http\Controllers\TenantTallerMotos\MantenimientoGarantiaHeroController::class,
        'MGL' => \App\Http\Controllers\TenantTallerMotos\MantenimientoGarantiaLifanController::class,
    ];

    public function __construct(private RegistrosMotoService $registros)
    {
    }

    public static function ref(string $prefijo, int $id): string
    {
        return strtolower($prefijo) . '-' . $id;
    }

    public static function numeroOrden(string $prefijo, int $id): string
    {
        return strtoupper($prefijo) . '-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Ordenes de todas las motos verificadas de la cuenta (o de una), del
     * mas reciente al mas antiguo, con su estado ya calculado.
     *
     * @param  string  $estado  'todas' o 'activas' (solo las que siguen en el taller).
     * @return Collection<int, array{registro: object, moto: Moto, vinculo: CuentaMovilMoto, estado: string, entrega: ?string}>
     */
    public function listar(CuentaMovil $cuenta, string $estado = 'todas', ?int $motoId = null): Collection
    {
        $vinculos = CuentaMovilMoto::with('moto')
            ->where('CMV_Id', $cuenta->CMV_Id)
            ->where('CMM_Estado', CuentaMovilMoto::VERIFICADA)
            ->when($motoId, fn ($q) => $q->where('MOT_Id', $motoId))
            ->get();

        $filas = collect();

        foreach ($vinculos as $vinculo) {
            foreach ($this->registros->visiblesPara($vinculo, $cuenta, $vinculo->moto) as $registro) {
                $filas->push(['registro' => $registro, 'moto' => $vinculo->moto, 'vinculo' => $vinculo]);
            }
        }

        $cierres = $this->cierresDeCuentas($filas->pluck('registro.res_id')->filter()->unique()->all());

        $filas = $filas->map(function ($fila) use ($cierres) {
            $cierre = $cierres[$fila['registro']->res_id] ?? null;
            $fila['estado'] = $this->estadoPublico($fila['registro'], $cierre);
            $fila['entrega'] = $cierre ? Carbon::parse($cierre)->toIso8601String() : null;

            return $fila;
        });

        if ($estado === 'activas') {
            $filas = $filas->where('estado', self::EN_TALLER);
        }

        return $filas->sortByDesc(fn ($f) => (string) ($f['registro']->fecha_inicio ?: $f['registro']->fecha))->values();
    }

    /**
     * Resuelve una referencia ("mpc-12") a la orden completa SOLO si la cuenta
     * puede verla. Devuelve null en cualquier otro caso.
     *
     * @return array{registro: object, moto: Moto, vinculo: CuentaMovilMoto, estado: string, entrega: ?string}|null
     */
    public function localizar(CuentaMovil $cuenta, string $ref): ?array
    {
        if (! preg_match('/^(mgc|mgi|mpc|mpi|mav|mgh|mgl)-(\d{1,10})$/', $ref, $m)) {
            return null;
        }

        $prefijo = strtoupper($m[1]);
        $id = (int) $m[2];

        $meta = collect(GestionProcesoService::tipos())->firstWhere('prefijo', $prefijo);
        $placa = $meta ? DB::table($meta['tabla'])->where("{$prefijo}_Id", $id)->value("{$prefijo}_Placa") : null;
        $placaNormalizada = NormalizadorPlaca::normalizar($placa);

        if (! $placaNormalizada) {
            return null;
        }

        $moto = Moto::where('MOT_Placa', $placaNormalizada)->first();

        $vinculo = $moto
            ? CuentaMovilMoto::where('CMV_Id', $cuenta->CMV_Id)->where('MOT_Id', $moto->MOT_Id)->where('CMM_Estado', CuentaMovilMoto::VERIFICADA)->first()
            : null;

        if (! $vinculo) {
            return null;
        }

        $registro = $this->registros->visiblesPara($vinculo, $cuenta, $moto)
            ->first(fn ($r) => $r->prefijo === $prefijo && (int) $r->id === $id);

        if (! $registro) {
            return null;
        }

        $cierre = $this->cierresDeCuentas(array_filter([$registro->res_id]))[$registro->res_id] ?? null;

        return [
            'registro' => $registro,
            'moto' => $moto,
            'vinculo' => $vinculo,
            'estado' => $this->estadoPublico($registro, $cierre),
            'entrega' => $cierre ? Carbon::parse($cierre)->toIso8601String() : null,
        ];
    }

    public static function etiquetaEstado(string $codigo): string
    {
        return self::ETIQUETAS_ESTADO[$codigo] ?? $codigo;
    }

    /**
     * Estado que ve el cliente, derivado de datos que SI existen (no hay un
     * estado "listo para recoger" en el sistema):
     *  - ENTREGADA: la cuenta de bahia de su reserva ya se cerro.
     *  - TRABAJO_TERMINADO: tiene fecha de termino, o el taller ya la aprobo
     *    (la aprobacion es el paso en que se avisa al cliente).
     *  - EN_TALLER: sigue abierta y es reciente.
     *  - SIN_CERRAR: abierta hace mas de 30 dias; casi seguro es un registro
     *    que nadie cerro, y no se le presenta al cliente como "en el taller".
     */
    public function estadoPublico(object $registro, ?string $cierreBahia): string
    {
        if ($cierreBahia) {
            return self::ENTREGADA;
        }

        if ($registro->fecha_termino || $registro->estado === 'APROBADO') {
            return self::TRABAJO_TERMINADO;
        }

        $base = $registro->fecha_inicio ?? $registro->fecha;
        $creado = $base ? Carbon::parse($base) : null;

        return ($creado && $creado->lt(Carbon::now()->subDays(self::DIAS_MAX_EN_TALLER)))
            ? self::SIN_CERRAR
            : self::EN_TALLER;
    }

    /**
     * Fecha de cierre de la cuenta de bahia de cada reserva, para las que ya
     * estan cerradas. [RES_Id => fecha].
     */
    private function cierresDeCuentas(array $resIds): array
    {
        if (! $resIds) {
            return [];
        }

        return DB::table('bahia_cuenta')
            ->whereIn('RES_Id', $resIds)
            ->whereIn('BCT_Estado', ['CERRADA', 'CERRADA_SIN_COBRO'])
            ->whereNotNull('BCT_CerradoEn')
            ->select('RES_Id', DB::raw('MAX(BCT_CerradoEn) as cierre'))
            ->groupBy('RES_Id')
            ->pluck('cierre', 'RES_Id')
            ->all();
    }

    // ------------------------------------------------------------------
    // Detalle de una orden
    // ------------------------------------------------------------------

    /** Campos de texto del registro que se muestran al cliente (lista blanca). */
    public function textos(string $prefijo, int $id): ?object
    {
        $meta = collect(GestionProcesoService::tipos())->firstWhere('prefijo', $prefijo);

        return DB::table($meta['tabla'])
            ->where("{$prefijo}_Id", $id)
            ->select(
                DB::raw("{$prefijo}_DetalleIngreso as solicitado"),
                DB::raw("{$prefijo}_DetalleRealizado as realizado"),
                DB::raw("{$prefijo}_Recomendacion as recomendacion"),
                DB::raw("{$prefijo}_RecomendacionPrioridad as prioridad")
            )
            ->first();
    }

    /** Repuestos usados. El precio solo se incluye si el taller lo permite. */
    public function repuestos(string $prefijo, int $id): array
    {
        $cfg = RepuestosBahiaSync::CONFIG[$prefijo];

        $filas = DB::table($cfg['tabla'])
            ->where($cfg['idField'], $id)
            ->orderBy($cfg['itemField'])
            ->get([$cfg['descField'] . ' as descripcion', $cfg['precioField'] . ' as precio']);

        $conPrecios = $this->mostrarPrecios();

        return $filas->map(fn ($f) => array_filter([
            'descripcion' => trim((string) $f->descripcion),
            'precio' => $conPrecios && $f->precio !== null ? number_format((float) $f->precio, 2, '.', '') : null,
        ], fn ($v) => $v !== null))->all();
    }

    public function mostrarPrecios(): bool
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        return (bool) ($empresa?->app_movil_mostrar_precios ?? true);
    }

    /**
     * Fotos del trabajo: [{item, miniatura(ruta relativa), ruta}]. Las rutas son
     * rutas internas de almacenamiento; la API nunca las entrega tal cual, sino
     * a traves de un endpoint autorizado.
     */
    public function fotos(string $prefijo, int $id): Collection
    {
        $tabla = strtolower($prefijo) . '_imagen';

        return DB::table($tabla)
            ->where("{$prefijo}_Id", $id)
            ->orderBy("{$prefijo}I_Item")
            ->get([DB::raw("{$prefijo}I_Item as item"), DB::raw("{$prefijo}I_url as url"), DB::raw("{$prefijo}I_Thumb as miniatura")]);
    }

    /** Ruta de la foto en el disco publico, o null si no existe o no es de esta orden. */
    public function rutaFoto(string $prefijo, int $id, int $item, bool $miniatura): ?string
    {
        $foto = $this->fotos($prefijo, $id)->firstWhere('item', $item);

        if (! $foto) {
            return null;
        }

        $url = $miniatura && $foto->miniatura ? $foto->miniatura : $foto->url;
        $ruta = ltrim(str_replace('/storage/', '', (string) $url), '/');

        return $ruta !== '' && ! str_contains($ruta, '..') ? $ruta : null;
    }

    /**
     * El PDF de la orden contiene datos de la persona que figura en ella (nombre,
     * documento, celular), asi que solo se entrega para las ordenes hechas con el
     * celular de la propia cuenta, incluso si su vinculo tiene alcance COMPLETO.
     */
    public function puedeDescargarPdf(array $orden, CuentaMovil $cuenta): bool
    {
        return NormalizadorCelular::normalizar($orden['registro']->celular) === $cuenta->CMV_Celular;
    }

    /** Genera el PDF de la orden de servicio con el mismo documento que usa el taller. */
    public function pdf(string $prefijo, int $id)
    {
        return app(self::CONTROLADORES_PDF[$prefijo])->ordenServicio($id);
    }
}
