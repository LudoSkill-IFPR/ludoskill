<?php

/**
 * TODO Implementar corretamente. NAO FOI VALIDADO
 */

namespace app\models;

use app\models\Usuario;
class Administrador extends Usuario
{
    public function __construct(
        int $id,
        string $nomeCompleto,
        \DateTimeImmutable $dataNascimento,
        string $cpf,
        string $email,
        string $senha,
        string $numeroTelefone,
        string $estado = 'ATIVO'
    ) {
        parent::__construct($id, $nomeCompleto, $dataNascimento, $cpf, $email, $senha, $numeroTelefone, 'admin', $estado);
    }
}
