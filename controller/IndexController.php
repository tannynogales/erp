<?php
//use Usuario;

class IndexController extends BasisController
{
    public function __construct()
    {
        
    }
     
    public function HomepageAction()
    {     
        $venta = new Factura();
        $ventas = $venta -> ventas_by_mes();

        foreach ($ventas as &$value) 
        {
            $value["venta_neto"] = "$ ".number_format($value["venta_neto"], 0, '.', ',');
        } 
        
        $this -> view
        ( 
            "homepage", // Plantilla 
            [
                "title" => 'Titulo', 
                "ventas" => $ventas,
                "usuario_nombre" => $_SESSION["nombre"],
                "roles" => $_SESSION["roles"]
            ]
        );      
        
    }    
    public function LoginAction()
    {
        //$this -> redirect("usuario","index");
        //echo "Estas dentro de LoginAction()";
        $this -> view("login");
    }
    function logoutAction()
    {
        $session = new session();
        $session -> logOut();
        $this -> view
        ( 
            "Login", // Plantilla 
            [
                "message" => 'Sesión Cerrada',
                "state"   => 'ERROR'
            ]
        );           
    }
    
    function Try2logAction() 
    {
        //echo "En Try2logAction";
        if ( isset($_POST["username"]) && isset($_POST["password"]) )
        {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $usuario = new Usuario();
            $result = $usuario ->getBy('email', $username);
            //var_dump($result); exit();
            
            if (empty($result))
                $this -> view
                ( 
                        "Login", // Plantilla 
                        [
                            "message" => 'Usuario no encontrado',
                            "state"   => 'ERROR'
                        ]
                );                
            else
            {
                if ($result[0]["password"] === $password)
                {
                    //echo "Contraseña correcta";
                    $usuario_has_rol = new Usuario_has_Rol();
                    $roles = $usuario_has_rol ->getBy(  $result[0]["id"]  );
                    //var_dump($roles);

                    $session = new session();
                    $session -> setLog($result[0], $roles);
                    $this -> HomepageAction();
                }
                else
                    $this -> view
                    ( 
                            "Login", // Plantilla 
                            [
                                "message" => 'Contraseña incorrecta',
                                "state"   => 'ERROR'
                            ]
                    );                     
            }
        }else
        {
            $this -> view
            ( 
                "Login", // Plantilla 
                [
                    "message" => 'Variables POST sin setear',
                    "state"   => 'ERROR'
                ]
            );            
        }
    }
    
    function TablaejemploAction()
    {
        $this -> view
        ( 
            "tablaejemplo", // Plantilla 
            [
                "title" => 'Bootstrap'
            ]
        );         
    }
    
}