<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Item;
use app\services\ItemService;
use app\helpers\Validador;
use app\services\FuncionarioService;
use app\services\InventarioService;
use DomainException;

class ItemController extends Controller 
{
    private ItemService $itemService;
    private InventarioService $inventarioService;

    public function __construct() {
        $this->itemService = new ItemService();
        $this->inventarioService = new InventarioService();
    }

    private function processarUploadImagem(array $imagem, array &$erros): ?string {
        if ($imagem['error'] !== UPLOAD_ERR_OK) {
            $erros['imagem'] = 'Erro ao enviar a imagem.';
            return null;
        }

        $tamanhoMaximo = 2 * 1024 * 1024;
        if ($imagem['size'] > $tamanhoMaximo) {
            $erros['imagem'] = 'A imagem deve ter no máximo 2MB.';
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($imagem['tmp_name']);
        $extensoes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($extensoes[$mimeType])) {
            $erros['imagem'] = 'Formato de imagem inválido. Use JPG, PNG ou WEBP.';
            return null;
        }

        $nomeArquivo = uniqid('item_', true) . '.' . $extensoes[$mimeType];
        $pastaUpload = UPLOAD_PATH . '/itens';

        if (!is_dir($pastaUpload) && !mkdir($pastaUpload, 0755, true) && !is_dir($pastaUpload)) {
            $erros['imagem'] = 'Não foi possível criar a pasta de upload.';
            return null;
        }

        $destino = $pastaUpload . '/' . $nomeArquivo;
        if (!move_uploaded_file($imagem['tmp_name'], $destino)) {
            $erros['imagem'] = 'Não foi possível salvar a imagem.';
            return null;
        }

        return 'assets/uploads/itens/' . $nomeArquivo;
    }

    public function listarTodos() {
        $this->adminRequired();
        $data['lista'] = $this->itemService->getItems();
        $this->view('/administrador/itens/item_list', $data);
    }

    public function listarTodosFuncionario() {
        $this->funcionarioRequired();
        $data['lista'] = $this->itemService->getItemsAtivos();
        $data['funcionario'] = (new FuncionarioService())->getFuncionarioByUsuario($_SESSION['usuario_logado']->getId());
        $data['inventario'] = $this->inventarioService->getByUsuario($_SESSION['usuario_logado']->getId());
        $data['mensagem'] = $_SESSION['mensagem'] ?? null;
        $data['erro'] = $_SESSION['erro'] ?? null;
        unset($_SESSION['mensagem'], $_SESSION['erro']);
        $this->view('/funcionario/loja', $data);
    }

    public function comprar() {
        $this->funcionarioRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect(URL_BASE . '/funcionario/loja');
        try {
            $this->inventarioService->comprar($_SESSION['usuario_logado']->getId(), $id);
            $_SESSION['mensagem'] = 'Item comprado com sucesso.';
        } catch (\Throwable $exception) {
            error_log($exception->__toString());
            $_SESSION['erro'] = $exception instanceof DomainException ? $exception->getMessage() : 'Não foi possível concluir a compra.';
        }
        $this->redirect(URL_BASE . '/funcionario/loja');
    }

    public function criar() {
        $this->adminRequired();
        $this->view('/administrador/itens/item_create', []);
    }

    private function removerImagemAntiga(?string $imagem): void {
        if (empty($imagem)) {
            return;
        }

        if (strpos($imagem, 'http://') === 0 || strpos($imagem, 'https://') === 0) {
            return;
        }

        if (str_starts_with(ltrim($imagem, '/'), 'assets/imagens/')) {
            return;
        }

        $caminhoNormalizado = ltrim($imagem, '/');
        $pathLocal = null;

        if (str_starts_with($caminhoNormalizado, 'assets/')) {
            $pathLocal = dirname(__DIR__, 2) . '/public/' . $caminhoNormalizado;
        } elseif (str_starts_with($caminhoNormalizado, 'public/')) {
            $pathLocal = dirname(__DIR__, 2) . '/' . $caminhoNormalizado;
        }

        if ($pathLocal !== null && is_file($pathLocal)) {
            @unlink($pathLocal);
        }
    }

    private function getImagemTemaPadrao(): string
    {
        return 'assets/imagens/imagemTEMA.png';
    }

    private function converterEstadoParaBit($estado): string {
        if ($estado === 'ATIVO' || $estado === '1' || $estado === 1 || $estado === true) {
            return 'ATIVO';
        }

        return 'INATIVO';
    }

    public function salvar() {
        $this->adminRequired();
        $_POST['estado'] = $_POST['estado'] ?? '1';
        if ($_POST['estado'] === '') {
            $_POST['estado'] = '1';
        }

        $erros = Validador::validarItem($_POST, $_FILES);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['item'] = $_POST;
            $this->view('/administrador/itens/item_create', $data);
            return;
        }

        $imagem = $_POST['tipo'] === 'TEMA'
            ? $this->getImagemTemaPadrao()
            : $this->processarUploadImagem($_FILES['imagem'], $erros);

        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['item'] = $_POST;
            $this->view('/administrador/itens/item_create', $data);
            return;
        }

        $item = new Item();
        $item->setEstado($this->converterEstadoParaBit($_POST['estado']));
        $item->setNome($_POST['nome']);
        $item->setTipo($_POST['tipo']);
        $item->setPreco($_POST['preco']);
        $item->setImagem($imagem);

        $this->itemService->saveItem($item);
        $this->redirect(URL_BASE . '/administrador/itens');
    }

    public function editar() {
        $this->adminRequired();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/itens');
        }
        $data['item'] = $this->itemService->getItemById($id);
        if (!$data['item']) $this->redirect(URL_BASE . '/administrador/itens');
        $this->view('/administrador/itens/item_edit', $data);
    }

    public function desativar(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $this->itemService->desativarItem($id);
        }
        $this->redirect(URL_BASE . '/administrador/itens');
    }

    public function ativar(){
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $this->itemService->ativarItem($id);
        }
        $this->redirect(URL_BASE . '/administrador/itens');
    }

    public function excluir(){
        $this->desativar();
    }

    public function atualizar() {
        $this->adminRequired();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->itemService->getItemById($id)) {
            $this->redirect(URL_BASE . '/administrador/itens/item_list');
        }

        $_POST['estado'] = $_POST['estado'] ?? '1';
        if ($_POST['estado'] === '') {
            $_POST['estado'] = '1';
        }

        $erros = Validador::validarItem($_POST, $_FILES);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $dadosItem = $_POST;
            $dadosItem['id_item'] = $id;
            $dadosItem['imagem'] = $_POST['imagem_atual'] ?? '';
            $data['item'] = Item::arrayParaObjeto($dadosItem);
            $this->view('/administrador/itens/item_edit', $data);
            return;
        }

        $imagem = $_POST['imagem_atual'] ?? '';
        if (($_POST['tipo'] ?? '') === 'TEMA') {
            $this->removerImagemAntiga($imagem);
            $imagem = $this->getImagemTemaPadrao();
        } elseif (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagemAntiga = $imagem;
            $novaImagem = $this->processarUploadImagem($_FILES['imagem'], $erros);
            if (!empty($erros)) {
                $data['erros'] = $erros;
                $dadosItem = $_POST;
                $dadosItem['id_item'] = $id;
                $dadosItem['imagem'] = $imagem;
                $data['item'] = Item::arrayParaObjeto($dadosItem);
                $this->view('/administrador/itens/item_edit', $data);
                return;
            }
            $this->removerImagemAntiga($imagemAntiga);
            $imagem = $novaImagem;
        }

        $item = new Item();
        $item->setId($id);
        $item->setEstado($this->converterEstadoParaBit($_POST['estado']));
        $item->setNome($_POST['nome']);
        $item->setTipo($_POST['tipo']);
        $item->setPreco($_POST['preco']);
        $item->setImagem($imagem);

        $this->itemService->updateItem($item);
        $this->redirect(URL_BASE . '/administrador/itens');
    }
}
