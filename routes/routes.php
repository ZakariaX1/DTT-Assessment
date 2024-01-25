<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/facility', App\Controllers\IndexController::class . '@getAll');
$router->post('/facility', App\Controllers\IndexController::class . '@create');
$router->get('/facility/search', App\Controllers\IndexController::class . '@search');
$router->get('/facility/{id}', App\Controllers\IndexController::class . '@getById');
$router->post('/facility/{id}', App\Controllers\IndexController::class . '@update'); // Post not Put for the use of the _POST variable
$router->delete('/facility/{id}', App\Controllers\IndexController::class . '@delete');
$router->get('/', App\Controllers\IndexController::class . '@test');
