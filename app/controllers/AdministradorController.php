<?php


namespace app\controllers;

use app\core\Controller;
use app\models\Usuario;
use app\services\UsuarioService;
use app\services\GestorService;
use app\services\ModuloService;
use app\services\EmpresaService;
use app\services\ItemService;

class AdministradorController extends UsuarioController
{
    public function inicial()
    {
        $this->adminRequired();

        // Obter quantidades para o dashboard
        $gestorService = new GestorService();
        $moduloService = new ModuloService();
        $empresaService = new EmpresaService();
        $itemService = new ItemService();

        $quantidadeGestores = $gestorService->countGestores();
        $quantidadeModulos = $moduloService->countModulos();
        $quantidadeEmpresas = $empresaService->countEmpresas();
        $quantidadeItens = $itemService->countItens();

        $this->view('administrador/inicial', [
            'quantidadeGestores' => $quantidadeGestores,
            'quantidadeModulos' => $quantidadeModulos,
            'quantidadeEmpresas' => $quantidadeEmpresas,
            'quantidadeItens' => $quantidadeItens
        ]);
    }
}
