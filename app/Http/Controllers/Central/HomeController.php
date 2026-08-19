<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tenant;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller
{
    public function index()
    {
        $hoy = Carbon::now('America/Lima');

        // ================= KPIs =================

        $totalTenants = Tenant::count();
        $tenantsActivos = Tenant::where('status', 'activo')->count();
        $tenantsSuspendidos = Tenant::where('status', 'suspendido')->count();
        $tenantsCancelados = Tenant::where('status', 'cancelado')->count();

        // MRR estimado: suma del precio de referencia de cada plan (config/saas.php)
        // por cada tenant activo en ese plan. Es un piso estimado, no una cifra
        // de facturación real (Plus/Empresarial pueden tener acuerdos distintos).
        $tenantsPorPlan = Tenant::where('status', 'activo')
            ->select('plan', DB::raw('count(*) as total'))
            ->groupBy('plan')
            ->pluck('total', 'plan');

        // El MRR se calcula por tipo_negocio + plan porque cada vertical tiene
        // su propio precio por plan (ej: Start de Tallermoto ≠ Start de Genérico).
        $tenantsPorNegocioYPlan = Tenant::where('status', 'activo')
            ->select('tipo_negocio', 'plan', DB::raw('count(*) as total'))
            ->groupBy('tipo_negocio', 'plan')
            ->get();

        $planesConfigPorNegocio = [];
        $mrrEstimado = 0;
        foreach ($tenantsPorNegocioYPlan as $fila) {
            $planesConfigPorNegocio[$fila->tipo_negocio] ??= saas_plans_config($fila->tipo_negocio);
            $mrrEstimado += ($planesConfigPorNegocio[$fila->tipo_negocio][$fila->plan]['price'] ?? 0) * $fila->total;
        }

        $planLabels = ['start' => 'Start', 'basic' => 'Basic', 'plus' => 'Plus', 'empresarial' => 'Empresarial'];
        $tenantsPorPlanLabels = [];
        $tenantsPorPlanData = [];
        foreach ($planLabels as $key => $label) {
            $tenantsPorPlanLabels[] = $label;
            $tenantsPorPlanData[] = $tenantsPorPlan[$key] ?? 0;
        }

        // ================= NUEVOS TENANTS (últimos 6 meses) =================

        $mesesEs = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $labelsMeses = [];
        $serieNuevosTenants = [];

        for ($i = 5; $i >= 0; $i--) {
            $mesRef = $hoy->copy()->subMonthsNoOverflow($i);
            $labelsMeses[] = $mesesEs[$mesRef->month - 1];

            $serieNuevosTenants[] = Tenant::whereYear('created_at', $mesRef->year)
                ->whereMonth('created_at', $mesRef->month)
                ->count();
        }

        // ================= COBROS DE LA SEMANA (vencidos + por vencer en 7 días) =================
        // El cobro es recurrente por día del mes (billing_day). Se excluyen los
        // ciclos que ya tienen un Pago registrado para el mes actual, y se
        // muestran tanto los vencidos (ya pasó el día y no se pagó) como los
        // que vencen dentro de los próximos 7 días.

        $proximosVencimientos = Client::where('status', 'activo')
            ->whereNotNull('billing_day')
            ->with(['pagos' => fn ($q) => $q->where('periodo', $hoy->format('Y-m'))])
            ->get()
            ->map(function ($cliente) use ($hoy) {
                $cliente->estado_ciclo = $cliente->estadoCicloActual($hoy);
                $cliente->fecha_cobro = $cliente->fechaCicloActual($hoy);

                return $cliente;
            })
            ->filter(fn ($cliente) => in_array($cliente->estado_ciclo, ['vencido', 'por_vencer']))
            ->sortBy('fecha_cobro')
            ->values()
            ->take(8);

        // ================= ÚLTIMOS CLIENTES REGISTRADOS =================

        $ultimosClientes = Client::orderByDesc('created_at')->limit(6)->get();

        // Plan y tipo_negocio de cada tenant asociado a los últimos clientes,
        // para no golpear la BD tenant por tenant dentro de la vista.
        $tenantsIndex = Tenant::whereIn('id', $ultimosClientes->pluck('tenant_id'))->get()->keyBy('id');

        return view('central.menu.home', compact(
            'hoy',
            'totalTenants',
            'tenantsActivos',
            'tenantsSuspendidos',
            'tenantsCancelados',
            'mrrEstimado',
            'tenantsPorPlanLabels',
            'tenantsPorPlanData',
            'labelsMeses',
            'serieNuevosTenants',
            'proximosVencimientos',
            'ultimosClientes',
            'tenantsIndex'
        ));
    }
    public function inicio()
    {
        $tenantid = null;
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $dataProductos = [];
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            if ($plan == 'start' || $tiponegocio !== 'tallermoto') {
                $colorview = $empresa->tipo_tema ?? 'dark';
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            // basic, plus y empresarial comparten la web completa (multi-página).
            $colorview = $empresa->tipo_tema ?? 'dark';

            if (tenant_has_module('productos')) {
                // Plus/Empresarial: Query Base para Productos con Lotes acumulados
                $queryProductos = DB::table('producto as pd')
                    ->join('categoria as ct', 'pd.CAT_Id', '=', 'ct.CAT_Id')
                    ->join('lote as lt', 'pd.PRO_Id', '=', 'lt.PRO_Id')
                    ->select(
                        'pd.PRO_Id',
                        'pd.PRO_Nombre',
                        'pd.PRO_Descripcion',
                        'pd.PRO_Marca',
                        'pd.PRO_PrecioVenta',
                        'pd.PRO_Imagen',
                        'ct.CAT_Id',
                        'ct.CAT_Nombre',
                        DB::raw('SUM(lt.LOT_CantidadReal) as cantidad_total')
                    )
                    ->groupBy(
                        'pd.PRO_Id',
                        'pd.PRO_Nombre',
                        'pd.PRO_Descripcion',
                        'pd.PRO_Marca',
                        'pd.PRO_PrecioVenta',
                        'pd.PRO_Imagen',
                        'ct.CAT_Id',
                        'ct.CAT_Nombre'
                    );

                // Paginación de 12 en 12 productos (Mantiene la query con string del buscador si aplica)
                $dataProductos = $queryProductos->paginate(4)->withQueryString();
            } else {
                // Basic: sin catálogo de productos habilitado.
                $dataProductos = null;
            }
            return view('tenant_' . $tiponegocio . '.landing.index', compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview', 'dataProductos'));
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function salir()
    {
        Auth::guard('central')->logout();
        return redirect()->route('central.login');
    }
}
