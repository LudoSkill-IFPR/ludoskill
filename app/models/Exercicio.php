<?php 

namespace app\models;

class Exercicio {
    private int $id;
    private string $descricao;
    private string $conteudo;
    private Atividade $atividade;

    /**
     * Cria uma entidade de exercício com seus dados básicos.
     *
     * O conteúdo permanece como uma string JSON, pois é nesse formato que ele é
     * armazenado na coluna `conteudo`. Quando nenhuma atividade é recebida, cria
     * um objeto Atividade vazio para manter a propriedade sempre inicializada.
     */
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
     * Converte uma linha associativa retornada pelo banco em um objeto Exercicio.
     *
     * Se a atividade completa não for informada, monta uma referência contendo o
     * ID da chave estrangeira e, quando disponível no JOIN, o nome da atividade.
     * Também garante que `conteudo` seja uma string JSON, mesmo quando o valor já
     * tiver sido decodificado antes de chegar ao model.
     */
    public static function arrayParaObjeto(array $exercicio, ?Atividade $atividade = null): self
    {
        $atividade ??= new Atividade(
            (int) ($exercicio['id_atividade'] ?? 0),
            null,
            '',
            (string) ($exercicio['nome_atividade'] ?? '')
        );

        // O PDO normalmente entrega a coluna JSON como string; arrays são codificados novamente.
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

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getConteudo(): string
    {
        return $this->conteudo;
    }

    public function setConteudo(string $conteudo): self
    {
        $this->conteudo = $conteudo;

        return $this;
    }

    public function getAtividade(): Atividade
    {
        return $this->atividade;
    }

    public function setAtividade(Atividade $atividade): self
    {
        $this->atividade = $atividade;

        return $this;
    }
}
