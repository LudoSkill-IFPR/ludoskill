# Análise de requisitos do LudoSkill

Data da análise: 05/11/2026  
Escopo: código original em `/var/www/html/ludoskill` e requisitos da documentação do Projeto Integrador.

## Critérios usados

- **Atendido:** há fluxo implementado e evidência direta no código.
- **Parcial:** existe uma parte do fluxo, mas há diferença relevante em relação ao texto do requisito.
- **Não comprovado:** a leitura do código não basta; é necessário teste de navegador, carga ou infraestrutura.
- **Não atendido:** não foi localizada implementação correspondente.

Esta análise considera a documentação somente como fonte dos requisitos. As alterações realizadas foram limitadas ao pedido atual.

## Requisitos funcionais

| Requisito | Status | Evidência e observação |
|---|---|---|
| RF01 — autenticação | Atendido | Login, verificação de senha, sessão e bloqueio de usuário inativo estão implementados nas camadas de autenticação e em `Controller`. |
| RF02 — administrador visualiza gestores por empresa | Atendido | A lista de gestores mostra a empresa vinculada e o painel administrativo apresenta usuários por empresa. |
| RF03 — administrador mantém módulos, atividades e questões | Atendido | Há CRUDs separados de módulos, atividades e exercícios, com exercícios armazenados como JSON. |
| RF04 — administrador mantém gestores | Atendido | Cadastro, edição, listagem, ativação e inativação estão implementados. |
| RF05 — administrador mantém itens | Atendido | O CRUD de itens permite os tipos tema e visual do mascote, estado, preço e imagem quando aplicável. |
| RF06 — gestor visualiza desempenho decrescente | Atendido | A consulta ordena pela média de estrelas em ordem decrescente e o painel agora a apresenta em gráfico de barras. |
| RF07 — gestor mantém funcionários | Atendido | O gestor cadastra, lista, edita, ativa e inativa somente funcionários de sua empresa. |
| RF08 — tela inicial mostra progresso, módulo e atividade | Parcial | A tela mostra progresso total em gráfico e a próxima atividade, mas não mostra claramente o nome do módulo atual. A tela de módulos contém a relação completa. |
| RF09 — perfil mostra identificação e, para funcionário, desempenho/progresso | Parcial | Nome, CPF e métricas do funcionário são exibidos; o ID interno do usuário não é apresentado explicitamente. |
| RF10 — usuário altera suas informações | Atendido | Nome, nascimento, e-mail, telefone e senha podem ser alterados; CPF permanece protegido. |
| RF11 — funcionário visualiza módulos e atividades | Atendido | A tela usa módulos expansíveis e lista as atividades pertencentes a cada um. |
| RF12 — funcionário realiza atividade e vê justificativa do erro | Atendido | Após a correção, a resposta correta e a justificativa de cada questão são exibidas. |
| RF13 — funcionário responde questões vinculadas à atividade | Atendido | O conteúdo JSON é decodificado, renderizado e corrigido por atividade. |
| RF14 — funcionário repete atividade para melhorar resultado | Atendido | Não há limite de tentativas e o repositório preserva a melhor pontuação e as melhores estrelas. |
| RF15 — conclusão concede pontos e bolotas | Atendido | O resultado atualiza pontuação e bolotas em transação. As bolotas agora são configuráveis por atividade. |
| RF16 — desempenho individual é registrado | Atendido | `Funcionario_atividade` registra estado, nota, estrelas, bolotas, tentativas e tempo. |
| RF17 — funcionário compra itens com bolotas | Atendido | A compra valida saldo, disponibilidade e propriedade antes da transação. |
| RF18 — compra desconta bolotas | Atendido | O desconto e a inclusão no inventário ocorrem na mesma transação. |
| RF19 — funcionário visualiza inventário | Atendido | O painel do funcionário lista os itens adquiridos e seu estado de uso. |
| RF20 — funcionário ativa item e altera interface/mascote | Atendido | O item equipado é persistido; temas alteram variáveis visuais e visuais do mascote são exibidos no perfil inicial. |

## Regras de negócio

| Regra | Status | Evidência e observação |
|---|---|---|
| RN01 — administrador previamente cadastrado | Parcial | `DatabaseInitializer` cria o administrador inicial, mas depende das variáveis de ambiente administrativas estarem configuradas. |
| RN02 — recompensa e descontos por erro/tempo | Atendido | A atividade define o máximo de estrelas e as bolotas. Cada erro desconta uma estrela e ultrapassar o limite desconta mais uma. |
| RN03 — conclusão com no mínimo 66% | Atendido | A conclusão é calculada com `pontuacao >= 66`. |
| RN04 — tentativas ilimitadas | Atendido | Não há bloqueio por quantidade de tentativas. |
| RN05 — manter somente a maior pontuação | Atendido | O repositório usa o maior valor entre o resultado anterior e o novo. |
| RN06 — liberação sequencial das atividades | Atendido | O acesso é negado enquanto houver atividade anterior ativa e não concluída no módulo. |
| RN07 — tipos introdução, revisão e simulação | Parcial | Os três tipos existem, mas o sistema acrescentou `TEXTO`, extensão não prevista na versão analisada da documentação. |
| RN08 — progresso composto por atividade e módulo | Parcial | O progresso de atividades é calculado; não há cálculo separado de percentual/conclusão por módulo na tela inicial. |
| RN09 — desempenho é média de estrelas | Atendido | As consultas calculam média de estrelas das atividades concluídas, excluindo atividades somente textuais. |
| RN10 — módulo exige estrelas mínimas do módulo anterior | Parcial | O sistema compara o mínimo do módulo com a soma global de estrelas concluídas, não especificamente com as estrelas do módulo anterior. |
| RN11 — bolotas concedidas uma única vez por atividade | Atendido | Uma atividade já concluída conserva as bolotas anteriores em novas tentativas. |
| RN12 — itens dos tipos tema e visual do mascote | Atendido | O enum e o CRUD usam `TEMA` e `VISUAL_ESQUILOSO`. |
| RN13 — item único e pago somente com bolotas | Atendido | Há restrição única no banco e validações transacionais de propriedade e saldo. |
| RN14 — bolotas gastas não são devolvidas | Atendido | Não existe fluxo de estorno ou devolução. |
| RN15 — itens adquiridos não são devolvidos | Atendido | Não existe exclusão/devolução do inventário pelo funcionário. |
| RN16 — somente gestor cadastra/inativa funcionário | Atendido | As ações exigem perfil gestor e são limitadas à empresa vinculada. |
| RN17 — CPF único | Atendido | Há `UNIQUE` no banco e verificações nos cadastros. |
| RN18 — gestor/funcionário não alteram CPF, progresso, desempenho ou bolotas | Atendido | A edição do próprio perfil não recebe nem atualiza esses campos. |

## Requisitos não funcionais

| Requisito | Status | Evidência e observação |
|---|---|---|
| RNF01 — cancelar/voltar e confirmar inativação | Parcial | Os CRUDs possuem retorno e as inativações principais pedem confirmação. Não há comprovação de retorno/cancelamento uniforme em toda operação crítica. |
| RNF02 — consistência visual | Parcial | Há CSS compartilhado, cards e menus padronizados, mas permanecem estilos específicos e diferenças de estrutura entre telas. |
| RNF03 — mensagens compreensíveis | Atendido | Validações usam mensagens em linguagem de usuário e erros internos são registrados no servidor. |
| RNF04 — funções principais em até dois cliques | Parcial | Os menus dão acesso direto aos principais CRUDs, mas isso precisa de teste formal de navegação para todos os perfis. |
| RNF05 — últimas versões de Chrome, Firefox, Edge e Brave | Não comprovado | Exige matriz de testes real nos navegadores e resoluções definidas. |
| RNF06 — disponibilidade de 95% | Não comprovado | Depende da hospedagem, monitoramento e período de medição. |
| RNF07 — 100 usuários simultâneos | Não comprovado | Não foi localizado teste de carga; deve ser validado com ferramenta como k6 ou JMeter em ambiente equivalente ao de produção. |
| RNF08 — resultado confirmado persistido antes de fechar a aba | Atendido | O resultado só é mostrado após gravação transacional no banco. |
| RNF09 — progresso salvo no `localStorage` a cada alteração | Parcial | As respostas marcadas durante o exercício são salvas a cada mudança; o progresso geral do curso permanece no banco e não é espelhado no `localStorage`. |
| RNF10 — senhas protegidas por hash | Atendido | Cadastros e alterações usam `password_hash`, e o login usa verificação de hash. |
| RNF11 — controle de acesso por perfil e empresa | Atendido | Há guardas por perfil, escopo da empresa nos fluxos do gestor, CSRF global para POST e consultas do próprio funcionário. |
| RNF12 — arquitetura, nomenclatura e estilo consistentes | Parcial | O projeto segue MVC com model/repository/service/controller, porém ainda há nomenclaturas legadas, formatação desigual e métodos de validação fora do helper. |

## Pontos que merecem decisão futura

Estes itens foram somente registrados e não alterados:

1. Definir se o desbloqueio de módulo deve usar estrelas globais ou apenas as estrelas do módulo imediatamente anterior (RN10).
2. Definir uma métrica explícita de progresso por módulo e mostrar o nome do módulo atual na tela inicial (RF08/RN08).
3. Decidir se o ID interno deve ser exibido no perfil para cumprir literalmente o RF09.
4. Formalizar testes de navegadores, carga e disponibilidade para comprovar RNF05, RNF06 e RNF07.
5. Decidir se o `localStorage` deve guardar somente respostas ainda não enviadas ou também uma cópia do progresso geral (RNF09).
