<?php
class Venta_has_Producto extends MyPDO{
    public $table = "venta_has_producto";
    public
            $id, 
            $venta_id, $producto_id, 
            $cantidad, 
            $precio;
    
    public function __construct() 
    {
        
    }

    function getId() {
        return $this->id;
    }

    function getVenta_id() {
        return $this->venta_id;
    }

    function getProducto_id() {
        return $this->producto_id;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getPrecio() {
        return $this->precio;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setVenta_id($venta_id) {
        $this->venta_id = $venta_id;
    }

    function setProducto_id($producto_id) {
        $this->producto_id = $producto_id;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }

    function getDetalle($venta_id)
    {
        $sql = "
        SELECT 
            producto.codigo as 'producto_codigo',
            producto.nombre as 'producto_nombre',
            `venta_has_producto`.cantidad as 'cantidad',
            `venta_has_producto`.precio as 'precio_venta',
            `venta_has_producto`.precio*`venta_has_producto`.cantidad as 'total'            
        FROM 
                venta_has_producto
        INNER JOIN producto
        ON producto.id=venta_has_producto.producto_id
        INNER JOIN venta 
        ON venta_id = venta.id
        WHERE `venta`.id = $venta_id;";                
        
        $response = $this ->consult($sql);
        return $response;
    }
    
    public function save()
    {
        $sql="INSERT INTO $this->table (venta_id, producto_id, cantidad, precio)
                VALUES(
                       '".$this->venta_id."',
                       '".$this->producto_id."',
                       '".$this->cantidad."',
                       '".$this->precio."');";
        //echo $sql;
        $response = $this ->consult($sql);      
        return $response;
    }

    public function update()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            rut='$this->rut',
            nombre='$this->nombre',
            aPaterno='$this->apellido',
            email='$this->email',
            password='$this->password'
        WHERE 
            id=$this->id;
        ";
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }
    
    public function getBy($fieldName, $value, $order = [])
    {
        $sql="
        SELECT 
            venta_has_producto.id, 
            venta_id, 
            producto_id, 
            cantidad, 
            venta_has_producto.precio,
            producto.nombre,
            producto.codigo
        FROM $this->table
        JOIN producto
        ON venta_has_producto.producto_id=producto.id
        WHERE 
        $fieldName = '$value';";  
        
        $response = $this ->consult($sql);
        return $response;
    }   
    
}