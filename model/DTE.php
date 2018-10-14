<?php
class DTE extends MyPDO{
    public $table = "dte";
    public
            $id, 
            $folio, 
            $FchEmis, 
            $RUTRecep, 
            $MntNeto, 
            $IVA, 
            $MntTotal, 
            $CdgVendedor, 
            $FechRegistro, 
            $tipoDTE, 
            $validado,
            $estado,
            $tiene_productos;
    
    public function __construct() 
    {
        
    }
    
    function getId() {
        return $this->id;
    }
    
    function getTiene_productos() {
        return $this->tiene_productos;
    }
    
    function getEstado() {
        return $this->estado;
    }

    function getFolio() {
        return $this->folio;
    }

    function getFchEmis() {
        return $this->FchEmis;
    }

    function getRUTRecep() {
        return $this->RUTRecep;
    }

    function getMntNeto() {
        return $this->MntNeto;
    }

    function getIVA() {
        return $this->IVA;
    }

    function getMntTotal() {
        return $this->MntTotal;
    }

    function getCdgVendedor() {
        return $this->CdgVendedor;
    }

    function getFechRegistro() {
        return $this->FechRegistro;
    }

    function getTipoDTE() {
        return $this->tipoDTE;
    }

    function getValidado() {
        return $this->validado;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTiene_productos($tiene_productos) {
        $this->tiene_productos= $tiene_productos;
    }
    
    function setFolio($folio) {
        $this->folio = $folio;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setFchEmis($FchEmis) {
        $this->FchEmis = $FchEmis;
    }

    function setRUTRecep($RUTRecep) {
        $this->RUTRecep = $RUTRecep;
    }

    function setMntNeto($MntNeto) {
        $this->MntNeto = $MntNeto;
    }

    function setIVA($IVA) {
        $this->IVA = $IVA;
    }

    function setMntTotal($MntTotal) {
        $this->MntTotal = $MntTotal;
    }

    function setCdgVendedor($CdgVendedor) {
        $this->CdgVendedor = $CdgVendedor;
    }

    function setFechRegistro($FechRegistro) {
        $this->FechRegistro = $FechRegistro;
    }

    function setTipoDTE($tipoDTE) {
        $this->tipoDTE = $tipoDTE;
    }

    function setValidado($validado) {
        $this->validado = $validado;
    }
    
    public function validar()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            validado=$this->validado
        WHERE 
            folio=$this->folio;
        ";
        //echo $sql; exit();
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }
    
    public function save()
    {
        $sql="INSERT INTO $this->table (folio, FchEmis, RUTRecep, MntNeto, IVA, MntTotal, CdgVendedor, FechRegistro, tipoDTE, validado, estado)
                VALUES(
                       '".$this->folio."',
                       '".$this->FchEmis."',
                       '".$this->RUTRecep."',
                       '".$this->MntNeto."',
                       '".$this->IVA."',    
                       '".$this->MntTotal."',
                       '".$this->CdgVendedor."',   
                       '".$this->FechRegistro."',
                       '".$this->tipoDTE."',
                       '".$this->validado."',
                       '".$this->estado."');";
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
    public function update_tiene_productos()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            tiene_productos='$this->tiene_productos'
        WHERE 
            id=$this->id;
        ";
        $response = $this ->consult($sql);
        return $response;
    }
    function max($fieldName, $tipoDTE=33)
    {
        $sql =
        "SELECT 
            max($fieldName) as 'max'
        FROM 
            $this->table
        WHERE tipoDTE = $tipoDTE;";
        
        $response = $this ->consult($sql);
        return ($response[0]["max"]+0);
    }    
}
?>