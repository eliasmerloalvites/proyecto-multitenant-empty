<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

/**
 * Formato unico de las respuestas de la API movil:
 *   exito: { "data": ..., "meta": {...} }
 *   error: { "error": { "code": "...", "message": "...", "fields": {...}, "request_id": "..." } }
 *
 * "code" es estable y pensado para que la app decida que hacer; "message" es
 * texto para mostrar al usuario.
 */
class ApiRespuesta
{
    public static function ok(mixed $data = null, int $status = 200, array $meta = []): JsonResponse
    {
        $cuerpo = ['data' => $data];

        if ($meta) {
            $cuerpo['meta'] = $meta;
        }

        return response()->json($cuerpo, $status);
    }

    public static function error(string $code, string $message, int $status, array $fields = []): JsonResponse
    {
        $error = [
            'code' => $code,
            'message' => $message,
            'request_id' => request()->attributes->get('request_id'),
        ];

        if ($fields) {
            $error['fields'] = $fields;
        }

        return response()->json(['error' => $error], $status);
    }
}
