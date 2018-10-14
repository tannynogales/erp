<?php
class BasisEntity{
    private $table; 
    private $DBConnector;
    //private $conectar;

    public function __construct($table, $DBConnector) {
        $this->table=(string) $table;
        
	/*
        require_once 'Conectar.php';
        $this->conectar=new Conectar();
        $this->db=$this->conectar->conexion();
	 */
	//$this->conectar = null;
	$this->DBConnector = $DBConnector;
    }
    /*
    public function getConetar(){
        return $this->conectar;
    }
    */
    public function getDBConnector(){
        return $this->DBConnector;
    }
    
    public function getAll(){
        $query=$this->DBConnector->query("SELECT * FROM $this->table ORDER BY id DESC");

        while ($row = $query->fetch_object()) {
           $resultSet[]=$row;
        }
        
        return $resultSet;
    }
    
    public function getById($id){
        $query=$this->DBConnector->query("SELECT * FROM $this->table WHERE id=$id");

        if($row = $query->fetch_object()) {
           $resultSet=$row;
        }
        
        return $resultSet;
    }
    
    public function getBy($column,$value){
        $query=$this->DBConnector->query("SELECT * FROM $this->table WHERE $column='$value'");

        while($row = $query->fetch_object()) {
           $resultSet[]=$row;
        }
        
        return $resultSet;
    }
    
    public function deleteById($id){
        $query=$this->DBConnector->query("DELETE FROM $this->table WHERE id=$id"); 
        return $query;
    }
    
    public function deleteBy($column,$value){
        $query=$this->DBConnector->query("DELETE FROM $this->table WHERE $column='$value'"); 
        return $query;
    }
    

    /*
     * Aqui podemos montarnos un monton de métodos que nos ayuden
     * a hacer operaciones con la base de datos de la entidad
     */
    
}
?>
