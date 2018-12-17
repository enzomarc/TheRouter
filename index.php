<?php

require 'vendor/autoload.php';

$router = new App\Router\Router($_GET['url']);
$poison = new App\Poison\Poison();

$router->get('/contact', function () {
    $poison->render('contact.php');
})->name('contact');

$router->get('/', function () {
    echo "Je suis la page accueil<br>";
    echo $router->url('contact');
});

$router->run();