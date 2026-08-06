<?php

/**
 * CORRIGIDO
 */

namespace app\services;

use app\models\Empresa;
use app\repositories\EmpresaRepository;


class EmpresaService {
    private EmpresaRepository $empresaRepository;

    public function __construct() {
        $this->empresaRepository = new EmpresaRepository();
    }

    public function getEmpresas(): array {
        return $this->empresaRepository->getEmpresas();
    }

    public function getEmpresaById(int $id) {
        return $this->empresaRepository->getEmpresaById($id);
    }

    public function saveEmpresa(Empresa $empresa) {
        return $this->empresaRepository->saveEmpresa($empresa);
    }

    public function deleteEmpresa(int $id) {
        return $this->empresaRepository->deleteEmpresa($id);
    }

    public function updateEmpresa(Empresa $empresa) {
        return $this->empresaRepository->updateEmpresa($empresa);
    }
}