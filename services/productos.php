<?php

require_once '../lib/library_php.php';
require_once '../config/global.php';

//Conexión a la base de datos
require_once '../core/MyPDO.php';
// Inicializo la clase MyPDO, asegurando una única instancia 
MyPDO::singleton(require_once '../config/database.php');

foreach(glob("../model/*.php") as $file)
{
    require_once $file;
} 

                
$responseAux = 
[
    "TpoCodigo" => "INT1",
    "VlrCodigo" => NULL,
    "NmbItem" => NULL,
    "DscItem" => NULL,
    "IndExe" => 1,
    "UnmdItem" => "",
    "PrcItem" => NULL,
    "ValorDR" => 0,
    "TpoValor" => "%",
    "CodImpAdic" => NULL
];

$producto = new Producto();

$products = $producto->getAll();
var_dump($products);

exit();
if(count($products)>0)
{
    /*
    $response["VlrCodigo"] = $products[0]["codigo"];
    $response["NmbItem"]   = $products[0]["nombre"];
    $response["PrcItem"]   = (int) $products[0]["precio"];
    $response["DscItem"]   = $products[0]["descripcion_corta"];    
    */
    $response["VlrCodigo"] = $products[0]["id"];
    $response["NmbItem"]   = $products[0]["codigo"];
    $response["PrcItem"]   = (int) $products[0]["precio"];
    $response["DscItem"]   = $products[0]["nombre"];
    $response["IndExe"]    = (int) $products[0]["exento"];
    
    echo json_encode($response);
}else
{
    header("HTTP/1.0 404 Not Found");
}

/*
{
  "TpoCodigo":"INT1",
  "VlrCodigo":"dte-cert",
  "NmbItem":"Asesor\u00eda DTE",
  "DscItem":"Certificaci\u00f3n facturaci\u00f3n electr\u00f3nica",
  "IndExe":1,
  "UnmdItem":"",
  "PrcItem":125000,
  "ValorDR":0,
  "TpoValor":"%",
  "CodImpAdic":null
}
 *  */
