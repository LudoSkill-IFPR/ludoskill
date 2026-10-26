<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\SolicitacaoAcesso;
use PDO;

class SolicitacaoAcessoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getTodas(): array
    {
        $stm = $this->connection->query(
            "SELECT s.*, s.email_enviado + 0 AS email_enviado FROM Solicitacoes_acesso s
             ORDER BY FIELD(estado, 'PENDENTE', 'APROVADA', 'RECUSADA'), data_solicitacao DESC"
        );
        return array_map([SolicitacaoAcesso::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function countPendentes(): int
    {
        return (int) $this->connection
            ->query("SELECT COUNT(*) FROM Solicitacoes_acesso WHERE estado = 'PENDENTE'")
            ->fetchColumn();
    }

    public function existeEmAberto(string $email, string $cpf): bool
    {
        $stm = $this->connection->prepare(
            "SELECT 1 FROM Solicitacoes_acesso
             WHERE estado IN ('PENDENTE', 'APROVADA') AND (email = :emailSolicitacao OR CPF = :cpfSolicitacao)
             UNION ALL
             SELECT 1 FROM Usuarios WHERE email = :emailUsuario OR CPF = :cpfUsuario
             LIMIT 1"
        );
        $stm->execute([
            'emailSolicitacao' => $email,
            'cpfSolicitacao' => $cpf,
            'emailUsuario' => $email,
            'cpfUsuario' => $cpf,
        ]);
        return $stm->fetchColumn() !== false;
    }

    public function salvar(SolicitacaoAcesso $solicitacao): bool
    {
        $stm = $this->connection->prepare(
            'INSERT INTO Solicitacoes_acesso
                (nome_completo, data_nascimento, CPF, email, numero_telefone,
                 empresa_nome, empresa_cnpj, empresa_email, mensagem)
             VALUES
                (:nome, :data, :cpf, :email, :telefone,
                 :empresaNome, :empresaCnpj, :empresaEmail, :mensagem)'
        );
        return $stm->execute([
            'nome' => $solicitacao->getNomeCompleto(),
            'data' => $solicitacao->getDataNascimento()?->format('Y-m-d'),
            'cpf' => $solicitacao->getCpf(),
            'email' => $solicitacao->getEmail(),
            'telefone' => $solicitacao->getNumeroTelefone(),
            'empresaNome' => $solicitacao->getEmpresaNome(),
            'empresaCnpj' => $solicitacao->getEmpresaCnpj(),
            'empresaEmail' => $solicitacao->getEmpresaEmail(),
            'mensagem' => $solicitacao->getMensagem() ?: null,
        ]);
    }

    /**
     * Cria a empresa (se necessário), o usuário e o gestor em uma única transação.
     * Retorna os dados necessários para o controller enviar o e-mail de acesso.
     */
    public function aprovar(int $id, string $senha, string $plano): array
    {
        $this->connection->beginTransaction();
        try {
            $stm = $this->connection->prepare(
                "SELECT * FROM Solicitacoes_acesso WHERE id_solicitacao = :id FOR UPDATE"
            );
            $stm->execute(['id' => $id]);
            $dados = $stm->fetch();

            if (!$dados || $dados['estado'] !== 'PENDENTE') {
                throw new \DomainException('A solicitação não existe ou já foi analisada.');
            }

            $stm = $this->connection->prepare(
                'SELECT 1 FROM Usuarios WHERE email = :email OR CPF = :cpf LIMIT 1'
            );
            $stm->execute(['email' => $dados['email'], 'cpf' => $dados['CPF']]);
            if ($stm->fetchColumn() !== false) {
                throw new \DomainException('Já existe um usuário com o e-mail ou CPF desta solicitação.');
            }

            $stm = $this->connection->prepare('SELECT id_empresa FROM Empresas WHERE cnpj = :cnpj');
            $stm->execute(['cnpj' => $dados['empresa_cnpj']]);
            $idEmpresa = $stm->fetchColumn();

            if (!$idEmpresa) {
                $stm = $this->connection->prepare(
                    'INSERT INTO Empresas (cnpj, nome, email, plano)
                     VALUES (:cnpj, :nome, :email, :plano)'
                );
                $stm->execute([
                    'cnpj' => $dados['empresa_cnpj'],
                    'nome' => $dados['empresa_nome'],
                    'email' => $dados['empresa_email'],
                    'plano' => $plano,
                ]);
                $idEmpresa = (int) $this->connection->lastInsertId();
            }

            $stm = $this->connection->prepare(
                'INSERT INTO Usuarios
                    (nome_completo, data_nascimento, CPF, email, senha_hash, numero_telefone)
                 VALUES (:nome, :data, :cpf, :email, :senha, :telefone)'
            );
            $stm->execute([
                'nome' => $dados['nome_completo'],
                'data' => $dados['data_nascimento'],
                'cpf' => $dados['CPF'],
                'email' => $dados['email'],
                'senha' => password_hash($senha, PASSWORD_DEFAULT),
                'telefone' => $dados['numero_telefone'],
            ]);
            $idUsuario = (int) $this->connection->lastInsertId();

            $stm = $this->connection->prepare(
                'INSERT INTO Gestores (id_usuario, id_empresa) VALUES (:usuario, :empresa)'
            );
            $stm->execute(['usuario' => $idUsuario, 'empresa' => (int) $idEmpresa]);
            $idGestor = (int) $this->connection->lastInsertId();

            $stm = $this->connection->prepare(
                "UPDATE Solicitacoes_acesso
                 SET estado = 'APROVADA', id_gestor = :gestor, data_decisao = CURRENT_TIMESTAMP
                 WHERE id_solicitacao = :id"
            );
            $stm->execute(['gestor' => $idGestor, 'id' => $id]);
            $this->connection->commit();

            return [
                'nome' => $dados['nome_completo'],
                'email' => $dados['email'],
                'senha' => $senha,
            ];
        } catch (\Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $e;
        }
    }

    public function marcarEmailEnviado(int $id): bool
    {
        $stm = $this->connection->prepare(
            'UPDATE Solicitacoes_acesso SET email_enviado = 1 WHERE id_solicitacao = :id'
        );
        return $stm->execute(['id' => $id]);
    }

    public function recusar(int $id): bool
    {
        $stm = $this->connection->prepare(
            "UPDATE Solicitacoes_acesso
             SET estado = 'RECUSADA', data_decisao = CURRENT_TIMESTAMP
             WHERE id_solicitacao = :id AND estado = 'PENDENTE'"
        );
        $stm->execute(['id' => $id]);
        return $stm->rowCount() === 1;
    }
}
