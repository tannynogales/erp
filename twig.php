<?php

require_once 'lib/twig/Autoloader.php';
Twig_Autoloader::register();

//loader for template files  
$loader = new Twig_Loader_Filesystem('tpl');  

//twig instance  
$twig = new Twig_Environment($loader); //, array('cache' => 'cache')  

//load template file  
$template = $twig->loadTemplate('hijo.twig');  

//render a template  
echo $template->render([
    'title' => 'Welcome to Twig see template',
    'age' => 16,
    'users' => array
    (
        array('name' => 'max', 'age' =>18),
        array('name' => 'Joe', 'age' =>22)
    )
]);