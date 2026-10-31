# Implementação da descrição completa das atividades com TinyMCE

## Objetivo

A atividade passou a possuir um conteúdo introdutório formatado. O administrador escreve esse conteúdo com o TinyMCE e o funcionário o visualiza antes de iniciar os exercícios.

O fluxo final é:

1. O administrador cadastra ou edita uma atividade.
2. O TinyMCE transforma o campo `textarea` em um editor de texto rico.
3. O controller sanitiza e valida o HTML recebido.
4. O repository salva o HTML na coluna `Atividades.descricao`.
5. O funcionário abre um módulo e clica em **Ver atividade**.
6. Uma tela apresenta o conteúdo cadastrado.
7. O botão **Exercícios** abre a tela de questões e inicia a contagem de tempo.

## 1. Banco de dados

O arquivo `app/database/updates/2026_10_30_alterAtividade.sql` adiciona a coluna:

```sql
ALTER TABLE Atividades
    ADD COLUMN IF NOT EXISTS descricao LONGTEXT NOT NULL DEFAULT '';
```

Foi usado `LONGTEXT` porque o TinyMCE salva HTML e o conteúdo pode ser maior do que uma descrição comum de 255 caracteres. O mesmo campo foi incluído em `app/database/banco.sql`, garantindo que bancos criados do zero também tenham a coluna.

## 2. Model Atividade

O arquivo `app/models/Atividade.php` recebeu:

- a propriedade privada `$descricao`;
- o parâmetro opcional no construtor;
- leitura do campo em `arrayParaObjeto()`;
- `getDescricao()`;
- `setDescricao()`.

Assim, um registro do banco pode ser usado como objeto:

```php
$atividade->getDescricao();
```

## 3. Repository

Em `app/repositories/AtividadeRepository.php`, os comandos `INSERT` e `UPDATE` passaram a incluir `descricao`.

O valor chega ao repository por meio do objeto:

```php
$atividade->getDescricao();
```

As consultas já usavam `SELECT *`, por isso o novo campo é carregado automaticamente e convertido pelo model.

## 4. TinyMCE

O helper `app/helpers/TinyMCEHelper.php` centraliza a configuração do editor. Ele aponta para a instalação local:

```text
public/assets/tinymce_8.10.1/tinymce/js/tinymce/tinymce.min.js
```

As telas de criação e edição chamam:

```php
<?= TinyMCEHelper::render('#descricao') ?>
```

O seletor `#descricao` corresponde ao campo:

```html
<textarea id="descricao" name="descricao"></textarea>
```

A configuração usa o tema escuro e oferece títulos, negrito, itálico, sublinhado, alinhamento, listas, links, tabelas, pré-visualização, tela cheia e visualização do código HTML.

## 5. Sanitização do HTML

HTML vindo de formulário não deve ser exibido diretamente. O helper `app/helpers/ConteudoHtmlHelper.php` remove scripts, eventos como `onclick`, URLs `javascript:` e atributos não autorizados.

São preservados elementos úteis, como:

- parágrafos e títulos;
- negrito, itálico, sublinhado e tachado;
- listas;
- citações;
- links HTTP, HTTPS, `mailto:` ou internos;
- tabelas;
- alinhamento de texto.

O controller sanitiza o conteúdo antes da validação e antes de salvar:

```php
$dados['descricao'] = ConteudoHtmlHelper::sanitizar($dados['descricao'] ?? '');
```

A tela do funcionário sanitiza novamente o conteúdo como proteção adicional.

## 6. Validação

O método `Validador::validarAtividade()` converte o HTML em texto puro para validar o conteúdo real. Isso impede que valores vazios do TinyMCE, como `<p><br></p>`, sejam aceitos.

Regras aplicadas:

- descrição obrigatória;
- máximo de 100.000 caracteres de texto;
- as validações anteriores de nome, tipo, estado, ordem, tempo e módulo continuam ativas.

## 7. Controller

O `AtividadeController` foi atualizado em três pontos:

### Cadastro

`salvar()` sanitiza, valida, coloca a descrição no objeto e chama o service.

### Edição

`atualizar()` repete as mesmas proteções antes de atualizar o registro.

### Visualização do funcionário

`visualizarFuncionario()`:

1. exige um funcionário autenticado;
2. valida o ID da atividade;
3. confirma se atividade e módulo estão ativos;
4. aplica as regras de liberação por progresso e estrelas;
5. envia a atividade e o conteúdo sanitizado para a view.

## 8. Rotas e telas do funcionário

A nova rota é:

```text
GET /funcionario/atividades/conteudo?id=ID
```

Ela renderiza `app/views/funcionario/atividade.php`.

Os links da listagem de módulos e da página inicial do funcionário agora abrem essa apresentação. A tela possui o botão **Exercícios**, que encaminha para a rota já existente:

```text
GET /funcionario/atividades/exercicios?id=ID
```

Nas atividades com exercícios, a atividade só é marcada como iniciada e o cronômetro só começa quando o funcionário entra nos exercícios. Nas atividades do tipo `TEXTO`, o início é registrado ao abrir a leitura.

## 9. Segurança e permissões

- Somente administradores podem cadastrar e editar atividades.
- Somente funcionários autenticados podem abrir o conteúdo.
- A mesma regra de desbloqueio usada pelos exercícios protege a apresentação.
- Formulários POST continuam protegidos pelo token CSRF inserido pelo controller base.
- O HTML é sanitizado no cadastro e novamente na exibição.

## 10. Arquivos envolvidos

- `app/database/banco.sql`
- `app/database/updates/2026_10_30_alterAtividade.sql`
- `app/models/Atividade.php`
- `app/repositories/AtividadeRepository.php`
- `app/controllers/AtividadeController.php`
- `app/helpers/Validador.php`
- `app/helpers/TinyMCEHelper.php`
- `app/helpers/ConteudoHtmlHelper.php`
- `app/views/administrador/atividades/atividade_create.php`
- `app/views/administrador/atividades/atividade_edit.php`
- `app/views/administrador/atividades/atividade_list.php`
- `app/views/funcionario/atividade.php`
- `app/views/funcionario/modulos.php`
- `app/views/funcionario/inicial.php`
- `public/assets/css/exercicios.css`
- `public/index.php`

## 11. Testes

Foram atualizados os testes de conversão do model e renderização das views. O arquivo `tests/conteudo_html.php` verifica especificamente se HTML perigoso é removido e se a formatação permitida é mantida.

## 12. Atividades somente de texto

O tipo `TEXTO` foi acrescentado ao campo `Atividades.tipo_atividade`. A migração correspondente está em `app/database/updates/2026_10_31_tipo_texto_atividade.sql`.

O funcionamento é o seguinte:

1. O administrador escolhe **Texto** no cadastro ou na edição da atividade.
2. Atividades desse tipo não aparecem entre as opções do cadastro de exercícios.
3. O controller também rejeita uma tentativa direta de cadastrar ou mover exercícios para uma atividade textual.
4. Uma atividade que já possui exercícios não pode ser alterada para `TEXTO` até que esses exercícios sejam removidos.
5. Ao abrir a atividade, o funcionário recebe o botão **Concluir leitura** no lugar de **Exercícios**.
6. A conclusão cria ou atualiza `Funcionario_atividade` com estado `CONCLUIDO`, registra o tempo e uma tentativa, mas mantém pontuação, estrelas e bolotas em zero.
7. A atividade passa a contar na taxa de conclusão e libera as próximas atividades. Por não possuir avaliação, ela é desconsiderada no cálculo da média de estrelas.

A conclusão é aceita apenas quando o registro continua sendo do tipo `TEXTO` e não possui exercícios. Essa verificação acontece no controller e novamente dentro da transação do repository.

## 13. Famílias e tamanhos de fonte

A barra do TinyMCE possui os controles `fontfamily` e `fontsize`. O administrador pode escolher entre Arial, Georgia, Times New Roman, Verdana, Tahoma, Trebuchet MS e Courier New, nos tamanhos de 12px a 36px definidos no `TinyMCEHelper`.

O TinyMCE grava essa formatação em elementos `<span>` com estilos. Por isso, o `ConteudoHtmlHelper` permite a tag `<span>` e conserva somente combinações de `font-family` e `font-size` presentes nas listas de valores autorizados. Fontes, tamanhos e demais propriedades CSS que não estejam nessas listas são removidos antes do conteúdo ser salvo e exibido.
