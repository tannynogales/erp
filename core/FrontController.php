<?php
session_start();
class FrontController{
    
    
    public function __construct() 
    {
        require_once 'lib/dte_client.php';
        //$dte_client = new dte_client();
        
        require_once 'lib/library_php.php';
        
        //Configuración Global
        require_once 'config/global.php';
        
        //Base para los controladores
        require_once 'core/BasisController.php';
        /*
        require_once 'lib/khipu_client/autoload.php';
        echo $authorization = "'HTTP Authorization:".$this->Calculo_del_HASH()."'";
        echo "<br>";
        header ($authorization);
        $this->khipu();
        */
        
        //Cargamos el Controlador y la Acción desde el Get[]
        //var_dump($_GET["action"]);
        if(  !isset($_GET["controller"])  )
            $controllerName = DEFAULT_CONTROLLER;
        else
            $controllerName = ucwords($_GET["controller"])."Controller";

        if(  !isset($_GET["action"])  )
            $actionName = DEFAULT_ACTION;
        else
            $actionName = ucwords($_GET["action"])."Action";
        
        //Conexión a la base de datos
        require_once 'core/MyPDO.php';
        // Inicializo la clase MyPDO, asegurando una única instancia 
        MyPDO::singleton(require_once 'config/database.php');
        
        // Incluir todos los modelos
        foreach(glob("model/*.php") as $file)
            require_once $file;
                
        // Verificamos que exista una Session
        require_once 'session.php';
        $session = new session();
        // Verificamos que exista creada una Sesión
        if( !$session->isLog() )
        {
            //echo "Is not login<br>";
            if( $controllerName == "IndexController" && $actionName == "Try2logAction")
            {
                //echo "Trying to login";
                //echo $_POST["rememberMe"];
                $controllerName = DEFAULT_CONTROLLER; // 'LogInController';
                $actionName     = 'Try2logAction';                
            }
            else
            {
                //echo "Login";
                //echo $_POST["rememberMe"];
                $controllerName = DEFAULT_CONTROLLER; // 'LogInController';
                $actionName     = 'LoginAction';                 
            }
        }
        
        $controllerPath = CONTROLLERS_FOLDER . '/' . $controllerName . '.php';
        
        //Incluimos el fichero que contiene nuestra clase controladora solicitada
        if(is_file($controllerPath))
        {
            require $controllerPath;
        }
        else
              die('El controlador ('.$controllerPath.') no existe - 404 not found');
        
        if (class_exists($controllerName))
        {
            //echo "clase '$controllerName' existe";
            $controller = new $controllerName();
            
            if ( method_exists($controller, $actionName) )
            {
                //echo "La Acción '$actionName' de la clase '$controllerName' existe";
                /*
                // Incluir todos los modelos
                foreach(glob("model/*.php") as $file)
                {
                    require_once $file;
                } 
                */               
                $controller->$actionName();
                
            }
            else
            {
                echo ("NO EXISTE la Acción '$actionName' de la clase '$controllerName' (E_USER_NOTICE: ". E_USER_NOTICE.")");
            }
        }
        else
        {
            die ("La Clase del Controlador '$controllerName' NO EXISTE");
        }
    }
    
      
}