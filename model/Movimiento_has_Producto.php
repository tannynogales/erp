<?php
class Movimiento_has_Producto extends MyPDO{
    public $table = "movimiento_de_stock_has_producto";
    public
            $id, 
            $movimiento_de_stock_id, 
            $producto_id, 
            $cantidad;
    
    public function __construct() 
    {
        
    }
    function getId() {
        return $this->id;
    }

    function getMovimiento_de_stock_id() {
        return $this->movimiento_de_stock_id;
    }

    function getProducto_id() {
        return $this->producto_id;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setMovimiento_de_stock_id($movimiento_de_stock_id) {
        $this->movimiento_de_stock_id = $movimiento_de_stock_id;
    }

    function setProducto_id($producto_id) {
        $this->producto_id = $producto_id;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    
    public function save()
    {
        $sql="INSERT INTO $this->table (movimiento_de_stock_id, producto_id, cantidad)
                VALUES(
                       '".$this->movimiento_de_stock_id."',
                       '".$this->producto_id."',
                       '".$this->cantidad."');";
        $response = $this ->consult($sql);      
        return $response;
    }
    public function getBy($fieldName, $value, $order = [])
    {
        $sql="
        SELECT 
            movimiento_de_stock_has_producto.id, 
            movimiento_de_stock_id, 
            producto_id, 
            cantidad, 
            producto.nombre,
            producto.codigo
        FROM $this->table
        JOIN producto
        ON movimiento_de_stock_has_producto.producto_id=producto.id
        WHERE 
        $fieldName = '$value';";  
        
        $response = $this ->consult($sql);
        return $response;
    }    
}

