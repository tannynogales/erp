<?php
class venta_tipo extends MyPDO{

    public 
           $id, $nombre, $estado, $venta_tipo;
    
    Public  $table = "venta_tipo";
 
   
    public function __construct() 
    {
        
    }
    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getEstado() {
        return $this->estado;
    }

    function getVenta_tipo() {
        return $this->venta_tipo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setVenta_tipo($venta_tipo) {
        $this->venta_tipo = $venta_tipo;
    }


}