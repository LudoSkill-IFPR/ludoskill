<?php

namespace app\models;

use app\models\Usuario;
use app\models\Empresa;
use app\models\Funcionario;
use app\repositories\FuncionarioRepository;

class Gestor extends Usuario
{
    private int $id;
    private Empresa $empresa;
    private FuncionarioRepository $funcionarioRepository;

    public function __construct(
        int $id,
        string $nomeCompleto,
        \DateTimeImmutable $dataNascimento,
        string $cpf,
        string $email,
        string $senha,
        string  $numeroTelefone,
        Empresa $empresa
    ) {
        parent::__construct($id, $nomeCompleto, $dataNascimento, $cpf, $email, $senha, $numeroTelefone);
        $this->empresa = $empresa;
        $this->funcionarioRepository = new FuncionarioRepository();
    }

    public function cadastrarFuncionario($funcionario)
    {
        $this->funcionarioRepository->saveFuncionario($funcionario);
    }

    public function editarFuncionario($funcionario)
    {
        $this->funcionarioRepository->updateFuncionario($funcionario);
    }

    public function desativarFuncionario()
    {

    }

    public function emitirRelatorio()
    {
        //conversar sobre depois
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
}