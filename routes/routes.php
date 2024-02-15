<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/facility', App\Controllers\FacilityController::class . '@getAll');
$router->post('/facility', App\Controllers\FacilityController::class . '@create');
$router->get('/facility/search', App\Controllers\FacilityController::class . '@search');
$router->get('/facility/{id}', App\Controllers\FacilityController::class . '@getById');
$router->post('/facility/{id}', App\Controllers\FacilityController::class . '@update'); // Post not Put for the use of the _POST variable
$router->delete('/facility/{id}', App\Controllers\FacilityController::class . '@delete');
$router->get('/', App\Controllers\IndexController::class . '@notFound');
