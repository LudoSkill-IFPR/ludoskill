<?php

use app\model\Usuario;
use app\repositories\UsuarioRepository;

$usuarioRepository = new UsuarioRepository();

$funcionarios = $data['lista'];

$usuarios = [];

foreach ($funcionarios as $funcionario) {
    array_push($usuarios, $usuarioRepository->getUsuarioById($funcionario['id_usuario']));
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Empresa</th>
            <th>Bolotas</th>
            <th>Pontuação</th>
            <th>Nível</th>
        </tr>
    <?php
        foreach($funcionarios as $funcionario){
    ?>
        <tr>
            <th><?=$funcionario['id_funcionario']?></th>
            <th><?=$usuarioRepository->getUsuarioById($funcionario['id_usuario'])->getNomeCompleto()?></th>
            <th><?=$funcionario['id_empresa']?></th>
            <th><?=$funcionario['bolotas_totais']?></th>
            <th><?=$funcionario['pontuacao_total']?></th>
            <th><?=$funcionario['nivel']?></th>
        </tr>
    <?php  
        }
    ?>
    </table>
</body>
</html>