<?php

/**
 * CORRIGIDO
 */

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Empresa;
use PDO;

class EmpresaRepository {
    private PDO $connection;

    public function __construct() {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getEmpresas(): array {
        $stm = $this->connection->prepare("SELECT * FROM Empresas");
        $stm->execute();
        $empresas = $stm->fetchAll();
        return $empresas;
    }

    public function getEmpresaById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Empresas WHERE id_empresa = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $empresa = $stm->fetch();

        return $empresa;
    }

    public function saveEmpresa(Empresa $empresa){
        $sql = "INSERT INTO Empresas (cnpj, nome, email, plano) VALUES (:cnpj, :nome, :email, :plano)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('cnpj', $empresa->getCnpj());
        $stm->bindValue('nome', $empresa->getNome());
        $stm->bindValue('email', $empresa->getEmail());
        $stm->bindValue('plano', $empresa->getPlano());
        return $stm->execute();
    }

    public function deleteEmpresa(int $id){
        $stm = $this->connection->prepare("DELETE FROM Empresas WHERE id_empresa = :id");
        $stm->bindValue('id', $id);
        return $stm->execute();
    }

    public function updateEmpresa(Empresa $empresa){
        $sql = "UPDATE Empresas SET cnpj = :cnpj, nome = :nome, email = :email, plano = :plano WHERE id_empresa = :id";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('cnpj', $empresa->getCnpj());
        $stm->bindValue('nome', $empresa->getNome());
        $stm->bindValue('email', $empresa->getEmail());
        $stm->bindValue('plano', $empresa->getPlano());
        $stm->bindValue('id', $empresa->getId());
        return $stm->execute();
    }

    public function countEmpresas(): int {
        $stm = $this->connection->prepare("SELECT COUNT(*) FROM Empresas");
        $stm->execute();
        return (int) $stm->fetchColumn();
    }
}