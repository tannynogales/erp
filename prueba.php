<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// incluir autocarga de composer
require 'vendor/autoload.php';   
require 'lib/dte_client.php';   

$url = 'https://libredte.cl';
$hash   = 'fWbaHVrnzw3tX0YpKSt70Wp3ChFN8cec';
 
// Crear Cliente
$cliente = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
 
$FolioSinUso = $cliente->get('/dte/admin/dte_folios/info/33/76884800?sinUso=1');

print_r($FolioSinUso["body"]["sin_uso"]);

$dte_client = new dte_client();
$sin_uso = $dte_client -> getUnusedFolio();
print_r($sin_uso);

