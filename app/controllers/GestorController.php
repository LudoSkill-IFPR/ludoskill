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
        $this->gestorRequired();

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
        $this->view('administrador/gestores/gestor_list', $data);
    }

    public function listarGestor() {
        $this->adminRequired();

        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/administrador/gestores');
        }

        $id = $_GET['id'];
        $data['gestor'] = $this->gestorService->getGestorById($id);
        $this->redirect(URL_BASE . '/administrador/gestores');
    }

    public function criar() {
        $this->adminRequired();
        $data['empresas'] = $this->empresaService->getEmpresas();
        $this->view('administrador/gestores/gestor_create', $data);
    }

    public function salvar() {
        $this->adminRequired();
        $erros = Validador::validarGestor($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['gestor'] = $_POST;
            $data['empresas'] = $this->empresaService->getEmpresas();
            $this->view('administrador/gestores/gestor_create', $data);
            return;
        }

        $empresa = (new Empresa())->setId((int) $_POST['id_empresa']);
        $gestor = new Gestor(
            0,
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'] ?? '',
            $_POST['numero_telefone'],
            $empresa
        );

        $this->gestorService->saveGestor($gestor);
        $this->redirect(URL_BASE . '/administrador/gestores');
    }

    public function editar() {
        $this->adminRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/gestores');
        }
        $data['gestor'] = $this->gestorService->getGestorById($id);
        if (!$data['gestor']) $this->redirect(URL_BASE . '/administrador/gestores');
        $data['empresas'] = $this->empresaService->getEmpresas();
        $this->view('administrador/gestores/gestor_edit', $data);
    }

    public function excluir() {
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/gestores');
        }
        $this->gestorService->deleteGestor($id);
        $this->redirect(URL_BASE . '/administrador/gestores');
    }

    public function atualizar() {
        $this->adminRequired();
        $idGestor = filter_input(INPUT_POST, 'id_gestor', FILTER_VALIDATE_INT);
        if (!$idGestor || !$this->gestorService->getGestorById($idGestor)) {
            $this->redirect(URL_BASE . '/administrador/gestores');
        }
        $erros = Validador::validarGestor($_POST, false);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['gestor'] = $_POST;
            $data['empresas'] = $this->empresaService->getEmpresas();
            $this->view('administrador/gestores/gestor_edit', $data);
            return;
        }

        $empresa = (new Empresa())->setId((int) $_POST['id_empresa']);
        $gestor = new Gestor(
            0,
            $_POST['nome_completo'],
            new \DateTimeImmutable($_POST['data_nascimento']),
            $_POST['cpf'],
            $_POST['email'],
            $_POST['senha'] ?? '',
            $_POST['numero_telefone'],
            $empresa
        );

        $this->gestorService->updateGestor($gestor, $idGestor, $_POST['senha'] ?? null);
        $this->redirect(URL_BASE . '/administrador/gestores');
    }

    public function countGestoresByEmpresa(int $idEmpresa): int {
        return $this->gestorService->countGestoresByEmpresa($idEmpresa);
    }
}
