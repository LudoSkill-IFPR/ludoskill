<?php

namespace app\models;

use app\models\Usuario;
use app\models\Empresa;
use app\models\Funcionario;
use app\repositories\FuncionarioRepository;

class Gestor extends Usuario
{
    private int $idGestor;
    private Empresa $empresa;
    private FuncionarioRepository $funcionarioRepository;

    public function __construct(
        int $id = 0,
        string $nomeCompleto = '',
        ?\DateTimeImmutable $dataNascimento = null,
        string $cpf = '',
        string $email = '',
        string $senha = '',
        ?string $numeroTelefone = null,
        ?Empresa $empresa = null,
        string $estado = 'ATIVO', // Valor padrão para o estado
        int $idGestor = 0
    ) {
        parent::__construct($id, $nomeCompleto, $dataNascimento, $cpf, $email, $senha, $numeroTelefone, 'gestor', $estado);
        $this->idGestor = $idGestor;
        $this->empresa = $empresa ?? new Empresa();
        $this->funcionarioRepository = new FuncionarioRepository();
    }

    /**
     * O array deve vir de um JOIN entre Usuarios e Gestores.
     * Se o objeto Empresa não for informado, cria uma Empresa apenas com o id (FK id_empresa).
     */
    public static function arrayParaObjeto(array $gestor, ?Empresa $empresa = null): self
    {
        $u = self::extrairDadosUsuario($gestor);
        $empresa ??= new Empresa((int) ($gestor['id_empresa'] ?? 0));

        return new self(
            $u['id'],
            $u['nomeCompleto'],
            $u['dataNascimento'],
            $u['cpf'],
            $u['email'],
            $u['senha'],
            $u['numeroTelefone'],
            $empresa,
            $u['estado'],
            (int) ($gestor['id_gestor'] ?? 0)
        );
    }

    public function getIdGestor(): int
    {
        return $this->idGestor;
    }

    public function setIdGestor(int $idGestor): self
    {
        $this->idGestor = $idGestor;

        return $this;
    }

    public function cadastrarFuncionario($funcionario)
    {
        $this->funcionarioRepository->saveFuncionario($funcionario);
    }

    public function editarFuncionario($funcionario)
    {
        $this->funcionarioRepository->updateFuncionario(
            $funcionario,
            $funcionario->getEmpresa()->getId(),
            $funcionario->getSenha()
        );
    }

    public function desativarFuncionario()
    {

    }

    public function emitirRelatorio()
    {
        //conversar sobre depois
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