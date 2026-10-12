<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Inventario;
use DomainException;
use PDO;

class InventarioRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getByUsuario(int $idUsuario): array
    {
        $sql = "SELECT i.*, it.id_item, it.estado, it.nome, it.tipo, it.preco, it.imagem
                FROM Inventarios i
                INNER JOIN Funcionarios f ON f.id_funcionario = i.id_funcionario
                INNER JOIN Itens it ON it.id_item = i.id_item
                WHERE f.id_usuario = :id_usuario
                ORDER BY i.data_aquisicao DESC, i.id_inventario DESC";
        $stm = $this->connection->prepare($sql);
        $stm->execute(['id_usuario' => $idUsuario]);
        return array_map([Inventario::class, 'arrayParaObjeto'], $stm->fetchAll());
    }

    public function comprar(int $idUsuario, int $idItem): void
    {
        $this->connection->beginTransaction();
        try {
            $stm = $this->connection->prepare(
                "SELECT f.id_funcionario, f.bolotas_totais
                 FROM Funcionarios f
                 INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
                 WHERE f.id_usuario = :usuario AND u.estado = 'ATIVO' FOR UPDATE"
            );
            $stm->execute(['usuario' => $idUsuario]);
            $funcionario = $stm->fetch();
            if (!$funcionario) throw new DomainException('Funcionário não encontrado.');

            $stm = $this->connection->prepare("SELECT id_item, preco FROM Itens WHERE id_item = :item AND estado = b'1'");
            $stm->execute(['item' => $idItem]);
            $item = $stm->fetch();
            if (!$item) throw new DomainException('Este item não está disponível.');

            $stm = $this->connection->prepare(
                'SELECT 1 FROM Inventarios WHERE id_funcionario = :funcionario AND id_item = :item'
            );
            $stm->execute(['funcionario' => $funcionario['id_funcionario'], 'item' => $idItem]);
            if ($stm->fetchColumn()) throw new DomainException('Este item já pertence ao seu inventário.');
            if ((int) $funcionario['bolotas_totais'] < (int) $item['preco']) {
                throw new DomainException('Saldo de bolotas insuficiente.');
            }

            $stm = $this->connection->prepare(
                'INSERT INTO Inventarios (id_funcionario, id_item) VALUES (:funcionario, :item)'
            );
            $stm->execute(['funcionario' => $funcionario['id_funcionario'], 'item' => $idItem]);

            $stm = $this->connection->prepare(
                'UPDATE Funcionarios SET bolotas_totais = bolotas_totais - :preco WHERE id_funcionario = :funcionario'
            );
            $stm->execute(['preco' => $item['preco'], 'funcionario' => $funcionario['id_funcionario']]);
            $this->connection->commit();
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) $this->connection->rollBack();
            throw $exception;
        }
    }

    public function equipar(int $idUsuario, int $idItem): string
    {
        $this->connection->beginTransaction();
        try {
            $sql = "SELECT i.id_funcionario, it.tipo
                    FROM Inventarios i
                    INNER JOIN Funcionarios f ON f.id_funcionario = i.id_funcionario
                    INNER JOIN Itens it ON it.id_item = i.id_item
                    WHERE f.id_usuario = :usuario AND i.id_item = :item FOR UPDATE";
            $stm = $this->connection->prepare($sql);
            $stm->execute(['usuario' => $idUsuario, 'item' => $idItem]);
            $inventario = $stm->fetch();
            if (!$inventario) throw new DomainException('O item não pertence ao seu inventário.');

            $sql = "UPDATE Inventarios i
                    INNER JOIN Itens it ON it.id_item = i.id_item
                    SET i.em_uso = b'0'
                    WHERE i.id_funcionario = :funcionario AND it.tipo = :tipo";
            $stm = $this->connection->prepare($sql);
            $stm->execute(['funcionario' => $inventario['id_funcionario'], 'tipo' => $inventario['tipo']]);

            $stm = $this->connection->prepare(
                "UPDATE Inventarios SET em_uso = b'1'
                 WHERE id_funcionario = :funcionario AND id_item = :item"
            );
            $stm->execute(['funcionario' => $inventario['id_funcionario'], 'item' => $idItem]);
            $this->connection->commit();
            return $inventario['tipo'];
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) $this->connection->rollBack();
            throw $exception;
        }
    }
}
