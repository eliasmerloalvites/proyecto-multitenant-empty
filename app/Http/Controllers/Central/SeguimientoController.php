<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\SeguimientoCliente;
use App\Models\SeguimientoEvento;
use App\Services\Seguimiento\SeguimientoPlantillas;
use App\Services\Seguimiento\SeguimientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SeguimientoController extends Controller
{
    public function __construct(private SeguimientoService $seguimiento)
    {
    }

    private function usuario(): ?object
    {
        return Auth::guard('central')->user();
    }

    public function index()
    {
        return view('central.admin.seguimiento.index', [
            'etapas' => SeguimientoCliente::ETAPAS,
            'plantillas' => collect(SeguimientoPlantillas::PLANTILLAS)->map(fn ($p, $k) => ['clave' => $k, 'nombre' => $p['nombre']])->values(),
            'automaticoActivo' => (bool) config('saas.seguimiento.automatico'),
        ]);
    }

    /** Aplica los filtros de la pantalla sobre la consulta base. */
    private function filtrar($q, Request $request)
    {
        $hoy = SeguimientoService::hoy()->toDateString();
        $sinPagos = 'NOT EXISTS (SELECT 1 FROM pagos WHERE pagos.client_id = clients.id)';

        switch ($request->input('segmento', 'pruebas')) {
            case 'en_prueba':
                $q->whereRaw($sinPagos)->whereNotNull('clients.trial_ends_at')->whereDate('clients.trial_ends_at', '>', $hoy);
                break;
            case 'vencida':
                $q->whereRaw($sinPagos)->whereNotNull('clients.trial_ends_at')->whereDate('clients.trial_ends_at', '<=', $hoy);
                break;
            case 'convertido':
                $q->whereRaw('EXISTS (SELECT 1 FROM pagos WHERE pagos.client_id = clients.id)');
                break;
            case 'todos':
                break;
            default: // pruebas: toda prueba que aun no pago (vigente o vencida)
                $q->whereRaw($sinPagos)->whereNotNull('clients.trial_ends_at');
        }

        if ($etapa = $request->input('etapa')) {
            $etapa === 'nuevo'
                ? $q->where(fn ($w) => $w->whereNull('s.etapa')->orWhere('s.etapa', 'nuevo'))
                : $q->where('s.etapa', $etapa);
        }

        if ($uso = $request->input('uso')) {
            $uso === 'sin_medir' ? $q->whereNull('s.uso_nivel') : $q->where('s.uso_nivel', $uso);
        }

        if ($request->boolean('para_hoy')) {
            $q->whereDate('s.proxima_accion_at', '<=', $hoy)
                ->where(fn ($w) => $w->whereNull('s.etapa')->orWhereNotIn('s.etapa', ['convertido', 'perdido']));
        }

        return $q;
    }

    public function datos(Request $request)
    {
        $q = $this->filtrar($this->seguimiento->baseQuery(), $request);
        $hoy = SeguimientoService::hoy();

        return DataTables::of($q->orderByDesc('clients.created_at')->get())
            ->addColumn('seleccion', fn ($r) => '<input type="checkbox" class="chk-cliente" value="' . $r->id . '">')
            ->addColumn('negocio', function ($r) {
                $tel = $r->telefono ? e($r->telefono) : '<span class="text-muted">sin teléfono</span>';
                $mail = $r->email ? e($r->email) : '<span class="text-muted">sin correo</span>';

                return '<div class="font-weight-bold">' . e($r->razon_social) . '</div>'
                    . '<small class="text-muted"><i class="far fa-envelope"></i> ' . $mail . '<br><i class="fab fa-whatsapp"></i> ' . $tel . '</small>';
            })
            ->addColumn('prueba', function ($r) use ($hoy) {
                if ((int) $r->pagos_count > 0) {
                    return '<span class="badge badge-success">Pagó</span>';
                }
                if (! $r->trial_ends_at) {
                    return '<span class="badge badge-secondary">Sin prueba</span>';
                }
                $fin = \Illuminate\Support\Carbon::parse($r->trial_ends_at)->startOfDay();
                $dias = (int) $hoy->diffInDays($fin, false);
                $fecha = $fin->format('d/m/Y');

                if ($dias > 0) {
                    $cls = $dias <= 2 ? 'warning' : 'info';

                    return '<span class="badge badge-' . $cls . '">Quedan ' . $dias . ' d</span><br><small class="text-muted">hasta ' . $fecha . '</small>';
                }

                return '<span class="badge badge-danger">Venció hace ' . abs($dias) . ' d</span><br><small class="text-muted">' . $fecha . '</small>';
            })
            ->addColumn('uso', function ($r) {
                $nivel = $r->s_uso_nivel;
                $map = ['sin_uso' => ['danger', 'Sin uso'], 'poco_uso' => ['warning', 'Poco uso'], 'activo' => ['success', 'Activo']];

                if (! $nivel) {
                    return '<span class="badge badge-light border">Sin medir</span>';
                }

                [$cls, $txt] = $map[$nivel];
                $uso = $r->s_uso_datos ? json_decode($r->s_uso_datos, true) : [];
                $resumen = $uso['resumen'] ?? '';
                $ultima = $r->s_ultima_actividad_at
                    ? 'últ. actividad ' . \Illuminate\Support\Carbon::parse($r->s_ultima_actividad_at)->setTimezone('America/Lima')->diffForHumans()
                    : 'sin actividad';

                return '<span class="badge badge-' . $cls . '">' . $txt . '</span><br><small class="text-muted" title="' . e($resumen) . '">' . e($ultima) . '</small>';
            })
            ->addColumn('etapa', function ($r) {
                $etapa = $r->s_etapa ?: ((int) $r->pagos_count > 0 ? 'convertido' : 'nuevo');
                $colores = ['nuevo' => 'secondary', 'contactado' => 'primary', 'demo' => 'info', 'interesado' => 'warning', 'convertido' => 'success', 'perdido' => 'dark'];

                $html = '<span class="badge badge-' . ($colores[$etapa] ?? 'secondary') . '">' . e(SeguimientoCliente::ETAPAS[$etapa] ?? $etapa) . '</span>';

                if ($r->s_no_contactar) {
                    $html .= '<br><span class="badge badge-danger" title="Pidió no recibir mensajes">No contactar</span>';
                }

                return $html;
            })
            ->addColumn('contacto', function ($r) use ($hoy) {
                $ultimo = $r->s_ultimo_contacto_at
                    ? 'Último: ' . \Illuminate\Support\Carbon::parse($r->s_ultimo_contacto_at)->setTimezone('America/Lima')->diffForHumans()
                    : 'Sin contactar';

                $prox = '';
                if ($r->s_proxima_accion_at) {
                    $f = \Illuminate\Support\Carbon::parse($r->s_proxima_accion_at)->startOfDay();
                    $vencida = $f->lte($hoy);
                    $prox = '<br><small class="' . ($vencida ? 'text-danger font-weight-bold' : 'text-muted') . '"><i class="far fa-calendar-check"></i> '
                        . $f->format('d/m') . ($r->s_proxima_accion_nota ? ' · ' . e($r->s_proxima_accion_nota) : '') . '</small>';
                }

                return '<small class="text-muted">' . e($ultimo) . '</small>' . $prox;
            })
            ->addColumn('acciones', function ($r) {
                $wa = SeguimientoService::telefonoWhatsapp($r->telefono);

                return '<div class="btn-group btn-group-sm">'
                    . '<button type="button" class="btn btn-primary btn-sm abrirFicha" data-id="' . $r->id . '" title="Ver seguimiento"><i class="fas fa-clipboard-list"></i></button>'
                    . ($wa ? '<button type="button" class="btn btn-success btn-sm abrirFicha" data-id="' . $r->id . '" data-tab="mensaje" title="Escribir por WhatsApp o correo"><i class="fab fa-whatsapp"></i></button>' : '')
                    . '</div>';
            })
            ->rawColumns(['seleccion', 'negocio', 'prueba', 'uso', 'etapa', 'contacto', 'acciones'])
            ->make(true);
    }

    /** Numeros de las tarjetas de arriba. */
    public function resumen()
    {
        $hoy = SeguimientoService::hoy()->toDateString();
        $base = fn () => Client::query()->leftJoin('seguimiento_clientes as s', 's.client_id', '=', 'clients.id');
        $sinPagos = 'NOT EXISTS (SELECT 1 FROM pagos WHERE pagos.client_id = clients.id)';

        return response()->json([
            'en_prueba' => $base()->whereRaw($sinPagos)->whereNotNull('clients.trial_ends_at')->whereDate('clients.trial_ends_at', '>', $hoy)->count(),
            'vencidas' => $base()->whereRaw($sinPagos)->whereNotNull('clients.trial_ends_at')->whereDate('clients.trial_ends_at', '<=', $hoy)->count(),
            'sin_uso' => $base()->whereRaw($sinPagos)->whereNotNull('clients.trial_ends_at')->where('s.uso_nivel', 'sin_uso')->count(),
            'para_hoy' => $base()->whereDate('s.proxima_accion_at', '<=', $hoy)->where(fn ($w) => $w->whereNull('s.etapa')->orWhereNotIn('s.etapa', ['convertido', 'perdido']))->count(),
            'convertidos' => $base()->whereRaw('EXISTS (SELECT 1 FROM pagos WHERE pagos.client_id = clients.id)')->count(),
        ]);
    }

    /** Todo lo que necesita la ventana de seguimiento de un cliente. */
    public function ficha(Client $client)
    {
        $ficha = $this->seguimiento->ficha($client);
        $asesor = $this->usuario()?->name;
        $vars = $this->seguimiento->variables($client, $asesor);

        $plantillas = collect(SeguimientoPlantillas::PLANTILLAS)->map(fn ($p, $k) => [
            'clave' => $k,
            'nombre' => $p['nombre'],
            'asunto' => SeguimientoPlantillas::aplicar($p['asunto'], $vars),
            'cuerpo' => SeguimientoPlantillas::aplicar($p['cuerpo'], $vars),
        ])->values();

        $dias = $this->seguimiento->diasRestantes($client);
        $segmento = $this->seguimiento->segmento($client);

        // Plantilla sugerida segun la situacion del cliente.
        $sugerida = match (true) {
            $segmento === SeguimientoService::PRUEBA_VENCIDA => 'prueba_vencida',
            $segmento === SeguimientoService::EN_PRUEBA && $dias !== null && $dias <= 2 => 'por_vencer',
            $ficha->uso_nivel === 'poco_uso' => 'poco_uso',
            $ficha->uso_nivel === 'sin_uso' => 'sin_uso',
            default => 'agradecimiento',
        };

        return response()->json([
            'cliente' => [
                'id' => $client->id,
                'razon_social' => $client->razon_social,
                'email' => $client->email,
                'telefono' => $client->telefono,
                'whatsapp' => SeguimientoService::telefonoWhatsapp($client->telefono),
                'ruc' => $client->ruc,
                'registrado' => $client->created_at?->setTimezone('America/Lima')->format('d/m/Y H:i'),
                'fin_prueba' => $client->trial_ends_at?->format('d/m/Y'),
                'dias_restantes' => $dias,
                'segmento' => $segmento,
                'url' => $this->seguimiento->urlPanel($client),
            ],
            'ficha' => [
                'etapa' => $ficha->etapa,
                'motivo_perdida' => $ficha->motivo_perdida,
                'proxima_accion_at' => $ficha->proxima_accion_at?->toDateString(),
                'proxima_accion_nota' => $ficha->proxima_accion_nota,
                'ultimo_contacto' => $ficha->ultimo_contacto_at?->setTimezone('America/Lima')->format('d/m/Y H:i'),
                'no_contactar' => $ficha->no_contactar,
                'uso_nivel' => $ficha->uso_nivel,
                'uso_resumen' => $ficha->uso_datos['resumen'] ?? null,
                'uso_conteo' => $ficha->uso_datos['conteo'] ?? [],
                'ultima_actividad' => $ficha->ultima_actividad_at?->setTimezone('America/Lima')->format('d/m/Y H:i'),
                'uso_medido' => $ficha->uso_medido_at?->setTimezone('America/Lima')->format('d/m/Y H:i'),
            ],
            'plantillas' => $plantillas,
            'sugerida' => $sugerida,
            'eventos' => SeguimientoEvento::where('client_id', $client->id)->orderByDesc('created_at')->orderByDesc('id')->limit(100)->get()->map(fn ($e) => [
                'tipo' => $e->tipo,
                'asunto' => $e->asunto,
                'detalle' => $e->detalle,
                'quien' => $e->user_nombre ?: ($e->tipo === 'auto' ? 'Automático' : 'Sistema'),
                'cuando' => $e->created_at->setTimezone('America/Lima')->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function actualizarUso(Client $client)
    {
        $ficha = $this->seguimiento->medirUso($client);

        if (! $ficha->uso_medido_at) {
            return response()->json(['error' => 'No se pudo medir el uso de este cliente.'], 422);
        }

        return response()->json(['success' => 'Uso actualizado.']);
    }

    /** Mide el uso de todas las pruebas que aun no pagaron. */
    public function actualizarUsoTodos()
    {
        @set_time_limit(300);

        $clientes = Client::whereNotNull('trial_ends_at')
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('pagos')->whereColumn('pagos.client_id', 'clients.id'))
            ->get();

        $medidos = 0;
        foreach ($clientes as $c) {
            $ficha = $this->seguimiento->medirUso($c);
            $medidos += $ficha->uso_medido_at && $ficha->uso_medido_at->gt(now()->subMinute()) ? 1 : 0;
        }

        return response()->json(['success' => "Uso actualizado en {$medidos} de {$clientes->count()} clientes."]);
    }

    public function etapa(Request $request, Client $client)
    {
        $data = $request->validate([
            'etapa' => ['required', Rule::in(array_keys(SeguimientoCliente::ETAPAS))],
            'motivo_perdida' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['etapa'] === 'perdido' && blank($data['motivo_perdida'] ?? null)) {
            return response()->json(['error' => 'Indica por qué se perdió el cliente.'], 422);
        }

        $this->seguimiento->cambiarEtapa($client, $data['etapa'], $data['motivo_perdida'] ?? null, $this->usuario());

        AuditLog::registrar('seguimiento.etapa', 'Cambió la etapa de "' . $client->razon_social . '" a ' . $data['etapa'], ['client_id' => $client->id]);

        return response()->json(['success' => 'Etapa actualizada.']);
    }

    public function proximaAccion(Request $request, Client $client)
    {
        $data = $request->validate([
            'fecha' => ['nullable', 'date'],
            'nota' => ['nullable', 'string', 'max:255'],
        ]);

        $ficha = $this->seguimiento->ficha($client);
        $ficha->proxima_accion_at = $data['fecha'] ?? null;
        $ficha->proxima_accion_nota = ($data['fecha'] ?? null) ? ($data['nota'] ?? null) : null;
        $ficha->save();

        return response()->json(['success' => $ficha->proxima_accion_at ? 'Próximo contacto programado.' : 'Próximo contacto quitado.']);
    }

    public function nota(Request $request, Client $client)
    {
        $data = $request->validate([
            'tipo' => ['required', Rule::in(['nota', 'llamada', 'whatsapp'])],
            'detalle' => ['required', 'string', 'max:2000'],
        ]);

        $titulos = ['nota' => 'Nota', 'llamada' => 'Llamada', 'whatsapp' => 'WhatsApp'];
        $this->seguimiento->evento($client, $data['tipo'], $titulos[$data['tipo']], $data['detalle'], $this->usuario());

        // Una llamada o un WhatsApp es un contacto; una nota interna no.
        if ($data['tipo'] !== 'nota') {
            $this->seguimiento->marcarContacto($this->seguimiento->ficha($client));
        }

        return response()->json(['success' => 'Registrado en el historial.']);
    }

    public function noContactar(Request $request, Client $client)
    {
        $ficha = $this->seguimiento->ficha($client);
        $ficha->no_contactar = $request->boolean('valor');
        $ficha->save();

        $this->seguimiento->evento($client, 'nota', 'Preferencia de contacto', $ficha->no_contactar ? 'Pidió no recibir más mensajes.' : 'Volvió a aceptar mensajes.', $this->usuario());

        return response()->json(['success' => $ficha->no_contactar ? 'No se le enviarán más mensajes.' : 'Podrá recibir mensajes otra vez.']);
    }

    public function enviarCorreo(Request $request, Client $client)
    {
        $data = $request->validate([
            'asunto' => ['required', 'string', 'max:200'],
            'cuerpo' => ['required', 'string', 'max:5000'],
        ]);

        $vars = $this->seguimiento->variables($client, $this->usuario()?->name);
        [$ok, $msg] = $this->seguimiento->enviarCorreo(
            $client,
            SeguimientoPlantillas::aplicar($data['asunto'], $vars),
            SeguimientoPlantillas::aplicar($data['cuerpo'], $vars),
            $this->usuario()
        );

        if ($ok) {
            AuditLog::registrar('seguimiento.correo', 'Envió un correo de seguimiento a "' . $client->razon_social . '"', ['client_id' => $client->id]);
        }

        return response()->json($ok ? ['success' => $msg] : ['error' => $msg], $ok ? 200 : 422);
    }

    /** Un mismo mensaje (con variables por cliente) a varios clientes. */
    public function enviarMasivo(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:200'],
            'ids.*' => ['integer'],
            'asunto' => ['required', 'string', 'max:200'],
            'cuerpo' => ['required', 'string', 'max:5000'],
        ]);

        @set_time_limit(300);
        $enviados = 0;
        $fallidos = [];

        foreach (Client::whereIn('id', $data['ids'])->get() as $client) {
            $vars = $this->seguimiento->variables($client, $this->usuario()?->name);
            [$ok, $msg] = $this->seguimiento->enviarCorreo(
                $client,
                SeguimientoPlantillas::aplicar($data['asunto'], $vars),
                SeguimientoPlantillas::aplicar($data['cuerpo'], $vars),
                $this->usuario()
            );

            $ok ? $enviados++ : $fallidos[] = $client->razon_social . ': ' . $msg;
        }

        AuditLog::registrar('seguimiento.masivo', "Envió un correo de seguimiento a {$enviados} clientes", ['ids' => $data['ids']]);

        return response()->json([
            'success' => "Se enviaron {$enviados} correos" . ($fallidos ? ' y ' . count($fallidos) . ' no se pudieron enviar.' : '.'),
            'enviados' => $enviados,
            'fallidos' => $fallidos,
        ]);
    }
}
