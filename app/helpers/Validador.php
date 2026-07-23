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

}

