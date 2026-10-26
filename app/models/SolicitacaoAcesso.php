<?php

namespace app\models;

class SolicitacaoAcesso
{
    public function __construct(
        private int $id = 0,
        private string $nomeCompleto = '',
        private ?\DateTimeImmutable $dataNascimento = null,
        private string $cpf = '',
        private string $email = '',
        private string $numeroTelefone = '',
        private string $empresaNome = '',
        private string $empresaCnpj = '',
        private string $empresaEmail = '',
        private string $mensagem = '',
        private string $estado = 'PENDENTE',
        private ?int $idGestor = null,
        private ?\DateTimeImmutable $dataSolicitacao = null,
        private ?\DateTimeImmutable $dataDecisao = null,
        private bool $emailEnviado = false
    ) {
    }

    public static function arrayParaObjeto(array $dados): self
    {
        return new self(
            (int) ($dados['id_solicitacao'] ?? 0),
            (string) ($dados['nome_completo'] ?? ''),
            !empty($dados['data_nascimento']) ? new \DateTimeImmutable($dados['data_nascimento']) : null,
            (string) ($dados['CPF'] ?? $dados['cpf'] ?? ''),
            (string) ($dados['email'] ?? ''),
            (string) ($dados['numero_telefone'] ?? ''),
            (string) ($dados['empresa_nome'] ?? ''),
            (string) ($dados['empresa_cnpj'] ?? ''),
            (string) ($dados['empresa_email'] ?? ''),
            (string) ($dados['mensagem'] ?? ''),
            (string) ($dados['estado'] ?? 'PENDENTE'),
            isset($dados['id_gestor']) ? (int) $dados['id_gestor'] : null,
            !empty($dados['data_solicitacao']) ? new \DateTimeImmutable($dados['data_solicitacao']) : null,
            !empty($dados['data_decisao']) ? new \DateTimeImmutable($dados['data_decisao']) : null,
            (int) ($dados['email_enviado'] ?? 0) === 1
        );
    }

    public function getId(): int { return $this->id; }
    public function getNomeCompleto(): string { return $this->nomeCompleto; }
    public function getDataNascimento(): ?\DateTimeImmutable { return $this->dataNascimento; }
    public function getCpf(): string { return $this->cpf; }
    public function getEmail(): string { return $this->email; }
    public function getNumeroTelefone(): string { return $this->numeroTelefone; }
    public function getEmpresaNome(): string { return $this->empresaNome; }
    public function getEmpresaCnpj(): string { return $this->empresaCnpj; }
    public function getEmpresaEmail(): string { return $this->empresaEmail; }
    public function getMensagem(): string { return $this->mensagem; }
    public function getEstado(): string { return $this->estado; }
    public function getIdGestor(): ?int { return $this->idGestor; }
    public function getDataSolicitacao(): ?\DateTimeImmutable { return $this->dataSolicitacao; }
    public function getDataDecisao(): ?\DateTimeImmutable { return $this->dataDecisao; }
    public function isEmailEnviado(): bool { return $this->emailEnviado; }
}
