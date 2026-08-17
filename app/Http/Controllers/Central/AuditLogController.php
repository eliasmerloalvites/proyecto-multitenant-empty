<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::query()
            ->when($request->filled('accion'), fn ($q) => $q->where('accion', $request->input('accion')))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('hasta')))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $acciones = AuditLog::query()
            ->select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        return view('central.admin.auditoria.index', compact('logs', 'acciones'));
    }
}
