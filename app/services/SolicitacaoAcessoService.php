<?php

namespace app\services;

use app\models\SolicitacaoAcesso;
use app\repositories\SolicitacaoAcessoRepository;

class SolicitacaoAcessoService
{
    private SolicitacaoAcessoRepository $repository;

    public function __construct()
    {
        $this->repository = new SolicitacaoAcessoRepository();
    }

    public function getTodas(): array { return $this->repository->getTodas(); }
    public function countPendentes(): int { return $this->repository->countPendentes(); }
    public function existeEmAberto(string $email, string $cpf): bool { return $this->repository->existeEmAberto($email, $cpf); }
    public function salvar(SolicitacaoAcesso $solicitacao): bool { return $this->repository->salvar($solicitacao); }
    public function aprovar(int $id, string $senha, string $plano): array { return $this->repository->aprovar($id, $senha, $plano); }
    public function marcarEmailEnviado(int $id): bool { return $this->repository->marcarEmailEnviado($id); }
    public function recusar(int $id): bool { return $this->repository->recusar($id); }
}
