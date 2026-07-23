<?php

namespace app\models;

use DateTimeImmutable;

class Usuario {

    private $id;
    private $nomeCompleto;
    private $dataNascimento;
    private $cpf;
    private $email;
    private $senha;
    private $numeroTelefone;

    public function __construct(
        $id,
        $nomeCompleto,
        $dataNascimento,
        $cpf,
        $email,
        $senha,
        $numeroTelefone
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

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNomeCompleto() {
        return $this->nomeCompleto;
    }

    public function setNomeCompleto($nomeCompleto) {
        $this->nomeCompleto = $nomeCompleto;
    }

    public function getDataNascimento() {
        return $this->dataNascimento;
    }

    public function setDataNascimento($dataNascimento) {
        $this->dataNascimento = $dataNascimento;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getNumeroTelefone() {
        return $this->numeroTelefone;
    }

    public function setNumeroTelefone($numeroTelefone) {
        $this->numeroTelefone = $numeroTelefone;
    }
}