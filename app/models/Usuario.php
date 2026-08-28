<?php

namespace app\models;

use DateTimeImmutable;

// use DateTimeImmutable;

class Usuario
{
    private int $id;
    private string $nomeCompleto;
    private ?DateTimeImmutable $dataNascimento;
    private string $cpf;
    private string $email;
    private string $senha;
    private ?string $numeroTelefone;
    private ?string $perfil;

    public function __construct(
        int $id = 0,
        string $nomeCompleto = '',
        ?DateTimeImmutable $dataNascimento = null,
        string $cpf = '',
        string $email = '',
        string $senha = '',
        ?string $numeroTelefone = null,
        ?string $perfil = null
    ) {
        $this->id = $id;
        $this->nomeCompleto = $nomeCompleto;
        $this->dataNascimento = $dataNascimento;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->senha = $senha;
        $this->numeroTelefone = $numeroTelefone;
        $this->perfil = $perfil;
    }

    public static function arrayParaObjeto(array $usuario): self
    {
        $dataNascimento = $usuario['data_nascimento'] ?? null;
        if (is_string($dataNascimento) && $dataNascimento !== '') {
            $dataNascimento = new DateTimeImmutable($dataNascimento);
        }

        return new self(
            (int) ($usuario['id_usuario'] ?? 0),
            $usuario['nome_completo'] ?? '',
            $dataNascimento,
            $usuario['CPF'] ?? $usuario['cpf'] ?? '',
            $usuario['email'] ?? '',
            $usuario['senha_hash'] ?? $usuario['senha'] ?? '',
            $usuario['numero_telefone'] ?? null,
            $usuario['perfil'] ?? null
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNomeCompleto(): string
    {
        return $this->nomeCompleto;
    }

    public function setNomeCompleto(string $nomeCompleto): self
    {
        $this->nomeCompleto = $nomeCompleto;

        return $this;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    public function getNumeroTelefone(): ?string
    {
        return $this->numeroTelefone;
    }

    public function setNumeroTelefone(?string $numeroTelefone): self
    {
        $this->numeroTelefone = $numeroTelefone;

        return $this;
    }

    public function getPerfil(): ?string
    {
        return $this->perfil;
    }

    public function setPerfil(?string $perfil): self
    {
        $this->perfil = $perfil;

        return $this;
    }

    public function getDataNascimento(): ?DateTimeImmutable
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(DateTimeImmutable $dataNascimento): self
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }
}
