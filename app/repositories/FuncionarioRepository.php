<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Funcionario;
use PDO;

class FuncionarioRepository{
    private PDO $connection;

    public function __construct() {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getFuncionarios(): array {
        $stm = $this->connection->prepare("SELECT * FROM Funcionarios");
        $stm->execute();
        return $stm->fetchAll();
    }

    public function getFuncionariosByEmpresa(int $idEmpresa): array {
        $sql = "SELECT f.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                       u.email, u.numero_telefone
                FROM Funcionarios f
                INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
                WHERE f.id_empresa = :idEmpresa
                ORDER BY u.nome_completo";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll();
    }

    public function getFuncionarioByIdAndEmpresa(int $id, int $idEmpresa){
        $sql = "SELECT f.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                       u.email, u.senha_hash, u.numero_telefone
                FROM Funcionarios f
                INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
                WHERE f.id_funcionario = :id AND f.id_empresa = :idEmpresa";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);

        $stm->execute();

        return $stm->fetch() ?: null;
    }

    public function getEmpresaIdByGestorUsuario(int $idUsuario): ?int {
        $stm = $this->connection->prepare(
            "SELECT id_empresa FROM Gestores WHERE id_usuario = :idUsuario"
        );
        $stm->bindValue('idUsuario', $idUsuario, PDO::PARAM_INT);
        $stm->execute();
        $idEmpresa = $stm->fetchColumn();
        return $idEmpresa === false ? null : (int) $idEmpresa;
    }

    public function saveFuncionario(Funcionario $funcionario): bool {
        try {
            $this->connection->beginTransaction();
            $sql = "INSERT INTO Usuarios (nome_completo, data_nascimento, CPF, email, senha_hash, numero_telefone) VALUES (:nomeCompleto, :dataNascimento, :cpf, :email, :senha_hash, :numeroTelefone)";
            $stm = $this->connection->prepare($sql);
            $stm->execute([
                'nomeCompleto' => $funcionario->getNomeCompleto(),
                'dataNascimento' => $funcionario->getDataNascimento()->format('Y-m-d'),
                'cpf' => $funcionario->getCpf(),
                'email' => $funcionario->getEmail(),
                'senha_hash' => password_hash($funcionario->getSenha(), PASSWORD_DEFAULT),
                'numeroTelefone' => $funcionario->getNumeroTelefone(),
            ]);

            $sql = "INSERT INTO Funcionarios (id_usuario, bolotas_totais, pontuacao_total, nivel, id_empresa) VALUES (:idUsuario, :bolotasTotais, :pontuacaoTotal, :nivel, :idEmpresa)";
            $stm = $this->connection->prepare($sql);
            $stm->execute([
                'idUsuario' => (int) $this->connection->lastInsertId(),
                'bolotasTotais' => $funcionario->getBolotasTotais(),
                'pontuacaoTotal' => $funcionario->getPontuacaoTotal(),
                'nivel' => $funcionario->getNivel(),
                'idEmpresa' => $funcionario->getEmpresa()->getId(),
            ]);
            return $this->connection->commit();
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    public function deleteFuncionario(int $id, int $idEmpresa): bool {
        $stm = $this->connection->prepare(
            "DELETE u FROM Usuarios u INNER JOIN Funcionarios f ON f.id_usuario = u.id_usuario WHERE f.id_funcionario = :id AND f.id_empresa = :idEmpresa"
        );
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function updateFuncionario(Funcionario $funcionario, int $idEmpresa, ?string $novaSenha = null): bool {
        $atual = $this->getFuncionarioByIdAndEmpresa($funcionario->getId(), $idEmpresa);
        if (!$atual) {
            return false;
        }

        $senhaSql = $novaSenha !== null && $novaSenha !== '' ? ', senha_hash = :senha' : '';
        $sql = "UPDATE Usuarios SET nome_completo = :nomeCompleto, data_nascimento = :dataNascimento, CPF = :cpf, email = :email, numero_telefone = :numeroTelefone{$senhaSql} WHERE id_usuario = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nomeCompleto', $funcionario->getNomeCompleto());
        $stm->bindValue('dataNascimento', $funcionario->getDataNascimento()->format('Y-m-d'));
        $stm->bindValue('cpf', $funcionario->getCpf());
        $stm->bindValue('email', $funcionario->getEmail());
        $stm->bindValue('numeroTelefone', $funcionario->getNumeroTelefone());
        $stm->bindValue('id', $atual['id_usuario'], PDO::PARAM_INT);
        if ($novaSenha !== null && $novaSenha !== '') {
            $stm->bindValue('senha', password_hash($novaSenha, PASSWORD_DEFAULT));
        }
        $stm->execute();

        $sql = "UPDATE Funcionarios SET bolotas_totais = :bolotasTotais, pontuacao_total = :pontuacaoTotal, nivel = :nivel WHERE id_funcionario = :id AND id_empresa = :idEmpresa";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('id', $funcionario->getId(), PDO::PARAM_INT);
        $stm->bindValue('bolotasTotais', $funcionario->getBolotasTotais());
        $stm->bindValue('pontuacaoTotal', $funcionario->getPontuacaoTotal());
        $stm->bindValue('nivel', $funcionario->getNivel());
        $stm->bindValue('idEmpresa', $funcionario->getEmpresa()->getId());
        return $stm->execute();
    }

    public function countFuncionarios(): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Funcionarios");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }

    public function countFuncionariosByEmpresa(int $idEmpresa): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Funcionarios WHERE id_empresa = :idEmpresa");
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);
        $stm->execute();
        return (int) $stm->fetchColumn();
    }

}
