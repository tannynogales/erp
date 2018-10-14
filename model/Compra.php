<?php
class Compra extends MyPDO{
    public $table = "compra";
    
    public 
            $id, 
            $fecha_registro, 
            $fecha_compra, 
            $fecha_ingreso, 
            $comentario, 
            $proveedor, 
            $bodega_id,
            $neto,
            $iva,
            $total,
            $usuario_id;
            
    public function __construct() 
    {
        
    }

    function getId() {
        return $this->id;
    }

    function getFecha_registro() {
        return $this->fecha_registro;
    }

    function getFecha_compra() {
        return $this->fecha_compra;
    }

    function getFecha_ingreso() {
        return $this->fecha_ingreso;
    }

    function getComentario() {
        return $this->comentario;
    }

    function getProveedor() {
        return $this->proveedor;
    }

    function getBodega_id() {
        return $this->bodega_id;
    }

    function getNeto() {
        return $this->neto;
    }

    function getIva() {
        return $this->iva;
    }

    function getTotal() {
        return $this->total;
    }

    function getUsuario_id() {
        return $this->usuario_id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    function setFecha_compra($fecha_compra) {
        $this->fecha_compra = $fecha_compra;
    }

    function setFecha_ingreso($fecha_ingreso) {
        $this->fecha_ingreso = $fecha_ingreso;
    }

    function setComentario($comentario) {
        $this->comentario = $comentario;
    }

    function setProveedor($proveedor) {
        $this->proveedor = $proveedor;
    }

    function setBodega_id($bodega_id) {
        $this->bodega_id = $bodega_id;
    }

    function setNeto($neto) {
        $this->neto = $neto;
    }

    function setIva($iva) {
        $this->iva = $iva;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    function setUsuario_id($usuario_id) {
        $this->usuario_id = $usuario_id;
    }    
    
    public function save()
    {
        $sql="INSERT INTO $this->table (fecha_registro, fecha_compra, fecha_ingreso, comentario, proveedor, bodega_id, usuario_id, neto, iva, total)
                VALUES(
                       '".$this->fecha_registro."',
                       '".$this->fecha_compra."',
                       '".$this->fecha_ingreso."',
                       '".$this->comentario."',
                       '".$this->proveedor."',
                       '".$this->bodega_id."',
                       '".$this->usuario_id."',    
                       '".$this->neto."',  
                       '".$this->iva."',     
                       '".$this->total."');";
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
            comentario = '$this->comentario',
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