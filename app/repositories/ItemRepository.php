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

    public function getItems(): array{
        $stm = $this->connection->prepare("SELECT * FROM Itens");
        $stm->execute();
        $items = $stm->fetchAll();
        return $items;
    }

    public function getItemById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Itens WHERE id_item = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $item = $stm->fetch();

        return $item;
    }

    public function saveItem(Item $item){
        $sql = "INSERT INTO Itens (estado, nome, tipo, preco, imagem) VALUES (:estado, :nome, :tipo, :preco, :imagem)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('estado', $item->getEstado());
        $stm->bindValue('nome', $item->getNome());
        $stm->bindValue('tipo', $item->getTipo());
        $stm->bindValue('preco', $item->getPreco());
        $stm->bindValue('imagem', $item->getImagem());
        return $stm->execute();
    }

    public function deleteItem(int $id){
        $stm = $this->connection->prepare("DELETE FROM Itens WHERE id_item = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateItem(Item $item){
        $sql = "UPDATE Itens SET estado = :estado, nome = :nome, tipo = :tipo, preco = :preco, imagem = :imagem WHERE id_item = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('estado', $item->getEstado());
        $stm->bindValue('nome', $item->getNome());
        $stm->bindValue('tipo', $item->getTipo());
        $stm->bindValue('preco', $item->getPreco());
        $stm->bindValue('imagem', $item->getImagem());
        $stm->bindValue('id', $item->getId());
        return $stm->execute();
    }
}