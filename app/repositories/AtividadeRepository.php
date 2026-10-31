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
        return array_map([Atividade::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function getAtividadeById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Atividades WHERE id_atividade = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $atividade = $stm->fetch();

        return $atividade ? Atividade::arrayParaObjeto($atividade) : null;
    }

    public function getAtividadesByModulo(int $idModulo): array {
        $stm = $this->connection->prepare(
            "SELECT * FROM Atividades WHERE id_modulo = :id_modulo ORDER BY ordem, id_atividade"
        );
        $stm->bindValue('id_modulo', $idModulo, PDO::PARAM_INT);
        $stm->execute();
        return array_map([Atividade::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function getAtividadesAtivasByModulo(int $idModulo): array {
        $stm = $this->connection->prepare(
            "SELECT * FROM Atividades WHERE id_modulo = :id_modulo AND estado = 'ATIVO' ORDER BY ordem, id_atividade"
        );
        $stm->execute(['id_modulo' => $idModulo]);
        return array_map([Atividade::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function saveAtividade(Atividade $atividade){
        $sql = "INSERT INTO Atividades (id_modulo, tipo_atividade, nome, descricao, estado, ordem, tempo_limite) VALUES (:idModulo, :tipoAtividade, :nome, :descricao, :estado, :ordem, :tempoLimite)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('idModulo', $atividade->getModulo()->getId());
        $stm->bindValue('tipoAtividade', $atividade->getTipoAtividade());
        $stm->bindValue('nome', $atividade->getNome());
        $stm->bindValue('descricao', $atividade->getDescricao());
        $stm->bindValue('estado', $atividade->getEstado());
        $stm->bindValue('ordem', $atividade->getOrdem(), PDO::PARAM_INT);
        $stm->bindValue('tempoLimite', $atividade->getTempoLimite(), PDO::PARAM_INT);
        return $stm->execute();
    }

    public function deleteAtividade(int $id){
        $stm = $this->connection->prepare("UPDATE Atividades SET estado = 'INATIVO' WHERE id_atividade = :id");
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function updateAtividade(Atividade $atividade){
        $sql = "UPDATE Atividades SET id_modulo = :idModulo, tipo_atividade = :tipoAtividade, nome = :nome, descricao = :descricao, estado = :estado, ordem = :ordem, tempo_limite = :tempoLimite WHERE id_atividade = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('idModulo', $atividade->getModulo()->getId());
        $stm->bindValue('tipoAtividade', $atividade->getTipoAtividade());
        $stm->bindValue('nome', $atividade->getNome());
        $stm->bindValue('descricao', $atividade->getDescricao());
        $stm->bindValue('estado', $atividade->getEstado());
        $stm->bindValue('ordem', $atividade->getOrdem(), PDO::PARAM_INT);
        $stm->bindValue('tempoLimite', $atividade->getTempoLimite(), PDO::PARAM_INT);
        $stm->bindValue('id', $atividade->getId());
        return $stm->execute();
    }

    public function countAtividades(): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Atividades");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }
}
