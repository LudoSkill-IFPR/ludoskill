<?php


namespace app\controllers;

use app\core\Controller;
use app\services\GestorService;
use app\services\ModuloService;
use app\services\EmpresaService;
use app\services\ItemService;
use app\services\SolicitacaoAcessoService;

class AdministradorController extends UsuarioController
{
    public function inicial()
    {
        $this->adminRequired();

        $gestorService = new GestorService();
        $moduloService = new ModuloService();
        $empresaService = new EmpresaService();
        $itemService = new ItemService();
        $solicitacaoService = new SolicitacaoAcessoService();

        $quantidadeGestores = $gestorService->countGestores();
        $quantidadeModulos = $moduloService->countModulos();
        $quantidadeEmpresas = $empresaService->countEmpresas();
        $quantidadeItens = $itemService->countItens();
        $quantidadeSolicitacoes = $solicitacaoService->countPendentes();
        $empresasComQuantidadeUsuarios = $empresaService->getEmpresasComQuantidadeUsuarios();

        $this->view('administrador/inicial', [
            'empresasComQuantidadeUsuarios' => $empresasComQuantidadeUsuarios,
            'quantidadeGestores' => $quantidadeGestores,
            'quantidadeModulos' => $quantidadeModulos,
            'quantidadeEmpresas' => $quantidadeEmpresas,
            'quantidadeItens' => $quantidadeItens,
            'quantidadeSolicitacoes' => $quantidadeSolicitacoes
        ]);
    }
}
