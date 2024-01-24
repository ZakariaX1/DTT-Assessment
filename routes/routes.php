<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/hello', App\Controllers\IndexController::class . '@create(\"Test\")');
$router->get('/', App\Controllers\IndexController::class . '@test');
