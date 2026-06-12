<?php

require_once __DIR__ . '/../app/config/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'HomepageController@homepage');
$router->get('/login', 'HomepageController@login');

$router->run();