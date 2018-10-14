<?php
session_start();
class FrontController{
    
    public function addLibrary()
    {
        
    }
    
    public function __construct() 
    {
        //Gestor de plantillas PHP Twig
        // require_once 'lib/twig/Autoloader.php';
        // Twig_Autoloader::register(); //clase:metodo
        
        require_once 'lib/library_php.php';
        
        require_once 'lib/DTE.php';
        
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
    
    function Calculo_del_HASH()
    {
        $receiver_id = 104944;
        $secret  = '5839b88aa6eb5734943272f8064c68b74d144ceae';
        $method = 'POST';
        $url = 'https://khipu.com/api/2.0/payments';

        $params = array(
            'subject' => 'ejemplo de compra'
            , 'amount' => '1000'
            , 'currency' => 'CLP'
        );

        $keys = array_keys($params);
        sort($keys);

        $toSign = "$method&" . rawurlencode($url);
        foreach ($keys as $key) {
                $toSign .= "&" . rawurlencode($key) . "=" . rawurlencode($params[$key]);
        }
        $hash = hash_hmac('sha256', $toSign , $secret);
        $value = "$receiver_id:$hash";
        return $value;        
    }
    
    function khipu()
    {
        // Debemos conocer el $receiverId y el $secretKey de ante mano.
        $receiverId = 104944;
        $secretKey  = '5839b88aa6eb5734943272f8064c68b74d144ceae';

        $configuration = new Khipu\Configuration();
        $configuration->setReceiverId($receiverId);
        $configuration->setSecret($secretKey);
        // $configuration->setDebug(true);
        //var_dump($configuration);

        $client = new Khipu\ApiClient($configuration);
        $payments = new Khipu\Client\PaymentsApi($client);
        //var_dump($payments);

        try 
        {
            $opts = array
                (
                "transaction_id" => "MTI-100",
                "return_url" => "http://mi-ecomerce.com/backend/return",
                "cancel_url" => "http://mi-ecomerce.com/backend/cancel",
                "picture_url" => "http://mi-ecomerce.com/pictures/foto-producto.jpg",
                "notify_url" => "http://mi-ecomerce.com/backend/notify",
                "notify_api_version" => "1.3"
            );

            $response = $payments->paymentsPost
                (
                        "Compra de prueba de la API" //Motivo de la compra
                , "CLP" //Moneda
                , 100.0 //Monto
                //, $opts //campos opcionales
                );

                var_dump($response);
                echo ("try");
        } 
        catch (\Khipu\ApiException $e) 
        {
                print_r( $e);
                echo ("hola_catch");
            //echo print_r($e->getResponseBody(), TRUE);
        }
        
    }
    
}