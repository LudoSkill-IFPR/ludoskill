<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Gestor;
use PDO;

class GestorRepository{
    private PDO $connection;

    public function __construct(){
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getGestores(): array{
        $stm = $this->connection->prepare("SELECT * FROM Gestores");
        $stm->execute();
        $gestores = $stm->fetchAll();
        return $gestores;
    }

    public function getGestorById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Gestores WHERE id_gestor = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $gestor = $stm->fetch();

        return $gestor;
    }

    public function saveGestor(Gestor $gestor){
        $sql = "INSERT INTO Gestores (nome_completo, data_nascimento, cpf, email, senha, numero_telefone, id_empresa) VALUES (:nomeCompleto, :dataNascimento, :cpf, :email, :senha, :numeroTelefone, :idEmpresa)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nomeCompleto', $gestor->getNomeCompleto());
        $stm->bindValue('dataNascimento', $gestor->getDataNascimento()->format('Y-m-d'));
        $stm->bindValue('cpf', $gestor->getCpf());
        $stm->bindValue('email', $gestor->getEmail());
        $stm->bindValue('senha', password_hash($gestor->getSenha(), PASSWORD_DEFAULT));
        $stm->bindValue('numeroTelefone', $gestor->getNumeroTelefone());
        $stm->bindValue('idEmpresa', $gestor->getEmpresa()->getId());
        return $stm->execute();
    }

    public function deleteGestor(int $id){
        $stm = $this->connection->prepare("DELETE FROM Gestores WHERE id_gestor = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateGestor(Gestor $gestor){
        $sql = "UPDATE Gestores SET nome_completo = :nomeCompleto, data_nascimento = :dataNascimento, cpf = :cpf, email = :email, senha = :senha, numero_telefone = :numeroTelefone, id_empresa = :idEmpresa WHERE id_gestor = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('nomeCompleto', $gestor->getNomeCompleto());
        $stm->bindValue('dataNascimento', $gestor->getDataNascimento()->format('Y-m-d'));
        $stm->bindValue('cpf', $gestor->getCpf());
        $stm->bindValue('email', $gestor->getEmail());
        $stm->bindValue('senha', password_hash($gestor->getSenha(), PASSWORD_DEFAULT));
        $stm->bindValue('numeroTelefone', $gestor->getNumeroTelefone());
        $stm->bindValue('idEmpresa', $gestor->getEmpresa()->getId());
        $stm->bindValue('id', $gestor->getId());
        return $stm->execute();
    }
    
}