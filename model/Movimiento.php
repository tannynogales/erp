<?php
class Movimiento extends MyPDO
{
    
    public $table = "movimiento_de_stock";    
    public $id, $fecha_movimiento, $fecha_registro, $usuario_id, $bodega_id_entra, $bodega_id_sale;
    
    public function __construct() 
    {
        
    }

    function getId() {
        return $this->id;
    }

    function getFecha_movimiento() {
        return $this->fecha_movimiento;
    }

    function getFecha_registro() {
        return $this->fecha_registro;
    }

    function getUsuario_id() {
        return $this->usuario_id;
    }

    function getBodega_id_entra() {
        return $this->bodega_id_entra;
    }

    function getBodega_id_sale() {
        return $this->bodega_id_sale;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFecha_movimiento($fecha_movimiento) {
        $this->fecha_movimiento = $fecha_movimiento;
    }

    function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    function setUsuario_id($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    function setBodega_id_entra($bodega_id_entra) {
        $this->bodega_id_entra = $bodega_id_entra;
    }

    function setBodega_id_sale($bodega_id_sale) {
        $this->bodega_id_sale = $bodega_id_sale;
    }

    public function save()
    {
        $sql="INSERT INTO $this->table (fecha_movimiento, fecha_registro, usuario_id, bodega_id_entra, bodega_id_sale)
                VALUES(
                       '".$this->fecha_movimiento."',
                       '".$this->fecha_registro."',
                        '".$this->usuario_id."',
                       '".$this->bodega_id_entra."',
                       '".$this->bodega_id_sale."');";
        //echo $sql;
        $response = $this ->consult($sql);
	if ($response)
	{
            $query_2 = "SELECT LAST_INSERT_ID() as 'producto_id'";
            $result_2 = $this -> consult($query_2);
            return 	$result_2[0]["producto_id"];	
        }else        
        return FALSE;
    }
    
    function update()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            fecha_movimiento='$this->fecha_movimiento',
            bodega_id_entra='$this->bodega_id_entra',
            bodega_id_sale='$this->bodega_id_sale'
        WHERE 
            id=$this->id;
        ";
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;        
    }
}
?>