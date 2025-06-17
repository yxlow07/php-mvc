<?php

use app\Controllers\AuthController;

$routes = new \core\Router\RoutesCollector();

$routes::GETPOST('/register', [AuthController::class, 'register', 'renderRegisterPage']);