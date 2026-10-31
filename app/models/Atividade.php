<?php

namespace app\models;

class Atividade {
    private int $id;
    private Modulo $modulo;
    private string $tipoAtividade;
    private string $nome;
    private string $estado;
    private int $ordem;
    private int $tempoLimite;
    private string $descricao;

    public function __construct(
        int $id = 0,
        ?Modulo $modulo = null,
        string $tipoAtividade = '',
        string $nome = '',
        string $estado = 'ATIVO',
        int $ordem = 1,
        int $tempoLimite = 180,
        string $descricao = ''
    ) {
        $this->id = $id;
        $this->modulo = $modulo ?? new Modulo();
        $this->tipoAtividade = $tipoAtividade;
        $this->nome = $nome;
        $this->estado = $estado;
        $this->ordem = $ordem;
        $this->tempoLimite = $tempoLimite;
        $this->descricao = $descricao;
    }

    /**
     * Se o objeto Modulo não for informado, cria um Modulo apenas com o id (FK id_modulo).
     */
    public static function arrayParaObjeto(array $atividade, ?Modulo $modulo = null): self
    {
        $modulo ??= new Modulo((int) ($atividade['id_modulo'] ?? 0));

        return new self(
            (int) ($atividade['id_atividade'] ?? 0),
            $modulo,
            $atividade['tipo_atividade'] ?? '',
            $atividade['nome'] ?? '',
            $atividade['estado'] ?? 'ATIVO',
            (int) ($atividade['ordem'] ?? 1),
            (int) ($atividade['tempo_limite'] ?? 180),
            (string) ($atividade['descricao'] ?? '')
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

    public function getOrdem(): int
    {
        return $this->ordem;
    }

    public function setOrdem(int $ordem): self
    {
        $this->ordem = $ordem;
        return $this;
    }

    public function getTempoLimite(): int
    {
        return $this->tempoLimite;
    }

    public function setTempoLimite(int $tempoLimite): self
    {
        $this->tempoLimite = $tempoLimite;
        return $this;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }
}
