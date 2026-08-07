<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Funcionario;
use PDO;

class FuncionarioRepository{
    private PDO $connection;

    public function __construct() {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getFuncionarios(): array {
        $stm = $this->connection->prepare("SELECT * FROM Funcionarios");
        $stm->execute();
        $funcionarios = $stm->fetchAll();
        return $funcionarios;
    }

    public function getFuncionarioById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Funcionarios WHERE id_funcionario = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $funcionario = $stm->fetch();

        return $funcionario;
    }

    public function saveFuncionario(Funcionario $funcionario){

        $sql = "INSERT INTO Usuarios (nome_completo, data_nascimento, cpf, email, senha_hash, numero_telefone) VALUES (:nomeCompleto, :dataNascimento, :cpf, :email, :senha_hash, :numeroTelefone)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nomeCompleto', $funcionario->getNomeCompleto());
        $stm->bindValue('dataNascimento', $funcionario->getDataNascimento()->format('Y-m-d'));
        $stm->bindValue('cpf', $funcionario->getCpf());
        $stm->bindValue('email', $funcionario->getEmail());
        $stm->bindValue('senha_hash', password_hash($funcionario->getSenha(), PASSWORD_DEFAULT));
        $stm->bindValue('numeroTelefone', $funcionario->getNumeroTelefone());
        $stm->execute();

        $lastId = $this->connection->lastInsertId(); 
        $empresa = $funcionario->getEmpresa();

        $sql = "INSERT INTO Funcionarios (id_usuario, bolotas_totais, pontuacao_total, nivel, id_empresa) VALUES (:idUsuario, :bolotasTotais, :pontuacaoTotal, :nivel, :idEmpresa)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('idUsuario', $lastId);
        $stm->bindValue('bolotasTotais', $funcionario->getBolotasTotais());
        $stm->bindValue('pontuacaoTotal', $funcionario->getPontuacaoTotal());
        $stm->bindValue('nivel', $funcionario->getNivel());
        $stm->bindValue('idEmpresa', $empresa->getId());
        return $stm->execute();
    }

    public function deleteFuncionario(int $id){
        $stm = $this->connection->prepare("DELETE FROM Funcionarios WHERE id_funcionario = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateFuncionario(Funcionario $funcionario){
        $sql = "UPDATE Funcionarios SET nome_completo = :nomeCompleto, data_nascimento = :dataNascimento, cpf = :cpf, email = :email, senha = :senha, numero_telefone = :numeroTelefone, bolotas_totais = :bolotasTotais, pontuacao_total = :pontuacaoTotal, nivel = :nivel, id_empresa = :idEmpresa WHERE id_funcionario = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nomeCompleto', $funcionario->getNomeCompleto());
        $stm->bindValue('dataNascimento', $funcionario->getDataNascimento());
        $stm->bindValue('cpf', $funcionario->getCpf());
        $stm->bindValue('email', $funcionario->getEmail());
        $stm->bindValue('senha', password_hash($funcionario->getSenha(), PASSWORD_DEFAULT));
        $stm->bindValue('numeroTelefone', $funcionario->getNumeroTelefone());
        $stm->bindValue('bolotasTotais', $funcionario->getBolotasTotais());
        $stm->bindValue('pontuacaoTotal', $funcionario->getPontuacaoTotal());
        $stm->bindValue('nivel', $funcionario->getNivel());
        $stm->bindValue('idEmpresa', $funcionario->getEmpresa()->getId());
        $stm->bindValue('id', $funcionario->getId());
        return $stm->execute();
    }

}
