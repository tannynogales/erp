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

if(!isset($_GET["codigo"]))
{
    header("HTTP/1.0 404 Not Found");
    die;
}
else
{
    $codigo = $_GET["codigo"];
}
                
$response = 
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
/*
$response["TpoCodigo"]  = "INT1";
$response["IndExe"]     = 1;
$response["UnmdItem"]   = "";
$response["ValorDR"]    = 0;
$response["TpoValor"]   = "%";
$response["CodImpAdic"] = NULL;
*/

$producto = new Producto();

$products = $producto->getBy("codigo", $_GET["codigo"]);
//var_dump($products[0]);
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
