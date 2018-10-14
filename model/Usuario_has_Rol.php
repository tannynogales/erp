<?php
class Usuario_has_Rol extends MyPDO{
    public $usuario_id, $rol_id;
    public $table = "usuario_has_rol";
            
    public function __construct() 
    {
        
    }

    public function getBy($value, $fieldName = 'usuario_id', $order = [])
    {
        $sql="
        SELECT 
                usuario.id as 'usuario_id', 
            usuario.rut as 'usuario_rut', 
            usuario.nombre as 'usuario_nombre', 
            usuario_has_rol.rol_id as 'rol_id',
            rol.nombre as 'rol_nombre'
        FROM 
                $this->table 
            INNER JOIN usuario ON (usuario_has_rol.usuario_id=usuario.id)
            INNER JOIN rol     ON (usuario_has_rol.rol_id=rol.id)
        WHERE 
                $fieldName = '$value';";
         
        
        $response = $this ->consult($sql);
        return $response;
    }       
 
}