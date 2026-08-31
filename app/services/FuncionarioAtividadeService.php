<?php

namespace app\services;

use app\repositories\FuncionarioAtividadeRepository;

class FuncionarioAtividadeService
{
    private FuncionarioAtividadeRepository $repository;

    public function __construct()
    {
        $this->repository = new FuncionarioAtividadeRepository();
    }

    public function getResultadosByUsuario(int $idUsuario): array
    {
        return $this->repository->getResultadosByUsuario($idUsuario);
    }

    public function registrarResultado(
        int $idUsuario,
        int $idAtividade,
        int $pontuacao,
        int $estrelas,
        int $bolotas,
        float $tempoExecucao
    ): array {
        return $this->repository->registrarResultado(
            $idUsuario,
            $idAtividade,
            $pontuacao,
            $estrelas,
            $bolotas,
            $tempoExecucao
        );
    }
}
