<?php

namespace app\services;

use app\models\Gestor;
use app\repositories\GestorRepository;

class GestorService
{
    private GestorRepository $repository;

    public function __construct()
    {
        $this->repository = new GestorRepository;
    }

    public function getGestores(): array
    {
        return $this->repository->getGestores();
    }

    public function getGestorByUsuario(int $idUsuario): ?Gestor
    {
        return $this->repository->getGestorByUsuario($idUsuario);
    }

    public function saveGestor(Gestor $gestor)
    {
        return $this->repository->saveGestor($gestor);
    }

    public function cpfExiste(string $cpf): bool { return $this->repository->cpfExiste($cpf); }
    public function emailExiste(string $email): bool { return $this->repository->emailExiste($email); }

    public function desativarGestor(int $id)
    {
        return $this->repository->desativarGestor($id);
    }

    public function ativarGestor(int $id)
    {
        return $this->repository->ativarGestor($id);
    }

    public function countGestores(): int
    {
        return $this->repository->countGestores();
    }

    public function countGestoresByEmpresa(int $idEmpresa): int
    {
        return $this->repository->countGestoresByEmpresa($idEmpresa);
    }
}
