<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Usuario;
use app\services\UsuarioService;
use DateTimeImmutable;
use app\services\FuncionarioAtividadeService;
use app\services\FuncionarioService;
use app\services\GestorService;

use app\services\ModuloService;
use app\services\EmpresaService;
use app\services\ItemService;

class UsuarioController extends Controller
{
    private UsuarioService $service;

    public function __construct() {
        $this->service = new UsuarioService();
    }

    public function perfil(): void
    {
        $this->autenticacaoRequired();
        $usuarioSessao = $_SESSION['usuario_logado'];
        $perfil = $usuarioSessao->getPerfil();
        $dadosView = ['usuario' => $usuarioSessao];

        if ($perfil === 'funcionario') {
            $funcionario = (new FuncionarioService())->getFuncionarioByUsuario($usuarioSessao->getId()) ?? $usuarioSessao;
            $resumo = (new FuncionarioAtividadeService())->getResumoByUsuario($usuarioSessao->getId());
            $proximaAtividade = (new FuncionarioAtividadeService())->getProximaAtividade($usuarioSessao->getId());

            $dadosView['usuario'] = $funcionario;
            $dadosView['resumo'] = $resumo;
            $dadosView['proximaAtividade'] = $proximaAtividade;
        } elseif ($perfil === 'gestor') {
            $gestor = (new GestorService())->getGestorByUsuario($usuarioSessao->getId()) ?? $usuarioSessao;
            $idEmpresa = method_exists($gestor, 'getEmpresa') && $gestor->getEmpresa() ? $gestor->getEmpresa()->getId() : 0;

            $dadosView['usuario'] = $gestor;
            $dadosView['empresa'] = method_exists($gestor, 'getEmpresa') ? $gestor->getEmpresa() : null;
            $dadosView['qtdFuncionarios'] = (new FuncionarioService())->countFuncionariosByEmpresa($idEmpresa);
            $dadosView['qtdModulos'] = (new ModuloService())->countModulos();
            $dadosView['taxaConclusao'] = (new FuncionarioAtividadeService())->getTaxaConclusaoByEmpresa($idEmpresa);
        } elseif ($perfil === 'admin') {
            $dadosView['qtdEmpresas'] = (new EmpresaService())->countEmpresas();
            $dadosView['qtdGestores'] = (new GestorService())->countGestores();
            $dadosView['qtdModulos'] = (new ModuloService())->countModulos();
            $dadosView['qtdItens'] = (new ItemService())->countItens();
        }

        $dadosView['mensagemSucesso'] = $_SESSION['mensagem_sucesso'] ?? null;
        unset($_SESSION['mensagem_sucesso']);

        $this->view('usuario/perfil', $dadosView);
    }

    public function editarPerfil(): void
    {
        $this->autenticacaoRequired();
        $usuario = $_SESSION['usuario_logado'];
        if ($usuario->getPerfil() === 'funcionario') {
            $usuario = (new FuncionarioService())->getFuncionarioByUsuario($usuario->getId()) ?? $usuario;
        } elseif ($usuario->getPerfil() === 'gestor') {
            $usuario = (new GestorService())->getGestorByUsuario($usuario->getId()) ?? $usuario;
        }

        $this->view('usuario/editar', ['usuario' => $usuario]);
    }

    public function atualizarPerfil(): void
    {
        $this->autenticacaoRequired();
        $usuarioAtual = $_SESSION['usuario_logado'];
        $dados = $_POST;
        $erros = [];

        // Validação do nome
        $nome = trim($dados['nome_completo'] ?? '');
        if ($nome === '') {
            $erros['nome_completo'] = 'O nome completo é obrigatório.';
        } elseif (strlen($nome) > 255) {
            $erros['nome_completo'] = 'O nome deve possuir no máximo 255 caracteres.';
        }

        // Validação da data de nascimento
        $dataStr = trim($dados['data_nascimento'] ?? '');
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $dataStr);
        if (!$data || $data->format('Y-m-d') !== $dataStr) {
            $erros['data_nascimento'] = 'Informe uma data de nascimento válida.';
        } elseif ($data > new DateTimeImmutable('today')) {
            $erros['data_nascimento'] = 'A data de nascimento não pode ser futura.';
        }

        // Validação do e-mail
        $email = trim($dados['email'] ?? '');
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Informe um endereço de e-mail válido.';
        } elseif ($this->service->emailExiste($email, $usuarioAtual->getId())) {
            $erros['email'] = 'Este e-mail já está sendo utilizado por outro usuário.';
        }

        // Validação do telefone
        $telefone = trim($dados['numero_telefone'] ?? '');
        if ($telefone === '') {
            $erros['numero_telefone'] = 'O número de telefone é obrigatório.';
        } elseif (strlen($telefone) > 20) {
            $erros['numero_telefone'] = 'O telefone deve possuir no máximo 20 caracteres.';
        }

        // Validação opcional da senha
        $senha = $dados['senha'] ?? '';
        if ($senha !== '' && strlen($senha) < 8) {
            $erros['senha'] = 'A nova senha deve possuir pelo menos 8 caracteres.';
        }

        if (!empty($erros)) {
            $usuarioParaView = $usuarioAtual;
            if ($usuarioAtual->getPerfil() === 'funcionario') {
                $usuarioParaView = (new FuncionarioService())->getFuncionarioByUsuario($usuarioAtual->getId()) ?? $usuarioAtual;
            } elseif ($usuarioAtual->getPerfil() === 'gestor') {
                $usuarioParaView = (new GestorService())->getGestorByUsuario($usuarioAtual->getId()) ?? $usuarioAtual;
            }

            $this->view('usuario/editar', [
                'usuario' => $usuarioParaView,
                'dados' => $dados,
                'erros' => $erros
            ]);
            return;
        }


        $usuarioAtualizado = new Usuario(
            $usuarioAtual->getId(),
            $nome,
            $data,
            $usuarioAtual->getCpf(),
            $email,
            '',
            $telefone,
            $usuarioAtual->getPerfil(),
            $usuarioAtual->getEstado()
        );

        $this->service->updatePerfil($usuarioAtualizado, $senha !== '' ? $senha : null);

        $usuarioAtual->setNomeCompleto($nome)
                     ->setDataNascimento($data)
                     ->setEmail($email)
                     ->setNumeroTelefone($telefone);

        $_SESSION['mensagem_sucesso'] = 'Suas informações de perfil foram atualizadas com sucesso!';
        $this->redirect(URL_BASE . '/perfil');
    }

}
