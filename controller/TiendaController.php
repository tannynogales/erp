<?php
class TiendaController extends BasisController
{
    public function __construct() {
        //parent::__construct();
    }
    
    public function BuscaProductoAction($create_result="")
    {
        $response = Array();
        //Creamos el objeto del Modelo "usuario"
        $producto = new Producto();
        
        //Conseguimos todos los productos
        $products = $producto->getAll();
        //var_dump($products);
        foreach ($products as &$valor) 
        {
            $valor["precio"] = "$ ".number_format($valor["precio"], 0, '.', ',');
        }
        $response["asdf"] = "holasdd";
        $response["html"] = $this -> gat
        ( 
            "crearFactura", // Plantilla 
            [
                "products" => $products
            ],
            [
                "template" => "products",
                "render"   => FALSE
            ]
        );     
        echo json_encode($response);
        $this -> view
        ( 
            "crearCompra", // Plantilla 
            [
                "bodegas" => $bodegas,
                "message" => $message,
                "state"   => "$state"
            ]
        );   		
    }
    
}
