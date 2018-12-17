<?php

require 'vendor/autoload.php';

$router = new App\Router($_GET['url']);

$router->get('/', function () {
    echo "Hello World!";
});