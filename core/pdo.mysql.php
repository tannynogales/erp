<?php
class Database{
    public static $conn = FALSE;
    
    private $driver;
    private $host;
    private $user;
    private $pass;
    private $database;
    private $charset;
/*
    "driver"    =>"mysql",
    "host"      =>"localhost",
    "user"      =>"root",
    "pass"      =>"",
    "database"  =>"pruebas",
    "charset"   =>"utf8"
 *  */    
    
    function __construct() {
        $db_cfg = require_once 'config/database.php';
        
        $this->driver=$db_cfg["driver"];
        $this->host=$db_cfg["host"];
        $this->user=$db_cfg["user"];
        $this->pass=$db_cfg["pass"];
        $this->database=$db_cfg["database"];
        $this->charset=$db_cfg["charset"];
        
        self::$conn = $this->conect();
    }
    
    function getConn() {
	return self::$conn;
    }
    
    function connect() {
        /* Conectar a una base de datos invocando al controlador PDO_mysql*/
	$dsn = 'mysql:dbname=neges_base;host=127.0.0.1';
	$usuario = 'tanny';
	$contraseña = '1111';
		
	try {
            return new PDO($dsn, $usuario, $contraseña);
        }
        catch (PDOException $e) {
            return 'Falló la conexión: ' . $e->getMessage();
        }	
    }	
		
    public function query($sql, $retorno_de_datos = TRUE) {
        $conPDO = $this->getConn();
	if ($retorno_de_datos !== TRUE) 
        {
            $responce = $conPDO->exec($sql);
            if ($responce !== FALSE)
				return TRUE;
			else
				{
					$this->error_handler($sql);
					return FALSE;
				}
        }
        else
        {
			$responce = $conPDO->query($sql);
			if ($responce !== FALSE)
				return $this->fetch_array($responce, $retorno_de_datos);
			else
				{
					return FALSE;	
					$this->error_handler($sql);
				}
	}
	}	

	function fetch_array($query, $retorno_de_datos = TRUE)
	{
		
		$datos = Array();
		while( $fila = $query->fetch(PDO::FETCH_ASSOC) ){//FETCH_BOTH 
		   $datos[] = $fila;
		}		
		return $datos;
	}	
	
	function MySQLDateFormat($dato)
	{
		//exit($dato);
		$result = "";
		$fecha = trim($dato);
		if ( $fecha[4]=="-" || $fecha[4]=="/" )
		{
			$result = $fecha;
		}
		else {
			$result = twistDate($fecha);
		}
		//var_dump($result); echo "<br>";
		return $result;
	}	
	function Array2Utf8($array)
	{
		if ($array === FALSE) // adaptado porque yo en las consultas puedo retornar 
		return FALSE;
				  // falso si no encuentra nada
	    foreach($array as $key_fila => &$fila)
		{
			if (is_array($fila))
			{
			    foreach($fila as $key_celda => &$celda)
				{
					if(!mb_check_encoding($celda, 'UTF-8'))
					$celda = utf8_encode($celda);
				}
			}
			else
			{
				if(!mb_check_encoding($fila, 'UTF-8'))
				$fila = utf8_encode($fila);				
			}	 
	    }
	    return $array;
	} 		
}