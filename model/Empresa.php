<?php
class Empresa extends MyPDO
{

    Public  $table = "empresa";
    Public $id, $rol, $nombre, $direccion, $fono;

    function getId() {
        return $this->id;
    }

    function getRol() {
        return $this->rol;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getDireccion() {
        return $this->direccion;
    }

    function getFono() {
        return $this->fono;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setRol($rol) {
        $this->rol = $rol;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setFono($fono) {
        $this->fono = $fono;
    }

    public function __construct() 
    {
    }

}
?>