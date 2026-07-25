<?php

namespace app\services;

use app\models\Funcionario;
use app\repositories\FuncionarioRepository;

class FuncionarioService 
{
    private FuncionarioRepository $funcionarioRepository;

    public function __construct() {
        $this->funcionarioRepository = new FuncionarioRepository();
    }

    public function getFuncionarios(): array {
        return $this->funcionarioRepository->getFuncionarios();
    }

    public function getFuncionarioById(int $id) {
        return $this->funcionarioRepository->getFuncionarioById($id);
    }

    public function saveFuncionario(Funcionario $funcionario) {
        return $this->funcionarioRepository->saveFuncionario($funcionario);
    }

    public function deleteFuncionario(int $id) {
        return $this->funcionarioRepository->deleteFuncionario($id);
    }

    public function updateFuncionario(Funcionario $funcionario) {
        return $this->funcionarioRepository->updateFuncionario($funcionario);
    }
}