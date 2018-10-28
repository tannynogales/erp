<?php
class Inventario extends MyPDO{
    public $table = "inventario";
    
    public 
            $id, 
            $bodega_id, 
            $usuario_id, 
            $fecha_registro, 
            $fecha_inventario, 
            $diferencia_costo, 
            $diferencia_venta;
            
    public function __construct() 
    {
        
    }

    function getId() {
        return $this->id;
    }

    function getBodega_id() {
        return $this->bodega_id;
    }

    function getUsuario_id() {
        return $this->usuario_id;
    }

    function getFecha_registro() {
        return $this->fecha_registro;
    }

    function getFecha_inventario() {
        return $this->fecha_inventario;
    }

    function getDiferencia_costo() {
        return $this->diferencia_costo;
    }

    function getDiferencia_venta() {
        return $this->diferencia_venta;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setBodega_id($bodega_id) {
        $this->bodega_id = $bodega_id;
    }

    function setUsuario_id($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }
    public function getUltimoConteo()
    {
        $sql = " 
        SELECT 
            ultimo_conteo.inventario_id, 
            ultimo_conteo.producto_id, 
            ultimo_conteo.bodega_id, 
            cantidad as 'conteo_cantidad', 
            fecha_inventario as 'conteo_fecha',
            bodega.nombre as 'bodega_nombre',
            producto.codigo as 'producto_codigo',
            producto.nombre as 'producto_nombre'        
        FROM 
        (
        SELECT 
            MAX(inventario.id) as 'inventario_id',
            inventario_has_producto.producto_id, 
            inventario.bodega_id as 'bodega_id'
        FROM 
            inventario_has_producto
        INNER JOIN 
            inventario
        ON 
            inventario.id = inventario_has_producto.inventario_id
        GROUP BY
            inventario_has_producto.producto_id, inventario.bodega_id
        ) as ultimo_conteo
        LEFT JOIN
        (
        SELECT 
            inventario.id as 'inventario_id',
            inventario_has_producto.producto_id, 
            inventario.bodega_id as 'bodega_id',
            inventario_has_producto.cantidad,
            inventario.fecha_inventario
        FROM 
            inventario_has_producto
        INNER JOIN 
            inventario
        ON 
            inventario.id = inventario_has_producto.inventario_id 
        ) as conteo_cantidad
        ON 
            conteo_cantidad.inventario_id = ultimo_conteo.inventario_id and
            conteo_cantidad.producto_id   = ultimo_conteo.producto_id   and
            conteo_cantidad.bodega_id     = ultimo_conteo.bodega_id 
        LEFT JOIN producto
                ON ultimo_conteo.producto_id = producto.id     
        LEFT JOIN bodega
                ON ultimo_conteo.bodega_id = bodega.id
        WHERE producto.eliminado = 0
        -- WHERE ultimo_conteo.producto_id = 1518;";                
        
        $response = $this ->consult($sql);
        return $response;
    } 

    public function getVentas($fecha_conteo, $producto_id, $bodega_id)
    {
    $sql ="
        SELECT
            ventas.id,
            ventas.fuente,
            ventas.fecha_venta,
            ventas.producto_id,
            ventas.bodega_id,
             SUM(ventas.cantidad) AS 'ventas_cantidad',
            bodega.nombre as 'bodega_nombre',
            producto.codigo as 'producto_codigo'
        FROM
        (
            SELECT
                movimiento_de_stock.id as 'id',
                'mov_sale' as 'fuente',
                fecha_movimiento 'fecha_venta', 
                producto_id,
                bodega_id_sale as 'bodega_id',
                cantidad*-1 as 'cantidad'    
            FROM 
                movimiento_de_stock_has_producto
            INNER JOIN movimiento_de_stock
            ON movimiento_de_stock_id = movimiento_de_stock.id     
            
            UNION
            
            SELECT
		movimiento_de_stock.id as 'id',
                'mov_entra' as 'fuente',
                fecha_movimiento 'fecha_venta', 
                producto_id,
                bodega_id_entra as 'bodega_id',
                cantidad as 'cantidad'    
            FROM 
                movimiento_de_stock_has_producto
            INNER JOIN movimiento_de_stock
            ON movimiento_de_stock_id = movimiento_de_stock.id     
            
            UNION
        
            SELECT
		compra.id as 'id',
                'compra' as 'fuente',
		fecha_compra as 'fecha_venta', 
		producto_id,
		bodega_id,
		cantidad as 'cantidad'    
            FROM 
                compra_has_producto
            INNER JOIN compra
            ON compra_id = compra.id    
                
            UNION        
                
            SELECT 
		venta.id as 'id',
                'ventas' as 'fuente',
                fecha_venta, 
                producto_id,
                bodega_id,
                cantidad*-1 as 'cantidad'
            FROM venta_has_producto
            INNER JOIN venta
            ON venta_id = venta.id

            UNION

            SELECT 
		folio as 'id',
                'dte' as 'fuente',
                FchEmis as  'fecha_venta',
                producto_id,
                1 as 'bodega_id',
                cantidad*-1 as 'cantidad'
            FROM dte_has_producto
            INNER JOIN dte
            ON dte_id = dte.id
            WHERE tipoDTE = 33
            
            UNION
            
            SELECT 
		folio as 'id',
                'credito' as 'fuente',
                FchEmis as  'fecha_venta',
                producto_id,
                1 as 'bodega_id',
                cantidad as 'cantidad'
            FROM dte_has_producto
            INNER JOIN dte
            ON dte_id = dte.id
            WHERE tipoDTE = 61
            
        ) AS ventas    
        INNER JOIN bodega
        ON bodega.id = bodega_id
        INNER JOIN producto
        ON producto.id = producto_id
        WHERE 
            producto_id = $producto_id AND fecha_venta >= '$fecha_conteo' AND bodega_id = $bodega_id
        ORDER BY producto_id
        ";      
        
        $response = $this ->consult($sql);
        return $response;
    }     
    public function getVentasDetalle($fecha_conteo, $producto_id, $bodega_id)
    { 
    $sql ="
        SELECT
            ventas.id,
            ventas.fuente,
            ventas.fecha_venta,
            ventas.producto_id,
            ventas.bodega_id,
            ventas.cantidad,
            bodega.nombre as 'bodega_nombre',
            producto.codigo as 'producto_codigo',
            producto.eliminado as 'producto_eliminado'
        FROM
        (
            SELECT
                movimiento_de_stock.id as 'id',
                'mov_sale' as 'fuente',
                fecha_movimiento 'fecha_venta', 
                producto_id,
                bodega_id_sale as 'bodega_id',
                cantidad*-1 as 'cantidad'    
            FROM 
                movimiento_de_stock_has_producto
            INNER JOIN movimiento_de_stock
            ON movimiento_de_stock_id = movimiento_de_stock.id     
            
            UNION
            
            SELECT
		movimiento_de_stock.id as 'id',
                'mov_entra' as 'fuente',
                fecha_movimiento 'fecha_venta', 
                producto_id,
                bodega_id_entra as 'bodega_id',
                cantidad as 'cantidad'    
            FROM 
                movimiento_de_stock_has_producto
            INNER JOIN movimiento_de_stock
            ON movimiento_de_stock_id = movimiento_de_stock.id     
            
            UNION
        
            SELECT
		compra.id as 'id',
                'compra' as 'fuente',
		fecha_compra as 'fecha_venta', 
		producto_id,
		bodega_id,
		cantidad as 'cantidad'    
            FROM 
                compra_has_producto
            INNER JOIN compra
            ON compra_id = compra.id    
                
            UNION        
                
            SELECT 
		venta.id as 'id',
                'ventas' as 'fuente',
                fecha_venta, 
                producto_id,
                bodega_id,
                cantidad*-1 as 'cantidad'
            FROM venta_has_producto
            INNER JOIN venta
            ON venta_id = venta.id

            UNION

            SELECT 
		folio as 'id',
                'dte' as 'fuente',
                FchEmis as  'fecha_venta',
                producto_id,
                1 as 'bodega_id',
                cantidad*-1 as 'cantidad'
            FROM dte_has_producto
            INNER JOIN dte
            ON dte_id = dte.id
            WHERE tipoDTE = 33
            
            UNION
            
            SELECT 
		folio as 'id',
                'credito' as 'fuente',
                FchEmis as  'fecha_venta',
                producto_id,
                1 as 'bodega_id',
                cantidad as 'cantidad'
            FROM dte_has_producto
            INNER JOIN dte
            ON dte_id = dte.id
            WHERE tipoDTE = 61
            
        ) AS ventas    
        INNER JOIN bodega
        ON bodega.id = bodega_id
        INNER JOIN producto
        ON producto.id = producto_id
        WHERE 
            producto_id = $producto_id AND fecha_venta >= '$fecha_conteo' AND bodega_id = $bodega_id;";        
        
        $response = $this ->consult($sql);
        return $response;
    }      
    
    function setFecha_inventario($fecha_inventario) {
        $this->fecha_inventario = $fecha_inventario;
    }

    function setDiferencia_costo($diferencia_costo) {
        $this->diferencia_costo = $diferencia_costo;
    }

    function setDiferencia_venta($diferencia_venta) {
        $this->diferencia_venta = $diferencia_venta;
    }

    
    public function save()
    {
        $sql="INSERT INTO $this->table (bodega_id, usuario_id, fecha_registro, fecha_inventario, diferencia_costo, diferencia_venta)
                VALUES(
                       '".$this->bodega_id."',
                       '".$this->usuario_id."',
                       '".$this->fecha_registro."',
                       '".$this->fecha_inventario."',
                       '".$this->diferencia_costo."',
                       '".$this->diferencia_venta."');";
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

    public function update()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            fecha_compra = '$this->fecha_compra',
            fecha_ingreso = '$this->fecha_ingreso',
            proveedor = '$this->proveedor',
            bodega_id='$this->bodega_id',
            neto='$this->neto',
            iva='$this->iva',
            total='$this->total'
        WHERE 
            id=$this->id;
        ";

        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }
 
}
?>