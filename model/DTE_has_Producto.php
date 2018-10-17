<?php
class DTE_has_Producto extends MyPDO{
    public $table = "dte_has_producto";
    public
            $id, 
            $DTE_id, $producto_id, 
            $cantidad, 
            $descuento, 
            $precio;
    
    public function __construct() 
    {
        
    }
    function getId() {
        return $this->id;
    }

    function getDTE_id() {
        return $this->DTE_id;
    }

    function getProducto_id() {
        return $this->producto_id;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getDescuento() {
        return $this->descuento;
    }

    function getPrecio() {
        return $this->precio;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDTE_id($DTE_id) {
        $this->DTE_id = $DTE_id;
    }

    function setProducto_id($producto_id) {
        $this->producto_id = $producto_id;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setDescuento($descuento) {
        $this->descuento = $descuento;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }
    
    function getDetalle($folio)
    {
        $sql = "
        SELECT 
            producto.codigo as 'producto_codigo',
            producto.nombre as 'producto_nombre',
            dte_has_producto.cantidad as 'cantidad',
            dte_has_producto.precio as 'precio_venta',
            dte_has_producto.precio*dte_has_producto.cantidad as 'total'
        FROM 
            dte_has_producto
        INNER JOIN producto
        ON producto.id=dte_has_producto.producto_id
        INNER JOIN dte 
        ON DTE_id = dte.id
        WHERE folio = $folio;";                
        
        $response = $this ->consult($sql);
        return $response;
    }
    
    public function save()
    {
        $sql="INSERT INTO $this->table (DTE_id, producto_id, cantidad, descuento, precio)
                VALUES(
                       '".$this->DTE_id."',
                       '".$this->producto_id."',
                       '".$this->cantidad."',
                       '".$this->descuento."',
                       '".$this->precio."');";
        //echo $sql;
        $response = $this ->consult($sql);
	if ($response)
	{
            $query_2 = " SELECT LAST_INSERT_ID() as 'producto_id'";
            $result_2 = $this -> consult($query_2);
            return 	$result_2[0]["producto_id"];	
        }else        
        return FALSE;
    } 
}
?>