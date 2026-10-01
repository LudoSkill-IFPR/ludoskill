<?php 

namespace app\models;

class Exercicio {
    private int $id;
    private string $descricao;
    private string $conteudo;
    private Atividade $atividade;

    public function __construct(
        int $id = 0,
        string $descricao = '',
        string $conteudo = '', // JSON com perguntas, alternativas e justificativa da resposta
        ?Atividade $atividade = null
    ) {
        $this->id = $id;
        $this->descricao = $descricao;
        $this->conteudo = $conteudo;
        $this->atividade = $atividade ?? new Atividade();
    }

    /**
     * Se o objeto Atividade não for informado, cria uma Atividade apenas com o id (FK id_atividade).
     */
    public static function arrayParaObjeto(array $exercicio, ?Atividade $atividade = null): self
    {
        $atividade ??= new Atividade((int) ($exercicio['id_atividade'] ?? 0));

        // A coluna JSON chega como string do PDO; se já vier decodificada, volta para JSON.
        $conteudo = $exercicio['conteudo'] ?? '';
        if (!is_string($conteudo)) {
            $conteudo = (string) json_encode($conteudo, JSON_UNESCAPED_UNICODE);
        }

        return new self(
            (int) ($exercicio['id_exercicio'] ?? 0),
            $exercicio['descricao'] ?? '',
            $conteudo,
            $atividade
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
     * Get the value of descricao
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     */
    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get the value of conteudo
     */
    public function getConteudo(): string
    {
        return $this->conteudo;
    }

    /**
     * Set the value of conteudo
     */
    public function setConteudo(string $conteudo): self
    {
        $this->conteudo = $conteudo;

        return $this;
    }

    /**
     * Get the value of atividade
     */
    public function getAtividade(): Atividade
    {
        return $this->atividade;
    }

    /**
     * Set the value of atividade
     */
    public function setAtividade(Atividade $atividade): self
    {
        $this->atividade = $atividade;

        return $this;
    }
}