<?php

namespace App\Jobs;

use App\Services\Facturacion\SunatService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EnviarVentaSunatJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /** Reintentos ante caidas puntuales de SUNAT o de la red. */
    public $tries = 3;

    /** Espera creciente entre reintentos: medio minuto, dos, cinco. */
    public $backoff = [30, 120, 300];

    public $timeout = 180;

    public $ventaId;
    public $tenantId;
    public $tipoNegocio;

    public function __construct($ventaId, $tenantId, $tipoNegocio)
    {
        $this->ventaId = $ventaId;
        $this->tenantId = $tenantId;
        $this->tipoNegocio = $tipoNegocio;
    }

    public function handle(): void
    {
        tenancy()->initialize($this->tenantId);

        // El servicio se resuelve despues de inicializar el tenant: su
        // constructor consulta tenant('id'), y si se inyectara por la firma del
        // metodo se construiria antes de que el tenant exista.
        $resultado = app(SunatService::class)->enviarVenta($this->ventaId);

        // La imagen del ticket ya no se genera aqui: ocupa espacio en disco y
        // casi nunca se usa. Se crea bajo demanda al enviarla por WhatsApp.

        // Solo se relanza cuando el fallo pudo ser transitorio (red, timeout,
        // configuracion). Un rechazo de SUNAT no se reintenta: ya quedo
        // guardado en el documento y reintentarlo daria el mismo resultado.
        if (($resultado['estado'] ?? null) === 'ERROR') {
            throw new \RuntimeException($resultado['descripcion'] ?? 'Error enviando a SUNAT');
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Agotados los reintentos de envio a SUNAT', [
            'venta'  => $this->ventaId,
            'tenant' => $this->tenantId,
            'error'  => $e->getMessage(),
        ]);
    }
}
