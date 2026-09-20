<?php

namespace App\Http\Controllers\TenantTallerMotos;

/**
 * Configuracion de Empresa propio de taller de motos. Hereda toda la
 * logica compartida de App\Http\Controllers\Tenant\EmpresaFacturacionController
 * y solo sobreescribe vistaEmpresa() (usado por index() y store() de la
 * base) para agregarle el resumen de almacenamiento de archivos (fotos de
 * producto/mantenimiento, logos, etc.) contra el limite del plan del
 * tenant -- pedido puntual de este vertical, no de generico. El binding en
 * AppServiceProvider hace que la ruta de Configuracion > Empresa resuelva
 * a ESTA clase cuando el tenant es 'tallermoto'.
 */
class EmpresaFacturacionController extends \App\Http\Controllers\Tenant\EmpresaFacturacionController
{
    /**
     * Sobreescribe el hook de la base (usado tanto por index() como por
     * store()) para agregarle el resumen de almacenamiento. Asi queda
     * disponible sin importar si se llega por GET (abrir la pantalla) o
     * por POST (guardar, que re-renderiza esta misma vista directo).
     */
    protected function vistaEmpresa($empresa)
    {
        // Mismo limite que ya se hace cumplir al subir un archivo (ver
        // tenant_storage_usado_mb() en app/helpers.php).
        $storageUsadoMb = tenant_storage_usado_mb();
        $storageLimiteMb = (float) tenant('storage_limit_mb');
        $storagePorcentaje = $storageLimiteMb > 0
            ? min(100, round(($storageUsadoMb / $storageLimiteMb) * 100, 1))
            : 0;

        return view(
            'tenant_tallermoto.configuracion.empresa.index',
            compact('empresa', 'storageUsadoMb', 'storageLimiteMb', 'storagePorcentaje')
        );
    }
}
