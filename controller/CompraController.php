<?php
class CompraController extends BasisController
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
        //$products = $producto->getAll();
        //var_dump($products);
        foreach ($products as &$valor) 
        {
            $valor["precio"] = "$ ".number_format($valor["precio"], 0, '.', ',');
            $valor["costo"]  = "$ ".number_format($valor["costo"], 0, '.', ',');
        }
        $response["asdf"] = "holasdd";
        $response["html"] = $this -> gat
        ( 
            "crearCompra", // Plantilla 
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
        if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["CompraController_CrearAction"] = TRUE;             
        }
 
        if($_SESSION["CompraController_CrearAction"] === FALSE)
        {
            //$_POST = [];
            //echo count($_POST);
            header('Location: index.php?controller=compra&action=crear');
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
            if( count($_POST) > 8)
            {
                $message = "";
                $state   = "false";  
                
                $objeto = new Compra();
                
                $objeto->setFecha_compra( MySQLDateFormat($_POST["fecha_compra"]) );
                $objeto->setFecha_ingreso( MySQLDateFormat($_POST["fecha_ingreso"]) );
                $objeto->setFecha_registro( date("Y/m/d") );
                $objeto->setBodega_id($_POST["bodega_entra"]);
                $objeto->setUsuario_id($_SESSION["id"]);
                $objeto->setProveedor( $_POST["proveedor"] );
                $objeto->setComentario( $_POST["comentario"] );
                $objeto->setNeto( extratcNumber($_POST["resumen_neto"]));
                $objeto->setIva(extratcNumber($_POST["resumen_iva"]));
                $objeto->setTotal(extratcNumber($_POST["resumen_total"]));
                //echo "voy a guardar";
                $compra_id = $objeto -> save();
                if($compra_id !== FALSE)
                {
                    //echo "limpiar";
                    //$_POST = [];
                    $state   = "true";
                    $message = "Se creó la Compra id: '".$compra_id."'";
                }
                
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
                            $productos_aux["costo"] = $valor;   
                            $productos[] = $productos_aux;
                            $productos_aux = [];
                        }
                    }
                }
                //print_r($productos);
                
                $productoObj = new Producto(); 
                        
                $compra_has_producto = new Compra_has_Producto(); 
                $prod_error = 0;
                $prod_count = 0;
                foreach($productos as $producto)
                {

                    $compra_has_producto -> setCompra_id( $compra_id );
                    $compra_has_producto -> setProducto_id($producto["id"]);
                    $compra_has_producto -> setCantidad(extratcNumber($producto["cantidad"]));
                    $compra_has_producto ->setCosto( extratcNumber($producto["costo"]));    
                    
                    $aux = $compra_has_producto -> save();
                    
                    $prod_count ++;
                    ///var_dump($aux);
                    if($aux !== TRUE)
                    {
                        $prod_error ++;
                        echo "producto id con error: ".$producto["id"];
                    }
                    else
                    {
                        // Actualizo los costos de la tabla productos 
                        $aux_producto = $productoObj->getBy("id", $producto["id"]);
                        $aux_producto = $aux_producto[0];
                        //print_r($aux_producto);
                        // Si se está comprando el producto más caro, es decir, 
                        // si el precio subió
                        if($aux_producto["costo"] < extratcNumber($producto["costo"]))
                        {
                            $aux1 = $productoObj->UpdateAttribute($producto["id"], "costo", extratcNumber($producto["costo"]));
                            $aux2 = $productoObj->UpdateAttribute($producto["id"], "validado", 2);                            
                        }
                    }
                        
                }
                $message .= "; con '".$prod_count."' Producto(s) guardado(s) y '$prod_error' Error(es).";
                
                /*
                 * Con el objeto de evitar se ejecute esta acción al refrescar 
                 * la página y así guardar registros duplicados
                 */
                $_SESSION["CompraController_CrearAction"] = FALSE; 
            }
            else
            {
                //echo "- else >5<br>";
                $message = "Debe agregar productos a la Compra";
                $state = "false";  
            }
        }
        else
        {
            $message = "Completa el siguiente formulario para crear una nueva Compra";
            $state = "emptyForm";               
        } // end else
        
        $this -> view
        ( 
            "crearCompra", // Plantilla 
            [
                "bodegas" => $bodegas,
                "message" => $message,
                "state"   => "$state"
            ]
        );          
    } 
    public function VerAction()//$create_result="")
    {
        //Creamos el objeto del Modelo "usuario"
        $objeto = new Compra();
        
        //Conseguimos todos los usuarios
        $datos = $objeto->getAll();
        
        foreach ($datos as &$value) 
        {
            //$value["rut"] = $this->rutFormato($value["rut"].$this->dv($value["rut"]));
            $value["fecha_compra"]   = twistDate(  $value["fecha_compra"]  );
            $value["fecha_registro"] = twistDate(  $value["fecha_registro"]  );
            $value["fecha_ingreso"]  = twistDate(  $value["fecha_ingreso"]  );
            
            $value["total"] = "$ ".number_format($value["total"], 0, '.', ',');
            $value["iva"]   = "$ ".number_format($value["iva"], 0, '.', ',');
            $value["neto"]  = "$ ".number_format($value["neto"], 0, '.', ',');
        }

        $this -> view
        ( 
            "verCompra", // Plantilla 
            [
                "datos" => $datos
            ]
        );
    }    
    
    public function EliminarAction()
    {
        $id = $_POST["id"];
        $objeto = new Compra();
        
        $response = Array();
        
        $eliminar = $objeto -> deleteBy("id", $id); 
        //$eliminar = TRUE;
        
        if ($eliminar === TRUE)
        {
            $response["message"] = "Se eliminó con éxito la Compra";
            $response["result"]  = TRUE;
        }
            
        else
        {
            $response["message"] = "No se puedo eliminar la Compra"; 
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
            $_SESSION["CompraController_ActualizarAction"] = TRUE;             
        }        
        $response = Array();
        
        if(isset($_GET["id_busca"]))
        {          
            //$campo = $this->extratcRUT( $_GET["rut_busca"] );
            $campo = $_GET["id_busca"];
            
            $objeto = new Compra();
            $datos = $objeto ->getBy('id', $campo );

            /*
            ob_start();
            var_dump($datos);
            $aux = ob_get_clean();
            exit($aux);
            */
            if(count($datos)==0)
            {
                $response["message"] = "La Compra buscada no existe. Intente nuevamente con un Código diferente"; 
                $response["state"]   = "false";
            }  
            else 
            {
                $response["message"]   = "Se encontró la Compra. Utilice el formulario para editar sus datos.";
                $response["state"]     = "true";
                
                $datos        = $datos[0]; 
                //$datos["rut"] = $datos["rut"].$this->dv($datos["rut"]);
                //$datos["rut"] = $this->rutFormato($datos["rut"]);
                $datos["fecha_compra"] = twistDate($datos["fecha_compra"]);
                $datos["fecha_registro"] = twistDate($datos["fecha_registro"]);
                $datos["fecha_ingreso"] = twistDate($datos["fecha_ingreso"]);
                $response["datos"]   = $datos;

                $usuario = new Usuario();
                $usuario_lista = $usuario ->getBy('id', $datos["usuario_id"] );
                //var_dump($usuario_lista);
                $response["usuario"] = $usuario_lista[0];
                //var_dump($response["usuario"]);
                
                $bodega = new Bodega();
                $bodegas = $bodega->getAll();
                
                $response["bodegas"]   = $bodegas;
                
                $compra_has_producto = new Compra_has_Producto();
                $producto_lista = $compra_has_producto ->getBy('compra_id', $datos["id"] );                
                //var_dump($producto_lista);
                //$resumen = [];
                //$resumen["neto"] = 0;
                foreach ($producto_lista as &$prod) 
                {
                    $prod["total"] = $prod["cantidad"]*$prod["costo"];
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
                "buscaEditaCompra", // Plantilla 
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
        if (isset ( $_SESSION["CompraController_ActualizarAction"] ) )
        {
            if($_SESSION["CompraController_ActualizarAction"] === FALSE)
            {
                //$_POST = [];
                //echo count($_POST);
                header('Location: index.php?controller=compra&action=editar&id_busca='.$_POST["id"]);
                //header('Location: index.php?controller=movimiento&action=actualizar');
                //header('Location: index.php?controller=movimiento&action=editar');
                exit;
            }  
        }    
        if( isset(  $_POST["id"])  )
        {
            $objeto_id = $_POST["id"];

            $objeto = new Compra();
            
            $objeto->setId( $objeto_id );    
            $objeto->setFecha_compra( MySQLDateFormat($_POST["fecha_compra"]) );
            $objeto->setFecha_ingreso( MySQLDateFormat($_POST["fecha_ingreso"]) );
            //$objeto->setFecha_registro( date("Y/m/d") );
            $objeto->setBodega_id($_POST["bodega_id"]);
            //$objeto->setUsuario_id($_SESSION["id"]);
            $objeto->setProveedor( $_POST["proveedor"] );
            $objeto->setComentario( $_POST["comentario"] );
            $objeto->setNeto( extratcNumber($_POST["resumen_neto"]));
            $objeto->setIva(extratcNumber($_POST["resumen_iva"]));
            $objeto->setTotal(extratcNumber($_POST["resumen_total"]));
                
            $result = $objeto->update(); 
            $_SESSION["CompraController_ActualizarAction"] = FALSE; 
            
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
                $compra_has_producto = new Compra_has_Producto();
                
                $eliminar = $compra_has_producto -> deleteBy("compra_id", $objeto_id);
                
                $prod_error = 0;
                if($eliminar === TRUE)
                {
                    foreach($productos as $producto)
                    {

                    $compra_has_producto -> setCompra_id( $objeto_id );
                    $compra_has_producto -> setProducto_id($producto["id"]);
                    $compra_has_producto -> setCantidad(extratcNumber($producto["cantidad"]));
                    $compra_has_producto ->setCosto( extratcNumber($producto["precio"])); 

                        $aux = FALSE;
                        $aux = $compra_has_producto -> save();

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
                    $prod_error = count($productos);
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
                    "buscaEditaCompra", // Plantilla 
                    $response
            );            
            
        }else
            //header('Location: index.php?controller=movimiento&action=editar');
            echo "Post[] vacío.";
    }
    
}
