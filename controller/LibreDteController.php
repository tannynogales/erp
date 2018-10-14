<?php

set_time_limit ( 3000 );
/*
require_once '../lib/libredte-sdk-php-master/sdk/LibreDTE.php';
require_once '../lib/libredte-sdk-php-master/sdk/Network/Http/Rest.php';
require_once '../lib/libredte-sdk-php-master/sdk/Network/Http/Socket.php';

require_once '../core/MyPDO.php';
MyPDO::singleton(require_once '../config/database.php');
require_once '../core/BasisController.php';

require_once '../model/DTE.php';
require_once '../model/DTE_has_Producto.php';
*/
class LibreDteController extends BasisController
{
    public function __construct() {
        //parent::__construct();
    }
    
    function IndexAction()
    {
        
    }
    function GetDteAction()
    {
        $dte_client = new dte_client();
        $dte_responce = $dte_client->getDTEbyFolio(877);
        print_r($dte_responce);
    }    
    function importarDteAction()
    {
        $ventas_sin_detalle = 0;
        $dte_nombre = "";
        
        if(  !isset($_POST["TipoDTE"])  )
        {
            $respuesta = Array();
            $respuesta["detalle"] = FALSE;
            $respuesta["mensaje"] = Array();
            $respuesta["mensaje"][] = "No se ha seccionado un DTE para importar";
        }else
        {
            $max_iteraciones = 200; 
            $iteracion = 0;
            $tipoDTE = $_POST["TipoDTE"];
            
            //var_dump($_POST["TipoDTE"]); exit;
            
            if($tipoDTE==33)
            {
                $folio_desde = 601;
                $dte_nombre = "Facturas Electrónicas";
                
            } elseif($tipoDTE==61) 
            {
                $folio_desde = 41;
                $dte_nombre = "Notas de Crédito";
                
            }else
            {
                echo "Tipo de DTE no válido"; exit();
            }

            $empresa = new Empresa();
            $Contribuyente = $empresa ->getBy("id", $_SESSION["empresa_id"]);
            $Contribuyente = $Contribuyente[0]["rol"];
            //$Contribuyente = 76884800;
            //echo $Contribuyente; exit();


            $dte = new DTE();
            //var_dump($dte);
            $dte_max = $dte ->max("folio", $tipoDTE);
            //print_r($dte_max); exit();
            if($dte_max == 0)
                $folio = $folio_desde; // voy a partir de la 41
            else
                $folio = $dte_max+1;

            //echo  $folio; exit();

            $dte_client = new dte_client();

            $dte_responce = $dte_client->getDTEbyFolio($folio, $Contribuyente, $tipoDTE);

            var_dump($dte_responce); exit();

            //$dte_responce_dte = $dte_responce["dte"];
            //print_r($dte_responce_dte); exit();
            $respuesta = Array();
            $respuesta["detalle"] = FALSE;
            $respuesta["mensaje"] = Array();

            while ($dte_responce != FALSE && $iteracion < $max_iteraciones) 
            {
                $iteracion ++;
                //print_r($dte_responce["dte"]["Folio"]); exit();

                $dte->setFolio($dte_responce["dte"]["Folio"]);
                $dte->setFchEmis($dte_responce["dte"]["FchEmis"]);
                $dte->setRUTRecep($dte_responce["dte"]["RUTRecep"]);
                $dte->setMntNeto($dte_responce["dte"]["MntNeto"]);
                $dte->setIVA($dte_responce["dte"]["IVA"]);
                $dte->setMntTotal($dte_responce["dte"]["MntTotal"]);
                $dte->setCdgVendedor($dte_responce["dte"]["CdgVendedor"]);
                $dte->setFechRegistro(date("Y/m/d"));
                $dte->setTipoDTE(61);
                $dte->setValidado(0); // 0-> No validado, 1-> Validado
                $dte->setEstado( $dte_responce["dte"]["estado"] ); 
                //echo "holaa"; exit();
                $dte_id = $dte->save();
                //var_dump($dte_id); exit();

                $dte_producto = new DTE_has_Producto();

                if($dte_id != FALSE)
                {
                    $producto_guardado = TRUE;
                    foreach ($dte_responce["detalle"] as $valor) 
                    {
                        $dte_producto ->setDTE_id($dte_id);
                        $dte_producto ->setProducto_id($valor["producto_id"]);
                        $dte_producto ->setCantidad($valor["cantidad"]);
                        $dte_producto ->setDescuento($valor["descuento"]);
                        $dte_producto ->setPrecio($valor["precio"]);

                        $dte_producto_save = $dte_producto->save();

                        $respuesta_detalle                      = Array();
                        $respuesta_detalle["id"]                = $dte_id;
                        $respuesta_detalle["folio"]             = $dte_responce["dte"]["Folio"];
                        $respuesta_detalle["producto_id"]       = $valor["producto_id"];
                        $respuesta_detalle["producto_guardado"] = ($dte_producto_save) ? 'true' : 'false';

                        $respuesta["detalle"][] = $respuesta_detalle;
                        if ($dte_producto_save === FALSE)
                            $producto_guardado = FALSE;
                    }
                    $dte->setId($dte_id);
                    if ($producto_guardado ===FALSE)
                    {
                        $dte->setTiene_productos(FALSE);
                        $ventas_sin_detalle ++;
                    }
                    else
                    {
                        $dte->setTiene_productos(TRUE);
                    }
                    $dte->update_tiene_productos();

                    //print_r($respuesta["detalle"]); exit();
                }

                $folio++;
                $dte_responce = $dte_client->getDTEbyFolio($folio, $Contribuyente, $tipoDTE);
            }   

            $respuesta["mensaje"][] = "Se importaron ".$iteracion." ".$dte_nombre.".";

            if ($ventas_sin_detalle == 0)
                $respuesta["mensaje"][] = "No se ingresó $dte_nombre sin detalle";        
            elseif($ventas_sin_detalle ==1)
                $respuesta["mensaje"][] = "Se ingresó una $dte_nombre sin detalle"; 
            elseif ($ventas_sin_detalle>1)
                $respuesta["mensaje"][] = "Se ingresaron ".$ventas_sin_detalle." ventas sin detalle"; 
            else
                $respuesta["mensaje"][] = "No se puede determinar la cantidad de $dte_nombre sin detalle que fueron ingresadas"; 

            $respuesta["mensaje"][] = "Próximo folio: $folio";
        }
        $this -> view
        ( 
            "importardte", // Plantilla 
            [
                "respuesta" => $respuesta,
                "ventas_sin_detalle" => $ventas_sin_detalle,
                "fecha" => date('l jS \of F Y h:i:s A'),
                "dte_nombre" => $dte_nombre
            ]
        );          
    }
    /*
    function importarDteAction()
    { 
        $max_iteraciones = 200; 
        $iteracion = 0;
        $ventas_sin_detalle = 0;
        
        $dte = new DTE();
        //var_dump($dte);
        $dte_max = $dte ->max("folio");
        //print_r($dte_max); exit();
        if($dte_max == 0)
            $folio = 601; //1150 369  360 primer folio emitido 
        else
            $folio = $dte_max+1;
        //$folio = 360;
        
        $dte_client = new dte_client();
        
        $dte_responce = $dte_client->getDTEbyFolio($folio);
        
        //var_dump($dte_responce); exit();
     
        //$dte_responce_dte = $dte_responce["dte"];
        //print_r($dte_responce_dte); exit();
        $respuesta = Array();
        $respuesta["detalle"] = FALSE;
        $respuesta["mensaje"] = Array();
        
        while ($dte_responce != FALSE && $iteracion < $max_iteraciones) 
        {
            $iteracion ++;
            //print_r($dte_responce["dte"]["Folio"]); exit();
            
            $dte->setFolio($dte_responce["dte"]["Folio"]);
            $dte->setFchEmis($dte_responce["dte"]["FchEmis"]);
            $dte->setRUTRecep($dte_responce["dte"]["RUTRecep"]);
            $dte->setMntNeto($dte_responce["dte"]["MntNeto"]);
            $dte->setIVA($dte_responce["dte"]["IVA"]);
            $dte->setMntTotal($dte_responce["dte"]["MntTotal"]);
            $dte->setCdgVendedor($dte_responce["dte"]["CdgVendedor"]);
            $dte->setFechRegistro(date("Y/m/d"));
            $dte->setTipoDTE(33);
            $dte->setValidado(0); // 0-> No validado, 1-> Validado
            $dte->setEstado( $dte_responce["dte"]["estado"] ); 
            //echo "holaa"; exit();
            $dte_id = $dte->save();
            //var_dump($dte_id); exit();
            
            $dte_producto = new DTE_has_Producto();
            
            if($dte_id != FALSE)
            {
                $producto_guardado = TRUE;
                foreach ($dte_responce["detalle"] as $valor) 
                {
                    $dte_producto ->setDTE_id($dte_id);
                    $dte_producto ->setProducto_id($valor["producto_id"]);
                    $dte_producto ->setCantidad($valor["cantidad"]);
                    $dte_producto ->setDescuento($valor["descuento"]);
                    $dte_producto ->setPrecio($valor["precio"]);
                    
                    $dte_producto_save = $dte_producto->save();
                    
                    $respuesta_detalle                      = Array();
                    $respuesta_detalle["id"]                = $dte_id;
                    $respuesta_detalle["folio"]             = $dte_responce["dte"]["Folio"];
                    $respuesta_detalle["producto_id"]       = $valor["producto_id"];
                    $respuesta_detalle["producto_guardado"] = ($dte_producto_save) ? 'true' : 'false';
                    
                    $respuesta["detalle"][] = $respuesta_detalle;
                    if ($dte_producto_save === FALSE)
                        $producto_guardado = FALSE;
                }
                $dte->setId($dte_id);
                if ($producto_guardado ===FALSE)
                {
                    $dte->setTiene_productos(FALSE);
                    $ventas_sin_detalle ++;
                }
                else
                {
                    $dte->setTiene_productos(TRUE);
                }
                $dte->update_tiene_productos();
                
                //print_r($respuesta["detalle"]); exit();
            }

            $folio++;
            $dte_responce = $dte_client->getDTEbyFolio($folio);
        }   
        
        $respuesta["mensaje"][] = "Se importaron ".$iteracion." ventas.";
        
        if ($ventas_sin_detalle == 0)
            $respuesta["mensaje"][] = "No se ingresó ventas sin detalle";        
        elseif($ventas_sin_detalle ==1)
            $respuesta["mensaje"][] = "Se ingresó una venta sin detalle"; 
        elseif ($ventas_sin_detalle>1)
            $respuesta["mensaje"][] = "Se ingresaron ".$ventas_sin_detalle." ventas sin detalle"; 
        else
            $respuesta["mensaje"][] = "No se puede determinar la cantidad de ventas sin detalle que fueron ingresadas"; 
        
        $respuesta["mensaje"][] = "Próximo folio: $folio";

        $this -> view
        ( 
            "importardte", // Plantilla 
            [
                "respuesta" => $respuesta,
                "ventas_sin_detalle" => $ventas_sin_detalle,
                "fecha" => date('l jS \of F Y h:i:s A')
            ]
        );          
    }    
    */
}