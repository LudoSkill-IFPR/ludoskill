-- =========================================================================
-- SCRIPT DE RECRIÇÃO E DADOS DE TESTE (LUDOSKILL - APRESENTAÇÃO DE BANCA)
-- =========================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. LIMPEZA DOS DADOS ANTIGOS (Respeitando as chaves estrangeiras)
DELETE FROM `Funcionario_atividade`;
DELETE FROM `Inventarios`;
DELETE FROM `Exercicios`;
DELETE FROM `Atividades`;
DELETE FROM `Modulos`;
DELETE FROM `Itens`;
DELETE FROM `Funcionarios`;
DELETE FROM `Gestores`;
DELETE FROM `Administradores`;
DELETE FROM `Empresas`;
DELETE FROM `Usuarios` WHERE `email` IN (
    'admin.banca@ludoskill.com', 
    'gestor.banca@ludoskill.com', 
    'funcionario.banca@ludoskill.com'
);

-- =========================================================================
-- 2. RECRIÇÃO DOS ITENS DA LOJA
-- =========================================================================
INSERT INTO `Itens` (`id_item`, `estado`, `nome`, `tipo`, `preco`, `imagem`) VALUES
(1, b'1', 'Tema Escuro Neônio', 'TEMA', 15, 'assets/imagens/tema_escuro.png'),
(2, b'1', 'Tema Minimalista Clean', 'TEMA', 20, 'assets/imagens/tema_clean.png'),
(3, b'1', 'Esquilo de Terno Executivo', 'VISUAL_ESQUILOSO', 30, 'assets/imagens/esquilo_terno.png'),
(4, b'1', 'Esquilo com Óculos de Sol', 'VISUAL_ESQUILOSO', 25, 'assets/imagens/esquilo_oculos.png');

-- =========================================================================
-- 3. RECRIÇÃO DOS MÓDULOS
-- =========================================================================
INSERT INTO `Modulos` (`id_modulo`, `nome`, `descricao`, `min_estrelas_liberacao`, `estado`) VALUES
(1, 'Módulo 1: Fundamentos de Hardware', 'Introdução aos conceitos básicos de bancada, ferramentas e segurança ESD.', 0, 'ATIVO'),
(2, 'Módulo 2: Diagnóstico e Manutenção', 'Análise de falhas comuns em smartphones e procedimentos de troca de componentes.', 5, 'ATIVO'),
(3, 'Módulo 3: Simulações Avançadas', 'Treinamento prático simulado de reparo em placas e substituição de telas.', 10, 'ATIVO');

-- =========================================================================
-- 4. RECRIÇÃO DAS ATIVIDADES
-- =========================================================================
INSERT INTO `Atividades` (`id_atividade`, `id_modulo`, `tipo_atividade`, `nome`, `estado`, `ordem`, `tempo_limite`, `estrelas_recompensa`, `bolotas_recompensa`, `descricao`) VALUES
(1, 1, 'INTRODUCAO', 'Conceitos Iniciais de Bancada', 'ATIVO', 1, 180, 5, 10, '<p>Aprenda os conceitos fundamentais de organização e segurança na bancada de assistência técnica.</p>'),
(2, 2, 'REVISAO', 'Revisão de Diagnóstico de Bateria', 'ATIVO', 1, 240, 5, 15, '<p>Revisão teórica sobre identificação de falhas e desgaste em baterias de íon de lítio.</p>'),
(3, 3, 'SIMULACAO', 'Simulação Prática de Troca de Tela', 'ATIVO', 1, 300, 10, 20, '<p>Simule o processo completo de substituição do display frontal com segurança e eficiência.</p>');

-- =========================================================================
-- 5. RECRIÇÃO DOS EXERCÍCIOS (JSON)
-- =========================================================================
INSERT INTO `Exercicios` (`id_exercicio`, `id_atividade`, `descricao`, `conteudo`) VALUES
(1, 1, 'Questionário: Conceitos de Bancada', '{\"tipo\":\"exercicio\",\"questoes\":[{\"enunciado\":\"Qual equipamento é essencial para evitar Descargas Eletrostáticas (ESD) na bancada?\",\"alternativas\":[\"A) Chave philips comum\",\"B) Pulseira antiestática e manta dissipativa\",\"C) Soprador térmico em alta potência\",\"D) Multímetro analógico sem calibração\"],\"alternativa_correta\":1,\"justificativa\":\"A pulseira antiestática descarrega a eletricidade estática do corpo do técnico, protegendo os componentes sensíveis.\"}]}'),
(2, 2, 'Questionário: Diagnóstico de Bateria', '{\"tipo\":\"exercicio\",\"questoes\":[{\"enunciado\":\"Qual é o principal indicativo visual de que uma bateria de íon de lítio precisa ser substituída imediatamente?\",\"alternativas\":[\"A) Adesivo traseiro levemente descolado por poeira\",\"B) Estufamento (swelling) visível na carcaça ou tampa traseira\",\"C) Pequenos riscos superficiais na película protetora\",\"D) Leve aquecimento normal durante carregamento rápido\"],\"alternativa_correta\":1,\"justificativa\":\"Baterias estufadas representam risco de curto-circuito e até princípio de incêndio, exigindo substituição imediata.\"}]}'),
(3, 3, 'Simulação de Troca de Tela', '{\"tipo\":\"simulacao\",\"peca_quebrada\":\"tela\"}');

-- =========================================================================
-- 6. CADASTRO DE EMPRESA E USUÁRIOS FICTÍCIOS PARA A BANCA
-- Senha padrão para todos: Banca@2026! (Hash Bcrypt gerado)
-- =========================================================================

-- Empresa Fictícia de Testes
INSERT INTO `Empresas` (`id_empresa`, `cnpj`, `nome`, `email`, `plano`) VALUES
(99, '12345678000199', 'Assistência Técnica Demo Banca', 'contato@demobanca.com', 'INTERMEDIARIO');

-- Usuário Administrador da Banca
INSERT INTO `Usuarios` (`id_usuario`, `nome_completo`, `data_nascimento`, `CPF`, `email`, `senha_hash`, `numero_telefone`, `estado`) VALUES
(901, 'Prof. Validador da Banca', '1980-05-15', '11122233344', 'admin.banca@ludoskill.com', '$2y$10$skw952doIA4kSBRkNE7ZsuaJTjTIC1RTKcYFS8Ijo99RfXrfkdF3y', '45999991111', 'ATIVO');

INSERT INTO `Administradores` (`id_administrador`, `id_usuario`) VALUES
(901, 901);

-- Usuário Gestor da Banca
INSERT INTO `Usuarios` (`id_usuario`, `nome_completo`, `data_nascimento`, `CPF`, `email`, `senha_hash`, `numero_telefone`, `estado`) VALUES
(902, 'Carlos Gestor Exemplo', '1985-08-20', '22233344455', 'gestor.banca@ludoskill.com', '$2y$10$skw952doIA4kSBRkNE7ZsuaJTjTIC1RTKcYFS8Ijo99RfXrfkdF3y', '45999992222', 'ATIVO');

INSERT INTO `Gestores` (`id_gestor`, `id_usuario`, `id_empresa`) VALUES
(902, 902, 99);

-- Usuário Funcionário da Banca
INSERT INTO `Usuarios` (`id_usuario`, `nome_completo`, `data_nascimento`, `CPF`, `email`, `senha_hash`, `numero_telefone`, `estado`) VALUES
(903, 'Ana Funcionária Teste', '1998-12-10', '33344455666', 'funcionario.banca@ludoskill.com', '$2y$10$skw952doIA4kSBRkNE7ZsuaJTjTIC1RTKcYFS8Ijo99RfXrfkdF3y', '45999993333', 'ATIVO');

INSERT INTO `Funcionarios` (`id_funcionario`, `id_usuario`, `id_empresa`, `bolotas_totais`, `pontuacao_total`, `nivel`) VALUES
(903, 903, 99, 15, 50, 1);

COMMIT;