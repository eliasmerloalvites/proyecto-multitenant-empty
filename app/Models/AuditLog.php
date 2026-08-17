<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    public $timestamps = false; // el log es inmutable, solo interesa created_at

    protected $fillable = [
        'user_id',
        'user_name',
        'accion',
        'descripcion',
        'datos',
        'ip',
        'created_at',
    ];

    protected $casts = [
        'datos' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Registra una acción del staff logueado en el panel central. Nunca
     * lanza excepción hacia afuera: un fallo al auditar no debe tumbar la
     * acción real que se está registrando.
     */
    public static function registrar(string $accion, string $descripcion, array $datos = []): void
    {
        try {
            $user = Auth::guard('central')->user();

            static::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'accion' => $accion,
                'descripcion' => $descripcion,
                'datos' => $datos ?: null,
                'ip' => Request::ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
