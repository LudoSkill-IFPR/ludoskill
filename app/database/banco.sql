CREATE DATABASE IF NOT EXISTS ludoskill;
USE ludoskill;

-- ===========================
-- USUÁRIO
-- ===========================
CREATE TABLE IF NOT EXISTS Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    cpf CHAR(11) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha CHAR(60) NOT NULL,
    numero_telefone CHAR(20) NOT NULL UNIQUE,
    perfil CHAR(1) NOT NULL,
    CONSTRAINT check_perfil
        CHECK (perfil IN ('F','G','A'))
) ENGINE=InnoDB;

-- ===========================
-- EMPRESA
-- ===========================
CREATE TABLE IF NOT EXISTS Empresa (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cnpj CHAR(14) NOT NULL UNIQUE,
    numero_funcionarios INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    plano CHAR(1) NOT NULL,
    CONSTRAINT check_plano
        CHECK (plano IN ('B','P'))
) ENGINE=InnoDB;

-- ===========================
-- MÓDULOS
-- ===========================
CREATE TABLE IF NOT EXISTS Modulo (
    id_modulo INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    min_estrelas_liberacao INT NOT NULL
) ENGINE=InnoDB;

-- ===========================
-- FASES
-- ===========================
CREATE TABLE IF NOT EXISTS Fase (
    id_fase INT AUTO_INCREMENT PRIMARY KEY,
    id_modulo INT NOT NULL,
    tipo_fase CHAR(1) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    tarefa JSON NOT NULL,

    CONSTRAINT fk_fase_modulo
        FOREIGN KEY (id_modulo)
        REFERENCES Modulo(id_modulo),

    CONSTRAINT check_tipo_fase
        CHECK (tipo_fase IN ('I','S','R'))
) ENGINE=InnoDB;

-- ===========================
-- FUNCIONÁRIOS
-- ===========================
CREATE TABLE IF NOT EXISTS Funcionario (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    bolotas INT NOT NULL DEFAULT 0,
    desempenho INT NOT NULL DEFAULT 0,

    CONSTRAINT fk_funcionario_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES Usuario(id_usuario)
) ENGINE=InnoDB;

-- ===========================
-- GESTORES
-- ===========================
CREATE TABLE IF NOT EXISTS Gestor (
    id_gestor INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_empresa INT NOT NULL,

    CONSTRAINT fk_gestor_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES Usuario(id_usuario),

    CONSTRAINT fk_gestor_empresa
        FOREIGN KEY (id_empresa)
        REFERENCES Empresa(id_empresa)
) ENGINE=InnoDB;

-- ===========================
-- ADMINISTRADORES
-- ===========================
CREATE TABLE IF NOT EXISTS Administrador (
    id_administrador INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,

    CONSTRAINT fk_admin_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES Usuario(id_usuario)
) ENGINE=InnoDB;

-- ===========================
-- INVENTÁRIO
-- ===========================
CREATE TABLE IF NOT EXISTS Inventario (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_funcionario INT NOT NULL UNIQUE,

    CONSTRAINT fk_inventario_funcionario
        FOREIGN KEY (id_funcionario)
        REFERENCES Funcionario(id_funcionario)
) ENGINE=InnoDB;

-- ===========================
-- ITENS
-- ===========================
CREATE TABLE IF NOT EXISTS Item (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo CHAR(1) NOT NULL,
    estado BOOLEAN NOT NULL DEFAULT TRUE,
    preco INT NOT NULL,
    imagem VARCHAR(255) NOT NULL,

    CONSTRAINT check_tipo_item
        CHECK (tipo IN ('B','P'))
) ENGINE=InnoDB;

-- ===========================
-- ITENS DO INVENTÁRIO
-- ===========================
CREATE TABLE IF NOT EXISTS Inventario_Item (
    id_inventario INT NOT NULL,
    id_item INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,

    PRIMARY KEY (id_inventario, id_item),

    CONSTRAINT fk_inv_item_inventario
        FOREIGN KEY (id_inventario)
        REFERENCES Inventario(id_inventario),

    CONSTRAINT fk_inv_item_item
        FOREIGN KEY (id_item)
        REFERENCES Item(id_item)
) ENGINE=InnoDB;

-- ===========================
-- PROGRESSO
-- ===========================
CREATE TABLE IF NOT EXISTS Progresso (
    id_progresso INT AUTO_INCREMENT PRIMARY KEY,
    id_funcionario INT NOT NULL,
    id_modulo INT NOT NULL,
    id_fase INT NOT NULL,

    estado BOOLEAN NOT NULL DEFAULT FALSE,
    estrelas_obtidas INT NOT NULL DEFAULT 0,
    estrelas_totais INT NOT NULL DEFAULT 0,

    CONSTRAINT fk_progresso_funcionario
        FOREIGN KEY (id_funcionario)
        REFERENCES Funcionario(id_funcionario),

    CONSTRAINT fk_progresso_modulo
        FOREIGN KEY (id_modulo)
        REFERENCES Modulo(id_modulo),

    CONSTRAINT fk_progresso_fase
        FOREIGN KEY (id_fase)
        REFERENCES Fase(id_fase)
) ENGINE=InnoDB;