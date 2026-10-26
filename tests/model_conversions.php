<?php

require __DIR__ . '/../app/core/Autoload.php';

use app\models\Administrador;
use app\models\Atividade;
use app\models\Empresa;
use app\models\Exercicio;
use app\models\Funcionario;
use app\models\FuncionarioAtividade;
use app\models\Gestor;
use app\models\Inventario;
use app\models\Item;
use app\models\Modulo;
use app\models\Usuario;
use app\models\SolicitacaoAcesso;

$usuario = [
    'id_usuario' => 1,
    'nome_completo' => 'Pessoa',
    'data_nascimento' => '2000-01-01',
    'CPF' => '12345678901',
    'email' => 'pessoa@example.com',
    'senha_hash' => password_hash('senha-segura', PASSWORD_DEFAULT),
    'numero_telefone' => '45999999999',
    'estado' => 'ATIVO',
];

$casos = [
    Usuario::arrayParaObjeto($usuario),
    Administrador::arrayParaObjeto($usuario + ['id_administrador' => 1]),
    Empresa::arrayParaObjeto(['id_empresa' => 1, 'cnpj' => '12345678000199', 'nome' => 'Empresa', 'email' => 'empresa@example.com']),
    Gestor::arrayParaObjeto($usuario + ['id_gestor' => 1, 'id_empresa' => 1]),
    Funcionario::arrayParaObjeto($usuario + ['id_funcionario' => 1, 'id_empresa' => 1]),
    Modulo::arrayParaObjeto(['id_modulo' => 1, 'nome' => 'Módulo', 'descricao' => 'Descrição', 'estado' => 'ATIVO']),
    Atividade::arrayParaObjeto(['id_atividade' => 1, 'id_modulo' => 1, 'tipo_atividade' => 'INTRODUCAO', 'nome' => 'Atividade', 'estado' => 'ATIVO']),
    Exercicio::arrayParaObjeto(['id_exercicio' => 1, 'id_atividade' => 1, 'descricao' => 'Exercício', 'conteudo' => '[]']),
    Item::arrayParaObjeto(['id_item' => 1, 'estado' => "\x01", 'nome' => 'Item', 'tipo' => 'TEMA', 'preco' => 10]),
    Inventario::arrayParaObjeto(['id_inventario' => 1, 'id_funcionario' => 1, 'id_item' => 1, 'estado' => "\x01", 'nome' => 'Item', 'tipo' => 'TEMA', 'preco' => 10, 'em_uso' => "\x01"]),
    FuncionarioAtividade::arrayParaObjeto(['id_funcionario_atividade' => 1, 'id_funcionario' => 1, 'id_atividade' => 1, 'estado' => 'INICIADO']),
    SolicitacaoAcesso::arrayParaObjeto([
        'id_solicitacao' => 1,
        'nome_completo' => 'Gestor solicitado',
        'data_nascimento' => '1990-01-01',
        'CPF' => '99988877766',
        'email' => 'solicitante@example.com',
        'empresa_nome' => 'Empresa solicitante',
        'empresa_cnpj' => '12345678000199',
        'empresa_email' => 'empresa@example.com',
    ]),
];

foreach ($casos as $objeto) {
    if (!is_object($objeto)) throw new RuntimeException('Uma conversão não retornou um objeto.');
}

echo count($casos) . " conversões de models validadas.\n";
