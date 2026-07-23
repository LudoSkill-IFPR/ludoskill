<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Empresa;
use app\services\EmpresaService;
use app\helpers\Validador;

class EmpresaController extends Controller{

    private EmpresaService $empresaService;

    public function __construct() {
        $this->empresaService = new EmpresaService();
    }

    public function listarTodos() {
        $data['lista'] = $this->empresaService->getEmpresas();
        $this->view('empresas/empresas_list', $data);
    }

    public function listarEmpresas(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/empresas');
        }

        $id = $_GET['id']; 
        $data['empresa'] = $this->empresaService->getEmpresaById($id);
        $this->view('empresas/empresa_show', $data);
    }

    public function criar(){
        $this->view('empresas/empresa_create', []);
    }

    public function salvar(){
        $erros = Validador::validarEmpresa($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['empresa'] = $_POST;
            $this->view('empresas/empresa_create', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setCnpj($_POST['cnpj']);
        $empresa->setNome($_POST['nome']);
        $empresa->setEmail($_POST['email']);
        $empresa->setPlano($_POST['plano']);

        $this->empresaService->saveEmpresa($empresa);
        $this->redirect(URL_BASE . '/empresas');
    }

    public function editar(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/empresas');
        }

        $id = $_GET['id'];

        $data['empresa'] = $this->empresaService->getEmpresaById($id);
        $this->view('empresas/empresa_edit', $data);
    }

    public function excluir(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/empresas');
        }

        $id = $_GET['id'];
        $this->empresaService->deleteEmpresa($id);
        $this->redirect(URL_BASE . '/empresas');
    }

    public function atualizar(){
        $erros = Validador::validarEmpresa($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['empresa'] = $_POST;
            $this->view('empresas/empresa_edit', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setId($_POST['id']);
        $empresa->setCnpj($_POST['cnpj']);
        $empresa->setNome($_POST['nome']);
        $empresa->setEmail($_POST['email']);
        $empresa->setPlano($_POST['plano']);

        $this->empresaService->updateEmpresa($empresa);
        $this->redirect(URL_BASE . '/empresas');
    }

}