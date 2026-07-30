<?php 

namespace app\helpers;

class Validador {

    public static function validarModulo(array $data): array {
        $erros = [];

        if (empty($data['nome'])) {
            $erros['nome'] = 'O campo nome é obrigatório.';
        }

        if (empty($data['descricao'])) {
            $erros['descricao'] = 'O campo descrição é obrigatório.';
        }

        if (!isset($data['min_estrelas_liberacao']) || !is_numeric($data['min_estrelas_liberacao'])) {
            $erros['min_estrelas_liberacao'] = 'O campo mínimo de estrelas para liberação deve ser um número.';
        }

        return $erros;
    }

    //TODO: Implementar validações para outras entidades, como Usuário, atividade, etc. 
    public static function validarExercicio(array $data): array {
        $erros = [];

        if (empty($data['descricao'])) {
            $erros['descricao'] = 'O campo descrição é obrigatório.';
        }

        if (empty($data['conteudo'])) {
            $erros['conteudo'] = 'O campo conteúdo é obrigatório.';
        }

        return $erros;
    }

    public static function validarEmpresa(array $data): array {
        $erros = [];

        if (empty($data['cnpj'])) {
            $erros['cnpj'] = 'O campo cnpj é obrigatório.';
        }

        if (empty($data['nome'])) {
            $erros['nome'] = 'O campo nome é obrigatório.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (empty($data['plano'])) {
            $erros['plano'] = 'O campo plano é obrigatório.';
        }

        return $erros;
    }

    public static function validarItem(array $data): array {
        $erros = [];

        if (empty($data['estado'])) {
            $erros['estado'] = 'O campo estado é obrigatório.';
        }

        if (empty($data['nome'])) {
            $erros['nome'] = 'O campo nome é obrigatório.';
        }

        if (empty($data['tipo'])) {
            $erros['tipo'] = 'O campo tipo é obrigatório.';
        }

        if (!isset($data['preco']) || !is_numeric($data['preco'])) {
            $erros['preco'] = 'O campo preço deve ser um número.';
        }

        if (empty($data['imagem'])) {
            $erros['imagem'] = 'O campo imagem é obrigatório.';
        }

        return $erros;
    }

    public static function validarUsuario(array $data): array {
        $erros = [];

        if (empty($data['nome_completo'])) {
            $erros['nome_completo'] = 'O campo nome é obrigatório.';
        }

        if (empty($data['data_nascimento'])) {
            $erros['data_nascimento'] = 'O campo data de nascimento é obrigatório.';
        }

        if (empty($data['cpf'])) {
            $erros['cpf'] = 'O campo cpf é obrigatório.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (empty($data['senha'])) {
            $erros['senha'] = 'O campo senha é obrigatório.';
        }

        if (empty($data['numero_telefone'])) {
            $erros['numero_telefone'] = 'O campo número de telefone é obrigatório.';
        }

        return $erros;
    }

    public static function validarGestor(array $data): array {
        $erros = [];

        if (empty($data['nome_completo'])) {
            $erros['nome_completo'] = 'O campo nome é obrigatório.';
        }

        if (empty($data['data_nascimento'])) {
            $erros['data_nascimento'] = 'O campo data de nascimento é obrigatório.';
        }

        if (empty($data['cpf'])) {
            $erros['cpf'] = 'O campo cpf é obrigatório.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (empty($data['senha'])) {
            $erros['senha'] = 'O campo senha é obrigatório.';
        }

        if (empty($data['numero_telefone'])) {
            $erros['numero_telefone'] = 'O campo número de telefone é obrigatório.';
        }

        if (empty($data['id_empresa'])) {
            $erros['id_empresa'] = 'O campo empresa é obrigatório.';
        }

        return $erros;
    }

    public static function validarFuncionario(array $data): array {
        $erros = [];

        if (empty($data['nome_completo'])) {
            $erros['nome_completo'] = 'O campo nome é obrigatório.';
        }

        if (empty($data['data_nascimento'])) {
            $erros['data_nascimento'] = 'O campo data de nascimento é obrigatório.';
        }

        if (empty($data['cpf'])) {
            $erros['cpf'] = 'O campo cpf é obrigatório.';
        }

        if (empty($data['email'])) {
            $erros['email'] = 'O campo email é obrigatório.';
        }

        if (empty($data['senha'])) {
            $erros['senha'] = 'O campo senha é obrigatório.';
        }

        if (empty($data['numero_telefone'])) {
            $erros['numero_telefone'] = 'O campo número de telefone é obrigatório.';
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
        }

        if (empty($data['tipo_atividade'])) {
            $erros['tipo_atividade'] = 'O campo tipo de atividade é obrigatório.';
        }

        if (empty($data['estado'])) {
            $erros['estado'] = 'O campo estado é obrigatório.';
        }

        if (!isset($data['pontuacao']) || !is_numeric($data['pontuacao'])) {
            $erros['pontuacao'] = 'O campo pontuação deve ser um número.';
        }

        if (!isset($data['estrelas']) || !is_numeric($data['estrelas'])) {
            $erros['estrelas'] = 'O campo estrelas deve ser um número.';
        }

        if (empty($data['modulo_id'])) {
            $erros['modulo_id'] = 'O campo módulo é obrigatório.';
        }

        return $erros;
    }
}
