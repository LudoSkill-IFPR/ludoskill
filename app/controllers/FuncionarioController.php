<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Funcionario;
use app\services\FuncionarioService;
use app\helpers\Validador;
use DateTimeImmutable;
use app\models\Empresa;
use app\services\InventarioService;
use DomainException;

class FuncionarioController extends Controller
{
    private FuncionarioService $funcionarioService;

    public function inicial()
    {
        $this->funcionarioRequired();
        $idUsuario = $_SESSION['usuario_logado']->getId();
        $atividadeService = new \app\services\FuncionarioAtividadeService();
        $this->view('funcionario/inicial', [
            'funcionario' => $this->funcionarioService->getFuncionarioByUsuario($idUsuario),
            'inventario' => (new InventarioService())->getByUsuario($idUsuario),
            'resumo' => $atividadeService->getResumoByUsuario($idUsuario),
            'proximaAtividade' => $atividadeService->getProximaAtividade($idUsuario),
            'mensagem' => $_SESSION['mensagem'] ?? null,
            'erro' => $_SESSION['erro'] ?? null,
        ]);
        unset($_SESSION['mensagem'], $_SESSION['erro']);
    }

    public function __construct()
    {
        $this->funcionarioService = new FuncionarioService();
    }

    public function listarTodos()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $data['lista'] = $this->funcionarioService->getFuncionariosByEmpresa($idEmpresa);
        $this->view('gestor/funcionarios/funcionario_list', $data);
    }

    public function criar()
    {
        $this->gestorRequired();
        $this->getEmpresaIdDoGestor();
        $this->view('gestor/funcionarios/funcionario_create', []);
    }

    public function salvar()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $dados = $_POST;
        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf'] ?? '');
        $dados['id_empresa'] = $idEmpresa;
        $erros = Validador::validarFuncionario($dados);
        if (empty($erros['cpf']) && $this->funcionarioService->cpfExiste($dados['cpf'])) {
            $erros['cpf'] = 'Já existe um usuário com este CPF.';
        }
        if (empty($erros['email']) && $this->funcionarioService->emailExiste($dados['email'])) {
            $erros['email'] = 'Já existe um usuário com este e-mail.';
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['funcionario'] = $dados;
            $this->view('gestor/funcionarios/funcionario_create', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setId($idEmpresa);

        $funcionario = new Funcionario(
            0,
            $dados['nome_completo'],
            new DateTimeImmutable($dados['data_nascimento']),
            $dados['cpf'],
            $dados['email'],
            $dados['senha_hash'],
            $dados['numero_telefone'],
            $empresa,
            0,
            0,
            1
        );

        $this->funcionarioService->saveFuncionario($funcionario);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function editar()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }

        $data['funcionario'] = $this->funcionarioService->getFuncionarioByIdAndEmpresa($id, $idEmpresa);
        if (!$data['funcionario']) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }
        $this->view('gestor/funcionarios/funcionario_edit', $data);
    }

    public function funcEditar()
    {
        $this->funcionarioRequired();
        $id = $_SESSION['usuario_logado']->getId();
        if (!$id) {
            $this->redirect(URL_BASE . '/login');
        }
        $funcionario = $this->funcionarioService->getFuncionarioByUsuario($id);
        if (!$funcionario) $this->redirect(URL_BASE . '/funcionario/inicial');
        $idEmpresa = $funcionario->getEmpresa()->getId();
        $idFuncionario = $funcionario->getIdFuncionario();

        $data['funcionario'] = $this->funcionarioService->getFuncionarioByIdAndEmpresa($idFuncionario, $idEmpresa);
        if (!$data['funcionario']) {
            $this->redirect(URL_BASE . '/funcionario/inicial');
        }
        $this->view('funcionario/editar', $data);
    }

    public function funcAtualizar()
    {
        $this->funcionarioRequired();
        $id = $_SESSION['usuario_logado']->getId();
        if (!$id) {
            $this->redirect(URL_BASE . '/login');
        }
        $funcionario = $this->funcionarioService->getFuncionarioByUsuario($id);
        if (!$funcionario) $this->redirect(URL_BASE . '/funcionario/inicial');
        $idEmpresa = $funcionario->getEmpresa()->getId();
        $idFuncionario = $funcionario->getIdFuncionario();
        if (!$idFuncionario || !$this->funcionarioService->getFuncionarioByIdAndEmpresa($idFuncionario, $idEmpresa)) {
            $this->redirect(URL_BASE . '/funcionario/inicial');
        }
        $dados = $_POST;
        $dados['cpf'] = $funcionario->getCpf();
        $dados['id_empresa'] = $idEmpresa;
        $erros = Validador::validarFuncionario($dados, false);
        if (empty($erros['email']) && $this->funcionarioService->emailExiste($dados['email'], $idFuncionario)) {
            $erros['email'] = 'Já existe um usuário com este e-mail.';
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['funcionario'] = $dados;
            $dados['id_funcionario'] = $idFuncionario;
            $dados['id_usuario'] = $funcionario->getId();
            $dados['bolotas_totais'] = $funcionario->getBolotasTotais();
            $dados['pontuacao_total'] = $funcionario->getPontuacaoTotal();
            $dados['nivel'] = $funcionario->getNivel();
            $this->view('funcionario/editar', ['erros' => $erros, 'funcionario' => Funcionario::arrayParaObjeto($dados)]);
            return;
        }

        $empresa = (new Empresa())->setId($idEmpresa);

        $funcionario = new Funcionario(
            $funcionario->getId(),
            $dados['nome_completo'],
            new \DateTimeImmutable($dados['data_nascimento']),
            $dados['cpf'],
            $dados['email'],
            $dados['senha_hash'] ?? '',
            $dados['numero_telefone'],
            $empresa,
            $funcionario->getBolotasTotais(),
            $funcionario->getPontuacaoTotal(),
            $funcionario->getNivel(),
            $funcionario->getEstado(),
            $idFuncionario
        );

        $this->funcionarioService->updateFuncionario($funcionario, $idEmpresa, $dados['senha_hash'] ?? null);
        $_SESSION['usuario_logado']->setNomeCompleto($dados['nome_completo']);
        $this->redirect(URL_BASE . '/funcionario/inicial');
    }

    public function desativar()
    {
        $this->gestorRequired();

        $idEmpresa = $this->getEmpresaIdDoGestor();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }

        $this->funcionarioService->desativarFuncionario($id, $idEmpresa);

        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function ativar()
    {
        $this->gestorRequired();

        $idEmpresa = $this->getEmpresaIdDoGestor();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }

        $this->funcionarioService->ativarFuncionario($id, $idEmpresa);

        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    public function excluir()
    {
        $this->desativar();
    }

    public function atualizar()
    {
        $this->gestorRequired();
        $idEmpresa = $this->getEmpresaIdDoGestor();
        $idFuncionario = filter_input(INPUT_POST, 'id_funcionario', FILTER_VALIDATE_INT);
        if (!$idFuncionario || !$this->funcionarioService->getFuncionarioByIdAndEmpresa($idFuncionario, $idEmpresa)) {
            $this->redirect(URL_BASE . '/gestor/funcionarios');
        }
        $dados = $_POST;
        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf'] ?? '');
        $dados['id_empresa'] = $idEmpresa;
        $erros = Validador::validarFuncionario($dados, false);
        if (empty($erros['cpf']) && $this->funcionarioService->cpfExiste($dados['cpf'], $idFuncionario)) {
            $erros['cpf'] = 'Já existe um usuário com este CPF.';
        }
        if (empty($erros['email']) && $this->funcionarioService->emailExiste($dados['email'], $idFuncionario)) {
            $erros['email'] = 'Já existe um usuário com este e-mail.';
        }
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $dados['id_funcionario'] = $idFuncionario;
            $atual = $this->funcionarioService->getFuncionarioByIdAndEmpresa($idFuncionario, $idEmpresa);
            $dados['id_usuario'] = $atual?->getId() ?? 0;
            $dados['bolotas_totais'] = $atual?->getBolotasTotais() ?? 0;
            $dados['pontuacao_total'] = $atual?->getPontuacaoTotal() ?? 0;
            $dados['nivel'] = $atual?->getNivel() ?? 1;
            $data['funcionario'] = Funcionario::arrayParaObjeto($dados);
            $this->view('gestor/funcionarios/funcionario_edit', $data);
            return;
        }

        $empresa = (new Empresa())->setId($idEmpresa);

        $atual = $this->funcionarioService->getFuncionarioByIdAndEmpresa($idFuncionario, $idEmpresa);
        $funcionario = new Funcionario(
            $atual->getId(),
            $dados['nome_completo'],
            new \DateTimeImmutable($dados['data_nascimento']),
            $dados['cpf'],
            $dados['email'],
            $dados['senha_hash'] ?? '',
            $dados['numero_telefone'],
            $empresa,
            $atual->getBolotasTotais(),
            $atual->getPontuacaoTotal(),
            $atual->getNivel(),
            $atual->getEstado(),
            $idFuncionario
        );

        $this->funcionarioService->updateFuncionario($funcionario, $idEmpresa, $dados['senha_hash'] ?? null);
        $this->redirect(URL_BASE . '/gestor/funcionarios');
    }

    private function getEmpresaIdDoGestor(): int
    {
        $idEmpresa = $this->funcionarioService->getEmpresaIdByGestorUsuario(
            $_SESSION['usuario_logado']->getId()
        );

        if ($idEmpresa === null) {
            $this->redirect(URL_BASE . '/gestor/inicial');
        }

        return $idEmpresa;
    }

    public function equipaItem()
    {
        $this->funcionarioRequired();
        $idItem = filter_input(INPUT_POST, 'id_item', FILTER_VALIDATE_INT);
        if (!$idItem) $this->redirect(URL_BASE . '/funcionario/inicial');
        try {
            $tipo = (new InventarioService())->equipar($_SESSION['usuario_logado']->getId(), $idItem);
            if ($tipo === 'TEMA') $_SESSION['tema_equipado'] = $idItem;
            $_SESSION['mensagem'] = 'Item equipado com sucesso.';
        } catch (\Throwable $exception) {
            error_log($exception->__toString());
            $_SESSION['erro'] = $exception instanceof DomainException ? $exception->getMessage() : 'Não foi possível equipar o item.';
        }
        $this->redirect(URL_BASE . '/funcionario/inicial');
    }
}
