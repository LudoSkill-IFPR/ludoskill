<?php

namespace app\models;

class FuncionarioAtividade
{
    public function __construct(
        private int $id = 0,
        private int $idFuncionario = 0,
        private int $idAtividade = 0,
        private string $estado = 'PENDENTE',
        private int $pontuacaoObtida = 0,
        private int $estrelasObtidas = 0,
        private int $bolotasObtidas = 0,
        private int $qtdTentativas = 0,
        private float $tempoExecucao = 0.0
    ) {}

    public static function arrayParaObjeto(array $registro): self
    {
        return new self(
            (int) ($registro['id_funcionario_atividade'] ?? 0),
            (int) ($registro['id_funcionario'] ?? 0),
            (int) ($registro['id_atividade'] ?? 0),
            $registro['estado'] ?? 'PENDENTE',
            (int) ($registro['pontuacao_obtida'] ?? 0),
            (int) ($registro['estrelas_obtidas'] ?? 0),
            (int) ($registro['bolotas_obtidas'] ?? 0),
            (int) ($registro['qtd_tentativas'] ?? 0),
            (float) ($registro['tempo_execucao'] ?? 0)
        );
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getIdFuncionario(): int
    {
        return $this->idFuncionario;
    }
    public function getIdAtividade(): int
    {
        return $this->idAtividade;
    }
    public function getEstado(): string
    {
        return $this->estado;
    }
    public function getPontuacaoObtida(): int
    {
        return $this->pontuacaoObtida;
    }
    public function getEstrelasObtidas(): int
    {
        return $this->estrelasObtidas;
    }
    public function getBolotasObtidas(): int
    {
        return $this->bolotasObtidas;
    }
    public function getQtdTentativas(): int
    {
        return $this->qtdTentativas;
    }
    public function getTempoExecucao(): float
    {
        return $this->tempoExecucao;
    }
}
