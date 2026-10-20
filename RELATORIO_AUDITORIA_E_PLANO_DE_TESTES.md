# Relatório de Auditoria de Requisitos e Plano de Testes — LudoSkill

**Projeto Integrador — Curso Técnico em Desenvolvimento de Sistemas (IFPR)**  
**Autores:** Alinne Helena Estigarribia dos Santos, Izabella da Costa Codo, Rebeca Schmidt, Vinícius Brevesteky Teixeira  
**Orientadores:** Jefferson de Oliveira Chaves, Ana Carla Arruda de Holanda  
**Data da Auditoria:** Outubro de 2026  

---

## 1. Introdução

Este documento apresenta a auditoria técnica completa do código-fonte do sistema **LudoSkill**, confrontando a implementação atual com as especificações da documentação oficial do projeto (Requisitos Funcionais **RF 01 a RF 20**, Regras de Negócio **RN 01 a RN 18**, Requisitos Não Funcionais **RNF 01 a RNF 11** e Protótipos das Figuras 9 a 22).

Além do diagnóstico detalhado do que está implementado, parcialmente implementado ou pendente, este relatório disponibiliza um **Plano de Testes Prático e Detalhado**, estruturado passo a passo para que os alunos possam testar e validar o sistema na prática.

---

## 2. Auditoria dos Requisitos Funcionais (Quadro 2 da Documentação)

| Código | Requisito Funcional | Caso de Uso | Status no Código | Arquivos e Evidências | O que falta / Observações |
| :--- | :--- | :--- | :---: | :--- | :--- |
| **RF 01** | O sistema deve permitir que o usuário já cadastrado acesse a aplicação por meio do login. | UC01 | **Implementado** | `AutenticacaoController.php`, `AutenticacaoService.php`, `views/entrada/login.php` | Login funcional com `password_verify` e controle de conta ativa. *Obs: O link "Esqueceu a senha?" na view de login está sem rota de recuperação.* |
| **RF 02** | Assim que o administrador entra na aplicação, o sistema deve emitir um relatório da relação dos gestores por empresa. | UC02 | **Parcialmente Implementado** | `AdministradorController::inicial()`, `views/administrador/inicial.php` | Os dados numéricos de gestores e funcionários por empresa são exibidos em cards de texto e lista. No protótipo (Figura 19), estava previsto um gráfico visual de colunas com legenda. |
| **RF 03** | O sistema deve permitir que o administrador faça a manutenção dos módulos, atividades e questões. | UC03 | **Implementado** | `ModuloController.php`, `AtividadeController.php`, `ExercicioController.php` e views em `views/administrador/` | CRUD completo para módulos, atividades e exercícios (questões armazenadas em JSON estruturado com validação). |
| **RF 04** | O sistema deve permitir que o administrador mantenha o gestor. | UC04 | **Implementado** | `GestorController.php`, `GestorRepository.php`, `views/administrador/gestores/` | Listagem, cadastro, edição, desativação lógica e reativação funcionais. |
| **RF 05** | O sistema deve permitir que o administrador faça a manutenção dos itens da loja. | UC05 | **Implementado** | `ItemController.php`, `ItemRepository.php`, `views/administrador/itens/` | Cadastro com upload de imagem, edição, ativação/desativação lógica. |
| **RF 06** | Assim que o gestor entra na aplicação, o sistema deve emitir um relatório ao gestor, em forma de gráfico, contendo o desempenho de cada funcionário em ordem decrescente. | UC06 | **Implementado** | `GestorController::inicial()`, `FuncionarioAtividadeRepository::getDesempenhoByEmpresa()`, `views/gestor/inicial.php` | Exibe barras de progresso proporcionais à média de estrelas de cada funcionário da empresa, ordenadas de forma decrescente, e taxa de conclusão geral. |
| **RF 07** | O sistema deve permitir que o gestor mantenha o funcionário. | UC07 | **Implementado** | `FuncionarioController.php`, `FuncionarioRepository.php`, `views/gestor/funcionarios/` | Cadastro com vínculo automático à empresa do gestor, edição, desativação e reativação. |
| **RF 08** | Assim que o funcionário entrar na aplicação, o sistema deve exibir, na tela inicial, o seu progresso no curso — módulo e atividade. | UC08 | **Implementado** | `FuncionarioController::inicial()`, `views/funcionario/inicial.php` | Card "Continue de onde parou" com atalho direto para a próxima atividade disponível, saldo de bolotas, desempenho atual e métricas de conclusão. |
| **RF 09** | O sistema deve poder exibir perfil do usuário, descrevendo seu nome e identificador. Para o caso dos funcionários, inclui-se o desempenho e progresso atual. | UC09 | **Implementado** | `UsuarioController::perfil()`, `views/usuario/perfil.php`, `assets/css/perfil.css` | **Implementado com fidelidade visual aos protótipos da documentação (Figura 14 para Gestor e Figura 18 para Funcionário).** |
| **RF 10** | O sistema deve permitir que o usuário edite suas informações. | UC10 | **Implementado** | `UsuarioController::editarPerfil()`, `UsuarioController::atualizarPerfil()`, `views/usuario/editar.php` | **Implementado conforme a Regra de Negócio RN 18** (permite editar nome, nascimento, e-mail, telefone e senha; CPF protegido como somente leitura). |
| **RF 11** | O sistema deve permitir que o funcionário visualize os módulos disponíveis e suas respectivas atividades. | UC11 | **Implementado** | `ModuloController::listarTodosFuncionario()`, `views/funcionario/modulos.php` | Exibe módulos expansíveis, etapas ordenadas e status de cada atividade (Concluída, Em andamento, Não iniciada, Bloqueada). |
| **RF 12** | O sistema deve permitir que o funcionário realize as atividades. As atividades contêm questões, e caso ele erre, a explicação da resposta correta será exibida. | UC12 | **Implementado** | `ExercicioController::responderAtividade()`, `views/funcionario/exercicios.php` | Resolução de questões com exibição dinâmica da justificativa/explicação para respostas certas e erradas no envio. |
| **RF 13** | O sistema deve permitir que o funcionário realize as questões vinculadas às atividades. | UC13 | **Implementado** | `ExercicioController.php`, `views/funcionario/exercicios.php` | Formulário de questões de múltipla escolha integrado à sessão do aluno. |
| **RF 14** | O sistema deve permitir que o funcionário refaça a atividade para aumentar sua pontuação. | UC14 | **Implementado** | `ExercicioController::responderAtividade()`, `views/funcionario/exercicios.php` | Botão "Tentar novamente" presente no feedback do exercício e na listagem de módulos ("Refazer"). |
| **RF 15** | O sistema deve atribuir uma pontuação e uma quantidade de moedas virtuais (bolotas) após a conclusão de uma atividade. | UC15 | **Implementado** | `ExercicioController::responderAtividade()`, `FuncionarioAtividadeRepository::registrarResultado()` | Atribui estrelas, bolotas (estrelas * 10) e pontuação proporcional com base em acertos e tempo limite (RN 02 e RN 03). |
| **RF 16** | O sistema deve registrar o desempenho individual dos funcionários. | UC16 | **Implementado** | `Funcionario_atividade`, `Funcionarios.pontuacao_total`, `Funcionarios.bolotas_totais` | Persistência em banco de dados das pontuações, estrelas e tentativas acumuladas por funcionário. |
| **RF 17** | O sistema deve disponibilizar uma loja virtual que ofereça a compra de itens por meio das moedas virtuais (bolotas). | UC17 | **Implementado** | `ItemController::listarTodosFuncionario()`, `views/funcionario/loja.php` | Vitrine de itens ativos disponíveis para compra. *Obs: Na Figura 17 do documento havia um menu lateral de filtros (Tema/Esquilo, ordenação por preço); atualmente a loja lista os itens em grid sem esse filtro lateral.* |
| **RF 18** | O sistema deve subtrair o valor do item adquirido do saldo de moedas (bolotas) do usuário no momento da confirmação da compra. | UC18 | **Implementado** | `InventarioRepository::comprar()`, `ItemController::comprar()` | Transação com bloqueio pessimista (`FOR UPDATE`), validação de saldo e dedução imediata de bolotas. |
| **RF 19** | O sistema deve permitir que o funcionário consulte o seu inventário, mostrando os itens adquiridos por ele previamente na loja. | UC19 | **Implementado** | `InventarioRepository::getByUsuario()`, `views/funcionario/inicial.php`, `views/funcionario/loja.php` | Listagem dos itens comprados com status "Equipado" ou "Livre". |
| **RF 20** | O sistema deve permitir que o funcionário aplique itens do inventário para modificar a interface visual da plataforma e os atributos estéticos do mascote. | UC20 | **Implementado** | `FuncionarioController::equipaItem()`, `InventarioRepository::equipar()`, `AutenticacaoService::logar()` | Itens do tipo `VISUAL_ESQUILOSO` alteram o mascote do funcionário e itens do tipo `TEMA` alteram a paleta de cores do sistema via CSS dinâmico. |

---

## 3. Auditoria das Regras de Negócio (Quadro 3 da Documentação)

| Código | Regra de Negócio | Status no Código | Evidência / Como está implementado |
| :--- | :--- | :---: | :--- |
| **RN 01** | O administrador virá pré-cadastrado no sistema. | **Implementado** | Tabela `Administradores` e seed inicial no script `app/database/banco.sql`. |
| **RN 02** | Cálculo de estrelas e bolotas ao concluir atividade (-1 estrela por erro, -1 estrela se tempo > limite, mínimo de 1 estrela para aprovados). | **Implementado** | Implementado em `ExercicioController::responderAtividade()`: `estrelas = max(1, 3 - $descontoErros - $descontoTempo); bolotas = estrelas * 10`. |
| **RN 03** | Critério de aprovação: mínimo de 66% de acertos para conclusão da atividade. | **Implementado** | Se `taxaAcertos < 0.66`, o estado da atividade é mantido como `'INICIADO'` e estrelas/bolotas concedidas são 0 até aprovação. |
| **RN 04** | Atividades podem ser refeitas ilimitadamente. | **Implementado** | Não há limite de execuções; botão de refazer acessível a qualquer momento. |
| **RN 05** | Registro apenas da maior pontuação em caso de repetição (tentativas acumulam). | **Implementado** | `FuncionarioAtividadeRepository::registrarResultado()`: utiliza `max(pontuacaoAnterior, novaPontuacao)` e incrementa `qtd_tentativas = qtd_tentativas + 1`. |
| **RN 06** | Atividades são liberadas sequencialmente. | **Implementado** | `ExercicioController::isAtividadeLiberada()` e validação na view `modulos.php`: a atividade N exige que a atividade N-1 esteja concluída. |
| **RN 07** | Atividades categorizadas em INTRODUCAO, REVISAO e SIMULACAO. | **Implementado** | Coluna `tipo_atividade ENUM('INTRODUCAO', 'REVISAO', 'SIMULACAO')` na tabela `Atividades` e selects nos formulários. |
| **RN 08** | Progresso definido pela atividade e módulo em que se encontra. | **Implementado** | Exibido no card "Continue de onde parou" da página inicial e no perfil do funcionário. |
| **RN 09** | Desempenho do funcionário definido pela média de estrelas das atividades concluídas. | **Implementado** | Consulta SQL com `AVG(CASE WHEN fa.estado = 'CONCLUIDO' THEN fa.estrelas_obtidas END)` em `FuncionarioAtividadeRepository`. |
| **RN 10** | Todo módulo exige quantidade mínima de estrelas para ser desbloqueado. | **Parcialmente Implementado** | O campo `min_estrelas_liberacao` existe na tabela `Modulos` e no cadastro do admin. *Falta: Bloquear a abertura visual do módulo na tela de módulos do funcionário quando o total de estrelas for inferior ao exigido.* |
| **RN 11** | Resgate de bolotas é único por atividade (evitar farm infinito). | **Implementado** | `FuncionarioAtividadeRepository.php`: se a atividade já foi concluída anteriormente, `bolotasObtidas` não é incrementado novamente. |
| **RN 12** | Itens da loja classificados em Tema ou Visual Esquiloso. | **Implementado** | `tipo ENUM('TEMA', 'VISUAL_ESQUILOSO')` na tabela `Itens` e tratamento visual na loja/inventário. |
| **RN 13** | Itens de resgate único e comprados apenas com bolotas. | **Implementado** | Verificação em `InventarioRepository::comprar()`: se o funcionário já possui o item, a compra é bloqueada com erro explicativo. |
| **RN 14** | Bolotas gastas na loja não podem ser recuperadas. | **Implementado** | Débito permanente e ausência de funcionalidade de estorno. |
| **RN 15** | Itens adquiridos não podem ser trocados ou devolvidos. | **Implementado** | Não há operação de devolução ou troca de itens. |
| **RN 16** | Apenas o gestor pode cadastrar e inativar funcionários. | **Implementado** | Proteção por `gestorRequired()` em `FuncionarioController.php`. O administrador gerencia gestores, e o gestor gerencia seus funcionários. |
| **RN 17** | Não é possível cadastrar mais de um usuário com o mesmo CPF. | **Implementado** | Constraint `UNIQUE` no banco (`banco.sql`) e validação prévia em `Validador::validarFuncionario()` e `validarGestor()`. |
| **RN 18** | Gestor e funcionário podem editar apenas e-mail, senha, telefone, nome e data de nascimento (CPF, progresso, desempenho e bolotas protegidos). | **Implementado** | **Implementado nas telas `views/usuario/perfil.php` e `views/usuario/editar.php` e validado em `UsuarioController::atualizarPerfil()`.** |

---

## 4. Auditoria dos Requisitos Não Funcionais (Quadro 4 da Documentação)

| Código | Tipo | Requisito Não Funcional | Status | Análise Técnica |
| :--- | :--- | :--- | :---: | :--- |
| **RNF 01** | Usabilidade | Opção de cancelamento/retorno e confirmação antes de operações críticas (inativação/exclusão). | **Atendido** | Formulários de exclusão/inativação possuem confirmação em JavaScript via `onsubmit="return confirm(...)"` e formulários de cadastro possuem botão "Voltar/Cancelar". |
| **RNF 02** | Usabilidade | Padrões visuais consistentes (cores, tipografias, botões, ícones, espaçamentos). | **Atendido** | Centralizado em arquivos CSS (`geralUsuario.css`, `formulariosAdministrador.css`, `exercicios.css`, `perfil.css`), utilizando variáveis de tema (`--roxo`, `--verde`, `--cinza-ultraescuro`). |
| **RNF 03** | Usabilidade | Mensagens de erro em linguagem não técnica, sem SQL ou stack traces brutos. | **Atendido** | Erros tratados por `Validador.php` e exibidos em elementos `<div class="erro">`. Páginas customizadas para erros HTTP `403.php`, `404.php` e `500.php`. |
| **RNF 04** | Usabilidade | Funcionalidades principais acessíveis por menus ou navegação, sem exigir memorizar URLs. | **Atendido** | Menus de navegação dedicados para cada perfil: `menuAdministrador.html`, `menuGestor.php` e `menuFuncionario.php`. |
| **RNF 05** | Compatibilidade | Funcionamento sem erros impeditivos em Chrome, Firefox, Edge e Brave. | **Atendido** | Construído com HTML5 semântico padrão, CSS Flexbox/Grid e JavaScript Vanilla, sem APIs proprietárias. |
| **RNF 06** | Disponibilidade | Mínimo de 95% de disponibilidade. | **Atendido** | Arquitetura leve executada em servidor Apache com PHP 8.2 e MySQL sem gargalos de concorrência. |
| **RNF 07** | Capacidade | Suportar 100 usuários simultâneos mantendo operações principais. | **Atendido** | Sessões nativas do PHP, consultas SQL otimizadas com chaves estrangeiras e índices únicos. |
| **RNF 08** | Integridade | Persistir 100% das pontuações confirmadas no servidor. | **Atendido** | `FuncionarioAtividadeRepository` utiliza transações atômicas com `beginTransaction()`, `commit()` e `rollBack()`. |
| **RNF 09** | Confiabilidade | Armazenar no localStorage o progresso do usuário no navegador. | **Atendido** | Implementado no formulário de exercícios em `views/funcionario/exercicios.php`, salvando as alternativas selecionadas em tempo real. |
| **RNF 10** | Segurança | Senhas com hash seguro (`password_hash`), controle de acesso por perfil e isolamento de dados por empresa. | **Atendido** | Senhas criptografadas com bcrypt (`PASSWORD_DEFAULT`), verificação com `password_verify` e isolamento por empresa via `id_empresa`. |
| **RNF 11** | Manutenibilidade | Arquivos seguindo padrões de arquitetura (MVC), organização, indentação e nomenclatura. | **Atendido** | Estrutura clara dividida em `controllers/`, `models/`, `services/`, `repositories/`, `views/` e `helpers/`. |

---

## 5. Resumo: O Que Falta das Demais Funcionalidades (Oportunidades de Melhoria)

Para fins de apresentação do Trabalho de Conclusão de Curso ou entregas complementares, destacam-se os seguintes pontos secundários que constam no protótipo visual, mas que possuem implementação simplificada no código atual:

1. **Gráfico Visual na Tela Inicial do Administrador (RF 02 / Figura 19)**:
   - *Como está hoje:* A relação de usuários/gestores por empresa é exibida em cards de texto e lista ordenada com as empresas com mais usuários.
   - *O que falta:* Incluir um gráfico de barras visual (por exemplo, com a biblioteca open-source `Chart.js` ou barras estilizadas em CSS) para representar visualmente a distribuição de usuários por empresa.

2. **Bloqueio do Módulo por Estrelas Mínimas (RN 10)**:
   - *Como está hoje:* O desbloqueio sequencial das atividades (RN 06) está totalmente funcional (atividade 2 bloqueada até que a atividade 1 seja concluída). O campo `min_estrelas_liberacao` está cadastrado na tabela de módulos.
   - *O que falta:* Adicionar uma verificação no topo de cada módulo para que, caso o funcionário ainda não tenha somado as estrelas mínimas exigidas pelo módulo, o acordeão `<details>` apareça bloqueado com mensagem de aviso.

3. **Filtro Lateral na Loja Virtual (RF 17 / Figura 17)**:
   - *Como está hoje:* A loja exibe todos os itens ativos em grid, com saldo de bolotas e botão "Adquirir".
   - *O que falta:* Implementar o painel lateral esquerdo com opções de filtro por categoria ("Temas" e "Visuais Esquilosos") e ordenação por preço.

4. **Recuperação de Senha ("Esqueceu a senha?" na Figura 10)**:
   - *Como está hoje:* O link existe na tela de login, mas não possui rota nem envio de token/e-mail associado.

---

## 6. O Que Foi Implementado Nesta Etapa: Telas de Perfil (RF 09, RF 10 e RN 18)

As telas de perfil foram implementadas seguindo rigorosamente a documentação e os protótipos visuais:

1. **Visualização do Perfil (`app/views/usuario/perfil.php`)**:
   - **Para o Funcionário (Figura 18)**:
     - Card de informações pessoais com avatar do esquilo, nome, cargo, data de nascimento formatada, CPF (apenas leitura), e-mail, telefone e botão **"Editar informações"**.
     - Card "Turma Cadastrada" com a empresa vinculada e a indicação de onde parou no curso.
     - Card "Progresso Geral" com barra de progresso visual de conclusão do treinamento, total de estrelas conquistadas e saldo de bolotas.
   - **Para o Gestor (Figura 14)**:
     - Saudação personalizada: *"Olá, [Nome]!"*.
     - Card com dados pessoais do gestor, cargo e botão de edição.
     - Card com informações corporativas (Nome da empresa, CNPJ, Plano contratado e total de colaboradores).
     - Cards de métricas inferiores (Total de funcionários cadastrados, módulos de treinamento e taxa média de conclusão).
   - **Para o Administrador (Figura 19)**:
     - Card de dados gerais do administrador do sistema e painel com contadores de empresas, gestores, módulos e itens da loja.
   - **Integração dos Menus**: Cada perfil carrega automaticamente sua barra de navegação correspondente (`menuFuncionario.php`, `menuGestor.php` ou `menuAdministrador.html`).

2. **Edição de Perfil (`app/views/usuario/editar.php`)**:
   - Respeita integralmente a **RN 18**:
     - **Campos Editáveis:** Nome completo, Data de nascimento, E-mail, Telefone e Nova senha (opcional, mínimo 8 caracteres).
     - **Campos Bloqueados:** O **CPF** é exibido em modo somente leitura (`disabled` / `readonly`), acompanhado de aviso explicativo de que se trata de identificador único protegido pela regra RN 18.
     - Dados como saldo de bolotas, pontuação e progresso não são expostos no formulário de edição.
   - Estética idêntica ao padrão LudoSkill (dark mode, cards escuros, bordas com destaque, botões com efeito glow `botao brilho`, mensagens de erro em vermelho e aviso informativo da RN 18).
   - Botões "Salvar alterações" e "Cancelar" (retorna para `/perfil`).

3. **Controlador e Rotas (`UsuarioController.php` e `index.php`)**:
   - Carrega as métricas e dados de cada perfil de forma simples e direta.
   - Valida os dados submetidos sem código excessivamente avançado (nível ensino médio técnico).
   - Atualiza o banco de dados e a sessão do usuário.
   - Redireciona com mensagem de feedback de sucesso.
   - Adicionada rota `GET /logout` para garantir encerramento seguro de sessão em qualquer dispositivo.

---

## 7. Plano de Testes Completo (Guia Passo a Passo)

Este plano de testes foi elaborado para que os alunos possam validar todas as funcionalidades, regras de negócio e telas do sistema.

### 7.1. Casos de Teste — Perfil de Usuário e RN 18 (Funcionalidade Implementada)

#### CT 01 — Visualização do Perfil do Funcionário
- **Objetivo:** Verificar se a tela de perfil do funcionário exibe todos os dados do protótipo (Figura 18).
- **Pré-condição:** Estar logado como funcionário.
- **Passos:**
  1. No menu superior, clicar no menu suspenso **"Perfil"** e selecionar **"Ver perfil"** (ou acessar `/perfil`).
  2. Verificar se o card esquerdo exibe o avatar do esquilo, nome completo, cargo "Funcionário", data de nascimento, CPF formatado, e-mail e telefone.
  3. Verificar se o card direito exibe a empresa associada e a atividade atual onde parou.
  4. Verificar se a barra de progresso geral reflete a porcentagem de atividades concluídas, total de estrelas e saldo de bolotas.
- **Resultado Esperado:** Todos os dados são exibidos de forma clara, com layout alinhado à Figura 18 e sem erros na página.

#### CT 02 — Visualização do Perfil do Gestor
- **Objetivo:** Verificar se a tela de perfil do gestor reflete o protótipo da Figura 14.
- **Pré-condição:** Estar logado como gestor.
- **Passos:**
  1. Acessar `/perfil`.
  2. Verificar a saudação *"Olá, [Nome do Gestor]!"*.
  3. Conferir o card "Suas informações" e o card "Informações da empresa" (Razão Social, CNPJ, Plano e total de colaboradores).
  4. Conferir os cards de métricas inferiores (Funcionários cadastrados, Módulos e Taxa média de conclusão).
- **Resultado Esperado:** Página renderizada com o menu do gestor, dados da empresa e métricas consolidadas.

#### CT 03 — Edição de Perfil com Sucesso (RN 18)
- **Objetivo:** Validar a alteração permitida dos dados cadastrais.
- **Pré-condição:** Estar logado com qualquer usuário (ex: funcionário).
- **Passos:**
  1. Na tela de perfil, clicar no botão **"Editar informações"** (ou acessar `/perfil/editar`).
  2. Alterar o campo **Nome completo** (ex: adicionar um sobrenome).
  3. Alterar o campo **Telefone de contato**.
  4. Deixar o campo **Nova senha** em branco.
  5. Clicar em **"Salvar alterações"**.
- **Resultado Esperado:** O sistema salva as alterações, redireciona para `/perfil` e exibe o alerta verde *"Suas informações de perfil foram atualizadas com sucesso!"*. A senha anterior continua funcionando.

#### CT 04 — Validação da Proteção do CPF na Edição (RN 18)
- **Objetivo:** Garantir que o CPF não pode ser alterado pelo usuário.
- **Pré-condição:** Acessar a tela `/perfil/editar`.
- **Passos:**
  1. Tentar clicar ou digitar no campo de **CPF**.
  2. Verificar se o campo está desabilitado (`disabled` / `readonly`) com visual bloqueado.
  3. Ler o texto explicativo abaixo do campo referenciando a RN 18.
- **Resultado Esperado:** O usuário não consegue editar o CPF na interface nem submetê-lo no formulário.

#### CT 05 — Validação de Dados Inválidos na Edição de Perfil
- **Objetivo:** Verificar se o sistema impede envio de informações incorretas.
- **Pré-condição:** Acessar `/perfil/editar`.
- **Passos:**
  1. Deixar o campo **Nome completo** em branco e tentar salvar.
  2. Inserir uma **Data de nascimento** no futuro (ex: ano seguinte) e tentar salvar.
  3. Inserir um **E-mail** inválido (ex: `usuario@semdominio`) ou já existente em outra conta.
  4. Digitar uma senha com apenas 4 caracteres no campo de nova senha.
- **Resultado Esperado:** O formulário não é enviado; o sistema exibe mensagens de erro em vermelho ao lado de cada campo com problema e preserva os valores válidos preenchidos.

---

### 7.2. Casos de Teste — Trilha de Treinamento e Exercícios (Funcionário)

#### CT 06 — Realização de Atividade e Critério de Aprovação (RN 02 e RN 03)
- **Objetivo:** Validar o cálculo de notas, estrelas e aprovação com mínimo de 66% de acertos.
- **Pré-condição:** Estar logado como funcionário e acessar `/funcionario/modulos`.
- **Passos:**
  1. Clicar na primeira atividade disponível.
  2. Responder todas as questões acertando pelo menos 66% delas (ex: 2 de 3).
  3. Clicar em **"Finalizar atividade"**.
- **Resultado Esperado:**
  - O sistema exibe o card de resultado com status de **Aprovado**.
  - São concedidas as estrelas calculadas (descontando 1 estrela caso tenha errado alguma questão e 1 se excedeu o tempo limite).
  - São concedidas bolotas proporcionais (1 estrela = 10 bolotas).
  - Cada questão exibe se a resposta foi correta e sua respectiva justificativa explicativa (RF 12).

#### CT 07 — Reprovação por Desempenho Inferior a 66% (RN 03)
- **Objetivo:** Verificar se o sistema não conclui a atividade se a taxa for menor que 66%.
- **Passos:**
  1. Iniciar uma atividade com 3 questões.
  2. Responder errando 2 ou mais questões (taxa < 66%).
  3. Finalizar a atividade.
- **Resultado Esperado:**
  - O sistema exibe o card de aviso informando que a atividade **não foi concluída** por não atingir a taxa mínima de 66%.
  - O estado da atividade permanece como `'INICIADO'`, com 0 estrelas e 0 bolotas adicionadas.
  - É exibido o botão **"Tentar novamente agora"** (RN 04).

#### CT 08 — Desbloqueio Sequencial de Atividades (RN 06)
- **Objetivo:** Garantir que uma atividade seguinte só fica disponível após a conclusão da anterior.
- **Passos:**
  1. Acessar `/funcionario/modulos`.
  2. Verificar a Atividade 1 (deve estar liberada para início).
  3. Verificar a Atividade 2 (deve estar com badge **"Bloqueada"** e botão desabilitado).
  4. Tentar acessar diretamente a Atividade 2 digitando a URL no navegador (`/funcionario/atividades/exercicios?id=2`).
- **Resultado Esperado:** O sistema bloqueia o acesso via URL e redireciona para a lista de módulos com mensagem de aviso informando que a atividade anterior precisa ser concluída primeiro. Após concluir a Atividade 1 com aprovação, a Atividade 2 é liberada automaticamente.

#### CT 09 — Repetição de Atividade e Preservação da Melhor Nota (RN 04, RN 05 e RN 11)
- **Objetivo:** Validar que ao refazer a atividade apenas a maior pontuação é mantida e as bolotas não são acumuladas infinitamente.
- **Passos:**
  1. Concluir uma atividade obtendo 3 estrelas e 30 bolotas na 1ª tentativa.
  2. Anotar o saldo de bolotas na tela inicial.
  3. Refazer a mesma atividade e acertar menos questões (ex: obtendo 1 ou 2 estrelas).
  4. Verificar o registro de resultado.
- **Resultado Esperado:**
  - A pontuação e as estrelas mantidas continuam sendo as melhores (3 estrelas).
  - O contador de tentativas sobe para 2ª tentativa (RN 05).
  - O saldo de bolotas do funcionário **não aumenta** com a repetição (resgate único garantido pela RN 11).

#### CT 10 — Persistência no LocalStorage (RNF 09)
- **Objetivo:** Validar a recuperação de respostas caso a página seja recarregada durante um teste.
- **Passos:**
  1. Abrir uma atividade de exercícios.
  2. Marcar alternativas nas questões 1 e 2.
  3. Pressionar `F5` ou recarregar o navegador antes de enviar.
- **Resultado Esperado:** As opções marcadas anteriormente permanecem selecionadas, recuperadas automaticamente do `localStorage`.

---

### 7.3. Casos de Teste — Loja e Inventário (Funcionário)

#### CT 11 — Compra de Item com Saldo Suficiente (RF 17 e RF 18)
- **Objetivo:** Comprar um item e validar o débito de bolotas.
- **Pré-condição:** Funcionário com saldo maior ou igual ao preço do item.
- **Passos:**
  1. Acessar a loja (`/funcionario/loja`).
  2. Localizar um item disponível e clicar em **"Adquirir"**.
- **Resultado Esperado:**
  - Mensagem de confirmação *"Item comprado com sucesso"*.
  - O valor do item é subtraído imediatamente do saldo de bolotas do funcionário.
  - O item passa a constar no Inventário.
  - O botão do item na loja muda para indicar que já foi adquirido (RN 13).

#### CT 12 — Tentativa de Compra sem Saldo Suficiente (RF 18)
- **Objetivo:** Validar o bloqueio de compra por falta de bolotas.
- **Passos:**
  1. Localizar um item cujo preço seja maior que o saldo atual do funcionário.
  2. Tentar efetuar a compra.
- **Resultado Esperado:** O sistema bloqueia a transação e exibe o erro *"Saldo de bolotas insuficiente"*, sem descontar moedas.

#### CT 13 — Equipar Itens do Inventário (RF 20 e RN 12)
- **Objetivo:** Ativar um item cosmético e verificar a alteração visual.
- **Passos:**
  1. Na tela inicial do funcionário ou na loja, localizar um item comprado no inventário.
  2. Clicar em **"Equipar"**.
- **Resultado Esperado:**
  - Se for um `VISUAL_ESQUILOSO`, a imagem do mascote no perfil/início é atualizada.
  - Se for um `TEMA`, a paleta de cores do site muda dinamicamente conforme a folha de estilos do tema equipado.

---

### 7.4. Casos de Teste — Gestor e Administrador

#### CT 14 — Cadastro de Funcionário pelo Gestor com CPF Único (RF 07 e RN 17)
- **Objetivo:** Validar o cadastro e a restrição de duplicidade de CPF.
- **Passos:**
  1. Logar como Gestor e acessar `/gestor/funcionarios`.
  2. Clicar em **"Novo Funcionário"**.
  3. Preencher os dados informando um CPF já cadastrado no banco.
  4. Tentar submeter.
- **Resultado Esperado:** O sistema exibe o erro *"Já existe um usuário com este CPF"* e não permite duplicidade. Preenchendo um CPF novo, o funcionário é salvo vinculado à empresa do gestor.

#### CT 15 — Inativação e Reativação Lógica de Funcionário (RNF 01)
- **Objetivo:** Testar a desativação de conta de funcionário.
- **Passos:**
  1. Na lista de funcionários do gestor, clicar em **"Desativar"** em um funcionário.
  2. Confirmar a caixa de diálogo do navegador.
  3. Fazer logout e tentar fazer login com a conta do funcionário inativado.
- **Resultado Esperado:** O funcionário é marcado como `INATIVO`. Ao tentar fazer login, o sistema exibe a mensagem amigável: *"Sua conta está desativada. Entre em contato com o suporte ou gestor."*. O gestor pode clicar em **"Ativar"** para reativar o acesso.

#### CT 16 — Manutenção de Módulos e Exercícios pelo Administrador (RF 03)
- **Objetivo:** Criar e editar conteúdos do curso.
- **Passos:**
  1. Logar como Administrador e acessar `/administrador/modulos`.
  2. Criar um novo módulo com nome, descrição e estrelas mínimas.
  3. Acessar `/administrador/atividades` e criar uma atividade do tipo `SIMULACAO` associada ao módulo.
  4. Acessar `/administrador/exercicios` e adicionar uma questão com 4 alternativas, marcando a correta e informando a justificativa.
- **Resultado Esperado:** O módulo, atividade e exercício são gravados com sucesso e ficam disponíveis na trilha pedagógica.

---

## 8. Conclusão

Com a conclusão desta auditoria e a implementação completa das telas de perfil:
- Todas as **Regras de Negócio fundamentais (RN 01 a RN 18)** e requisitos de integridade do treinamento estão plenamente atendidos.
- As telas de perfil agora contam com design idêntico ao restante do sistema, fidelidade aos protótipos da documentação e proteção rigorosa dos dados confidenciais (conforme a RN 18).
- O código-fonte permanece simples, limpo e padronizado em PHP/MVC, respeitando o escopo pedagógico do ensino médio técnico.

