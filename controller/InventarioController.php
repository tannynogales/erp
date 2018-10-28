<?php
class InventarioController extends BasisController
{
    public function __construct() {
        //parent::__construct();
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
            $valor["costo"]  = "$ ".number_format($valor["costo"], 0, '.', ',');
        }
        $response["asdf"] = "holasdd";
        $response["html"] = $this -> gat
        ( 
            "contarInventario", // Plantilla 
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
    
    public function ContarAction()
    {
        //var_dump( $_SESSION["FacturaController_CrearAction"] );
        if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["InventarioController_ContarAction"] = TRUE;             
        }
 
        if($_SESSION["InventarioController_ContarAction"] === FALSE)
        {
            //$_POST = [];
            //echo count($_POST);
            header('Location: index.php?controller=inventario&action=contar');
            exit;
        }
        $bodega = new Bodega();
        $bodegas = $bodega->getAll();
        //var_dump($bodegas);
        // Primero valido haber recibido las variables desde el formulario
        
        //echo 'count($_POST): '.count($_POST)."<br>";
        if( count($_POST) > 0)
        {
            //echo "- if > 0<br>";
            //foreach($_POST as $nombre_campo => $valor)
            //echo $asignacion = "\$" . $nombre_campo . "='" . $valor . "';";

            if( count($_POST) > 5)
            {
                $message = "";
                $state   = "false";  
                
                $objeto = new Inventario();
                
                $objeto->setBodega_id( $_POST["bodega"] );
                $objeto->setUsuario_id($_SESSION["id"]);
                $objeto->setFecha_registro( date("Y/m/d") );
                $objeto->setFecha_inventario( MySQLDateFormat($_POST["fecha"]) );
                $objeto->setDiferencia_costo( 0 );
                $objeto->setDiferencia_venta( extratcNumber(0) );

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
                        if($campo[0].$campo[1] == "productocosto")
                            $productos_aux["costo"] = extratcNumber($valor);
                        if($campo[0].$campo[1] == "productoprecio")
                        {
                            $productos_aux["precio"] = extratcNumber($valor);   
                            $productos[] = $productos_aux;
                            $productos_aux = [];
                        }
                    }
                }
                
                $costo_aux  = 0;
                $precio_aux = 0;
                foreach($productos as $valor)
                {
                    //var_dump($valor);
                    $costo_aux  += $valor["costo"]*$valor["cantidad"];
                    $precio_aux += $valor["precio"]*$valor["cantidad"];
                }
                //echo $costo_aux;
                //var_dump($productos);
                $objeto->setDiferencia_costo( $costo_aux  );
                $objeto->setDiferencia_venta( $precio_aux );                  
                //echo $objeto->getDiferencia_venta();

                $objeto_id = $objeto -> save();
                if($objeto_id !== FALSE)
                {
                    $state   = "true";
                    $message = "Se creó el Inventario de Productos id: '".$objeto_id."'";
                    /*
                     * Con el objeto de evitar se ejecute esta acción al refrescar 
                     * la página y así guardar registros duplicados
                     */
                    $_SESSION["InventarioController_ContarAction"] = FALSE;                    
                }
                else 
                {
                    $state   = "false";
                    $message = "No se creó el Inventario de Productos id: '".$objeto_id."'";                    
                }
                
                $objeto_has_producto = new Inventario_has_Producto(); 
                $prod_error = 0;
                $prod_count = 0;
                foreach($productos as $producto)
                {

                    $objeto_has_producto -> setInventario_id( $objeto_id );
                    $objeto_has_producto -> setProducto_id($producto["id"]);
                    $objeto_has_producto -> setCantidad(extratcNumber($producto["cantidad"]));
                    $objeto_has_producto -> setPrecio_costo( extratcNumber($producto["costo"]));    
                    $objeto_has_producto -> setPrecio_venta( extratcNumber($producto["precio"]));
                    
                    $aux = $objeto_has_producto -> save();
                    
                    $prod_count ++;
                    ///var_dump($aux);
                    if($aux !== TRUE)
                    {
                        $prod_error ++;
                        echo "producto id: ".$producto["id"];
                    }
                        
                }
                $message .= "; con '".$prod_count."' Producto(s) guardado(s) y '$prod_error' Error(es).";
                
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
            "contarInventario", // Plantilla 
            [
                "bodegas" => $bodegas,
                "message" => $message,
                "state"   => "$state"
            ]
        );          
    } 

    
    public function VerconteoAction()//$create_result="")
    {
        $inventario = new Inventario();
        $conteos    = $inventario->getAll();
        
        $bodega  = new Bodega();
        $bodegas = $bodega->getAll();
        //print_r ($bodegas);
        
        $inventario_has_producto  = new Inventario_has_Producto();
        
        foreach ($conteos as &$value) 
        {
            $value["bodega_nombre"] = search_associative_array($value["bodega_id"], $bodegas, "id", "nombre");
            $value["fecha_inventario"] = twistDate($value["fecha_inventario"]);
            $value["productos"] = $inventario_has_producto->getBy("inventario_id", $value["id"]);
        }
        //$value["bodega_nombre"] = search_associative_array($value["bodega_id"], $bodegas, "id", "nombre");
            
        $this -> view
        ( 
            "verconteo", // Plantilla 
            [
                "datos" => $conteos
            ]
        );
    }       
    
    public function VerstockAction()//$create_result="")
    {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $inventario = new Inventario();
        $conteos    = $inventario->getUltimoConteo();
        //print_r($conteos);
        //$bodega  = new Bodega();
        //$bodegas = $bodega->getAll();
        //print_r ($bodegas);
        
        //$inventario_has_producto  = new Inventario_has_Producto();
        
        foreach ($conteos as &$value) 
        {
            //$value["bodega_nombre"] = search_associative_array($value["bodega_id"], $bodegas, "id", "nombre");
            //echo $value["conteo_fecha"].", ".$value["producto_id"].", ".$value["bodega_id"]."<br>";
            
            $venta = $inventario->getVentas($value["conteo_fecha"], $value["producto_id"], $value["bodega_id"]);
            if(isset($venta[0]))
                $venta = $venta[0]["ventas_cantidad"];
            else
                $venta = 0;
            $value["cantidad"] = $value["conteo_cantidad"]+$venta;
            $value["conteo_fecha"] = twistDate($value["conteo_fecha"]);
            //$value["productos"] = $inventario_has_producto->getBy("inventario_id", $value["id"]);
        }
        
            
        $this -> view
        ( 
            "verstock", // Plantilla 
            [
                "datos" => $conteos
            ]
        );
    }    

    public function VerstockdetalleAction()//$create_result="")
    {
        if( !isset($_GET["conteo_fecha"]) ||!isset($_GET["producto_id"]) ||!isset($_GET["bodega_id"]) )
        {
            echo "Variables no seteadas";
            exit();
        }
        
        $inventario = new Inventario();
        $detalle = $inventario->getVentasDetalle(MySQLDateFormat($_GET["conteo_fecha"]), $_GET["producto_id"], $_GET["bodega_id"]);
        
        //foreach ($detalle as &$value) {        }
            
        $this -> view
        ( 
            "verstockdetalle", // Plantilla 
            [
                "datos" => $detalle
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
                $response["message"]   = "Se encontró la Venta. Utilice el formulario para editar sus datos.";
                $response["state"]     = "true";
                
                $datos        = $datos[0]; 
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
                
                $response["bodegas"]   = $bodegas;
                
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
            $objeto->setFecha_venta( MySQLDateFormat($_POST["fecha_venta"]) );
            $objeto->setBodega_id($_POST["bodega_salida"]);
            //$objeto->setFecha_registro( date("Y/m/d") );
            //$objeto->setUsuario_id($_SESSION["id"]);
            $objeto->setNeto( extratcNumber($_POST["resumen_neto"]));
            $objeto->setIva(extratcNumber($_POST["resumen_iva"]));
            $objeto->setTotal(extratcNumber($_POST["resumen_total"]));
                
        
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
    
}
