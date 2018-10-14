<?php
/*
 * Creamos el archivo datos.txt
 * ponemos tipo 'a' para añadir lineas sin borrar
 */
$file=fopen("datos.txt","a") or die("Problemas");
//vamos añadiendo el contenido
fputs($file,"1...");
fputs($file,"\n");
fputs($file,"2..");
fputs($file,"\n");
fclose($file);