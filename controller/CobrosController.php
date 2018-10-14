<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CobrosController extends BasisController
{
    public function __construct() {
        //parent::__construct();
    }
    function TestAction()
    {
        require './vendor/autoload.php';
        // Debemos conocer el $receiverId y el $secretKey de ante mano.
        $receiverId = '104944';
        $secretKey = '5839b88aa6eb5734943272f8064c68b74d144cee';

        $configuration = new Khipu\Configuration();
        $configuration->setReceiverId($receiverId);
        $configuration->setSecret($secretKey);
        // $configuration->setDebug(true);
                
        $client = new Khipu\ApiClient($configuration);
        $payments = new Khipu\Client\PaymentsApi($client);
        //print_r($payments); exit();
        try 
        {
            $opts = array
            (
                "transaction_id" => "MTI-100",
                "return_url" => "http://mi-ecomerce.com/backend/return",
                "cancel_url" => "http://mi-ecomerce.com/backend/cancel",
                "picture_url" => "http://mi-ecomerce.com/pictures/foto-producto.jpg",
                "notify_url" => "http://mi-ecomerce.com/backend/notify",
                "notify_api_version" => "1.3"
            );
            $response = $payments->paymentsPost("Compra de prueba de la API" //Motivo de la compra
                , "CLP" //Moneda
                , 100.0 //Monto
                , $opts //campos opcionales
            );

            print_r($response);
        } 
        catch (\Khipu\ApiException $e) 
        {
            echo print_r($e->getResponseBody(), TRUE);
        }

        /*
        $receiver_id = "identificador_de_comercio";
        $secret = 'secret-key';
        $method = 'POST';
        $url = 'https://khipu.com/api/2.0/payments';

        $params = array
        (
            'subject' => 'ejemplo de compra',
            'amount' => '1000',
            'currency' => 'CLP'
        );

        $keys = array_keys($params);
        sort($keys);

        $toSign = "$method&" . rawurlencode($url);
        
        foreach ($keys as $key) 
        {
                $toSign .= "&" . rawurlencode($key) . "=" . rawurlencode($params[$key]);
        }
        $hash = hash_hmac('sha256', $toSign , $secret);
        $value = "$receiver_id:$hash";
        print "$value\n";
        */        
    }
}