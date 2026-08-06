<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Item;
use app\services\ItemService;
use app\helpers\Validador;

class ItemController extends Controller 
{
    private ItemService $itemService;

    public function __construct() {
        $this->itemService = new ItemService();
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
        $data['lista'] = $this->itemService->getItems();
        $this->view('/administrador/itens/item_list', $data);
    }

    public function listarItem() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/administrador/itens/item_list');
        }

        $id = $_GET['id'];
        $data['item'] = $this->itemService->getItemById($id);
        $this->view('/administrador/itens/itens_show', $data);
    }

    public function criar() {
        $this->view('/administrador/itens/item_create', []);
    }

    private function converterEstadoParaBit($estado): int {
        if ($estado === 'ATIVO' || $estado === '1' || $estado === 1 || $estado === true) {
            return 1;
        }

        return 0;
    }

    public function salvar() {
        $erros = Validador::validarItem($_POST, $_FILES);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['item'] = $_POST;
            $this->view('/administrador/itens/item_create', $data);
            return;
        }

        $imagem = $this->processarUploadImagem($_FILES['imagem'], $erros);
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
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/administrador/itens');
        }

        $id = $_GET['id'];
        $data['item'] = $this->itemService->getItemById($id);
        $this->view('/administrador/itens/item_edit', $data);
    }

    public function excluir(){
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect(URL_BASE . '/administrador/itens/item_list');
        }

        $this->itemService->deleteItem($id);
        $this->redirect(URL_BASE . '/administrador/itens');
    }

    public function atualizar() {
        if (!isset($_POST['id'])) {
            $this->redirect(URL_BASE . '/administrador/itens/item_list');
        }

        $erros = Validador::validarItem($_POST, $_FILES);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['item'] = $_POST;
            $this->view('/administrador/itens', $data);
            return;
        }

        $imagem = $_POST['imagem_atual'] ?? '';
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagem = $this->processarUploadImagem($_FILES['imagem'], $erros);
            if (!empty($erros)) {
                $data['erros'] = $erros;
                $data['item'] = $_POST;
                $this->view('/administrador/itens', $data);
                return;
            }
        }

        $item = new Item();
        $item->setId($_POST['id']);
        $item->setEstado($this->converterEstadoParaBit($_POST['estado']));
        $item->setNome($_POST['nome']);
        $item->setTipo($_POST['tipo']);
        $item->setPreco($_POST['preco']);
        $item->setImagem($imagem);

        $this->itemService->updateItem($item);
        $this->redirect(URL_BASE . '/administrador/itens');
    }
}