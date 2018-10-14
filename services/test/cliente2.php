<?php
// Desactivar toda notificación de error
error_reporting(0);

// Incluimos libreria
require_once('../../lib/nusoap-php7/class.nusoap_base.php');

$client = new nusoap_client('http://127.0.0.1/ERP/services/test/server2.php?wsdl', false); //'wsdl'
/*
$personas = array();
$personas[1] =  array('cedula' => '123A', 'nombre' => 'Juan', 'FechaNacimiento'   => 1965);
$personas[2] =  array('cedula' => '123B', 'nombre' => 'Marcos',   'FechaNacimiento'   => 1980);
$personas[3] =  array('cedula' => '123C', 'nombre' => 'Pedro',    'FechaNacimiento'   => 1990);
$personas[4] =  array('cedula' => '123D', 'nombre' => 'Ana',  'FechaNacimiento'   => 1995);
$personas[5] =  array('cedula' => '123F', 'nombre' => 'Maria',    'FechaNacimiento'   => 1972);
*/
$personas = 
[
    'cedula' => '123F', 
    'nombre' => 'Maria',    
    'FechaNacimiento'   => 1972
];

$datos_persona_entrada = array( "datos_persona_entrada" => $personas);

$response = $client -> call('consultaPersonas', $datos_persona_entrada);

//Setting credentials for Authentication 
$client->setCredentials("abhishek","123456","basic");

$error = $client -> getError();
if ($error)
{
	echo '<pre style="color: red">' . $error . '</pre>';
	echo '<p style="color:red;'>htmlspecialchars($client->getDebug(), ENT_QUOTES).'</p>';
	die();
}

//var_dump($response);
print_r ($response[0]["Nombre"]);