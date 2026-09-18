<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\User;
use App\Models\EsquemaComision;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class VendedorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Vendedor::with(['user:id,name,email', 'esquemasComision' => fn ($q) => $q->where('activo', true)])
                ->withCount('clientes')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nombre', fn ($row) => $row->user->name ?? '—')
                ->addColumn('email', fn ($row) => $row->user->email ?? '—')
                ->addColumn('codigo_referido', fn ($row) => $row->codigo_referido)
                ->addColumn('esquema', function ($row) {
                    $esquema = $row->esquemasComision->first();
                    if (! $esquema) {
                        return '<span class="text-muted">Sin esquema</span>';
                    }

                    return $esquema->porcentaje . '% / ' . $esquema->meses_duracion . ' meses';
                })
                ->addColumn('clientes_count', fn ($row) => $row->clientes_count)
                ->addColumn('estado', function ($row) {
                    $badge = $row->estado === 'activo' ? 'badge-success' : 'badge-secondary';

                    return '<span class="badge ' . $badge . '">' . strtoupper($row->estado) . '</span>';
                })
                ->addColumn('action1', function ($row) {
                    return '<a data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Editar" class="edit btn btn-primary btn-sm editVendedor"><i class="fa fa-edit"></i></a>';
                })
                ->addColumn('action2', function ($row) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Cambiar esquema" class="btn btn-info btn-sm cambiarEsquemaVendedor"><i class="fa fa-percentage"></i></a>';
                })
                ->addColumn('action3', function ($row) {
                    if ($row->estado !== 'activo') {
                        return '';
                    }

                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Desactivar" class="btn btn-secondary btn-sm deactivateVendedor"><i class="fa fa-ban"></i></a>';
                })
                ->rawColumns(['esquema', 'estado', 'action1', 'action2', 'action3'])
                ->make(true);
        }

        return view('central.admin.vendedores.index');
    }

    /**
     * Crea el usuario central (guard 'central', rol Vendedor), el perfil
     * vendedores y su primer esquema de comisión — todo en un solo paso,
     * ya listo para recibir referidos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'meses_duracion' => 'required|integer|min:1|max:60',
        ]);

        // PER_Id no está en $fillable de Central\User (columna legacy sin
        // default en la tabla) — se asigna directo, mismo patrón que ya usa
        // UserController::store().
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->estadousuario = 1;
        $user->tipousuario = 0;
        $user->PER_Id = 1;
        $user->save();
        $user->assignRole('Vendedor');

        $vendedor = Vendedor::create([
            'user_id' => $user->id,
            'codigo_referido' => $this->generarCodigoReferido($validated['name']),
            'estado' => 'activo',
        ]);

        EsquemaComision::create([
            'vendedor_id' => $vendedor->id,
            'porcentaje' => $validated['porcentaje'],
            'meses_duracion' => $validated['meses_duracion'],
            'activo' => true,
        ]);

        \App\Models\AuditLog::registrar(
            'vendedor.creado',
            'Creó al vendedor "' . $validated['name'] . '" (' . $validated['porcentaje'] . '% / ' . $validated['meses_duracion'] . ' meses)',
            ['vendedor_id' => $vendedor->id]
        );

        return response()->json(['success' => 'Vendedor creado correctamente.']);
    }

    public function edit(Vendedor $vendedor)
    {
        $vendedor->load('user:id,name,email');

        return response()->json([
            'data' => [
                'id' => $vendedor->id,
                'name' => $vendedor->user->name ?? '',
                'email' => $vendedor->user->email ?? '',
                'codigo_referido' => $vendedor->codigo_referido,
                'estado' => $vendedor->estado,
            ],
        ]);
    }

    public function update(Request $request, Vendedor $vendedor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $vendedor->user_id,
            'estado' => 'required|in:activo,inactivo',
        ]);

        $vendedor->user()->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $vendedor->update(['estado' => $validated['estado']]);

        \App\Models\AuditLog::registrar(
            'vendedor.actualizado',
            'Actualizó al vendedor "' . $validated['name'] . '"',
            ['vendedor_id' => $vendedor->id]
        );

        return response()->json(['success' => 'Vendedor actualizado correctamente.']);
    }

    /**
     * Elimina al vendedor y su usuario central. Los clientes que ya trajo
     * (cliente_vendedor, comisiones) NO se borran (siguen ligados por
     * vendedor_id, útil para conservar el historial de comisiones ya
     * generadas) — solo deja de poder traer clientes nuevos ni loguearse.
     */
    public function destroy(Vendedor $vendedor)
    {
        $nombre = $vendedor->user->name ?? ('Vendedor #' . $vendedor->id);
        $userId = $vendedor->user_id;

        $vendedor->update(['estado' => 'inactivo']);
        \App\Models\Central\User::where('id', $userId)->update(['estadousuario' => 0]);

        \App\Models\AuditLog::registrar(
            'vendedor.desactivado',
            'Desactivó al vendedor "' . $nombre . '"',
            ['vendedor_id' => $vendedor->id]
        );

        return response()->json(['success' => 'Vendedor desactivado correctamente.']);
    }

    private function generarCodigoReferido(string $name): string
    {
        $base = Str::slug($name, '');
        $base = $base === '' ? 'vend' : Str::upper(Str::limit($base, 10, ''));

        do {
            $codigo = $base . Str::upper(Str::random(4));
        } while (Vendedor::where('codigo_referido', $codigo)->exists());

        return $codigo;
    }
}
