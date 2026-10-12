<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Usuario;
use app\services\UsuarioService;
use DateTimeImmutable;
use app\services\FuncionarioAtividadeService;
use app\services\FuncionarioService;
use app\services\GestorService;

class UsuarioController extends Controller
{
    private UsuarioService $service;

    public function __construct() {
        $this->service = new UsuarioService();
    }

    public function perfil(): void
    {
        $this->autenticacaoRequired();
        $usuario = $_SESSION['usuario_logado'];
        if ($usuario->getPerfil() === 'funcionario') {
            $usuario = (new FuncionarioService())->getFuncionarioByUsuario($usuario->getId()) ?? $usuario;
        } elseif ($usuario->getPerfil() === 'gestor') {
            $usuario = (new GestorService())->getGestorByUsuario($usuario->getId()) ?? $usuario;
        }
        $resumo = $usuario->getPerfil() === 'funcionario'
            ? (new FuncionarioAtividadeService())->getResumoByUsuario($usuario->getId())
            : null;
        $this->view('usuario/perfil', ['usuario' => $usuario, 'resumo' => $resumo]);
    }

    public function editarPerfil(): void
    {
        $this->autenticacaoRequired();
        $this->view('usuario/editar', ['usuario' => $_SESSION['usuario_logado']]);
    }

    public function atualizarPerfil(): void
    {
        $this->autenticacaoRequired();
        $usuarioAtual = $_SESSION['usuario_logado'];
        $dados = $_POST;
        $erros = [];
        if (trim($dados['nome_completo'] ?? '') === '') $erros['nome_completo'] = 'Informe o nome.';
        elseif (strlen(trim($dados['nome_completo'])) > 255) $erros['nome_completo'] = 'O nome deve possuir no máximo 255 caracteres.';
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $dados['data_nascimento'] ?? '');
        if (!$data || $data->format('Y-m-d') !== ($dados['data_nascimento'] ?? '') || $data > new DateTimeImmutable('today')) $erros['data_nascimento'] = 'Informe uma data válida.';
        if (!filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) $erros['email'] = 'Informe um e-mail válido.';
        elseif ($this->service->emailExiste($dados['email'], $usuarioAtual->getId())) $erros['email'] = 'Este e-mail já está em uso.';
        if (trim($dados['numero_telefone'] ?? '') === '') $erros['numero_telefone'] = 'Informe o telefone.';
        elseif (strlen(trim($dados['numero_telefone'])) > 20) $erros['numero_telefone'] = 'O telefone deve possuir no máximo 20 caracteres.';
        if (!empty($dados['senha']) && strlen($dados['senha']) < 8) $erros['senha'] = 'A senha deve possuir pelo menos 8 caracteres.';
        if ($erros) {
            $this->view('usuario/editar', ['usuario' => $usuarioAtual, 'dados' => $dados, 'erros' => $erros]);
            return;
        }
        $usuario = new Usuario(
            $usuarioAtual->getId(), trim($dados['nome_completo']), $data, $usuarioAtual->getCpf(),
            trim($dados['email']), '', trim($dados['numero_telefone']), $usuarioAtual->getPerfil(), $usuarioAtual->getEstado()
        );
        $this->service->updatePerfil($usuario, $dados['senha'] ?? null);
        $usuarioAtual->setNomeCompleto($usuario->getNomeCompleto())->setDataNascimento($data)->setEmail($usuario->getEmail())->setNumeroTelefone($usuario->getNumeroTelefone());
        $this->redirect(URL_BASE . '/perfil');
    }

}
