<?php
class Comuna extends MyPDO
{
    Public  $table = "comuna";
    private $id, $codigo, $nombre;
    
    public function __construct() 
    {
    }
    
    function getId() {
        return $this->id;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getNombre() {
        return $this->nombre;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }


}