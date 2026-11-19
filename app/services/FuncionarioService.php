<?php

namespace app\services;

use app\models\Funcionario;
use app\repositories\FuncionarioRepository;

class FuncionarioService
{
    private FuncionarioRepository $funcionarioRepository;

    public function __construct()
    {
        $this->funcionarioRepository = new FuncionarioRepository();
    }

    public function getFuncionarioByUsuario(int $id_usuario)
    {
        return $this->funcionarioRepository->getFuncionarioByUsuario($id_usuario);
    }

    public function getFuncionariosByEmpresa(int $idEmpresa): array
    {
        return $this->funcionarioRepository->getFuncionariosByEmpresa($idEmpresa);
    }

    public function getEmpresaIdByGestorUsuario(int $idUsuario): ?int
    {
        return $this->funcionarioRepository->getEmpresaIdByGestorUsuario($idUsuario);
    }

    public function cpfExiste(string $cpf): bool
    {
        return $this->funcionarioRepository->cpfExiste($cpf);
    }

    public function emailExiste(string $email): bool
    {
        return $this->funcionarioRepository->emailExiste($email);
    }

    public function saveFuncionario(Funcionario $funcionario)
    {
        return $this->funcionarioRepository->saveFuncionario($funcionario);
    }

    public function desativarFuncionario(int $id, int $idEmpresa)
    {
        return $this->funcionarioRepository->desativarFuncionario($id, $idEmpresa);
    }

    public function ativarFuncionario(int $id, int $idEmpresa)
    {
        return $this->funcionarioRepository->ativarFuncionario($id, $idEmpresa);
    }

    public function countFuncionarios(): int
    {
        return $this->funcionarioRepository->countFuncionarios();
    }

    public function countFuncionariosByEmpresa(int $idEmpresa): int
    {
        return $this->funcionarioRepository->countFuncionariosByEmpresa($idEmpresa);
    }
}
