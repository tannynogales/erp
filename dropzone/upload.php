<?php
/*
 * Creamos el archivo datos.txt
 * ponemos tipo 'a' para añadir lineas sin borrar
 */
$file=fopen("datos.txt","a") or die("Problemas");
//vamos añadiendo el contenido
fputs($file,$_POST["otro_dato"]);
fputs($file,"\n");
fputs($file,"2a..");
fputs($file,"\n");
fclose($file);


echo "hola";
exit();
echo $ds     = DIRECTORY_SEPARATOR;  //1
$storeFolder = 'uploads';   //2
 
if (!empty($_FILES)) 
{
     
    $tempFile = $_FILES['file']['tmp_name'];          //3             
      
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
     
    $targetFile =  $targetPath. $_FILES['file']['name'];  //5
 
    $aux = move_uploaded_file($tempFile,$targetFile); //6
}
else
{
    echo "else";
}

/*
 * Creamos el archivo datos.txt
 * ponemos tipo 'a' para añadir lineas sin borrar
 */
$file=fopen("datos.txt","a") or die("Problemas");
//vamos añadiendo el contenido
fputs($file,"1b...");
fputs($file,"\n");
fputs($file,"2b..");
fputs($file,"\n");
fclose($file);

