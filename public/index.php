<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'HomepageController@homepage');

$router->get('/login', 'HomepageController@login');
$router->post('/logar', 'AutenticacaoController@logar');

$router->post('/funcionarios/salvar', 'FuncionarioController@salvar');
$router->get('/funcionarios/cadastrar', 'FuncionarioController@salvar');
$router->get('/funcionarios/lista', 'FuncionarioController@listarTodos');

$router->get('/modulos', 'ModuloController@listarTodos');
$router->get('/modulos/cadastrar', 'ModuloController@criar');
$router->get('/modulos/editar', 'ModuloController@editar');
$router->post('/modulos/salvar', 'ModuloController@salvar');

$router->run();