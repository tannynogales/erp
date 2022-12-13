<?php
class ProductoController extends BasisController
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
        $response["html"] = $producto->getAll(["codigo"]);  
          
        //var_dump($products);
        //$response["asdf"] = "holasdd";

        header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

        echo json_encode($response);
    }
    
    public function CrearAction()
    {
        /*
        $producto = new Producto();
        $result = $producto -> get
                ([
                    ["field"=>"habilitado","value"=>1],
                    ["field"=>"validado","value"=>1, "operator"=>"like"]
                ]);
        
        print_r($result); echo"<br>";
         
        $result2 = $producto -> update_construct
                ([
                    ["field"=>"habilitado","value"=>1],
                    ["field"=>"validado","value"=>1]
                ],
                [
                    ["field"=>"habilitado","value"=>1],
                    ["field"=>"validado","value"=>1, "operator"=>"like"]
                ]);
        print_r($result2);
        exit();
        */
        if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["ProductoController_CrearAction"] = TRUE;             
        }
 
        if($_SESSION["ProductoController_CrearAction"] === FALSE)
        {
            //$_POST = [];
            //echo count($_POST);
            header('Location: index.php?controller=producto&action=crear');
            exit;
        }
        //$bodega = new Bodega();
        //$bodegas = $bodega->getAll();
        //var_dump($bodegas);
        // Primero valido haber recibido las variables desde el formulario
        
        
        if( count($_POST) > 0)
        {
            //echo 'count($_POST): '.count($_POST)."<br>"; exit();
            //echo "- if > 0<br>";
            //foreach($_POST as $nombre_campo => $valor)
            //echo $asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
   
            $message = "";
            $state   = "false";  
                
            $objeto = new Producto();
            $objeto->setEmpresa_id( $_SESSION['empresa_id'] );
            $objeto->setCodigo( $_POST["codigo"] );
            $objeto->setNombre( $_POST["nombre"] );
            $objeto->setPrecio(  extratcNumber($_POST["precio"])  );
            $objeto->setPrecio_mayorista(  extratcNumber($_POST["precio_mayorista"])  );
            $objeto->setPrecio_web(  extratcNumber($_POST["precio_web"])  );        
            $objeto->setCosto(  extratcNumber($_POST["costo"])  );
            $objeto->setDescripcion_larga( $_POST["descripcion_larga"] );
            $objeto->setMuestra_precio( $_POST["muestra_precio"] );
            $objeto->setHabilitado( $_POST["habilitado"] );
            $objeto->setDestacado( $_POST["destacado"]);
            $objeto->setExento( $_POST["exento"]);
            $objeto->setDescripcion_corta( $_POST["descripcion_corta"]);
          
            if ($this->rol(2))
                $objeto->setValidado(TRUE);
            else
                 $objeto->setValidado(FALSE);
            
            //echo "voy a guardar";
            $objeto_id = $objeto -> save();
            if($objeto_id !== FALSE)
            {
            //echo "limpiar";
            //$_POST = [];
            $state   = "true";
            $message = "Se creó el Producto id: '".$objeto_id."'";
            }
            /*
            * Con el objeto de evitar se ejecute esta acción al refrescar 
            * la página y así guardar registros duplicados
            */
            $_SESSION["ProductoController_CrearAction"] = FALSE; 
        }
        else
        {
            $message = "Completa el siguiente formulario para crear un nuevo Producto";
            $state = "emptyForm";               
        } // end else
        
        $this -> view
        ( 
            "crearProducto", // Plantilla 
            [
                "message" => $message,
                "state"   => "$state"
            ]
        );          
    } 
    public function VerAction()//$create_result="")
    {
        $Producto_Validado = new Producto_Validado();
        $producto_validado_all = $Producto_Validado ->getAll();
        
        //Creamos el objeto del Modelo "usuario"
        $objeto = new Producto();
        
        //Conseguimos todos los usuarios
        //$datos = $objeto->getAll(FALSE, [["campo"=>"nombre", "order"=>"ASC",]]);
        $datos = $objeto->getBy("eliminado", 0, [["campo"=>"nombre", "order"=>"ASC",]]);
        
        foreach ($datos as &$value) 
        {
            $value["precio"]  = number_format($value["precio"], 0, '.', ',');
            $value["precio_mayorista"]   = number_format($value["precio_mayorista"], 0, '.', ',');
            $value["precio_web"]   = number_format($value["precio_web"], 0, '.', ',');
            $value["costo"]   = number_format($value["costo"], 0, '.', ',');
        }

        $this -> view
        ( 
            "verProducto", // Plantilla 
            [
                "datos" => $datos,
                "producto_validado_all" => $producto_validado_all
            ]
        );
    }    
    
    public function EliminarAction()
    {

        $response["message"] = "";
        
        if ( !isset( $_POST["id"] ) ||  !isset( $_POST["codigo"] ) ) 
        {
            $response["message"] = "Variables Post no seteadas.<br>Inténtelo nuevamente o comuníquese con el Administrador."; 
            $response["result"]  = FALSE;            
        }
        else
        {
            $id     = $_POST["id"];
            $codigo = $_POST["codigo"];           

            
            // Validar que el producto no este en una venta electrónica
            $Venta_has_Producto = new Venta_has_Producto();
            $venta_prod = $Venta_has_Producto -> getBy("producto_id", $id);

            // Validar que el producto no este en una venta electrónica
            $DTE_has_Producto = new DTE_has_Producto();
            $dte_prod = $DTE_has_Producto -> getBy("producto_id", $id); 

            //var_dump($venta_prod);
            //var_dump($dte_prod);            
            
            //if (count($dte_prod)!=0 || count($venta_prod)!=0 )
            if (count($dte_prod)!=0 || count($venta_prod) != 0)
            {                
                $objeto = new Producto();
                $objeto->setId($id);
                $eliminado = $objeto -> updateEliminado(TRUE); 
                 if ($eliminado === TRUE)
                {
                    $response["message"] = "Se eliminó el Producto '$codigo'. (Se actualizó el atributo).";
                    $response["result"]  = TRUE;
                }

                else
                {
                    $response["message"] = "No se puedo eliminar el Producto '$codigo'. (Actualizar el atributo)."; 
                    $response["result"]  = FALSE;
                }          
                if (count($dte_prod)!=0 && count($venta_prod) != 0 )
                {
                    $response["message"] .= "<br><br>Existen ventas (DTE's y SB) que lo utilizan.";
                }
                elseif (count($dte_prod)!=0)
                {
                    $response["message"] .= "<br><br>Existen DTE's que lo utilizan.";
                }
                elseif ( count($venta_prod) != 0 )
                {
                    $response["message"] .= "<br><br>Existen ventas que lo utilizan.";
                }                
            }
            else
            {
                $objeto = new Producto();

                $response = Array();

                $eliminar = $objeto -> deleteBy("id", "'$id'"); 
                //$eliminar = TRUE;

                if ($eliminar === TRUE)
                {
                    $response["message"] = "Se eliminó con éxito el Producto '$codigo'";
                    $response["result"]  = TRUE;
                }

                else
                {
                    $response["message"] = "No se puedo eliminar el Producto '$codigo'"; 
                    $response["result"]  = FALSE;
                }
            }    

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
            $_SESSION["ProductoController_ActualizarAction"] = TRUE;             
        }        
        $response = Array();
        
        if(isset($_GET["id_busca"]))
        {
            //$campo = $this->extratcRUT( $_GET["rut_busca"] );
            $campo = $_GET["id_busca"];
            
            $objeto = new Producto();
            $datos = $objeto ->getBy('codigo', $campo );

            /*
            ob_start();
            var_dump($datos);
            $aux = ob_get_clean();
            exit($aux);
            */
            if(count($datos)==0)
            {
                $response["message"] = "el Producto buscado no existe. Intente nuevamente con un Código diferente"; 
                $response["state"]   = "false";
            }  
            else 
            {
                $response["message"]   = "Se encontró el Producto. Utilice el formulario para editar sus datos.";
                $response["state"]     = "true";

                $datos        = $datos[0]; 
                $datos["precio"]  = "$ ".number_format($datos["precio"], 0, '.', ',');
                $datos["costo"]   = "$ ".number_format($datos["costo"], 0, '.', ',');
                $response["datos"]   = $datos;
                
                $Producto_Validado = new Producto_Validado();
                $producto_validado_all = $Producto_Validado ->getAll();
                $response["producto_validado_all"] = $producto_validado_all;
            }
        }else
        {
            $response["message"] = "(is not set) Ingrese un Código para buscar"; 
            $response["state"]   = "emptyForm";            
        }
        
        $this -> view
        ( 
                "buscaEditaProducto", // Plantilla 
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
        if (isset ( $_SESSION["ProductoController_ActualizarAction"] ) )
        {
            if($_SESSION["ProductoController_ActualizarAction"] === FALSE)
            {
                //$_POST = [];
                //echo count($_POST);
                header('Location: index.php?controller=producto&action=editar&id_busca='.$_POST["id"]);
                //header('Location: index.php?controller=movimiento&action=actualizar');
                //header('Location: index.php?controller=movimiento&action=editar');
                exit;
            }  
        }    
        if( isset(  $_POST["id"])  )
        {
            $objeto_id = $_POST["id"];
            
            $objeto = new Producto();
            $objeto->setId( $objeto_id );
            $objeto->setEmpresa_id( $_SESSION['empresa_id'] );
            $objeto->setCodigo( $_POST["codigo"] );
            $objeto->setNombre( $_POST["nombre"] );
            $objeto->setPrecio(  extratcNumber($_POST["precio"])  );
            $objeto->setPrecio_mayorista(   extratcNumber($_POST["precio_mayorista"])   );
            $objeto->setPrecio_web(  extratcNumber($_POST["precio_web"])  );
            $objeto->setCosto(  extratcNumber($_POST["costo"])  );
            $objeto->setDescripcion_larga( $_POST["descripcion_larga"] );
            $objeto->setMuestra_precio( $_POST["muestra_precio"] );
            $objeto->setHabilitado( $_POST["habilitado"] );
            $objeto->setDestacado( $_POST["destacado"]);
            $objeto->setExento( $_POST["exento"]);
            $objeto->setDescripcion_corta( $_POST["descripcion_corta"]);
            
            $objeto->setValidado( $_POST["validado"]);
            /*
            if ($this->rol(2))
                $objeto->setValidado(TRUE);
            else
                 $objeto->setValidado(FALSE);
            */
            $result = $objeto->update(); 
            $_SESSION["ProductoController_ActualizarAction"] = FALSE; 
            
            if($result === TRUE)
            {
                $response["message"] = "Se han actualizado con éxito los valores"; 
                $response["state"]   = "trueEdit"; 
            }
            else
            {
                $response["message"] = "No se logró actualizar los valores"; 
                $response["state"]   = "falseEdit";                     
            }

            $this -> view
            ( 
                    "buscaEditaProducto", // Plantilla 
                    $response
            );            
            
        }else
            //header('Location: index.php?controller=movimiento&action=editar');
            echo "Post[] vacío.";
    }
       
}
