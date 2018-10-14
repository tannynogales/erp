<?php
class UsuariosController extends BasisController{
    public function __construct() {
        //parent::__construct();
    }

    public function VerAction()//$create_result="")
    {
        //Creamos el objeto del Modelo "usuario"
        $usuario = new Usuario();
        
        //Conseguimos todos los usuarios
        $allusers = $usuario->getAll();
        
        foreach ($allusers as &$value) 
            $value["rut"] = $this->rutFormato($value["rut"].$this->dv($value["rut"]));

        $this -> view
        ( 
            "verUsuario", // Plantilla 
            [
                "users" => $allusers
            ]
        );
    }
        
    public function crearAction()
    {
        // Primero valido haber recibido las variables desde el formulario
        if(
                isset($_POST["nombre"])   && 
                isset($_POST["rut"])      && 
                isset($_POST["apellido"]) && 
                isset($_POST["email"])    && 
                isset($_POST["password"]) )
        {
            $usuario = new Usuario();
            
            $usuario->setRut($this->extratcRUT($_POST["rut"]));
            $usuario->setNombre($_POST["nombre"]);
            $usuario->setApellido($_POST["apellido"]);
            $usuario->setAMaterno($_POST["aMaterno"]);
            $usuario->setEmail($_POST["email"]);
            //$usuario->setPassword(sha1($_POST["password"]));
            $usuario->setPassword($_POST["password"]);
        
            $result = $usuario->save();
            
            if ($result === TRUE)
            {
                $message = "Si se guardó con éxito el Rut: ".$this->extratcRUT($_POST["rut"]);
                $state   = "true";
            }
            elseif($result === FALSE)
            {
                $message = "No se guardó (Rut: ".$this->extratcRUT($_POST["rut"]).", Nombre: ".$_POST["nombre"].")";
                $state   = "false";
            }
            else
            {
                $message = "Variable RESULT no definida."; 
                $state = "other";
            }            
        }
        else
        {
            $message = "";
            $state = "emptyForm";           
        } // end else
        
        $this -> view
        ( 
            "crearUsuario", // Plantilla 
            [
                "message" => $message,
                "state"   => "$state"
            ]
        );          
    }
    
    public function ActualizarAction()
    {
      if(
                isset($_POST["nombre"])   && 
                isset($_POST["rut"])      && 
                isset($_POST["aPaterno"]) && 
                isset($_POST["email"])    && 
                isset($_POST["password"]) )
        {
            $usuario = new Usuario();
            
            $usuario->setId($_POST["id"]);
            $usuario->setRut($this->extratcRUT($_POST["rut"]));
            $usuario->setNombre($_POST["nombre"]);
            $usuario->setApellido($_POST["aPaterno"]);
            $usuario->setAMaterno($_POST["aMaterno"]);
            $usuario->setEmail($_POST["email"]);
            $usuario->setPassword($_POST["password"]);
        
            $result = $usuario->update(); 
            //$result = true;
            if($result === TRUE)
            {
                $response["message"] = "Se ha actualizado con éxito los valores"; 
                $response["state"]   = "trueEdit";     
            }
            else
            {
                $response["message"] = "No se logró actualizar los valores"; 
                $response["state"]   = "falseEdit";                     
            }

            $this -> view
            ( 
                    "buscaEditaUsuario", // Plantilla 
                    $response
            );            
            
        }        
    }

    public function EliminarAction()
    {
        $id = $_POST["id"];
        $usuario = new Usuario();
        
        $response = Array();
        
        $eliminar = $usuario -> deleteBy("id", $id); 
        //$eliminar = TRUE;
        
        if ($eliminar === TRUE)
        {
            $response["message"] = "Se eliminó con éxito el Usuario";
            $response["result"]  = TRUE;
        }
            
        else
        {
            $response["message"] = "No se puedo eliminar el Usuario"; 
            $response["result"]  = FALSE;
        }
                   
        
        echo json_encode($response);
    }
    
    public function EditarAction()    
    {
        $response = Array();
        
        if(isset($_GET["rut_busca"]))
        {          
            //exit();
            //$rut = $this->extratcRUT($_GET["rut"]);
            //$rut = $_GET["rut"];
            //$rut = (int)$rut;
            
            $campo = $this->extratcRUT( $_GET["rut_busca"] );
            
            $usuario = new Usuario();
            $usuarios_lista = $usuario ->getBy('rut', $campo );
            
            //echo $result[0]["nombre"];
            /*
            ob_start();
            var_dump($result);
            $aux = ob_get_clean();
            exit($aux);
            */
            if(count($usuarios_lista)==0)
            {
                $response["message"] = "El usuario buscado no existe. Intente nuevamente con un Rut diferente"; 
                $response["state"]   = "false";
            }  
            else 
            {
                $response["message"]   = "Se encontró el Usuario. Utilice el formulario para editar sus datos.";
                $response["state"]     = "true";
                
                $usuarios_lista        = $usuarios_lista[0]; 
                $usuarios_lista["rut"] = $usuarios_lista["rut"].$this->dv($usuarios_lista["rut"]);
                $usuarios_lista["rut"] = $this->rutFormato($usuarios_lista["rut"]);
                
                $response["usuario"]   = $usuarios_lista;
            }
            //$result= $result[0];
        }else
        {
            $response["message"] = "(is not set) Ingrese un Rut para buscar"; 
            $response["state"]   = "emptyForm";            
        }
        
        $this -> view
        ( 
                "buscaEditaUsuario", // Plantilla 
                $response
        );
    }    
}
?>
