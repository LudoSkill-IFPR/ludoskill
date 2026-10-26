<?php

$_SERVER['SCRIPT_NAME'] = '/ludoskill/public/index.php';
ini_set('session.save_path', '/tmp');
require __DIR__ . '/../app/core/Autoload.php';
require __DIR__ . '/../app/config/Config.php';

use app\core\Controller;
use app\models\Administrador;
use app\models\Atividade;
use app\models\Empresa;
use app\models\Exercicio;
use app\models\Funcionario;
use app\models\Gestor;
use app\models\Item;
use app\models\Modulo;
use app\models\SolicitacaoAcesso;

set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$empresa = new Empresa(1, '12345678000199', 'Empresa', 'empresa@example.com');
$modulo = new Modulo(1, 'Módulo', str_repeat('Descrição ', 4), 0, 'ATIVO');
$atividade = new Atividade(1, $modulo, 'INTRODUCAO', 'Atividade', 'ATIVO', 1, 180);
$exercicio = new Exercicio(1, 'Conjunto', '[{"enunciado":"Questão?","alternativas":["A","B"],"alternativa_correta":0,"justificativa":"Justificativa"}]', $atividade);
$item = new Item(1, 'ATIVO', 'Tema', 'TEMA', 10, 'assets/imagens/imagemTEMA.png');
$funcionario = new Funcionario(1, 'Funcionário', new DateTimeImmutable('2000-01-01'), '12345678901', 'func@example.com', '', '45999999999', $empresa, 100, 80, 1, 'ATIVO', 1);
$gestor = new Gestor(2, 'Gestor', new DateTimeImmutable('1990-01-01'), '10987654321', 'gestor@example.com', '', '45999999998', $empresa, 'ATIVO', 1);
$admin = new Administrador(3, 'Admin', new DateTimeImmutable('1980-01-01'), '11122233344', 'admin@example.com', '', '45999999997', 'ATIVO', 1);
$solicitacao = new SolicitacaoAcesso(1, 'Solicitante', new DateTimeImmutable('1990-01-01'), '99988877766', 'solicitante@example.com', '45999999996', 'Empresa', '12345678000199', 'empresa@example.com');
$_SESSION['usuario_logado'] = $funcionario;

$controller = new Controller();
$casos = [
    ['administrador/empresas/empresa_list', ['lista' => [$empresa]]],
    ['administrador/empresas/empresa_create', ['empresa' => ['nome' => 'Empresa'], 'erros' => ['cnpj' => 'Inválido']]],
    ['administrador/empresas/empresa_edit', ['empresa' => ['id_empresa' => 1, 'cnpj' => '123', 'nome' => 'Empresa', 'email' => 'invalido', 'plano' => 'BASICO'], 'erros' => ['email' => 'Inválido']]],
    ['administrador/modulos/modulo_list', ['lista' => [$modulo]]],
    ['administrador/modulos/modulo_create', ['modulo' => ['nome' => 'Módulo'], 'erros' => ['descricao' => 'Inválida']]],
    ['administrador/modulos/modulo_edit', ['modulo' => $modulo]],
    ['administrador/atividades/atividade_list', ['lista' => [$atividade]]],
    ['administrador/atividades/atividade_create', ['atividade' => ['nome' => 'Atividade'], 'modulos' => [$modulo], 'erros' => ['ordem' => 'Inválida']]],
    ['administrador/atividades/atividade_edit', ['atividade' => $atividade, 'modulos' => [$modulo]]],
    ['administrador/exercicios/exercicio_list', ['lista' => [$exercicio]]],
    ['administrador/exercicios/exercicio_create', ['exercicio' => ['descricao' => 'Conjunto', 'atividade_id' => 1], 'questoes' => json_decode($exercicio->getConteudo(), true), 'atividades' => [$atividade], 'erros' => ['conteudo' => 'Inválido']]],
    ['administrador/exercicios/exercicio_edit', ['exercicio' => $exercicio, 'questoes' => json_decode($exercicio->getConteudo(), true), 'atividades' => [$atividade]]],
    ['administrador/gestores/gestor_list', ['lista' => [$gestor]]],
    ['administrador/gestores/gestor_create', ['gestor' => ['nome_completo' => 'Gestor'], 'empresas' => [$empresa], 'erros' => ['email' => 'Inválido']]],
    ['administrador/gestores/gestor_edit', ['gestor' => $gestor, 'empresas' => [$empresa]]],
    ['administrador/itens/item_list', ['lista' => [$item]]],
    ['administrador/itens/item_create', ['item' => ['nome' => 'Item', 'estado' => '1'], 'erros' => ['tipo' => 'Inválido']]],
    ['administrador/itens/item_edit', ['item' => $item]],
    ['administrador/solicitacoes/solicitacao_list', ['lista' => [$solicitacao], 'mensagem' => null, 'tipoMensagem' => 'sucesso']],
    ['entrada/esqueciSenha', ['erro' => 'E-mail inválido']],
    ['entrada/gerarNovaSenha', ['token' => str_repeat('a', 64), 'email' => 'pessoa@example.com']],
    ['entrada/solicitarAcesso', ['solicitacao' => ['nome_completo' => 'Solicitante'], 'erros' => ['email' => 'Inválido']]],
    ['gestor/funcionarios/funcionario_list', ['lista' => [$funcionario]]],
    ['gestor/funcionarios/funcionario_create', ['funcionario' => ['nome_completo' => 'Funcionário'], 'erros' => ['email' => 'Inválido']]],
    ['gestor/funcionarios/funcionario_edit', ['funcionario' => $funcionario]],
    ['gestor/inicial', ['quantidadeFuncionarios' => 1, 'empresa' => $empresa, 'desempenho' => [['nome_completo' => 'Funcionário', 'media_estrelas' => 2.5]], 'taxaConclusao' => 50.0]],
    ['funcionario/inicial', ['funcionario' => $funcionario, 'inventario' => [], 'resumo' => ['desempenho' => 2, 'concluidas' => 1, 'total' => 2], 'proximaAtividade' => $atividade, 'mensagem' => null, 'erro' => null]],
    ['funcionario/loja', ['funcionario' => $funcionario, 'inventario' => [], 'lista' => [$item], 'mensagem' => null, 'erro' => null]],
    ['funcionario/modulos', ['lista' => [['entidade' => $modulo, 'atividades' => [['entidade' => $atividade, 'liberada' => true]]]], 'resultados' => []]],
    ['funcionario/exercicios', ['atividade' => $atividade, 'exercicios' => [['id_exercicio' => 1, 'descricao' => 'Conjunto', 'questoes' => json_decode($exercicio->getConteudo(), true)]]]],
    ['usuario/perfil', ['usuario' => $funcionario, 'resumo' => ['concluidas' => 1, 'total' => 2, 'estrelas' => 2, 'desempenho' => 2]]],
    ['usuario/perfil', ['usuario' => $gestor, 'empresa' => $empresa, 'qtdFuncionarios' => 5, 'qtdModulos' => 2, 'taxaConclusao' => 75.0]],
    ['usuario/perfil', ['usuario' => $admin, 'qtdEmpresas' => 3, 'qtdGestores' => 4, 'qtdModulos' => 2, 'qtdItens' => 5]],
    ['usuario/editar', ['usuario' => $funcionario]],
    ['usuario/editar', ['usuario' => $gestor]],
];

foreach ($casos as [$view, $dados]) {
    ob_start();
    $controller->view($view, $dados);
    $html = ob_get_clean();
    if ($html === '') throw new RuntimeException("View vazia: {$view}");
    if (preg_match('/<form\b[^>]*method=["\']?post/i', $html) && !str_contains($html, 'name="_csrf"')) {
        throw new RuntimeException("Formulário POST sem token CSRF: {$view}");
    }
}

restore_error_handler();
echo count($casos) . " views renderizadas sem avisos.\n";
