<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class EmpresaFacturacionController extends Controller
{
    public function index()
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        return view(
            'tenant_'.tenant('tipo_negocio').'.configuracion.empresa.index', 
            compact('empresa')
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $empresa = EmpresaFacturacion::updateOrCreate(
                [
                    'tenant_id' => tenant('id')
                ],
                [
                    // EMPRESA
                    'ruc' => $request->ruc,
                    'razon_social' => $request->razon_social,
                    'nombre_comercial' => $request->nombre_comercial,

                    // DIRECCION
                    'ubigeo' => $request->ubigeo,
                    'direccion' => $request->direccion,
                    'departamento' => $request->departamento,
                    'provincia' => $request->provincia,
                    'distrito' => $request->distrito,
                    //'cod_local' => $request->cod_local,

                    // CONTACTO
                    'telefono' => $request->telefono,
                    'whatsapp' => $request->whatsapp,
                    'correo' => $request->correo,
                    'web' => $request->web,

                    // SOL
                    'sol_usuario' => $request->sol_usuario,
                    'sol_password' => $request->sol_password,

                    // CERTIFICADO
                    'certificado_password' => $request->certificado_password,
                    'certificado_vencimiento' => $request->certificado_vencimiento,

                    // FACTURACION
                    'ambiente' => $request->ambiente,
                    //'proveedor_facturacion' => $request->proveedor_facturacion,
                    //'facturacion_electronica' => $request->facturacion_electronica,

                    // SERIES
                    'serie_factura' => $request->serie_factura,
                    'serie_boleta' => $request->serie_boleta,
                    'serie_nota_credito' => $request->serie_nota_credito,
                    'serie_nota_debito' => $request->serie_nota_debito,

                    // CONFIG
                    /* 'moneda' => $request->moneda,
                    'decimales' => $request->decimales,
                    'formato_pdf' => $request->formato_pdf, */

                    // BRANDING
                    // 'color_principal' => $request->color_principal,

                    // ESTADO
                    'activo' => $request->activo ?? true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | LOGO EMPRESA
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('logo')) {
                /* ELIMINAR ANTERIOR */
                if ($empresa->logo) {
                    $rutaAnterior = str_replace('/storage/', '', $empresa->logo);
                    Storage::disk('public')->delete($rutaAnterior);
                }

                $file = $request->file('logo');
                $nombreArchivo = Str::uuid() . '.webp';
                $path = tenant('tipo_negocio') . '/'. tenant('id'). '/empresa/logo/';

                /* OPTIMIZAR */
                $image = Image::read($file);
                $image->scaleDown(width: 500);
                Storage::disk('public')->put(
                    $path . $nombreArchivo,
                    (string) $image->toWebp(80)
                );
                $empresa->logo = Storage::url($path . $nombreArchivo);
            }

            /*
            |--------------------------------------------------------------------------
            | LOGO PDF
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('logo_pdf')) {
                if ($empresa->logo_pdf) {
                    $rutaAnterior = str_replace('/storage/', '', $empresa->logo_pdf);
                    Storage::disk('public')->delete($rutaAnterior);
                }

                $file = $request->file('logo_pdf');
                $nombreArchivo = Str::uuid() . '.webp';
                $path = tenant('tipo_negocio') . '/'. tenant('id'). '/empresa/logo_pdf/';
                $image = Image::read($file);
                $image->scaleDown(width: 800);
                Storage::disk('public')->put(
                    $path . $nombreArchivo,
                    (string) $image->toWebp(85)
                );
                $empresa->logo_pdf = Storage::url($path . $nombreArchivo);
            }

            /*
            |--------------------------------------------------------------------------
            | CERTIFICADO
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('certificado_ruta')) {
                if ($empresa->certificado_ruta) {
                    $rutaAnterior = str_replace('/storage/', '', $empresa->certificado_ruta);
                    Storage::disk('public')->delete($rutaAnterior);
                }

                $file = $request->file('certificado_ruta');
                $extension = $file->getClientOriginalExtension();
                $nombreArchivo = Str::uuid() . '.' . $extension;
                $path = tenant('tipo_negocio') . '/'. tenant('id'). '/empresa/certificados/';

                Storage::disk('public')->putFileAs(
                    $path,
                    $file,
                    $nombreArchivo
                );

                $empresa->certificado_ruta = Storage::url(
                    $path . $nombreArchivo
                );
            }

            $empresa->save();

            DB::commit();

            return view(
                    'tenant_'.tenant('tipo_negocio').'.configuracion.empresa.index', 
                    compact('empresa')
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $empresa = EmpresaFacturacion::findOrFail($id);

        return response()->json($empresa);
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $empresa = EmpresaFacturacion::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | ELIMINAR LOGOS
            |--------------------------------------------------------------------------
            */

            if ($empresa->logo) {

                Storage::disk('public')->delete($empresa->logo);
            }

            if ($empresa->logo_pdf) {

                Storage::disk('public')->delete($empresa->logo_pdf);
            }

            /*
            |--------------------------------------------------------------------------
            | ELIMINAR CERTIFICADO
            |--------------------------------------------------------------------------
            */

            if ($empresa->certificado_ruta) {

                Storage::disk('public')->delete(
                    $empresa->certificado_ruta
                );
            }

            /*
            |--------------------------------------------------------------------------
            | ELIMINAR REGISTRO
            |--------------------------------------------------------------------------
            */

            $empresa->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Empresa eliminada correctamente'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}