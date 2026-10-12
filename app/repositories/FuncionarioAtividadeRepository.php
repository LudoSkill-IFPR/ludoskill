<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use PDO;
use RuntimeException;
use app\models\Atividade;
use app\models\FuncionarioAtividade;

class FuncionarioAtividadeRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getFuncionarioIdByUsuario(int $idUsuario): ?int
    {
        $stm = $this->connection->prepare(
            'SELECT id_funcionario FROM Funcionarios WHERE id_usuario = :id_usuario'
        );
        $stm->bindValue('id_usuario', $idUsuario, PDO::PARAM_INT);
        $stm->execute();
        $id = $stm->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    public function getResultadosByUsuario(int $idUsuario): array
    {
        $sql = "SELECT fa.*
                FROM Funcionario_atividade fa
                INNER JOIN Funcionarios f ON f.id_funcionario = fa.id_funcionario
                WHERE f.id_usuario = :id_usuario";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('id_usuario', $idUsuario, PDO::PARAM_INT);
        $stm->execute();

        $resultados = [];
        foreach ($stm->fetchAll() as $resultado) {
            $resultados[(int) $resultado['id_atividade']] = FuncionarioAtividade::arrayParaObjeto($resultado);
        }
        return $resultados;
    }

    public function getDesempenhoByEmpresa(int $idEmpresa): array
    {
        $sql = "SELECT f.id_funcionario, u.nome_completo,
                       COALESCE(AVG(CASE WHEN fa.estado = 'CONCLUIDO' THEN fa.estrelas_obtidas END), 0) AS media_estrelas,
                       COUNT(CASE WHEN fa.estado = 'CONCLUIDO' THEN 1 END) AS atividades_concluidas
                FROM Funcionarios f
                INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
                LEFT JOIN Funcionario_atividade fa ON fa.id_funcionario = f.id_funcionario
                WHERE f.id_empresa = :empresa AND u.estado = 'ATIVO'
                GROUP BY f.id_funcionario, u.nome_completo
                ORDER BY media_estrelas DESC, u.nome_completo";
        $stm = $this->connection->prepare($sql);
        $stm->execute(['empresa' => $idEmpresa]);
        return $stm->fetchAll();
    }

    public function getTaxaConclusaoByEmpresa(int $idEmpresa): float
    {
        $stm = $this->connection->prepare(
            "SELECT COUNT(*) FROM Funcionarios f INNER JOIN Usuarios u ON u.id_usuario = f.id_usuario
             WHERE f.id_empresa = :empresa AND u.estado = 'ATIVO'"
        );
        $stm->execute(['empresa' => $idEmpresa]);
        $funcionarios = (int) $stm->fetchColumn();
        $atividades = (int) $this->connection->query(
            "SELECT COUNT(*) FROM Atividades a INNER JOIN Modulos m ON m.id_modulo = a.id_modulo
             WHERE a.estado = 'ATIVO' AND m.estado = 'ATIVO'"
        )->fetchColumn();
        $total = $funcionarios * $atividades;
        if ($total === 0) return 0.0;
        $stm = $this->connection->prepare(
            "SELECT COUNT(*) FROM Funcionario_atividade fa
             INNER JOIN Funcionarios f ON f.id_funcionario = fa.id_funcionario
             WHERE f.id_empresa = :empresa AND fa.estado = 'CONCLUIDO'"
        );
        $stm->execute(['empresa' => $idEmpresa]);
        return ((int) $stm->fetchColumn() / $total) * 100;
    }

    public function podeAcessarAtividade(int $idUsuario, int $idAtividade): bool
    {
        $sql = "SELECT a.id_modulo, a.ordem, m.min_estrelas_liberacao
                FROM Atividades a
                INNER JOIN Modulos m ON m.id_modulo = a.id_modulo
                WHERE a.id_atividade = :atividade AND a.estado = 'ATIVO' AND m.estado = 'ATIVO'";
        $stm = $this->connection->prepare($sql);
        $stm->execute(['atividade' => $idAtividade]);
        $atividade = $stm->fetch();
        if (!$atividade) return false;

        $stm = $this->connection->prepare(
            "SELECT COALESCE(SUM(fa.estrelas_obtidas), 0)
             FROM Funcionario_atividade fa
             INNER JOIN Funcionarios f ON f.id_funcionario = fa.id_funcionario
             WHERE f.id_usuario = :usuario AND fa.estado = 'CONCLUIDO'"
        );
        $stm->execute(['usuario' => $idUsuario]);
        if ((int) $stm->fetchColumn() < (int) $atividade['min_estrelas_liberacao']) return false;

        $sql = "SELECT COUNT(*)
                FROM Atividades anterior
                WHERE anterior.id_modulo = :modulo AND anterior.estado = 'ATIVO'
                  AND anterior.ordem < :ordem
                  AND NOT EXISTS (
                      SELECT 1 FROM Funcionario_atividade fa
                      INNER JOIN Funcionarios f ON f.id_funcionario = fa.id_funcionario
                      WHERE f.id_usuario = :usuario
                        AND fa.id_atividade = anterior.id_atividade
                        AND fa.estado = 'CONCLUIDO'
                  )";
        $stm = $this->connection->prepare($sql);
        $stm->execute(['modulo' => $atividade['id_modulo'], 'ordem' => $atividade['ordem'], 'usuario' => $idUsuario]);
        return (int) $stm->fetchColumn() === 0;
    }

    public function iniciarAtividade(int $idUsuario, int $idAtividade): void
    {
        $idFuncionario = $this->getFuncionarioIdByUsuario($idUsuario);
        if ($idFuncionario === null) throw new RuntimeException('Funcionário não encontrado.');
        $sql = "INSERT INTO Funcionario_atividade
                    (id_funcionario, id_atividade, estado, qtd_tentativas)
                VALUES (:funcionario, :atividade, 'INICIADO', 0)
                ON DUPLICATE KEY UPDATE estado = IF(estado = 'CONCLUIDO', estado, 'INICIADO')";
        $stm = $this->connection->prepare($sql);
        $stm->execute(['funcionario' => $idFuncionario, 'atividade' => $idAtividade]);
    }

    public function getResumoByUsuario(int $idUsuario): array
    {
        $sql = "SELECT COUNT(a.id_atividade) AS total,
                       COUNT(CASE WHEN fa.estado = 'CONCLUIDO' THEN 1 END) AS concluidas,
                       COALESCE(SUM(CASE WHEN fa.estado = 'CONCLUIDO' THEN fa.estrelas_obtidas ELSE 0 END), 0) AS estrelas,
                       COALESCE(AVG(CASE WHEN fa.estado = 'CONCLUIDO' THEN fa.estrelas_obtidas END), 0) AS desempenho
                FROM Atividades a
                INNER JOIN Modulos m ON m.id_modulo = a.id_modulo AND m.estado = 'ATIVO'
                LEFT JOIN Funcionarios f ON f.id_usuario = :usuario
                LEFT JOIN Funcionario_atividade fa ON fa.id_funcionario = f.id_funcionario AND fa.id_atividade = a.id_atividade
                WHERE a.estado = 'ATIVO'";
        $stm = $this->connection->prepare($sql);
        $stm->execute(['usuario' => $idUsuario]);
        return $stm->fetch() ?: ['total' => 0, 'concluidas' => 0, 'estrelas' => 0, 'desempenho' => 0];
    }

    public function getProximaAtividade(int $idUsuario): ?Atividade
    {
        $stm = $this->connection->query(
            "SELECT a.* FROM Atividades a
             INNER JOIN Modulos m ON m.id_modulo = a.id_modulo
             WHERE a.estado = 'ATIVO' AND m.estado = 'ATIVO'
             ORDER BY m.id_modulo, a.ordem, a.id_atividade"
        );
        foreach ($stm->fetchAll() as $registro) {
            if (!$this->podeAcessarAtividade($idUsuario, (int) $registro['id_atividade'])) continue;
            $resultado = $this->getResultadosByUsuario($idUsuario)[(int) $registro['id_atividade']] ?? null;
            if (!$resultado || $resultado->getEstado() !== 'CONCLUIDO') return Atividade::arrayParaObjeto($registro);
        }
        return null;
    }

    public function registrarResultado(
        int $idUsuario,
        int $idAtividade,
        int $pontuacao,
        int $estrelas,
        int $bolotas,
        float $tempoExecucao
    ): FuncionarioAtividade {
        $this->connection->beginTransaction();

        try {
            $idFuncionario = $this->getFuncionarioIdByUsuario($idUsuario);
            if ($idFuncionario === null) {
                throw new RuntimeException('Funcionário não encontrado para o usuário autenticado.');
            }

            $stm = $this->connection->prepare(
                'SELECT * FROM Funcionario_atividade
                 WHERE id_funcionario = :id_funcionario AND id_atividade = :id_atividade
                 ORDER BY id_funcionario_atividade LIMIT 1 FOR UPDATE'
            );
            $stm->execute([
                'id_funcionario' => $idFuncionario,
                'id_atividade' => $idAtividade
            ]);
            $anterior = $stm->fetch();

            $pontuacaoAnterior = (int) ($anterior['pontuacao_obtida'] ?? 0);
            $estrelasAnteriores = (int) ($anterior['estrelas_obtidas'] ?? 0);
            $bolotasAnteriores = (int) ($anterior['bolotas_obtidas'] ?? 0);
            $jaConcluida = ($anterior['estado'] ?? '') === 'CONCLUIDO';
            $melhorPontuacao = max($pontuacaoAnterior, $pontuacao);
            $melhoresEstrelas = max($estrelasAnteriores, $estrelas);
            $melhoresBolotas = $jaConcluida ? $bolotasAnteriores : max($bolotasAnteriores, $bolotas);
            $concluida = $melhorPontuacao >= 66 || $jaConcluida;
            $estado = $concluida ? 'CONCLUIDO' : 'INICIADO';

            if ($anterior) {
                $sql = 'UPDATE Funcionario_atividade
                        SET pontuacao_obtida = :pontuacao,
                            estrelas_obtidas = :estrelas,
                            bolotas_obtidas = :bolotas,
                            estado = :estado,
                            qtd_tentativas = qtd_tentativas + 1,
                            tempo_execucao = :tempo
                        WHERE id_funcionario_atividade = :id';
                $stm = $this->connection->prepare($sql);
                $stm->execute([
                    'pontuacao' => $melhorPontuacao,
                    'estrelas' => $melhoresEstrelas,
                    'bolotas' => $melhoresBolotas,
                    'estado' => $estado,
                    'tempo' => $tempoExecucao,
                    'id' => $anterior['id_funcionario_atividade']
                ]);
                $idRegistro = (int) $anterior['id_funcionario_atividade'];
                $tentativas = (int) $anterior['qtd_tentativas'] + 1;
            } else {
                $sql = 'INSERT INTO Funcionario_atividade
                        (id_funcionario, id_atividade, estado, pontuacao_obtida, estrelas_obtidas, bolotas_obtidas, qtd_tentativas, tempo_execucao)
                        VALUES (:id_funcionario, :id_atividade, :estado, :pontuacao, :estrelas, :bolotas, 1, :tempo)';
                $stm = $this->connection->prepare($sql);
                $stm->execute([
                    'id_funcionario' => $idFuncionario,
                    'id_atividade' => $idAtividade,
                    'pontuacao' => $melhorPontuacao,
                    'estrelas' => $melhoresEstrelas,
                    'bolotas' => $melhoresBolotas,
                    'estado' => $estado,
                    'tempo' => $tempoExecucao
                ]);
                $idRegistro = (int) $this->connection->lastInsertId();
                $tentativas = 1;
            }

            $stm = $this->connection->prepare(
                'UPDATE Funcionarios
                 SET pontuacao_total = pontuacao_total + :pontos,
                     bolotas_totais = bolotas_totais + :bolotas
                 WHERE id_funcionario = :id_funcionario'
            );
            $stm->execute([
                'pontos' => $melhorPontuacao - $pontuacaoAnterior,
                'bolotas' => $melhoresBolotas - $bolotasAnteriores,
                'id_funcionario' => $idFuncionario
            ]);

            $this->connection->commit();
            return FuncionarioAtividade::arrayParaObjeto([
                'id_funcionario_atividade' => $idRegistro,
                'id_funcionario' => $idFuncionario,
                'id_atividade' => $idAtividade,
                'pontuacao_obtida' => $melhorPontuacao,
                'estrelas_obtidas' => $melhoresEstrelas,
                'bolotas_obtidas' => $melhoresBolotas,
                'qtd_tentativas' => $tentativas,
                'tempo_execucao' => $tempoExecucao,
                'estado' => $estado
            ]);
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }
}
