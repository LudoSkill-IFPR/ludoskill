<?php

namespace app\services;

use app\models\Exercicio;
use app\repositories\ExercicioRepository;


class ExercicioService {
    private ExercicioRepository $exercicioRepository;

    public function __construct() {
        $this->exercicioRepository = new ExercicioRepository();
    }

    public function getExercicios(): array {
        return $this->exercicioRepository->getExercicios();
    }

    public function getExercicioById(int $id) {
        return $this->exercicioRepository->getExercicioById($id);
    }

    public function saveExercicio(Exercicio $exercicio) {
        return $this->exercicioRepository->saveExercicio($exercicio);
    }

    public function deleteExercicio(int $id) {
        return $this->exercicioRepository->deleteExercicio($id);
    }

    public function updateExercicio(Exercicio $exercicio) {
        return $this->exercicioRepository->updateExercicio($exercicio);
    }
}