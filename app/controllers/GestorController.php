<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Gestor;
use app\models\Empresa;
use app\services\GestorService;
use app\services\EmpresaService;
use app\services\FuncionarioService;
use app\helpers\Validador;
use app\services\FuncionarioAtividadeService;

class GestorController extends Controller
{

    private GestorService $gestorService;
    private EmpresaService $empresaService;
    private FuncionarioService $funcionarioService;

    public function inicial()
    {
        $this->gestorRequired();

        $gestor = $this->gestorService->getGestorByUsuario($_SESSION['usuario_logado']->getId());
        if (!$gestor) $this->redirect(URL_BASE . '/login');
        $idEmpresa = $gestor->getEmpresa()->getId();
        $quantidadeFuncionarios = $this->funcionarioService->countFuncionariosByEmpresa($idEmpresa);
        $empresa = $this->empresaService->getEmpresaById($idEmpresa);
        $atividadeService = new FuncionarioAtividadeService();
        $desempenho = $atividadeService->getDesempenhoByEmpresa($idEmpresa);
        $taxaConclusao = $atividadeService->getTaxaConclusaoByEmpresa($idEmpresa);

        $this->view('gestor/inicial', [
            'quantidadeFuncionarios' => $quantidadeFuncionarios,
            'gestor' => $gestor,
            'empresa' => $empresa,
            'desempenho' => $desempenho,
            'taxaConclusao' => $taxaConclusao
        ]);
    }

    public function __construct()
    {
        $this->gestorService = new GestorService();
        $this->empresaService = new EmpresaService();
        $this->funcionarioService = new FuncionarioService();
    }

    public function listarTodos()
    {
        $this->adminRequired();
        $data['lista'] = $this->gestorService->getGestores();
        $this->view('administrador/gestores/gestor_list', $data);
    }

    public function criar()
    {
        $this->adminRequired();
        $data['empresas'] = $this->empresaService->getEmpresas();
        $this->view('administrador/gestores/gestor_create', $data);
    }

    public function salvar()
    {
        $this->adminRequired();
        $_POST['cpf'] = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $erros = Validador::validarGestor($_POST);
        if (empty($erros['id_empresa']) && !$this->empresaService->getEmpresaById((int) $_POST['id_empresa'])) $erros['id_empresa'] = 'A empresa selecionada não existe.';
        if (empty($erros['cpf']) && $this->gestorService->cpfExiste(preg_replace('/\D/', '', $_POST['cpf'] ?? ''))) $erros['cpf'] = 'Já existe um usuário com este CPF.';
        if (empty($erros['email']) && $this->gestorService->emailExiste($_POST['email'] ?? '')) $erros['email'] = 'Já existe um usuário com este e-mail.';
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

    public function editar()
    {
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

    public function desativar()
    {
        $this->adminRequired();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/gestores');
        }

        $this->gestorService->desativarGestor($id);

        $this->redirect(URL_BASE . '/administrador/gestores');
    }

    public function ativar()
    {
        $this->adminRequired();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/gestores');
        }

        $this->gestorService->ativarGestor($id);

        $this->redirect(URL_BASE . '/administrador/gestores');
    }

    public function excluir()
    {
        $this->desativar();
    }

    public function atualizar()
    {
        $this->adminRequired();
        $idGestor = filter_input(INPUT_POST, 'id_gestor', FILTER_VALIDATE_INT);
        if (!$idGestor || !$this->gestorService->getGestorById($idGestor)) {
            $this->redirect(URL_BASE . '/administrador/gestores');
        }
        $_POST['cpf'] = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $erros = Validador::validarGestor($_POST, false);
        if (empty($erros['id_empresa']) && !$this->empresaService->getEmpresaById((int) $_POST['id_empresa'])) $erros['id_empresa'] = 'A empresa selecionada não existe.';
        if (empty($erros['cpf']) && $this->gestorService->cpfExiste(preg_replace('/\D/', '', $_POST['cpf'] ?? ''), $idGestor)) $erros['cpf'] = 'Já existe um usuário com este CPF.';
        if (empty($erros['email']) && $this->gestorService->emailExiste($_POST['email'] ?? '', $idGestor)) $erros['email'] = 'Já existe um usuário com este e-mail.';
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $dadosGestor = $_POST;
            $dadosGestor['id_gestor'] = $idGestor;
            $dadosGestor['id_usuario'] = $this->gestorService->getGestorById($idGestor)?->getId() ?? 0;
            $data['gestor'] = Gestor::arrayParaObjeto($dadosGestor);
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

    public function countGestoresByEmpresa(int $idEmpresa): int
    {
        return $this->gestorService->countGestoresByEmpresa($idEmpresa);
    }
}
