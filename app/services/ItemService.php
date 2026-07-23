<?php 

namespace app\services;

use app\models\Item;
use app\repositories\ItemRepository;

class ItemService {
    private ItemRepository $itemRepository;

    public function __construct() {
        $this->itemRepository = new ItemRepository();
    }

    public function getItems(): array {
        return $this->itemRepository->getItems();
    }

    public function getItemById(int $id) {
        return $this->itemRepository->getItemById($id);
    }

    public function saveItem(Item $item) {
        return $this->itemRepository->saveItem($item);
    }

    public function deleteItem(int $id) {
        return $this->itemRepository->deleteItem($id);
    }

    public function updateItem(Item $item) {
        return $this->itemRepository->updateItem($item);
    }
}