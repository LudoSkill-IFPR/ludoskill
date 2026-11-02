<?php

namespace app\helpers;

use DOMDocument;
use DOMElement;
use DOMNode;

class ConteudoHtmlHelper
{
    private const TAGS_PERMITIDAS = [
        'p', 'br', 'h2', 'h3', 'h4', 'span', 'strong', 'b', 'em', 'i', 'u', 's',
        'ul', 'ol', 'li', 'blockquote', 'a', 'table', 'thead', 'tbody',
        'tr', 'th', 'td', 'hr'
    ];

    private const FONTES_PERMITIDAS = [
        'arial,helvetica,sans-serif' => 'Arial, Helvetica, sans-serif',
        'georgia,serif' => 'Georgia, serif',
        'timesnewroman,times,serif' => '"Times New Roman", Times, serif',
        'verdana,geneva,sans-serif' => 'Verdana, Geneva, sans-serif',
        'tahoma,arial,helvetica,sans-serif' => 'Tahoma, Arial, Helvetica, sans-serif',
        'trebuchetms,arial,sans-serif' => '"Trebuchet MS", Arial, sans-serif',
        'couriernew,courier,monospace' => '"Courier New", Courier, monospace',
    ];

    private const TAMANHOS_FONTE_PERMITIDOS = [
        '12px', '14px', '16px', '18px', '20px', '24px', '30px', '36px',
    ];

    /*
      Sanitiza o HTML produzido pelo TinyMCE antes de ele ser salvo ou exibido.
     
      O conteúdo é carregado em um DOMDocument dentro de uma div temporária. Em seguida, limparFilhos() percorre toda a árvore para remover elementos, atributos e estilos não autorizados. No final, somente o conteúdo interno da div temporária é convertido novamente para HTML e devolvido.
     
      @param string $html HTML original recebido do editor.
      @return string HTML filtrado e seguro para ser armazenado ou apresentado.
     */
    public static function sanitizar(string $html): string
    {
        if (trim($html) === '') return '';

        $documento = new DOMDocument('1.0', 'UTF-8');
        $estadoAnterior = libxml_use_internal_errors(true);
        $documento->loadHTML(
            '<?xml encoding="UTF-8"><div id="conteudo-ludoskill">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($estadoAnterior);

        $raiz = $documento->getElementById('conteudo-ludoskill');
        if (!$raiz) return '';

        self::limparFilhos($raiz);

        $resultado = '';
        foreach ($raiz->childNodes as $filho) {
            $resultado .= $documento->saveHTML($filho);
        }
        return trim($resultado);
    }

    /*
      Converte um conteúdo HTML em texto simples para fins de validação.
     
      Remove as tags, converte entidades como &amp; e &nbsp; para caracteres comuns e reduz sequências de espaços e quebras de linha a um único espaço. Assim, um HTML visualmente vazio, como <p><br></p>, também é tratado como vazio.
     
      @param string $html Conteúdo HTML que será convertido.
      @return string Texto normalizado, sem tags HTML.
     */
    public static function textoPuro(string $html): string
    {
        $texto = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $texto = str_replace("\u{00A0}", ' ', $texto);
        return trim(preg_replace('/\s+/u', ' ', $texto) ?? '');
    }

    /*
      Percorre recursivamente os filhos de um nó e limpa a estrutura do HTML.
     
      Elementos perigosos, como script e iframe, são removidos com todo o seu conteúdo. Tags que não estão na lista permitida são retiradas, mas seus filhos são preservados. Para cada tag aceita, os atributos são filtrados por limparAtributos() e seus nós internos são analisados da mesma maneira.
      Comentários HTML também são descartados.
     
      A variável $proximo é guardada antes de cada remoção para que o percurso continue corretamente mesmo quando a árvore DOM é modificada.
     
      @param DOMNode $pai Nó cujos filhos serão examinados.
      @return void
     */
    private static function limparFilhos(DOMNode $pai): void
    {
        for ($filho = $pai->firstChild; $filho !== null;) {
            $proximo = $filho->nextSibling;

            if ($filho instanceof DOMElement) {
                $tag = strtolower($filho->tagName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed'], true)) {
                    $pai->removeChild($filho);
                    $filho = $proximo;
                    continue;
                }

                if (!in_array($tag, self::TAGS_PERMITIDAS, true)) {
                    self::limparFilhos($filho);
                    while ($filho->firstChild) {
                        $pai->insertBefore($filho->firstChild, $filho);
                    }
                    $pai->removeChild($filho);
                    $filho = $proximo;
                    continue;
                }

                self::limparAtributos($filho);
                self::limparFilhos($filho);
            } elseif ($filho->nodeType === XML_COMMENT_NODE) {
                $pai->removeChild($filho);
            }

            $filho = $proximo;
        }
    }

    /*
      Mantém em um elemento somente os atributos considerados seguros.
     
      Links aceitam apenas endereços HTTP, HTTPS, mailto ou caminhos internos.
      O atributo style é reconstruído por sanitizarEstilo(), e células de tabela aceitam colspan e rowspan somente dentro do limite definido. Qualquer outro atributo, incluindo eventos como onclick, é removido. Links válidos recebem rel="noopener noreferrer" para reduzir riscos ao abrir páginas externas.
     
      Os nomes dos atributos são copiados antes da remoção porque não é seguro modificar a coleção de atributos enquanto ela está sendo percorrida.
     
      @param DOMElement $elemento Elemento cujos atributos serão filtrados.
      @return void
     */
    private static function limparAtributos(DOMElement $elemento): void
    {
        $tag = strtolower($elemento->tagName);
        $atributos = [];
        foreach ($elemento->attributes as $atributo) {
            $atributos[] = $atributo->name;
        }

        foreach ($atributos as $nome) {
            $valor = trim($elemento->getAttribute($nome));
            $permitido = false;

            if ($tag === 'a' && $nome === 'href') {
                $permitido = preg_match('#^(https?://|mailto:|/)#i', $valor) === 1;
            } elseif ($tag === 'a' && $nome === 'title') {
                $permitido = true;
            } elseif ($nome === 'style') {
                $estiloSeguro = self::sanitizarEstilo($tag, $valor);
                if ($estiloSeguro !== '') {
                    $elemento->setAttribute('style', $estiloSeguro);
                    $permitido = true;
                }
            } elseif (in_array($tag, ['th', 'td'], true) && in_array($nome, ['colspan', 'rowspan'], true)) {
                $permitido = ctype_digit($valor) && (int) $valor > 0 && (int) $valor <= 20;
            }

            if (!$permitido) $elemento->removeAttribute($nome);
        }

        if ($tag === 'a' && $elemento->hasAttribute('href')) {
            $elemento->setAttribute('rel', 'noopener noreferrer');
        }
    }

    /*
      Filtra as declarações CSS presentes no atributo style de uma tag.
     
      Cada declaração é separada em propriedade e valor. O método permite apenas alinhamentos válidos nos elementos de bloco e fontes ou tamanhos presentes nas listas seguras da classe. As propriedades aprovadas são reconstruídas em um novo atributo style; propriedades desconhecidas são ignoradas.
     
      O array usa o nome da propriedade como chave para impedir que uma mesma propriedade seja repetida várias vezes no resultado final.
     
      @param string $tag Nome da tag que possui o atributo style.
      @param string $estilo Conteúdo original do atributo style.
      @return string CSS filtrado, ou uma string vazia se nada for permitido.
     */
    private static function sanitizarEstilo(string $tag, string $estilo): string
    {
        $declaracoesSeguras = [];

        foreach (explode(';', $estilo) as $declaracao) {
            if (!str_contains($declaracao, ':')) continue;

            [$propriedade, $valor] = array_map('trim', explode(':', $declaracao, 2));
            $propriedade = strtolower($propriedade);

            if ($propriedade === 'text-align' && in_array($tag, ['p', 'h2', 'h3', 'h4'], true)) {
                $alinhamento = strtolower($valor);
                if (in_array($alinhamento, ['left', 'center', 'right', 'justify'], true)) {
                    $declaracoesSeguras['text-align'] = 'text-align: ' . $alinhamento . ';';
                }
                continue;
            }

            if (!in_array($tag, ['span', 'p', 'h2', 'h3', 'h4'], true)) continue;

            if ($propriedade === 'font-family') {
                $chaveFonte = strtolower(preg_replace('/[\s\'\"]+/u', '', $valor) ?? '');
                if (isset(self::FONTES_PERMITIDAS[$chaveFonte])) {
                    $declaracoesSeguras['font-family'] = 'font-family: ' . self::FONTES_PERMITIDAS[$chaveFonte] . ';';
                }
            } elseif ($propriedade === 'font-size') {
                $tamanho = strtolower($valor);
                if (in_array($tamanho, self::TAMANHOS_FONTE_PERMITIDOS, true)) {
                    $declaracoesSeguras['font-size'] = 'font-size: ' . $tamanho . ';';
                }
            }
        }

        return implode(' ', $declaracoesSeguras);
    }
}
