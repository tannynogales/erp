<?php
function rm_recursive($filepath) 
{
    if (is_dir($filepath) && !is_link($filepath)) 
    {
    	if ($dh = opendir($filepath)) 
    	{
            while (($sf = readdir($dh)) !== false) 
            {  
                if ($sf == '.' || $sf == '..') 
                {
                    continue;
                }
                if (!rm_recursive($filepath.'/'.$sf)) 
                {
                    throw new Exception($filepath.'/'.$sf.' could not be deleted.');
                }
            }
            closedir($dh);
        }
        return rmdir($filepath);
    }
    return unlink($filepath);
}

function nombreMes($mes)
{
	setlocale(LC_TIME, 'spanish');
	$nombre=strftime("%B",mktime(0, 0, 0, $mes, 1, 2000));
	return $nombre;
} 

function plancha_madre($largo, $ancho, $alto)
{	
	$resp = array();
	$merma_carton = 5;
	$aleta = 30;
	
	$resp["largo"] = 2*$largo + 2*$ancho + 4*$merma_carton + $aleta;
	$resp["ancho"] = $alto + $merma_carton + 2*($merma_carton/2) + 2*($ancho/2) ; //no ocupé ceil() , round()

	if ( $resp["largo"] >= 1800 && $resp["ancho"] >= 350)	
	$resp["largo"] = $resp["largo"] + 10;
	
	return $resp; 
}

//esta fucnion agrega X=$day dias laborales a una fecha=$fecha
function addDay($fecha, $day, $feriados = Array())
{
	if ($day==0) return $fecha; else
	{
		$nuevafecha = $fecha;
		for ($i = 1; $i <= $day; $i++) 
		{
		    $nuevafecha = strtotime ( "+1 day" , strtotime ( $nuevafecha ) ) ;
		    $nuevafecha = date ( 'Y-m-j' , $nuevafecha);
			$condicion = esLaboral($nuevafecha, $feriados);
			while(!$condicion)
			{
			    $nuevafecha = strtotime ( "+1 day" , strtotime ( $nuevafecha ) ) ;
			    $nuevafecha = date ( 'Y-m-j' , $nuevafecha);		
				$condicion = esLaboral($nuevafecha, $feriados);
			}			
		}		
	}	
	return $nuevafecha;
}

// esta funcion me entrega el día (de 0 a 6 ) de la semana que corresponde una fecha
function diaSemana($ano,$mes,$dia)
{
	// 0->domingo | 6->sabado
	$dia= date("w",mktime(0, 0, 0, $mes, $dia, $ano));
	return $dia;
}
//esta funcion me entrega TRUE si la fecha es Laboral o FALSE si no es Laboral
function esLaboral($fecha, $feriados = Array())
{
	$fecha_aux = explode("-", $fecha);
	$dia = diaSemana($fecha_aux[0],$fecha_aux[1],$fecha_aux[2]);
	$condicion = $dia!=0&&$dia!=6&&!inArrayDeep($fecha,$feriados); 
	return $condicion;
}

// Esta función buscar en todos los valores de un array si existe un valor
function inArrayDeep($aguja, $array, $estricto=FALSE)
{
	foreach ($array as $clave => $valor) 
	{
		if(in_array ($aguja,$valor,$estricto))
		return TRUE;
	}
	return FALSE;
}

function search_associative_array($aguja, $array, $column_search, $column_return, $estricto=FALSE)
{
    $array_column     = array_column($array, $column_search);
    $array_column_key = array_search($aguja, $array_column);
    return $array[$array_column_key][$column_return];
}

function inArray($array, $dato)
{
	for ($i = 0; $i < count($array); $i++)
	{
		if ($array[$i] == $dato)
		return true;
	}
	return false;	
}

function date2number($date)  
{
	//echo $date.": ";
	//var_dump($date);  
    $valores = explode ("-", $date);
	//echo count ($valores);
	if ( count ($valores) != 3) 
    $valores = explode ("/", $date);
	
	// echo strlen ($valores[0] );
	if(  strlen ($valores[0]) == 4) // -> Formato AAA/MM/DD
	{
		//echo "caso 1, ";
	    $dia  = $valores[2];    
		$mes  = $valores[1];    
		$anyo = $valores[0];  
	}
	else // -> Formato DD/MM/YYYY
	{
		//echo "caso 2, ";
	    $dia  = $valores[0];    
		$mes  = $valores[1];    
		$anyo = $valores[2];  	
	}	
    if(!checkdate($mes, $dia, $anyo))
    {
    	// "La fecha ".$date." no es válida";  
        return 0;  
    }else
	{
		// echo  gregoriantojd($mes, $dia, $anyo); echo "<br>";		
    	return  gregoriantojd($mes, $dia, $anyo);  
	} 
}

function utf8($value)
{
	if(!mb_check_encoding($value, 'UTF-8'))
	return $value = utf8_encode($value);
	else
	return $value;
}

function Array2Utf8($array)
{
	if ($array === FALSE) // adaptado porque yo en las consultas puedo retornar 
	return FALSE;		  // falso si no encuentra nada
	
    foreach($array as $key_fila => &$fila)
	{
	    foreach($fila as $key_celda => &$celda)
		{
			if(!mb_check_encoding($celda, 'UTF-8'))
			$celda = utf8_encode($celda);
		}
    }
    return $array;
 } 

function rutFormato($rut_param)
{
    $parte4 = substr($rut_param, -1); // seria solo el numero verificador 
    $parte3 = substr($rut_param, -4,3); // la cuenta va de derecha a izq  
    $parte2 = substr($rut_param, -7,3);  
    $parte1 = substr($rut_param, 0,-7); ; //de esta manera toma todos los caracteres desde el 8 hacia la izq 

    return $parte1.".".$parte2.".".$parte3."-".$parte4; 
}

function extratcNumber($cadena)
{
	//$cadena = str_replace(",", "", $cadena);
	//return $cadena;
	$cadena = strtolower($cadena);
    $numero = "";
    for( $index = 0; $index < strlen($cadena); $index++ )
    {
        if( is_numeric($cadena[$index]) || $cadena[$index] == ".")
        {
            $numero .= $cadena[$index];
        }
    }
	if ($numero==="")
	$numero = 0;
	return $numero; 
		
}

function extratcRUT($cadena)
{
    // solo la uso para quitar el formato a los rut. mantiene el digito verficador
    //$cadena = "asdasd7234";
	$cadena = strtolower($cadena);
    $numero = "";
    for( $index = 0; $index < strlen($cadena); $index++ )
    {
        if( is_numeric($cadena[$index]) || $cadena[$index] == "k")
        {
            $numero .= $cadena[$index];
        }
    }
	return $numero;  
}

function dv($rut)
{
	// genera el digito verificador chileno
	if ( $rut > 0)
	{
		$d = 1;
		for ($x = 0; $rut != 0; $rut /= 10)
			$d = ($d + $rut % 10 * (9 - $x++ % 6)) % 11;
		return chr($d ? $d + 47 : 75);
	} 
	else
	{
		return false;
	}
}
	
function numero_formato($numero, $decimales = 0)
{
	if (is_numeric ($numero))
		return number_format ($numero, $decimales);
	else
		return $numero;
	
}

function MySQLDateFormat($fecha)
{
	$fecha = trim($fecha);
        
        $guion = strpos($fecha, "-");
        if ($guion === FALSE)
            $fecha = str_replace("/", "-", $fecha);
        
        $fecha = explode("-", $fecha);
        
	return "$fecha[2]-$fecha[1]-$fecha[0]";;
}

function twistDate($date)
{
 
 // Normaliza una fecha de 
 // dd/mm/aaaa a aaaa/mm/dd
 // o tambien de
 // aaaa/mm/dd a dd/mm/aaaa
 // dependiendo el formato de la fecha ingresada
 // 
    if(!empty($date))
    {
        $var = explode('/',str_replace('-','/',$date));
        $aux = "$var[2]/$var[1]/$var[0]";
	return trim($aux);
    }
}