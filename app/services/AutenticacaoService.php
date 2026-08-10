<?php

namespace app\services;

use app\repositories\UsuarioRepository;
use app\models\Usuario;

class AutenticacaoService
{
    private UsuarioRepository $usuarioRepository;

    public function __construct()
    {
        $this->usuarioRepository = new UsuarioRepository();
    }

    public function logar(string $email, string $senha): ?Usuario
    {
        $usuario = $this->usuarioRepository->getUsuarioByEmail($email);

        if (!$usuario) {
            return null;
        }

        if (!password_verify($senha, $usuario->getSenha())) {
            return null;
        }

        $_SESSION['usuario_logado'] = $usuario;

        return $usuario;
    }

    public function logout(): void
    {
        session_destroy();
    }
}