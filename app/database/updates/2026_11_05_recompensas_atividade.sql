ALTER TABLE Atividades
    ADD COLUMN IF NOT EXISTS estrelas_recompensa INT NOT NULL DEFAULT 3 AFTER tempo_limite,
    ADD COLUMN IF NOT EXISTS bolotas_recompensa INT NOT NULL DEFAULT 10 AFTER estrelas_recompensa;
