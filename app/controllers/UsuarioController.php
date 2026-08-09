<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Usuario;
use app\services\UsuarioService;

class UsuarioController extends Controller
{
    private UsuarioService $service;

    public function __construct() {
        $this->service = new UsuarioService();
    }

    public function index() {
        $data['usuarios'] = $this->service->getUsuarios();
        $this->view('usuarios/usuario_list', $data);
    }

    public function cadastrar() {
        $this->view('usuarios/usuario_create');
    }

    public function salvar()
    {
        $usuario = new Usuario(
            0,
            $_POST['nomeCompleto'],
            $_POST['dataNascimento'],
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numeroTelefone']
        );

        if ($this->service->saveUsuario($usuario)) {
            $this->redirect(URL_BASE . '/usuarios');
        } else {
            echo "Erro: CPF, e-mail ou telefone já cadastrado!";
        }
    }

    public function editar() {
        $id = $_GET['id'];
        $data['usuario'] = $this->service->getUsuarioById($id);
        $this->view('usuarios/usuario_edit', $data);
    }

    public function atualizar() {
        $usuario = new Usuario(
            $_POST['id'], 
            $_POST['nomeCompleto'],
            $_POST['dataNascimento'],
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numeroTelefone']
        );
        $this->service->updateUsuario($usuario);
        $this->redirect(URL_BASE . '/usuarios');
    }

    public function excluir() {
        $id = $_GET['id'];
        $this->service->deleteUsuario($id);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }
}
