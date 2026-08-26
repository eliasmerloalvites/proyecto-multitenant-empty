<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\TenantTallerMotos\Reservacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Recordatorio de reservas del dia siguiente por WhatsApp.
 *
 * El envio sigue siendo manual (como el resto del sistema: se genera un
 * link wa.me con el mensaje ya armado y el usuario le da clic), pero la
 * hora configurada aqui determina desde que momento del dia el panel avisa
 * que ya hay que notificar. No hay integracion con ninguna API de WhatsApp,
 * asi que no existe un envio automatico real sin que alguien de clic.
 */
class NotificacionReservaController extends Controller
{
    public function index()
    {
        $empresa = EmpresaFacturacion::delTenantActual();
        $manana = Carbon::now('America/Lima')->addDay()->toDateString();

        $reservas = DB::table('reservacion as r')
            ->join('almacen as a', 'a.ALM_Id', '=', 'r.ALM_Id')
            ->join('turno as t', 't.TUR_Id', '=', 'r.TUR_Id')
            ->where('r.RES_Estado', 'ACT')
            ->where('r.RES_State', '!=', 'RECHAZADO')
            ->whereDate('r.RES_FechaProgramada', $manana)
            ->select(
                'r.RES_Id',
                'r.RES_Cliente',
                'r.RES_Celular',
                'r.RES_Moto',
                'r.RES_Placa',
                'r.RES_FechaProgramada',
                'r.RES_Notificado',
                'r.RES_NotificadoEn',
                'a.ALM_NombreAlmacen',
                't.TUR_Descripcion'
            )
            ->orderBy('r.RES_Notificado')
            ->orderBy('t.TUR_Descripcion')
            ->get();

        $mensajePlantilla = $empresa ? $empresa->mensajeNotificacionReserva() : EmpresaFacturacion::MENSAJE_RESERVA_DEFECTO;
        $nombreEmpresa = $empresa ? ($empresa->nombre_comercial ?: $empresa->razon_social) : '';

        $fechaFormateada = Carbon::parse($manana)->translatedFormat('d/m/Y');

        $reservas = $reservas->map(function ($r) use ($mensajePlantilla, $nombreEmpresa, $fechaFormateada) {
            $r->mensaje = strtr($mensajePlantilla, [
                '{cliente}' => $r->RES_Cliente,
                '{moto}' => $r->RES_Moto,
                '{placa}' => $r->RES_Placa,
                '{fecha}' => $fechaFormateada,
                '{turno}' => $r->TUR_Descripcion,
                '{sede}' => $r->ALM_NombreAlmacen,
                '{empresa}' => $nombreEmpresa,
            ]);

            return $r;
        });

        // El aviso en pantalla solo tiene sentido a partir de la hora
        // configurada; antes de esa hora se puede seguir usando la lista
        // igual (no se bloquea), solo no se resalta como "hora de avisar".
        $horaConfigurada = $empresa?->reserva_notif_hora;
        $yaEsHora = $horaConfigurada
            ? Carbon::now('America/Lima')->format('H:i') >= substr($horaConfigurada, 0, 5)
            : true;

        return view('tenant_tallermoto.reservaciones.notificaciones.index', [
            'reservas' => $reservas,
            'empresa' => $empresa,
            'fechaManana' => $fechaFormateada,
            'yaEsHora' => $yaEsHora,
            'pendientes' => $reservas->where('RES_Notificado', false)->count(),
        ]);
    }

    public function guardarConfiguracion(Request $request)
    {
        $request->validate([
            'reserva_notif_activo' => 'nullable|boolean',
            'reserva_notif_hora' => 'nullable|date_format:H:i',
            'reserva_notif_mensaje' => 'nullable|string|max:500',
        ]);

        $empresa = EmpresaFacturacion::delTenantActual();

        if (!$empresa) {
            return back()->with('error', 'Configura primero los datos de tu empresa antes de activar el recordatorio.');
        }

        $empresa->update([
            'reserva_notif_activo' => $request->boolean('reserva_notif_activo'),
            'reserva_notif_hora' => $request->input('reserva_notif_hora'),
            'reserva_notif_mensaje' => $request->input('reserva_notif_mensaje'),
        ]);

        return back()->with('success', 'Configuracion de recordatorio guardada.');
    }

    public function marcarNotificado(Request $request, $id)
    {
        $reserva = Reservacion::findOrFail($id);
        $reserva->RES_Notificado = $request->boolean('notificado', true);
        $reserva->RES_NotificadoEn = $reserva->RES_Notificado ? now() : null;
        $reserva->save();

        return response()->json(['success' => true]);
    }
}
