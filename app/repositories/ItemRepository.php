<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Item;
use PDO;

class ItemRepository{
    private PDO $connection;

    public function __construct(){
        $this->connection = ConnectionFactory::getConnection();
    }

    private function normalizeEstado($estado): string {
        if (is_string($estado) && strlen($estado) === 1) {
            return ord($estado) === 1 ? '1' : '0';
        }

        return (string)(int)$estado;
    }

    private function normalizeItem(array $item): array {
        if (isset($item['estado'])) {
            $item['estado'] = $this->normalizeEstado($item['estado']);
        }

        return $item;
    }

    public function getItems(): array{
        $stm = $this->connection->prepare("SELECT * FROM Itens");
        $stm->execute();
        $items = $stm->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as &$item) {
            $item = $this->normalizeItem($item);
        }

        return $items;
    }

    public function getItemsAtivos(): array{
        $stm = $this->connection->prepare("SELECT * FROM Itens WHERE estado = b'1'");
        $stm->execute();
        $items = $stm->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as &$item) {
            $item = $this->normalizeItem($item);
        }

        return $items;
    }

    public function getItemById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Itens WHERE id_item = :id");
        $stm->bindValue('id', $id, PDO::PARAM_INT);

        $stm->execute();

        $item = $stm->fetch(PDO::FETCH_ASSOC);

        return $item ? $this->normalizeItem($item) : null;
    }

    public function saveItem(Item $item){
        $sql = "INSERT INTO Itens (estado, nome, tipo, preco, imagem) VALUES (:estado, :nome, :tipo, :preco, :imagem)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('estado', (int) $item->getEstado(), PDO::PARAM_INT);
        $stm->bindValue('nome', $item->getNome());
        $stm->bindValue('tipo', $item->getTipo());
        $stm->bindValue('preco', $item->getPreco());
        $stm->bindValue('imagem', $item->getImagem());
        return $stm->execute();
    }

    public function desativarItem(int $id): bool {
        $stm = $this->connection->prepare("UPDATE Itens SET estado = b'0' WHERE id_item = :id");
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function ativarItem(int $id): bool {
        $stm = $this->connection->prepare("UPDATE Itens SET estado = b'1' WHERE id_item = :id");
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function deleteItem(int $id): bool {
        return $this->desativarItem($id);
    }

    public function updateItem(Item $item){
        $sql = "UPDATE Itens SET estado = :estado, nome = :nome, tipo = :tipo, preco = :preco, imagem = :imagem WHERE id_item = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('estado', (int) $item->getEstado(), PDO::PARAM_INT);
        $stm->bindValue('nome', $item->getNome());
        $stm->bindValue('tipo', $item->getTipo());
        $stm->bindValue('preco', $item->getPreco());
        $stm->bindValue('imagem', $item->getImagem());
        $stm->bindValue('id', $item->getId(), PDO::PARAM_INT);
        return $stm->execute();
    }

    public function countItens(): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Itens WHERE estado = b'1'");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }
}
