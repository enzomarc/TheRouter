<?php

require 'vendor/autoload.php';

var_dump($_GET);
die();

$router = new App\Router($_GET['url']);

$router->get('/', function () {
    echo "Hello World!";
});