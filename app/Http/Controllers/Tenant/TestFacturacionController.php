<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestFacturacionController extends Controller
{
    protected $empresa;

    public function __construct()
    {
        $this->empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
    }

    public function enviar()
    {

        // CERTIFICADO
        $rutaCertificado = storage_path('app/tenant/' .tenant('tipo_negocio') .'/' .tenant('id') .
            '/sunat/certificado/' .$this->empresa->certificado);

        $certificadoBase64 = base64_encode(file_get_contents($rutaCertificado));

        // PAYLOAD
        $payload = [
            'serie' => 'B001',
            'correlativo' => '1',
            'fecha_emision' => now()->format('Y-m-d H:i:s'),
            'moneda' => 'PEN',
            'mto_oper_gravadas' => 100,
            'igv' => 18,
            'total_impuestos' => 18,
            'valor_venta' => 100,
            'subtotal' => 118,
            'total' => 118,
            'leyenda' => 'SON CIENTO DIECIOCHO Y 00/100 SOLES',

            // EMPRESA
            'empresa' => [
                'modo' => 'BETA',
                'ruc' => '20000000001',
                'razon_social' => 'EMPRESA DEMO SAC',
                'nombre_comercial' => 'EMPRESA DEMO',
                'usuario_sol' => 'MODDATOS',
                'clave_sol' => 'moddatos',
                'ubigeo' => '130101',
                'departamento' => 'LA LIBERTAD',
                'provincia' => 'TRUJILLO',
                'distrito' => 'TRUJILLO',
                'direccion' => 'AV DEMO 123'
            ],

            // CLIENTE
            'cliente' => [
                'tipo_doc' => '1',
                'numero' => '12345678',
                'nombre' => 'CLIENTE DEMO',
                'direccion' => 'TRUJILLO',
                'correo' => 'demo@gmail.com',
                'telefono' => '999999999'
            ],

            // ITEMS
            'detalle' => [
                [
                    'codigo' => 'P001',
                    'unidad' => 'NIU',
                    'cantidad' => 1,
                    'descripcion' => 'PRODUCTO DEMO',
                    'valor_unitario' => 100,
                    'precio_unitario' => 118,
                    'base_igv' => 100,
                    'porcentaje_igv' => 18,
                    'igv' => 18,
                    'tipo_afectacion' => '10',
                    'total_impuestos' => 18,
                    'valor_venta' => 100
                ]
            ],

            // CERTIFICADO
            'certificado' => $certificadoBase64,
            'clave_certificado' => $this->empresa->certificado_password,
            'extension_certificado' => 'p12'
        ];
        // dd($payload);
        // =========================
        // ENVIAR A MICROSERVICIO
        // =========================

        $response = Http::timeout(120)
            ->post(
                'https://apifacturador.kael.pe/FACT_WebService/api/boleta.php',
                $payload
            );

        // RESPUESTA

        $data = $response->json();

        // GUARDAR XML
        // =========================

        if (!empty($data['xml_base64'])) {
            $rutaXml = 'tenant/' . tenant('tipo_negocio') . '/' .
            tenant('id') . '/sunat/xml/' .$data['xml_name'];
            Storage::put($rutaXml, base64_decode($data['xml_base64']));
        }

        // =========================
        // GUARDAR CDR
        // =========================

        if (!empty($data['cdr_base64'])) {
            
            $rutaCdr = 'tenant/' . tenant('tipo_negocio') . '/' .
            tenant('id') . '/sunat/cdr/' .$data['cdr_name'];
            Storage::put($rutaCdr, 
                base64_decode(
                    $data['cdr_base64']
                )
            );
        }

        return response()->json($data);
    }
}