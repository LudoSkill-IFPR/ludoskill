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

    public function getDesempenhoByEmpresa(int $idEmpresa): array
    {
        return $this->repository->getDesempenhoByEmpresa($idEmpresa);
    }

    public function getTaxaConclusaoByEmpresa(int $idEmpresa): float
    {
        return $this->repository->getTaxaConclusaoByEmpresa($idEmpresa);
    }

    public function podeAcessarAtividade(int $idUsuario, int $idAtividade): bool
    {
        return $this->repository->podeAcessarAtividade($idUsuario, $idAtividade);
    }

    public function iniciarAtividade(int $idUsuario, int $idAtividade): void
    {
        $this->repository->iniciarAtividade($idUsuario, $idAtividade);
    }

    public function concluirAtividadeTexto(int $idUsuario, int $idAtividade, float $tempoExecucao): void
    {
        $this->repository->concluirAtividadeTexto($idUsuario, $idAtividade, $tempoExecucao);
    }

    public function getResumoByUsuario(int $idUsuario): array { return $this->repository->getResumoByUsuario($idUsuario); }
    public function getProximaAtividade(int $idUsuario): ?\app\models\Atividade { return $this->repository->getProximaAtividade($idUsuario); }

    public function registrarResultado(
        int $idUsuario,
        int $idAtividade,
        int $pontuacao,
        int $estrelas,
        int $bolotas,
        float $tempoExecucao
    ): \app\models\FuncionarioAtividade {
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
