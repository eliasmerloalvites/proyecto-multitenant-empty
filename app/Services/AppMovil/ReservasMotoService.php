<?php

namespace App\Services\AppMovil;

use App\Models\TenantTallerMotos\CuentaMovil;
use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Models\TenantTallerMotos\Moto;
use App\Models\TenantTallerMotos\Reservacion;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Reservas de la app. Reusa las reglas de la reserva web (horario de la sede,
 * slot unico bahia+turno+fecha, 60 dias de anticipacion) y agrega las propias
 * de la cuenta. El cliente nunca elige ni ve bahias ni mecanicos: la bahia se
 * asigna aqui, entre las libres de la sede.
 */
class ReservasMotoService
{
    public const CREADA = 'creada';
    public const SIN_CUPO = 'sin_cupo';
    public const HORARIO_NO_DISPONIBLE = 'horario_no_disponible';
    public const LIMITE = 'limite';

    private const MAX_PENDIENTES = 3;
    private const DIAS_ANTICIPACION = 60;

    private const DIAS = [0 => 'DOMINGO', 1 => 'LUNES', 2 => 'MARTES', 3 => 'MIERCOLES', 4 => 'JUEVES', 5 => 'VIERNES', 6 => 'SABADO'];

    public function sedes(): Collection
    {
        return DB::table('almacen')->where('ALM_Status', 1)
            ->get(['ALM_Id as id', 'ALM_NombreAlmacen as nombre', 'ALM_Direccion as direccion', 'ALM_Distrito as distrito']);
    }

    /** @return Collection<int, array{id:int, nombre:string, hora:?string, libres:int}> */
    public function disponibilidad(int $sedeId, string $fecha): Collection
    {
        $bahias = DB::table('bahia')->where('ALM_Id', $sedeId)->where('BAH_Estado', 'ACT')->pluck('BAH_Id');

        $horarios = DB::table('horario as h')
            ->join('turno as t', 't.TUR_Id', '=', 'h.TUR_Id')
            ->where('h.ALM_Id', $sedeId)->where('h.HOR_Dia', $this->diaDe($fecha))
            ->where('h.HOR_Estado', 'ACT')->where('t.TUR_Estado', 'ACT')
            ->orderBy('h.TUR_Id')
            ->get(['t.TUR_Id', 't.TUR_Nombre', 'h.HOR_Detalle']);

        $ocupadas = Reservacion::where('RES_FechaProgramada', $fecha)->where('RES_Estado', 'ACT')
            ->where('RES_State', '!=', 'RECHAZADO')->whereIn('BAH_Id', $bahias)
            ->get(['TUR_Id', 'BAH_Id'])->groupBy('TUR_Id');

        return $horarios->map(fn ($h) => [
            'id' => (int) $h->TUR_Id,
            'nombre' => $h->TUR_Nombre,
            'hora' => $h->HOR_Detalle,
            'libres' => max(0, $bahias->count() - ($ocupadas[$h->TUR_Id] ?? collect())->count()),
        ]);
    }

    /** @return array{0:string, 1:?Reservacion} */
    public function crear(CuentaMovil $cuenta, Moto $moto, int $sedeId, int $turnoId, string $fecha, ?string $detalle, ?int $marcaId = null): array
    {
        $enHorario = DB::table('horario')->where('ALM_Id', $sedeId)->where('TUR_Id', $turnoId)
            ->where('HOR_Dia', $this->diaDe($fecha))->where('HOR_Estado', 'ACT')->exists();

        if (! $enHorario) {
            return [self::HORARIO_NO_DISPONIBLE, null];
        }

        $pendientes = Reservacion::where('RES_Estado', 'ACT')->where('RES_State', 'PENDIENTE')
            ->where(fn ($q) => $q->where('CMV_Id', $cuenta->CMV_Id)->orWhere('RES_Celular', substr($cuenta->CMV_Celular, -9)))
            ->count();

        if ($pendientes >= self::MAX_PENDIENTES) {
            return [self::LIMITE, null];
        }

        $bahias = DB::table('bahia')->where('ALM_Id', $sedeId)->where('BAH_Estado', 'ACT')->orderBy('BAH_Id')->pluck('BAH_Id');

        foreach ($bahias as $bahiaId) {
            if (Reservacion::slotEstaOcupado($bahiaId, $turnoId, $fecha)) {
                continue;
            }

            try {
                $reserva = Reservacion::create([
                    'ALM_Id' => $sedeId, 'TUR_Id' => $turnoId, 'BAH_Id' => $bahiaId,
                    'RES_Placa' => $moto->MOT_Placa,
                    'RES_Moto' => $moto->MOT_Descripcion ?: 'MOTO',
                    'MAR_Id' => \App\Services\TenantTallerMotos\MarcaVehiculoService::marcaValida($marcaId),
                    'RES_Cliente' => mb_strtoupper($cuenta->CMV_Nombre ?: 'CLIENTE APP'),
                    'RES_Celular' => substr($cuenta->CMV_Celular, -9),
                    'RES_Detalle' => $detalle,
                    'RES_Adicional' => 'Reservada desde la app',
                    'RES_FechaProgramada' => $fecha,
                    'RES_State' => 'PENDIENTE',
                    'RES_Estado' => 'ACT',
                ]);
                Reservacion::where('RES_Id', $reserva->RES_Id)->update(['CMV_Id' => $cuenta->CMV_Id]);

                return [self::CREADA, $reserva->fresh()];
            } catch (QueryException $e) {
                if (! Reservacion::esConflictoDeSlot($e)) {
                    throw $e;
                }
                // Otra persona tomo esa bahia justo ahora: probar con la siguiente.
            }
        }

        return [self::SIN_CUPO, null];
    }

    /** Reservas de la cuenta: las que hizo desde la app o las hechas con su celular. */
    public function deLaCuenta(CuentaMovil $cuenta, bool $soloProximas): Collection
    {
        $nacional = substr($cuenta->CMV_Celular, -9);

        $filas = Reservacion::where(fn ($q) => $q->where('CMV_Id', $cuenta->CMV_Id)->orWhere('RES_Celular', 'like', '%' . $nacional . '%'))
            ->when($soloProximas, fn ($q) => $q->where('RES_FechaProgramada', '>=', Carbon::today()->toDateString())->where('RES_State', '!=', 'RECHAZADO'))
            ->orderByDesc('RES_FechaProgramada')->limit(100)->get();

        return $filas->filter(fn ($r) => $r->CMV_Id === $cuenta->CMV_Id
            || NormalizadorCelular::normalizar($r->RES_Celular) === $cuenta->CMV_Celular)->values();
    }

    public function localizar(CuentaMovil $cuenta, int $id): ?Reservacion
    {
        return $this->deLaCuenta($cuenta, false)->firstWhere('RES_Id', $id);
    }

    /** Cancela si todavia no hubo check-in ni esta cancelada/rechazada. */
    public function cancelar(Reservacion $reserva): bool
    {
        if ($reserva->RES_CheckIn || $reserva->RES_State === 'RECHAZADO' || $reserva->RES_Estado !== 'ACT') {
            return false;
        }

        Reservacion::where('RES_Id', $reserva->RES_Id)->update(['RES_State' => 'RECHAZADO', 'RES_CanceladaEn' => now()]);

        return true;
    }

    public static function estadoPublico(Reservacion $r): string
    {
        return match (true) {
            $r->RES_State === 'RECHAZADO' => $r->RES_CanceladaEn ? 'CANCELADA' : 'RECHAZADA',
            $r->RES_CheckIn !== null => 'EN_ATENCION',
            $r->RES_State === 'APROBADO' => 'CONFIRMADA',
            default => 'PENDIENTE',
        };
    }

    public function fechaValida(string $fecha): bool
    {
        $f = Carbon::parse($fecha)->startOfDay();

        return $f->gte(Carbon::today()) && $f->lte(Carbon::today()->addDays(self::DIAS_ANTICIPACION));
    }

    private function diaDe(string $fecha): string
    {
        return self::DIAS[Carbon::parse($fecha)->dayOfWeek];
    }
}
