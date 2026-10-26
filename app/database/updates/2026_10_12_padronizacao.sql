-- Execute uma vez, com backup, em bancos criados por versões anteriores.

ALTER TABLE Modulos
    ADD COLUMN IF NOT EXISTS estado ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO';

ALTER TABLE Atividades
    MODIFY estado VARCHAR(20) NOT NULL DEFAULT 'ATIVO';
UPDATE Atividades SET estado = 'ATIVO' WHERE estado NOT IN ('ATIVO', 'INATIVO');
ALTER TABLE Atividades
    MODIFY estado ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    ADD COLUMN IF NOT EXISTS ordem INT NOT NULL DEFAULT 1,
    ADD COLUMN IF NOT EXISTS tempo_limite INT NOT NULL DEFAULT 180,
    DROP COLUMN IF EXISTS pontuacao,
    DROP COLUMN IF EXISTS estrelas;

ALTER TABLE Funcionario_atividade
    ADD COLUMN IF NOT EXISTS estado ENUM('PENDENTE', 'INICIADO', 'CONCLUIDO') NOT NULL DEFAULT 'PENDENTE',
    MODIFY qtd_tentativas INT DEFAULT 0;

CREATE TEMPORARY TABLE fa_consolidada AS
SELECT MAX(id_funcionario_atividade) AS id_manter, id_funcionario, id_atividade,
       MAX(pontuacao_obtida) AS pontuacao, MAX(estrelas_obtidas) AS estrelas,
       MAX(bolotas_obtidas) AS bolotas, SUM(qtd_tentativas) AS tentativas,
       MIN(tempo_execucao) AS tempo,
       IF(SUM(estado = 'CONCLUIDO') > 0, 'CONCLUIDO', IF(SUM(estado = 'INICIADO') > 0, 'INICIADO', 'PENDENTE')) AS estado_final
FROM Funcionario_atividade
GROUP BY id_funcionario, id_atividade;

UPDATE Funcionario_atividade fa
INNER JOIN fa_consolidada c ON c.id_manter = fa.id_funcionario_atividade
SET fa.pontuacao_obtida = c.pontuacao, fa.estrelas_obtidas = c.estrelas,
    fa.bolotas_obtidas = c.bolotas, fa.qtd_tentativas = c.tentativas,
    fa.tempo_execucao = c.tempo, fa.estado = c.estado_final;

DELETE fa FROM Funcionario_atividade fa
INNER JOIN fa_consolidada c ON c.id_funcionario = fa.id_funcionario AND c.id_atividade = fa.id_atividade
WHERE fa.id_funcionario_atividade <> c.id_manter;
DROP TEMPORARY TABLE fa_consolidada;

ALTER TABLE Funcionario_atividade
    ADD CONSTRAINT uq_funcionario_atividade UNIQUE (id_funcionario, id_atividade);

DELETE i1 FROM Inventarios i1
INNER JOIN Inventarios i2 ON i1.id_funcionario = i2.id_funcionario
    AND i1.id_item = i2.id_item AND i1.id_inventario > i2.id_inventario;
ALTER TABLE Inventarios
    ADD CONSTRAINT uq_inventario_item UNIQUE (id_funcionario, id_item);

ALTER TABLE Inventarios DROP FOREIGN KEY fk_inventario_item;
ALTER TABLE Inventarios ADD CONSTRAINT fk_inventario_item FOREIGN KEY (id_item)
    REFERENCES Itens(id_item) ON DELETE RESTRICT ON UPDATE CASCADE;
