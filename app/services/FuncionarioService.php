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

    public function getFuncionariosByEmpresa(int $idEmpresa): array {
        return $this->funcionarioRepository->getFuncionariosByEmpresa($idEmpresa);
    }

    public function getFuncionarioByIdAndEmpresa(int $id, int $idEmpresa) {
        return $this->funcionarioRepository->getFuncionarioByIdAndEmpresa($id, $idEmpresa);
    }

    public function getEmpresaIdByGestorUsuario(int $idUsuario): ?int {
        return $this->funcionarioRepository->getEmpresaIdByGestorUsuario($idUsuario);
    }

    public function cpfExiste(string $cpf, ?int $idFuncionarioIgnorado = null): bool {
        return $this->funcionarioRepository->cpfExiste($cpf, $idFuncionarioIgnorado);
    }

    public function emailExiste(string $email, ?int $idFuncionarioIgnorado = null): bool {
        return $this->funcionarioRepository->emailExiste($email, $idFuncionarioIgnorado);
    }

    public function saveFuncionario(Funcionario $funcionario) {
        return $this->funcionarioRepository->saveFuncionario($funcionario);
    }

    public function deleteFuncionario(int $id, int $idEmpresa) {
        return $this->funcionarioRepository->deleteFuncionario($id, $idEmpresa);
    }

    public function updateFuncionario(Funcionario $funcionario, int $idEmpresa, ?string $novaSenha = null) {
        return $this->funcionarioRepository->updateFuncionario($funcionario, $idEmpresa, $novaSenha);
    }

    public function countFuncionarios(): int {
        return $this->funcionarioRepository->countFuncionarios();
    }

    public function countFuncionariosByEmpresa(int $idEmpresa): int {
        return $this->funcionarioRepository->countFuncionariosByEmpresa($idEmpresa);
    }
}
