<?php

namespace App\Services\Facturacion;

use App\Models\Tenant\EmpresaFacturacion;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Client\Client;

use Greenter\Model\Sale\FormaPagos\FormaPagoContado;

use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;


class GreenterService
{
    protected $empresa;

    public function __construct()
    {
        $this->empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER CONFIGURACION GREENTER
    |--------------------------------------------------------------------------
    */

    public function getSee(): See
    {
        $see = new See();

        /* RUTA CERTIFICADO */

        $certPath = storage_path('app/tenant/' .tenant('tipo_negocio') .'/' .tenant('id') .
            '/sunat/certificado/' .$this->empresa->certificado);
        
        /* CERTIFICADO PEM */
        $see->setCertificate(file_get_contents($certPath));

        /* CERTIFICADO P12 
        $p12 = file_get_contents($certPath);
        $password = $this->empresa->certificado_password;

        $certificate = new X509Certificate($p12, $password);
        $see->setCertificate($certificate->export(X509ContentType::PEM));*/
                // BETA
        if ($this->empresa->ambiente == 'beta') {
            $see->setService(SunatEndpoints::FE_BETA);
        } else {
            // PRODUCCION
            $see->setService(SunatEndpoints::FE_PRODUCCION);
        }

        
        // CREDENCIALES SOL
        $see->setClaveSOL(
            $this->empresa->ruc,
            $this->empresa->sol_usuario,
            $this->empresa->sol_password
        );
        
        return $see;
    }

    public function getCompany(): Company
    {
        $address = (new Address())
            ->setUbigueo($this->empresa->ubigeo ?? '150101')
            ->setDepartamento($this->empresa->departamento ?? 'LIMA')
            ->setProvincia($this->empresa->provincia ?? 'LIMA')
            ->setDistrito($this->empresa->distrito ?? 'LIMA')
            ->setUrbanizacion('-')
            ->setDireccion($this->empresa->direccion ?? 'DIRECCION');

        $company = (new Company())
            ->setRuc($this->empresa->ruc)
            ->setRazonSocial($this->empresa->razon_social)
            ->setNombreComercial($this->empresa->nombre_comercial ?? $this->empresa->razon_social)
            ->setAddress($address);

        return $company;
    }

    public function getClient(array $data): Client
    {
        $client = new Client();

        $client
            ->setTipoDoc($data['tipo_doc'])
            ->setNumDoc($data['numero_doc'])
            ->setRznSocial($data['razon_social']);

        return $client;
    }

    public function getInvoice(): Invoice
    {
        /*
    |--------------------------------------------------------------------------
    | CLIENTE
    |--------------------------------------------------------------------------
    */

        $client = $this->getClient([
            'tipo_doc' => '6',
            'numero_doc' => '20111111111',
            'razon_social' => 'CLIENTE DEMO SAC'
        ]);




    $invoice = (new Invoice())
    ->setUblVersion('2.0')
    ->setTipoOperacion('01') // Venta - Catalog. 51
    ->setTipoDoc('01') // Factura - Catalog. 01 
    ->setSerie('F001')
    ->setCorrelativo('00000001')
    ->setFechaEmision(
    new \DateTime('now', new \DateTimeZone('America/Lima'))
)// Zona horaria: Lima
    // ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
    ->setTipoMoneda('PEN') // Sol - Catalog. 02
    ->setCompany($this->getCompany())
    ->setClient($client)
    ->setMtoOperGravadas(100.00)
    ->setMtoIGV(18.00)
    ->setMtoOperExoneradas(0)
    ->setIcbper(0)  
    ->setTotalImpuestos(18.00)
    ->setValorVenta(100.00)
    ->setSubTotal(118.00)
    ->setMtoImpVenta(118.00);

        /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

        $item = (new SaleDetail())
            ->setCodProducto('P001')
            ->setUnidad('NIU') // Unidad - Catalog. 03
            ->setCantidad(2)
            ->setMtoValorUnitario(50.00)
            ->setDescripcion('PRODUCTO 1')
            ->setMtoBaseIgv(100)
            ->setPorcentajeIgv(18.00) // 18%
            ->setIgv(18.00)
            ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
            ->setTotalImpuestos(18.00) // Suma de impuestos en el detalle
            ->setMtoValorVenta(100.00)
            ->setMtoPrecioUnitario(59.00);

        /*
    |--------------------------------------------------------------------------
    | LEYENDA
    |--------------------------------------------------------------------------
    */

        $legend = (new Legend())
            ->setCode('1000') // Monto en letras - Catalog. 52
            ->setValue('SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES');

        /*
    |--------------------------------------------------------------------------
    | FACTURA
    |--------------------------------------------------------------------------
    */

        /* $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setTipoDoc('01')
            ->setSerie('F001')
            ->setCorrelativo('1')
            ->setFechaEmision(new \DateTime())
            ->setTipoMoneda('PEN')
            ->setCompany($this->getCompany())
            ->setClient($client)
            ->setMtoOperGravadas(200)
            ->setMtoIGV(36)
            ->setTotalImpuestos(36)
            ->setValorVenta(200)
            ->setSubTotal(236)
            ->setMtoImpVenta(236)
            ->setDetails([$detail])
            ->setLegends([$legend]); */
        $invoice->setDetails([$item])->setLegends([$legend]);

        return $invoice;
    }

    public function generateXml()
    {
        /* FACTURA */
        $invoice = $this->getInvoice();
        /*SEE */
        $see = $this->getSee();

        /* NOMBRE XML */

        $filename = $this->empresa->ruc . '-' . $invoice->getTipoDoc() . '-' . $invoice->getSerie() . '-' . $invoice->getCorrelativo();

        /* GENERAR XML */
        $xml = $see->getXmlSigned($invoice);

        /* RUTA DONDE SE GUARDARA EL XML */
        $id = tenant('id');
        $ubicacionNegocio = tenant('tipo_negocio');

        $path = storage_path('app/tenant/' . $ubicacionNegocio . '/' . $id . '/sunat/xml');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        /* GUARDAR XML */
        file_put_contents(
            $path . '/' . $filename . '.xml',
            $xml
        );

        return [
            'success' => true,
            'filename' => $filename . '.xml',
        ];
    }

    public function send()
    {
        /* FACTURA */
        $invoice = $this->getInvoice();

        /* SEE */
        $see = $this->getSee();
        $result = $see->send($invoice);


        /** @var \Greenter\Model\Response\BillResult $result */
        /* RESPUESTA */
        if (!$result->isSuccess()) {

            return [
                'success' => false,
                'error' => [
                    'code' => $result->getError()->getCode(),
                    'message' => $result->getError()->getMessage(),
                ]
            ];
        }

        /* CDR */
        $cdr = $result->getCdrResponse();

        return [
            'success' => true,
            'cdr' => [
                'code' => $cdr->getCode(),
                'description' => $cdr->getDescription(),
                'notes' => $cdr->getNotes(),
            ]
        ];
    }


}
