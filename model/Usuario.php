<?php
class Usuario extends MyPDO{
    private $id;
    private $rut;
    private $nombre;
    private $apellido;
    private $aMaterno;
    private $email;
    private $password;
    public $table = "usuario";
    
    public function __construct() 
    {
        
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    
    public function setAMaterno($aMaterno) {
        $this->aMaterno = $aMaterno;
    }
    
    public function getAMaterno() {
        return $this->aMaterno;
    }
    
    public function getApellido() {
        return $this->apellido;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getRut() {
        return $this->password;
    }

    public function setRut($rut) {
        $this->rut = $rut;
    }
    
    public function save()
    {
        $sql="INSERT INTO $this->table (rut, nombre,aPaterno,aMaterno,email,password)
                VALUES(
                       '".$this->rut."',
                       '".$this->nombre."',
                       '".$this->apellido."',
                       '".$this->aMaterno."',    
                       '".$this->email."',
                       '".$this->password."');";
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }

    public function update()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            rut='$this->rut',
            nombre='$this->nombre',
            aPaterno='$this->apellido',
            aMaterno='$this->aMaterno',
            email='$this->email',
            password='$this->password'
        WHERE 
            id=$this->id;
        ";
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }
    /*
    function deleteByRut($rut)
    {
        $sql = "DELETE FROM $this->table WHERE rut = $rut;";
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;        
    }
    */
}
?>