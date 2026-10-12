<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Funcionario;
use app\models\Empresa;
use PDO;

class FuncionarioRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getFuncionarios(): array
    {
        $sql = "SELECT f.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                       u.email, u.numero_telefone, u.estado
                FROM Funcionarios f
                INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
                ORDER BY f.pontuacao_total DESC, u.nome_completo";
        $stm = $this->connection->prepare($sql);
        $stm->execute();
        return array_map([Funcionario::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function getFuncionarioByUsuario(int $id_usuario): ?Funcionario
    {
        $stm = $this->connection->prepare(
            "SELECT f.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                    u.email, u.senha_hash, u.numero_telefone, u.estado,
                    e.cnpj AS empresa_cnpj, e.nome AS empresa_nome,
                    e.email AS empresa_email, e.plano AS empresa_plano
             FROM Funcionarios f
             INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
             INNER JOIN Empresas e ON e.id_empresa = f.id_empresa
             WHERE f.id_usuario = :idUsuario"
        );
        $stm->bindValue('idUsuario', $id_usuario, PDO::PARAM_INT);
        $stm->execute();
        $funcionario = $stm->fetch();
        if (!$funcionario) return null;
        $empresa = new Empresa(
            (int) $funcionario['id_empresa'],
            (string) $funcionario['empresa_cnpj'],
            (string) $funcionario['empresa_nome'],
            (string) $funcionario['empresa_email'],
            (string) $funcionario['empresa_plano']
        );
        return Funcionario::arrayParaObjeto($funcionario, $empresa);
    }

    public function getFuncionariosByEmpresa(int $idEmpresa): array
    {
        $sql = "SELECT f.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                       u.email, u.numero_telefone, u.estado
                FROM Funcionarios f
                INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
                WHERE f.id_empresa = :idEmpresa
                ORDER BY u.nome_completo";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);
        $stm->execute();
        return array_map([Funcionario::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function getFuncionarioByIdAndEmpresa(int $id, int $idEmpresa)
    {
        $sql = "SELECT f.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                       u.email, u.senha_hash, u.numero_telefone, u.estado
                FROM Funcionarios f
                INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
                WHERE f.id_funcionario = :id AND f.id_empresa = :idEmpresa";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);

        $stm->execute();

        $funcionario = $stm->fetch();
        return $funcionario ? Funcionario::arrayParaObjeto($funcionario) : null;
    }

    public function getEmpresaIdByGestorUsuario(int $idUsuario): ?int
    {
        $stm = $this->connection->prepare(
            "SELECT id_empresa FROM Gestores WHERE id_usuario = :idUsuario"
        );
        $stm->bindValue('idUsuario', $idUsuario, PDO::PARAM_INT);
        $stm->execute();
        $idEmpresa = $stm->fetchColumn();
        return $idEmpresa === false ? null : (int) $idEmpresa;
    }

    public function cpfExiste(string $cpf, ?int $idFuncionarioIgnorado = null): bool
    {
        $sql = "SELECT 1 FROM Usuarios u
                LEFT JOIN Funcionarios f ON f.id_usuario = u.id_usuario
                WHERE u.CPF = :cpf";

        if ($idFuncionarioIgnorado !== null) {
            $sql .= " AND (f.id_funcionario IS NULL OR f.id_funcionario <> :idFuncionario)";
        }

        $sql .= " LIMIT 1";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('cpf', $cpf);

        if ($idFuncionarioIgnorado !== null) {
            $stm->bindValue('idFuncionario', $idFuncionarioIgnorado, PDO::PARAM_INT);
        }

        $stm->execute();
        return $stm->fetchColumn() !== false;
    }

    public function emailExiste(string $email, ?int $idFuncionarioIgnorado = null): bool
    {
        $sql = "SELECT 1 FROM Usuarios u
                LEFT JOIN Funcionarios f ON f.id_usuario = u.id_usuario
                WHERE u.email = :email";

        if ($idFuncionarioIgnorado !== null) {
            $sql .= " AND (f.id_funcionario IS NULL OR f.id_funcionario <> :idFuncionario)";
        }

        $sql .= " LIMIT 1";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('email', $email);

        if ($idFuncionarioIgnorado !== null) {
            $stm->bindValue('idFuncionario', $idFuncionarioIgnorado, PDO::PARAM_INT);
        }

        $stm->execute();
        return $stm->fetchColumn() !== false;
    }

    public function saveFuncionario(Funcionario $funcionario): bool
    {
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

    public function desativarFuncionario(int $id, int $idEmpresa): bool
    {
        $stm = $this->connection->prepare(
            "UPDATE Usuarios u
         INNER JOIN Funcionarios f ON f.id_usuario = u.id_usuario
         SET u.estado = 'INATIVO'
         WHERE f.id_funcionario = :id
         AND f.id_empresa = :idEmpresa"
        );

        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);

        return $stm->execute();
    }

    public function ativarFuncionario(int $id, int $idEmpresa): bool
    {
        $stm = $this->connection->prepare(
            "UPDATE Usuarios u
         INNER JOIN Funcionarios f ON f.id_usuario = u.id_usuario
         SET u.estado = 'ATIVO'
         WHERE f.id_funcionario = :id
         AND f.id_empresa = :idEmpresa"
        );

        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);

        return $stm->execute();
    }

    public function updateFuncionario(Funcionario $funcionario, int $idEmpresa, ?string $novaSenha = null): bool
    {
        $atual = $this->getFuncionarioByIdAndEmpresa($funcionario->getIdFuncionario(), $idEmpresa);
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
        $stm->bindValue('id', $atual->getId(), PDO::PARAM_INT);
        if ($novaSenha !== null && $novaSenha !== '') {
            $stm->bindValue('senha', password_hash($novaSenha, PASSWORD_DEFAULT));
        }
        return $stm->execute();
    }

    public function countFuncionarios(): int
    {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Funcionarios f INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario WHERE u.estado = 'ATIVO'");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }

    public function countFuncionariosByEmpresa(int $idEmpresa): int
    {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Funcionarios f INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario WHERE f.id_empresa = :idEmpresa AND u.estado = 'ATIVO'");
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);
        $stm->execute();
        return (int) $stm->fetchColumn();
    }
}
