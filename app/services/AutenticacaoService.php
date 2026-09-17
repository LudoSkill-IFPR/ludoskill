<?php

namespace app\services;

use app\repositories\UsuarioRepository;
use app\models\Usuario;

class AutenticacaoService
{
    private UsuarioRepository $usuarioRepository;
    private ?string $ultimoErro = null;

    public function __construct()
    {
        $this->usuarioRepository = new UsuarioRepository();
    }

    public function getUltimoErro(): ?string
    {
        return $this->ultimoErro;
    }

    public function logar(string $email, string $senha): ?Usuario
    {
        $this->ultimoErro = null;
        $usuario = $this->usuarioRepository->getUsuarioByEmail($email);

        if (!$usuario) {
            $this->ultimoErro = 'E-mail ou senha incorretos.';
            return null;
        }

        if ($usuario->getEstado() !== 'ATIVO') {
            $this->ultimoErro = 'Sua conta está desativada. Entre em contato com o suporte ou gestor.';
            return null;
        }

        if (!password_verify($senha, $usuario->getSenha())) {
            $this->ultimoErro = 'E-mail ou senha incorretos.';
            return null;
        }

        $_SESSION['usuario_logado'] = $usuario;

        return $usuario;
    }

    public function logout(): void
    {
        unset($_SESSION['usuario_logado']);
        session_destroy();
    }
}
