<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'HomepageController@homepage');
$router->get('/login', 'HomepageController@login');
$router->post('/logar', 'AutenticacaoController@logar');

$router->post('/funcionario/salvar', 'FuncionarioController@salvar');
$router->get('/funcionario/cadastrar', 'FuncionarioController@salvar');
$router->get('/funcionario/lista', 'FuncionarioController@listarTodos');

$router->run();