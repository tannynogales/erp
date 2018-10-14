<?php

$padre = Padre::singleton();
echo Padre::$var_padre."<br>";

$hijo_a  = new Hijo();
$hijo_b  = new Hijo();

echo Padre::$var_padre."<br>";

class Padre
{
    public static $instance;
    public static $var_padre = 0;
 
    function __construct()
    {
        self::$var_padre ++;
    }
    
    public static function singleton()
    {
        if( self::$instance == null )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }    
}

class Hijo extends Padre
{
    public static $instance;
 
    function __construct()
    {
        parent::$var_padre ++;
        //echo Padre::$var_padre."<br>";
    }
}