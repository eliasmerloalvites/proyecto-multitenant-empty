<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\TenantTallerMotos\Bahia;
use App\Models\TenantTallerMotos\Horario;
use App\Models\TenantTallerMotos\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Primera vez que un taller entra al sistema, Bahías/Turnos/Horarios están
 * vacíos y no hay forma de agendar nada hasta que alguien los llene a mano,
 * uno por uno, en 3 pantallas distintas. Este asistente reemplaza eso con
 * 3 preguntas simples (¿cuántas bahías?, ¿cuántos turnos?, ¿qué días
 * trabajas?) y genera todo con nombres/horarios por defecto razonables —
 * el dueño los renombra después desde Configuración si quiere.
 */
class AsistenteConfiguracionController extends Controller
{
    private const DIAS_VALIDOS = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];

    /**
     * Plantillas de nombre + rango horario según cuántos turnos se pidan.
     * Para 5+ turnos no hay plantilla "bonita" con nombre propio, se
     * numeran genéricos.
     */
    private const PLANTILLAS_TURNO = [
        1 => [['Turno Único', '08:00 - 18:00']],
        2 => [['Turno Mañana', '08:00 - 13:00'], ['Turno Tarde', '14:00 - 19:00']],
        3 => [['Turno Mañana', '08:00 - 13:00'], ['Turno Tarde', '14:00 - 19:00'], ['Turno Noche', '19:00 - 22:00']],
        4 => [['Turno Mañana', '08:00 - 11:00'], ['Turno Medio Día', '11:00 - 14:00'], ['Turno Tarde', '14:00 - 17:00'], ['Turno Noche', '17:00 - 20:00']],
    ];

    public function index()
    {
        $bahias = Bahia::count();
        $turnos = Turno::count();
        $horarios = Horario::count();

        return view('tenant_tallermoto.configuracion.asistente.index', [
            'yaConfigurado' => $bahias > 0 || $turnos > 0 || $horarios > 0,
            'bahias' => $bahias,
            'turnos' => $turnos,
            'horarios' => $horarios,
        ]);
    }

    public function generar(Request $request)
    {
        $validated = $request->validate([
            'num_bahias' => 'required|integer|min:1|max:20',
            'num_turnos' => 'required|integer|min:1|max:6',
            'dias' => 'required|array|min:1',
            'dias.*' => 'in:' . implode(',', self::DIAS_VALIDOS),
        ], [
            'num_bahias.required' => 'Indica cuántas bahías tienes.',
            'num_turnos.required' => 'Indica cuántos turnos manejas.',
            'dias.required' => 'Selecciona al menos un día de trabajo.',
        ]);

        // Este asistente es solo para la primera configuración. Si ya hay
        // algo cargado, que lo edite desde Configuración en vez de arriesgar
        // duplicados (BAH_Nombre/TUR_Nombre son únicos).
        if (Bahia::exists() || Turno::exists() || Horario::exists()) {
            return response()->json([
                'error' => 'Ya tienes bahías, turnos u horarios configurados. Edítalos desde Configuración en vez de generar de nuevo.'
            ], 422);
        }

        $almacen = Almacen::orderBy('ALM_Id')->first();
        if (! $almacen) {
            return response()->json(['error' => 'Tu taller todavía no tiene ninguna sede/almacén registrado.'], 422);
        }

        $usuarioId = Auth::guard('tenant')->id();

        DB::transaction(function () use ($validated, $almacen, $usuarioId) {
            for ($i = 1; $i <= $validated['num_bahias']; $i++) {
                Bahia::create([
                    'BAH_Nombre' => 'Bahía ' . $i,
                    'ALM_Id' => $almacen->ALM_Id,
                    'USU_Id' => $usuarioId,
                    'BAH_Estado' => 'ACT',
                ]);
            }

            $plantillas = self::PLANTILLAS_TURNO[$validated['num_turnos']]
                ?? collect(range(1, $validated['num_turnos']))
                    ->map(fn ($n) => ['Turno ' . $n, '08:00 - 18:00'])
                    ->toArray();

            $turnos = [];
            foreach ($plantillas as [$nombre, $descripcion]) {
                $turnos[] = Turno::create([
                    'TUR_Nombre' => $nombre,
                    'TUR_Descripcion' => $descripcion,
                    'TUR_Estado' => 'ACT',
                ]);
            }

            foreach ($validated['dias'] as $dia) {
                foreach ($turnos as $turno) {
                    Horario::create([
                        'HOR_Dia' => $dia,
                        'HOR_Detalle' => $turno->TUR_Descripcion,
                        'ALM_Id' => $almacen->ALM_Id,
                        'TUR_Id' => $turno->TUR_Id,
                        'HOR_Estado' => 'ACT',
                    ]);
                }
            }
        });

        return response()->json([
            'success' => '¡Listo! Se generaron ' . $validated['num_bahias'] . ' bahía(s), ' . $validated['num_turnos'] . ' turno(s) y sus horarios para ' . count($validated['dias']) . ' día(s). Puedes renombrarlos cuando quieras desde Configuración.',
        ]);
    }
}
