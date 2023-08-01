<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DudeGenuine\PHP\MVC\App\Router;
use DudeGenuine\PHP\MVC\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/about', HomeController::class, 'about');

Router::run();