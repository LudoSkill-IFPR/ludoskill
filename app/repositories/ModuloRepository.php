<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Modulo;
use PDO;

class ModuloRepository {
    private PDO $connection;

    public function __construct() {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getModulos(): array {
        $stm = $this->connection->prepare("SELECT * FROM Modulos");
        $stm->execute();
        $modulos = $stm->fetchAll();
        return $modulos;
    }

    public function getModuloById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Modulos WHERE id_modulo = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $modulo = $stm->fetch();

        return $modulo;
    }

    public function saveModulo(Modulo $modulo){
        $sql = "INSERT INTO Modulos (nome, descricao, min_estrelas_liberacao) VALUES (:nome, :descricao, :minEstrelasLiberacao)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nome', $modulo->getNome());
        $stm->bindValue('descricao', $modulo->getDescricao());
        $stm->bindValue('minEstrelasLiberacao', $modulo->getMinEstrelasLiberacao());
        return $stm->execute();
    }

    public function deleteModulo(int $id){
        $stm = $this->connection->prepare("DELETE FROM Modulos WHERE id_modulo = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateModulo(Modulo $modulo){
        $sql = "UPDATE Modulos SET nome = :nome, descricao = :descricao, min_estrelas_liberacao = :minEstrelasLiberacao WHERE id_modulo = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nome', $modulo->getNome());
        $stm->bindValue('descricao', $modulo->getDescricao());
        $stm->bindValue('minEstrelasLiberacao', $modulo->getMinEstrelasLiberacao());
        $stm->bindValue('id', $modulo->getId());
        return $stm->execute();
    }
}