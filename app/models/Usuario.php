<?php

namespace app\models;

use DateTimeImmutable;

class Usuario {

    private int $id;
    private string $nomeCompleto;
    private DateTimeImmutable $dataNascimento;
    private string $cpf;
    private string $email;
    private string $senha;
    private string $numeroTelefone;

    public function __construct(
        int $id,
        string $nomeCompleto,
        DateTimeImmutable $dataNascimento,
        string $cpf,
        string $email,
        string $senha,
        string  $numeroTelefone
    ) {
        $this->id = $id;
        $this->nomeCompleto = $nomeCompleto;
        $this->dataNascimento = $dataNascimento;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->senha = $senha;
        $this->numeroTelefone = $numeroTelefone;
    }

    public static function arrayParaObjeto(array $usuario)
    {
        return new self(
            $usuario['id_usuario'],
            $usuario['nome_completo'],
            $usuario['data_nascimento'],
            $usuario['cpf'],
            $usuario['email'],
            $usuario['senha'],
            $usuario['numero_telefone']
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
     * Get the value of nomeCompleto
     */
    public function getNomeCompleto(): string
    {
        return $this->nomeCompleto;
    }

    /**
     * Set the value of nomeCompleto
     */
    public function setNomeCompleto(string $nomeCompleto): self
    {
        $this->nomeCompleto = $nomeCompleto;

        return $this;
    }

    /**
     * Get the value of dataNascimento
     */
    public function getDataNascimento(): DateTimeImmutable
    {
        return $this->dataNascimento;
    }

    /**
     * Set the value of dataNascimento
     */
    public function setDataNascimento(DateTimeImmutable $dataNascimento): self
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    /**
     * Get the value of cpf
     */
    public function getCpf(): string
    {
        return $this->cpf;
    }

    /**
     * Set the value of cpf
     */
    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

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
     * Get the value of senha
     */
    public function getSenha(): string
    {
        return $this->senha;
    }

    /**
     * Set the value of senha
     */
    public function setSenha(string $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get the value of numeroTelefone
     */
    public function getNumeroTelefone(): string
    {
        return $this->numeroTelefone;
    }

    /**
     * Set the value of numeroTelefone
     */
    public function setNumeroTelefone(string $numeroTelefone): self
    {
        $this->numeroTelefone = $numeroTelefone;

        return $this;
    }
}