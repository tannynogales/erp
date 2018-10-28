<?php
class BasisController{

    public function __construct() {
	//require_once 'Conectar.php';
        //require_once 'EntidadBase.php';
        //require_once 'ModeloBase.php';

        //Incluir todos los modelos
        /*
        foreach(glob("model/*.php") as $file){
            require_once $file;
        }
         */
    }
    /*
     *  gat -> Get Ajax Template
     *  Es una mejora de ->view(), que permite retornar y no "echo" el template
     *  renderizado
     */
    public function gat($view, $data=Array(), $optional = Array() )
    {
        if ( !isset( $optional["template"] )) $path = strtolower ($view).'/'.strtolower ( $view ).'View.twig';
        else                                  $path = strtolower ($view).'/'.strtolower ( $optional["template"] ).'View.twig';
        //echo $path;
        /* 
         * Twig uses a loader (Twig_Loader_Array) 
         * to locate templates,
         */
        $loader = new Twig_Loader_Filesystem('view');  

        /*
         * Twig uses an environment (Twig_Environment) 
         * to store the configuration.
         * 
         * Segundo parámetro: array('cache' => 'cache')
         * para guardar configuración en cache
         */
        //$twig = new Twig_Environment($loader);  
        
        $twig = new Twig_Environment($loader, array(
            'debug' => true,
            // ...
        ));
        $twig->addExtension(new Twig_Extension_Debug());
        //load template file  
        try 
        {
            $template = $twig->loadTemplate(   $path   );  
        } 
        catch (Exception $ex) 
        {
            exit ($ex->getMessage() );
        }
        
        if ( !isset( $optional["render"] )) $optional["render"] = TRUE;
        
        //render a template 
        if ( $optional["render"] === TRUE )
            echo $template->render($data);
        else
            return $template->render($data);
    }

    public function view($vista, $datos=Array())
    {
        $datos['get'] = $_GET;
        
        if(isset($_SESSION["roles"]))
        {
            $datos['roles'] = $_SESSION["roles"];
            foreach ($_SESSION["roles"] as $clave => $valor) 
            {
                $datos['rol_'.$valor["rol_id"]] = TRUE;
                //print_r ($valor);
                if($valor["rol_id"] == 2)
                {
                    //$datos['rol_2'] = TRUE;
                }
            }            
        }
        else 
        {
            $datos['roles'] = FALSE;
            $datos['rol_1'] = FALSE;
            $datos['rol_2'] = FALSE;
            $datos['rol_3'] = FALSE;
        }

        /* 
         * Twig uses a loader (Twig_Loader_Array) 
         * to locate templates,
         */
        $loader = new Twig_Loader_Filesystem('view');  

        /*
         * Twig uses an environment (Twig_Environment) 
         * to store the configuration.
         * 
         * Segundo parámetro: array('cache' => 'cache')
         * para guardar configuración en cache
         */
        //$twig = new Twig_Environment($loader);  
        $twig = new Twig_Environment($loader, array(
            'debug' => true,
            // ...
        ));
        $twig->addExtension(new Twig_Extension_Debug());
        //load template file  
        try 
        {
            //echo ucwords(strtolower ($vista)).'/'.ucwords (strtolower ($vista)).'View.twig';
            //exit;
            $template = $twig->loadTemplate( strtolower ($vista).'/'. strtolower ($vista).'View.twig');  
        } 
        catch (Exception $ex) 
        {
            exit ($ex->getMessage() );
        }
        //render a template 
        echo $template->render($datos); 
    }
    
    public function redirect($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO)
    {
        header("Location:index.php?controller=".$controlador."&action=".$accion);
    }

    function extratcRUT($cadena="17.327.515-3")
    {
        // Devuelve una string con todos los caracteres alfabéticos 
        // convertidos a minúsculas.
        $cadena = strtolower($cadena)."";
        // Quito el último valor, es decir, quito el digito verificador 
        $cadena = substr($cadena, 0, -1);
        $rut = "";
        // strlen() devuelve la longitud del string dado.
        for( $index = 0; $index < strlen($cadena); $index++ )
        {
            if( is_numeric($cadena[$index]) )
            {
                $rut .= $cadena[$index];
            }
        }
            return $rut;  
    }
    function dv($rut)
    {
            // genera el digito verificador chileno
            if ( $rut > 0)
            {
                    $d = 1;
                    for ($x = 0; $rut != 0; $rut /= 10)
                            $d = ($d + $rut % 10 * (9 - $x++ % 6)) % 11;
                    return chr($d ? $d + 47 : 75);
            } 
            else
            {
                    return false;
            }
    }
    
    function rutFormato($rut_param)
    {
        $parte4 = substr($rut_param, -1); // seria solo el numero verificador 
        $parte3 = substr($rut_param, -4,3); // la cuenta va de derecha a izq  
        $parte2 = substr($rut_param, -7,3);  
        $parte1 = substr($rut_param, 0,-7); ; //de esta manera toma todos los caracteres desde el 8 hacia la izq 

        return $parte1.".".$parte2.".".$parte3."-".$parte4; 
    }
    function rol($rol, $rol_key = "rol_id")
    {
        $doExist = FALSE;
        foreach ($_SESSION["roles"] as $value) 
        {
            if($value[$rol_key] == $rol)
            {
                $doExist = TRUE; 
            }
        }
        return $doExist;
    }
}

