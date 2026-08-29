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

    public function getEmpresasComQuantidadeUsuarios(): array {
        $sql = "SELECT e.id_empresa, e.nome, e.cnpj, e.email, e.plano,
                       COUNT(DISTINCT g.id_gestor) AS quantidade_gestores,
                       COUNT(DISTINCT f.id_funcionario) AS quantidade_funcionarios,
                       COUNT(DISTINCT g.id_gestor) + COUNT(DISTINCT f.id_funcionario) AS quantidade_usuarios
                FROM Empresas e
                LEFT JOIN Gestores g ON g.id_empresa = e.id_empresa
                LEFT JOIN Funcionarios f ON f.id_empresa = e.id_empresa
                GROUP BY e.id_empresa, e.nome, e.cnpj, e.email, e.plano
                ORDER BY quantidade_usuarios DESC, e.nome";

        $stm = $this->connection->prepare($sql);
        $stm->execute();
        return $stm->fetchAll();
    }

    public function getEmpresaById(int $id){
        $stm = $this->connection->prepare("SELECT * FROM Empresas WHERE id_empresa = :id");
        $stm->bindValue('id', $id);

        $stm->execute();

        $empresa = $stm->fetch();

        return $empresa;
    }

    public function cnpjExiste(string $cnpj, ?int $idIgnorado = null): bool {
        $sql = "SELECT 1 FROM Empresas WHERE cnpj = :cnpj";
        if ($idIgnorado !== null) {
            $sql .= " AND id_empresa <> :id";
        }
        $sql .= " LIMIT 1";

        $stm = $this->connection->prepare($sql);
        $stm->bindValue('cnpj', $cnpj);
        if ($idIgnorado !== null) {
            $stm->bindValue('id', $idIgnorado, PDO::PARAM_INT);
        }
        $stm->execute();
        return $stm->fetchColumn() !== false;
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

    public function possuiUsuariosVinculados(int $id): bool {
        $sql = "SELECT EXISTS(SELECT 1 FROM Gestores WHERE id_empresa = :id UNION ALL SELECT 1 FROM Funcionarios WHERE id_empresa = :id)";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->execute();
        return (bool) $stm->fetchColumn();
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
