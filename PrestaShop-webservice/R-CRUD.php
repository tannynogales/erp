<?php
// Here we define constants
define('DEBUG', false);										   // Debug mode
define('PS_SHOP_PATH', 'http://127.0.0.1/ps17');		   // Root path of your PrestaShop store
define('PS_WS_AUTH_KEY', '1VDHCTFAXPNB5RMBEWST7S8FMUYZHYTX');  // Auth key (Get it in your Back Office)

require_once( './../lib/PSWebServiceLibrary.php' );

try 
{
	$webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);  
        
        $opt['resource'] = 'customers';
	$opt['id']       = 1;
        
	//Retrieving the XML data
        $xml = $webService->get($opt); 
        //print_r($xml);
        
        //$resources = $xml->customers->children();
        //$resources = $xml->customer->children();
        $resources = $xml->children()->children();
        
        //print_r($resources);
        foreach ($resources as $key => $resource)
            echo 'Name of field: ' . $key . ' - Value: ' . $resource . '<br />';       
        
}
catch(PrestaShopWebserviceException $ex)
{
	// Shows a message related to the error
    echo 'Other error: <br />' . $ex->getMessage();
}
	
?>