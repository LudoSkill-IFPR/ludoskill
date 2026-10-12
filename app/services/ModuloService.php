<?php

namespace app\services;

use app\models\Modulo;
use app\repositories\ModuloRepository;

class ModuloService {
    private ModuloRepository $moduloRepository;

    public function __construct() {
        $this->moduloRepository = new ModuloRepository();
    }

    public function getModulos(): array {
        return $this->moduloRepository->getModulos();
    }

    public function getModulosAtivos(): array {
        return $this->moduloRepository->getModulosAtivos();
    }

    public function getModuloById(int $id) {
        return $this->moduloRepository->getModuloById($id);
    }

    public function nomeExiste(string $nome, ?int $idIgnorado = null): bool {
        return $this->moduloRepository->nomeExiste($nome, $idIgnorado);
    }

    public function saveModulo(Modulo $modulo) {
        return $this->moduloRepository->saveModulo($modulo);
    }

    public function deleteModulo(int $id) {
        return $this->moduloRepository->deleteModulo($id);
    }

    public function updateModulo(Modulo $modulo) {
        return $this->moduloRepository->updateModulo($modulo);
    }

    public function countModulos(): int {
        return $this->moduloRepository->countModulos();
    }
}
