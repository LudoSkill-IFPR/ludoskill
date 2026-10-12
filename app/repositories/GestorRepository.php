<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Gestor;
use app\models\Empresa;
use PDO;

class GestorRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getGestores(): array
    {
        $stm = $this->connection->prepare(
            "SELECT g.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                    u.email, u.numero_telefone, u.estado, e.nome AS nome_empresa
             FROM Gestores g
             INNER JOIN Usuarios u ON u.id_usuario = g.id_usuario
             INNER JOIN Empresas e ON e.id_empresa = g.id_empresa
             ORDER BY u.nome_completo"
        );
        $stm->execute();
        return array_map(function (array $registro): Gestor {
            $empresa = new \app\models\Empresa(
                (int) $registro['id_empresa'],
                '',
                (string) ($registro['nome_empresa'] ?? '')
            );
            return Gestor::arrayParaObjeto($registro, $empresa);
        }, $stm->fetchAll());
    }

    public function getGestorById(int $id)
    {
        $stm = $this->connection->prepare(
            "SELECT g.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                    u.email, u.senha_hash, u.numero_telefone, u.estado
             FROM Gestores g INNER JOIN Usuarios u ON u.id_usuario = g.id_usuario
             WHERE g.id_gestor = :id"
        );
        $stm->bindValue('id', $id);

        $stm->execute();

        $gestor = $stm->fetch();

        return $gestor ? Gestor::arrayParaObjeto($gestor) : null;
    }

    public function getGestorByUsuario(int $idUsuario): ?Gestor
    {
        $stm = $this->connection->prepare(
            "SELECT g.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                    u.email, u.senha_hash, u.numero_telefone, u.estado,
                    e.cnpj AS empresa_cnpj, e.nome AS empresa_nome,
                    e.email AS empresa_email, e.plano AS empresa_plano
             FROM Gestores g
             INNER JOIN Usuarios u ON u.id_usuario = g.id_usuario
             INNER JOIN Empresas e ON e.id_empresa = g.id_empresa
             WHERE g.id_usuario = :idUsuario"
        );
        $stm->bindValue('idUsuario', $idUsuario, PDO::PARAM_INT);
        $stm->execute();
        $gestor = $stm->fetch();
        if (!$gestor) return null;
        $empresa = new Empresa(
            (int) $gestor['id_empresa'],
            (string) $gestor['empresa_cnpj'],
            (string) $gestor['empresa_nome'],
            (string) $gestor['empresa_email'],
            (string) $gestor['empresa_plano']
        );
        return Gestor::arrayParaObjeto($gestor, $empresa);
    }

    public function saveGestor(Gestor $gestor): bool
    {
        try {
            $this->connection->beginTransaction();
            $stm = $this->connection->prepare("INSERT INTO Usuarios (nome_completo, data_nascimento, CPF, email, senha_hash, numero_telefone) VALUES (:nome, :data, :cpf, :email, :senha, :telefone)");
            $stm->execute([
                'nome' => $gestor->getNomeCompleto(),
                'data' => $gestor->getDataNascimento()->format('Y-m-d'),
                'cpf' => $gestor->getCpf(),
                'email' => $gestor->getEmail(),
                'senha' => password_hash($gestor->getSenha(), PASSWORD_DEFAULT),
                'telefone' => $gestor->getNumeroTelefone()
            ]);
            $stm = $this->connection->prepare("INSERT INTO Gestores (id_usuario, id_empresa) VALUES (:usuario, :empresa)");
            $stm->execute(['usuario' => (int) $this->connection->lastInsertId(), 'empresa' => $gestor->getEmpresa()->getId()]);
            return $this->connection->commit();
        } catch (\Throwable $e) {
            if ($this->connection->inTransaction()) $this->connection->rollBack();
            throw $e;
        }
    }

    public function cpfExiste(string $cpf, ?int $idGestorIgnorado = null): bool
    {
        $sql = 'SELECT 1 FROM Usuarios u LEFT JOIN Gestores g ON g.id_usuario = u.id_usuario WHERE u.CPF = :valor';
        if ($idGestorIgnorado !== null) $sql .= ' AND (g.id_gestor IS NULL OR g.id_gestor <> :id)';
        $sql .= ' LIMIT 1';
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('valor', $cpf);
        if ($idGestorIgnorado !== null) $stm->bindValue('id', $idGestorIgnorado, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchColumn() !== false;
    }

    public function emailExiste(string $email, ?int $idGestorIgnorado = null): bool
    {
        $sql = 'SELECT 1 FROM Usuarios u LEFT JOIN Gestores g ON g.id_usuario = u.id_usuario WHERE u.email = :valor';
        if ($idGestorIgnorado !== null) $sql .= ' AND (g.id_gestor IS NULL OR g.id_gestor <> :id)';
        $sql .= ' LIMIT 1';
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('valor', $email);
        if ($idGestorIgnorado !== null) $stm->bindValue('id', $idGestorIgnorado, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchColumn() !== false;
    }

    public function desativarGestor(int $id): bool
    {
        $stm = $this->connection->prepare(
            "UPDATE Usuarios u
         INNER JOIN Gestores g ON g.id_usuario = u.id_usuario
         SET u.estado = 'INATIVO'
         WHERE g.id_gestor = :id"
        );

        $stm->bindValue('id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function ativarGestor(int $id): bool
    {
        $stm = $this->connection->prepare(
            "UPDATE Usuarios u
         INNER JOIN Gestores g ON g.id_usuario = u.id_usuario
         SET u.estado = 'ATIVO'
         WHERE g.id_gestor = :id"
        );

        $stm->bindValue('id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function updateGestor(Gestor $gestor, int $idGestor, ?string $novaSenha = null)
    {
        $atual = $this->getGestorById($idGestor);
        if (!$atual) return false;
        $this->connection->beginTransaction();
        try {
            $senhaSql = $novaSenha !== null && $novaSenha !== '' ? ', senha_hash = :senha' : '';
            $sql = "UPDATE Usuarios SET nome_completo = :nomeCompleto, data_nascimento = :dataNascimento, CPF = :cpf, email = :email, numero_telefone = :numeroTelefone{$senhaSql} WHERE id_usuario = :idUsuario";
            $stm = $this->connection->prepare($sql);
            $stm->bindValue('nomeCompleto', $gestor->getNomeCompleto());
            $stm->bindValue('dataNascimento', $gestor->getDataNascimento()->format('Y-m-d'));
            $stm->bindValue('cpf', $gestor->getCpf());
            $stm->bindValue('email', $gestor->getEmail());
            $stm->bindValue('numeroTelefone', $gestor->getNumeroTelefone());
            $stm->bindValue('idUsuario', $atual->getId(), PDO::PARAM_INT);
            if ($novaSenha !== null && $novaSenha !== '') $stm->bindValue('senha', password_hash($novaSenha, PASSWORD_DEFAULT));
            $stm->execute();
            $stm = $this->connection->prepare("UPDATE Gestores SET id_empresa = :empresa WHERE id_gestor = :id");
            $stm->execute(['empresa' => $gestor->getEmpresa()->getId(), 'id' => $idGestor]);
            return $this->connection->commit();
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) $this->connection->rollBack();
            throw $exception;
        }
    }

    public function countGestores(): int
    {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Gestores g INNER JOIN Usuarios u ON u.id_usuario = g.id_usuario WHERE u.estado = 'ATIVO'");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }

    public function countGestoresByEmpresa(int $idEmpresa): int
    {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Gestores g INNER JOIN Usuarios u ON u.id_usuario = g.id_usuario WHERE g.id_empresa = :idEmpresa AND u.estado = 'ATIVO'");
        $stm->bindValue('idEmpresa', $idEmpresa, PDO::PARAM_INT);
        $stm->execute();
        return (int) $stm->fetchColumn();
    }
}
