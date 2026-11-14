<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Usuario;
use app\models\Administrador;
use app\models\Gestor;
use app\models\Funcionario;
use PDO;

class UsuarioRepository{
    private PDO $connection;

    public function __construct(){
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getUsuarioByEmail(string $email): ?Usuario
    {
        $sql = "
            SELECT 
                u.*, a.id_administrador, g.id_gestor, g.id_empresa AS gestor_empresa,
                f.id_funcionario, f.id_empresa AS funcionario_empresa,
                f.bolotas_totais, f.pontuacao_total, f.nivel,
                eg.cnpj AS gestor_empresa_cnpj, eg.nome AS gestor_empresa_nome,
                eg.email AS gestor_empresa_email, eg.plano AS gestor_empresa_plano,
                ef.cnpj AS funcionario_empresa_cnpj, ef.nome AS funcionario_empresa_nome,
                ef.email AS funcionario_empresa_email, ef.plano AS funcionario_empresa_plano,
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
            LEFT JOIN Empresas eg
                ON eg.id_empresa = g.id_empresa
            LEFT JOIN Funcionarios f 
                ON f.id_usuario = u.id_usuario
            LEFT JOIN Empresas ef
                ON ef.id_empresa = f.id_empresa
            WHERE u.email = :email
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch();

        if (!$usuario) {
            return null;
        }

        $empresaGestor = new \app\models\Empresa(
            (int) ($usuario['gestor_empresa'] ?? 0),
            (string) ($usuario['gestor_empresa_cnpj'] ?? ''),
            (string) ($usuario['gestor_empresa_nome'] ?? ''),
            (string) ($usuario['gestor_empresa_email'] ?? ''),
            (string) ($usuario['gestor_empresa_plano'] ?? 'BASICO')
        );
        $empresaFuncionario = new \app\models\Empresa(
            (int) ($usuario['funcionario_empresa'] ?? 0),
            (string) ($usuario['funcionario_empresa_cnpj'] ?? ''),
            (string) ($usuario['funcionario_empresa_nome'] ?? ''),
            (string) ($usuario['funcionario_empresa_email'] ?? ''),
            (string) ($usuario['funcionario_empresa_plano'] ?? 'BASICO')
        );

        return match ($usuario['perfil']) {
            'admin' => Administrador::arrayParaObjeto($usuario),
            'gestor' => Gestor::arrayParaObjeto(array_merge($usuario, ['id_empresa' => $usuario['gestor_empresa']]), $empresaGestor),
            'funcionario' => Funcionario::arrayParaObjeto(array_merge($usuario, ['id_empresa' => $usuario['funcionario_empresa']]), $empresaFuncionario),
            default => Usuario::arrayParaObjeto($usuario),
        };
    }

    public function emailExiste(string $email, int $idIgnorado): bool
    {
        $stm = $this->connection->prepare('SELECT 1 FROM Usuarios WHERE email = :email AND id_usuario <> :id LIMIT 1');
        $stm->execute(['email' => $email, 'id' => $idIgnorado]);
        return $stm->fetchColumn() !== false;
    }

    public function updatePerfil(Usuario $usuario, ?string $novaSenha = null): bool
    {
        $senhaSql = $novaSenha !== null && $novaSenha !== '' ? ', senha_hash = :senha' : '';
        $sql = "UPDATE Usuarios SET nome_completo = :nome, data_nascimento = :data,
                email = :email, numero_telefone = :telefone{$senhaSql}
                WHERE id_usuario = :id";
        $stm = $this->connection->prepare($sql);
        $dados = [
            'nome' => $usuario->getNomeCompleto(),
            'data' => $usuario->getDataNascimento()?->format('Y-m-d'),
            'email' => $usuario->getEmail(),
            'telefone' => $usuario->getNumeroTelefone(),
            'id' => $usuario->getId(),
        ];
        if ($senhaSql !== '') $dados['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
        return $stm->execute($dados);
    }

    public function updateSenha(int $idUsuario, string $senhaHash): bool
    {
        $stm = $this->connection->prepare(
            'UPDATE Usuarios SET senha_hash = :senha WHERE id_usuario = :id'
        );
        return $stm->execute(['senha' => $senhaHash, 'id' => $idUsuario]);
    }

}
