<?php

namespace app\models;

class Atividade {
    private int $id;
    private Modulo $modulo;
    private string $tipoAtividade;
    private string $nome;
    private string $estado;
    private int $pontuacao;
    private int $estrelas;

    

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of modulo
     */
    public function getModulo(): Modulo
    {
        return $this->modulo;
    }

    /**
     * Set the value of modulo
     */
    public function setModulo(Modulo $modulo): self
    {
        $this->modulo = $modulo;

        return $this;
    }

    /**
     * Get the value of tipoAtividade
     */
    public function getTipoAtividade(): string
    {
        return $this->tipoAtividade;
    }

    /**
     * Set the value of tipoAtividade
     */
    public function setTipoAtividade(string $tipoAtividade): self
    {
        $this->tipoAtividade = $tipoAtividade;

        return $this;
    }

    /**
     * Get the value of nome
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     */
    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of estado
     */
    public function getEstado(): string
    {
        return $this->estado;
    }

    /**
     * Set the value of estado
     */
    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get the value of pontuacao
     */
    public function getPontuacao(): int
    {
        return $this->pontuacao;
    }

    /**
     * Set the value of pontuacao
     */
    public function setPontuacao(int $pontuacao): self
    {
        $this->pontuacao = $pontuacao;

        return $this;
    }

    /**
     * Get the value of estrelas
     */
    public function getEstrelas(): int
    {
        return $this->estrelas;
    }

    /**
     * Set the value of estrelas
     */
    public function setEstrelas(int $estrelas): self
    {
        $this->estrelas = $estrelas;

        return $this;
    }
}