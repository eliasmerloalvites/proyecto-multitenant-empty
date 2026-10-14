<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultaDocumentoController extends Controller
{
    public $successStatus = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('buscador');
    }

    public function indexsunsat()
    {
        return view('buscadorsunat');
    }

    /**
     * Token del servicio de consulta DNI/RUC (migo.pe). Antes estaba escrito
     * directamente en este archivo (y por lo tanto en el repositorio); ahora
     * se lee de MIGO_TOKEN en el .env, via config/services.php.
     */
    private function tokenMigo(): ?string
    {
        return config('services.migo.token') ?: null;
    }



    public function buscarDni(Request $request,$id)
    {
        if (! $this->tokenMigo()) {
            return response()->json(['success' => [1 => false], 'message' => 'Servicio de consulta no configurado'], 200);
        }


                $curl = curl_init();

        $data = [
            'token' => $this->tokenMigo(),
            'dni' => $id
        ];

        $post_data = http_build_query($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.migo.pe/api/v1/dni",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_TIMEOUT => 10,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $info = json_decode($response, true);
        if($info['success']==false)
        {
            $datos = array(
            1 => $info['success'],
            );
        }
        else if($info['success']==true)
        {
            $cadena = explode(" ",$info['nombre']) ;
            $cad['nombre'] = "";
            $cad['apellido'] = "";
            for($i = 0 ; $i < count($cadena); $i++  ){
                if($i<2){
                    $cad['apellido'] = $cad['apellido'].' '.$cadena[$i];
                }else{
                    $cad['nombre'] = $cad['nombre'].' '.$cadena[$i];
                }
            }

            $datos = array(
            0 => $cad,
            1 => $info['success'],
            );
        }

        return response()->json(['success' => $datos], $this-> successStatus);


    }


    /* function buscarRuc(Request $request,$id)
    {

        $curl = curl_init();

        $data = [
            'token' => $this->tokenMigo(),
            'ruc' => $id
        ];

        $post_data = http_build_query($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.migo.pe/api/v1/ruc",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_TIMEOUT => 10,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $info = json_decode($response, true);
        $cad[]="";

        if($info['direccion_simple'] == ""){
            $cadena = explode(" ",$info['nombre_o_razon_social']) ;
            $cad['nombre'] = "";
            $cad['apellido'] = "";
            for($i = 0 ; $i < count($cadena); $i++  ){
                if($i<2){
                    $cad['apellido'] = $cad['apellido'].' '.$cadena[$i];
                }else{
                    $cad['nombre'] = $cad['nombre'].' '.$cadena[$i];
                }
            }
        }

        $datos = array(
            0 => $cad,
            1 => $info['nombre_o_razon_social'],
            2 => $info['direccion_simple'],
            3 => $info['departamento'].' - '.$info['provincia'].' - '.$info['distrito']
            );

        return response()->json(['success' => $datos], $this-> successStatus);
    } */
    function buscarRuc(Request $request, $id)
    {
        if (! $this->tokenMigo()) {
            return response()->json(['success' => false, 'message' => 'Servicio de consulta no configurado'], 200);
        }

    
        $curl = curl_init();
    
        $data = [
            'token' => $this->tokenMigo(),
            'ruc' => $id
        ];
    
        $post_data = http_build_query($data);
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.migo.pe/api/v1/ruc",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_TIMEOUT => 10,
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        $info = json_decode($response, true);
    
        if (!isset($info['nombre_o_razon_social']) || empty($info['nombre_o_razon_social'])) {
            return response()->json(['success' => false, 'message' => 'RUC Inválido'], 200);
        }
    
        $cad['nombre'] = $info['nombre_o_razon_social'];
        $cad['direccion'] = $info['direccion_simple'];
        $cad['ubicacion'] = $info['departamento'] . ' - ' . $info['provincia'] . ' - ' . $info['distrito'];
    
        return response()->json(['success' => true, 'data' => $cad], 200);
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
