<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BahiaCuenta extends Model
{
    protected $table = 'bahia_cuenta';
    protected $primaryKey = 'BCT_Id';

    const ESTADO_ABIERTA = 'ABIERTA';
    const ESTADO_CERRADA = 'CERRADA';
    const ESTADO_CERRADA_SIN_COBRO = 'CERRADA_SIN_COBRO';

    protected $fillable = [
        'RES_Id',
        'ALM_Id',
        'BAH_Id',
        'VEN_Id',
        'BCT_Estado',
        'USU_Id_Abre',
        'BCT_AbiertoEn',
        'BCT_CerradoEn',
    ];

    protected $casts = [
        'BCT_AbiertoEn' => 'datetime',
        'BCT_CerradoEn' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(BahiaCuentaItem::class, 'BCT_Id', 'BCT_Id');
    }

    public function adelantos()
    {
        return $this->hasMany(BahiaCuentaAdelanto::class, 'BCT_Id', 'BCT_Id');
    }

    /** Lo que el cliente ya pago por adelantado sobre esta cuenta. */
    public function totalAdelantado(): float
    {
        return round((float) $this->adelantos()->sum('BCA_Monto'), 2);
    }

    public function reservacion()
    {
        return $this->belongsTo(Reservacion::class, 'RES_Id', 'RES_Id');
    }

    /**
     * Cuando se anula la venta con la que se cobro esta cuenta, la bahia
     * tiene que volver a poder cobrarse (ej. el cliente dio mal el DNI).
     * La cuenta vuelve a ABIERTA con sus productos y adelantos, se desliga
     * de la venta anulada y se reactiva la reserva. Devuelve false si no
     * habia nada que reabrir o si otra reserva ya tomo ese horario.
     */
    public static function reabrirPorVentaAnulada(int $ventaId): bool
    {
        $cuenta = self::where('VEN_Id', $ventaId)->where('BCT_Estado', self::ESTADO_CERRADA)->first();

        return $cuenta ? $cuenta->reabrir() : false;
    }

    /**
     * Rescata las cuentas de un dia que se cerraron con una venta que ya
     * esta anulada (anuladas antes de que la cuenta se reabriera sola).
     * Devuelve cuantas reabrio.
     */
    public static function reabrirAnuladasDelDia(int $almacenId, string $fecha): int
    {
        $cuentas = self::from('bahia_cuenta as bc')
            ->join('reservacion as r', 'r.RES_Id', '=', 'bc.RES_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'bc.VEN_Id')
            ->where('bc.BCT_Estado', self::ESTADO_CERRADA)
            ->where('dov.DOV_Anulado', 1)
            ->where('r.ALM_Id', $almacenId)
            ->whereDate('r.RES_FechaProgramada', $fecha)
            ->select('bc.*')
            ->get();

        return $cuentas->filter(fn ($c) => $c->reabrir())->count();
    }

    private function reabrir(): bool
    {
        return DB::transaction(function () {
            if (self::where('RES_Id', $this->RES_Id)->where('BCT_Estado', self::ESTADO_ABIERTA)->exists()) {
                return false;
            }

            $reserva = Reservacion::find($this->RES_Id);
            if (! $reserva || Reservacion::slotEstaOcupado($reserva->BAH_Id, $reserva->TUR_Id, $reserva->RES_FechaProgramada, $reserva->RES_Id)) {
                return false;
            }

            $reserva->update(['RES_Estado' => 'ACT']);
            $this->update(['BCT_Estado' => self::ESTADO_ABIERTA, 'VEN_Id' => null, 'BCT_CerradoEn' => null]);

            return true;
        });
    }

    public function estaAbierta(): bool
    {
        return $this->BCT_Estado === self::ESTADO_ABIERTA;
    }

    public function total(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->BCI_Cantidad * $item->BCI_PrecioUnitario);
    }
}
