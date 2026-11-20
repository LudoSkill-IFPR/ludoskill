# Fluxos das funcionalidades principais do LudoSkill

Este documento explica como as principais funcionalidades percorrem as camadas do projeto. O padrão geral é:

```text
rota -> controller -> service -> repository -> banco de dados
                              |
                              -> view
```

- As rotas ficam em `public/index.php`.
- Controllers recebem dados, aplicam regras do fluxo e escolhem a view.
- Services fazem a ligação entre controllers e repositories.
- Repositories executam SQL e convertem registros em objetos.
- Models representam as entidades.
- Views montam o HTML e, em alguns casos, usam JavaScript para interação.

## 1. Cadastro e leitura de exercícios

### 1.1 Estrutura no banco

A tabela `Exercicios` possui uma coluna `conteudo` do tipo JSON. O model `app/models/Exercicio.php` mantém esse conteúdo como `string`, pois o JSON é armazenado dessa forma no banco e só é decodificado quando necessário.

Cada exercício pertence a uma atividade por meio de `id_atividade`. Uma atividade pode ter mais de um registro de exercício, mas o sistema não permite misturar questões e simulações dentro da mesma atividade.

Atividades do tipo `TEXTO` não aceitam exercícios.

### 1.2 Formulário administrativo

As telas são:

- `app/views/administrador/exercicios/exercicio_create.php`
- `app/views/administrador/exercicios/exercicio_edit.php`

O administrador escolhe entre:

- `exercicio`: conjunto de questões de múltipla escolha;
- `simulacao`: manutenção interativa de um celular.

O arquivo `public/assets/javascript/exercicios.js` controla o formulário. Para questões, ele:

1. Clona o template de questão.
2. Permite adicionar ou remover questões.
3. Permite adicionar alternativas.
4. Exige pelo menos duas alternativas.
5. Reorganiza os índices dos campos após inclusões e exclusões.

Os nomes enviados ao PHP seguem este formato:

```text
questoes[0][enunciado]
questoes[0][alternativas][0]
questoes[0][alternativas][1]
questoes[0][alternativa_correta]
questoes[0][justificativa]
```

### 1.3 JSON de questões

No método `salvar()` de `app/controllers/ExercicioController.php`, `normalizarQuestoes()` limpa os espaços, reorganiza alternativas e converte o índice correto em inteiro. O resultado é codificado com `json_encode()`:

```json
{
  "tipo": "exercicio",
  "questoes": [
    {
      "enunciado": "Qual ferramenta deve ser usada?",
      "alternativas": ["Pinça", "Martelo", "Serrote"],
      "alternativa_correta": 0,
      "justificativa": "A pinça permite manipular peças pequenas com segurança."
    }
  ]
}
```

O `Validador::validarExercicio()` confirma o tipo, a descrição, a atividade, a existência de questões, o número de alternativas, o índice correto e a justificativa.

Depois da validação:

1. O controller cria um objeto `Exercicio`.
2. O `ExercicioService` encaminha o objeto.
3. O `ExercicioRepository` grava descrição, atividade e JSON em `Exercicios`.

### 1.4 JSON de simulação

No cadastro da simulação, o administrador escolhe somente a peça inicialmente quebrada:

- `bateria`;
- `camera`;
- `chip`.

O JSON atual é propositalmente pequeno:

```json
{
  "tipo": "simulacao",
  "peca_quebrada": "bateria"
}
```

As etapas e ferramentas não são escritas pelo administrador. Elas são definidas no `ExercicioController` pelos métodos:

- `montarEtapasSimulacao()`;
- `getFerramentasSimulacao()`.

Isso mantém o passo a passo padronizado. Ao abrir a simulação, o servidor acrescenta as etapas adequadas à peça e as ferramentas permitidas.

### 1.5 Decodificação do JSON

O método `decodificarConteudo()` executa `json_decode($conteudo, true)` e devolve uma estrutura padronizada.

Ele reconhece:

1. JSON atual de questões.
2. JSON atual de simulação.
3. Formatos antigos em lista.
4. Simulações antigas nas quais a peça quebrada estava dentro das etapas ou dos componentes.

Essa compatibilidade evita que registros antigos deixem de funcionar.

Ao funcionário acessar `/funcionario/atividades/exercicios?id=...`, o método `acessarAtividade()`:

1. Confirma que a sessão pertence a um funcionário.
2. Valida a atividade e as regras de liberação.
3. Busca todos os exercícios daquela atividade.
4. Decodifica cada JSON.
5. Escolhe `funcionario/exercicios.php` ou `funcionario/simulacao.php`.

Questões são exibidas como alternativas de rádio. A simulação usa as imagens de `public/assets/imagens/simulacao/` e o comportamento de `public/assets/javascript/simulacao.js`.

### 1.6 Segurança da simulação

O JavaScript controla a experiência visual, registra as ações e permite que o funcionário corrija peças danificadas. Exemplos de ações:

```text
ferramenta:aquecedor
interagir:tampa
interagir:bateria
instalar:bateria
instalar:tampa
```

As ações ficam em um campo oculto como JSON. Entretanto, o servidor não confia apenas no JavaScript. Ao receber a resposta, `avaliarSimulacao()` repete a máquina de estados em PHP e confirma que:

- a tampa foi removida corretamente;
- a peça defeituosa foi substituída;
- peças quebradas por engano também foram consertadas;
- não ficou nenhuma peça fora do aparelho;
- a tampa foi recolocada.

## 2. Recuperação de senha e envio de e-mails

### 2.1 Recuperação de senha

As rotas são:

```text
GET  /esqueci-senha
POST /recuperar-senha
GET  /gerar-nova-senha?token=...
POST /redefinir-senha
```

As views são:

- `app/views/entrada/esqueciSenha.php`;
- `app/views/entrada/gerarNovaSenha.php`.

O fluxo em `AutenticacaoController` é:

1. O usuário informa o e-mail.
2. `UsuarioService` e `UsuarioRepository` procuram a conta.
3. O controller gera 32 bytes aleatórios e os transforma em um token hexadecimal de 64 caracteres.
4. O token é usado como chave em `$_SESSION['recuperacoes_senha']`.
5. A sessão guarda `id_usuario`, e-mail e horário de expiração.
6. Um link com o token é enviado por e-mail.
7. Ao abrir o link, `recuperacaoValida()` verifica formato, existência e expiração de 30 minutos.
8. A nova senha e sua confirmação são validadas.
9. `AutenticacaoService::redefinirSenha()` cria o hash com `password_hash()`.
10. `UsuarioRepository::updateSenha()` atualiza `Usuarios.senha_hash`.
11. O token usado é removido e o usuário volta ao login.

A recuperação autoriza somente a troca de senha; ela não cria `$_SESSION['usuario_logado']` e não autentica automaticamente o usuário.

### 2.2 Limitação atual da recuperação

O token é armazenado na sessão PHP, não no banco. Por isso, o link depende do cookie da mesma sessão que fez a solicitação. Abrir o e-mail em outro navegador ou dispositivo pode produzir “link inválido ou expirado”.

Para permitir recuperação entre dispositivos, uma evolução futura seria armazenar no banco o hash do token, o usuário e a expiração.

### 2.3 PHPMailer

O envio é centralizado em `app/helpers/SendMailHelper.php`. O helper lê estas variáveis de ambiente:

```text
MAIL_HOST
MAIL_PORT
MAIL_ENCRYPTION
MAIL_USERNAME
MAIL_PASSWORD
MAIL_FROM_ADDRESS
MAIL_FROM_NAME
```

O PHPMailer usa SMTP, conteúdo HTML, UTF-8 e também cria uma versão em texto simples em `AltBody`. Falhas são gravadas no log do PHP/Apache e o método retorna `false`.

O helper é usado em três fluxos principais:

- recuperação de senha, pelo `AutenticacaoController`;
- aprovação de solicitação de gestor, pelo `SolicitacaoAcessoController`;
- criação de funcionário, pelo `FuncionarioController`.

Na aprovação do gestor, o sistema gera uma senha temporária aleatória, salva somente seu hash no banco e envia a senha original no e-mail. Na criação do funcionário, a senha definida no formulário também é transformada em hash pelo repository antes de ser salva.

## 3. TinyMCE e descrição completa da atividade

### 3.1 Cadastro e edição

As telas de atividade possuem:

```html
<textarea id="descricao" name="descricao"></textarea>
```

No final das views, é chamado:

```php
TinyMCEHelper::render('#descricao')
```

O helper `app/helpers/TinyMCEHelper.php` carrega a instalação local do TinyMCE em `public/assets/tinymce_8.10.1/` e inicializa o editor sobre o seletor informado.

O editor oferece títulos, fontes, tamanhos, negrito, itálico, sublinhado, alinhamento, listas, links, tabelas, visualização, tela cheia e edição do código HTML.

### 3.2 Sanitização e armazenamento

Quando o formulário chega a `AtividadeController::salvar()` ou `atualizar()`, o HTML passa por:

```php
ConteudoHtmlHelper::sanitizar($dados['descricao'])
```

O helper permite somente tags, atributos, links e estilos conhecidos. Elementos perigosos como `script`, `iframe`, `object` e `embed` são removidos. Eventos HTML como `onclick` também não são mantidos.

`ConteudoHtmlHelper::textoPuro()` converte o HTML em texto para que a validação consiga identificar conteúdos visualmente vazios, como `<p><br></p>`.

Depois da validação, o HTML sanitizado é salvo em `Atividades.descricao`, uma coluna `LONGTEXT`.

### 3.3 Exibição ao funcionário

Ao abrir `/funcionario/atividades/conteudo?id=...`, `AtividadeController::visualizarFuncionario()` sanitiza novamente a descrição e a envia para `app/views/funcionario/atividade.php`.

A view imprime:

```php
<?= $descricao ?>
```

Não se usa `htmlspecialchars()` nesse ponto, pois isso exibiria as tags como texto. A impressão direta é segura porque o conteúdo já passou pelo sanitizador e permite preservar títulos, listas, fontes e tabelas criadas no TinyMCE.

Depois da leitura:

- atividades com exercícios exibem o botão “Exercícios”;
- atividades `TEXTO` sem exercícios exibem “Concluir leitura”;
- atividades sem conteúdo de exercício informam que ele ainda não foi cadastrado.

## 4. Timer, correção e recompensas

### 4.1 Início e timer

Ao abrir os exercícios, o servidor registra o início da tentativa:

```php
$_SESSION['atividade_inicio'][$idAtividade] ??= microtime(true);
```

O operador `??=` é importante: voltar ao conteúdo e entrar novamente não reinicia o tempo.

O servidor também chama `FuncionarioAtividadeService::iniciarAtividade()`, que cria ou mantém o registro em `Funcionario_atividade` como `INICIADO`.

`calcularTempoRestante()` compara o início salvo com `Atividades.tempo_limite`. A view recebe os segundos restantes e o JavaScript apenas atualiza a apresentação visual a cada segundo.

O servidor calcula novamente o tempo ao corrigir. Portanto, alterar o contador pelo navegador não altera o resultado real.

As respostas não enviadas ficam temporariamente no `localStorage`, permitindo voltar ao conteúdo sem perdê-las. Após a correção, esse armazenamento é removido.

### 4.2 Correção das questões

`ExercicioController::responderAtividade()` percorre todas as questões, valida que todas foram respondidas e compara o índice recebido com `alternativa_correta`.

A pontuação é percentual:

```text
pontuação = arredondar(acertos / total * 100)
```

A atividade é considerada concluída com pelo menos 66 pontos.

Para questões:

- cada erro reduz uma estrela;
- ultrapassar o tempo reduz mais uma estrela;
- a quantidade nunca fica abaixo de zero;
- bolotas são concedidas somente se a atividade for concluída.

A view mostra a alternativa correta, a escolha incorreta e a justificativa cadastrada pelo administrador.

### 4.3 Correção da simulação

A simulação exige que o aparelho termine totalmente consertado. Erros podem ser cometidos, mas precisam ser corrigidos.

Depois da manutenção válida:

- a pontuação da simulação é 100;
- se concluir dentro do tempo, recebe todas as estrelas configuradas;
- se concluir fora do tempo, recebe zero estrelas;
- as bolotas configuradas são concedidas após o conserto completo.

### 4.4 Gravação em `Funcionario_atividade`

O resultado é encaminhado para `FuncionarioAtividadeRepository::registrarResultado()`, que usa uma transação.

O repository:

1. Localiza o funcionário pelo ID de usuário da sessão.
2. Bloqueia o resultado anterior com `FOR UPDATE`.
3. Mantém a melhor pontuação e a melhor quantidade de estrelas.
4. Incrementa `qtd_tentativas`.
5. Atualiza `estado` para `CONCLUIDO` quando a melhor pontuação chega a 66.
6. Grava o tempo da tentativa.
7. Soma em `Funcionarios` somente a diferença de pontos e bolotas ainda não concedida.

O cálculo por diferença impede ganhar repetidamente as mesmas bolotas ao refazer uma atividade já concluída.

## 5. Loja e inventário

### 5.1 Carregamento da loja

A rota `GET /funcionario/loja` chama `ItemController::listarTodosFuncionario()`.

O controller carrega:

- itens ativos por `ItemService`;
- dados e saldo do funcionário por `FuncionarioService`;
- itens já adquiridos por `InventarioService`;
- mensagens de sucesso ou erro guardadas na sessão.

A view compara os IDs dos itens da loja com o inventário e decide se deve mostrar:

- “Já adquirido”;
- “Bolotas insuficientes”;
- botão “Comprar”.

### 5.2 Filtros

Os filtros de `app/views/funcionario/loja.php` funcionam no navegador. Cada item possui atributos `data-*` com nome, preço, tipo e posse.

O JavaScript permite:

- ordenar por menor ou maior preço;
- ordenar alfabeticamente A–Z ou Z–A;
- mostrar itens que o funcionário possui ou não possui;
- filtrar `TEMA` e `VISUAL_ESQUILOSO`.

Os filtros não consultam novamente o servidor: eles reorganizam e ocultam os itens já carregados na página.

### 5.3 Compra

O formulário envia `POST /funcionario/comprar` com o ID do item. `InventarioRepository::comprar()` abre uma transação e:

1. Confirma que o usuário autenticado é um funcionário ativo.
2. Bloqueia o funcionário com `FOR UPDATE`.
3. Confirma que o item existe e está ativo.
4. Impede a compra de um item já adquirido.
5. Confirma que o saldo é suficiente.
6. Insere o item em `Inventarios` com a data atual.
7. Desconta o preço de `Funcionarios.bolotas_totais`.
8. Confirma a transação.

Se qualquer etapa falhar, é feito `ROLLBACK`: o item não entra no inventário e as bolotas não são descontadas.

### 5.4 Correção da compra no localhost

O log local registrava:

```text
Field 'data_aquisicao' doesn't have a default value
```

O banco hospedado possuía um valor padrão para essa coluna, mas o banco local antigo não. O código inseria somente `id_funcionario` e `id_item`, dependendo desse padrão.

O `INSERT` de `app/repositories/InventarioRepository.php` passou a informar explicitamente:

```sql
INSERT INTO Inventarios (id_funcionario, id_item, data_aquisicao)
VALUES (:funcionario, :item, CURRENT_DATE)
```

Assim, a compra funciona tanto em bancos antigos sem valor padrão quanto no esquema atual.

### 5.5 Equipar itens

Os itens comprados aparecem no inventário da página inicial do funcionário. A rota `POST /funcionario/equipar` chama `InventarioRepository::equipar()`.

O repository:

1. Confirma que o item pertence ao funcionário.
2. Identifica o tipo do item.
3. Desmarca outros itens em uso do mesmo tipo.
4. Marca o escolhido com `em_uso = 1`.

Para itens `TEMA`, o ID equipado também fica em `$_SESSION['tema_equipado']`, permitindo ao sistema aplicar o tema durante a navegação.

## 6. Arquivos de referência rápida

| Funcionalidade | Arquivos principais |
|---|---|
| Cadastro e JSON de exercícios | `ExercicioController.php`, `exercicio_create.php`, `exercicio_edit.php`, `exercicios.js` |
| Decodificação e correção | `ExercicioController.php`, `funcionario/exercicios.php`, `funcionario/simulacao.php`, `simulacao.js` |
| Resultados e recompensas | `FuncionarioAtividadeService.php`, `FuncionarioAtividadeRepository.php` |
| Conteúdo TinyMCE | `TinyMCEHelper.php`, `ConteudoHtmlHelper.php`, `AtividadeController.php`, `funcionario/atividade.php` |
| Recuperação de senha | `AutenticacaoController.php`, `AutenticacaoService.php`, `UsuarioRepository.php` |
| E-mails | `SendMailHelper.php`, `SolicitacaoAcessoController.php`, `FuncionarioController.php` |
| Loja e inventário | `ItemController.php`, `InventarioService.php`, `InventarioRepository.php`, `funcionario/loja.php` |

