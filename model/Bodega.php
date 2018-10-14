<?php
class Bodega extends MyPDO
{
    private $id;
    private $empresa_id;
    private $nombre;
    Public  $table = "bodega";
 
    function getId() {
        return $this->id;
    }

    function getEmpresa_id() {
        return $this->empresa_id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setEmpresa_id($empresa_id) {
        $this->empresa_id = $empresa_id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

        
    public function __construct() 
    {
    }
    

}
?>