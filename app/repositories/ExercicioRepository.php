<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Exercicio;
use PDO;

class ExercicioRepository {
    private PDO $connection;

    public function __construct() {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getExercicios(): array {
        $stm = $this->connection->prepare(
            "SELECT e.*, a.nome AS nome_atividade
             FROM Exercicios e
             INNER JOIN Atividades a ON a.id_atividade = e.id_atividade
             ORDER BY e.id_exercicio DESC"
        );
        $stm->execute();
        $exercicios = $stm->fetchAll();
        return $exercicios;
    }

    public function getExercicioById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Exercicios WHERE id_exercicio = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $exercicio = $stm->fetch();

        return $exercicio;
    }

    public function getExerciciosByAtividade(int $idAtividade): array {
        $stm = $this->connection->prepare(
            "SELECT e.*, a.nome AS nome_atividade
             FROM Exercicios e
             INNER JOIN Atividades a ON a.id_atividade = e.id_atividade
             WHERE e.id_atividade = :id_atividade
             ORDER BY e.id_exercicio"
        );
        $stm->bindValue('id_atividade', $idAtividade, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll();
    }

    public function saveExercicio(Exercicio $exercicio){
        $sql = "INSERT INTO Exercicios (id_atividade, descricao, conteudo) VALUES (:id_atividade, :descricao, :conteudo)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('id_atividade', $exercicio->getAtividade()->getId());
        $stm->bindValue('descricao', $exercicio->getDescricao());
        $stm->bindValue('conteudo', $exercicio->getConteudo());
        return $stm->execute();
    }

    public function deleteExercicio(int $id){
        $stm = $this->connection->prepare("DELETE FROM Exercicios WHERE id_exercicio = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateExercicio(Exercicio $exercicio){
        $sql = "UPDATE Exercicios SET id_atividade = :id_atividade, descricao = :descricao, conteudo = :conteudo WHERE id_exercicio = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('id_atividade', $exercicio->getAtividade()->getId());
        $stm->bindValue('descricao', $exercicio->getDescricao());
        $stm->bindValue('conteudo', $exercicio->getConteudo());
        $stm->bindValue('id', $exercicio->getId());
        return $stm->execute();
    }
}
