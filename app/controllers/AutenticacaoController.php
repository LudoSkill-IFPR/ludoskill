<?php

namespace app\controllers;

use app\core\Controller;
use app\services\AutenticacaoService;

class AutenticacaoController extends Controller
{

    private AutenticacaoService $autenticacaoService;

    public function __construct()
    {
        $this->autenticacaoService = new AutenticacaoService();
    }

    public function login()
    {

        $this->view('autenticacao/login');
    }

    public function logar()
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $usuario = $this->autenticacaoService->logar($email, $senha);

        if (!$usuario) {
            $data['erro'] = 'E-mail ou senha incorretos.';
            $this->view('entrada/login', $data);
            return;
        }

        if ($usuario->getPerfil() === null) {
            session_destroy();

            $this->redirect(URL_BASE . '/cadastro-nao-liberado');
        }

        switch ($usuario->getPerfil()) {

            case 'admin':
                $this->redirect(URL_BASE . '/administrador/inicial');
                break;

            case 'gestor':
                $this->redirect(URL_BASE . '/gestor/inicial');
                break;

            case 'funcionario':
                $this->redirect(URL_BASE . '/funcionario/inicial');
                break;

            default:
                session_destroy();

                $this->redirect(URL_BASE . '/cadastro-nao-liberado');
        }
    }

    public function cadastroNaoLiberado()
    {
        $this->view('entrada/cadastro_nao_liberado');
    }

    public function logout()
    {
        $this->autenticacaoService->logout();
        $this->redirect(URL_BASE . '/');
    }
}
