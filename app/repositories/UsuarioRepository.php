<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Usuario;
use PDO;

class UsuarioRepository{
    private PDO $connection;

    public function __construct(){
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getUsuarios(): array{
        $sql = "SELECT * FROM Usuarios";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUsuario(int $id): array{
        $stm = $this->connection->prepare("SELECT * FROM Usuarios WHERE id_usuario = :id");
        $stm->bindValue(':id', $id);

        $stm->execute();

        $usuario = $stm->fetch();

        return $usuario;
    }

    public function getUsuarioById(int $id): Usuario{
        $stm = $this->connection->prepare("SELECT * FROM Usuarios WHERE id_usuario = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $usuario = $stm->fetch();

        return Usuario::arrayParaObjeto($usuario);
    }

    public function getUsuarioByEmail(string $email): ?Usuario
    {
        $sql = "
            SELECT 
                u.*,
                CASE
                    WHEN a.id_administrador IS NOT NULL THEN 'admin'
                    WHEN g.id_gestor IS NOT NULL THEN 'gestor'
                    WHEN f.id_funcionario IS NOT NULL THEN 'funcionario'
                    ELSE NULL
                END AS perfil
            FROM Usuarios u
            LEFT JOIN Administradores a 
                ON a.id_usuario = u.id_usuario
            LEFT JOIN Gestores g 
                ON g.id_usuario = u.id_usuario
            LEFT JOIN Funcionarios f 
                ON f.id_usuario = u.id_usuario
            WHERE u.email = :email
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch();

        if (!$usuario) {
            return null;
        }

        return Usuario::arrayParaObjeto($usuario);
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        $sql = "UPDATE Usuarios
                SET nome_completo = :nomeCompleto,
                    data_nascimento = :dataNascimento,
                    cpf = :cpf,
                    email = :email,
                    senha_hash = :senha,
                    numero_telefone = :numeroTelefone
                WHERE id_usuario = :id";

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':nomeCompleto', $usuario->getNomeCompleto());
        $stmt->bindValue(':dataNascimento', $usuario->getDataNascimento());
        $stmt->bindValue(':cpf', $usuario->getCpf());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha', $usuario->getSenha());
        $stmt->bindValue(':numeroTelefone', $usuario->getNumeroTelefone());
        $stmt->bindValue(':id', $usuario->getId());

        return $stmt->execute();
    }

    public function saveUsuario(Usuario $usuario)
    {
        $sql = "INSERT INTO Usuarios 
                (nome_completo, data_nascimento, cpf, email, senha_hash, numero_telefone)
                VALUES
                (:nomeCompleto, :dataNascimento, :cpf, :email, :senha, :numeroTelefone)";

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':nomeCompleto', $usuario->getNomeCompleto());
        $stmt->bindValue(':dataNascimento', $usuario->getDataNascimento());
        $stmt->bindValue(':cpf', $usuario->getCpf());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha', password_hash($usuario->getSenha(), PASSWORD_DEFAULT));
        $stmt->bindValue(':numeroTelefone', $usuario->getNumeroTelefone());

        return $stmt->execute();
    }

    public function deleteUsuario(int $id): bool {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
