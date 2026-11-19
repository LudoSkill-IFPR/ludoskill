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
        $this->adminRequired();
        $data['lista'] = $this->empresaService->getEmpresas();
        $this->view('/administrador/empresas/empresa_list', $data);
    }

    public function criar(){
        $this->adminRequired();
        $this->view('/administrador/empresas/empresa_create', []);
    }

    public function salvar(){
        $this->adminRequired();
        $dados = $_POST;
        $dados['cnpj'] = preg_replace('/\D/', '', $dados['cnpj'] ?? '');
        $erros = Validador::validarEmpresa($dados);
        if (empty($erros['cnpj']) && $this->empresaService->cnpjExiste($dados['cnpj'])) {
            $erros['cnpj'] = 'Já existe uma empresa com este CNPJ.';
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['empresa'] = $dados;
            $this->view('/administrador/empresas/empresa_create', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setId(0);
        $empresa->setCnpj($dados['cnpj']);
        $empresa->setNome(trim($dados['nome']));
        $empresa->setEmail(trim($dados['email']));
        $empresa->setPlano($dados['plano']);

        $this->empresaService->saveEmpresa($empresa);
        $this->redirect(URL_BASE . '/administrador/empresas');
    }

    public function editar(){
        $this->adminRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/empresas');
        }
        $data['empresa'] = $this->empresaService->getEmpresaById($id);
        if (!$data['empresa']) {
            $this->redirect(URL_BASE . '/administrador/empresas');
        }
        $this->view('/administrador/empresas/empresa_edit', $data);
    }

    public function excluir(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->empresaService->getEmpresaById($id)) {
            $this->redirect(URL_BASE . '/administrador/empresas');
        }

        $possuiUsuarios = $this->empresaService->possuiUsuariosVinculados($id);
        $exclusaoConfirmada = ($_POST['confirmar_exclusao'] ?? '') === '1';

        if ($possuiUsuarios && !$exclusaoConfirmada) {
            $this->view('/administrador/empresas/empresa_list', [
                'lista' => $this->empresaService->getEmpresas(),
                'erro' => 'Não podemos excluir uma empresa com gestores ou funcionários cadastrados. Clique novamente em “Excluir empresa e usuários” para confirmar a exclusão de todos os vínculos.',
                'empresaConfirmacaoId' => $id,
            ]);
            return;
        }

        try {
            if ($possuiUsuarios) {
                $this->empresaService->deleteEmpresaComUsuarios($id);
            } else {
                $this->empresaService->deleteEmpresa($id);
            }
        } catch (\Throwable $exception) {
            error_log('Erro ao excluir empresa: ' . $exception->getMessage());
            $this->view('/administrador/empresas/empresa_list', [
                'lista' => $this->empresaService->getEmpresas(),
                'erro' => 'Não foi possível excluir a empresa. Nenhum dado foi removido.',
            ]);
            return;
        }

        $this->redirect(URL_BASE . '/administrador/empresas');
    }

    public function atualizar(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->empresaService->getEmpresaById($id)) {
            $this->redirect(URL_BASE . '/administrador/empresas');
        }
        $dados = $_POST;
        $dados['id'] = $id;
        $dados['id_empresa'] = $id;
        $dados['cnpj'] = preg_replace('/\D/', '', $dados['cnpj'] ?? '');
        $erros = Validador::validarEmpresa($dados);
        if (empty($erros['cnpj']) && $this->empresaService->cnpjExiste($dados['cnpj'], $id)) {
            $erros['cnpj'] = 'Já existe uma empresa com este CNPJ.';
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['empresa'] = $dados;
            $this->view('/administrador/empresas/empresa_edit', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setId($id);
        $empresa->setCnpj($dados['cnpj']);
        $empresa->setNome(trim($dados['nome']));
        $empresa->setEmail(trim($dados['email']));
        $empresa->setPlano($dados['plano']);

        $this->empresaService->updateEmpresa($empresa);
        $this->redirect(URL_BASE . '/administrador/empresas');
    }

}
