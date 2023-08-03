<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DudeGenuine\PHP\MVC\App\Router;
use DudeGenuine\PHP\MVC\Middleware\AuthorizedMiddleware;
use DudeGenuine\PHP\MVC\Middleware\UnAuthorizedMiddleware;
use DudeGenuine\PHP\MVC\Controller\{HomeController, UserController};

// HomeController
Router::add(
    'GET',
    '/',
    HomeController::class,
    'view'
);

// UserController
Router::add(
    'GET',
    '/users/register',
    UserController::class,
    'viewRegister',
    [AuthorizedMiddleware::class]
);
Router::add(
    'POST',
    '/users/register',
    UserController::class,
    'submitRegister',
    [AuthorizedMiddleware::class]
);
Router::add(
    'GET',
    '/users/login',
    UserController::class,
    'viewLogin',
    [AuthorizedMiddleware::class]
);
Router::add(
    'POST',
    '/users/login',
    UserController::class,
    'submitLogin',
    [AuthorizedMiddleware::class]
);
Router::add(
    'GET',
    '/users/logout',
    UserController::class,
    'logout',
    [UnAuthorizedMiddleware::class]
);
Router::run();