<?php

namespace app\services;

use app\models\Atividade;
use app\repositories\AtividadeRepository;

class AtividadeService {
    private AtividadeRepository $atividadeRepository;

    public function __construct() {
        $this->atividadeRepository = new AtividadeRepository();
    }

    public function getAtividades(): array {
        return $this->atividadeRepository->getAtividades();
    }

    public function getAtividadeById(int $id) {
        return $this->atividadeRepository->getAtividadeById($id);
    }

    public function getAtividadesByModulo(int $idModulo): array {
        return $this->atividadeRepository->getAtividadesByModulo($idModulo);
    }

    public function saveAtividade(Atividade $atividade) {
        return $this->atividadeRepository->saveAtividade($atividade);
    }

    public function deleteAtividade(int $id) {
        return $this->atividadeRepository->deleteAtividade($id);
    }

    public function updateAtividade(Atividade $atividade) {
        return $this->atividadeRepository->updateAtividade($atividade);
    }
}
