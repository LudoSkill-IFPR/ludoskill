# Guia de estudo do código — LudoSkill

Este documento explica a estrutura atual do projeto, os principais fluxos e as regras de negócio implementadas. Ele foi escrito para servir como material de estudo do time.

## 1. Arquitetura geral

O projeto usa uma organização MVC com uma camada adicional de serviços:

```text
Requisição HTTP
    -> Router
    -> Controller
    -> Service
    -> Repository
    -> MariaDB

Controller
    -> View
    -> HTML enviado ao navegador
```

- `public/index.php`: declara as rotas da aplicação.
- `app/core/Router.php`: compara a URL e o método HTTP com as rotas cadastradas.
- `app/controllers`: controla permissões, entrada do usuário, validação e escolha da view.
- `app/services`: representa os casos de uso e faz a ligação entre controller e repository.
- `app/repositories`: concentra SQL e transforma registros do PDO em objetos.
- `app/models`: representa as entidades e seus dados.
- `app/views`: apresenta dados recebidos pelo controller. Views não consultam mais o banco.
- `app/helpers/Validador.php`: valida formato e obrigatoriedade dos dados, sem executar SQL.
- `app/database`: conexão, criação do banco, estrutura SQL e migrações.

## 2. Objetos e `arrayParaObjeto`

O PDO devolve linhas do banco como arrays associativos. Cada model possui `arrayParaObjeto()` para centralizar a conversão.

Exemplo:

```php
$registro = $statement->fetch();
$funcionario = Funcionario::arrayParaObjeto($registro);

echo $funcionario->getNome();
echo $funcionario->getEmail();
```

`Usuario::extrairDadosUsuario()` concentra os campos compartilhados por administrador, gestor e funcionário. Assim, as subclasses não repetem a conversão de nome, CPF, e-mail, senha, telefone, estado e nascimento.

O projeto possui objetos para:

- `Usuario`, `Administrador`, `Gestor` e `Funcionario`;
- `Empresa`, `Modulo`, `Atividade`, `Exercicio` e `Item`;
- `Inventario` e `FuncionarioAtividade`.

`Funcionario` possui dois identificadores diferentes:

- `getId()`: ID da tabela `Usuarios`;
- `getIdFuncionario()`: ID da tabela `Funcionarios`.

Da mesma forma, `Gestor` possui `getId()` e `getIdGestor()`.

## 3. Autenticação e permissões

As permissões ficam em `app/core/Controller.php`, porque dependem da sessão e de redirecionamentos HTTP. Elas não pertencem aos models.

- `autenticacaoRequired()`: aceita qualquer usuário autenticado e ativo.
- `adminRequired()`: aceita somente administrador.
- `gestorRequired()`: aceita somente gestor.
- `funcionarioRequired()`: aceita somente funcionário.

Responsabilidades:

| Perfil | Responsabilidades |
|---|---|
| Administrador | Empresas, gestores, módulos, atividades, exercícios e itens |
| Gestor | Funcionários da própria empresa e acompanhamento de desempenho |
| Funcionário | Curso, atividades, perfil, loja e inventário |

No login, o repository identifica a especialização do usuário e cria um objeto `Administrador`, `Gestor` ou `Funcionario`. Depois da autenticação, o ID da sessão é regenerado.

Todas as requisições POST recebem automaticamente um token CSRF. O router recusa solicitações sem um token válido.

## 4. Curso, módulos e atividades

`Modulos.estado` e `Atividades.estado` usam somente:

```text
ATIVO | INATIVO
```

Esses estados controlam se o conteúdo está disponível ao funcionário.

Ao usar a ação “Desativar”, módulos e atividades são preservados no banco e passam para `INATIVO`. Isso mantém exercícios e históricos de progresso existentes.

O progresso individual fica em `Funcionario_atividade.estado`:

```text
PENDENTE | INICIADO | CONCLUIDO
```

Uma atividade possui:

- módulo;
- tipo `INTRODUCAO`, `REVISAO` ou `SIMULACAO`;
- nome;
- estado administrativo;
- ordem dentro do módulo;
- tempo limite em segundos.

Uma atividade somente é liberada quando:

1. o módulo e a atividade estão ativos;
2. o funcionário possui o mínimo de estrelas exigido pelo módulo;
3. todas as atividades anteriores do mesmo módulo foram concluídas.

## 5. Exercícios e JSON

Um registro de `Exercicios` representa um conjunto de questões ligado a uma atividade. A coluna `conteudo` armazena uma lista JSON:

```json
[
  {
    "enunciado": "Qual é a alternativa correta?",
    "alternativas": ["Alternativa A", "Alternativa B"],
    "alternativa_correta": 1,
    "justificativa": "A alternativa B está correta porque..."
  }
]
```

O administrador pode adicionar quantas questões quiser no mesmo formulário. `normalizarQuestoes()` limpa os dados e `Validador::validarExercicio()` confere:

- enunciado obrigatório;
- pelo menos duas alternativas;
- nenhuma alternativa vazia;
- alternativa correta existente;
- justificativa obrigatória.

No formulário do funcionário, cada resposta usa o ID real do conjunto e o índice da questão. Isso evita colisão quando uma atividade possui mais de um conjunto de exercícios.

As respostas ainda não enviadas são guardadas no `localStorage` do navegador a cada alteração. Depois da correção, esses dados locais são apagados.

## 6. Pontuação e progresso

Ao abrir uma atividade, o progresso passa para `INICIADO`. Ao responder:

- pontuação = percentual de respostas corretas;
- conclusão exige pelo menos 66%;
- a tentativa é contabilizada;
- começa com três estrelas;
- cada erro remove uma estrela;
- ultrapassar o tempo limite remove mais uma estrela;
- estrelas nunca ficam negativas;
- bolotas somente são concedidas em uma tentativa concluída.

O repository usa transação e preserva a maior pontuação e a maior quantidade de estrelas. A recompensa em bolotas é concedida somente na primeira conclusão da atividade; novas tentativas podem melhorar pontuação e estrelas, mas não concedem outra recompensa.

O banco possui uma restrição única para o par funcionário/atividade.

## 7. Dashboard do gestor

O gestor somente enxerga funcionários da sua empresa. O dashboard mostra:

- quantidade de funcionários ativos;
- empresa do gestor;
- ranking em ordem decrescente;
- média de estrelas das atividades concluídas.

Funcionários sem atividade concluída aparecem com média zero. A tela também trata corretamente uma empresa ainda sem funcionários.

## 8. Perfil

Todos os perfis podem visualizar e alterar:

- nome;
- data de nascimento;
- e-mail;
- telefone;
- senha.

O CPF não é enviado pelo formulário de edição. Pontuação, bolotas, nível e progresso também não são aceitos do navegador.

O perfil do funcionário mostra atividades concluídas, estrelas e desempenho médio.

## 9. Loja e inventário

Comprar e equipar usam POST e exigem perfil de funcionário.

A compra é executada em uma transação:

1. bloqueia o saldo do funcionário para a operação;
2. confirma que o item está ativo;
3. confirma que o item ainda não foi adquirido;
4. confirma saldo suficiente;
5. adiciona o item ao inventário;
6. desconta as bolotas;
7. confirma a transação.

O par funcionário/item é único no banco. Itens adquiridos não são apagados quando o administrador os desativa.

Ao equipar, o sistema identifica o funcionário pela sessão e confirma que o item pertence a ele. Somente itens do mesmo tipo são desequipados. Dessa maneira, um tema e um visual do mascote podem ficar ativos ao mesmo tempo.

Os temas alteram as variáveis de cores da interface do funcionário. O visual do mascote aparece na página inicial.

## 10. Banco de dados

O arquivo principal é `app/database/banco.sql`.

Para um banco antigo, use `app/database/migrations/2026_10_12_padronizacao.sql` depois de realizar backup. A migração:

- adiciona estado aos módulos;
- converte o estado das atividades para `ATIVO/INATIVO`;
- adiciona ordem e tempo limite;
- remove pontuação e estrelas da definição da atividade;
- consolida progressos duplicados;
- consolida compras duplicadas;
- cria as restrições únicas;
- impede exclusão física de item pertencente a um inventário.

O administrador inicial pode ser criado de forma segura durante a inicialização, sem senha fixa no repositório, usando:

```text
LUDOSKILL_ADMIN_EMAIL
LUDOSKILL_ADMIN_PASSWORD
LUDOSKILL_ADMIN_CPF
LUDOSKILL_ADMIN_NAME
LUDOSKILL_ADMIN_BIRTHDATE
LUDOSKILL_ADMIN_PHONE
```

`DB_PASS` também pode ser definido como variável de ambiente.

## 11. Tratamento de erros

- Exceções completas são enviadas ao log do PHP.
- O usuário recebe uma página amigável, sem SQL ou stack trace.
- Para habilitar mensagens de desenvolvimento local, configure `APP_ENV=development`.
- Violações de CSRF recebem HTTP 403.
- Erros inesperados recebem HTTP 500.

## 12. Decisões importantes

- Models guardam estado e comportamento simples; não acessam repositories.
- Views apenas apresentam dados; não executam SQL.
- Repositories retornam entidades para registros de domínio.
- Consultas de relatório podem retornar arrays, pois representam projeções calculadas e não uma tabela/entidade completa.
- Permissões ficam na camada HTTP, não em `Administrador`, `Gestor` ou `Funcionario`.
- Operações que alteram dados usam POST.
- Operações com mais de uma escrita usam transação.

## 13. Limites de verificação

Disponibilidade de 95%, compatibilidade real entre navegadores e suporte simultâneo a 100 usuários precisam ser comprovados em ambiente publicado por monitoramento, testes de navegador e teste de carga. Essas metas não podem ser garantidas apenas pela leitura do código.
