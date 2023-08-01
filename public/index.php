<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DudeGenuine\PHP\MVC\App\Router;
use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Controller\{HomeController, UserController};

Database::getConnection('prod');

// HomeController
Router::add(
    'GET',
    '/',
    HomeController::class,
    'view'
);

// HomeController
Router::add(
    'GET',
    '/users/register',
    UserController::class,
    'viewRegister'
);
Router::add(
    'POST',
    '/users/register',
    UserController::class,
    'submitRegister'
);
Router::run();