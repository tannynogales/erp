<?php
class Inventario_has_Producto extends MyPDO{
    public $table = "inventario_has_producto";
    public
            $id, 
            $inventario_id, 
            $producto_id, 
            $cantidad, 
            $precio_costo, 
            $precio_venta;
    
    function getId() {
        return $this->id;
    }

    function getInventario_id() {
        return $this->inventario_id;
    }

    function getProducto_id() {
        return $this->producto_id;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getPrecio_costo() {
        return $this->precio_costo;
    }

    function getPrecio_venta() {
        return $this->precio_venta;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setInventario_id($inventario_id) {
        $this->inventario_id = $inventario_id;
    }

    function setProducto_id($producto_id) {
        $this->producto_id = $producto_id;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setPrecio_costo($precio_costo) {
        $this->precio_costo = $precio_costo;
    }

    function setPrecio_venta($precio_venta) {
        $this->precio_venta = $precio_venta;
    }

    public function __construct() 
    {
        
    }
    
    public function save()
    {
        $sql="INSERT INTO $this->table (inventario_id, producto_id, cantidad, precio_costo, precio_venta)
                VALUES(
                       '".$this->inventario_id."',
                       '".$this->producto_id."',
                       '".$this->cantidad."',
                       '".$this->precio_costo."',    
                       '".$this->precio_venta."');";
        
        $response = $this ->consult($sql);      
        return $response;
    }
    function getBy($fieldName, $value, $order = []) 
    {
        $sql = " 
	SELECT 
            producto.id as 'producto_id', producto.codigo as 'producto_codigo', producto.nombre as 'producto_nombre', cantidad
        FROM 
            $this->table
	INNER JOIN producto 
        ON producto.id = inventario_has_producto.producto_id
        WHERE
            $this->table.$fieldName = '$value'        
        ";
        $response = $this ->consult($sql);
        return $response;        
    }
}

