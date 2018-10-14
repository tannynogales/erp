<?php
// Notificar todos los errores de PHP
error_reporting(0);

require_once('../../lib/nusoap-php7/class.nusoap_base.php');

    $personas = array();
    $personas[1] =  array('cedula' => '123A', 'nombre' => 'Juan', 'FechaNacimiento'   => 1965);
    $personas[2] =  array('cedula' => '123B', 'nombre' => 'Marcos',   'FechaNacimiento'   => 1980);
    $personas[3] =  array('cedula' => '123C', 'nombre' => 'Pedro',    'FechaNacimiento'   => 1990);
    $personas[4] =  array('cedula' => '123D', 'nombre' => 'Ana',  'FechaNacimiento'   => 1995);
    $personas[5] =  array('cedula' => '123F', 'nombre' => 'Maria',    'FechaNacimiento'   => 1972);
     
     
    $cliente = new nusoap_client('http://127.0.0.1/ERP/services/test/server2.php');
    //var_dump($cliente);
     
    $datos_persona_entrada = array( "datos_persona_entrada" => $personas);
	
    $resultado = $cliente->call('calculo_edades_b', $datos_persona_entrada);
     
    print_r($resultado);