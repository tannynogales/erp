<?php
class session{

	function __construct()
	{
	}
	
	function isLog()
	{
            if (   isset($_SESSION['nombre']) )
            {
                $usuario = new Usuario();
                $usuario_datos = $usuario ->getBy("id", $_SESSION['id']);
                $usuario_datos = $usuario_datos[0];
                if($usuario_datos["password"] == $_SESSION['password'])
                    return true;
                else
                    return false;
            }
            else
                return false;
	}
	function setLog($fila, $roles)
	{
            //session_start();
            $_SESSION['id']         = $fila['id'];
            $_SESSION['nombre']     = $fila['nombre'];
            $_SESSION['email']      = $fila['email'];
            $_SESSION['password']   = $fila['password'];
            $_SESSION['empresa_id'] = 1;
            $_SESSION['roles']      = $roles;
	}
        /*
	function seguridad()
	{
		if( !$this->isLog() )
		{
			$this->logOut();
			exit ("Debe iniciar sesión para entrar a esta sección");
		}
	}
        */
	function logOut()
	{
		session_unset();
		session_destroy();
	}	
}