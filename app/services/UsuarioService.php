<?php

namespace app\services;

use app\models\Usuario;
use app\repositories\UsuarioRepository;

class UsuarioService
{
    private UsuarioRepository $repository;

    public function emailExiste(string $email, int $idIgnorado): bool { return $this->repository->emailExiste($email, $idIgnorado); }
    public function updatePerfil(Usuario $usuario, ?string $senha = null): bool { return $this->repository->updatePerfil($usuario, $senha); }

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
    }


    public function getUsuarioByEmail(string $email): ?Usuario
    {
        return $this->repository->getUsuarioByEmail($email);
    }


}
