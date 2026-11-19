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

    public function cpfExiste(string $cpf): bool
    {
        $sql = 'SELECT 1 FROM Usuarios WHERE CPF = :valor LIMIT 1';
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('valor', $cpf);
        $stm->execute();
        return $stm->fetchColumn() !== false;
    }

    public function emailExiste(string $email): bool
    {
        $sql = 'SELECT 1 FROM Usuarios WHERE email = :valor LIMIT 1';
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('valor', $email);
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
