<?php
class Contribuyente extends MyPDO{

    public $table = "contribuyente";
    
    public
            $id, 
            $rut, 
            $razon_social, 
            $giro, 
            $direccion, 
            $comuna, 
            $correo, 
            $contacto;
    
    public function __construct() 
    {
        
    }
    
    function getId() {
        return $this->id;
    }

    function getRut() {
        return $this->rut;
    }

    function getRazon_social() {
        return $this->razon_social;
    }

    function getGiro() {
        return $this->giro;
    }

    function getDireccion() {
        return $this->direccion;
    }

    function getComuna() {
        return $this->comuna;
    }

    function getCorreo() {
        return $this->correo;
    }

    function getContacto() {
        return $this->contacto;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setRut($rut) {
        $this->rut = $rut;
    }

    function setRazon_social($razon_social) {
        $this->razon_social = $razon_social;
    }

    function setGiro($giro) {
        $this->giro = $giro;
    }

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setComuna($comuna) {
        $this->comuna = $comuna;
    }

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setContacto($contacto) {
        $this->contacto = $contacto;
    }
    /*
    public function save()
    {
        $sql="INSERT INTO $this->table (rut, razon_social, giro, direccion, comuna, correo, contacto)
                VALUES(
                       '".$this->rut."',
                       '".$this->razon_social."',
                       '".$this->giro."',
                       '".$this->direccion."',
                       '".$this->comuna."',    
                       '".$this->correo."',
                       '".$this->contacto."');";
        //echo $sql;
        $response = $this ->consult($sql);
	if ($response)
	{
            $query_2 = " SELECT LAST_INSERT_ID() as 'objeto_id'";
            $result_2 = $this -> consult($query_2);
            return 	$result_2[0]["objeto_id"];	
        }else        
        return FALSE;
    }
    */
    function save()
    {
        $sql= "
        INSERT INTO contribuyente 
            (`rut`, `razon_social`, `giro`, `direccion`, `comuna`, `correo`, `contacto`) 
        VALUES 
            ($this->rut, '$this->razon_social', '$this->giro', '$this->direccion', '$this->comuna', '$this->correo', '$this->contacto') 
        ON DUPLICATE KEY 
        UPDATE 
            razon_social='$this->razon_social', 
            giro='$this->giro',
            direccion='$this->direccion', 
            comuna='$this->comuna', 
            correo='$this->correo', 
            contacto='$this->contacto';";        
    
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
        
    }
}