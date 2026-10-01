<?php

namespace app\models;
use DateTimeImmutable;

class Empresa{
    private int $id;
    private string $cnpj;
    private string $nome;
    private string $email;
    private string $plano;

    public function __construct(
        int $id = 0,
        string $cnpj = '',
        string $nome = '',
        string $email = '',
        string $plano = 'BASICO' // Valor padrão para o plano
    ) {
        $this->id = $id;
        $this->cnpj = $cnpj;
        $this->nome = $nome;
        $this->email = $email;
        $this->plano = $plano; // Valor padrão para o plano
    }
   
    public static function arrayParaObjeto(array $empresa): self
    {
        return new self(
            (int) ($empresa['id_empresa'] ?? 0),
            $empresa['cnpj'] ?? '',
            $empresa['nome'] ?? '',
            $empresa['email'] ?? '',
            $empresa['plano'] ?? 'BASICO'
        );
    }
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
    public function getPlano(): string
    {
        return $this->plano;
    }

    /**
     * Set the value of plano
     */
    public function setPlano(string $plano): self
    {
        $this->plano = $plano;

        return $this;
    }
}