<?php

namespace app\models;

class Item {
    private int $id;
    private string $estado;
    private string $nome;
    private string $tipo;
    private int $preco;
    private string $imagem;

    public function __construct(
        int $id = 0,
        string $estado = 'ATIVO', // Valor padrão para o estado
        string $nome = '',
        string $tipo = '',
        int $preco = 0,
        string $imagem = ''
    ) {
        $this->id = $id;
        $this->estado = $estado; // Valor padrão para o estado
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->preco = $preco;
        $this->imagem = $imagem;
    }

    public static function arrayParaObjeto(array $item): self
    {
        // A coluna estado é BIT(1): pode chegar como 1, '1', true ou "\x01".
        $bit = $item['estado'] ?? 1;
        if ($bit === 'ATIVO' || $bit === 'INATIVO') {
            $estado = $bit;
        } else {
            $estado = ($bit === true || $bit === 1 || $bit === '1' || $bit === "\x01") ? 'ATIVO' : 'INATIVO';
        }

        return new self(
            (int) ($item['id_item'] ?? 0),
            $estado,
            $item['nome'] ?? '',
            $item['tipo'] ?? '',
            (int) ($item['preco'] ?? 0),
            $item['imagem'] ?? ''
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
     * Get the value of tipo
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     */
    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of preco
     */
    public function getPreco(): int
    {
        return $this->preco;
    }

    /**
     * Set the value of preco
     */
    public function setPreco(int $preco): self
    {
        $this->preco = $preco;

        return $this;
    }

    /**
     * Get the value of imagem
     */
    public function getImagem(): string
    {
        return $this->imagem;
    }

    /**
     * Set the value of imagem
     */
    public function setImagem(string $imagem): self
    {
        $this->imagem = $imagem;

        return $this;
    }
}