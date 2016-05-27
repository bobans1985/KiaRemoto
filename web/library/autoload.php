<?php
require_once 'library.php';

function __autoload($class_name) {
    include __DIR__ . 'autoload.php/' .str_replace('\\', '/', $class_name)  . '.php';
}


