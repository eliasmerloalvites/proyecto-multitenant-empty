<?php

namespace App\Services\Mantenimiento;

use Illuminate\Support\Facades\DB;

class RepuestosBahiaSync
{
    // Offset alto para que los items de origen BAHIA nunca choquen con la numeracion secuencial de los items MANUAL.
    private const OFFSET_ITEM_BAHIA = 900000;

    public const CONFIG = [
        'MGC' => ['tabla' => 'mgc_detalle_reemplazo', 'idField' => 'MGC_Id', 'itemField' => 'MGCD_Item', 'descField' => 'MGCD_Descripcion', 'precioField' => 'MGC_Precio'],
        'MGI' => ['tabla' => 'mgi_detalle_reemplazo', 'idField' => 'MGI_Id', 'itemField' => 'MGID_Item', 'descField' => 'MGID_Descripcion', 'precioField' => 'MGI_Precio'],
        'MPC' => ['tabla' => 'mpc_detalle_reemplazo', 'idField' => 'MPC_Id', 'itemField' => 'MPCD_Item', 'descField' => 'MPCD_Descripcion', 'precioField' => 'MPC_Precio'],
        'MPI' => ['tabla' => 'mpi_detalle_reemplazo', 'idField' => 'MPI_Id', 'itemField' => 'MPID_Item', 'descField' => 'MPID_Descripcion', 'precioField' => 'MPI_Precio'],
        'MAV' => ['tabla' => 'mav_detalle_reemplazo', 'idField' => 'MAV_Id', 'itemField' => 'MAVD_Item', 'descField' => 'MAVD_Descripcion', 'precioField' => 'MAV_Precio'],
    ];

    /**
     * Reconcilia las filas de origen BAHIA de un mantenimiento contra el estado actual
     * de la(s) cuenta(s) de bahia de su reserva: inserta lo nuevo, borra lo que ya no esta.
     * Las filas de origen MANUAL nunca se tocan aqui.
     */
    public static function sincronizar(string $tipo, int $mantenimientoId, ?int $resId): void
    {
        if (!$resId || !isset(self::CONFIG[$tipo])) {
            return;
        }

        $cfg = self::CONFIG[$tipo];

        $itemsBahia = DB::table('bahia_cuenta_item')
            ->join('bahia_cuenta', 'bahia_cuenta.BCT_Id', '=', 'bahia_cuenta_item.BCT_Id')
            ->join('producto', 'producto.PRO_Id', '=', 'bahia_cuenta_item.PRO_Id')
            ->where('bahia_cuenta.RES_Id', $resId)
            ->select(
                'bahia_cuenta_item.BCI_Id',
                'bahia_cuenta_item.BCI_Cantidad',
                'bahia_cuenta_item.BCI_PrecioUnitario',
                'producto.PRO_Nombre'
            )
            ->get()
            ->keyBy('BCI_Id');

        $filasActuales = DB::table($cfg['tabla'])
            ->where($cfg['idField'], $mantenimientoId)
            ->where('origen', 'BAHIA')
            ->get()
            ->keyBy('BCI_Id');

        // Borrar las que ya no estan en la cuenta de bahia.
        $idsABorrar = $filasActuales->keys()->diff($itemsBahia->keys());
        if ($idsABorrar->isNotEmpty()) {
            DB::table($cfg['tabla'])
                ->where($cfg['idField'], $mantenimientoId)
                ->where('origen', 'BAHIA')
                ->whereIn('BCI_Id', $idsABorrar)
                ->delete();
        }

        // Insertar/actualizar las vigentes.
        foreach ($itemsBahia as $bciId => $item) {
            $descripcion = number_format((float) $item->BCI_Cantidad, 2) . ' x ' . $item->PRO_Nombre;
            $precioTotal = round($item->BCI_Cantidad * $item->BCI_PrecioUnitario, 2);

            if ($filasActuales->has($bciId)) {
                DB::table($cfg['tabla'])
                    ->where($cfg['idField'], $mantenimientoId)
                    ->where('BCI_Id', $bciId)
                    ->where('origen', 'BAHIA')
                    ->update([
                        $cfg['descField'] => $descripcion,
                        $cfg['precioField'] => $precioTotal,
                    ]);
            } else {
                DB::table($cfg['tabla'])->insert([
                    $cfg['idField'] => $mantenimientoId,
                    $cfg['itemField'] => self::OFFSET_ITEM_BAHIA + $bciId,
                    $cfg['descField'] => $descripcion,
                    $cfg['precioField'] => $precioTotal,
                    'origen' => 'BAHIA',
                    'BCI_Id' => $bciId,
                ]);
            }
        }
    }

    /**
     * Reemplaza unicamente las filas de origen MANUAL de un mantenimiento con las
     * descripciones/precios enviados desde el formulario. Las filas BAHIA no se tocan.
     */
    public static function reemplazarManuales(string $tipo, int $mantenimientoId, array $descripciones, array $precios): void
    {
        if (!isset(self::CONFIG[$tipo])) {
            return;
        }

        $cfg = self::CONFIG[$tipo];

        DB::table($cfg['tabla'])
            ->where($cfg['idField'], $mantenimientoId)
            ->where('origen', 'MANUAL')
            ->delete();

        foreach ($descripciones as $i => $descripcion) {
            if ($descripcion === null || $descripcion === '') {
                continue;
            }
            DB::table($cfg['tabla'])->insert([
                $cfg['idField'] => $mantenimientoId,
                $cfg['itemField'] => $i + 1,
                $cfg['descField'] => $descripcion,
                $cfg['precioField'] => $precios[$i] ?? null,
                'origen' => 'MANUAL',
                'BCI_Id' => null,
            ]);
        }
    }
}
