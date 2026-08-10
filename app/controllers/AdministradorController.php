<?php


namespace app\controllers;

use app\core\Controller;
use app\models\Usuario;
use app\services\UsuarioService;

class AdministradorController extends UsuarioController
{
    public function inicial()
    {
        $this->adminRequired();

        $this->view('administrador/inicial');
    }
}
