<?php

namespace app\helpers;

class Auth
{
    public static function autenticacao_required()
    {
        if (!isset($_SESSION['usuario_logado'])) {
            header('Location: ' . URL_BASE . '/login');
            exit();
        }
    }

    public static function adm_required()
    {
        if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado']->getPerfil() !== 'admin') {
            header('Location: ' . URL_BASE . '/login');
            exit();
        }
    }

    public static function gestor_required()
    {
        if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado']->getPerfil() !== 'gestor') {
            header('Location: ' . URL_BASE . '/login');
            exit();
        }
    }

    public static function funcionario_required()
    {
        if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado']->getPerfil() !== 'funcionario') {
            header('Location: ' . URL_BASE . '/login');
            exit();
        }
    }
}
