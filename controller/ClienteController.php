<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClienteController extends BasisController
{
    public function __construct() {}
    
    function VerAction()
    {
        $contribuyente = new Contribuyente();
        $contribuyentes = $contribuyente->getAll();
        
        $this -> view
        ( 
            "vercliente", // Plantilla 
            [
                "clientes"   => $contribuyentes
            ]
        ); 
    }
}