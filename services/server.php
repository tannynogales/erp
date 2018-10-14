<?php
/*
 * Es obligatorio que estén desactivadas las notificaciones de error, 
 * de lo contrario, se generará un error cuando se ejecute el cliente 
 * del servicio.
 */
//error_reporting(0);

/*
 * Incluimos libreria nuSoap
 */
require_once('./../lib/nusoap-php7/class.nusoap_base.php');

/*
 * El nameSpace identifica al servicio en la web, por ende se recomienda
 * usar el nombre de tu sitio para así tener un nombre único.
 * Resultado -> 127.0.0.1/PHP-MVC-BASE/services/test/server2.php
 */
$nameSpace = filter_input(\INPUT_SERVER, 'SERVER_NAME').filter_input(\INPUT_SERVER , 'PHP_SELF');
//exit($nameSpace);

$server = new nusoap_server();

/*
 * En el título del servicio no se aceptan acentos, 
 * caracteres especiales o espacios
 */
$server -> configureWSDL('Titulo_Servicios', $nameSpace);
// Registramos el nameSpace
$server -> wsdl -> schematargetnamespace = $nameSpace;

/*
 * Para mantener el orden, importo las estructuras del servicio desde otro 
 * archivo php.
 */
require_once './estructures.php';

/*
 * Para mantener el orden, importo las funciones del servicio desde otro 
 * archivo php.
 */
require_once 'functions.php';

$server -> xml_encoding = "utf-8";
$server -> soap_defencoding = "utf-8";

/*
 * Registro las Funciones del servicio.
 */
$server -> register
(
        'consultaPersonas',
        //['$param' => 'xsd:string'], //'xsd:int'
        array('datos_persona_entrada' => 'tns:datos_persona_entrada'), // parametros de entrada
        ['return'        => 'tns:ArregloDeEstructuras'],
        $nameSpace, // namespace,
        false, // soapaction:(use default) // 'urn:infoBlog#muestraImagen'
        'rpc', // estilo
        'encoded', // use: encoded or literal
        // description: documentation for the method
        'Complex Hello World Method'        
);


// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : "php://input";
$server -> service(file_get_contents($HTTP_RAW_POST_DATA));
//@$server->service($HTTP_RAW_POST_DATA);
//@$server->service(file_get_contents("php://input"));