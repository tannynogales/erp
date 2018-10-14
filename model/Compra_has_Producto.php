<?php
class Compra_has_Producto extends MyPDO{
    public $table = "compra_has_producto";
    public
            $id,
            $compra_id, 
            $producto_id, 
            $cantidad, 
            $costo;
    
    public function __construct() 
    {
        
    }
    function getId() {
        return $this->id;
    }

    function getCompra_id() {
        return $this->compra_id;
    }

    function getProducto_id() {
        return $this->producto_id;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getCosto() {
        return $this->costo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCompra_id($compra_id) {
        $this->compra_id = $compra_id;
    }

    function setProducto_id($producto_id) {
        $this->producto_id = $producto_id;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setCosto($costo) {
        $this->costo = $costo;
    }

    

    public function save()
    {
        $sql="INSERT INTO $this->table (compra_id, producto_id, cantidad, costo)
                VALUES(
                       '".$this->compra_id."',
                       '".$this->producto_id."',
                       '".$this->cantidad."',
                       '".$this->costo."');";
        //echo $sql;
        $response = $this ->consult($sql);      
        return $response;
    }


    public function getBy($fieldName, $value, $order = [])
    {
        $sql="
        SELECT 
            compra_has_producto.id, 
            compra_id, 
            producto_id, 
            cantidad, 
            compra_has_producto.costo,
            producto.nombre,
            producto.codigo
        FROM $this->table
        JOIN producto
        ON compra_has_producto.producto_id=producto.id
        WHERE 
        $fieldName = '$value';";  
        
        $response = $this ->consult($sql);
        return $response;
    }   
    
}