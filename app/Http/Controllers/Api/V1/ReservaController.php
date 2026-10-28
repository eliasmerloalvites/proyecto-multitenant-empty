<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Models\TenantTallerMotos\Reservacion;
use App\Services\AppMovil\ReservasMotoService as Svc;
use App\Support\ApiRespuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Reservas de la cuenta. Una reserva ajena responde 404 igual que una inexistente. */
class ReservaController extends Controller
{
    public function __construct(private Svc $reservas)
    {
    }

    public function sedes()
    {
        return ApiRespuesta::ok($this->reservas->sedes()->all());
    }

    /** Marcas activas para elegir (opcional) al reservar. */
    public function marcas()
    {
        return ApiRespuesta::ok(\App\Services\TenantTallerMotos\MarcaVehiculoService::activas()
            ->map(fn ($m) => ['id' => (int) $m->MAR_Id, 'nombre' => $m->MAR_Nombre])->values()->all());
    }

    public function disponibilidad(Request $request)
    {
        $d = $request->validate(['sede' => 'required|integer|min:1', 'fecha' => 'required|date_format:Y-m-d']);

        if (! $this->reservas->fechaValida($d['fecha'])) {
            return ApiRespuesta::error('FECHA_INVALIDA', 'Elige una fecha desde hoy y hasta dentro de 60 días.', 422);
        }

        return ApiRespuesta::ok($this->reservas->disponibilidad($d['sede'], $d['fecha'])->all());
    }

    public function index(Request $request)
    {
        $d = $request->validate(['estado' => 'nullable|in:proximas,todas']);
        $lista = $this->reservas->deLaCuenta($request->user('cliente'), ($d['estado'] ?? 'proximas') === 'proximas');

        return ApiRespuesta::ok($lista->map(fn ($r) => $this->presentar($r))->all());
    }

    public function show(Request $request, int $id)
    {
        $r = $this->reservas->localizar($request->user('cliente'), $id);

        return $r ? ApiRespuesta::ok($this->presentar($r)) : $this->noEncontrada();
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'moto_id' => 'required|integer|min:1',
            'sede' => 'required|integer|min:1|exists:almacen,ALM_Id',
            'turno' => 'required|integer|min:1',
            'fecha' => 'required|date_format:Y-m-d',
            'detalle' => 'nullable|string|max:250',
            // Opcional: null = "no esta en la lista". Si una app vieja no lo
            // envia, se usa la ultima marca registrada para esa placa.
            'marca' => 'nullable|integer|min:1',
        ]);

        $cuenta = $request->user('cliente');

        $vinculo = CuentaMovilMoto::with('moto')->where('CMV_Id', $cuenta->CMV_Id)->where('MOT_Id', $d['moto_id'])
            ->where('CMM_Estado', CuentaMovilMoto::VERIFICADA)->first();

        if (! $vinculo) {
            return ApiRespuesta::error('NO_ENCONTRADO', 'No encontramos esa moto en tu garaje.', 404);
        }

        if (! $this->reservas->fechaValida($d['fecha'])) {
            return ApiRespuesta::error('FECHA_INVALIDA', 'Elige una fecha desde hoy y hasta dentro de 60 días.', 422);
        }

        $marcaId = $request->exists('marca')
            ? ($d['marca'] ?? null)
            : Reservacion::where('RES_Placa', $vinculo->moto->MOT_Placa)->whereNotNull('MAR_Id')->orderByDesc('RES_Id')->value('MAR_Id');

        [$resultado, $reserva] = $this->reservas->crear($cuenta, $vinculo->moto, $d['sede'], $d['turno'], $d['fecha'], $d['detalle'] ?? null, $marcaId ? (int) $marcaId : null);

        return match ($resultado) {
            Svc::CREADA => ApiRespuesta::ok($this->presentar($reserva), 201),
            Svc::SIN_CUPO => ApiRespuesta::error('SIN_CUPO', 'Ese horario ya no tiene cupo. Elige otro turno u otro día.', 409),
            Svc::LIMITE => ApiRespuesta::error('LIMITE_RESERVAS', 'Ya tienes varias reservas pendientes. Espera a que el taller las atienda o cancela alguna.', 429),
            default => ApiRespuesta::error('HORARIO_NO_DISPONIBLE', 'Ese horario no está disponible para reservar.', 422),
        };
    }

    public function cancelar(Request $request, int $id)
    {
        $r = $this->reservas->localizar($request->user('cliente'), $id);

        if (! $r) {
            return $this->noEncontrada();
        }

        if (! $this->reservas->cancelar($r)) {
            return ApiRespuesta::error('NO_CANCELABLE', 'Esta reserva ya no se puede cancelar. Comunícate con el taller.', 409);
        }

        return ApiRespuesta::ok($this->presentar($r->fresh()));
    }

    private function presentar(Reservacion $r): array
    {
        $turno = DB::table('turno')->where('TUR_Id', $r->TUR_Id)->value('TUR_Nombre');
        $hora = DB::table('horario')->where('ALM_Id', $r->ALM_Id)->where('TUR_Id', $r->TUR_Id)->value('HOR_Detalle');
        $sede = DB::table('almacen')->where('ALM_Id', $r->ALM_Id)->value('ALM_NombreAlmacen');

        return [
            'id' => $r->RES_Id,
            'estado' => Svc::estadoPublico($r),
            'fecha' => $r->RES_FechaProgramada instanceof \DateTimeInterface ? $r->RES_FechaProgramada->format('Y-m-d') : substr((string) $r->RES_FechaProgramada, 0, 10),
            'turno' => $turno,
            'hora' => $hora,
            'sede' => $sede,
            'placa' => $r->RES_Placa,
            'moto' => $r->RES_Moto,
            'marca' => \App\Services\TenantTallerMotos\MarcaVehiculoService::nombre($r->MAR_Id),
            'detalle' => $r->RES_Detalle,
            'cancelable' => $r->RES_State !== 'RECHAZADO' && $r->RES_CheckIn === null && $r->RES_Estado === 'ACT',
        ];
    }

    private function noEncontrada()
    {
        return ApiRespuesta::error('NO_ENCONTRADO', 'No encontramos esa reserva.', 404);
    }
}
