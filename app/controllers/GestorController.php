<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Gestor;
use app\models\Empresa;
use app\services\GestorService;
use app\services\EmpresaService;
use app\services\FuncionarioService;
use app\helpers\Validador;

class GestorController extends Controller {

    private GestorService $gestorService;
    private EmpresaService $empresaService;
    private FuncionarioService $funcionarioService;

    public function inicial()
    {
        $this->autenticacaoRequired();

        // Obter dados do gestor logado
        $usuarioLogado = $_SESSION['usuario_logado'];
        $idUsuario = $usuarioLogado->getId();
        
        // Obter gestor e sua empresa
        $gestores = $this->gestorService->getGestores();
        $gestor = null;
        $idEmpresa = null;
        
        foreach ($gestores as $g) {
            if ($g['id_usuario'] == $idUsuario) {
                $gestor = $g;
                $idEmpresa = $g['id_empresa'];
                break;
            }
        }
        
        // Obter quantidade de funcionários cadastrados por este gestor
        $quantidadeFuncionarios = 0;
        $empresa = null;
        if ($idEmpresa) {
            $quantidadeFuncionarios = $this->funcionarioService->countFuncionariosByEmpresa($idEmpresa);
            $empresa = $this->empresaService->getEmpresaById($idEmpresa);
        }
        
        $this->view('gestor/inicial', [
            'quantidadeFuncionarios' => $quantidadeFuncionarios,
            'gestor' => $gestor,
            'empresa' => $empresa
        ]);
    }

    public function __construct() {
        $this->gestorService = new GestorService();
        $this->empresaService = new EmpresaService();
        $this->funcionarioService = new FuncionarioService();
    }

    public function listarTodos() {
        $this->adminRequired();
        $data['lista'] = $this->gestorService->getGestores();
        $this->view('gestores/gestores_list', $data);
    }

    public function listarGestor() {
        $this->adminRequired();

        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/gestores');
        }

        $id = $_GET['id'];
        $data['gestor'] = $this->gestorService->getGestorById($id);
        $this->view('gestores/gestor_show', $data);
    }

    public function criar() {
        $this->adminRequired();
        $data['empresas'] = $this->empresaService->getEmpresas();
        $this->view('gestores/gestor_create', $data);
    }

    public function salvar() {
        $this->adminRequired();
        $erros = Validador::validarGestor($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['gestor'] = $_POST;
            $data['empresas'] = $this->empresaService->getEmpresas();
            $this->view('gestores/gestor_create', $data);
            return;
        }

        $empresa = new Empresa($_POST['id_empresa'], '', '', '', '', '', '');
        $gestor = new Gestor(
            0,
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numero_telefone'],
            $empresa
        );

        $this->gestorService->saveGestor($gestor);
        $this->redirect(URL_BASE . '/gestores');
    }

    public function editar() {
        $this->adminRequired();
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/gestores');
        }

        $id = $_GET['id'];
        $data['gestor'] = $this->gestorService->getGestorById($id);
        $data['empresas'] = $this->empresaService->getEmpresas();
        $this->view('gestores/gestor_edit', $data);
    }

    public function excluir() {
        $this->adminRequired();
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/gestores');
        }

        $id = $_GET['id'];
        $this->gestorService->deleteGestor($id);
        $this->redirect(URL_BASE . '/gestores');
    }

    public function atualizar() {
        $this->adminRequired();
        $erros = Validador::validarGestor($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['gestor'] = $_POST;
            $data['empresas'] = $this->empresaService->getEmpresas();
            $this->view('gestores/gestor_edit', $data);
            return;
        }

        $empresa = new Empresa($_POST['id_empresa'], '', '', '', '', '', '');
        $gestor = new Gestor(
            $_POST['id_usuario'],
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'],
            $_POST['numero_telefone'],
            $empresa
        );

        $this->gestorService->updateGestor($gestor);
        $this->redirect(URL_BASE . '/gestores');
    }
}