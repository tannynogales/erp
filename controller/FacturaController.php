<?php
class FacturaController extends BasisController
{
    public function __construct() {
        //parent::__construct();
    }
    
    public function BuscaContribuyenteAction($rut = FALSE, $return = FALSE) 
    {
        if ( $rut ===  FALSE)
            $rut = extratcRUT($_POST["rut"]);
        /*
        $responce = Array();
        $responce["status"] = [
            "code" => 0,
            "message" => ""
        ];
        */
        /*
         * status -> code = 0, es por defecto
         */
        $responce = 
        [
            "status" => ["code" => 0, "message"=>"vacio"],
            "body" => 
            [
                "razon_social" => 1,
                "giro" => 1,
                "direccion" => 1,
                "comuna" => 1,
                "correo" => 1,
                "contacto" => 1
            ]
        ];
        
        //$responce["status"]["code"] = 1;
        //print_r($responce);
        
        $contribuyente = new Contribuyente();
        
        $contribuyente->setRut( substr($rut, 0, -1) );
        //echo $contribuyente->getRut();

        $data = $contribuyente->getBy("rut", $contribuyente->getRut());
        
        if (count($data) === 0)
        {
            $dte = new dte_client();
            $contribuyente = $dte ->getContribuyente(  substr($rut, 0, -1) );
            
            if( $contribuyente["status"]["code"] == 200 )
            {
                //Consulta exitosa
                $responce["status"]["message"] = $contribuyente["status"]["message"];
                $responce["status"]["code"] = $contribuyente["status"]["code"];      
                
                $comuna = new Comuna();
                $comuna_nombre = $comuna ->getBy("codigo", $contribuyente["body"]["comuna"]);
                if (isset($comuna_nombre[0]["nombre"]))
                $comuna_nombre = $comuna_nombre[0]["nombre"];
                else
                $comuna_nombre = "";
                
                $responce["body"] = 
                [
                    "razon_social" => $contribuyente["body"]["razon_social"],
                    "giro" => $contribuyente["body"]["giro"],
                    "direccion" => $contribuyente["body"]["direccion"],
                    "comuna" => $comuna_nombre,
                    "correo" => $contribuyente["body"]["email"],
                    "contacto" => $contribuyente["body"]["telefono"]
                ];                
            }
            else
            {
                //Consulta no exitosa
                //"code":"403","message":"Forbidden"
                //"code": 404, "message": "Not Found"
                $responce["status"]["message"] = $contribuyente["body"]."<br>(".$contribuyente["status"]["message"].")<br>$rut<br>";
                $responce["status"]["code"] = $contribuyente["status"]["code"];                
            }
        }
        else 
        {
                //Consulta exitosa: El cliente estaba en la base interna
                $responce["status"]["message"] = "Se encontró el Cliente";
                $responce["status"]["code"] = 200;     
                
                $responce["body"] = 
                [
                    "razon_social" => $data[0]["razon_social"],
                    "giro" => $data[0]["giro"],
                    "direccion" => $data[0]["direccion"],
                    "comuna" => $data[0]["comuna"],
                    "correo" => $data[0]["correo"],
                    "contacto" => $data[0]["contacto"]
                ];                 
        }
        if($return)
            return $responce;
        else
            echo json_encode($responce);

        
    }

    public function BuscaProductoAction($create_result="")
    {
        $response = Array();
        //Creamos el objeto del Modelo "usuario"
        $producto = new Producto();
        
        //Conseguimos todos los productos
        $products = $producto->getLike($_POST["valor"], ["codigo", "nombre"]);
        //var_dump($products);
        foreach ($products as &$valor) 
        {
            $valor["precio"] = "$ ".number_format($valor["precio"], 0, '.', ',');
        }
        $response["asdf"] = "holasdd";
        $response["html"] = $this -> gat
        ( 
            "crearFactura", // Plantilla 
            [
                "products" => $products
            ],
            [
                "template" => "products",
                "render"   => FALSE
            ]
        );     
        echo json_encode($response);
    }
    
    public function CrearAction()
    {
        //var_dump( $_SESSION["FacturaController_CrearAction"] );
        if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["FacturaController_CrearAction"] = TRUE;             
        }
 
        if($_SESSION["FacturaController_CrearAction"] === FALSE)
        {
            //$_POST = [];
            //echo count($_POST);
            header('Location: index.php?controller=factura&action=crear');
            exit;
        }
        
        $bodega  = new Bodega();
        $bodegas = $bodega->getAll();
        
        $venta_tipo  = new venta_tipo();
        // Filtro los tipo de venta a sólo los "habilitados"
        $venta_tipos = $venta_tipo->getBy("estado", 1);
        
        $productos = [];
        $productos_aux = [];
        foreach($_POST as $nombre_campo => $valor)
        {
            //echo $asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
            $campo = explode("_", $nombre_campo);
                    
            if($campo[0] == "producto")
            {
                //echo $nombre_campo."<br>";
                if($campo[0].$campo[1] == "productoid")
                    $productos_aux["id"] = $valor;
                if($campo[0].$campo[1] == "productocantidad")
                    $productos_aux["cantidad"] = $valor;
                if($campo[0].$campo[1] == "productoprecio")
                {
                    $productos_aux["precio"] = $valor;   
                    $productos[] = $productos_aux;
                    $productos_aux = [];
                }
            }
        }       
        //var_dump($_POST["cliente_check"]);
        //exit();
            
        // Primero, valido haber recibido las variables desde el formulario
        //echo count($_POST).", ". count($productos);
        if( count($_POST) > 0)
        {
            // Segundo, valido que se hayan enviado productos desde el formulario 
            if( count($productos) > 0 || TRUE)
            {
                //print_r($productos); exit();
                $message = "";
                $state   = "false";  
                
                if(  isset(  $_POST["cliente_check"]  )  )
                {
                    //var_dump($_POST["cliente_check"]); exit();
                    
                    $contribuyente = new Contribuyente();
                    
                    $contribuyente_rut = extratcRUT($_POST["cliente_rut"]);
                    $contribuyente_rut = substr($contribuyente_rut, 0, -1);
                    
                    $contribuyente->setRut( $contribuyente_rut );
                    $contribuyente->setRazon_social($_POST["cliente_nombre"]);
                    $contribuyente->setGiro($_POST["cliente_giro"]);
                    $contribuyente->setDireccion($_POST["cliente_direccion"]);
                    $contribuyente->setCorreo($_POST["cliente_correo"]);
                    $contribuyente->setComuna($_POST["cliente_comuna"]);
                    $contribuyente->setContacto($_POST["cliente_contacto"]);
                    
                    $contribuyente_save = $contribuyente->save();
                    //var_dump($contribuyente_save);
                    if($contribuyente_save === TRUE)
                    {
                        $contribuyente_id = $contribuyente ->getBy("rut", $contribuyente_rut);
                        $contribuyente_id = $contribuyente_id[0]["id"];
                        $message .= "Se grabó el contribuyente ".$_POST["cliente_rut"]." ($contribuyente_id)<br>";                        
                        
                    } else {
                        $message .= "NO se grabó el contribuyente ".$_POST['cliente_rut']."<br>";
                        $contribuyente_id = 0;
                    }
                }
                else
                {
                    $message .= "NO se ingresó ningún contribuyente<br>";
                    $contribuyente_id = 0; //$contribuyente_id = FALSE;
                    //echo $message; exit();
                }
                
                $factura = new Factura();
                
                $factura->setFecha_venta( MySQLDateFormat($_POST["fecha"]) );
                $factura->setBodega_id($_POST["bodega_salida"]);
                $factura->setFecha_registro( date("Y/m/d") );
                $factura->setUsuario_id($_SESSION["id"]);
                $factura->setComentario( $_POST["comentario"] );
                $factura->setNeto( extratcNumber($_POST["resumen_neto"]));
                $factura->setIva(extratcNumber($_POST["resumen_iva"]));
                $factura->setTotal(extratcNumber($_POST["resumen_total"]));
                $factura->setVenta_tipo_id($_POST["venta_tipo"]);
                $factura->setContribuyente_id($contribuyente_id);
                $factura->setEfectivo( extratcNumber($_POST["efectivo"]) );
                $factura->setVuelto( extratcNumber($_POST["vuelto"]) );
                
                //var_dump((int)$contribuyente_id); exit();
                
                //echo "voy a guardar";
                $venta_id = $factura -> save();
                //echo "Despues de guardar factura"; exit();
                if($venta_id !== FALSE)
                {
                    //echo "limpiar";
                    //$_POST = [];
                    $state   = "true";
                    $message .= "Se creó la Venta id: '".$venta_id."'<br>";
                    
                    $caja_movimientos = new Caja_Movimientos();
                    $caja_movimientos->setCaja_usuario_id($_SESSION["id"]);
                    $caja_movimientos->setMonto( extratcNumber($_POST["vuelto"])*-1 );
                    $caja_movimientos->setMovimiento_fecha(MySQLDateFormat($_POST["fecha"]));
                    $caja_movimientos->setMovimiento_registro( date("Y/m/d") );
                    $caja_movimientos->setMovimiento_usuario_id($_SESSION["id"]);          
                    $caja_movimientos_save = $caja_movimientos->save();
                    if($caja_movimientos_save !== FALSE)
                        $message .= "Se guardó la caja'<br>";
                    else
                        $message .= "No se guardó la caja'<br>";
                    
                    $venta_has_producto = new Venta_has_Producto(); 
                    $prod_error = 0;
                    $prod_count = 0;
                    foreach($productos as $producto)
                    {
                        $venta_has_producto -> setVenta_id( $venta_id );
                        $venta_has_producto -> setProducto_id($producto["id"]);
                        $venta_has_producto -> setCantidad(extratcNumber($producto["cantidad"]));
                        $venta_has_producto -> setPrecio( extratcNumber($producto["precio"]));    

                        $aux = $venta_has_producto -> save();

                        $prod_count ++;
                        ///var_dump($aux);
                        if($aux !== TRUE)
                        {
                            $prod_error ++;
                            //echo "producto id: ".$producto["id"];
                        }
                    }
                    
                   $message .= "Resumen: '".$prod_count."' Producto(s) guardado(s) y '$prod_error' Error(es).<br>";
                }
                else
                {
                    $state   = "false";
                    $message .= "No se creó la Venta ni se grabó la caja'<br>";                    
                }
                //DTE_ALLOW
                //$factura->setVenta_tipo_id($_POST["venta_tipo"]);
                if (     $factura->getVenta_tipo_id() === 1 ) // Factura Electronica
                {
                    
                }
                elseif ( $factura->getVenta_tipo_id() === 2 ) // Sin Boleta 
                {
                    
                }
                elseif ( $factura->getVenta_tipo_id() === 3 ) // Boleta Fisica
                {
                    
                }
                elseif ( $factura->getVenta_tipo_id() === 4 ) // Boleta Electronica
                {
                    
                }                
                else // Error: Caso sin Definir
                {
                    
                }
                /*
                 * Con el objeto de evitar se ejecute esta acción al refrescar 
                 * la página y así guardar registros duplicados
                 */
                $_SESSION["FacturaController_CrearAction"] = FALSE; 
            }
            else
            {
                //echo "- else >5<br>";
                $message = "Debe agregar productos a la Venta";
                $state = "false";  
            }
        }
        else
        {
            $message = "Completa el siguiente formulario para crear una nueva Venta";
            $state = "emptyForm";               
        } // end else

        $this -> view
        ( 
            "crearFactura", // Plantilla 
            [
                "bodegas" => $bodegas,
                "message" => $message,
                "state"   => $state,
                "venta_tipos" => $venta_tipos,
                "hoy"     => date("d/m/Y")
            ]
        );          
    } 
    
    public function GenerarDTEAction($venta_id=2)
    {
        
        $response = Array();
        $response["message"] = "nulo";
        $response["result"]  = "nulo";
            
        $objeto = new Factura();
        $venta = $objeto ->getBy("id", $venta_id); 
        
        if (count($venta)==0)
        {
            $response["message"] = "Esta Venta no existe.";
            $response["result"]  = FALSE;
            //print_r($response);
            return $response;            
        }
        
        $venta = $venta[0];
        //print_r($venta);
        
        if(
                $venta["venta_tipo_id"] == 2 or // 2 = sin boleta
                $venta["venta_tipo_id"] == 3 or // 3 = boleta fisica
                $venta["venta_tipo_id"] == 4)   // 4 = boleta electrónica = aún no está desarrollado
        {
            $venta_tipo = new venta_tipo();
            $venta_tipo_nombre = $venta_tipo->getBy("id", $venta["venta_tipo_id"]);
            $venta_tipo_nombre = $venta_tipo_nombre[0]["nombre"];
            
            $response["message"] = "No se puede generar un DTE con el tipo de venta '".$venta["venta_tipo_id"]."' ($venta_tipo_nombre).";
            $response["result"]  = FALSE;   
            //print_r($response);
            return $response;
        }
        elseif ($venta["venta_tipo_id"] == 1)
        {
            //echo "DTE";
            
            $empresa = new empresa();
            $empresa_detalle = $empresa->getBy("id", $_SESSION["empresa_id"]);
            $RUTEmisor = $empresa_detalle[0]["rol"];
            $RUTEmisor = $RUTEmisor."-".dv($RUTEmisor);
            $RUTEmisor = "76884800-9";
            
            $Venta_has_Producto = new Venta_has_Producto();
            $productos = $Venta_has_Producto->getDetalle($venta_id);

            $contribuyente = new contribuyente();
            $contribuyente = $contribuyente ->getBy("id", $venta["contribuyente_id"]);
            $contribuyente = $contribuyente[0];
            
            $dte_client = new dte_client();            
            //$dte_client->setDTE($contribuyente, $RUTEmisor, 33, $productos); 
            
            //print_r($dte_client->getDTE());
            
            //$response = $dte_client->generarDTE();
            
            
            
            print_r($response);
            
        }
        
        
    }
    
    public function VerDetalleAction()//$create_result="")
    {
        $response = Array();        
        
        //print_r($_POST);
        if ($_POST["fuente"] == "dte")
        {
            $obj_venta   = new DTE ();
            $obj_detalle = new DTE_has_Producto(); 
            $fieldName = "folio";
            //exit("holaa");
        }
        else
        {
            $obj_venta   = new Factura ();
            $obj_detalle = new Venta_has_Producto();
            $fieldName = "id";
        }
        
        $venta = $obj_venta->getBy($fieldName, $_POST["id"]);
        $venta = $venta[0];
        
        $venta = array_change_key_case($venta, CASE_LOWER);
        //var_dump($venta);exit();
        
        $productos = $obj_detalle->getDetalle($_POST["id"]);
        foreach ($productos as &$valor) 
        {
            $valor["precio_venta"] = number_format($valor["precio_venta"], 0, ',', '.');
            $valor["total"] = number_format($valor["total"], 0, ',', '.');
        }      
        
        if ($_POST["fuente"] == "dte")
        {
            
            if($venta["tipodte"]==33)
            {
                if($venta["comentario"]=="")
                $venta["comentario"] = "Factura importada desde libreDTE";
                else
                    $venta["comentario"] = $venta["comentario"];
            }elseif($venta["tipodte"]==61)
            {
                $venta["comentario"] = $venta["comentario"]."<br>"."Factura anulada: ".$venta["folioref"];
            }
            else
            {
                $venta["comentario"] = "Tipo de DTE no válido";
            }    
        }
        else
        {
            $venta["comentario"] = nl2br($venta["comentario"]);
        }
        
        
        if (isset($venta["contribuyente_id"]))
            $venta["contribuyente_id"] = (int)$venta["contribuyente_id"];
        else
            $venta["contribuyente_id"] = (int)$venta["rutrecep"];
        
        
        if( $venta["contribuyente_id"] === 0)
        {
            $contribuyente = FALSE;
        }
        else
        {
            if ($_POST["fuente"] == "dte")
            {
                $contribuyente = $this -> BuscaContribuyenteAction($venta["contribuyente_id"], TRUE);
                if($contribuyente["status"]["code"]==200)
                {
                    $contribuyente = $contribuyente["body"];
                    $contribuyente["rut"] = rutFormato($venta["contribuyente_id"]);
                    //var_dump($contribuyente); exit();
                }
                else
                {
                    $contribuyente = FALSE;
                    $venta["comentario"] .= "<br>No se pudo recuperar la info del Cliente."; 

                } 
            }
            else
            {
                $contribuyente_objeto = new Contribuyente();
                $contribuyente = $contribuyente_objeto ->getBy("id", $venta["contribuyente_id"]);
                $contribuyente = $contribuyente[0];
                $contribuyente["rut"] = rutFormato($contribuyente["rut"].dv($contribuyente["rut"]));
                //var_dump($contribuyente); exit();
            }            

            //print_r($contribuyente); exit();
        }
        
        if(!isset($venta["total"]))
            $venta["total"] = $venta["mnttotal"];
        
        if ($_POST["fuente"] == "venta")
        {
            if($venta["venta_tipo_id"]==2)
            {
                $venta["total"] =  $venta["total"] - $venta["iva"]  ;
                $venta["total"] =  $venta["neto"];
                $venta["iva"] = 0;
            }
        }
        /*
        ob_start();
        print_r($productos);
        $response["detalle"] = ob_get_clean();
        */    
        $response["html"] = $this -> gat //Get Ayax Template (gat)
        ( 
            "verfactura", // Plantilla 
            [
                "productos" => $productos,
                "venta" => $venta,
                //"receptor" => $venta["RUTRecep"],
                "IVA" => number_format($venta["iva"], 0, ',', '.'),
                "MntTotal" => number_format($venta["total"], 0, ',', '.'),
                "Contribuyente" => $contribuyente
            ],
            [
                "template" => "detalle",
                "render"   => FALSE
            ]
        );     
        
        echo json_encode($response);
    }
    
    public function VerfoliossinusarAction()//$create_result="")
    {
        //$_SESSION['empresa_id'];
        $dte_client = new dte_client();
        $folios_sin_usar = $dte_client->getUnusedFolio();
        rsort($folios_sin_usar);
        //var_dump($folios_sin_usar); exit();
        
        $this -> view
        ( 
            "ver_folios_sin_usar", // Plantilla 
            [
                "datos" => $folios_sin_usar
            ]
        );
    }        
    
    public function VerAction()//$create_result="")
    {
        //Creamos el objeto del Modelo "usuario"
        $factura = new Factura();
        $ventas = $factura->getAll();

        foreach ($ventas as &$value) 
        {
            //$value["rut"] = $this->rutFormato($value["rut"].$this->dv($value["rut"]));
            $value["neto"]  = number_format($value["neto"], 0, ',', '.');
            $value["total"] = number_format($value["total"], 0, ',', '.');
            $value["iva"]   = number_format($value["iva"], 0, ',', '.');
            //$value["bodega_id"]  = search_associative_array($value["bodega_id"], $bodegas, "id", "nombre");
            //$value["usuario_id"] = search_associative_array($value["usuario_id"], $usuarios, "id", "nombre");
        }        
        //foreach ($ventas as &$value) 
        //$value["rut"] = $this->rutFormato($value["rut"].$this->dv($value["rut"]));

        $this -> view
        ( 
            "verFactura", // Plantilla 
            [
                "datos" => $ventas,
                "titulo" => "Ventas"
            ]
        );
    }    

    public function VerNCAction()//$create_result="")
    {
        //Creamos el objeto del Modelo "usuario"
        $factura = new Factura();
        $ventas = $factura->getNotaCreditoAll();

        foreach ($ventas as &$value) 
        {
            //$value["rut"] = $this->rutFormato($value["rut"].$this->dv($value["rut"]));
            $value["neto"]  = number_format($value["neto"], 0, ',', '.');
            $value["total"] = number_format($value["total"], 0, ',', '.');
            $value["iva"]   = number_format($value["iva"], 0, ',', '.');
            //$value["bodega_id"]  = search_associative_array($value["bodega_id"], $bodegas, "id", "nombre");
            //$value["usuario_id"] = search_associative_array($value["usuario_id"], $usuarios, "id", "nombre");
        }        
        //foreach ($ventas as &$value) 
        //$value["rut"] = $this->rutFormato($value["rut"].$this->dv($value["rut"]));

        $this -> view
        ( 
            "vernotacredito", // Plantilla 
            [
                "datos" => $ventas, 
                "titulo" => "Notas de Crédito"
            ]
        );
    }  
    
    public function EliminarAction()
    {
        $id = $_POST["id"];
        $factura = new Factura();
        
        $response = Array();
        
        $eliminar = $factura -> deleteBy("id", $id); 
        //$eliminar = TRUE;
        
        if ($eliminar === TRUE)
        {
            $response["message"] = "Se eliminó con éxito la Venta";
            $response["result"]  = TRUE;
        }
            
        else
        {
            $response["message"] = "No se puedo eliminar la Venta"; 
            $response["result"]  = FALSE;
        }
                   
        
        echo json_encode($response);
    }
    
    public function ValidarAction()
    {
        if ($_POST["fuente"] == "dte")
        {
            $obj = new DTE ();
            $obj->setFolio($_POST["id"]);
        }
        else
        {
            $obj = new Factura ();
            $obj->setId($_POST["id"]);
        }            
        
        $obj->setValidado(1);
        
        $validar = $obj->validar();

        $response = Array();

        if ($validar === TRUE)
        {
            $response["message"] = "Se validó con éxito la Venta";
            $response["result"]  = TRUE;
        }
            
        else
        {
            $response["message"] = "No se puedo validar la Venta"; 
            $response["result"]  = FALSE;
        }
                   
        echo json_encode($response);
    }
    
    function EditarAction()
    {
          if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["FacturaController_ActualizarAction"] = TRUE;             
        }        
        $response = Array();
        
        if(isset($_GET["id_busca"]))
        {          
            //$campo = $this->extratcRUT( $_GET["rut_busca"] );
            $campo = $_GET["id_busca"];
            
            $objeto = new Factura();
            $datos = $objeto ->getBy('id', $campo );
            
            /*
            ob_start();
            var_dump($datos);
            $aux = ob_get_clean();
            exit($aux);
            */
            if(count($datos)==0)
            {
                $response["message"] = "La Venta buscada no existe. Intente nuevamente con un Código diferente"; 
                $response["state"]   = "false";
            }  
            else 
            {
                $datos        = $datos[0]; 
                
                if ($datos["validado"]==1)
                {
                    $response["message"]   = "No puede modificar esta venta porque ya fue revisada y aprobada.";
                    $response["state"]     = "venta_validada";                    
                }
                elseif($datos["validado"]==0)
                {
                    $response["message"]   = "Se encontró la Venta. Utilice el formulario para editar sus datos.";
                    $response["state"]     = "true";         
                    
                    //$datos["rut"] = $datos["rut"].$this->dv($datos["rut"]);
                    //$datos["rut"] = $this->rutFormato($datos["rut"]);
                    $datos["fecha_venta"] = twistDate($datos["fecha_venta"]);
                    $datos["fecha_registro"] = twistDate($datos["fecha_registro"]);
                    $response["datos"]   = $datos;

                    $usuario = new Usuario();
                    $usuario_lista = $usuario ->getBy('id', $datos["usuario_id"] );
                    //var_dump($usuario_lista);
                    $response["usuario"] = $usuario_lista[0];
                    //var_dump($response["usuario"]);

                    $bodega = new Bodega();
                    $bodegas = $bodega->getAll();

                    $venta_tipo  = new venta_tipo();
                    //$venta_tipos = $venta_tipo->getAll();
                    $venta_tipos = $venta_tipo->getBy("estado", 1);

                    $response["bodegas"]     = $bodegas;
                    $response["venta_tipos"] = $venta_tipos;

                    $venta_has_producto = new Venta_has_Producto();
                    $producto_lista = $venta_has_producto ->getBy('venta_id', $datos["id"] );                
                    //var_dump($producto_lista);
                    //$resumen = [];
                    //$resumen["neto"] = 0;
                    foreach ($producto_lista as &$prod) 
                    {
                        $prod["total"] = $prod["cantidad"]*$prod["precio"];
                        $prod["total"] = "$ ".number_format($prod["total"], 0, '.', ',');
                        //$resumen["neto"] += $prod["total"];
                    }                
                    $response["productos"] = $producto_lista;

                    //$resumen["iva"]        = $resumen["neto"]*0.19;
                    //$resumen["total"]      = $resumen["neto"] + $resumen["iva"] ;
                    //$response["resumen"] = $resumen;                    
                }
                else
                {
                    $response["message"]   = "Atributo ‘validado’ sin definir. Por favor notifique al Administrador.";
                    $response["state"]     = "false";                          
                }
            }
            //$result= $result[0];
        }else
        {
            $response["message"] = "(is not set) Ingrese un Código para buscar"; 
            $response["state"]   = "emptyForm";            
        }
        
        $this -> view
        ( 
                "buscaEditaVenta", // Plantilla
                $response
        );
    }    
    function ActualizarAction()
    {
        /*
        if( count($_POST) == 0 )
        {
            /*
            * ¿Esto se pone en la clase que limpia la condición? 
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            * /
            $_SESSION["FacturaController_ActualizarAction"] = TRUE;             
        }
        */
        //var_dump($_POST);
        if (isset ( $_SESSION["FacturaController_ActualizarAction"] ) )
        {
            if($_SESSION["FacturaController_ActualizarAction"] === FALSE)
            {
                //$_POST = [];
                //echo count($_POST);
                header('Location: index.php?controller=factura&action=editar&id_busca='.$_POST["id"]);
                //header('Location: index.php?controller=movimiento&action=actualizar');
                //header('Location: index.php?controller=movimiento&action=editar');
                exit;
            }  
        }    
        if( isset(  $_POST["id"])  )
        {
            $venta_id = $_POST["id"];

            $objeto = new Factura();
            
            $objeto->setId( $venta_id );
            //echo MySQLDateFormat($_POST["fecha_venta"]); exit();
            $objeto->setFecha_venta( MySQLDateFormat($_POST["fecha_venta"]) );
            $objeto->setBodega_id($_POST["bodega_salida"]);
            //$objeto->setFecha_registro( date("Y/m/d") );
            //$objeto->setUsuario_id($_SESSION["id"]);
            $objeto->setNeto( extratcNumber($_POST["resumen_neto"]));
            $objeto->setIva(extratcNumber($_POST["resumen_iva"]));
            $objeto->setTotal(extratcNumber($_POST["resumen_total"]));
            $objeto->setVenta_tipo_id($_POST["venta_tipo"]);    
            $objeto->setComentario($_POST["comentario"]);
            
            $result = $objeto->update(); 
            $_SESSION["FacturaController_ActualizarAction"] = FALSE; 
            
            if($result === TRUE)
            {
                $response["message"] = "Se han actualizado con éxito los valores"; 
                $response["state"]   = "trueEdit"; 
                
                $productos = [];
                $productos_aux = [];
                foreach($_POST as $nombre_campo => $valor)
                {
                    $campo = explode("_", $nombre_campo);
                    //echo "$nombre_campo: ".$valor."<br>";
                    if($campo[0] == "producto")
                    {
                        //echo $nombre_campo."<br>";
                        if($campo[0].$campo[1] == "productoid")
                            $productos_aux["id"] = $valor;
                        if($campo[0].$campo[1] == "productocantidad")
                            $productos_aux["cantidad"] = $valor;
                        if($campo[0].$campo[1] == "productoprecio")
                        {
                            $productos_aux["precio"] = $valor;   
                            $productos[] = $productos_aux;
                            $productos_aux = [];
                        }
                        
                    }
                }
                //var_dump($productos);
                //exit();
                $venta_has_producto = new Venta_has_Producto();
                
                $eliminar = $venta_has_producto -> deleteBy("venta_id", $venta_id);
                
                if($eliminar === TRUE)
                {
                    $prod_error = 0;
                    foreach($productos as $producto)
                    {

                    $venta_has_producto -> setVenta_id( $venta_id );
                    $venta_has_producto -> setProducto_id($producto["id"]);
                    $venta_has_producto -> setCantidad(extratcNumber($producto["cantidad"]));
                    $venta_has_producto -> setPrecio( extratcNumber($producto["precio"])); 

                        $aux = FALSE;
                        $aux = $venta_has_producto -> save();

                        ///var_dump($aux);
                        if($aux !== TRUE)
                        {
                            $prod_error ++;
                            $response["message"] .= "producto id: ".$producto["id"];
                        }

                    }                    
                }
                else
                {
                    $response["message"] .= "No se pudo resetear los productos";
                }

                $response["message"] .= "; con '".count($productos)."' Producto(s) guardado(s) y '$prod_error' Error(es).";
            }
            else
            {
                $response["message"] = "No se logró actualizar los valores"; 
                $response["state"]   = "falseEdit";                     
            }

            $this -> view
            ( 
                    "buscaEditaVenta", // Plantilla 
                    $response
            );            
            
        }else
            //header('Location: index.php?controller=movimiento&action=editar');
            echo "Post[] vacío.";
    }
    
    function VerVentaDetalleAction()
    {
        $this -> view
        ( 
            "ver_venta_detalle", // Plantilla 
            [
                "datos" => ""
            ]
        );
        
    }
}
