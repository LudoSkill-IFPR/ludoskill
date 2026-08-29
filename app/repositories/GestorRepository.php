<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Gestor;
use PDO;

class GestorRepository
{
    private PDO $connection;

    public function __construct(){
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getGestores(): array{
        $stm = $this->connection->prepare(
            "SELECT g.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                    u.email, u.numero_telefone, e.nome AS nome_empresa
             FROM Gestores g
             INNER JOIN Usuarios u ON u.id_usuario = g.id_usuario
             INNER JOIN Empresas e ON e.id_empresa = g.id_empresa
             ORDER BY u.nome_completo"
        );
        $stm->execute();
        $gestores = $stm->fetchAll();
        return $gestores;
    }

    public function getGestorById(int $id){
        $stm = $this->connection->prepare(
            "SELECT g.*, u.nome_completo, u.data_nascimento, u.CPF AS cpf,
                    u.email, u.senha_hash, u.numero_telefone
             FROM Gestores g INNER JOIN Usuarios u ON u.id_usuario = g.id_usuario
             WHERE g.id_gestor = :id"
        );
        $stm->bindValue('id', $id);

        $stm->execute();

        $gestor = $stm->fetch();

        return $gestor;
    }

    public function saveGestor(Gestor $gestor): bool {
        try {
            $this->connection->beginTransaction();
            $stm = $this->connection->prepare("INSERT INTO Usuarios (nome_completo, data_nascimento, CPF, email, senha_hash, numero_telefone) VALUES (:nome, :data, :cpf, :email, :senha, :telefone)");
            $stm->execute([
                'nome' => $gestor->getNomeCompleto(), 'data' => $gestor->getDataNascimento()->format('Y-m-d'),
                'cpf' => $gestor->getCpf(), 'email' => $gestor->getEmail(),
                'senha' => password_hash($gestor->getSenha(), PASSWORD_DEFAULT), 'telefone' => $gestor->getNumeroTelefone()
            ]);
            $stm = $this->connection->prepare("INSERT INTO Gestores (id_usuario, id_empresa) VALUES (:usuario, :empresa)");
            $stm->execute(['usuario' => (int) $this->connection->lastInsertId(), 'empresa' => $gestor->getEmpresa()->getId()]);
            return $this->connection->commit();
        } catch (\Throwable $e) {
            if ($this->connection->inTransaction()) $this->connection->rollBack();
            throw $e;
        }
    }

    public function deleteGestor(int $id){
        $stm = $this->connection->prepare("DELETE u FROM Usuarios u INNER JOIN Gestores g ON g.id_usuario = u.id_usuario WHERE g.id_gestor = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateGestor(Gestor $gestor, int $idGestor, ?string $novaSenha = null){
        $atual = $this->getGestorById($idGestor);
        if (!$atual) return false;
        $senhaSql = $novaSenha !== null && $novaSenha !== '' ? ', senha_hash = :senha' : '';
        $sql = "UPDATE Usuarios SET nome_completo = :nomeCompleto, data_nascimento = :dataNascimento, CPF = :cpf, email = :email, numero_telefone = :numeroTelefone{$senhaSql} WHERE id_usuario = :idUsuario";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nomeCompleto', $gestor->getNomeCompleto());
        $stm->bindValue('dataNascimento', $gestor->getDataNascimento()->format('Y-m-d'));
        $stm->bindValue('cpf', $gestor->getCpf());
        $stm->bindValue('email', $gestor->getEmail());
        $stm->bindValue('numeroTelefone', $gestor->getNumeroTelefone());
        $stm->bindValue('idUsuario', $atual['id_usuario']);
        if ($novaSenha !== null && $novaSenha !== '') $stm->bindValue('senha', password_hash($novaSenha, PASSWORD_DEFAULT));
        $stm->execute();
        $stm = $this->connection->prepare("UPDATE Gestores SET id_empresa = :empresa WHERE id_gestor = :id");
        return $stm->execute(['empresa' => $gestor->getEmpresa()->getId(), 'id' => $idGestor]);
    }

    public function countGestores(): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Gestores");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }

    public function countGestoresByEmpresa(int $idEmpresa): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Gestores WHERE id_empresa = :idEmpresa");
        $stm->bindValue('idEmpresa', $idEmpresa);
        $stm->execute();
        return (int) $stm->fetchColumn();
    }
    
}
