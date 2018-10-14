<?php
class Producto extends MyPDO{

    public 
            $id, 
            $empresa_id, 
            $codigo, 
            $nombre, 
            $precio, 
            $costo, 
            $descripcion_larga, 
            $muestra_precio, 
            $habilitado, 
            $destacado, 
            $exento,
            $descripcion_corta,
            $validado,
            $eliminado,
            $precio_mayorista,
            $precio_web;
    
    Public  $table = "producto";
 
   
    public function __construct() 
    {
        $this->setEliminado(0);
    }
    function getId() {
        return $this->id;
    }

    function getEmpresa_id() {
        return $this->empresa_id;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getPrecio() {
        return $this->precio;
    }

    function getCosto() {
        return $this->costo;
    }

    function getDescripcion_larga() {
        return $this->descripcion_larga;
    }

    function getMuestra_precio() {
        return $this->muestra_precio;
    }

    function getHabilitado() {
        return $this->habilitado;
    }

    function getDestacado() {
        return $this->destacado;
    }

    function getExento() {
        return $this->exento;
    }

    function getDescripcion_corta() {
        return $this->descripcion_corta;
    }

    function getValidado() {
        return $this->validado;
    }

    function getEliminado() {
        return $this->eliminado;
    }

    function getPrecio_mayorista() {
        return $this->precio_mayorista;
    }

    function getprecio_web() {
        return $this->precio_web;
    }
    
    function setId($id) {
        $this->id = $id;
    }

    function setEmpresa_id($empresa_id) {
        $this->empresa_id = $empresa_id;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }

    function setCosto($costo) {
        $this->costo = $costo;
    }

    function setDescripcion_larga($descripcion_larga) {
        $this->descripcion_larga = $descripcion_larga;
    }

    function setMuestra_precio($muestra_precio) {
        $this->muestra_precio = $muestra_precio;
    }

    function setHabilitado($habilitado) {
        $this->habilitado = $habilitado;
    }

    function setDestacado($destacado) {
        $this->destacado = $destacado;
    }

    function setExento($exento) {
        $this->exento = $exento;
    }

    function setDescripcion_corta($descripcion_corta) {
        $this->descripcion_corta = $descripcion_corta;
    }

    function setValidado($validado) {
        $this->validado = $validado;
    }

    function setEliminado($eliminado) {
        $this->eliminado = $eliminado;
    }

    function setPrecio_mayorista($precio_mayorista) {
        $this->precio_mayorista = $precio_mayorista;
    }

    function setPrecio_web($precio_web) {
        $this->precio_web = $precio_web;
    }
   
    public function save()
    {
        
        $sql="INSERT INTO $this->table (empresa_id, codigo, nombre, precio, costo, descripcion, muestra_precio, habilitado, destacado, exento, descripcion_corta, validado, eliminado, precio_mayorista, precio_web)
                VALUES(
                       '".$this->empresa_id."',
                       '".$this->codigo."',
                       '".$this->nombre."',
                       '".$this->precio."',    
                       '".$this->costo."',
                       '".$this->descripcion_larga."',  
                       '".$this->muestra_precio."',  
                       '".$this->habilitado."',  
                       '".$this->destacado."',   
                       '".$this->exento."', 
                       '".$this->descripcion_corta."', 
                       '".$this->validado."',
                       '".$this->eliminado."',
                       '".$this->precio_mayorista."',       
                       '".$this->precio_web."');";
        //exit($sql);
        $response = $this ->consult($sql);
	if ($response)
	{
            $query_2 = " SELECT LAST_INSERT_ID() as 'producto_id'";
            $result_2 = $this -> consult($query_2);
            return 	$result_2[0]["producto_id"];	
        }else
        {
            return FALSE;
        }
    }

    public function update()
    {
        $sql="
            
        UPDATE 
            $this->table
        SET
        `empresa_id` = '$this->empresa_id',
        `codigo` = '$this->codigo',
        `nombre` = '$this->nombre',
        `precio` = '$this->precio',
        `precio_mayorista` = '$this->precio_mayorista',
        `precio_web` = '$this->precio_web',
        `costo` = '$this->costo',
        `descripcion` = '$this->descripcion_larga',
        `muestra_precio` = '$this->muestra_precio',
        `habilitado` = '$this->habilitado',
        `destacado` = '$this->destacado',
        `exento` = '$this->exento',
        `descripcion_corta` = '$this->descripcion_corta',
        `validado` = '$this->validado'
        WHERE `id` = '$this->id'";

        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }
    
    public function updateEliminado($valor)
    {
        if (!isset($valor))
        {
            return FALSE;
        }
        elseif ( !is_bool ($valor) )
        {
            return FALSE;
        }
        else
        {         
            $sql="

            UPDATE 
                $this->table
            SET
            `eliminado` = '$valor'
            WHERE `id` = '$this->id'";

            $response = $this ->consult($sql);
            //var_dump($response);
            return $response;
        }
    }    

    public function getBy($fieldName, $value, $order = [])
    {
        $sql =
        "SELECT 
            *
        FROM 
            $this->table
        WHERE
            $fieldName = '$value' and 
            eliminado = 0
        ORDER BY nombre ASC
        ;";
        $response = $this ->consult($sql);
        return $response;
    }
    
    public function getLike($valor, $where = [])
    {
        $where_sql = " WHERE eliminado = 0 and ";
        $aux = 0;
        foreach ($where as $campo)
        {
            $aux = $aux + 1;
            $where_sql .= $campo.' LIKE "%'.$valor.'%"';
            if( count($where) != $aux)
            {
                $where_sql .=  ' OR ';
            }
        }
        $sql = 'SELECT * FROM '.$this->table.' WHERE eliminado = 0 and ( codigo LIKE "%'.$valor.'%" OR nombre LIKE "%'.$valor.'%");';
        //$sql = 'SELECT * FROM '.$this->table.$where_sql;
        //exit($sql);
        $response = $this ->consult($sql);
        return $response;
    }
    
}