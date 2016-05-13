<?php
require_once 'library.php';

//use Monolog\Logger;
//use Monolog\Handler\StreamHandler;
//use Monolog\Handler\FirePHPHandler;
//Global settings
//$WsdlHost='http://my-finances.ru/wsdl/';

function __autoload($class_name) {
    include __DIR__ . 'autoload.php/' .str_replace('\\', '/', $class_name)  . '.php';
}


