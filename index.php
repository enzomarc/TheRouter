<?php

require 'vendor/autoload.php';

$router = new App\Router\Router($_GET['url']);

$router->get('/contact', function () {
    echo "Poison is coming !";
})->name('contact');

$router->get('/', function () {
    echo "Je suis la page accueil<br>";
    echo $router->url('contact');
});

$router->run();