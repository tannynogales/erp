<?php
class Caja_Movimientos extends MyPDO{
    
    public $table = "caja_movimientos";
    
    public 
            $id, 
            $caja_usuario_id, 
            $monto, 
            $movimiento_fecha,
            $movimiento_registro,
            $movimiento_usuario_id;
    
    public function __construct() 
    {
        
    }
    
    function getMovimiento_registro() {
        return $this->movimiento_registro;
    }

    function setMovimiento_registro($movimiento_registro) {
        $this->movimiento_registro = $movimiento_registro;
    }
    
    function getId() {
        return $this->id;
    }

    function getCaja_usuario_id() {
        return $this->caja_usuario_id;
    }

    function getMonto() {
        return $this->monto;
    }

    function getMovimiento_fecha() {
        return $this->movimiento_fecha;
    }

    function getMovimiento_usuario_id() {
        return $this->movimiento_usuario_id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCaja_usuario_id($caja_usuario_id) {
        $this->caja_usuario_id = $caja_usuario_id;
    }

    function setMonto($monto) {
        $this->monto = $monto;
    }

    function setMovimiento_fecha($movimiento_fecha) {
        $this->movimiento_fecha = $movimiento_fecha;
    }

    function setMovimiento_usuario_id($movimiento_usuario_id) {
        $this->movimiento_usuario_id = $movimiento_usuario_id;
    }

    public function save()
    {
        $sql="INSERT INTO $this->table (caja_usuario_id, monto, movimiento_fecha, movimiento_registro, movimiento_usuario_id)
                VALUES(
                       '".$this->caja_usuario_id."',
                       '".$this->monto."',
                       '".$this->movimiento_fecha."',  
                       '".$this->movimiento_registro."',  
                       '".$this->movimiento_usuario_id."');";
        $response = $this ->consult($sql);
	if ($response)
	{
            $query_2 = " SELECT LAST_INSERT_ID() as 'LAST_INSERT_ID'";
            $result_2 = $this -> consult($query_2);
            return 	$result_2[0]["LAST_INSERT_ID"];	
        }else        
        return FALSE;
    }

    function getCaja() 
    {
        $sql = " 
        SELECT 
            caja_movimientos.*,
            usuario.nombre as `caja_usuario_nombre`,
            SUM(caja_movimientos.MONTO) AS `monto_suma`
        FROM 
                caja_movimientos
        INNER JOIN usuario ON caja_movimientos.caja_usuario_id = usuario.id
        GROUP BY caja_movimientos.caja_usuario_id     
        ";
        $response = $this ->consult($sql);
        return $response;        
    }    

    function getDetalle($caja_usuario_id) 
    {
        $sql = " 
        SELECT 
            caja_movimientos.*,
            usuario.nombre as `caja_usuario_nombre`
        FROM 
                caja_movimientos
        INNER JOIN usuario ON caja_movimientos.caja_usuario_id = usuario.id
        WHERE caja_usuario_id = $caja_usuario_id
        ";
        $response = $this ->consult($sql);
        return $response;        
    }   
    
}
?>
