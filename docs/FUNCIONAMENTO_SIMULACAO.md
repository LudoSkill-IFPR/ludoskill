# Como funciona a simulação do LudoSkill

Este arquivo explica o caminho completo de uma simulação: cadastro pelo administrador, criação do JSON, leitura do JSON, interação do funcionário, correção e gravação do resultado.

## 1. O que o administrador cadastra

O formulário fica em:

- `app/views/administrador/exercicios/exercicio_create.php`
- `app/views/administrador/exercicios/exercicio_edit.php`

Quando o tipo escolhido é **Simulação**, o administrador informa somente:

1. a descrição da simulação;
2. a atividade à qual ela pertence;
3. a peça inicialmente defeituosa: bateria, câmera ou chip.

As etapas e ferramentas não são escritas pelo administrador. Elas são fixas e controladas pelo sistema para todas as simulações seguirem as mesmas regras.

O arquivo `public/assets/javascript/exercicios.js` troca o formulário conforme o tipo escolhido. Para uma simulação, ele esconde o formulário de questões e exibe a escolha da peça defeituosa.

## 2. JSON salvo no banco

O método `salvar()` de `app/controllers/ExercicioController.php` recebe os campos do formulário e cria um JSON simples.

Exemplo de uma simulação de bateria:

```json
{
  "tipo": "simulacao",
  "peca_quebrada": "bateria"
}
```

Exemplo de uma simulação de câmera:

```json
{
  "tipo": "simulacao",
  "peca_quebrada": "camera"
}
```

O JSON é salvo na coluna `conteudo` da tabela `Exercicios`. Não foi necessário criar uma tabela nova.

As etapas não ficam gravadas no JSON porque são definidas pelo sistema. Isso evita que dois administradores cadastrem procedimentos diferentes para o mesmo tipo de conserto.

## 3. Validação do cadastro

O método `Validador::validarExercicio()` fica em `app/helpers/Validador.php`.

Para uma simulação, ele confere:

- se o JSON é válido;
- se o tipo do formulário e o tipo do JSON são iguais;
- se a peça defeituosa é `bateria`, `camera` ou `chip`.

Se uma peça diferente for enviada manualmente, o cadastro é rejeitado.

## 4. Decodificação do JSON

Ao editar ou realizar uma simulação, o método privado `decodificarConteudo()` do `ExercicioController` usa `json_decode()` para transformar o texto JSON novamente em um array PHP.

O resultado principal é semelhante a:

```php
[
    'tipo' => 'simulacao',
    'peca_quebrada' => 'bateria',
]
```

O decodificador também reconhece os JSONs antigos que guardavam `etapas`, `ferramentas`, `pecas_quebradas` ou o objeto `componentes`. Quando encontra um formato antigo, ele recupera a peça defeituosa e passa a usar as regras fixas atuais.

## 5. Criação das etapas fixas

Antes de abrir a tela do funcionário, `montarExerciciosDaAtividade()` chama:

- `montarEtapasSimulacao()`;
- `getFerramentasSimulacao()`.

`montarEtapasSimulacao()` cria as instruções de acordo com a peça defeituosa. A abertura e o fechamento do celular são iguais em todas as simulações. Somente a explicação de retirada da bateria, câmera ou chip muda.

As ferramentas fixas são:

- pinça;
- soprador térmico;
- ferro de solda;
- palheta plástica;
- álcool isopropílico.

Essas informações são acrescentadas ao array usado pela view, mas não são gravadas novamente no banco.

## 6. Imagens utilizadas

As imagens desenhadas para a implementação original foram copiadas para:

`public/assets/imagens/simulacao/`

A tela utiliza, entre outras:

- `verso-celular.png`: celular fechado;
- `pelado.png`: parte interna do celular;
- `bateria.png`, `camera.png` e `chip.png`: componentes clicáveis;
- `aquecedor.png`, `palheta.png`, `pinca-de-manutencao.png`, `solda.png` e `alcool.png`: ferramentas.

O PHP monta os endereços das imagens usando `URL_BASE`, portanto elas continuam funcionando quando o sistema está dentro de uma pasta como `/ludoskill`.

## 7. Montagem da tela do funcionário

A view está em `app/views/funcionario/simulacao.php`.

Ela recebe do controller:

- a atividade;
- os exercícios de simulação;
- as etapas fixas;
- as ferramentas;
- a peça inicialmente defeituosa;
- o tempo restante.

A tela possui três áreas:

1. etapas do conserto;
2. bancada com o celular e os componentes;
3. ferramentas e estado das peças.

Cada botão possui um atributo `data-acao`. Exemplos:

```html
data-acao="ferramenta:aquecedor"
data-acao="interagir:tampa"
data-acao="interagir:bateria"
data-acao="instalar:bateria"
data-acao="instalar:tampa"
```

O JavaScript lê esses valores para saber qual regra executar.

## 8. Regras da interação

As regras do navegador ficam em `public/assets/javascript/simulacao.js`.

No início, bateria, câmera e chip são considerados funcionais, exceto a peça que o administrador marcou como defeituosa.

O funcionário deve:

1. selecionar o soprador e clicar na tampa;
2. selecionar a palheta e clicar novamente na tampa;
3. retirar a peça defeituosa com a ferramenta correta;
4. inserir uma peça nova;
5. corrigir qualquer dano adicional;
6. recolocar a tampa.

### Bateria

A cola precisa ser dissolvida com álcool. Depois, a bateria é retirada com a palheta.

Usar o soprador diretamente no aparelho aberto danifica a bateria. Ela passa a ser marcada como defeituosa e precisa ser substituída.

### Câmera

A câmera defeituosa é retirada com a pinça.

Usar o soprador na câmera danifica uma câmera que estava funcionando. Nesse caso, ela também precisa ser retirada e substituída.

### Chip

A solda precisa ser liberada com o ferro de solda e o chip é retirado com a pinça.

Usar a solda em um chip que estava funcionando o danifica. O funcionário precisa terminar a retirada e inserir outro chip.

O botão **Concluir manutenção** somente é habilitado quando:

- nenhuma peça continua defeituosa;
- nenhuma peça está fora do aparelho;
- a tampa foi recolocada.

As ações são guardadas temporariamente no `localStorage`. Assim, voltar ao conteúdo e entrar novamente não apaga o andamento da tentativa. O tempo inicial continua guardado na sessão do PHP.

## 9. Envio das ações ao PHP

A sequência de ações fica em um campo oculto do formulário, no formato JSON.

Exemplo:

```json
[
  "ferramenta:aquecedor",
  "interagir:tampa",
  "ferramenta:palheta",
  "interagir:tampa",
  "ferramenta:alcool",
  "interagir:bateria",
  "ferramenta:palheta",
  "interagir:bateria",
  "instalar:bateria",
  "instalar:tampa"
]
```

O JavaScript ajuda na interação, mas o servidor não confia somente no navegador.

Ao receber o formulário, `responderAtividade()` chama `avaliarSimulacao()`. Esse método repete cada ação em PHP e verifica novamente o estado da tampa e dos componentes. Uma sequência adulterada ou incompleta não é aceita como conclusão.

## 10. Erros e correções

Um erro pode apenas impedir uma ação ou pode danificar um componente.

Quando uma peça é danificada:

1. ela aparece como **Com defeito**;
2. precisa ser retirada com o procedimento correto;
3. precisa ser substituída;
4. só depois disso a tampa pode ser recolocada.

Os erros ficam registrados para mostrar o resultado, mas não retiram estrelas diretamente. O próprio conserto do erro já exige mais ações e, consequentemente, mais tempo.

## 11. Critério de avaliação

A avaliação da simulação considera o tempo da atividade:

- concluiu dentro do tempo: recebe o máximo de estrelas configurado na atividade;
- concluiu depois do tempo: recebe zero estrelas;
- não terminou o conserto: o servidor não aceita a conclusão.

Os erros não diminuem estrelas por uma fórmula separada. Como precisam ser corrigidos, eles aumentam naturalmente o tempo usado pelo funcionário.

O tempo, as estrelas, as bolotas e a tentativa continuam sendo gravados em `Funcionario_atividade` pelo `FuncionarioAtividadeService` e pelo `FuncionarioAtividadeRepository`, seguindo o mesmo fluxo das outras atividades.

## 12. Arquivos principais

- `app/controllers/ExercicioController.php`: cria/decodifica o JSON, monta regras fixas, confere as ações e calcula o resultado;
- `app/helpers/Validador.php`: valida o JSON do cadastro;
- `app/views/administrador/exercicios/exercicio_create.php`: cadastro;
- `app/views/administrador/exercicios/exercicio_edit.php`: edição;
- `public/assets/javascript/exercicios.js`: alternância do formulário administrativo;
- `app/views/funcionario/simulacao.php`: decodificação visual para o funcionário;
- `public/assets/javascript/simulacao.js`: interação no navegador;
- `public/assets/css/simulacao.css`: aparência e responsividade;
- `public/assets/imagens/simulacao/`: desenhos originais;
- `tests/exercicio_tipos.php`: testes do JSON, etapas e regras de conserto.
