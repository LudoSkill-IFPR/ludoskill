<?php

namespace app\models;

use app\models\Usuario;
use app\models\Empresa;
use DateTimeImmutable;

class Funcionario extends Usuario{
    private Empresa $empresa;

    private int $bolotas_totais;
    private int $pontuacao_total;
    private int $nivel;

    public function __construct(
        int $id,
        string $nomeCompleto,
        DateTimeImmutable $dataNascimento,
        string $cpf,
        string $email,
        string $senha,
        string  $numeroTelefone,
        Empresa $empresa,
        int $bolotas_totais,
        int $pontuacao_total,
        int $nivel
    ) {
        parent::__construct($id, $nomeCompleto, $dataNascimento, $cpf, $email, $senha, $numeroTelefone);
        $this->empresa = $empresa;
        $this->bolotas_totais = $bolotas_totais;
        $this->pontuacao_total = $pontuacao_total;
        $this->nivel = $nivel;
    }

    /**
     * Get the value of empresa
     */
    public function getEmpresa(): Empresa
    {
        return $this->empresa;
    }

    /**
     * Set the value of empresa
     */
    public function setEmpresa(Empresa $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get the value of bolotas_totais
     */
    public function getBolotasTotais(): int
    {
        return $this->bolotas_totais;
    }

    /**
     * Set the value of bolotas_totais
     */
    public function setBolotasTotais(int $bolotas_totais): self
    {
        $this->bolotas_totais = $bolotas_totais;

        return $this;
    }

    /**
     * Get the value of pontuacao_total
     */
    public function getPontuacaoTotal(): int
    {
        return $this->pontuacao_total;
    }

    /**
     * Set the value of pontuacao_total
     */
    public function setPontuacaoTotal(int $pontuacao_total): self
    {
        $this->pontuacao_total = $pontuacao_total;

        return $this;
    }

    /**
     * Get the value of nivel
     */
    public function getNivel(): int
    {
        return $this->nivel;
    }

    /**
     * Set the value of nivel
     */
    public function setNivel(int $nivel): self
    {
        $this->nivel = $nivel;

        return $this;
    }
}
