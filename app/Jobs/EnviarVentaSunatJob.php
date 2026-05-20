<?php

namespace App\Jobs;

use App\Http\Controllers\Tenant\VentaController;
use App\Services\Facturacion\SunatService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class EnviarVentaSunatJob implements ShouldQueue
{
    use Dispatchable, Queueable;
    public $ventaId;
    public $tenantId;
    public $tipoNegocio;
    /**
     * Create a new job instance.
     */
    public function __construct($ventaId,$tenantId,$tipoNegocio) {
        $this->ventaId = $ventaId;
        $this->tenantId = $tenantId;
        $this->tipoNegocio = $tipoNegocio;
    }

    /**
     * Execute the job.
     */
    public function handle(SunatService $sunatService): void
    {
        tenancy()->initialize( $this->tenantId );
        $sunatService->enviarVenta( $this->ventaId ); 
        VentaController::ticketImagen( $this->ventaId );
    }
}
