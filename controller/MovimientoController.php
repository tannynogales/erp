<?php
class MovimientoController extends BasisController{
    public function __construct() {
        //parent::__construct();
    }

    public function BuscaProductoAction($create_result="")
    {
        $response = Array();
        //Creamos el objeto del Modelo "usuario"
        $producto = new Producto();
        
        //Conseguimos todos los productos
        $products = $producto->getAll();
        //var_dump($products);
        foreach ($products as &$valor) 
        {
            $valor["precio"] = "$ ".number_format($valor["precio"], 0, '.', ',');
        }
        $response["json"] = "I'm a json response";
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
        //var_dump( $_SESSION["MovimientoController_CrearAction"] );
        if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["MovimientoController_CrearAction"] = TRUE;             
        }
 
        if($_SESSION["MovimientoController_CrearAction"] === FALSE)
        {
            //$_POST = [];
            //echo count($_POST);
            header('Location: index.php?controller=movimiento&action=crear');
            exit;
        }
        $bodega = new Bodega();
        $bodegas = $bodega->getAll();
        
        if( count($_POST) > 0)
        {
            if( count($_POST) > 3)
            {
                $message = "";
                $state   = "false";  
                
                $Movimiento = new Movimiento();
                
                $Movimiento->setFecha_movimiento( MySQLDateFormat($_POST["fecha"]) );    
                $Movimiento->setFecha_registro( date("Y/m/d") );
                $Movimiento->setUsuario_id( $_SESSION["id"] );
                $Movimiento->setBodega_id_entra( $_POST["bodega_entrada"] );
                $Movimiento->setBodega_id_sale(  $_POST["bodega_salida"]  );

                $Movimiento_id = $Movimiento -> save();
                
                if($Movimiento_id !== FALSE)
                {
                    $state   = "true";
                    $message = "Se creó el Movimiento id: '".$Movimiento_id."'";
                }
                
                $productos = [];
                $productos_aux = [];
                foreach($_POST as $nombre_campo => $valor)
                {
                    $campo = explode("_", $nombre_campo);
                    
                    if($campo[0] == "producto")
                    {
                        //echo $nombre_campo."<br>";
                        if($campo[0].$campo[1] == "productoid")
                            $productos_aux["id"] = $valor;
                        if($campo[0].$campo[1] == "productocantidad")
                        {
                            $productos_aux["cantidad"] = $valor;
                            $productos[] = $productos_aux;
                            $productos_aux = [];
                        }
                    }
                }
                //echo count($productos);
                $Movimiento_has_Producto = new Movimiento_has_Producto(); 
                $prod_error = 0;
                $prod_count = 0;
                foreach($productos as $producto)
                {

                    $Movimiento_has_Producto -> setMovimiento_de_stock_id( $Movimiento_id );
                    $Movimiento_has_Producto -> setProducto_id($producto["id"]);
                    $Movimiento_has_Producto -> setCantidad(extratcNumber($producto["cantidad"]));
                    
                    $aux = $Movimiento_has_Producto -> save();
                    
                    $prod_count ++;
                    ///var_dump($aux);
                    if($aux !== TRUE)
                    {
                        $prod_error ++;
                        echo "producto id: ".$producto["id"];
                    }
                        
                }
                $message .= "; con '".$prod_count."' Producto(s) guardado(s) y '$prod_error' Error(es).";
                
                /*
                 * Con el objeto de evitar se ejecute esta acción al refrescar 
                 * la página y así guardar registros duplicados
                 */
                $_SESSION["MovimientoController_CrearAction"] = FALSE; 
            }
            else
            {
                //echo "- else >5<br>";
                $message = "Debe agregar productos al Movimiento";
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
            "crearMovimiento", // Plantilla 
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
        $movimiento = new Movimiento();
        
        //Conseguimos todos los usuarios
        $movimientos = $movimiento->getAll();
        
        //foreach ($ventas as &$value) 
        //$value["rut"] = $this->rutFormato($value["rut"].$this->dv($value["rut"]));

        $this -> view
        ( 
            "verMovimiento", // Plantilla 
            [
                "datos" => $movimientos
            ]
        );
    }
    public function EliminarAction()
    {
        $id = $_POST["id"];
        $movimiento = new Movimiento();
        
        $response = Array();
        
        $eliminar = $movimiento -> deleteBy("id", $id); 
        //$eliminar = TRUE;
        
        if ($eliminar === TRUE)
        {
            $response["message"] = "Se eliminó con éxito el Movimiento";
            $response["result"]  = TRUE;
        }
            
        else
        {
            $response["message"] = "No se puedo eliminar el Movimiento"; 
            $response["result"]  = FALSE;
        }
                   
        
        echo json_encode($response);
    }    

    public function EditarAction()    
    {
        if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["MovimientoController_ActualizarAction"] = TRUE;             
        }        
        $response = Array();
        
        if(isset($_GET["id_busca"]))
        {          
            //$campo = $this->extratcRUT( $_GET["rut_busca"] );
            $campo = $_GET["id_busca"];
            
            $objeto = new Movimiento();
            $datos = $objeto ->getBy('id', $campo );

            /*
            ob_start();
            var_dump($datos);
            $aux = ob_get_clean();
            exit($aux);
            */
            if(count($datos)==0)
            {
                $response["message"] = "El Movimiento buscado no existe. Intente nuevamente con un Código diferente"; 
                $response["state"]   = "false";
            }  
            else 
            {
                $response["message"]   = "Se encontró el Movimiento. Utilice el formulario para editar sus datos.";
                $response["state"]     = "true";
                
                $datos        = $datos[0]; 
                //$datos["rut"] = $datos["rut"].$this->dv($datos["rut"]);
                //$datos["rut"] = $this->rutFormato($datos["rut"]);
                $datos["fecha_movimiento"] = twistDate($datos["fecha_movimiento"]);
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
                
                $Movimiento_has_Producto = new Movimiento_has_Producto();
                $producto_lista = $Movimiento_has_Producto ->getBy('movimiento_de_stock_id', $datos["id"] );                
                //var_dump($producto_lista);
                $response["productos"] = $producto_lista;
            }
            //$result= $result[0];
        }else
        {
            $response["message"] = "(is not set) Ingrese un Código para buscar"; 
            $response["state"]   = "emptyForm";            
        }
        
        $this -> view
        ( 
                "buscaEditaMovimiento", // Plantilla 
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
            $_SESSION["MovimientoController_ActualizarAction"] = TRUE;             
        }
        */
        //var_dump($_POST);
        if (isset ( $_SESSION["MovimientoController_ActualizarAction"] ) )
        {
            if($_SESSION["MovimientoController_ActualizarAction"] === FALSE)
            {
                //$_POST = [];
                //echo count($_POST);
                header('Location: index.php?controller=movimiento&action=editar&id_busca='.$_POST["id"]);
                //header('Location: index.php?controller=movimiento&action=actualizar');
                //header('Location: index.php?controller=movimiento&action=editar');
                exit;
            }  
        }    
        if( isset(  $_POST["id"])  )
        {
            $Movimiento_id = $_POST["id"];
            $objeto = new Movimiento();
            
            $objeto->setId( $Movimiento_id );
            $objeto->setFecha_movimiento(MySQLDateFormat($_POST["fecha_movimiento"]));
            $objeto->setBodega_id_entra($_POST["bodega_id_entra"]);
            $objeto->setBodega_id_sale($_POST["bodega_id_sale"]);
        
            $result = $objeto->update(); 
            $_SESSION["MovimientoController_ActualizarAction"] = FALSE; 
            
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
                        {
                            $productos_aux["cantidad"] = $valor;
                            $productos[] = $productos_aux;
                            $productos_aux = [];
                        }
                    }
                }
                //var_dump($productos);
                //exit();
                
                $Movimiento_has_Producto = new Movimiento_has_Producto();
                
                $eliminar = $Movimiento_has_Producto -> deleteBy("movimiento_de_stock_id", $Movimiento_id);
                
                if($eliminar === TRUE)
                {
                    $prod_error = 0;
                    foreach($productos as $producto)
                    {

                        $Movimiento_has_Producto -> setMovimiento_de_stock_id( $Movimiento_id );
                        $Movimiento_has_Producto -> setProducto_id($producto["id"]);
                        $Movimiento_has_Producto -> setCantidad(extratcNumber($producto["cantidad"]));

                        $aux = FALSE;
                        $aux = $Movimiento_has_Producto -> save();

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
                    "buscaEditaMovimiento", // Plantilla 
                    $response
            );            
            
        }else
            //header('Location: index.php?controller=movimiento&action=editar');
            echo "Psot[] vacío.";
    }
    
}
