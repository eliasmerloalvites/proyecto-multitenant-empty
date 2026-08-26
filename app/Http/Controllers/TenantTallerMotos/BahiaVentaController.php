<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\BahiaCuenta;
use App\Models\TenantTallerMotos\BahiaCuentaItem;
use App\Models\TenantTallerMotos\Reservacion;
use App\Services\Ventas\ItemRapidoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Tablero "Ventas por Bahia": muestra las bahias de la sede activa con las
 * reservas aprobadas del dia, permite abrirles una cuenta e ir cargando
 * productos/servicios mientras se atiende la moto (sin tocar stock todavia),
 * y cerrar esa cuenta cobrandola (genera la Venta real, vía el mismo flujo
 * del punto de venta) o sin cobrar (revision que no genero venta).
 */
class BahiaVentaController extends Controller
{
    protected ItemRapidoService $itemRapidoService;

    public function __construct(ItemRapidoService $itemRapidoService)
    {
        $this->itemRapidoService = $itemRapidoService;
    }

    private function idAlmacenActivo(): int
    {
        return tenant_caja_activa_almacen_id() ?? 1;
    }

    public function index()
    {
        $idAlmacen = $this->idAlmacenActivo();
        $hoy = Carbon::now('America/Lima')->toDateString();

        $bahias = DB::table('bahia')
            ->where('ALM_Id', $idAlmacen)
            ->where('BAH_Estado', 'ACT')
            ->orderBy('BAH_Nombre')
            ->get();

        $reservas = DB::table('reservacion as r')
            ->join('turno as t', 't.TUR_Id', '=', 'r.TUR_Id')
            ->where('r.ALM_Id', $idAlmacen)
            ->where('r.RES_Estado', 'ACT')
            ->where('r.RES_State', '!=', 'RECHAZADO')
            ->whereDate('r.RES_FechaProgramada', $hoy)
            ->select('r.*', 't.TUR_Descripcion', 't.TUR_Nombre')
            ->orderBy('t.TUR_Id')
            ->get()
            ->groupBy('BAH_Id');

        // Cuenta mas reciente de cada reserva de hoy (si ya se abrio alguna).
        $resIds = $reservas->flatten(1)->pluck('RES_Id');
        $cuentas = BahiaCuenta::with('items.producto')
            ->whereIn('RES_Id', $resIds)
            ->orderByDesc('BCT_Id')
            ->get()
            ->unique('RES_Id')
            ->keyBy('RES_Id');

        $tablero = $bahias->map(function ($bahia) use ($reservas, $cuentas) {
            $reservasBahia = ($reservas[$bahia->BAH_Id] ?? collect())->map(function ($reserva) use ($cuentas) {
                $cuenta = $cuentas->get($reserva->RES_Id);

                return [
                    'reserva' => $reserva,
                    'cuenta' => $cuenta,
                    'total' => $cuenta ? $cuenta->total() : 0,
                    'items' => $cuenta ? $cuenta->items->count() : 0,
                ];
            })->values();

            return [
                'bahia' => $bahia,
                'reservas' => $reservasBahia,
            ];
        });

        return view('tenant_tallermoto.ventas.bahias.index', [
            'tablero' => $tablero,
            'fechaHoy' => Carbon::now('America/Lima')->translatedFormat('l d \d\e F'),
        ]);
    }

    /**
     * Abre (o recupera, si ya existe una abierta) la cuenta de una reserva.
     */
    public function abrir(Request $request, $reservacionId)
    {
        $reserva = Reservacion::findOrFail($reservacionId);

        $cuenta = BahiaCuenta::where('RES_Id', $reserva->RES_Id)
            ->where('BCT_Estado', BahiaCuenta::ESTADO_ABIERTA)
            ->first();

        if (!$cuenta) {
            $cuenta = BahiaCuenta::create([
                'RES_Id' => $reserva->RES_Id,
                'ALM_Id' => $reserva->ALM_Id,
                'BAH_Id' => $reserva->BAH_Id,
                'BCT_Estado' => BahiaCuenta::ESTADO_ABIERTA,
                'USU_Id_Abre' => Auth::id(),
                'BCT_AbiertoEn' => now(),
            ]);
        }

        return response()->json($this->resumenJson($cuenta));
    }

    /**
     * Agrega un producto del catalogo (por PRO_Id) a la cuenta.
     */
    public function agregarItem(Request $request, $cuentaId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);

        $validated = $request->validate([
            'pro_id' => 'required|integer|exists:producto,PRO_Id',
            'cantidad' => 'required|numeric|min:0.01',
            'precio' => 'required|numeric|min:0',
        ]);

        BahiaCuentaItem::create([
            'BCT_Id' => $cuenta->BCT_Id,
            'PRO_Id' => $validated['pro_id'],
            'BCI_Cantidad' => $validated['cantidad'],
            'BCI_PrecioUnitario' => $validated['precio'],
            'USU_Id_Agrega' => Auth::id(),
        ]);

        return response()->json($this->resumenJson($cuenta->fresh()));
    }

    /**
     * Item "al vuelo" (mismo mecanismo que "Item rapido" del punto de
     * venta): crea un producto minimo y lo agrega de una vez a la cuenta.
     */
    public function agregarItemRapido(Request $request, $cuentaId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'cantidad' => 'required|numeric|min:0.01',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto = $this->itemRapidoService->crear(
            $validated['nombre'],
            (float) $validated['cantidad'],
            (float) $validated['precio'],
            $cuenta->ALM_Id
        );

        BahiaCuentaItem::create([
            'BCT_Id' => $cuenta->BCT_Id,
            'PRO_Id' => $producto['PRO_Id'],
            'BCI_Cantidad' => $validated['cantidad'],
            'BCI_PrecioUnitario' => $validated['precio'],
            'USU_Id_Agrega' => Auth::id(),
        ]);

        return response()->json($this->resumenJson($cuenta->fresh()));
    }

    /**
     * Corrige la cantidad y/o el precio de un item ya cargado (por ejemplo,
     * se puso una cantidad al ojo mientras se atendia y se ajusta despues).
     */
    public function actualizarItem(Request $request, $cuentaId, $itemId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);

        $validated = $request->validate([
            'cantidad' => 'required|numeric|min:0.01',
            'precio' => 'required|numeric|min:0',
        ]);

        BahiaCuentaItem::where('BCT_Id', $cuenta->BCT_Id)->where('BCI_Id', $itemId)->update([
            'BCI_Cantidad' => $validated['cantidad'],
            'BCI_PrecioUnitario' => $validated['precio'],
        ]);

        return response()->json($this->resumenJson($cuenta->fresh()));
    }

    public function quitarItem(Request $request, $cuentaId, $itemId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);

        BahiaCuentaItem::where('BCT_Id', $cuenta->BCT_Id)->where('BCI_Id', $itemId)->delete();

        return response()->json($this->resumenJson($cuenta->fresh()));
    }

    /**
     * Cierra la cuenta sin generar venta (por ejemplo, una revision que no
     * termino en un cobro). Solo permitido si no se le cargo nada: si hay
     * items cargados, hay que cobrarlos o quitarlos primero.
     */
    public function cerrarSinCobrar(Request $request, $cuentaId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);

        if ($cuenta->items()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Esta cuenta tiene items cargados; cóbralos o quítalos antes de cerrar sin cobrar.',
            ], 422);
        }

        $cuenta->update([
            'BCT_Estado' => BahiaCuenta::ESTADO_CERRADA_SIN_COBRO,
            'BCT_CerradoEn' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Lleva al punto de venta ya existente con el carrito precargado con
     * los items de esta cuenta y el cliente/moto de la reserva ya
     * identificados. El cobro en si (comprobante, metodo de pago, stock)
     * lo sigue haciendo VentaController::store(), sin duplicar esa logica.
     */
    public function cobrar($cuentaId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);

        if ($cuenta->items()->count() === 0) {
            return back()->with('error', 'Esta cuenta no tiene items cargados. Agrega algo antes de cobrar, o ciérrala sin cobrar.');
        }

        return redirect()->route('tenant.ventas.venta.create', ['cuenta_bahia' => $cuenta->BCT_Id]);
    }

    /**
     * Resumen en vivo de la cuenta (para refrescar la tarjeta del tablero).
     */
    public function resumen($cuentaId)
    {
        $cuenta = BahiaCuenta::with('items')->findOrFail($cuentaId);

        return response()->json($this->resumenJson($cuenta));
    }

    private function cuentaAbiertaOFallar($cuentaId): BahiaCuenta
    {
        $cuenta = BahiaCuenta::findOrFail($cuentaId);

        if (!$cuenta->estaAbierta()) {
            throw new RuntimeException('Esta cuenta ya esta cerrada.');
        }

        return $cuenta;
    }

    private function resumenJson(BahiaCuenta $cuenta): array
    {
        $cuenta->loadMissing(['items' => function ($q) {
            $q->orderBy('BCI_Id');
        }]);

        $productos = DB::table('producto')
            ->whereIn('PRO_Id', $cuenta->items->pluck('PRO_Id'))
            ->pluck('PRO_Nombre', 'PRO_Id');

        return [
            'cuenta_id' => $cuenta->BCT_Id,
            'estado' => $cuenta->BCT_Estado,
            'total' => $cuenta->total(),
            'items' => $cuenta->items->map(function ($item) use ($productos) {
                return [
                    'id' => $item->BCI_Id,
                    'nombre' => $productos[$item->PRO_Id] ?? ('Producto #' . $item->PRO_Id),
                    'cantidad' => $item->BCI_Cantidad,
                    'precio' => $item->BCI_PrecioUnitario,
                    'subtotal' => $item->subtotal(),
                ];
            })->values(),
        ];
    }
}
