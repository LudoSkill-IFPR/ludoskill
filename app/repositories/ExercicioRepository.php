<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Exercicio;
use PDO;

class ExercicioRepository {
    private PDO $connection;

    /**
     * Obtém e guarda a conexão PDO compartilhada usada por todas as consultas do repository.
     */
    public function __construct() {
        $this->connection = ConnectionFactory::getConnection();
    }

    /**
     * Busca todos os exercícios para a tela administrativa.
     *
     * O JOIN recupera também o nome da atividade vinculada. Depois da consulta,
     * cada linha é convertida de array para Exercicio pelo método arrayParaObjeto().
     */
    public function getExercicios(): array {
        $stm = $this->connection->prepare(
            "SELECT e.*, a.nome AS nome_atividade
             FROM Exercicios e
             INNER JOIN Atividades a ON a.id_atividade = e.id_atividade
             ORDER BY e.id_exercicio DESC"
        );
        $stm->execute();
        return array_map([Exercicio::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function getExercicioById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Exercicios WHERE id_exercicio = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $exercicio = $stm->fetch();

        return $exercicio ? Exercicio::arrayParaObjeto($exercicio) : null; //se o registro existir, converte para objeto; senão, retorna null
    }

    /**
     * Busca, em ordem de cadastro, todos os exercícios pertencentes a uma atividade.
     *
     * Essa consulta é usada para montar a lista completa de questões que o
     * funcionário responderá. O resultado também inclui o nome da atividade.
     */
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
        return array_map([Exercicio::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    /**
     * Verifica de forma econômica se existe ao menos um exercício vinculado à atividade.
     */
    public function existeExercicioNaAtividade(int $idAtividade): bool {
        $stm = $this->connection->prepare(
            'SELECT 1 FROM Exercicios WHERE id_atividade = :id_atividade LIMIT 1'
        );
        $stm->bindValue('id_atividade', $idAtividade, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchColumn() !== false;
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
