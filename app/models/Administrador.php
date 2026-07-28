<?php

/**
 * TODO Implementar
 */

namespace app\models;

use app\models\Usuario;
use app\models\Gestor;
use app\models\Modulo;
use app\repositories\GestorRepository;
use app\repositories\ModuloRepository;

class Administrador extends Usuario
{
    private GestorRepository $gestorRepository;
    private ModuloRepository $moduloRepository;

    public function __construct()
    {
        $this->gestorRepository = new GestorRepository();
        $this->moduloRepository = new ModuloRepository();
    }

    public function cadastrarGestor(Gestor $gestor)
    {
        $this->gestorRepository->saveGestor($gestor);
    }

    public function editarGestor(Gestor $gestor)
    {
        $this->gestorRepository->updateGestor($gestor);
    }

    public function desativarGestor(Gestor $gestor)
    {
        //conversar sobre depois
    }

    public function cadastrarModulo(Modulo $modulo)
    {
        $this->moduloRepository->saveModulo($modulo);
    }

    public function editarModulo(Modulo $modulo)
    {
        $this->moduloRepository->updateModulo($modulo);
    }

    public function excluirModulo(Modulo $modulo)
    {
        $this->moduloRepository->deleteModulo($modulo->getId());
    }

    //implementar fase e atividade quando ficar pronto os CRUDs

    public function cadastrarFase($nome, array $dados)
    {
        
    }

    public function editarFase($id, array $dados)
    {

    }

    public function excluirFase($id)
    {

    }

    public function cadastrarAtividade($nome, array $dados)
    {

    }

    public function editarAtividade($id, array $dados)
    {

    }

    public function excluirAtividade($id)
    {

    }

    public function emitirRelatorio(): string
    {
        return "";
    }
}