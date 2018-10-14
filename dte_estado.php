<?php

/**
 * Ejemplo que muestra los pasos para:
 *  - Actualizar el estado de un DTE real
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2016-09-15
 */
// datos a utilizar
$url = 'https://libredte.cl';
$hash = 'fWbaHVrnzw3tX0YpKSt70Wp3ChFN8cec';
$rut = 76884800;
$dte = 33;
$folio = 778;
$metodo = 1; // =1 servicio web, =0 correo
// incluir autocarga de composer
require('vendor/autoload.php');
// crear cliente
$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
// $LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL
# consultar estado de dte emitido
$estado = $LibreDTE->get('/dte/dte_emitidos/actualizar_estado/'.$dte.'/'.$folio.'/'.$rut.'?usarWebservice='.$metodo);
if ($estado['status']['code']!=200) {
    die('Error al obtener el estado del DTE emitido: '.$estado['body']."\n");
}
print_r($estado['body']);


// resultado

// Array ( [track_id] => 2245599742 [revision_estado] => EPR - Envio Procesado [revision_detalle] => DTE aceptado ) 