CREATE TABLE IF NOT EXISTS Solicitacoes_acesso (
    id_solicitacao INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    CPF VARCHAR(11) NOT NULL,
    email VARCHAR(255) NOT NULL,
    numero_telefone VARCHAR(20) NOT NULL,
    empresa_nome VARCHAR(255) NOT NULL,
    empresa_cnpj VARCHAR(14) NOT NULL,
    empresa_email VARCHAR(255) NOT NULL,
    mensagem VARCHAR(1000),
    estado ENUM('PENDENTE', 'APROVADA', 'RECUSADA') NOT NULL DEFAULT 'PENDENTE',
    id_gestor INT NULL,
    data_solicitacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_decisao DATETIME NULL,
    email_enviado BIT(1) NOT NULL DEFAULT b'0',
    INDEX idx_solicitacao_estado (estado),
    INDEX idx_solicitacao_email (email),
    CONSTRAINT fk_solicitacao_gestor FOREIGN KEY (id_gestor)
        REFERENCES Gestores(id_gestor) ON DELETE SET NULL ON UPDATE CASCADE
);
