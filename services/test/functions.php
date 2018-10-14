<?php

function consultaPersonas($datos) 
{
    $aux = 0;
	$user = $_SERVER['PHP_AUTH_USER'];
    ob_start();
    var_dump($_SERVER['PHP_AUTH_USER']);
    $user = ob_get_clean();

    foreach ($datos as $key => $value)
    {
         $aux = $aux + 2;
    }
    
    $arreglo   = array();
    $arreglo[] = array('Nombre'=>  $aux."Juan".$user, 'Apellidos'=>"Torres", 'Edad'=>18);
    $arreglo[] = array('Nombre'=>"Teresa", 'Apellidos'=>"Jiménez Sánchez", 'Edad'=>19);
    $arreglo[] = array('Nombre'=>"Efraín", 'Apellidos'=>"Ovalles López", 'Edad'=>22);
    return $arreglo;
}