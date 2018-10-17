<?php
class Factura extends MyPDO{
    public $table = "venta";
    public
            $id, 
            $fecha_venta, 
            $bodega_id, 
            $fecha_registro, 
            $usuario_id, 
            $neto, 
            $iva, 
            $comentario,
            $total,
            $venta_tipo_id,
            $validado,
            $folio_dte,
            $contribuyente_id,
            $efectivo, 
            $vuelto;
    
    public function __construct() 
    {
        
    }
    
    function setEfectivo($efectivo) {
        $this->efectivo = $efectivo;
    }

    function getEfectivo() {
        return $this->efectivo;
    }
    
    function setVuelto($vuelto) {
        $this->vuelto = $vuelto;
    }

    function getVuelto() {
        return $this->vuelto;
    }   
    
    function setValidado($validado) {
        $this->validado = $validado;
    }

    function getContribuyente_id() {
        return $this->contribuyente_id;
    }
    
    function setContribuyente_id($contribuyente_id) {
        $this->contribuyente_id = $contribuyente_id;
    }
    
    function getValidado() {
        return $this->validado;
    }
    
    function setVenta_tipo_id($venta_tipo_id) {
        $this->venta_tipo_id = $venta_tipo_id;
    }
    
    function setFolio_dte($folio_dte) {
        $this->folio_dte = $folio_dte;
    }
    
    function getFolio_dte() {
        return $this->folio_dte;
    }
    
    function getVenta_tipo_id() {
        return $this->venta_tipo_id;
    }

    function getId() {
        return $this->id;
    }

    function getFecha_venta() {
        return $this->fecha_venta;
    }

    function getBodega_id() {
        return $this->bodega_id;
    }

    function getFecha_registro() {
        return $this->fecha_registro;
    }

    function getUsuario_id() {
        return $this->usuario_id;
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

    function setId($id) {
        $this->id = $id;
    }

    function setFecha_venta($fecha_venta) {
        $this->fecha_venta = $fecha_venta;
    }

    function setBodega_id($bodega_id) {
        $this->bodega_id = $bodega_id;
    }

    function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    function setUsuario_id($usuario_id) {
        $this->usuario_id = $usuario_id;
    }
    
    function setComentario($comentario) {
        $this->comentario = $comentario;
    }

    function setProveedor($proveedor) {
        $this->proveedor = $proveedor;
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
    
    public function getAll($select = [], $order = [])
    {
        $sql = "
        SELECT
        *
        FROM
        (
            SELECT 
                venta.id, 
                DATE_FORMAT(fecha_venta, '%d/%m/%Y') AS 'fecha_venta', 
                fecha_venta AS 'fecha_venta_mysql', 
                contribuyente.rut as 'cliente',
                bodega.nombre as 'bodega', 
                DATE_FORMAT(fecha_registro, '%d/%m/%Y') AS 'fecha_registro', 
                usuario.nombre as 'usuario', 
                neto, 
                IF(venta_tipo_id = 2, 0, iva) as 'iva',
                IF(venta_tipo_id = 2, neto, total) as 'total',
                validado, 
                venta_tipo.nombre as 'venta_tipo', 
                venta_tipo_id as 'venta_tipo_id', 
                'venta' as 'fuente',
                'estado' as 'estado'
                FROM 
                    venta
                INNER JOIN usuario
                    ON usuario.id = usuario_id
                    INNER JOIN venta_tipo
                    ON venta_tipo.id = venta_tipo_id
                    INNER JOIN bodega
                    ON bodega.id = bodega_id
                    LEFT JOIN contribuyente
                    ON contribuyente.id = contribuyente_id

            UNION

            SELECT
		folio as 'id', 
                DATE_FORMAT(FchEmis, '%d/%m/%Y') AS 'fecha_venta',
                FchEmis AS 'fecha_venta_mysql', 
                LEFT (RUTRecep, LENGTH(RUTRecep)-1) as 'cliente',  
                'Recoleta' as 'bodega',
                DATE_FORMAT(FechRegistro, '%d/%m/%Y') as 'fecha_registro',
                CdgVendedor as 'usuario',
                MntNeto as 'neto', 
                IVA as 'iva', 
                MntTotal as 'total', 
                validado as 'validado',
                IF(tipoDTE = 33, 'Factura E.', tipoDTE) as 'venta_tipo',
                tipoDTE as 'venta_tipo_id',
                'dte' as 'fuente',
                estado as 'estado'
            FROM 
                dte 
            WHERE tipoDTE = 33
        ) AS ventas
        -- WHERE ventas.id = 1 or ventas.id = 2
        ORDER BY fecha_venta_mysql DESC, id DESC";
        //exit($sql);
        $response = $this ->consult($sql);
        return $response;
    }
    public function getNotaCreditoAll()
    {
        $sql = "
        SELECT
            folio as 'id', 
            DATE_FORMAT(FchEmis, '%d/%m/%Y') AS 'fecha_venta',
            FchEmis AS 'fecha_venta_mysql', 
            LEFT (RUTRecep, LENGTH(RUTRecep)-1) as 'cliente',  
            'Recoleta' as 'bodega',
            DATE_FORMAT(FechRegistro, '%d/%m/%Y') as 'fecha_registro',
            CdgVendedor as 'usuario',
            FolioRef as 'FolioRef',
            MntNeto as 'neto', 
            IVA as 'iva', 
            MntTotal as 'total', 
            validado as 'validado',
            tipoDTE as 'venta_tipo_id',
            'dte' as 'fuente',
            estado as 'estado'
        FROM 
            dte 
        WHERE tipoDTE = 61
        ORDER BY FchEmis DESC, id DESC";
        //exit($sql);
        $response = $this ->consult($sql);
        return $response;
    }    
    function ventas_by_mes()
    {
        $sql = "
        SELECT 
                YEAR(fecha) AS 'ano', 
                MONTH(fecha) AS 'mes', 
            sum(neto) as 'venta_neto'	
        FROM
          (  
            SELECT 
                fecha_venta as 'fecha', 
                neto AS 'neto' 
            FROM 
                venta
                        
            UNION
            
            SELECT 
                FchEmis as 'fecha', 
                MntNeto as 'neto' 
            FROM 
                dte
            WHERE tipoDTE = 33

            UNION
            
            SELECT 
                FchEmis as 'fecha', 
                MntNeto*-1 as 'neto' 
            FROM 
                dte
            WHERE tipoDTE = 61

        ) as ventas
        GROUP BY MONTH(fecha), YEAR(fecha)
        ORDER BY YEAR(fecha) DESC, MONTH(fecha) DESC;";
        
        $response = $this ->consult($sql);
        return $response;
    }
    
    public function save()
    {
        $sql="INSERT INTO $this->table (fecha_venta, bodega_id, fecha_registro, usuario_id, comentario, neto, iva, venta_tipo_id, total, efectivo, vuelto, contribuyente_id)
                VALUES(
                       '".$this->fecha_venta."',
                       '".$this->bodega_id."',
                       '".$this->fecha_registro."',
                       '".$this->usuario_id."',
                       '".$this->comentario."',    
                       '".$this->neto."',
                       '".$this->iva."',   
                       '".$this->venta_tipo_id."',
                       '".$this->total."',
                       '".$this->efectivo."',
                       '".$this->vuelto."',
                       '".$this->contribuyente_id."');";
        //echo $sql;
        $response = $this ->consult($sql);
	if ($response)
	{
            $query_2 = "SELECT LAST_INSERT_ID() as 'producto_id'";
            $result_2 = $this -> consult($query_2);
            return 	$result_2[0]["producto_id"];	
        }else        
        return FALSE;
    }
    
    public function validar()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            validado=$this->validado
        WHERE 
            id=$this->id;
        ";
        //echo $sql; exit();
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
            bodega_id='$this->bodega_id',
            comentario = '$this->comentario',    
            neto='$this->neto',
            iva='$this->iva',
            fecha_venta='$this->fecha_venta',    
            venta_tipo_id='$this->venta_tipo_id',
            total='$this->total'
        WHERE 
            id=$this->id;
        ";
        //echo $sql; exit();
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }

    public function update_folio_DTE()
    {
        $sql="
        UPDATE 
            $this->table
        SET 
            folio_dte='$this->folio_dte'
        WHERE 
            id=$this->id;
        ";
        //echo $sql; exit();
        $response = $this ->consult($sql);
        //var_dump($response);
        return $response;
    }    
}
?>