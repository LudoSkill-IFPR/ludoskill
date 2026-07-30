<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Atividade;
use PDO;

class AtividadeRepository{
    private PDO $connection;

    public function __construct() {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getAtividades(): array {
        $stm = $this->connection->prepare("SELECT * FROM Atividades");
        $stm->execute();
        $atividades = $stm->fetchAll();
        return $atividades;
    }

    public function getAtividadeById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Atividades WHERE id_atividade = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $atividade = $stm->fetch();

        return $atividade;
    }

    public function saveAtividade(Atividade $atividade){
        $sql = "INSERT INTO Atividades (id_modulo, tipo_atividade, nome, estado, pontuacao, estrelas) VALUES (:idModulo, :tipoAtividade, :nome, :estado, :pontuacao, :estrelas)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('idModulo', $atividade->getModulo()->getId());
        $stm->bindValue('tipoAtividade', $atividade->getTipoAtividade());
        $stm->bindValue('nome', $atividade->getNome());
        $stm->bindValue('estado', $atividade->getEstado());
        $stm->bindValue('pontuacao', $atividade->getPontuacao());
        $stm->bindValue('estrelas', $atividade->getEstrelas());
        return $stm->execute();
    }

    public function deleteAtividade(int $id){
        $stm = $this->connection->prepare("DELETE FROM Atividades WHERE id_atividade = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateAtividade(Atividade $atividade){
        $sql = "UPDATE Atividades SET id_modulo = :idModulo, tipo_atividade = :tipoAtividade, nome = :nome, estado = :estado, pontuacao = :pontuacao, estrelas = :estrelas WHERE id_atividade = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('idModulo', $atividade->getModulo()->getId());
        $stm->bindValue('tipoAtividade', $atividade->getTipoAtividade());
        $stm->bindValue('nome', $atividade->getNome());
        $stm->bindValue('estado', $atividade->getEstado());
        $stm->bindValue('pontuacao', $atividade->getPontuacao());
        $stm->bindValue('estrelas', $atividade->getEstrelas());
        $stm->bindValue('id', $atividade->getId());
        return $stm->execute();
    }
}
