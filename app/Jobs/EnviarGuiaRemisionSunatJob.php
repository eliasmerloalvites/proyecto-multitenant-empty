<?php

namespace App\Jobs;

use App\Services\Facturacion\GuiaRemisionSunatService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EnviarGuiaRemisionSunatJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /** Reintentos ante caidas puntuales de SUNAT o de la red. */
    public $tries = 3;

    /** Espera creciente entre reintentos: medio minuto, dos, cinco. */
    public $backoff = [30, 120, 300];

    public $timeout = 180;

    public $guiaId;
    public $tenantId;
    public $tipoNegocio;

    public function __construct($guiaId, $tenantId, $tipoNegocio)
    {
        $this->guiaId = $guiaId;
        $this->tenantId = $tenantId;
        $this->tipoNegocio = $tipoNegocio;
    }

    public function handle(): void
    {
        tenancy()->initialize($this->tenantId);

        $resultado = app(GuiaRemisionSunatService::class)->enviarGuia($this->guiaId);

        // Solo se relanza cuando el fallo pudo ser transitorio (red, timeout,
        // configuracion). Un rechazo de SUNAT, o un envio que quedo
        // PENDIENTE por ticket, no se reintentan aqui: PENDIENTE se
        // resuelve consultando el ticket despues, no reenviando de nuevo.
        if (($resultado['estado'] ?? null) === 'ERROR') {
            throw new \RuntimeException($resultado['descripcion'] ?? 'Error enviando la guia a SUNAT');
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Agotados los reintentos de envio de guia de remision a SUNAT', [
            'guia'   => $this->guiaId,
            'tenant' => $this->tenantId,
            'error'  => $e->getMessage(),
        ]);
    }
}
