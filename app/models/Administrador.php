<?php

namespace app\models;

use app\models\Usuario;
use DateTimeImmutable;

class Administrador extends Usuario
{
    private int $idAdministrador;

    public function __construct(
        int $id = 0,
        string $nomeCompleto = '',
        ?DateTimeImmutable $dataNascimento = null,
        string $cpf = '',
        string $email = '',
        string $senha = '',
        ?string $numeroTelefone = null,
        string $estado = 'ATIVO', // Valor padrão para o estado
        int $idAdministrador = 0
    ) {
        parent::__construct($id, $nomeCompleto, $dataNascimento, $cpf, $email, $senha, $numeroTelefone, 'admin', $estado);
        $this->idAdministrador = $idAdministrador;
    }

    /**
     * O array deve vir de um JOIN entre Usuarios e Administradores.
     */
    public static function arrayParaObjeto(array $administrador): self
    {
        $u = self::extrairDadosUsuario($administrador);

        return new self(
            $u['id'],
            $u['nomeCompleto'],
            $u['dataNascimento'],
            $u['cpf'],
            $u['email'],
            $u['senha'],
            $u['numeroTelefone'],
            $u['estado'],
            (int) ($administrador['id_administrador'] ?? 0)
        );
    }

    public function getIdAdministrador(): int
    {
        return $this->idAdministrador;
    }

    public function setIdAdministrador(int $idAdministrador): self
    {
        $this->idAdministrador = $idAdministrador;

        return $this;
    }
}