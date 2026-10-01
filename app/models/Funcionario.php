<?php

namespace app\models;

use app\models\Usuario;
use app\models\Empresa;
use DateTimeImmutable;

class Funcionario extends Usuario{
    private int $idFuncionario;
    private Empresa $empresa;

    private int $bolotas_totais;
    private int $pontuacao_total;
    private int $nivel;

    public function __construct(
        int $id = 0,
        string $nomeCompleto = '',
        ?DateTimeImmutable $dataNascimento = null,
        string $cpf = '',
        string $email = '',
        string $senha = '',
        ?string $numeroTelefone = null,
        ?Empresa $empresa = null,
        int $bolotas_totais = 0,
        int $pontuacao_total = 0,
        int $nivel = 1, // Valor padrão para o nível
        string $estado = 'ATIVO', // Valor padrão para o estado
        int $idFuncionario = 0
    ) {
        parent::__construct($id, $nomeCompleto, $dataNascimento, $cpf, $email, $senha, $numeroTelefone, 'funcionario', $estado);
        $this->idFuncionario = $idFuncionario;
        $this->empresa = $empresa ?? new Empresa();
        $this->bolotas_totais = $bolotas_totais;
        $this->pontuacao_total = $pontuacao_total;
        $this->nivel = $nivel;
    }

    /**
     * O array deve vir de um JOIN entre Usuarios e Funcionarios.
     * Se o objeto Empresa não for informado, cria uma Empresa apenas com o id (FK id_empresa).
     */
    public static function arrayParaObjeto(array $funcionario, ?Empresa $empresa = null): self
    {
        $u = self::extrairDadosUsuario($funcionario);
        $empresa ??= new Empresa((int) ($funcionario['id_empresa'] ?? 0));

        return new self(
            $u['id'],
            $u['nomeCompleto'],
            $u['dataNascimento'],
            $u['cpf'],
            $u['email'],
            $u['senha'],
            $u['numeroTelefone'],
            $empresa,
            (int) ($funcionario['bolotas_totais'] ?? 0),
            (int) ($funcionario['pontuacao_total'] ?? 0),
            (int) ($funcionario['nivel'] ?? 1),
            $u['estado'],
            (int) ($funcionario['id_funcionario'] ?? 0)
        );
    }

    public function getIdFuncionario(): int
    {
        return $this->idFuncionario;
    }

    public function setIdFuncionario(int $idFuncionario): self
    {
        $this->idFuncionario = $idFuncionario;

        return $this;
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