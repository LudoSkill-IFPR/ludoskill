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

    public function getGestorById(int $id)
    {
        return $this->repository->getGestorById($id);
    }

    public function getGestorByUsuario(int $idUsuario): ?Gestor
    {
        return $this->repository->getGestorByUsuario($idUsuario);
    }

    public function saveGestor(Gestor $gestor)
    {
        return $this->repository->saveGestor($gestor);
    }

    public function cpfExiste(string $cpf, ?int $ignorar = null): bool { return $this->repository->cpfExiste($cpf, $ignorar); }
    public function emailExiste(string $email, ?int $ignorar = null): bool { return $this->repository->emailExiste($email, $ignorar); }

    public function desativarGestor(int $id)
    {
        return $this->repository->desativarGestor($id);
    }

    public function ativarGestor(int $id)
    {
        return $this->repository->ativarGestor($id);
    }

    public function updateGestor(Gestor $gestor, int $idGestor, ?string $novaSenha = null)
    {
        return $this->repository->updateGestor($gestor, $idGestor, $novaSenha);
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
