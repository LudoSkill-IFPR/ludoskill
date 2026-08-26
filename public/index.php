<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'HomepageController@homepage');

$router->get('/login', 'HomepageController@login');
$router->post('/logar', 'AutenticacaoController@logar');
$router->get('/logout', 'AutenticacaoController@logout');



//modulos
$router->get('/administrador/modulos', 'ModuloController@listarTodos');
$router->get('/administrador/modulos/cadastrar', 'ModuloController@criar');
$router->get('/administrador/modulos/editar', 'ModuloController@editar');
$router->post('/administrador/modulos/salvar', 'ModuloController@salvar');
$router->post('/administrador/modulos/atualizar', 'ModuloController@atualizar');
$router->post('/administrador/modulos/excluir', 'ModuloController@excluir');

//empresa
$router->get('/administrador/empresas', 'EmpresaController@listarTodos');
$router->get('/administrador/empresas/cadastrar', 'EmpresaController@criar');
$router->get('/administrador/empresas/editar', 'EmpresaController@editar');
$router->post('/administrador/empresas/salvar', 'EmpresaController@salvar');
$router->post('/administrador/empresas/atualizar', 'EmpresaController@atualizar');
$router->post('/administrador/empresas/excluir', 'EmpresaController@excluir');

//itens
$router->get('/administrador/itens', 'ItemController@listarTodos');
$router->get('/administrador/itens/cadastrar', 'ItemController@criar');
$router->get('/administrador/itens/editar', 'ItemController@editar');
$router->post('/administrador/itens/salvar', 'ItemController@salvar');
$router->post('/administrador/itens/atualizar', 'ItemController@atualizar');
$router->post('/administrador/itens/excluir', 'ItemController@excluir');

//funcionarios
$router->get('/gestor/funcionarios', 'FuncionarioController@listarTodos');
$router->get('/gestor/funcionarios/cadastrar', 'FuncionarioController@criar');
$router->get('/gestor/funcionarios/editar', 'FuncionarioController@editar');
$router->post('/gestor/funcionarios/salvar', 'FuncionarioController@salvar');
$router->post('/gestor/funcionarios/atualizar', 'FuncionarioController@atualizar');
$router->post('/gestor/funcionarios/excluir', 'FuncionarioController@excluir');

$router->get('/cadastro-nao-liberado', 'AutenticacaoController@cadastroNaoLiberado');

$router->get('/administrador/inicial', 'AdministradorController@inicial');
$router->get('/gestor/inicial', 'GestorController@inicial');
$router->get('/funcionario/inicial', 'FuncionarioController@inicial');

$router->get('/funcionario/loja', 'ItemController@listarTodosFuncionario');
$router->get('/funcionario/modulos', 'ModuloController@listarTodosFuncionario');

$router->run();
