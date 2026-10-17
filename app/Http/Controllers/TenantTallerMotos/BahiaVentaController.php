<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\BahiaCuenta;
use App\Models\TenantTallerMotos\BahiaCuentaAdelanto;
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

    public function index(Request $request)
    {
        $idAlmacen = $this->idAlmacenActivo();
        $hoy = Carbon::now('America/Lima')->toDateString();

        $fecha = $request->query('fecha');
        $fechaSeleccionada = ($fecha && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha))
            ? Carbon::createFromFormat('Y-m-d', $fecha, 'America/Lima')
            : Carbon::createFromFormat('Y-m-d', $hoy, 'America/Lima');

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
            ->whereDate('r.RES_FechaProgramada', $fechaSeleccionada->toDateString())
            ->select('r.*', 't.TUR_Descripcion', 't.TUR_Nombre')
            ->orderBy('t.TUR_Id')
            ->get()
            ->groupBy('BAH_Id');

        // Cuenta mas reciente de cada reserva de hoy (si ya se abrio alguna).
        $resIds = $reservas->flatten(1)->pluck('RES_Id');
        $cuentas = BahiaCuenta::with(['items.producto', 'adelantos'])
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
                    'adelantado' => $cuenta ? round((float) $cuenta->adelantos->sum('BCA_Monto'), 2) : 0,
                ];
            })->values();

            return [
                'bahia' => $bahia,
                'reservas' => $reservasBahia,
            ];
        });

        // Metodos reales para registrar adelantos ("Credito" y "Pago Mixto" son
        // etiquetas internas, no formas de pago).
        $metodosPago = DB::table('metodo_pago')
            ->whereNotIn('MEP_Pago', ['Credito', 'Pago Mixto'])
            ->orderBy('MEP_Id')
            ->get(['MEP_Id', 'MEP_Pago']);

        return view('tenant_tallermoto.ventas.bahias.index', [
            'tablero' => $tablero,
            'metodosPago' => $metodosPago,
            'fechaHoy' => $fechaSeleccionada->translatedFormat('l d \d\e F'),
            'fechaSeleccionada' => $fechaSeleccionada->toDateString(),
            'esHoy' => $fechaSeleccionada->toDateString() === $hoy,
            'fechaAnterior' => $fechaSeleccionada->copy()->subDay()->toDateString(),
            'fechaSiguiente' => $fechaSeleccionada->copy()->addDay()->toDateString(),
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
            'nombre_personalizado' => 'nullable|string|max:191',
        ]);

        BahiaCuentaItem::create([
            'BCT_Id' => $cuenta->BCT_Id,
            'PRO_Id' => $validated['pro_id'],
            'BCI_Cantidad' => $validated['cantidad'],
            'BCI_PrecioUnitario' => $validated['precio'],
            'USU_Id_Agrega' => Auth::id(),
            'BCI_NombrePersonalizado' => $validated['nombre_personalizado'] ?? null,
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
            'nombre_personalizado' => 'nullable|string|max:191',
        ]);

        $cambios = [
            'BCI_Cantidad' => $validated['cantidad'],
            'BCI_PrecioUnitario' => $validated['precio'],
        ];

        // Solo se toca el nombre si el request lo trae explicitamente (ej.
        // el input de nombre): los botones +/- de cantidad y el input de
        // precio no lo envian, y no deben borrar un nombre ya guardado.
        if ($request->has('nombre_personalizado')) {
            $cambios['BCI_NombrePersonalizado'] = $validated['nombre_personalizado'] ?: null;
        }

        BahiaCuentaItem::where('BCT_Id', $cuenta->BCT_Id)->where('BCI_Id', $itemId)->update($cambios);

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

        // Un adelanto ya entro a la caja: cerrar sin cobrar lo dejaria sin
        // venta que lo respalde. Hay que anularlo (devolverlo) primero.
        if ($cuenta->adelantos()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta cuenta tiene adelantos registrados; anúlalos (devuelve el dinero) antes de cerrar sin cobrar.',
            ], 422);
        }

        $cuenta->update([
            'BCT_Estado' => BahiaCuenta::ESTADO_CERRADA_SIN_COBRO,
            'BCT_CerradoEn' => now(),
        ]);

        // Libera el slot bahia+turno+dia de la reserva: la revision ya
        // termino (aunque no genero venta), asi que no hace falta seguir
        // bloqueando ese horario para una reserva nueva.
        Reservacion::liberarSlot($cuenta->RES_Id);

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

        $cuenta->load('items');
        if ($cuenta->totalAdelantado() > $cuenta->total() + 0.009) {
            return back()->with('error', 'Lo adelantado (S/ ' . number_format($cuenta->totalAdelantado(), 2) . ') supera el total de la cuenta (S/ ' . number_format($cuenta->total(), 2) . '). Agrega los items que faltan o anula el adelanto.');
        }

        return redirect()->route('tenant.ventas.venta.create', ['cuenta_bahia' => $cuenta->BCT_Id]);
    }

    /**
     * Registra un pago adelantado sobre la cuenta (el cliente paga una parte
     * antes de que termine el trabajo). Entra a la caja del turno abierto
     * ahora, con la fecha y hora de este momento; al cobrar la cuenta, el
     * punto de venta solo pide el saldo.
     */
    public function registrarAdelanto(Request $request, $cuentaId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);

        if (tenant_requiere_apertura_caja()) {
            return response()->json(['success' => false, 'message' => 'No hay ninguna caja aperturada. Abre una caja antes de registrar el adelanto.'], 422);
        }

        $datos = $request->validate([
            'monto' => 'required|numeric|min:0.01|max:999999',
            'metodo_pago' => 'required|integer|exists:metodo_pago,MEP_Id',
            'observacion' => 'nullable|string|max:255',
        ]);

        $metodoValido = DB::table('metodo_pago')
            ->where('MEP_Id', $datos['metodo_pago'])
            ->whereNotIn('MEP_Pago', ['Credito', 'Pago Mixto'])
            ->exists();

        if (!$metodoValido) {
            return response()->json(['success' => false, 'message' => 'Elige un método de pago real (efectivo, Yape, tarjeta…).'], 422);
        }

        BahiaCuentaAdelanto::create([
            'BCT_Id' => $cuenta->BCT_Id,
            'MEP_Id' => $datos['metodo_pago'],
            'USU_Id' => Auth::id(),
            'CAJ_Id' => tenant_caja_activa_id(),
            'CS_Id' => tenant_caja_sesion_activa_id(),
            'BCA_Monto' => round((float) $datos['monto'], 2),
            'BCA_Observacion' => $datos['observacion'] ?? null,
        ]);

        return response()->json($this->resumenJson($cuenta->fresh()));
    }

    /**
     * Anula un adelanto (se devuelve el dinero). Solo dentro del mismo turno
     * de caja en que se registro: si ese turno ya se cerro, su arqueo ya lo
     * conto y anularlo lo descuadraria.
     */
    public function anularAdelanto(Request $request, $cuentaId, $adelantoId)
    {
        $cuenta = $this->cuentaAbiertaOFallar($cuentaId);
        $adelanto = BahiaCuentaAdelanto::where('BCT_Id', $cuenta->BCT_Id)->findOrFail($adelantoId);

        if ((string) $adelanto->CS_Id !== (string) tenant_caja_sesion_activa_id()) {
            return response()->json(['success' => false, 'message' => 'Este adelanto se registró en otro turno de caja (ya cerrado o de otra caja); no se puede anular desde aquí.'], 422);
        }

        $adelanto->delete();

        return response()->json($this->resumenJson($cuenta->fresh()));
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

        $adelantos = DB::table('bahia_cuenta_adelanto as a')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'a.MEP_Id')
            ->where('a.BCT_Id', $cuenta->BCT_Id)
            ->orderBy('a.BCA_Id')
            ->get(['a.BCA_Id', 'a.BCA_Monto', 'a.BCA_Observacion', 'a.created_at', 'mp.MEP_Pago']);
        $totalAdelantado = round((float) $adelantos->sum('BCA_Monto'), 2);

        $productos = DB::table('producto')
            ->whereIn('PRO_Id', $cuenta->items->pluck('PRO_Id'))
            ->select('PRO_Id', 'PRO_Nombre', 'PRO_Imagen')
            ->get()
            ->keyBy('PRO_Id');

        return [
            'cuenta_id' => $cuenta->BCT_Id,
            'estado' => $cuenta->BCT_Estado,
            'total' => $cuenta->total(),
            'adelantado' => $totalAdelantado,
            'saldo' => round(max(0, $cuenta->total() - $totalAdelantado), 2),
            'adelantos' => $adelantos->map(fn ($a) => [
                'id' => $a->BCA_Id,
                'monto' => round((float) $a->BCA_Monto, 2),
                'metodo' => $a->MEP_Pago,
                'observacion' => $a->BCA_Observacion,
                'fecha' => Carbon::parse($a->created_at)->format('d/m/Y H:i'),
            ])->values(),
            'items' => $cuenta->items->map(function ($item) use ($productos) {
                $producto = $productos[$item->PRO_Id] ?? null;
                $nombreReal = $producto->PRO_Nombre ?? ('Producto #' . $item->PRO_Id);

                return [
                    'id' => $item->BCI_Id,
                    'pro_id' => $item->PRO_Id,
                    'nombre' => $item->BCI_NombrePersonalizado ?: $nombreReal,
                    'nombre_real' => $nombreReal,
                    'nombre_personalizado' => $item->BCI_NombrePersonalizado,
                    'imagen' => $producto && $producto->PRO_Imagen
                        ? '/storage/' . tenant('tipo_negocio') . '/' . tenant('id') . '/archivos/producto/' . $producto->PRO_Imagen
                        : '/images/imagen_default.png',
                    'cantidad' => $item->BCI_Cantidad,
                    'precio' => $item->BCI_PrecioUnitario,
                    'subtotal' => $item->subtotal(),
                ];
            })->values(),
        ];
    }
}
