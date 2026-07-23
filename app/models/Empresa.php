<?php

namespace app\models;

class Empresa{
    private $id;
    private string $cnpj;
    private string $nome;
    private string $email;
    private $plano;

    /**
     * Get the value of ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the value of Id
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of cnpj
     */
    public function getCnpj(): string
    {
        return $this->cnpj;
    }

    /**
     * Set the value of cnpj
     */
    public function setCnpj(string $cnpj): self
    {
        $this->cnpj = $cnpj;

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
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of plano
     */
    public function getPlano()
    {
        return $this->plano;
    }

    /**
     * Set the value of plano
     */
    public function setPlano($plano): self
    {
        $this->plano = $plano;

        return $this;
    }
}