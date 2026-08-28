<?php

namespace app\services;

use app\models\Gestor;
use app\repositories\GestorRepository;

class GestorService {
    private GestorRepository $repository;

    public function __construct()
    {
        $this->repository = new GestorRepository;
    }

    public function getGestores(): array {
        return $this->repository->getGestores();
    }

    public function getGestorById(int $id) {
        return $this->repository->getGestorById($id);
    }

    public function saveGestor(Gestor $gestor) {
        return $this->repository->saveGestor($gestor);
    }

    public function deleteGestor(int $id) {
        return $this->repository->deleteGestor($id);
    }

    public function updateGestor(Gestor $gestor, int $idGestor, ?string $novaSenha = null) {
        return $this->repository->updateGestor($gestor, $idGestor, $novaSenha);
    }

    public function countGestores(): int {
        return $this->repository->countGestores();
    }

}
