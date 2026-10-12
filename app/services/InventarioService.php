<?php

namespace app\services;

use app\repositories\InventarioRepository;

class InventarioService
{
    private InventarioRepository $repository;

    public function __construct()
    {
        $this->repository = new InventarioRepository();
    }

    public function getByUsuario(int $idUsuario): array
    {
        return $this->repository->getByUsuario($idUsuario);
    }
    public function comprar(int $idUsuario, int $idItem): void
    {
        $this->repository->comprar($idUsuario, $idItem);
    }
    public function equipar(int $idUsuario, int $idItem): string
    {
        return $this->repository->equipar($idUsuario, $idItem);
    }
}
