# Navegação e cronômetro dos exercícios

Este documento explica as alterações feitas no fluxo de realização dos exercícios pelo funcionário.

## Objetivos

- Permitir que o funcionário volte ao conteúdo da atividade durante uma tentativa.
- Manter as respostas marcadas quando ele retornar aos exercícios.
- Mostrar quanto tempo ainda resta para responder.
- Exibir, depois da correção, um botão para encerrar a visualização e voltar aos módulos.

## 1. Retorno ao conteúdo da atividade

Na view `app/views/funcionario/exercicios.php`, o antigo botão que voltava diretamente aos módulos foi substituído por **Voltar ao conteúdo**. O endereço inclui o ID da atividade:

```text
/funcionario/atividades/conteudo?id=ID
```

As alternativas escolhidas já eram guardadas no `localStorage` do navegador. Antes de sair pelo novo botão, o JavaScript marca a navegação como segura para evitar o alerta de abandono da página. Quando os exercícios são abertos novamente, as respostas armazenadas são selecionadas outra vez.

## 2. Preservação do início da tentativa

O método `ExercicioController::acessarAtividade()` anteriormente substituía o horário inicial sempre que a página era aberta:

```php
$_SESSION['atividade_inicio'][$idAtividade] = microtime(true);
```

Agora o operador `??=` só cria esse valor quando ele ainda não existe:

```php
$_SESSION['atividade_inicio'][$idAtividade] ??= microtime(true);
```

Assim, voltar ao conteúdo e retornar aos exercícios não reinicia o tempo da tentativa.

## 3. Cálculo do tempo restante

O método privado `calcularTempoRestante()` usa:

- o instante inicial armazenado na sessão;
- o `tempo_limite` configurado pelo administrador na atividade;
- o horário atual do servidor.

O cálculo retorna a quantidade de segundos restantes, nunca um número negativo. Esse valor é enviado para a view tanto no primeiro acesso quanto quando o formulário retorna com o aviso para responder todas as questões.

O servidor continua sendo a referência para calcular o tempo total da tentativa e as estrelas. O JavaScript serve apenas para atualizar a informação visual a cada segundo.

## 4. Cronômetro da tela

A view apresenta o tempo no formato `minutos:segundos`. O painel usa posicionamento `sticky` para permanecer visível durante a rolagem das questões. O JavaScript calcula o instante final uma vez e compara esse valor com `Date.now()`, evitando que pequenas pausas do navegador façam o contador perder precisão.

Estados visuais:

- normal: borda e número verdes;
- último minuto: amarelo;
- tempo esgotado: vermelho.

Quando o contador chega a zero, o funcionário ainda pode enviar as respostas. O tempo excedido será considerado pelo cálculo de estrelas que já existia no controller.

O cronômetro não é mostrado depois da correção.

## 5. Correção e conclusão da visualização

O botão de envio passou a se chamar **Corrigir respostas**. Ao enviar:

1. o controller valida todas as alternativas;
2. calcula acertos, pontuação, estrelas, bolotas e tempo;
3. chama `FuncionarioAtividadeService::registrarResultado()`;
4. o repository grava os dados em `Funcionario_atividade`;
5. somente depois disso a view mostra o resultado e as justificativas.

No final da lista corrigida aparece **Concluir exercício**. Esse botão direciona para `/funcionario/modulos`. Nenhuma gravação depende desse clique, pois o resultado já foi salvo antes de a correção ser apresentada.

Depois da correção, as respostas temporárias daquela atividade são removidas do `localStorage`.

## 6. Arquivos alterados

- `app/controllers/ExercicioController.php`
- `app/views/funcionario/exercicios.php`
- `public/assets/css/exercicios.css`
- `tests/smoke_views.php`

## 7. Testes

Os testes de views cobrem dois estados da página:

- tentativa em andamento, com o tempo restante;
- exercício corrigido, com resultado e ação de conclusão.

Também devem ser executados o lint do PHP e a verificação de conversão dos models.
