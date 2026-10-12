<?php

namespace app\database;

use PDO;

class DatabaseInitializer
{

    public function init(PDO $connection)
    {
        $connection->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $connection->exec("USE " . DB_NAME);
        $scriptPath = __DIR__ . '/banco.sql';
        
        if (file_exists($scriptPath)) {
            
            $sql = file_get_contents($scriptPath);
            try {
                $connection->exec($sql);
                $this->criarAdministradorInicial($connection);
            } catch (\Exception $e) {
                error_log("Erro ao inicializar banco de dados: " . $e->getMessage());
                throw $e;
            }
        }
    }

    private function criarAdministradorInicial(PDO $connection): void
    {
        $email = getenv('LUDOSKILL_ADMIN_EMAIL');
        $senha = getenv('LUDOSKILL_ADMIN_PASSWORD');
        $cpf = preg_replace('/\D/', '', getenv('LUDOSKILL_ADMIN_CPF') ?: '');
        if (!$email || !$senha || strlen($cpf) !== 11) return;

        $connection->beginTransaction();
        try {
            $stm = $connection->prepare('SELECT id_usuario FROM Usuarios WHERE email = :email');
            $stm->execute(['email' => $email]);
            $idUsuario = $stm->fetchColumn();
            if (!$idUsuario) {
                $stm = $connection->prepare("INSERT INTO Usuarios
                    (nome_completo, data_nascimento, CPF, email, senha_hash, numero_telefone)
                    VALUES (:nome, :data, :cpf, :email, :senha, :telefone)");
                $stm->execute([
                    'nome' => getenv('LUDOSKILL_ADMIN_NAME') ?: 'Administrador',
                    'data' => getenv('LUDOSKILL_ADMIN_BIRTHDATE') ?: '2000-01-01',
                    'cpf' => $cpf,
                    'email' => $email,
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                    'telefone' => getenv('LUDOSKILL_ADMIN_PHONE') ?: null,
                ]);
                $idUsuario = (int) $connection->lastInsertId();
            }
            $stm = $connection->prepare('INSERT IGNORE INTO Administradores (id_usuario) VALUES (:usuario)');
            $stm->execute(['usuario' => $idUsuario]);
            $connection->commit();
        } catch (\Throwable $exception) {
            if ($connection->inTransaction()) $connection->rollBack();
            throw $exception;
        }
    }
}
