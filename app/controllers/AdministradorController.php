<?php


namespace app\controllers;

use app\core\Controller;
use app\services\GestorService;
use app\services\ModuloService;
use app\services\ExercicioService;
use app\services\AtividadeService;
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
        $exercicioService = new ExercicioService();
        $atividadeService = new AtividadeService();
        $empresaService = new EmpresaService();
        $itemService = new ItemService();
        $solicitacaoService = new SolicitacaoAcessoService();

        $quantidadeGestores = $gestorService->countGestores();
        $quantidadeModulos = $moduloService->countModulos();
        $quantidadeAtividades = $atividadeService->countAtividades();
        $quantidadeExercicios = $exercicioService->countExercicios();
        $quantidadeEmpresas = $empresaService->countEmpresas();
        $quantidadeItens = $itemService->countItens();
        $quantidadeSolicitacoes = $solicitacaoService->countPendentes();
        $empresasComQuantidadeUsuarios = $empresaService->getEmpresasComQuantidadeUsuarios();

        $this->view('administrador/inicial', [
            'empresasComQuantidadeUsuarios' => $empresasComQuantidadeUsuarios,
            'quantidadeGestores' => $quantidadeGestores,
            'quantidadeModulos' => $quantidadeModulos,
            'quantidadeAtividades' => $quantidadeAtividades,
            'quantidadeExercicios' => $quantidadeExercicios,
            'quantidadeEmpresas' => $quantidadeEmpresas,
            'quantidadeItens' => $quantidadeItens,
            'quantidadeSolicitacoes' => $quantidadeSolicitacoes
        ]);
    }
}
