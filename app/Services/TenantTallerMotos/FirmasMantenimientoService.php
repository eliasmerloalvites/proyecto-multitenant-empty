<?php

namespace App\Services\TenantTallerMotos;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Firmas digitales de un mantenimiento (ver la migracion
 * create_mantenimiento_firma_tables). Reglas:
 *
 * - Orden fijo: TECNICO -> REPRESENTANTE -> CLIENTE.
 * - TECNICO: solo el usuario asignado al mantenimiento (PER_Id).
 * - REPRESENTANTE: un usuario con rol Admin o Gerente.
 * - CLIENTE: firma en el equipo del taller; queda como testigo el usuario
 *   con la sesion abierta. Con su firma el mantenimiento queda cerrado.
 * - Reabrir: solo Admin, con motivo; borra las firmas y deja registro.
 *
 * Igual que la tabla de firmas de los PDF, solo esta en planes Plus y
 * Empresarial. Los errores para el usuario salen como RuntimeException.
 */
class FirmasMantenimientoService
{
    public const ROLES = ['TECNICO', 'REPRESENTANTE', 'CLIENTE'];
    public const ROLES_REPRESENTANTE = ['Admin', 'Gerente'];
    public const ROL_REABRIR = 'Admin';
    private const DISCO = 'local';
    private const MAX_BYTES = 400 * 1024;

    public static function disponible(): bool
    {
        return in_array(tenant('plan'), ['plus', 'empresarial'], true)
            && Schema::hasTable('mantenimiento_firma');
    }

    /** Metadata del tipo (tabla, prefijo, ...) a partir del slug de la ruta (ej. "generalcarburada"). */
    public static function tipoPorSlug(string $slug): ?array
    {
        foreach (GestionProcesoService::tipos() as $meta) {
            if ($meta['ruta_param'] === $slug) {
                return $meta;
            }
        }

        return null;
    }

    public static function tipoPorTabla(string $tabla): ?array
    {
        foreach (GestionProcesoService::tipos() as $meta) {
            if ($meta['tabla'] === $tabla) {
                return $meta;
            }
        }

        return null;
    }

    public static function estaCerrado(string $tabla, int $id): bool
    {
        return Schema::hasTable('mantenimiento_firma')
            && DB::table('mantenimiento_firma')->where('FIR_Tabla', $tabla)->where('FIR_RegistroId', $id)->where('FIR_Rol', 'CLIENTE')->exists();
    }

    /** Lanza RuntimeException si el mantenimiento ya tiene la firma del cliente. */
    public static function asegurarEditable(string $tabla, int $id): void
    {
        if (self::estaCerrado($tabla, $id)) {
            throw new RuntimeException('Este mantenimiento ya fue firmado por el cliente y está cerrado. Un administrador puede reabrirlo desde la sección Firmas.');
        }
    }

    /** Firmas guardadas indexadas por rol. */
    public static function firmas(string $tabla, int $id)
    {
        if (! Schema::hasTable('mantenimiento_firma')) {
            return collect();
        }

        return DB::table('mantenimiento_firma as f')
            ->leftJoin('users as u', 'u.id', '=', 'f.USU_Id')
            ->where('f.FIR_Tabla', $tabla)
            ->where('f.FIR_RegistroId', $id)
            ->get(['f.*', 'u.name as usuario'])
            ->keyBy('FIR_Rol');
    }

    /**
     * Estado de las 3 firmas para la pantalla, con lo que el usuario
     * actual puede hacer en cada una.
     */
    public function estado(array $meta, int $id, $usuario): array
    {
        $registro = $this->registro($meta, $id);
        $firmas = self::firmas($meta['tabla'], $id);
        $tecnico = $registro->PER_Id ? DB::table('users')->where('id', $registro->PER_Id)->value('name') : null;

        $roles = [];
        foreach (self::ROLES as $rol) {
            $f = $firmas->get($rol);
            $motivo = $f ? null : $this->motivoNoPuede($rol, $registro, $firmas, $usuario);
            $roles[$rol] = [
                'firmado' => (bool) $f,
                'nombre' => $f->FIR_Nombre ?? null,
                'documento' => $f->FIR_Documento ?? null,
                'fecha' => $f ? (string) $f->created_at : null,
                'usuario' => $f->usuario ?? null,
                'puede_firmar' => ! $f && $motivo === null,
                'motivo' => $motivo,
            ];
        }

        return [
            'roles' => $roles,
            'cerrado' => $firmas->has('CLIENTE'),
            'puede_reabrir' => $firmas->isNotEmpty() && $usuario->hasRole(self::ROL_REABRIR),
            'tecnico_asignado' => $tecnico,
            'cliente_sugerido' => trim((string) ($registro->{$meta['prefijo'] . '_Propietario'} ?? '')),
        ];
    }

    public function firmar(array $meta, int $id, string $rol, string $imagenDataUri, ?string $nombreCliente, ?string $documento, $usuario, ?string $ip): void
    {
        if (! in_array($rol, self::ROLES, true)) {
            throw new RuntimeException('Tipo de firma no válido.');
        }

        $png = $this->decodificarPng($imagenDataUri);

        DB::transaction(function () use ($meta, $id, $rol, $png, $nombreCliente, $documento, $usuario, $ip) {
            $registro = DB::table($meta['tabla'])->where($meta['prefijo'] . '_Id', $id)->lockForUpdate()->first();
            if (! $registro) {
                throw new RuntimeException('Mantenimiento no encontrado.');
            }

            $firmas = self::firmas($meta['tabla'], $id);
            if ($firmas->has($rol)) {
                throw new RuntimeException('Esta firma ya fue registrada.');
            }
            $motivo = $this->motivoNoPuede($rol, $registro, $firmas, $usuario);
            if ($motivo !== null) {
                throw new RuntimeException($motivo);
            }

            if ($rol === 'CLIENTE') {
                $nombre = trim((string) $nombreCliente);
                if (mb_strlen($nombre) < 3) {
                    throw new RuntimeException('Escribe el nombre de quien firma como cliente.');
                }
            } else {
                $nombre = $usuario->name;
            }

            $ruta = 'firmas/' . tenant('id') . '/' . $meta['tabla'] . '/' . $id . '/' . strtolower($rol) . '-' . Str::uuid() . '.png';
            Storage::disk(self::DISCO)->put($ruta, $png);

            DB::table('mantenimiento_firma')->insert([
                'FIR_Tabla' => $meta['tabla'],
                'FIR_RegistroId' => $id,
                'FIR_Rol' => $rol,
                'FIR_Nombre' => mb_substr($nombre, 0, 150),
                'FIR_Documento' => $rol === 'CLIENTE' && $documento ? mb_substr(trim($documento), 0, 20) : null,
                'FIR_Imagen' => $ruta,
                'USU_Id' => $usuario->id,
                'FIR_Ip' => $ip,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function reabrir(array $meta, int $id, string $motivo, $usuario): void
    {
        if (! $usuario->hasRole(self::ROL_REABRIR)) {
            throw new RuntimeException('Solo un administrador puede reabrir un mantenimiento firmado.');
        }

        $rutas = DB::transaction(function () use ($meta, $id, $motivo, $usuario) {
            $firmas = DB::table('mantenimiento_firma')->where('FIR_Tabla', $meta['tabla'])->where('FIR_RegistroId', $id)->lockForUpdate()->get();
            if ($firmas->isEmpty()) {
                throw new RuntimeException('Este mantenimiento no tiene firmas.');
            }

            DB::table('mantenimiento_firma_reapertura')->insert([
                'FRA_Tabla' => $meta['tabla'],
                'FRA_RegistroId' => $id,
                'USU_Id' => $usuario->id,
                'FRA_Motivo' => mb_substr($motivo, 0, 255),
                'FRA_Firmas' => json_encode($firmas->map(fn ($f) => [
                    'rol' => $f->FIR_Rol, 'nombre' => $f->FIR_Nombre, 'documento' => $f->FIR_Documento,
                    'usuario_id' => $f->USU_Id, 'fecha' => (string) $f->created_at,
                ])->values()->all(), JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('mantenimiento_firma')->whereIn('FIR_Id', $firmas->pluck('FIR_Id'))->delete();

            return $firmas->pluck('FIR_Imagen')->all();
        });

        // Los archivos se borran recien cuando la base ya confirmo.
        Storage::disk(self::DISCO)->delete($rutas);
    }

    /** Contenido PNG de una firma (para mostrarla o incrustarla en el PDF). */
    public static function contenido(string $ruta): ?string
    {
        $disco = Storage::disk(self::DISCO);

        return $disco->exists($ruta) ? $disco->get($ruta) : null;
    }

    public static function dataUri(string $ruta): ?string
    {
        $png = self::contenido($ruta);

        return $png ? 'data:image/png;base64,' . base64_encode($png) : null;
    }

    private function registro(array $meta, int $id): object
    {
        $registro = DB::table($meta['tabla'])->where($meta['prefijo'] . '_Id', $id)->first();
        if (! $registro) {
            throw new RuntimeException('Mantenimiento no encontrado.');
        }

        return $registro;
    }

    /** null si el usuario puede firmar ese rol ahora; si no, el motivo para mostrarle. */
    private function motivoNoPuede(string $rol, object $registro, $firmas, $usuario): ?string
    {
        if ($firmas->has('CLIENTE')) {
            return 'El mantenimiento ya está cerrado.';
        }

        switch ($rol) {
            case 'TECNICO':
                if (! $registro->PER_Id) {
                    return 'Primero asigna un técnico al mantenimiento.';
                }
                if ((int) $registro->PER_Id !== (int) $usuario->id) {
                    return 'Solo el técnico asignado puede firmar, con su propio usuario.';
                }

                return null;

            case 'REPRESENTANTE':
                if (! $firmas->has('TECNICO')) {
                    return 'Primero debe firmar el técnico.';
                }
                if (! $usuario->hasAnyRole(self::ROLES_REPRESENTANTE)) {
                    return 'Firma un usuario con rol Admin o Gerente.';
                }

                return null;

            case 'CLIENTE':
                if (! $firmas->has('REPRESENTANTE')) {
                    return 'El cliente firma al final, después del técnico y del representante.';
                }

                return null;
        }

        return 'Tipo de firma no válido.';
    }

    private function decodificarPng(string $dataUri): string
    {
        if (! preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', $dataUri, $m)) {
            throw new RuntimeException('La firma no llegó bien. Vuelve a firmar.');
        }

        $png = base64_decode($m[1], true);
        if ($png === false || strlen($png) > self::MAX_BYTES || substr($png, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            throw new RuntimeException('La firma no llegó bien. Vuelve a firmar.');
        }

        $info = @getimagesizefromstring($png);
        if (! $info || $info[0] > 2000 || $info[1] > 1000) {
            throw new RuntimeException('La firma no llegó bien. Vuelve a firmar.');
        }

        return $png;
    }
}
