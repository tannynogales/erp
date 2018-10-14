<?php
//$DTE = new dte_service();
//$contribuyente = $DTE ->getContribuyente("17327515");
//print_r($contribuyente);

Class dte_client
{

    public $Client;

    function getClient() {
        return $this->Client;
    }

    function setClient($Client) {
        $this->Client = $Client;
    }
    
    public function __construct() 
    {
        // incluir autocarga de composer
        require 'vendor/autoload.php';     
        $url = 'https://libredte.cl';
        $hash   = 'fWbaHVrnzw3tX0YpKSt70Wp3ChFN8cec';
        
        // Crear Cliente
        $this->setClient(new \sasco\LibreDTE\SDK\LibreDTE($hash, $url));
        
        //var_dump(   $this->getClient()   );
    }
    
    function getContribuyente($contribuyente)
    {
        $url = "/dte/contribuyentes/info/$contribuyente";
        $contribuyente = $this->Client -> get($url);
        //var_dump($contribuyente); exit();
        return $contribuyente; 
    }

    function setDTE($receptor, $RUTEmisor, $tipoDTE, $productos)
    {
        $this->DTE = 
        [
            'Encabezado' => 
            [
                'IdDoc' => 
                [
                    'TipoDTE' => $tipoDTE,
                ],
                'Emisor' => 
                [
                    //'RUTEmisor' => '76192083-9',
                    'RUTEmisor' => $RUTEmisor
                ],
                'Receptor' => 
                [
                    'RUTRecep'    => $receptor["rut"]."-".dv($receptor["rut"]),
                    //'RUTRecep'  => '66666666-6',
                    'RznSocRecep' => $receptor["razon_social"],
                    'GiroRecep'   => $receptor["giro"],
                    'DirRecep'    => $receptor["direccion"],
                    'CmnaRecep'   => $receptor["comuna"],
                ],
            ],
            'Detalle' => 
            [
                [
                    'NmbItem' => 'Producto 1',
                    'QtyItem' => 2,
                    'PrcItem' => 10,
                ],
            ],
        ];
        
        /*
            producto.codigo as 'producto_codigo',
            producto.nombre as 'producto_nombre',
            `venta_has_producto`.cantidad as 'cantidad',
            `venta_has_producto`.precio as 'precio_venta',
            `venta_has_producto`.precio*`venta_has_producto`.cantidad as 'total'            
        */      
        $Detalle = [];
        $productos_aux = [];
        foreach($productos as $producto)
        {
            $productos_aux = 
            [
                "NmbItem" => "(".$producto["producto_codigo"].") ".$producto["producto_nombre"],
                "QtyItem" => $producto["cantidad"],
                "PrcItem" => $producto["precio_venta"]
            ];
            $Detalle[] = $productos_aux;
            $productos_aux = [];
        }
        $this->DTE ["Detalle"] = $Detalle;
    }
    
    function getDTE() {
        return $this->DTE;
    }
    function getUnusedFolio($DTE_Tipo = 33, $Contribuyente = 76884800)
    {
        $url = "/dte/admin/dte_folios/info/$DTE_Tipo/$Contribuyente?sinUso=1";
        
        $sin_uso = $this->Client -> get($url);
        return $sin_uso["body"]["sin_uso"]; 
    }
    
    function getDTEbyFolio($folio,$rut=76884800, $tipo_dte=33) 
    {
        //$folio = 1009;
        $response["message"] = "";
        $response["result"]  = "nulo";
        
        $dte    = $tipo_dte;
        $metodo = 1; // =1 servicio web, =0 correo

        // crear cliente
        $this->getClient()->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL
        // consultar dte emitido
        $estado = $this->getClient()->get("/dte/dte_emitidos/xml/$dte/$folio/$rut");
        //print_r($estado); exit();
        
        if ($estado['status']['code']!=200) 
        {
            $unUsedFolio = $this->getUnusedFolio();
            if(inArray($unUsedFolio, $folio))
            {
    
                $response = [
                    "message" => "Folio '$folio' sin uso.",
                    "result"  => TRUE
                ];
                $dte =
                [
                    "Folio" => $folio,
                    "FchEmis" => date("Y/m/d"),
                    "RUTRecep" => 0,
                    "MntNeto"=> 0,
                    "IVA"=> 0,
                    "MntTotal"=> 0,
                    "CdgVendedor" => 0,
                    "estado" => 2  
                ];      
                return ["response" => $response, "dte"=>$dte, "detalle"=>[]];           
            }
            else
                return FALSE;
            //die('Error al obtener el estado del DTE emitido: '.$estado['body']."\n");
        }elseif ($estado['status']['code']==200)
        {
            $response = [
                "message" => "Folio '$folio' encontrado!",
                "result"  => TRUE
            ];            
            $xml = base64_decode($estado['body']);
            $xml = simplexml_load_string($xml);
            print_r($xml); exit();
            $dte = 
            [
                "Folio" => (int) $xml->SetDTE->DTE->Documento->Encabezado->IdDoc->Folio,
                "FchEmis" => (string) $xml->SetDTE->DTE->Documento->Encabezado->IdDoc->FchEmis,
                "RUTRecep" => (int) extratcRUT($xml->SetDTE->DTE->Documento->Encabezado->Receptor->RUTRecep),
                "MntNeto"=> (int) $xml->SetDTE->DTE->Documento->Encabezado->Totales->MntNeto,
                "IVA"=> (int) $xml->SetDTE->DTE->Documento->Encabezado->Totales->IVA,
                "MntTotal"=> (int) $xml->SetDTE->DTE->Documento->Encabezado->Totales->MntTotal,
                "CdgVendedor" => (string) $xml->SetDTE->DTE->Documento->Encabezado->Emisor->CdgVendedor,
                "estado" => 3    
            ];

            $dte_detalle = [];
            foreach ($xml->SetDTE->DTE->Documento->Detalle as $clave => $valor) 
            {
                //print_r ($valor); exit();
                $dte_detalle[] = 
                        [
                            "producto_id" => (int) $valor->CdgItem->VlrCodigo,
                            "cantidad" => (int) $valor->QtyItem,
                            "descuento" => (int) $valor->DescuentoMonto,
                            "precio" => (int) $valor->MontoItem / (int) $valor->QtyItem  //PRECIO VENTA , MontoItem, PrcItem

                        ];
            }
            //print_r($dte_detalle[0]["descuento"]);    
            return ["response" => $response, "dte"=>$dte, "detalle"=>$dte_detalle];            
        }
        else
        {
            $response = [
                "message" => "Error 'else'",
                "result"  => FALSE
            ];       
            return FALSE;
        }
    }
    
    function generarDTE()
    {
        $response = Array();
        $response["message"] = "";
        $response["result"]  = "nulo";
        $response["folio"]   = "nulo";
                
        // crear DTE temporal
        $emitir = $this->getClient()->post('/dte/documentos/emitir', $this->getDTE());
        //$emitir = $Client->post('/dte/documentos/emitir', $dte);
        
        
        if ($emitir['status']['code']!=200) {
            //die('Error al emitir DTE temporal: '.$emitir['body']."\n");
            
            $response["message"] = 'Error al emitir DTE temporal: '.$emitir['body'];
            $response["result"]  = FALSE;
            return $response;
        }
        //exit("exit");  
        //print_r($emitir);

        // crear DTE real
        $generar = $this->getClient()->post('/dte/documentos/generar', $emitir['body']);
        if ($generar['status']['code']!=200) {
            //die('Error al generar DTE real: '.$generar['body']."\n");
            $response["message"] = 'Error al generar DTE real: '.$generar['body'];
            $response["result"]  = FALSE;
            return $response;            
        }

        $response["message"] = 'Se generó el DTE real con éxito, con folio: '.$generar["body"]["folio"];
        $response["folio"]   = $generar["body"]["folio"];
        $response["result"]  = TRUE;
        print_r($generar['body']['dte']);
        //print_r($generar);
        return $response; 
    }
    function generarDTE_copy()
    {
        // datos a utilizar
        $url = 'https://libredte.cl';
        $hash   = 'fWbaHVrnzw3tX0YpKSt70Wp3ChFN8cec';

        $dte = 
        [
            'Encabezado' => 
            [
                'IdDoc' => 
                [
                    'TipoDTE' => 33,
                ],
                'Emisor' => 
                [
                    //'RUTEmisor' => '76192083-9',
                    'RUTEmisor' => '76884800-9'
                ],
                'Receptor' => 
                [
                    'RUTRecep'    => '17779297-7',
                    //'RUTRecep'  => '66666666-6',
                    'RznSocRecep' => 'Persona sin RUT',
                    'GiroRecep'   => 'Particular',
                    'DirRecep'    => 'Santiago',
                    'CmnaRecep'   => 'Santiago',
                ],
            ],
            'Detalle' => 
            [
                [
                    'NmbItem' => 'Producto 1',
                    'QtyItem' => 2,
                    'PrcItem' => 10,
                ],
            ],
        ];
        // incluir autocarga de composer
        //require('../vendor/autoload.php');
        require 'vendor/autoload.php';

        // crear cliente
        $Client = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);

        // $Client->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL
        // crear DTE temporal
        $emitir = $Client->post('/dte/documentos/emitir', $dte);

        //print_r($emitir);

        if ($emitir['status']['code']!=200) {
            die('Error al emitir DTE temporal: '.$emitir['body']."\n");
        }


        // crear DTE real
        $generar = $Client->post('/dte/documentos/generar', $emitir['body']);
        if ($generar['status']['code']!=200) {
            die('Error al generar DTE real: '.$generar['body']."\n");
        }
        print_r($generar["body"]["folio"]);
    }

    function guardaPDF($dte_tipo, $folio, $emisor, $cedible=TRUE)
    {
        //dte
        //folio
        //emisor

        //'/33/1/153275153?cedible=true&papelContinuo=10&copias_tributarias=1&copias_cedibles=2'
        
        // obtener el PDF del DTE
        //echo '###/dte/dte_emitidos/pdf/'.$generar['body']['dte'].'/'.$generar['body']['folio'].'/'.$generar['body']['emisor']."###";
        $generar_pdf = $this->getClient()->get('/dte/dte_emitidos/pdf/'.$dte_tipo.'/'.$folio.'/'.$emisor.'?cedible='.$cedible);
        if ($generar_pdf['status']['code']!=200) {
            die('Error al generar PDF del DTE: '.$generar_pdf['body']."\n");
        }

        // guardar PDF en el disco
        //echo "file/dte/".str_replace('.php', '.pdf', basename(__FILE__));
        file_put_contents("file/dte/".$generar["body"]["folio"].".pdf" , $generar_pdf['body']);    
    }
}
?>