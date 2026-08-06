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
        $this->view('/administrador/itens/items_show', $data);
    }

    public function criar() {
        $this->view('items/item_create', []);
    }

    public function salvar() {
        $erros = Validador::validarItem($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['item'] = $_POST;
            $this->view('/administrador/itens/item_create', $data);
            return;
        }

        $item = new Item();
        $item->setEstado($_POST['estado']);
        $item->setNome($_POST['nome']);
        $item->setTipo($_POST['tipo']);
        $item->setPreco($_POST['preco']);
        $item->setImagem($_POST['imagem']);

        $this->itemService->saveItem($item);
        $this->redirect(URL_BASE . '/administrador/itens/item_list');
    }

    public function editar() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/items');
        }

        $id = $_GET['id'];
        $data['item'] = $this->itemService->getItemById($id);
        $this->view('items/item_edit', $data);
    }

    public function excluir(){
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/items');
        }

        $id = $_GET['id'];
        $this->itemService->deleteItem($id);
        $this->redirect(URL_BASE . '/items');
    }

    public function atualizar() {
        if (!isset($_POST['id'])) {
            $this->redirect(URL_BASE . '/items');
        }

        $erros = Validador::validarItem($_POST);
        if (!empty($erros)) {
            $data['erros'] = $erros;
            $data['item'] = $_POST;
            $this->view('items/item_edit', $data);
            return;
        }

        $item = new Item();
        $item->setId($_POST['id']);
        $item->setEstado($_POST['estado']);
        $item->setNome($_POST['nome']);
        $item->setTipo($_POST['tipo']);
        $item->setPreco($_POST['preco']);
        $item->setImagem($_POST['imagem']);

        $this->itemService->updateItem($item);
        $this->redirect(URL_BASE . '/items');
    }
}