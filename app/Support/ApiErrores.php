<?php

namespace App\Support;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Convierte cualquier excepcion de una ruta /api/* al formato de error unico
 * de la API movil (ver ApiRespuesta), sin filtrar detalles internos
 * (mensajes de SQL, rutas de archivos, trazas). El detalle real igual se
 * registra en el log con el request_id para poder rastrearlo.
 */
class ApiErrores
{
    public static function responder(Throwable $e)
    {
        return match (true) {
            $e instanceof ValidationException => ApiRespuesta::error(
                'ERROR_VALIDACION',
                'Revisa los datos ingresados.',
                422,
                $e->errors()
            ),
            $e instanceof AuthenticationException => ApiRespuesta::error('NO_AUTENTICADO', 'Inicia sesion para continuar.', 401),
            $e instanceof AuthorizationException => ApiRespuesta::error('NO_AUTORIZADO', 'No tienes permiso para esta accion.', 403),
            $e instanceof TenantCouldNotBeIdentifiedException => ApiRespuesta::error('TALLER_NO_ENCONTRADO', 'No encontramos ese taller.', 404),
            $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => ApiRespuesta::error('NO_ENCONTRADO', 'No encontramos lo que buscas.', 404),
            $e instanceof MethodNotAllowedHttpException => ApiRespuesta::error('METODO_NO_PERMITIDO', 'Operacion no permitida.', 405),
            $e instanceof ThrottleRequestsException => self::demasiadasPeticiones($e),
            $e instanceof HttpExceptionInterface => ApiRespuesta::error('ERROR_PETICION', 'No pudimos procesar la solicitud.', $e->getStatusCode()),
            default => ApiRespuesta::error('ERROR_INTERNO', 'Ocurrio un problema. Intentalo de nuevo en un momento.', 500),
        };
    }

    private static function demasiadasPeticiones(ThrottleRequestsException $e)
    {
        $respuesta = ApiRespuesta::error('DEMASIADAS_PETICIONES', 'Demasiados intentos. Espera un momento e intentalo de nuevo.', 429);

        foreach ($e->getHeaders() as $nombre => $valor) {
            $respuesta->headers->set($nombre, $valor);
        }

        return $respuesta;
    }
}
