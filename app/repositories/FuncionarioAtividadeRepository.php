<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use PDO;
use RuntimeException;

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
            $resultados[(int) $resultado['id_atividade']] = $resultado;
        }
        return $resultados;
    }

    public function registrarResultado(
        int $idUsuario,
        int $idAtividade,
        int $pontuacao,
        int $estrelas,
        int $bolotas,
        float $tempoExecucao
    ): array {
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
            $melhorPontuacao = max($pontuacaoAnterior, $pontuacao);
            $melhoresEstrelas = max($estrelasAnteriores, $estrelas);
            $melhoresBolotas = max($bolotasAnteriores, $bolotas);

            if ($anterior) {
                $sql = 'UPDATE Funcionario_atividade
                        SET pontuacao_obtida = :pontuacao,
                            estrelas_obtidas = :estrelas,
                            bolotas_obtidas = :bolotas,
                            qtd_tentativas = qtd_tentativas + 1,
                            tempo_execucao = :tempo
                        WHERE id_funcionario_atividade = :id';
                $stm = $this->connection->prepare($sql);
                $stm->execute([
                    'pontuacao' => $melhorPontuacao,
                    'estrelas' => $melhoresEstrelas,
                    'bolotas' => $melhoresBolotas,
                    'tempo' => $tempoExecucao,
                    'id' => $anterior['id_funcionario_atividade']
                ]);
                $idRegistro = (int) $anterior['id_funcionario_atividade'];
                $tentativas = (int) $anterior['qtd_tentativas'] + 1;
            } else {
                $sql = 'INSERT INTO Funcionario_atividade
                        (id_funcionario, id_atividade, pontuacao_obtida, estrelas_obtidas, bolotas_obtidas, qtd_tentativas, tempo_execucao)
                        VALUES (:id_funcionario, :id_atividade, :pontuacao, :estrelas, :bolotas, 1, :tempo)';
                $stm = $this->connection->prepare($sql);
                $stm->execute([
                    'id_funcionario' => $idFuncionario,
                    'id_atividade' => $idAtividade,
                    'pontuacao' => $melhorPontuacao,
                    'estrelas' => $melhoresEstrelas,
                    'bolotas' => $melhoresBolotas,
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
            return [
                'id_funcionario_atividade' => $idRegistro,
                'pontuacao_obtida' => $melhorPontuacao,
                'estrelas_obtidas' => $melhoresEstrelas,
                'bolotas_obtidas' => $melhoresBolotas,
                'qtd_tentativas' => $tentativas,
                'tempo_execucao' => $tempoExecucao
            ];
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }
}
