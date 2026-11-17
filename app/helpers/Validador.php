<?php 

namespace app\helpers;

class Validador {

    /** Confere os dígitos verificadores e rejeita sequências repetidas de CPF. */
    public static function cpfValido(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($digito = 9; $digito < 11; $digito++) {
            $soma = 0;
            for ($indice = 0; $indice < $digito; $indice++) {
                $soma += (int) $cpf[$indice] * (($digito + 1) - $indice);
            }
            $verificador = (10 * $soma) % 11;
            if ($verificador === 10) $verificador = 0;
            if ((int) $cpf[$digito] !== $verificador) return false;
        }
        return true;
    }

    /** Valida DDD brasileiro e formatos de telefone fixo ou celular. */
    public static function telefoneValido(string $telefone): bool
    {
        $numero = preg_replace('/\D/', '', $telefone);
        $ddds = [11,12,13,14,15,16,17,18,19,21,22,24,27,28,31,32,33,34,35,37,38,41,42,43,44,45,46,47,48,49,51,53,54,55,61,62,63,64,65,66,67,68,69,71,73,74,75,77,79,81,82,83,84,85,86,87,88,89,91,92,93,94,95,96,97,98,99];
        if (!in_array((int) substr($numero, 0, 2), $ddds, true)) return false;
        return preg_match('/^[1-9]{2}[2-5]\d{7}$/', $numero) === 1
            || preg_match('/^[1-9]{2}9[6-9]\d{7}$/', $numero) === 1;
    }

    /** Valida uma data real, não futura e dentro de uma faixa humana plausível. */
    public static function dataNascimentoValida(string $data): bool
    {
        $nascimento = \DateTimeImmutable::createFromFormat('!Y-m-d', $data);
        if (!$nascimento || $nascimento->format('Y-m-d') !== $data) return false;
        $hoje = new \DateTimeImmutable('today');
        return $nascimento <= $hoje && $nascimento >= $hoje->modify('-120 years');
    }

    public static function validarModulo(array $data): array {
        $erros = [];

        if (empty($data['nome'])) {
            $erros['nome'] = 'O campo nome é obrigatório.';
        }

        if (!empty($data['nome']) && strlen($data['nome']) > 255) {
            $erros['nome'] = 'O campo nome deve ter no máximo 255 caracteres.';
        }

        if (empty($data['descricao'])) {
            $erros['descricao'] = 'O campo descrição é obrigatório.';
        }
        
        if (!empty($data['descricao']) && strlen($data['descricao']) < 30) {
            $erros['descricao'] = 'O campo descrição deve ter pelo menos 30 caracteres.';
        }

        if (!empty($data['descricao']) && strlen($data['descricao']) > 255) {
            $erros['descricao'] = 'O campo descrição deve ter no máximo 255 caracteres.';
        }

        if (!isset($data['min_estrelas_liberacao']) || !is_numeric($data['min_estrelas_liberacao'])) {
            $erros['min_estrelas_liberacao'] = 'O campo mínimo de estrelas para liberação deve ser um número.';
        }
        
        if (isset($data['min_estrelas_liberacao']) && is_numeric($data['min_estrelas_liberacao']) && $data['min_estrelas_liberacao'] < 0) {
            $erros['min_estrelas_liberacao'] = 'O número de estrelas não pode ser negativo.';
        }

        if (!in_array($data['estado'] ?? 'ATIVO', ['ATIVO', 'INATIVO'], true)) {
            $erros['estado'] = 'Selecione um estado válido.';
        }


        return $erros;
    }

    public static function validarExercicio(array $data): array {
        $erros = [];

        $tipo = (string) ($data['tipo'] ?? '');
        if (!in_array($tipo, ['exercicio', 'simulacao'], true)) {
            $erros['tipo'] = 'Selecione um tipo de exercício válido.';
        }

        if (empty($data['descricao'])) {
            $erros['descricao'] = 'O campo descrição é obrigatório.';
        } elseif (strlen($data['descricao']) > 255) {
            $erros['descricao'] = 'O campo descrição deve ter no máximo 255 caracteres.';
        }

        if (empty($data['conteudo'])) {
            $erros['conteudo'] = 'O campo conteúdo é obrigatório.';
        } else {
            $conteudo = json_decode($data['conteudo'], true);

            if (!is_array($conteudo) || json_last_error() !== JSON_ERROR_NONE || array_is_list($conteudo)) {
                $erros['conteudo'] = 'O conteúdo deve ser um JSON válido.';
            } elseif (($conteudo['tipo'] ?? '') !== $tipo) {
                $erros['conteudo'] = 'O tipo informado não corresponde ao tipo do conteúdo.';
            } elseif ($tipo === 'simulacao') {
                $etapas = $conteudo['etapas'] ?? [];
                $ferramentas = $conteudo['ferramentas'] ?? [];
                $pecaQuebrada = (string) ($conteudo['peca_quebrada'] ?? '');
                $pecasPermitidas = ['bateria', 'camera', 'chip'];
                $ferramentasPermitidas = ['chave_fenda', 'pinca', 'pino_ejetor', 'luvas', 'aquecedor', 'palheta', 'adesivo'];

                if (!is_array($etapas) || $etapas === []) {
                    $erros['conteudo'] = 'Cadastre pelo menos uma etapa da simulação.';
                } elseif (!in_array($pecaQuebrada, $pecasPermitidas, true)) {
                    $erros['conteudo'] = 'Selecione a peça com defeito na simulação.';
                } elseif (!is_array($ferramentas) || $ferramentas === [] || array_diff($ferramentas, $ferramentasPermitidas)) {
                    $erros['conteudo'] = 'Selecione pelo menos uma ferramenta válida para a simulação.';
                }

                foreach (is_array($etapas) ? $etapas : [] as $indice => $etapa) {
                    if (isset($erros['conteudo'])) break;
                    $numero = $indice + 1;
                    if (trim((string) ($etapa['descricao'] ?? '')) === '') {
                        $erros['conteudo'] = "Informe a descrição da etapa {$numero}.";
                        break;
                    }
                }

                $necessarias = ['luvas', 'aquecedor', 'palheta', 'chave_fenda'];
                if ($pecaQuebrada === 'chip') {
                    $necessarias[] = 'pino_ejetor';
                } else {
                    $necessarias[] = 'pinca';
                }
                if ($pecaQuebrada === 'bateria') $necessarias[] = 'adesivo';
                $faltantes = array_diff($necessarias, is_array($ferramentas) ? $ferramentas : []);
                if (!isset($erros['conteudo']) && $faltantes !== []) {
                    $nomes = [
                        'luvas' => 'luvas', 'aquecedor' => 'soprador térmico', 'palheta' => 'palheta plástica',
                        'chave_fenda' => 'chave de fenda', 'pinca' => 'pinça', 'pino_ejetor' => 'pino ejetor',
                        'adesivo' => 'adesivo para bateria',
                    ];
                    $erros['conteudo'] = 'Inclua as ferramentas necessárias: ' . implode(', ', array_map(
                        static fn(string $ferramenta): string => $nomes[$ferramenta],
                        $faltantes
                    )) . '.';
                }
            } else {
                $questoes = $conteudo['questoes'] ?? [];
                if (!is_array($questoes) || $questoes === []) {
                    $erros['conteudo'] = 'Cadastre pelo menos uma questão.';
                }
                foreach (is_array($questoes) ? $questoes : [] as $indice => $exercicio) {
                    if (isset($erros['conteudo'])) break;
                    $numero = $indice + 1;
                    $alternativas = $exercicio['alternativas'] ?? [];
                    $correta = $exercicio['alternativa_correta'] ?? null;

                    if (trim((string) ($exercicio['enunciado'] ?? '')) === '') {
                        $erros['conteudo'] = "Informe o enunciado da questão {$numero}.";
                        break;
                    }
                    if (!is_array($alternativas) || count($alternativas) < 2) {
                        $erros['conteudo'] = "A questão {$numero} deve possuir pelo menos duas alternativas.";
                        break;
                    }
                    foreach ($alternativas as $alternativa) {
                        if (trim((string) $alternativa) === '') {
                            $erros['conteudo'] = "Preencha todas as alternativas da questão {$numero}.";
                            break 2;
                        }
                    }
                    if (!is_int($correta) || !array_key_exists($correta, $alternativas)) {
                        $erros['conteudo'] = "Selecione a alternativa correta da questão {$numero}.";
                        break;
                    }
                    if (trim((string) ($exercicio['justificativa'] ?? '')) === '') {
                        $erros['conteudo'] = "Informe a justificativa da questão {$numero}.";
                        break;
                    }
                }
            }
        }

        if (empty($data['atividade_id']) || !filter_var($data['atividade_id'], FILTER_VALIDATE_INT)) {
            $erros['atividade_id'] = 'Selecione uma atividade válida.';
        }

        return $erros;
    }

    public static function validarEmpresa(array $data): array {
        $erros = [];

        if (empty($data['cnpj'])) {
            $erros['cnpj'] = 'O campo cnpj é obrigatório.';
        }

        if (!empty($data['cnpj']) && !preg_match('/^\d{14}$/', $data['cnpj'])) {
            $erros['cnpj'] = 'O CNPJ deve possuir 14 dígitos.';
        }

        if (empty($data['nome'])) {
            $erros['nome'] = 'O campo nome é obrigatório.';
        }

        if (!empty($data['nome']) && strlen($data['nome']) > 255) {
            $erros['nome'] = 'O campo nome deve ter no máximo 255 caracteres.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (!empty($data['email']) && strlen($data['email']) > 255) {
            $erros['email'] = 'O campo email deve ter no máximo 255 caracteres.';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Informe um e-mail válido.';
        }

        if (empty($data['plano'])) {
            $erros['plano'] = 'O campo plano é obrigatório.';
        } elseif (!in_array($data['plano'], ['BASICO', 'INTERMEDIARIO', 'AVANCADO', 'CORPORATIVO'], true)) {
            $erros['plano'] = 'Selecione um plano válido.';
        }

        return $erros;
    }

    public static function validarItem(array $data, array $files = []): array {
        $erros = [];

        $data['estado'] = $data['estado'] ?? '1';
        if ($data['estado'] === '') {
            $data['estado'] = '1';
        }

        if (empty($data['nome'])) {
            $erros['nome'] = 'O campo nome é obrigatório.';
        }

        if (!empty($data['nome']) && strlen($data['nome']) > 255) {
            $erros['nome'] = 'O campo nome deve ter no máximo 255 caracteres.';
        }

        if (empty($data['tipo'])) {
            $erros['tipo'] = 'O campo tipo é obrigatório.';
        } elseif (!in_array(strtoupper((string) $data['tipo']), ['TEMA', 'VISUAL_ESQUILOSO'], true)) {
            $erros['tipo'] = 'Selecione um tipo válido.';
        }

        if (!in_array((string) $data['estado'], ['0', '1'], true)) {
            $erros['estado'] = 'Selecione um estado válido.';
        }

        if (!isset($data['preco']) || $data['preco'] === '' || !is_numeric($data['preco'])) {
            $erros['preco'] = 'O campo preço deve ser um número.';
        } elseif ($data['preco'] < 0) {
            $erros['preco'] = 'O preço deve ser um valor positivo.';
        }

        $tipo = strtoupper((string)($data['tipo'] ?? ''));
        if ($tipo !== 'TEMA') {
            $imagemAtua = !empty($data['imagem_atual']);
            $imagemFile = $files['imagem'] ?? null;

            if ($imagemFile && $imagemFile['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($imagemFile['error'] !== UPLOAD_ERR_OK) {
                    $erros['imagem'] = 'Erro no upload da imagem.';
                } else {
                    $tamanhoMaximo = 2 * 1024 * 1024;
                    if ($imagemFile['size'] > $tamanhoMaximo) {
                        $erros['imagem'] = 'A imagem deve ter no máximo 2MB.';
                    }

                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($imagemFile['tmp_name']);
                    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];

                    if (!in_array($mimeType, $tiposPermitidos, true)) {
                        $erros['imagem'] = 'Formato de imagem inválido. Use JPG, PNG ou WEBP.';
                    }
                }
            } elseif (!$imagemAtua) {
                $erros['imagem'] = 'O campo imagem é obrigatório.';
            }
        }

        return $erros;
    }

    public static function validarUsuario(array $data): array {
        $erros = [];

        if (empty($data['nome_completo'])) {
            $erros['nome_completo'] = 'O campo nome é obrigatório.';
        }

        if (!empty($data['nome_completo']) && strlen($data['nome_completo']) > 255) {
            $erros['nome_completo'] = 'O campo nome deve ter no máximo 255 caracteres.';
        }

        if (empty($data['data_nascimento'])) {
            $erros['data_nascimento'] = 'O campo data de nascimento é obrigatório.';
        } else {
            if (!self::dataNascimentoValida((string) $data['data_nascimento'])) {
                $erros['data_nascimento'] = 'Informe uma data de nascimento válida.';
            }
        }

        if (empty($data['cpf'])) {
            $erros['cpf'] = 'O campo cpf é obrigatório.';
        }

        if (!empty($data['cpf']) && !self::cpfValido((string) $data['cpf'])) {
            $erros['cpf'] = 'Informe um CPF válido.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (!empty($data['email']) && strlen($data['email']) > 255) {
            $erros['email'] = 'O campo email deve ter no máximo 255 caracteres.';
        }

        if (empty($data['senha'])) {
            $erros['senha'] = 'O campo senha é obrigatório.';
        }

        if (empty($data['numero_telefone'])) {
            $erros['numero_telefone'] = 'O campo número de telefone é obrigatório.';
        }

        if (!empty($data['numero_telefone']) && !self::telefoneValido((string) $data['numero_telefone'])) {
            $erros['numero_telefone'] = 'Informe um telefone brasileiro válido com DDD.';
        }

        return $erros;
    }

    public static function validarGestor(array $data, bool $senhaObrigatoria = true): array {
        $erros = [];

        if (empty($data['nome_completo'])) {
            $erros['nome_completo'] = 'O campo nome é obrigatório.';
        }

        if (!empty($data['nome_completo']) && strlen($data['nome_completo']) > 255) {
            $erros['nome_completo'] = 'O campo nome deve ter no máximo 255 caracteres.';
        }

        if (empty($data['data_nascimento'])) {
            $erros['data_nascimento'] = 'O campo data de nascimento é obrigatório.';
        } else {
            if (!self::dataNascimentoValida((string) $data['data_nascimento'])) {
                $erros['data_nascimento'] = 'Informe uma data de nascimento válida.';
            }
        }

        if (empty($data['cpf'])) {
            $erros['cpf'] = 'O campo cpf é obrigatório.';
        }

        if (!empty($data['cpf']) && !self::cpfValido((string) $data['cpf'])) {
            $erros['cpf'] = 'Informe um CPF válido.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (!empty($data['email']) && strlen($data['email']) > 255) {
            $erros['email'] = 'O campo email deve ter no máximo 255 caracteres.';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Informe um e-mail válido.';
        }

        if ($senhaObrigatoria && empty($data['senha'])) {
            $erros['senha'] = 'O campo senha é obrigatório.';
        } elseif (!empty($data['senha']) && strlen($data['senha']) < 8) {
            $erros['senha'] = 'A senha deve possuir pelo menos 8 caracteres.';
        }

        if (empty($data['numero_telefone'])) {
            $erros['numero_telefone'] = 'O campo número de telefone é obrigatório.';
        }

        if (!empty($data['numero_telefone']) && !self::telefoneValido((string) $data['numero_telefone'])) {
            $erros['numero_telefone'] = 'Informe um telefone brasileiro válido com DDD.';
        }

        if (empty($data['id_empresa'])) {
            $erros['id_empresa'] = 'O campo empresa é obrigatório.';
        }

        return $erros;
    }

    public static function validarFuncionario(array $data, bool $senhaObrigatoria = true): array {
        $erros = [];

        if (empty($data['nome_completo'])) {
            $erros['nome_completo'] = 'O campo nome é obrigatório.';
        }

        if (!empty($data['nome_completo']) && strlen($data['nome_completo']) > 255) {
            $erros['nome_completo'] = 'O campo nome deve ter no máximo 255 caracteres.';
        }

        if (empty($data['data_nascimento'])) {
            $erros['data_nascimento'] = 'O campo data de nascimento é obrigatório.';
        } else {
            if (!self::dataNascimentoValida((string) $data['data_nascimento'])) {
                $erros['data_nascimento'] = 'Informe uma data de nascimento válida.';
            }
        }

        if (empty($data['cpf'])) {
            $erros['cpf'] = 'O campo cpf é obrigatório.';
        }

        if (!empty($data['cpf']) && !self::cpfValido((string) $data['cpf'])) {
            $erros['cpf'] = 'Informe um CPF válido.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (!empty($data['email']) && strlen($data['email']) > 255) {
            $erros['email'] = 'O campo email deve ter no máximo 255 caracteres.';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Informe um e-mail válido.';
        }

        if ($senhaObrigatoria && empty($data['senha_hash'])) {
            $erros['senha_hash'] = 'O campo senha é obrigatório.';
        } elseif (!empty($data['senha_hash']) && strlen($data['senha_hash']) < 8) {
            $erros['senha_hash'] = 'A senha deve possuir pelo menos 8 caracteres.';
        }

        if (empty($data['numero_telefone'])) {
            $erros['numero_telefone'] = 'O campo número de telefone é obrigatório.';
        }

        if (!empty($data['numero_telefone']) && !self::telefoneValido((string) $data['numero_telefone'])) {
            $erros['numero_telefone'] = 'Informe um telefone brasileiro válido com DDD.';
        }

        if (empty($data['id_empresa'])) {
            $erros['id_empresa'] = 'O campo empresa é obrigatório.';
        }

        return $erros;
    }

    public static function validarAtividade(array $data): array {
        $erros = [];

        if (empty($data['nome'])) {
            $erros['nome'] = 'O campo nome é obrigatório.';
        } elseif (strlen($data['nome']) > 255) {
            $erros['nome'] = 'O campo nome deve ter no máximo 255 caracteres.';
        }

        $descricaoTexto = ConteudoHtmlHelper::textoPuro((string) ($data['descricao'] ?? ''));
        if ($descricaoTexto === '') {
            $erros['descricao'] = 'A descrição completa da atividade é obrigatória.';
        } elseif (mb_strlen($descricaoTexto) > 100000) {
            $erros['descricao'] = 'A descrição deve possuir no máximo 100.000 caracteres.';
        }

        if (empty($data['tipo_atividade'])) {
            $erros['tipo_atividade'] = 'O campo tipo de atividade é obrigatório.';
        } elseif (!in_array($data['tipo_atividade'], ['INTRODUCAO', 'REVISAO', 'SIMULACAO', 'TEXTO'], true)) {
            $erros['tipo_atividade'] = 'Selecione um tipo de atividade válido.';
        }

        if (empty($data['estado'])) {
            $erros['estado'] = 'O campo estado é obrigatório.';
        } elseif (!in_array($data['estado'], ['ATIVO', 'INATIVO'], true)) {
            $erros['estado'] = 'Selecione um estado válido.';
        }
        if (!isset($data['ordem']) || filter_var($data['ordem'], FILTER_VALIDATE_INT) === false || (int) $data['ordem'] < 1) {
            $erros['ordem'] = 'A ordem deve ser um número inteiro maior que zero.';
        }

        if (!isset($data['tempo_limite']) || filter_var($data['tempo_limite'], FILTER_VALIDATE_INT) === false || (int) $data['tempo_limite'] < 1) {
            $erros['tempo_limite'] = 'O tempo limite deve ser informado em segundos e ser maior que zero.';
        }

        if (!isset($data['estrelas_recompensa']) || filter_var($data['estrelas_recompensa'], FILTER_VALIDATE_INT) === false || (int) $data['estrelas_recompensa'] < 0 || (int) $data['estrelas_recompensa'] > 5) {
            $erros['estrelas_recompensa'] = 'A recompensa deve ser um número inteiro entre 0 e 5 estrelas.';
        }

        if (!isset($data['bolotas_recompensa']) || filter_var($data['bolotas_recompensa'], FILTER_VALIDATE_INT) === false || (int) $data['bolotas_recompensa'] < 0 || (int) $data['bolotas_recompensa'] > 100000) {
            $erros['bolotas_recompensa'] = 'A recompensa deve ser um número inteiro entre 0 e 100.000 bolotas.';
        }

        if (empty($data['id_modulo']) || filter_var($data['id_modulo'], FILTER_VALIDATE_INT) === false) {
            $erros['id_modulo'] = 'Selecione um módulo válido.';
        }

        return $erros;
    }

    public static function validarSolicitacaoAcesso(array $data): array
    {
        $erros = [];

        foreach (['nome_completo' => 'nome completo', 'empresa_nome' => 'nome da empresa'] as $campo => $rotulo) {
            if (trim((string) ($data[$campo] ?? '')) === '') {
                $erros[$campo] = "O campo {$rotulo} é obrigatório.";
            } elseif (strlen((string) $data[$campo]) > 255) {
                $erros[$campo] = "O campo {$rotulo} deve ter no máximo 255 caracteres.";
            }
        }

        if (!self::dataNascimentoValida((string) ($data['data_nascimento'] ?? ''))) {
            $erros['data_nascimento'] = 'Informe uma data de nascimento válida.';
        }

        if (!self::cpfValido((string) ($data['cpf'] ?? ''))) {
            $erros['cpf'] = 'Informe um CPF válido.';
        }
        if (!preg_match('/^\d{14}$/', (string) ($data['empresa_cnpj'] ?? ''))) {
            $erros['empresa_cnpj'] = 'O CNPJ deve possuir exatamente 14 dígitos.';
        }

        foreach (['email' => 'e-mail do gestor', 'empresa_email' => 'e-mail da empresa'] as $campo => $rotulo) {
            $valor = trim((string) ($data[$campo] ?? ''));
            if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                $erros[$campo] = "Informe um {$rotulo} válido.";
            } elseif (strlen($valor) > 255) {
                $erros[$campo] = "O {$rotulo} deve ter no máximo 255 caracteres.";
            }
        }

        $telefone = trim((string) ($data['numero_telefone'] ?? ''));
        if ($telefone === '') {
            $erros['numero_telefone'] = 'O número de telefone é obrigatório.';
        } elseif (!self::telefoneValido($telefone)) {
            $erros['numero_telefone'] = 'Informe um telefone brasileiro válido com DDD.';
        }

        if (strlen((string) ($data['mensagem'] ?? '')) > 1000) {
            $erros['mensagem'] = 'A mensagem deve ter no máximo 1000 caracteres.';
        }

        return $erros;
    }
}
