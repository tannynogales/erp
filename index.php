<?php
// Notificar todos los errores de PHP
error_reporting(E_ALL);


//Funciones para el controlador frontal
require_once 'core/FrontController.php';
               
       
        //define("__SERVER_NAME__", $_SERVER["SERVER_NAME"]);     //ejemplo, 127.0.0.1
        //define("__DOCUMENT_ROOT__", $_SERVER["DOCUMENT_ROOT"]); //ejemplo, C:/Users/tnogales/Dropbox/htdocs
        //define("__URI__", $_SERVER["REQUEST_URI"]);             //ejemplo, /PHP-MVC-BASE/
        /*
        echo __DOCUMENT_ROOT__."<br>";
        echo __URI__."<br>";
        echo $_SERVER["PHP_SELF"]."<br>";
        */
        //Gestor de plantillas PHP Twig
        define("__PATH__", $_SERVER["DOCUMENT_ROOT"]."/ERP/");
        
        /*
        require_once __PATH__.'lib/Twig/Autoloader.php';
        Twig_Autoloader::register(); //clase:metodo 
        */
        require('vendor/autoload.php');
        
        $FrontController = new FrontController();
