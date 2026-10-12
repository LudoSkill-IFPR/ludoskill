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
        $stm = $this->connection->prepare("SELECT * FROM Modulos ORDER BY id_modulo");
        $stm->execute();
        return array_map([Modulo::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function getModulosAtivos(): array {
        $stm = $this->connection->prepare("SELECT * FROM Modulos WHERE estado = 'ATIVO' ORDER BY id_modulo");
        $stm->execute();
        return array_map([Modulo::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function getModuloById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Modulos WHERE id_modulo = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $modulo = $stm->fetch();
        return $modulo ? Modulo::arrayParaObjeto($modulo) : null;
    }

    public function nomeExiste(string $nome, ?int $idIgnorado = null): bool {
        $sql = 'SELECT 1 FROM Modulos WHERE nome = :nome';
        if ($idIgnorado !== null) $sql .= ' AND id_modulo <> :id';
        $sql .= ' LIMIT 1';
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nome', $nome);
        if ($idIgnorado !== null) $stm->bindValue('id', $idIgnorado, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchColumn() !== false;
    }

    public function saveModulo(Modulo $modulo){
        $sql = "INSERT INTO Modulos (nome, descricao, min_estrelas_liberacao, estado) VALUES (:nome, :descricao, :minEstrelasLiberacao, :estado)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nome', $modulo->getNome());
        $stm->bindValue('descricao', $modulo->getDescricao());
        $stm->bindValue('minEstrelasLiberacao', $modulo->getMinEstrelasLiberacao());
        $stm->bindValue('estado', $modulo->getEstado());
        return $stm->execute();
    }

    public function deleteModulo(int $id){
        $stm = $this->connection->prepare("UPDATE Modulos SET estado = 'INATIVO' WHERE id_modulo = :id");
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function updateModulo(Modulo $modulo){
        $sql = "UPDATE Modulos SET nome = :nome, descricao = :descricao, min_estrelas_liberacao = :minEstrelasLiberacao, estado = :estado WHERE id_modulo = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nome', $modulo->getNome());
        $stm->bindValue('descricao', $modulo->getDescricao());
        $stm->bindValue('minEstrelasLiberacao', $modulo->getMinEstrelasLiberacao());
        $stm->bindValue('estado', $modulo->getEstado());
        $stm->bindValue('id', $modulo->getId());
        return $stm->execute();
    }

    public function countModulos(): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Modulos");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }
}
