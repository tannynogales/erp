<?php
class MyPDO extends PDO
{
    /*
     * Parámetro que permite implementar el patrón "singleton" y que 
     * guarda la instancia de la clase
     *      */ 
    public static $instance = null;
    
    public $estado = "hola";

    private $databaseDriver;  // por ejemplo, "mysql"
    private $databaseHost;    // por ejemplo, "localhost"
    private $databaseUser;    // por ejemplo, "root"
    private $databasePass;    // por ejemplo, ""
    private $databaseName;    // por ejemplo, "pruebas"
    private $databaseCharset; // por ejemplo, "utf8"
    
    public function __construct($db_cfg)
    {
        //$db_cfg = require_once 'config/database.php';
        $this->databaseDriver=$db_cfg["driver"];
        $this->databaseHost=$db_cfg["host"];
        $this->databaseUser=$db_cfg["user"];
        $this->databasePass=$db_cfg["pass"];
        $this->databaseName=$db_cfg["database"];
        $this->databaseCharset=$db_cfg["charset"];
	
        $dsn = $this->databaseDriver.':dbname='.$this->databaseName.';host='.$this->databaseHost;
                
        // Conectar a una base de datos invocando al controlador PDO_mysql
        try 
        {
            parent::__construct($dsn, $this->databaseUser, $this->databasePass);
            $this->connectionStatus = "Successful Connection";
        } 
        catch (PDOException $Exception) 
        {
            exit( "Failed Connection: ". $Exception->getMessage());
        }
    }
    public function consult ($sql) 
    {
        // Pregusto si la Query retorna datos
        $sqlReturn = $this -> sqlReturn($sql);
        
        $response = NULL;
        //$DATABASE_CONNECTOR = SPDO::singleton();
        $DATABASE_CONNECTOR = $this::$instance;
        //var_dump($DATABASE_CONNECTOR);
        
        if ($sqlReturn)
        {
            $response = $DATABASE_CONNECTOR -> query($sql);
            if ($response !== FALSE)
            {
               return $this->fetchArray($response); 
            }
            else
            {
                //echo "ERROR (si devuelve): $sql";
                return FALSE;
            }             
        }
        else
        {
            $response = $DATABASE_CONNECTOR -> exec($sql);
            if ($response !== FALSE)
            {
               return TRUE; 
            }
            else
            {
                //echo "ERROR (no devuelve): $sql<br>";
                return FALSE;
            }            
        }


    }
    
    function sqlReturn($sql)
    {
        $mystring = strtoupper($sql);
        //$mystring = substr($mystring, 7);
        
        $findme   = ['SELECT'];
        
        $doExist = false;
        foreach ($findme as $value) 
        {
            $pos = strpos($mystring, $value);
            if ($pos !== FALSE)
            {
                $doExist = TRUE;
            }
        }
        return $doExist;
    }
    public function fetchArray($array)
    {
        $datos = Array();
        while( $fila = $array -> fetch(PDO::FETCH_ASSOC) ) // FETCH_BOTH
        {
            $datos[] = $fila;
        }
        return $datos;        
    }
    
    public static function singleton($db_cfg)
    {
        if( self::$instance == null )
        {
            self::$instance = new self($db_cfg);
        }
        return self::$instance;
    }  
    
    /*
     * Funciones Genéricas de los modelos / Entidades
     */
    public function getAll($select = [], $order = [])
    {
        if ( count ($select) != 0 and $select !== FALSE)
        {
            $select_sql = "";
            $aux = 0;
            foreach ($select as $campo)
            {
                $aux = $aux + 1;
                $select_sql .= $campo;
                if( count($select) != $aux)
                {
                    $select_sql .=  ' , ';
                }
            }            
        }else
            $select_sql = "*";

        if ( count ($order) != 0)
        {
            $order_sql = "ORDER BY ";
            $aux = 0;
            foreach ($order as $value)
            {
                $aux = $aux + 1;
                $order_sql .= $value["campo"]." ".$value["order"];
                if( count($select) != $aux)
                {
                    $select_sql .=  ' , ';
                }
            }            
        }else
            $order_sql = "";
        
        $sql = "SELECT $select_sql FROM $this->table $order_sql";
        //exit($sql);
        $response = $this ->consult($sql);
        return $response;
    }
    public function getLike($valor, $where = [])
    {
        $where_sql = " WHERE ";
        $aux = 0;
        foreach ($where as $campo)
        {
            $aux = $aux + 1;
            $where_sql .= $campo.' LIKE "%'.$valor.'%"';
            if( count($where) != $aux)
            {
                $where_sql .=  ' OR ';
            }
        }
        //$sql = 'SELECT * FROM '.$this->table.' WHERE codigo LIKE "%'.$valor.'%" OR nombre LIKE "%'.$valor.'%"';
        $sql = 'SELECT * FROM '.$this->table.$where_sql;
        //exit($sql);
        $response = $this ->consult($sql);
        return $response;
    }
    public function getBy($fieldName, $value, $order = [])
    {
        if ( count ($order) != 0 and $order != FALSE )
        {
            $order_sql = "ORDER BY ";
            $aux = 0;
            foreach ($order as $val)
            {
                $aux = $aux + 1;
                $order_sql .= $val["campo"]." ".$val["order"];
                if( count($order) != $aux)
                {
                    $order_sql .=  ' , ';
                }
            }            
        }else
            $order_sql = "";
        
        $sql =
        "SELECT 
            *
        FROM 
            $this->table
        WHERE
            $fieldName = '$value'
        $order_sql;";
        
        $response = $this ->consult($sql);
        return $response;
    }
    
    function max($fieldName)
    {
        $sql =
        "SELECT 
            max($fieldName) as 'max'
        FROM 
            $this->table;";
        
        $response = $this ->consult($sql);
        return ($response[0]["max"]+0);
    }
    
    function deleteBy($fieldName, $value)
    {
        
        $sql = "DELETE FROM $this->table WHERE $fieldName = $value;";
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;        
    }  
    public function UpdateAttribute($unique_key_value, $attribute_name, $attribute_value, $Optional = ["unique_key_attribute"=>"id"])
    {
        $sql="
        UPDATE 
            $this->table
        SET
            `$attribute_name` = '$attribute_value'
        WHERE 
            `".$Optional["unique_key_attribute"]."` = '$unique_key_value'";

        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;        
    }    
    /*
    public function get($where = [], $logical_operators = "AND")
    {
        //
        // $where example:
        // [
        //      ["operator" => "=", "field"=>"estado", "value"=>0],
        //      ["operator" => "=", "field"=>"estado", "value"=>0],
        //  ]
        //
        $where_sql = $this->sentence_generator($where, $logical_operators, "WHERE");

        $sql = 'SELECT * FROM '.$this->table.$where_sql;
        $response = $this ->consult($sql);
        return $response;
    }    
    
    public function sentence_generator($where = [], $logical_operators = "AND", $action = "WHERE")
    {
        $logical_operators = strtoupper($logical_operators);
        
        $where_sql = "";
        $aux = 0;
        
        foreach ($where as $campo)
        {
            $aux = $aux + 1;
            if(!isset($campo["operator"]))
                $campo["operator"] = "=";
            else
                $campo["operator"] = strtoupper($campo["operator"]);
            
            if ($campo["operator"] == "=")
                $wrap = "";
            elseif ($campo["operator"] == "LIKE")
                $wrap = "%";
            
            $where_sql .= '`'.$campo["field"]."` ".$campo["operator"]." '".$wrap.$campo["value"].$wrap."'";
            if( count($where) != $aux)
            {
                $where_sql .=  " $logical_operators ";
            }
        }   
        
        if($where_sql != "")
            $where_sql = " $action ".$where_sql;
            
        return $where_sql;
    }
        
    public function update_construct($update, $where, $logical_operators = "AND")
    {
        $SET = $this->sentence_generator($update, ",", "SET");
        $WHERE  = $this->sentence_generator($where, $logical_operators, "WHERE");
        echo 
        $sql="
        UPDATE 
            $this->table
        $SET
        $WHERE";
        echo ($sql);
        //$response = $this ->consult($sql);
        //return $response;
    }
    */    
    public function getTable()
    {
        return $this->table;
    }     
}
