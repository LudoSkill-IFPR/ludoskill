<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'HomepageController@homepage');

$router->get('/sobreNos', 'HomepageController@sobreNos');
$router->get('/login', 'HomepageController@login');
$router->post('/logar', 'AutenticacaoController@logar');
$router->post('/logout', 'AutenticacaoController@logout');
$router->get('/esqueci-senha', 'AutenticacaoController@esqueciSenha');
$router->post('/recuperar-senha', 'AutenticacaoController@recuperarSenha');
$router->get('/gerar-nova-senha', 'AutenticacaoController@gerarNovaSenha');
$router->post('/redefinir-senha', 'AutenticacaoController@redefinirSenha');
$router->get('/solicitar-acesso', 'SolicitacaoAcessoController@criar');
$router->post('/solicitar-acesso/enviar', 'SolicitacaoAcessoController@salvar');
$router->get('/perfil', 'UsuarioController@perfil');
$router->get('/perfil/editar', 'UsuarioController@editarPerfil');
$router->post('/perfil/atualizar', 'UsuarioController@atualizarPerfil');



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
$router->post('/administrador/itens/desativar', 'ItemController@desativar');
$router->post('/administrador/itens/ativar', 'ItemController@ativar');
$router->post('/administrador/itens/excluir', 'ItemController@excluir');

//gestores
$router->get('/administrador/gestores', 'GestorController@listarTodos');
$router->get('/administrador/gestores/cadastrar', 'GestorController@criar');
$router->get('/administrador/gestores/editar', 'GestorController@editar');
$router->post('/administrador/gestores/salvar', 'GestorController@salvar');
$router->post('/administrador/gestores/atualizar', 'GestorController@atualizar');
$router->post('/administrador/gestores/desativar', 'GestorController@desativar');
$router->post('/administrador/gestores/ativar', 'GestorController@ativar');
$router->post('/administrador/gestores/excluir', 'GestorController@excluir');

//solicitações de acesso de gestores
$router->get('/administrador/solicitacoes-acesso', 'SolicitacaoAcessoController@listar');
$router->post('/administrador/solicitacoes-acesso/aprovar', 'SolicitacaoAcessoController@aprovar');
$router->post('/administrador/solicitacoes-acesso/recusar', 'SolicitacaoAcessoController@recusar');

//atividades
$router->get('/administrador/atividades', 'AtividadeController@listarTodos');
$router->get('/administrador/atividades/cadastrar', 'AtividadeController@criar');
$router->get('/administrador/atividades/editar', 'AtividadeController@editar');
$router->post('/administrador/atividades/salvar', 'AtividadeController@salvar');
$router->post('/administrador/atividades/atualizar', 'AtividadeController@atualizar');
$router->post('/administrador/atividades/excluir', 'AtividadeController@excluir');

//exercícios
$router->get('/administrador/exercicios', 'ExercicioController@listarTodos');
$router->get('/administrador/exercicios/cadastrar', 'ExercicioController@criar');
$router->get('/administrador/exercicios/editar', 'ExercicioController@editar');
$router->post('/administrador/exercicios/salvar', 'ExercicioController@salvar');
$router->post('/administrador/exercicios/atualizar', 'ExercicioController@atualizar');
$router->post('/administrador/exercicios/excluir', 'ExercicioController@excluir');
$router->get('/funcionario/atividades/exercicios', 'ExercicioController@acessarAtividade');
$router->post('/funcionario/atividades/exercicios/responder', 'ExercicioController@responderAtividade');

//funcionarios
$router->get('/gestor/funcionarios', 'FuncionarioController@listarTodos');
$router->get('/gestor/funcionarios/cadastrar', 'FuncionarioController@criar');
$router->get('/gestor/funcionarios/editar', 'FuncionarioController@editar');
$router->post('/gestor/funcionarios/salvar', 'FuncionarioController@salvar');
$router->post('/gestor/funcionarios/atualizar', 'FuncionarioController@atualizar');
$router->post('/gestor/funcionarios/desativar', 'FuncionarioController@desativar');
$router->post('/gestor/funcionarios/ativar', 'FuncionarioController@ativar');
$router->post('/gestor/funcionarios/excluir', 'FuncionarioController@excluir');

$router->get('/cadastro-nao-liberado', 'AutenticacaoController@cadastroNaoLiberado');

$router->get('/administrador/inicial', 'AdministradorController@inicial');
$router->get('/gestor/inicial', 'GestorController@inicial');
$router->get('/funcionario/inicial', 'FuncionarioController@inicial');

$router->get('/funcionario/loja', 'ItemController@listarTodosFuncionario');
$router->post('/funcionario/comprar', 'ItemController@comprar');
$router->get('/funcionario/editar', 'FuncionarioController@funcEditar');
$router->post('/funcionario/atualizar', 'FuncionarioController@funcAtualizar');
$router->get('/funcionario/modulos', 'ModuloController@listarTodosFuncionario');
$router->post('/funcionario/equipar', 'FuncionarioController@equipaItem');

try {
    $router->run();
} catch (\Throwable $exception) {
    error_log($exception->__toString());
    http_response_code(500);
    require __DIR__ . '/../app/views/outros/500.php';
}
