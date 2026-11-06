<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Services\TenantTallerMotos\FirmasMantenimientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

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
    /**
     * Firma automatica del representante: activar/desactivar, nombre y la
     * imagen de su firma o sello. La imagen va al disco privado (igual que
     * las firmas de los mantenimientos). Si se activa sin imagen, queda
     * apagada (no habria nada que estampar).
     */
    protected function guardarFirmaRepresentante(Request $request, $empresa): void
    {
        if (! Schema::hasColumn('empresa_facturacion', 'firma_representante_auto') || ! $request->has('firma_representante_form')) {
            return;
        }

        $request->validate([
            'firma_representante_imagen' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'firma_representante_nombre' => 'nullable|string|max:150',
        ], [
            'firma_representante_imagen.image' => 'La firma debe ser una imagen (PNG, JPG o WEBP).',
            'firma_representante_imagen.max' => 'La imagen de la firma no debe pesar más de 2 MB.',
        ]);

        $disco = Storage::disk('local');

        if ($request->boolean('firma_representante_quitar') && $empresa->firma_representante_imagen) {
            $disco->delete($empresa->firma_representante_imagen);
            $empresa->firma_representante_imagen = null;
        }

        if ($request->hasFile('firma_representante_imagen')) {
            if ($empresa->firma_representante_imagen) {
                $disco->delete($empresa->firma_representante_imagen);
            }

            // PNG (conserva el fondo transparente de un sello) y tamano acotado
            // para que pese poco y quepa en el recuadro del PDF.
            $imagen = Image::read($request->file('firma_representante_imagen'));
            $imagen->scaleDown(width: 600, height: 240);
            $ruta = 'firmas/' . tenant('id') . '/empresa/representante-' . Str::uuid() . '.png';
            $disco->put($ruta, (string) $imagen->toPng());
            $empresa->firma_representante_imagen = $ruta;
        }

        $empresa->firma_representante_nombre = trim((string) $request->input('firma_representante_nombre')) ?: null;
        $empresa->firma_representante_auto = $request->boolean('firma_representante_auto') && (bool) $empresa->firma_representante_imagen;
    }

    protected function vistaEmpresa($empresa)
    {
        // Mismo limite que ya se hace cumplir al subir un archivo (ver
        // tenant_storage_usado_mb() en app/helpers.php).
        $storageUsadoMb = tenant_storage_usado_mb();
        $storageLimiteMb = (float) tenant('storage_limit_mb');
        $storagePorcentaje = $storageLimiteMb > 0
            ? min(100, round(($storageUsadoMb / $storageLimiteMb) * 100, 1))
            : 0;

        // Vista previa de la firma guardada (esta en el disco privado).
        $firmaRepresentanteUri = $empresa && ! empty($empresa->firma_representante_imagen)
            ? FirmasMantenimientoService::dataUri($empresa->firma_representante_imagen)
            : null;
        $firmasDisponibles = FirmasMantenimientoService::disponible();

        return view(
            'tenant_tallermoto.configuracion.empresa.index',
            compact('empresa', 'storageUsadoMb', 'storageLimiteMb', 'storagePorcentaje', 'firmaRepresentanteUri', 'firmasDisponibles')
        );
    }
}
