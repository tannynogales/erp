<?php
class Caja_movimientoController extends BasisController
{
    
    public function __construct() {
        //parent::__construct();
    }

        
    public function CrearAction()
    {
        //var_dump( $_SESSION["Caja_movimientoController_CrearAction"] );
        if( count($_POST) == 0 )
        {
            /*
            * Permito que se ejecute de nuevo el formulario sólo si 
             * este viene vacio
            */
            $_SESSION["Caja_movimientoController_CrearAction"] = TRUE;             
        }
 
        if($_SESSION["Caja_movimientoController_CrearAction"] === FALSE)
        {
            header('Location: index.php?controller=Caja_movimiento&action=crear');
            exit;
        }
        
        $usuario = new Usuario();
        $usuarios = $usuario->getAll();
        
        if( count($_POST) > 0)
        {
            if( count($_POST) >= 3)
            {
                $message = "";
                $state   = "false";  
                
                $objeto = new Caja_Movimientos();
                
                $objeto->setMovimiento_fecha( MySQLDateFormat($_POST["movimiento_fecha"]) );    
                $objeto->setMovimiento_registro( date("Y/m/d") );
                $objeto->setMovimiento_usuario_id( $_SESSION["id"] );
                $objeto->setCaja_usuario_id( $_POST["caja_usuario_id"] );
                                
                $monto = extratcNumber($_POST["monto"]) * $_POST["movimiento_tipo"];
                
                $objeto->setMonto(  $monto  );

                $objeto_id = $objeto -> save();
                
                if($objeto_id !== FALSE)
                {
                    //echo "limpiar";
                    //$_POST = [];
                    $state   = "true";
                    $message .= "Se guardó con éxito el movimiento $objeto_id, con monto $monto";
                }
                else 
                {
                    $state   = "true";
                    $message .= "No se guardó con éxito el movimiento $objeto_id, con monto $monto";                    
                }                
     
                
                
                /*
                 * Con el objeto de evitar se ejecute esta acción al refrescar 
                 * la página y así guardar registros duplicados
                 */
                $_SESSION["Caja_movimientoController_CrearAction"] = FALSE; 
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
            "crear_caja_movimiento", // Plantilla 
            [
                "usuarios" => $usuarios,
                "message" => $message,
                "state"   => "$state"
            ]
        );          
    } 
    public function VerAction()
    {
        $objeto = new Caja_Movimientos();
        
        $datos = $objeto->getCaja();
        
        foreach ($datos as &$value) 
        {
            $value["monto_suma"]  = number_format($value["monto_suma"], 0, '.', ',');
        }

        $this -> view
        ( 
            "ver_caja_movimiento", // Plantilla 
            [
                "datos" => $datos
            ]
        );
    }   
    
    public function DetalleAction()
    {
        $objeto = new Caja_Movimientos();
        
        $datos = $objeto->getDetalle($_GET["usuario_id"]);
        
        foreach ($datos as &$value) 
        {
            $value["monto"]             = number_format($value["monto"], 0, '.', ',');
            $value["movimiento_fecha"]  = twistDate($value["movimiento_fecha"]);
        }

        $this -> view
        ( 
            "ver_caja_detalle", // Plantilla 
            [
                "datos" => $datos
            ]
        );
    }
    
    public function EliminarAction()
    {
        $id = $_POST["id"];
        $datos = new Caja_Movimientos();
        
        $response = Array();
        
        $eliminar = $datos -> deleteBy("caja_usuario_id", $id); 
        //$eliminar = TRUE;
        
        if ($eliminar === TRUE)
        {
            $response["message"] = "Se cerró con éxito la caja";
            $response["result"]  = TRUE;
        }
            
        else
        {
            $response["message"] = "No se puedo cerrar la caja"; 
            $response["result"]  = FALSE;
        }
                   
        
        echo json_encode($response);
    }    
    
}
