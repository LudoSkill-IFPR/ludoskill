<?php

namespace app\core;

class Controller
{

    public function view(string $view, ?array $data = null)
    {

        if ($data) {
            extract($data);
        }

        $path = __DIR__ . "/../views/$view.php";

        if (file_exists($path)) {
            ob_start();
            require $path;
            $conteudo = (string) ob_get_clean();
            $token = htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8');
            $conteudo = preg_replace_callback(
                '/<form\b([^>]*)>/i',
                static function (array $matches) use ($token): string {
                    if (!preg_match('/\bmethod\s*=\s*["\']?post["\']?/i', $matches[1])) return $matches[0];
                    return $matches[0] . '<input type="hidden" name="_csrf" value="' . $token . '">';
                },
                $conteudo
            );
            echo $conteudo;
        } else {
            print 'A view solicitada não foi encontrada: ' . $view;
        }
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf_token'];
    }

    public static function csrfValido(?string $token): bool
    {
        return is_string($token) && hash_equals(self::csrfToken(), $token);
    }

    public function redirect(string $url)
    {
        header('location: ' . $url);
        exit();
    }

    protected function autenticacaoRequired(): bool
    {
        if (!isset($_SESSION['usuario_logado']) || (method_exists($_SESSION['usuario_logado'], 'isAtivo') && !$_SESSION['usuario_logado']->isAtivo())) {
            unset($_SESSION['usuario_logado']);
            $this->redirect(URL_BASE . '/login');
        }

        return true;
    }

    protected function adminRequired(): bool
    {
        $this->autenticacaoRequired();

        if ($_SESSION['usuario_logado']->getPerfil() !== 'admin') {
            $this->redirect(URL_BASE . '/login');
        }

        return true;
    }

    protected function gestorRequired(): bool
    {
        $this->autenticacaoRequired();

        if ($_SESSION['usuario_logado']->getPerfil() !== 'gestor') {
            $this->redirect(URL_BASE . '/login');
        }

        return true;
    }

    protected function funcionarioRequired(): bool
    {
        $this->autenticacaoRequired();

        if ($_SESSION['usuario_logado']->getPerfil() !== 'funcionario') {
            $this->redirect(URL_BASE . '/login');
        }

        return true;
    }
}
