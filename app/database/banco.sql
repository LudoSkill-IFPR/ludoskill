
-- -----------------------------------------------------------------------------
-- 1. TABELA DE USUÁRIOS (Tabela Base)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    CPF VARCHAR(11) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    numero_telefone VARCHAR(20)
);

-- -----------------------------------------------------------------------------
-- 2. TABELA DE EMPRESAS
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Empresas (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    CNPJ VARCHAR(14) NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    plano ENUM('BASICO', 'INTERMEDIARIO', 'AVANCADO', 'CORPORATIVO') NOT NULL DEFAULT 'BASICO'
);

-- -----------------------------------------------------------------------------
-- 3. TABELAS DE ESPECIALIZAÇÃO DE USUÁRIOS
-- -----------------------------------------------------------------------------

-- Administradores
CREATE TABLE IF NOT EXISTS Administradores (
    id_administrador INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    CONSTRAINT fk_admin_usuario FOREIGN KEY (id_usuario) 
        REFERENCES Usuarios(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Gestores
CREATE TABLE IF NOT EXISTS Gestores (
    id_gestor INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    id_empresa INT NOT NULL,
    CONSTRAINT fk_gestor_usuario FOREIGN KEY (id_usuario) 
        REFERENCES Usuarios(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_gestor_empresa FOREIGN KEY (id_empresa) 
        REFERENCES Empresas(id_empresa) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Funcionários
CREATE TABLE IF NOT EXISTS Funcionarios (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    id_empresa INT NOT NULL,
    bolotas_totais INT NOT NULL DEFAULT 0,
    pontuacao_total INT NOT NULL DEFAULT 0,
    nivel INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_funcionario_usuario FOREIGN KEY (id_usuario) 
        REFERENCES Usuarios(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_funcionario_empresa FOREIGN KEY (id_empresa) 
        REFERENCES Empresas(id_empresa) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- -----------------------------------------------------------------------------
-- 4. ESTRUTURA DO CURSO (Módulos, Atividades, Exercícios)
-- -----------------------------------------------------------------------------

-- Módulos
CREATE TABLE IF NOT EXISTS Modulos (
    id_modulo INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao VARCHAR(255),
    min_estrelas_liberacao INT NOT NULL DEFAULT 0
);

-- Atividades
CREATE TABLE IF NOT EXISTS Atividades (
    id_atividade INT AUTO_INCREMENT PRIMARY KEY,
    id_modulo INT NOT NULL,
    tipo_atividade ENUM('INTRODUCAO', 'REVISAO', 'SIMULACAO') NOT NULL,
    nome VARCHAR(255) NOT NULL,
    estado ENUM('PENDENTE', 'INICIADO', 'CONCLUIDO') NOT NULL DEFAULT 'PENDENTE',
    pontuacao INT NOT NULL DEFAULT 0,
    estrelas INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_atividade_modulo FOREIGN KEY (id_modulo) 
        REFERENCES Modulos(id_modulo) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Exercícios
CREATE TABLE IF NOT EXISTS Exercicios (
    id_exercicio INT AUTO_INCREMENT PRIMARY KEY,
    id_atividade INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    conteudo JSON NOT NULL, -- Armazena perguntas, alternativas e justificativa da resposta
    CONSTRAINT fk_exercicio_atividade FOREIGN KEY (id_atividade) 
        REFERENCES Atividades(id_atividade) ON DELETE CASCADE ON UPDATE CASCADE
);

-- -----------------------------------------------------------------------------
-- 5. GAMIFICAÇÃO E DESEMPENHO (Execução de atividades)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Funcionario_atividade (
    id_funcionario_atividade INT AUTO_INCREMENT PRIMARY KEY,
    id_funcionario INT NOT NULL,
    id_atividade INT NOT NULL,
    pontuacao_obtida INT DEFAULT 0,
    estrelas_obtidas INT DEFAULT 0,
    bolotas_obtidas INT DEFAULT 0,
    qtd_tentativas INT DEFAULT 1,
    tempo_execucao DECIMAL(10,4) DEFAULT 0.0000,
    CONSTRAINT fk_fa_funcionario FOREIGN KEY (id_funcionario) 
        REFERENCES Funcionarios(id_funcionario) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_fa_atividade FOREIGN KEY (id_atividade) 
        REFERENCES Atividades(id_atividade) ON DELETE CASCADE ON UPDATE CASCADE
);

-- -----------------------------------------------------------------------------
-- 6. LOJA E INVENTÁRIO
-- -----------------------------------------------------------------------------

-- Itens da Loja
CREATE TABLE IF NOT EXISTS Itens (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    estado BIT(1) NOT NULL DEFAULT b'1', -- 1 para Ativo, 0 para Inativo
    nome VARCHAR(255) NOT NULL,
    tipo ENUM('TEMA', 'VISUAL_ESQUILOSO') NOT NULL,
    preco INT NOT NULL,
    imagem VARCHAR(255)
);

-- Inventário dos Funcionários
CREATE TABLE IF NOT EXISTS Inventarios (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_funcionario INT NOT NULL,
    id_item INT NOT NULL,
    data_aquisicao DATE NOT NULL,
    em_uso BIT(1) NOT NULL DEFAULT b'0', -- Controla se o item/tema está ativado no momento | 1 para Ativo, 0 para Inativo
    CONSTRAINT fk_inventario_funcionario FOREIGN KEY (id_funcionario) 
        REFERENCES Funcionarios(id_funcionario) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inventario_item FOREIGN KEY (id_item) 
        REFERENCES Itens(id_item) ON DELETE CASCADE ON UPDATE CASCADE
);