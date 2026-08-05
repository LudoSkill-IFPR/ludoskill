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

$router->get('/administrador/modulos', 'ModuloController@listarTodos');
$router->get('/administrador/modulos/cadastrar', 'ModuloController@criar');
$router->get('/administrador/modulos/editar', 'ModuloController@editar');
$router->post('/administrador/modulos/salvar', 'ModuloController@salvar');
$router->post('/administrador/modulos/atualizar', 'ModuloController@atualizar');
$router->post('/administrador/modulos/excluir', 'ModuloController@excluir');

$router->run();